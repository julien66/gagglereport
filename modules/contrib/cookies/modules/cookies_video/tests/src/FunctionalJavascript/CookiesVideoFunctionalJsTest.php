<?php

namespace Drupal\Tests\cookies_video\FunctionalJavascript;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\media\Entity\Media;
use Drupal\Tests\cookies\FunctionalJavascript\CookiesFunctionalJsTestBase;
use Drupal\Tests\field\Traits\EntityReferenceFieldCreationTrait;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;

/**
 * Class for testing the cookies_video submodule.
 *
 * @group cookies
 */
class CookiesVideoFunctionalJsTest extends CookiesFunctionalJsTestBase {
  use MediaTypeCreationTrait;
  use EntityReferenceFieldCreationTrait;

  /**
   * Default theme.
   *
   * @var string
   */
  protected $defaultTheme = 'claro';

  /**
   * A test node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'cookies_video',
    'media_test_oembed',
    'field_ui',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Get the display repository:
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    // Enable URLs for media:
    \Drupal::configFactory()
      ->getEditable('media.settings')
      ->set('standalone_url', TRUE)
      ->save(TRUE);
    $this->container->get('router.builder')->rebuild();
    $this->createContentType(['type' => 'article', 'name' => 'Article']);
    $this->createMediaType('oembed:video', [
      'id' => 'remote_video',
      'new_revision' => TRUE,
    ]);
    // Enable the field_media_oembed_video for testing:
    $display_repository->getViewDisplay('media', 'remote_video')
      ->setComponent('field_media_oembed_video', [
        'type' => 'oembed',
        'settings' => [],
      ])
      ->save();
    // Create an entity reference field:
    $this->createEntityReferenceField('node', 'article', 'field_cookies_video', 'Field Cookies Video', 'media', 'default', ['target_bundles' => ['video']], FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);
    // Enable form display:
    $display_repository->getFormDisplay('node', 'article')
      ->setComponent('field_cookies_video', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [],
      ])
      ->save();
    // Enable view display:
    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('field_cookies_video', [
        'type' => 'entity_reference_entity_view',
        'settings' => [],
      ])
      ->save();

    // Create media entities:
    $media1 = Media::create([
      'bundle' => 'remote_video',
      'name' => 'Video1',
      'field_media_oembed_video' => [
        0 => [
          'value' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
        ],
      ],
    ]);
    $media1->save();

    $media2 = Media::create([
      'bundle' => 'remote_video',
      'name' => 'Video1',
      'field_media_oembed_video' => [
        0 => [
          'value' => 'https://www.youtube.com/watch?v=7-qGKqveZaM',
        ],
      ],
    ]);
    $media2->save();

    $media3 = Media::create([
      'bundle' => 'remote_video',
      'name' => 'Video1',
      'field_media_oembed_video' => [
        0 => [
          'value' => 'https://www.youtube.com/watch?v=D-eDNDfU3oY',
        ],
      ],
    ]);
    $media3->save();

    // Create the test node:
    $this->node = $this->drupalCreateNode([
      'title' => $this->randomString(),
      'type' => 'article',
      'body' => 'Body field value.',
      'field_cookies_video' => [
        0 => [
          'target_id' => $media1->id(),
        ],
        1 => [
          'target_id' => $media2->id(),
        ],
        2 => [
          'target_id' => $media3->id(),
        ],
      ],
    ]);

  }

  /**
   * Tests correct field display after consent.
   *
   * Tests the correct display of a media field used inside a node after
   * consent, when three media items are displayed in the same field.
   */
  public function testConsentAllDisplayCorrectlyUnlimited() {
    $session = $this->assertSession();
    $this->drupalGet('node/' . $this->node->id());
    // Check if the blocking Banners exist:
    // Banner one:
    $session->elementExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(1) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(1) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video', 'src', '');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(1) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video', 'data-src', 'https%3A//www.youtube.com/watch%3Fv%3DjNQXAC9IVRw');
    // Banner two:
    $session->elementExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(2) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(2) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video', 'src', '');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(2) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video', 'data-src', 'https%3A//www.youtube.com/watch%3Fv%3D7-qGKqveZaM');
    // Banner three:
    $session->elementExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(3) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(3) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video', 'src', '');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(3) > article > div.field.field--name-field-media-oembed-video > div.field__item > div > iframe.cookies-video', 'data-src', 'https%3A//www.youtube.com/watch%3Fv%3DD-eDNDfU3oY');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);

    // Check if iframes are unblocked:
    // Banner one:
    $session->elementExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(1) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video');
    $session->elementAttributeNotExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(1) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video', 'data-src');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(1) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video', 'src', 'https%3A//www.youtube.com/watch%3Fv%3DjNQXAC9IVRw');
    // Banner two:
    $session->elementExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(2) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video');
    $session->elementAttributeNotExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(2) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video', 'data-src');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(2) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video', 'src', 'https%3A//www.youtube.com/watch%3Fv%3D7-qGKqveZaM');
    // Banner three:
    $session->elementExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(3) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video');
    $session->elementAttributeNotExists('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(3) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video', 'data-src');
    $session->elementAttributeContains('css', 'div.field.field--name-field-cookies-video > div.field__items > div:nth-child(3) > article > div.field.field--name-field-media-oembed-video > div.field__item > iframe.cookies-video', 'src', 'https%3A//www.youtube.com/watch%3Fv%3DD-eDNDfU3oY');

  }

}
