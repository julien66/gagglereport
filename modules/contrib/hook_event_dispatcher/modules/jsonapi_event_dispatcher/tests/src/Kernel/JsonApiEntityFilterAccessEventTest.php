<?php

namespace Drupal\Tests\jsonapi_event_dispatcher\Kernel;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\jsonapi\JsonApiResource\Data;
use Drupal\jsonapi\JsonApiResource\JsonApiDocumentTopLevel;
use Drupal\jsonapi\ResourceType\ResourceType;
use Drupal\jsonapi_event_dispatcher\Event\JsonApiEntityFilterAccessEvent;
use Drupal\jsonapi_event_dispatcher\JsonApiHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JsonapiEntityFilterAccessEvent.
 *
 * @covers \Drupal\jsonapi_event_dispatcher\Event\JsonApiEntityFilterAccessEvent
 *
 * @group hook_event_dispatcher
 * @group jsonapi_event_dispatcher
 */
class JsonApiEntityFilterAccessEventTest extends KernelTestBase {

  use ListenerTrait;
  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'text',
    'user',
    'file',
    'entity_test',
    'serialization',
    'jsonapi',
    'hook_event_dispatcher',
    'jsonapi_event_dispatcher',
  ];

  /**
   * Test JsonapiEntityFilterAccessEvent.
   *
   * @throws \Exception
   */
  public function testJsonapiEntityFilterAccessEvent(): void {
    $this->listen(JsonApiHookEvents::JSONAPI_ENTITY_FILTER_ACCESS, 'onJsonapiEntityFilterAccess');

    $this->setUpCurrentUser([], ['view test entity']);
    $entity = $this->createEntity();
    $request = $this->createRequest($entity->label());

    $resourceType = $this->container->get('jsonapi.resource_type.repository')->get('entity_test', 'entity_test');
    $this->assertInstanceOf(ResourceType::class, $resourceType);

    $response = $this->container->get('jsonapi.entity_resource')->getCollection($resourceType, $request);
    $responseData = $response->getResponseData();
    $this->assertInstanceOf(JsonApiDocumentTopLevel::class, $responseData);

    $data = $responseData->getData();
    $this->assertInstanceOf(Data::class, $data);
    $this->assertEquals(1, $data->count());
  }

  /**
   * Creates an entity test.
   *
   * @throws \Exception
   */
  private function createEntity(): EntityInterface {
    $this->installEntitySchema('entity_test');
    $entity = $this->container->get('entity_type.manager')->getStorage('entity_test')->create([
      'name' => $this->randomString(),
    ]);
    $entity->save();
    return $entity;
  }

  /**
   * Creates a JSON:API request.
   *
   * @param string $label
   *   The entity label.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   The JSON:API request.
   */
  private function createRequest(string $label): Request {
    $request = Request::create('/jsonapi/entity_test/entity_test');
    $request->query = new InputBag([
      'filter' => [
        'name' => $label,
      ],
    ]);
    return $request;
  }

  /**
   * Callback for JsonapiEntityFilterAccessEvent.
   *
   * @param \Drupal\jsonapi_event_dispatcher\Event\JsonApiEntityFilterAccessEvent $event
   *   The event.
   */
  public function onJsonapiEntityFilterAccess(JsonApiEntityFilterAccessEvent $event): void {
    $this->assertEquals('entity_test', $event->getEntityType()->id());
    $this->assertTrue($event->getAccount()->hasPermission('view test entity'));
    $event->addAccessResult(JSONAPI_FILTER_AMONG_ALL, AccessResult::allowed());
  }

}
