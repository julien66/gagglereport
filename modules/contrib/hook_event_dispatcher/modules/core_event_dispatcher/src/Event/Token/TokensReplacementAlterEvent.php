<?php

namespace Drupal\core_event_dispatcher\Event\Token;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\core_event_dispatcher\TokenHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Provides event to alter token replacements.
 *
 * @see hook_tokens_alter
 */
#[HookEvent(id: 'tokens_replacement_alter', alter: 'tokens')]
final class TokensReplacementAlterEvent extends Event implements EventInterface {

  /**
   * An array of replacement values.
   *
   * @var array
   */
  private array $replacements = [];

  /**
   * Constructor.
   *
   * @param array &$replacements
   *   An associative array of replacements returned by hook_tokens().
   * @param array $context
   *   The context in which hook_tokens() was called. An associative array with
   *   the following keys, which have the same meaning as the corresponding
   *   parameters of hook_tokens():
   *   - 'type'
   *   - 'tokens'
   *   - 'data'
   *   - 'options'
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleableMetadata
   *   The bubbleable metadata. In case you alter an existing token based upon
   *   a data source that isn't in $context['data'], you must add that
   *   dependency to $bubbleableMetadata.
   */
  public function __construct(array &$replacements, private readonly array $context, private readonly BubbleableMetadata $bubbleableMetadata) {
    $this->replacements = &$replacements;
  }

  /**
   * Getter.
   *
   * @return array
   *   An associative array of replacements returned by hook_tokens().
   */
  public function &getReplacements(): array {
    return $this->replacements;
  }

  /**
   * Getter.
   *
   * @return array
   *   The context given inside the hook_tokens_alter.
   */
  public function getContext(): array {
    return $this->context;
  }

  /**
   * Getter.
   *
   *  The bubbleable metadata. In case you alter an existing token based upon
   *  a data source that isn't in $context['data'], you must add that
   *  dependency to $bubbleableMetadata.
   *
   * @return \Drupal\Core\Render\BubbleableMetadata
   *   The metadata.
   */
  public function getBubbleableMetadata(): BubbleableMetadata {
    return $this->bubbleableMetadata;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return TokenHookEvents::TOKEN_REPLACEMENT_ALTER;
  }

}
