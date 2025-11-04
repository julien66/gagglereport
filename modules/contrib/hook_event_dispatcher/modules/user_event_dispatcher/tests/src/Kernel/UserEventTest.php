<?php

namespace Drupal\Tests\user_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\user_event_dispatcher\Event\User\UserCancelEvent;
use Drupal\user_event_dispatcher\Event\User\UserCancelMethodsAlterEvent;
use Drupal\user_event_dispatcher\Event\User\UserFormatNameAlterEvent;
use Drupal\user_event_dispatcher\Event\User\UserLoginEvent;
use Drupal\user_event_dispatcher\Event\User\UserLogoutEvent;
use Drupal\user_event_dispatcher\UserHookEvents;

/**
 * Class UserEventTest.
 *
 * @group hook_event_dispatcher
 * @group user_event_dispatcher
 */
class UserEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'hook_event_dispatcher',
    'user_event_dispatcher',
  ];

  /**
   * The user entity.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $this->user = $this->container->get('entity_type.manager')
      ->getStorage('user')
      ->create([
        'name' => $this->randomMachineName(),
      ]);
  }

  /**
   * User cancel event test.
   *
   * @covers \Drupal\user_event_dispatcher\Event\User\UserCancelEvent
   *
   * @throws \Exception
   */
  public function testUserCancelEvent(): void {
    $this->installEntitySchema('user');
    $this->installSchema('system', 'sequences');
    $this->user->save();

    $this->listen(UserHookEvents::USER_CANCEL, 'onUserCancel');
    user_cancel([], $this->user->id(), 'user_cancel_block');
  }

  /**
   * Callback for UserCancelEvent.
   *
   * @param \Drupal\user_event_dispatcher\Event\User\UserCancelEvent $event
   *   The event.
   */
  public function onUserCancel(UserCancelEvent $event): void {
    $this->assertEmpty($event->getEdit());
    $this->assertEquals($this->user->id(), $event->getAccount()->id());
    $this->assertEquals('user_cancel_block', $event->getMethod());
  }

  /**
   * User cancel methods alter event test.
   *
   * @covers \Drupal\user_event_dispatcher\Event\User\UserCancelMethodsAlterEvent
   *
   * @throws \Exception
   */
  public function testUserCancelMethodsAlterEvent(): void {
    $this->listen(UserHookEvents::USER_CANCEL_METHODS_ALTER, 'onUserCancelMethodsAlter');
    $methods = user_cancel_methods();

    $this->assertArrayHasKey('user_cancel_test', $methods['#options']);
    $this->assertEquals('Test', $methods['#options']['user_cancel_test']);
  }

  /**
   * Callback for UserCancelMethodsAlterEvent.
   *
   * @param \Drupal\user_event_dispatcher\Event\User\UserCancelMethodsAlterEvent $event
   *   The event.
   */
  public function onUserCancelMethodsAlter(UserCancelMethodsAlterEvent $event): void {
    $methods = &$event->getMethods();
    $methods['user_cancel_test'] = [
      'title' => 'Test',
    ];
  }

  /**
   * User login event test.
   *
   * @covers \Drupal\user_event_dispatcher\Event\User\UserLoginEvent
   *
   * @throws \Exception
   */
  public function testUserLoginEvent(): void {
    $this->installEntitySchema('user');
    $this->listen(UserHookEvents::USER_LOGIN, 'onUserLogin');
    user_login_finalize($this->user);
  }

  /**
   * Callback for UserLoginEvent.
   *
   * @param \Drupal\user_event_dispatcher\Event\User\UserLoginEvent $event
   *   The event.
   */
  public function onUserLogin(UserLoginEvent $event): void {
    $this->assertEquals($this->user->id(), $event->getAccount()->id());
  }

  /**
   * User logout event test.
   *
   * @covers \Drupal\user_event_dispatcher\Event\User\UserLogoutEvent
   *
   * @throws \Exception
   */
  public function testUserLogoutEvent(): void {
    $this->listen(UserHookEvents::USER_LOGOUT, 'onUserLogout');
    user_logout();
  }

  /**
   * Callback for UserLogoutEvent.
   *
   * @param \Drupal\user_event_dispatcher\Event\User\UserLogoutEvent $event
   *   The event.
   */
  public function onUserLogout(UserLogoutEvent $event): void {
    $this->assertTrue($event->getAccount()->isAnonymous());
  }

  /**
   * User format name alter event test.
   *
   * @covers \Drupal\user_event_dispatcher\Event\User\UserFormatNameAlterEvent
   *
   * @throws \Exception
   */
  public function testUserFormatNameAlterEvent(): void {
    $this->listen(UserHookEvents::USER_FORMAT_NAME_ALTER, 'onUserFormatNameAlter');
    $name = $this->user->getDisplayName();
    $this->assertEquals('Test name', $name);
  }

  /**
   * Callback for UserFormatNameAlterEvent.
   *
   * @param \Drupal\user_event_dispatcher\Event\User\UserFormatNameAlterEvent $event
   *   The event.
   */
  public function onUserFormatNameAlter(UserFormatNameAlterEvent $event): void {
    $name = &$event->getName();
    $this->assertEquals($name, $event->getAccount()->getAccountName());

    $name = 'Test name';
  }

}
