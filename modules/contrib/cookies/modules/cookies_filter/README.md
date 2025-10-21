COOKiES Filter
-----------------

## Introduction

The COOKiES Filter module supplies a "COOKiES Filter" text format filter and
"COOKiES filter service" entities, which provide ways to block
"script", "embed", "iframe", "img" and "object" html elements, using a
"COOKiES service" of your choice.

There are three blocking modes:

- None (Keep as is): Only disables the blocked element.
- Hide: Disables and Hides the blocked element.
- Overlay: Disables and presents a cookies overlay over the blocked element.

The module also supports blocking html elements, based on given css selectors.

Additionally you can specify custom css selectors to select an html element,
which should have the overlay applied to, instead of the blocked elements.

## Install

After you have installed the COOKiES Filter module, go to
"/admin/config/system/cookies/cookies-service-filter" to create a "COOKiES
service filter" entity. Optionally you can create a "COOKiES service" first,
which you want to use the filter for. After creating the "COOKiES service
filter" entity, go to "/admin/config/content/formats" and apply the
"COOKiES Filter: 2-Click Consent for page elements" text format filter to your
desired text format.
Now all the html elements, specified in your "COOKiES service filter" entities,
will get blocked in the defined text format.

## Requirements

### The following modules are required.

- [COOKiES](https://www.drupal.org/project/cookies)

### The following libraries are required:

- symfony/dom-crawler
- symfony/css-selector

#### There are several ways to download the needed third-party libraries.

#### Recommended:

Use the [Composer Merge plugin](https://github.com/wikimedia/composer-merge-plugin)
to include the Cookies Filter module\'s [composer.libraries.json](https://cgit.drupalcode.org/cookies/tree/modules/cookies_filter/composer.libraries.json)

#### Optional:

Execute composer commands:
- composer require symfony/dom-crawler
- composer require symfony/css-selector
