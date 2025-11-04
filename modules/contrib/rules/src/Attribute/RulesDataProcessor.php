<?php

declare(strict_types=1);

namespace Drupal\rules\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * PHP Attribute class for Rules data processor plugins.
 *
 * This attribute is used to identify plugins that want to alter variables
 * before they are passed on during Rules execution.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class RulesDataProcessor extends Plugin {

  /**
   * Constructs a Rules data processor attribute object.
   *
   * @param string $id
   *   The plugin ID. The machine-name of the data processor.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $label
   *   The human-readable name of the data processor.
   * @param array $types
   *   (optional) The data types this data processor can be applied to.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $description
   *   (optional) A short description of the data processor.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    public readonly string $id,
    public readonly TranslatableMarkup $label,
    public readonly ?array $types = [],
    public readonly ?TranslatableMarkup $description = NULL,
    public readonly ?string $deriver = NULL,
  ) {}

}
