<?php

namespace Drupal\field_example\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'field_example_color_background' formatter.
 *
 * This example demonstrates how a field formatter plugin can provide
 * configuration options to the user and then alter the output based on their
 * choices. We'll add a toggle, that defaults to on, for a feature that
 * attempts to automatically adjust the foreground color of the text to either
 * black or white depending on the lightness of the background color.
 *
 * @FieldFormatter(
 *   id = "field_example_color_background",
 *   label = @Translation("Change the background of the output text"),
 *   field_types = {
 *     "field_example_rgb"
 *   }
 * )
 */
class ColorBackgroundFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      // Set the value of the CSS color property depending on user provided
      // configuration. Individual configuration items can be accessed with
      // $this->getSetting('key') where 'key' is the same as the key in the
      // form array from settingsForm() and what's defined in the configuration
      // schema.
      $text_color = 'inherit';
      if ($this->getSetting('adjust_text_color')) {
        $text_color = $this->lightness($item->value) < 50 ? 'white' : 'black';
      }

      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('The content area color has been changed to @code', ['@code' => $item->value]),
        '#attributes' => [
          'style' => 'background-color: ' . $item->value . '; color: ' . $text_color,
        ],
      ];
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   *
   * Set the default values for the formatter's configuration.
   */
  public static function defaultSettings() {
    // The keys of this array should match the form element names in
    // settingsForm(), and the schema defined in
    // config/schema/field_example.schema.yml.
    return [
      'adjust_text_color' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   *
   * Define the Form API widgets a user should see when configuring the
   * formatter. These are displayed when a user clicks the gear icon in the row
   * for a formatter on the manage display page.
   *
   * The field_ui module takes care of handling submitted form values.
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    // Create a new array with one or more form elements. $form is available for
    // context, but you should not add your elements to it directly.
    $elements = [];

    // The keys of the array, 'adjust_text_color' in this case, should match
    // what is defined in ::defaultSettings(), and the field_example.schema.yml
    // schema. The values collected by the form will be automatically stored
    // as part of the field instance configuration, so you do not need to
    // implement form submission processing.
    $elements['adjust_text_color'] = [
      '#type' => 'checkbox',
      // The current configuration for this setting for the field instance can
      // be accessed via $this->getSetting().
      '#default_value' => $this->getSetting('adjust_text_color'),
      '#title' => $this->t('Adjust foreground text color'),
      '#description' => $this->t('Switch the foreground color between black and white depending on lightness of the background color.'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    // This optional summary text is displayed on the manage displayed in place
    // of the formatter configuration form when the form is closed. You'll
    // usually see it in the list of fields on the manage display page where
    // this formatter is used.
    $state = $this->getSetting('adjust_text_color') ? $this->t('yes') : $this->t('no');
    $summary[] = $this->t('Adjust text color: @state', ['@state' => $state]);
    return $summary;
  }

  /**
   * Determine lightness of a color.
   *
   * This might not be the best way to determine if the contrast between the
   * foreground and background colors is legible. But it'll work well enough for
   * this demonstration.
   *
   * Logic from https://stackoverflow.com/a/12228730/8616016.
   *
   * @param string $color
   *   A color in hex format, leading '#' is optional.
   *
   * @return float
   *   Percentage of lightness of the provided color.
   */
  protected function lightness(string $color) {
    $hex = ltrim($color, '#');
    // Convert the hex string to RGB.
    $r = hexdec($hex[0] . $hex[1]);
    $g = hexdec($hex[2] . $hex[3]);
    $b = hexdec($hex[4] . $hex[5]);

    // Calculate the HSL lightness value and return that as a percent.
    return ((max($r, $g, $b) + min($r, $g, $b)) / 510.0) * 100;
  }

}
