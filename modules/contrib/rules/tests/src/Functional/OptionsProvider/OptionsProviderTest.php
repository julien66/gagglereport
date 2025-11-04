<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Functional\OptionsProvider;

use Drupal\Core\Form\OptGroup;
use Drupal\Tests\BrowserTestBase;
use Drupal\rules\TypedData\Options\EntityBundleOptions;
use Drupal\rules\TypedData\Options\EntityTypeOptions;
use Drupal\rules\TypedData\Options\FieldListOptions;
use Drupal\rules\TypedData\Options\LanguageOptions;
use Drupal\rules\TypedData\Options\NodeTypeOptions;

/**
 * Tests using option providers.
 *
 * @group Rules
 */
class OptionsProviderTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   *
   * The output of some options providers depends on the enabled modules, so
   * this list can't be modified without also changing the expected results
   * below.
   */
  protected static $modules = [
    'rules',
    'node',
    'system',
    'taxonomy',
    'typed_data',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The installation profile to use for testing.
   *
   * We use the 'standard' profile because we need a large variety of entity
   * types, content types, fields, etc. to test our options providers.
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * @var \Drupal\Core\DependencyInjection\ClassResolver
   */
  protected $classResolver;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // The core OptionsProviderResolver uses this service to instantiate
    // options providers when given a ::class.
    $this->classResolver = $this->container->get('class_resolver');
  }

  /**
   * Tests output of options providers.
   *
   * @param string $definition
   *   A string class constant to identify the options provider class to test.
   * @param array $options
   *   An associative array containing the 'value' => 'option' pairs expected
   *   from the options provider being tested.
   *
   * @dataProvider provideOptionsProviders
   */
  public function testOptionsProvider(string $definition, array $options): void {
    $provider = $this->classResolver->getInstanceFromDefinition($definition);

    $flatten_options = OptGroup::flattenOptions($options);
    $values = array_keys($flatten_options);

    $this->assertNotNull($provider);
    $this->assertEquals($options, $provider->getPossibleOptions());
    $this->assertEquals($values, $provider->getPossibleValues());
    $this->assertEquals($options, $provider->getSettableOptions());
    $this->assertEquals($values, $provider->getSettableValues());
  }

  /**
   * Data provider - provides test data for testOptionsProviders().
   */
  public static function provideOptionsProviders(): array {
    $output = [
      'Entity bundles' => [
        EntityBundleOptions::class, [
          'Comment' => [
            'comment' => 'Default comments (comment)',
          ],
          'Contact message' => [
            'feedback' => 'Website feedback (feedback)',
            'personal' => 'Personal contact form (personal)',
          ],
          'Content' => [
            'article' => 'Article',
            'page' => 'Basic page (page)',
          ],
          'Content block' => [
            'basic' => 'Basic block (basic)',
          ],
          'Custom menu link' => [
            'menu_link_content' => 'Custom menu link (menu_link_content)',
          ],
          'File' => [
            'file' => 'File',
          ],
          'Shortcut link' => [
            'default' => 'Default',
          ],
          'Taxonomy term' => [
            'tags' => 'Tags',
          ],
          'URL alias' => [
            'path_alias' => 'URL alias (path_alias)',
          ],
          'User' => [
            'user' => 'User',
          ],
        ],
      ],
      'Entity types' => [
        EntityTypeOptions::class, [
          'comment' => 'Comment',
          'contact_message' => 'Contact message',
          'node' => 'Content (node)',
          'block_content' => 'Content block (block_content)',
          'menu_link_content' => 'Custom menu link (menu_link_content)',
          'file' => 'File',
          'shortcut' => 'Shortcut link (shortcut)',
          'taxonomy_term' => 'Taxonomy term',
          'path_alias' => 'URL alias (path_alias)',
          'user' => 'User',
        ],
      ],
      'Fields' => [
        FieldListOptions::class, [
          'access' => 'access (timestamp)',
          'alias' => 'alias (string)',
          'body' => 'body (text_with_summary)',
          'bundle' => 'bundle (string)',
          'changed' => 'changed (changed)',
          'cid' => 'cid (integer)',
          'comment' => 'comment (comment)',
          'comment_body' => 'comment_body (text_long)',
          'comment_type' => 'comment_type (entity_reference)',
          'contact_form' => 'contact_form (entity_reference)',
          'copy' => 'copy (boolean)',
          'created' => 'created (created)',
          'default_langcode' => 'default_langcode (boolean)',
          'description' => 'description (text_long)',
          'enabled' => 'enabled (boolean)',
          'entity_id' => 'entity_id (entity_reference)',
          'entity_type' => 'entity_type (string)',
          'expanded' => 'expanded (boolean)',
          'external' => 'external (boolean)',
          'fid' => 'fid (integer)',
          'field_image' => 'field_image (image)',
          'field_name' => 'field_name (string)',
          'field_tags' => 'field_tags (entity_reference)',
          'filemime' => 'filemime (string)',
          'filename' => 'filename (string)',
          'filesize' => 'filesize (integer)',
          'homepage' => 'homepage (uri)',
          'hostname' => 'hostname (string)',
          'id' => 'id (integer)',
          'info' => 'info (string)',
          'init' => 'init (email)',
          'langcode' => 'langcode (language)',
          'link' => 'link (link)',
          'login' => 'login (timestamp)',
          'mail' => 'mail (email)',
          'menu_name' => 'menu_name (string)',
          'message' => 'message (string_long)',
          'name' => 'name (string)',
          'nid' => 'nid (integer)',
          'parent' => 'parent (entity_reference)',
          'pass' => 'pass (password)',
          'path' => 'path (path)',
          'pid' => 'pid (entity_reference)',
          'preferred_admin_langcode' => 'preferred_admin_langcode (language)',
          'preferred_langcode' => 'preferred_langcode (language)',
          'promote' => 'promote (boolean)',
          'recipient' => 'recipient (entity_reference)',
          'rediscover' => 'rediscover (boolean)',
          'reusable' => 'reusable (boolean)',
          'revision_created' => 'revision_created (created)',
          'revision_default' => 'revision_default (boolean)',
          'revision_id' => 'revision_id (integer)',
          'revision_log' => 'revision_log (string_long)',
          'revision_log_message' => 'revision_log_message (string_long)',
          'revision_timestamp' => 'revision_timestamp (created)',
          'revision_translation_affected' => 'revision_translation_affected (boolean)',
          'revision_uid' => 'revision_uid (entity_reference)',
          'revision_user' => 'revision_user (entity_reference)',
          'roles' => 'roles (entity_reference)',
          'shortcut_set' => 'shortcut_set (entity_reference)',
          'status' => 'status (boolean)',
          'sticky' => 'sticky (boolean)',
          'subject' => 'subject (string)',
          'thread' => 'thread (string)',
          'tid' => 'tid (integer)',
          'timezone' => 'timezone (string)',
          'title' => 'title (string)',
          'type' => 'type (entity_reference)',
          'uid' => 'uid (integer)',
          'uri' => 'uri (file_uri)',
          'user_picture' => 'user_picture (image)',
          'uuid' => 'uuid (uuid)',
          'vid' => 'vid (entity_reference)',
          'weight' => 'weight (integer)',
        ],
      ],
      'Languages' => [
        LanguageOptions::class, [
          'en' => 'English - default (en)',
          'und' => 'Not specified',
        ],
      ],
      'Node types' => [
        NodeTypeOptions::class, [
          'article' => 'Article',
          'page' => 'Basic page (page)',
        ],
      ],
    ];

    return $output;
  }

}
