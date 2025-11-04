<?php

namespace Drupal\core_event_dispatcher\ValueObject;

use Drupal\Component\Render\MarkupInterface;

/**
 * Token ValueObject.
 *
 * Convenience object to handle the integrity and assembly of token types.
 */
final class TokenType {

  /**
   * Type.
   *
   * @var string
   */
  private string $type;

  /**
   * Name.
   *
   * @var string|\Drupal\Component\Render\MarkupInterface
   */
  private string|MarkupInterface $name;

  /**
   * Description.
   *
   * @var string|\Drupal\Component\Render\MarkupInterface|null
   */
  private string|MarkupInterface|NULL $description = NULL;

  /**
   * Needs data.
   *
   * @var string|null
   */
  private ?string $needsData = NULL;

  /**
   * Use create function instead.
   */
  private function __construct() {
  }

  /**
   * Token type factory.
   *
   * @param string $type
   *   The group name, like 'node'.
   * @param \Drupal\Component\Render\MarkupInterface|string $name
   *   The print-able name of the type.
   *
   * @return self
   *   A new instance.
   */
  public static function create(string $type, MarkupInterface|string $name): self {
    $instance = new self();
    $instance->type = $type;
    $instance->name = $name;
    return $instance;
  }

  /**
   * Set description and return a new instance.
   *
   * @param \Drupal\Component\Render\MarkupInterface|string $description
   *   The description of the token type.
   *
   * @return self
   *   A new instance with the description.
   */
  public function setDescription(MarkupInterface|string $description): self {
    $clone = clone $this;
    $clone->description = $description;
    return $clone;
  }

  /**
   * Set the needs data and return a new instance.
   *
   * @param string $needsData
   *   The needs data.
   *
   * @return self
   *   A new instance with the needs data property.
   */
  public function setNeedsData(string $needsData): self {
    $clone = clone $this;
    $clone->needsData = $needsData;
    return $clone;
  }

  /**
   * Getter.
   *
   * @return string|\Drupal\Component\Render\MarkupInterface|null
   *   The description.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Getter.
   *
   * @return string|null
   *   The needs data property.
   */
  public function getNeedsData(): ?string {
    return $this->needsData;
  }

  /**
   * Getter.
   *
   * @return string
   *   The token type like 'node'.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Getter.
   *
   * @return string
   *   The token type label, like 'The Node type'.
   */
  public function getName(): string {
    return $this->name;
  }

}
