<?php

namespace Drupal\core_event_dispatcher\Event\Language;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Url;
use Drupal\core_event_dispatcher\LanguageHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class LanguageSwitchLinksAlterEvent.
 */
#[HookEvent(id: 'language_switch_links_alter', alter: 'language_switch_links')]
class LanguageSwitchLinksAlterEvent extends Event implements EventInterface {

  /**
   * The links array.
   *
   * @var array
   */
  private $links = [];

  /**
   * LanguageSwitchLinksAlterEvent constructor.
   *
   * @param array $links
   *   The links array.
   * @param string $type
   *   The language type.
   * @param \Drupal\Core\Url $path
   *   The request path.
   */
  public function __construct(array &$links, private readonly string $type, private readonly Url $path) {
    $this->links = &$links;
  }

  /**
   * Get the links array.
   *
   * @return array
   *   The links array.
   */
  public function &getLinks(): array {
    return $this->links;
  }

  /**
   * Set the link for a specific language code.
   *
   * @param string $langcode
   *   The link language code.
   * @param array $link
   *   The link path.
   */
  public function setLinkForLanguage($langcode, array $link): void {
    $this->links[$langcode] = $link;
  }

  /**
   * Get the language type.
   *
   * @return string
   *   The language type.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Get the request path.
   *
   * @return \Drupal\Core\Url
   *   The link path.
   */
  public function getPath(): Url {
    return $this->path;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return LanguageHookEvents::LANGUAGE_SWITCH_LINKS_ALTER;
  }

}
