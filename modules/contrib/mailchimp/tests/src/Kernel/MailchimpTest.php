<?php

namespace Drupal\Tests\mailchimp\Kernel;

use Mailchimp\MailchimpAPIException;
use Mailchimp\Tests\MailchimpLists;

/**
 * Tests the mailchimp module API.
 *
 * @group mailchimp
 */
class MailchimpTest extends MailchimpKernelTestBase {

  /**
   * Tests trying getting a member that does exist.
   *
   * @covers mailchimp_get_memberinfo
   */
  public function testGetExistingMember() {
    $member_info = mailchimp_get_memberinfo('foo', 'foo@example.com');
    $this->assertTrue(isset($member_info->status), 'Failed asserting that a member does not exist.');
  }

  /**
   * Tests trying getting a member that does not exist yet.
   *
   * @covers mailchimp_get_memberinfo
   */
  public function testGetNonExistingMember() {
    $this->setMemberNotExist();

    // Assert that the member does not exist yet.
    $member_info = mailchimp_get_memberinfo('foo', 'foo@example.com');
    $this->assertFalse(isset($member_info->status), 'Failed asserting that a member does not exist.');
  }

  /**
   * Tests subscribing a new mail address using double opt-in.
   *
   * @covers mailchimp_subscribe_process
   */
  public function testSubscribeProcessNewDoubleOptin() {
    // Make sure that the member does not exist yet.
    $this->setMemberNotExist();

    $result = mailchimp_subscribe_process('foo', 'foo@example.com', NULL, [], TRUE);
    $expected = (object) [
      'id' => 'b48def645758b95537d4424c84d1a9ff',
      'email_address' => 'foo@example.com',
      'status' => 'pending',
      'email_type' => 'html',
    ];
    $this->assertEquals($expected, $result);
  }

  /**
   * Tests subscribing an existing mail address using double opt-in.
   *
   * @covers mailchimp_subscribe_process
   */
  public function testSubscribeProcessExistingDoubleOptin() {
    $result = mailchimp_subscribe_process('foo', 'foo@example.com', NULL, [], TRUE);
    $expected = (object) [
      'id' => 'b48def645758b95537d4424c84d1a9ff',
      'email_address' => 'foo@example.com',
      'status' => 'subscribed',
      'email_type' => 'html',
    ];
    $this->assertEquals($expected, $result);
  }

  /**
   * Sets a response that means that a member does not exist yet.
   *
   * MailchimpLists::getMemberInfo() is modified to throw an exception,
   * meaning that the requested member does not exist in the requested audience.
   */
  protected function setMemberNotExist(): void {
    $api_class = $this->container->get('mailchimp.client_factory')->getApiClass();

    // Override MailchimpLists class in order to mimic the situation that a
    // member does not exist yet.
    $lists = $this->getMockbuilder(MailchimpLists::class)
      ->onlyMethods(['getMemberInfo'])
      ->setConstructorArgs([$api_class])
      ->getMock();
    $lists->expects($this->atLeastOnce())
      ->method('getMemberInfo')
      ->will($this->throwException(new MailchimpAPIException('404: Resource Not Found - The requested resource could not be found.')));

    $this->container->get('mailchimp.client_factory')
      ->setInstance('MailchimpLists', $lists);
  }

}
