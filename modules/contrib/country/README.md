# Country

The Country module provides a country field for Drupal with two widgets: select
options and autocomplete. It's also possible to have checkboxes or radio
buttons.

For a full description of the module, visit the
[Country project page](https://www.drupal.org/project/country).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/country).

## Table of contents

- Requirements
- Installation
- Configuration
- Maintainers

## Requirements

This module requires no modules outside of Drupal core.

## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## Configuration

- You can add a country field to any entity.
Read [Add fields to content types](https://www.drupal.org/docs/user_guide/en/structure-fields.html)
to learn more about this.

- In the _Manage form display_ page
(**/admin/structure/types/manage/[your content type]/form-display**)
you can choose if the country field is an autocomplete, a select list, a radio
button, or a checkbox field.

- In the _Manage display_ page
(**/admin/structure/types/manage/[your content type]/display**)
you can choose if the country field value is shown as a country name (e.g.
"The Netherlands") or a country ISO code (e.g. "NL").

### Form API element

This module provides a [Form API](https://api.drupal.org/api/drupal/core%21core.api.php/group/form_api/) "country" element. You can add it as follows
to your custom forms.

    $form['country'] = [
      '#type' => 'country',
      '#title' => $this->t('My country'),
      '#required' => TRUE,
      '#multiple' => TRUE,
      '#empty_option' => 'Country',
      '#default_value' => ['BE', 'NL', 'LU']
    ];

### Facet settings

This module provides a Facets processor that shows the country name (e.g.
"British Virgin Islands") instead of the country ISO code (e.g. "VG") in a
facet block. In the facet settings page
(**/admin/config/search/facets/[your facet]/edit**) check "Country name" in
order to display names instead of country ISO codes in the facet block.

## Maintainers

- [dakala](https://www.drupal.org/u/dakala)
