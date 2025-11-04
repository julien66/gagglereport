<?php

declare(strict_types=1);

namespace Drupal\rules\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * PHP Attribute class for Rules expression plugins.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class RulesExpression extends Plugin {

  /**
   * Constructs a Rules expression attribute object.
   *
   * @param string $id
   *   The plugin ID. The machine-name of the expression.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $label
   *   The human-readable name of the expression.
   * @param string $form_class
   *   (optional) The class name of the form for displaying/editing this
   *   expression.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $description
   *   (optional) A short description of the expression.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    public readonly string $id,
    public readonly TranslatableMarkup $label,
    public readonly ?string $form_class = NULL,
    public readonly ?TranslatableMarkup $description = NULL,
    public readonly ?string $deriver = NULL,
  ) {}

}
