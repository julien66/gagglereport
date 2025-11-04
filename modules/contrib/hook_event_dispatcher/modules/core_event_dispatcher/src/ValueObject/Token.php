<?php

namespace Drupal\core_event_dispatcher\ValueObject;

use Drupal\Component\Render\MarkupInterface;

/**
 * Token ValueObject.
 *
 * Convenience object to handle the integrity and assembly of tokens.
 */
final class Token {

  /**
   * Type.
   *
   * @var string
   */
  private string $type;

  /**
   * Token.
   *
   * @var string
   */
  private string $token;

  /**
   * Description.
   *
   * @var \Drupal\Component\Render\MarkupInterface|string|null
   */
  private MarkupInterface|string|NULL $description = NULL;

  /**
   * Name.
   *
   * @var \Drupal\Component\Render\MarkupInterface|string
   */
  private MarkupInterface|string $name;

  /**
   * Is a dynamic field.
   *
   * @var bool
   */
  private bool $dynamic = FALSE;

  /**
   * Use create function instead.
   */
  private function __construct() {
  }

  /**
   * Token factory function.
   *
   * @param string $type
   *   The group name, like 'node'.
   * @param string $token
   *   The token, like 'url' or 'id'.
   * @param \Drupal\Component\Render\MarkupInterface|string $name
   *   The print-able name of the type.
   *
   * @return \Drupal\core_event_dispatcher\ValueObject\Token
   *   Creates a new token.
   */
  public static function create(string $type, string $token, MarkupInterface|string $name): self {
    $instance = new self();
    $instance->type = $type;
    $instance->token = $token;
    $instance->name = $name;
    return $instance;
  }

  /**
   * Set description and return a new instance.
   *
   * @param \Drupal\Component\Render\MarkupInterface|string $description
   *   The description of the token type.
   *
   * @return \Drupal\core_event_dispatcher\ValueObject\Token
   *   New instance with the given description.
   */
  public function setDescription(MarkupInterface|string $description): self {
    $clone = clone $this;
    $clone->description = $description;
    return $clone;
  }

  /**
   * Set whether or not the token is dynamic.
   *
   * @param bool $dynamic
   *   TRUE if the token is dynamic.
   *
   * @return \Drupal\core_event_dispatcher\ValueObject\Token
   *   New instance with the given dynamic.
   */
  public function setDynamic(bool $dynamic): self {
    $clone = clone $this;
    $clone->dynamic = $dynamic;
    return $clone;
  }

  /**
   * Getter.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string|null
   *   The description.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Getter.
   *
   * @return string
   *   The type like 'node'.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Getter.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   *   The label of the token.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Getter.
   *
   * @return string
   *   The token name like 'url'.
   */
  public function getToken(): string {
    return $this->token;
  }

  /**
   * Getter.
   *
   * @return bool
   *   Whether or not the token is dynamic.
   */
  public function isDynamic(): bool {
    return $this->dynamic;
  }

}
