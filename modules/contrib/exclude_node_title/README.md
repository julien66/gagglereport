# Exclude Node Title

This module handles a very simple functionality, decide whatever to exclude a
node title from full node page or node teasers.

It provides a checkbox on node-edit pages for easier exclusion, or you can use
the admin page to manually enter a list of node id's to exclude title.

This module also provides the option to hide all titles of a certain Content
type. From the administrative interface you can select a content type to hide
title for.

## Contents of this file

- Requirements
- Installation
- Configuration
- Maintainers

## Requirements

This module requires no modules outside of Drupal core.

## Installation

Install the Exclude Node Title as you would normally install a contributed
Drupal module. Visit [Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules) for further information.


## Configuration

The settings form is located at /admin/config/content/exclude-node-title

From here there are a few options

1. Checkbox to hide title on all search pages (requires Search module)
2. Type of rendering type, how will the title be removed.
   1. Remove text from rendering
   2. Hide with CSS
3. Exclude title by content type
   1. Check each content type to have title hidden on option for
      1. Can default for all nodes (All nodes option)
      2. Can let the editor decide (User defined nodes option)
      3. Important! Make sure to select which view modules to apply to. 
         1. Example: If hiding on an embed view that will need to be selected.

## Maintainers

- Neslee Canil Pinto - [Neslee Canil Pinto](https://www.drupal.org/u/neslee-canil-pinto)
- Yonas Yanfa - [fizk](https://www.drupal.org/u/fizk)
- Gabriel Ungureanu - [gabrielu](https://www.drupal.org/u/gabrielu)
- Stephen Mustgrave - [smustgrave](https://www.drupal.org/u/smustgrave)
