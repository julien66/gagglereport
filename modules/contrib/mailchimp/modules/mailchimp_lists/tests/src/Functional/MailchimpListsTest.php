<?php

namespace Drupal\Tests\mailchimp_lists\Functional;

/**
 * Tests core audience functionality.
 *
 * @group mailchimp
 */
class MailchimpListsTest extends MailchimpListsTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['mailchimp', 'mailchimp_lists', 'mailchimp_test'];

  /**
   * Tests that an audience can be loaded.
   */
  public function testGetList() {
    $list_id = '57afe96172';

    $list = mailchimp_get_list($list_id);

    $this->assertSame($list->id, $list_id);
    $this->assertSame($list->name, 'Test List One');
  }

  /**
   * Tests retrieval of a specific set of audiences.
   */
  public function testMultiListRetrieval() {
    $list_ids = [
      '57afe96172',
      'f4b7b26b2e',
    ];

    $lists = \Drupal::service('mailchimp.api')->getAudiences($list_ids);

    $this->assertSame(count($lists), 2, 'Tested correct audience count on retrieval.');

    $this->assertSame($lists[$list_ids[0]]->id, $list_ids[0]);
    $this->assertSame($lists[$list_ids[0]]->name, 'Test List One');

    $this->assertSame($lists[$list_ids[1]]->id, $list_ids[1]);
    $this->assertSame($lists[$list_ids[1]]->name, 'Test List Two');
  }

}
