<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\poll\Entity\Poll;

/**
 * Tests multilingual voting on a poll.
 *
 * @group poll
 */
class PollVoteMultilingualTest extends PollTestBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'language',
    'content_translation',
  ];

  /**
   * {@inheritdoc}
   */
  protected $adminPermissions = [
    'administer content translation',
    'administer languages',
    'create content translations',
    'update content translations',
    'translate any entity',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Allow anonymous users to vote on polls.
    user_role_change_permissions(AccountInterface::ANONYMOUS_ROLE, [
      'cancel own vote' => TRUE,
      'access polls' => TRUE,
    ]);

    $this->poll = $this->pollCreate(3);

    $this->poll->setAnonymousVoteAllow(TRUE)->save();

    // Set's up a poll as translated and translate the poll into CA.
    $this->drupalLogin($this->adminUser);

    // Add another language.
    $language = ConfigurableLanguage::createFromLangcode('ca');
    $language->save();

    // Make poll translatable.
    $this->drupalGet('admin/config/regional/content-language');
    $edit = [
      'entity_types[poll]' => TRUE,
      'entity_types[poll_choice]' => TRUE,
      'settings[poll][poll][translatable]' => TRUE,
      'settings[poll_choice][poll_choice][translatable]' => TRUE,
    ];
    $this->submitForm($edit, 'Save configuration');
    \Drupal::service('entity_field.manager')->clearCachedFieldDefinitions();

    // Translate a poll.
    $this->drupalGet('poll/' . $this->poll->id() . '/translations');
    $this->clickLink('Add');
    $edit = [
      'question[0][value]' => 'ca question',
      'choice[0][choice]' => 'ca choice 1',
      'choice[1][choice]' => 'ca choice 2',
      'choice[2][choice]' => 'ca choice 3',
    ];
    $this->submitForm($edit, 'Save');
    $this->drupalGet('ca/poll/' . $this->poll->id());
    $this->assertSession()->pageTextContains('ca choice 1');

    \Drupal::entityTypeManager()->getStorage('poll')->resetCache();
    \Drupal::entityTypeManager()->getStorage('poll_choice')->resetCache();
    $this->poll = Poll::load($this->poll->id());
  }

  /**
   * Tests multilingual voting on a poll.
   */
  public function testPollVoteMultilingual() {
    // Login as web user.
    $this->drupalLogin($this->webUser);

    // Record a vote.
    $edit = [
      'choice' => (string) $this->getChoiceId($this->poll, 2),
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    $this->drupalGet('ca/poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel vote' button appears.");
    $this->drupalGet('poll/' . $this->poll->id());

    // Cancel a vote.
    $this->submitForm([], 'Cancel vote');
    $this->assertSession()->pageTextContains('Your vote was cancelled.');
    $this->assertSession()->pageTextNotContains('Cancel your vote');

    // Vote again in reverse order.
    $edit = [
      'choice' => (string) $this->getChoiceIdByLabel($this->poll->getTranslation('ca'), 'ca choice 2'),
    ];
    $this->drupalGet('ca/poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel vote' button appears.");

    // Edit the original poll.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $this->poll->id() . '/edit');
    $edit = [
      'choice[0][choice]' => '',
      'choice[1][choice]' => 'choice 2',
      'choice[2][choice]' => 'choice 3',
      'choice[3][choice]' => 'choice 4',
    ];
    $this->submitForm($edit, 'Save');

    // Translate the new label.
    $this->drupalGet('ca/poll/' . $this->poll->id() . '/edit');
    $edit = [
      'choice[2][choice]' => 'ca choice 4',
    ];
    $this->submitForm($edit, 'Save');

    \Drupal::entityTypeManager()->getStorage('poll')->resetCache();
    \Drupal::entityTypeManager()->getStorage('poll_choice')->resetCache();
    $this->poll = Poll::load($this->poll->id());

    // Vote as anonymous user.
    $this->drupalLogout();
    $edit = [
      'choice' => (string) $this->getChoiceIdByLabel($this->poll->getTranslation('ca'), 'ca choice 4'),
    ];
    $this->drupalGet('ca/poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 2');
    $this->assertSession()->pageTextNotContains('ca choice 1');
    $this->assertSession()->pageTextContains('ca choice 4');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[1]')[0];
    $this->assertEquals($elements->getText(), '50% (1 vote)');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[3]')[0];
    $this->assertEquals($elements->getText(), '50% (1 vote)');

    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel vote' button appears.");
    $this->assertSession()->pageTextContains('Total votes: 2');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[1]')[0];
    $this->assertEquals($elements->getText(), '50% (1 vote)');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[3]')[0];
    $this->assertEquals($elements->getText(), '50% (1 vote)');
  }

  /**
   * Tests deletion of a multilingual poll.
   */
  public function testPollDeleteMultilingual() {
    // Log in as an admin user.
    $this->drupalLogin($this->adminUser);

    // Go to the poll overview page and check there are 2 polls listed.
    $this->drupalGet('admin/content/poll');
    $this->assertSession()->pageTextContains($this->poll->label());
    $this->assertSession()->pageTextContains('ca question');

    // Delete the poll translation.
    $this->drupalGet('ca/poll/' . $this->poll->id() . '/delete');
    $this->assertSession()->pageTextContains('Are you sure you want to delete the Catalan translation of the poll ca question?');
    $this->submitForm([], 'Delete Catalan translation');

    // Go to the poll overview page and check there is only 1 polls listed.
    $this->drupalGet('admin/content/poll');
    $this->assertSession()->pageTextContains($this->poll->label());
    $this->assertSession()->pageTextNotContains('ca question');
  }

}
