<?php

namespace Drupal\country\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;

/**
 * Plugin implementation of the 'country_default' widget.
 *
 * @FieldWidget(
 *   id = "country_default",
 *   label = @Translation("Country select options"),
 *   field_types = {
 *     "country"
 *   },
 *   multiple_values = TRUE
 * )
 */
class CountryDefaultWidget extends OptionsSelectWidget {

}
