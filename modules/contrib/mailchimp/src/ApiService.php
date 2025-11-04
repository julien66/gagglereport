<?php

declare(strict_types=1);

namespace Drupal\mailchimp;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\Language\ContextProvider\CurrentLanguageContext;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Exception;

/**
 * Access point for interacting with the Mailchimp API.
 */
final class ApiService {

  public function __construct(
    private ClientFactory $mailchimpClientFactory,
    private MessengerInterface $messenger,
    private KeyValueFactoryInterface $keyvalue,
    private CacheBackendInterface $cacheMailchimp,
    private LoggerChannelInterface $loggerChannel,
    private CurrentLanguageContext $languageCurrentLanguageContext,
    private ModuleHandlerInterface $moduleHandler,
    private ConfigFactoryInterface $configFactory
  ) {}

  /**
   * Instantiates a Mailchimp library object.
   *
   * @param string $classname
   *   A valid \Mailchimp\MailchimpApiUser class name.
   *
   * @return \Mailchimp\MailchimpApiUser
   *   Drupal Mailchimp library object.
   */
  public function getApiObject(string $classname = 'MailchimpApiUser') {
    $object = $this->mailchimpClientFactory->getByClassNameOrNull($classname);
    // returning mailchimpapiuser
    if (!$object) {
      $this->messenger->addError('Failed to load Mailchimp PHP library. Please refer to the installation requirements.');
      return NULL;
    }
    $config = $this->configFactory->get('mailchimp.settings');
    if (!$config->get('test_mode') && !$object->hasApiAccess()) {
      $mc_oauth_url = Url::fromRoute('mailchimp.admin.oauth');
      $this->messenger->addError(t('Unable to connect to Mailchimp API. Visit @oauth_settings_page to authenticate or uncheck "Use OAuth Authentication" and add an api_key below (deprecated).',
        [
          '@oauth_settings_page' => Link::fromTextAndUrl(t('OAuth Settings page'), $mc_oauth_url)->toString(),
          ]));
      return NULL;
    }

    return $object;
  }

  /**
   * Returns all Mailchimp audiences for a given account.
   *
   * Optionally limit audiences to those with the given IDs. Audiences are
   * stored in a collection.
   *
   * @param array $audience_ids
   *   An array of audience IDs to filter the results by.
   * @param bool $reset
   *   Force a refresh of the audiences from Mailchimp.
   *
   * @return array
   *   An array of audience data objects.
   */
  public function getAudiences(array $audience_ids = [], bool $reset = FALSE): array {
    $collection = $this->keyvalue->get('mailchimp_lists');
    $audiences = $reset ? [] : $collection->get('lists', []);

    // If we have no stored audiences, or we are forcing a refresh, get them
    // from Mailchimp.
    if ($audiences === []) {
      try {
        /** @var \Mailchimp\MailchimpLists $mcapi */
        $mcapi = $this->getApiObject('MailchimpLists');
        if ($mcapi != NULL) {
          $result = $mcapi->getLists(['count' => 500]);

          if ($result->total_items > 0) {
            foreach ($result->lists as $list) {
              $int_category_data = $mcapi->getInterestCategories($list->id, ['count' => 500]);
              if ($int_category_data->total_items > 0) {

                $list->intgroups = [];
                foreach ($int_category_data->categories as $interest_category) {
                  $interest_data = $mcapi->getInterests($list->id, $interest_category->id, ['count' => 500]);

                  if ($interest_data->total_items > 0) {
                    $interest_category->interests = $interest_data->interests;
                  }

                  $list->intgroups[] = $interest_category;
                }
              }

              $audiences[$list->id] = $list;

              // Append mergefields:
              $mergefields = $mcapi->getMergeFields($list->id, ['count' => 500]);
              if ($mergefields->total_items > 0) {
                $audiences[$list->id]->mergevars = $mergefields->merge_fields;
              }
            }
          }

          uasort($audiences, '_mailchimp_list_cmp');

          if ($reset) {
            // Delete entire collection. This will also cause merge vars to be
            // refreshed when they are requested.
            // @see \Drupal\mailchimp\ApiService::getMergevars()
            $collection->deleteAll();
          }
          $collection->set('lists', $audiences);
        }
      }
      catch (\Exception $e) {
        $this->loggerChannel->error('An error occurred requesting audience information from Mailchimp. "{message}"', [
          'message' => $e->getMessage(),
        ]);
      }
    }

    // There was a problem getting audiences, which was probably already logged.
    if (!isset($audiences) || is_null($audiences)) {
      return [];
    }

    // Filter by given IDs.
    if (!empty($audience_ids)) {
      $filtered_lists = [];

      foreach ($audience_ids as $id) {
        if (array_key_exists($id, $audiences)) {
          $filtered_lists[$id] = $audiences[$id];
        }
      }

      return $filtered_lists;
    }
    else {
      return $audiences;
    }
  }

  /**
   * Wrapper around MailchimpLists->getMergeFields().
   *
   * @param array $audience_ids
   *   Array of Mailchimp audience IDs.
   * @param bool $reset
   *   Set to TRUE if mergevars should not be loaded from cache.
   *
   * @return array
   *   Struct describing mergevars for the specified audiences.
   */
  public function getMergevars(array $audience_ids, bool $reset = FALSE) : array {
    $mergevars = [];
    $collection = $this->keyvalue->get('mailchimp_lists');

    if (!$reset) {
      foreach ($audience_ids as $key => $audience_id) {

        $state_data = $collection->get("list_{$audience_id}_mergevars");
        // Get cached data and unset from our remaining audiences to query.
        if ($state_data) {
          $mergevars[$audience_id] = $state_data;
          unset($audience_ids[$key]);
        }
      }
    }

    // Get the uncached merge vars from Mailchimp.
    if (count($audience_ids)) {
      /** @var \Mailchimp\MailchimpLists $mc_lists */
      $mc_lists = $this->getApiObject('MailchimpLists');
      $audience_id = NULL;

      try {
        if (!$mc_lists) {
          throw new Exception('Cannot get merge vars without Mailchimp API. Check API key has been entered.');
        }

        foreach ($audience_ids as $audience_id) {
          // Add default EMAIL merge var for all lists.
          $mergevars[$audience_id] = [
            (object) [
              'tag' => 'EMAIL',
              'name' => t('Email Address'),
              'type' => 'email',
              'required' => TRUE,
              'default_value' => '',
              'public' => TRUE,
              'display_order' => 1,
              'options' => (object) [
                'size' => 25,
              ],
            ],
          ];

          $result = $mc_lists->getMergeFields($audience_id, ['count' => 500]);

          if ($result->total_items > 0) {
            $mergevars[$audience_id] = array_merge($mergevars[$audience_id], $result->merge_fields);
          }

          $collection->set("list_{$audience_id}_mergevars", $mergevars[$audience_id]);
        }
      }

      catch (\Exception $e) {
        $this->loggerChannel->error('An error occurred requesting mergevars for list {list}. "{message}"', [
          'list' => $audience_id,
          'message' => $e->getMessage(),
        ]);
      }
    }

    return $mergevars;
  }

}
