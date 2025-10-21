# COOKiES Asset Injector
## Introduction
The "COOKiES Asset Injector" module implements a way to block javascript code
injected by the "Asset Injector" module using COOKiES Services and its consent
configuration.

## Requirements

This module requires the COOKiES and Asset Injector modules:
- COOKiES (https://www.drupal.org/project/cookies)
- Asset Injector (https://www.drupal.org/project/asset_injector)

## Installation

- Install as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/1897420 for further information.

## Usage
- Go to the JS Asset Injector creation page
("/admin/config/development/asset-injector/js/add") or edit an existing
js asset injector entity
("/admin/config/development/asset-injector/js/my_js_injector")
- Scroll down and you will see the "COOKiES Integration" setting
- Here you can set the COOKiES service this asset entity belongs to.

**ATTENTION**
- If you specify a COOKiES service, where consent is **NOT** required, the
asset will always execute, as user consent is assumed.
- Also note, that the asset injector "preprocess" option is **NOT** compatible
with the COOKiES integration.
