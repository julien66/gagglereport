# Mailchimp

This module provides integration with the Mailchimp email delivery service.
While tools for sending email from your own server, like SimpleNews, are great,
they lack the sophistication and ease of use of dedicated email providers like
Mailchimp.

The core module provides basic configuration and API integration. Features and
site functionality are provided by a set of submodules that depend upon the core
"mailchimp" module. These are in the "modules" subdirectory: See their
respective READMEs for more details.

Below is a list of features that this module provides:

- API integration
- Support for an unlimited number of audiences (mailing lists)
- Have anonymous sign up forms to subscribe site visitors to any combination
  of Mailchimp audiences
- Mailchimp audience subscription via entity fields, allowing subscription rules
  to be governed by entity controls, permissions, and UI
- Allow users to subscribe during registration by adding a field to Users
- Map Entity field values to your Mailchimp merge fields
- Standalone subscribe and unsubscribe forms
- Subscriptions can be maintained via cron or in real time
- Subscription forms can be created as pages or as blocks, with one or more
  audience subscriptions on a single form
- Include merge fields & interest groups on anonymous subscription forms
- Create & send Mailchimp Campaigns from within Drupal, using Drupal
  entities as content.
- Display a history of Mailchimp email and subscription activity on a tab
  for any Entity with an email address.

For a full description of the module, visit the
[project page](https://www.drupal.org/project/mailchimp).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/mailchimp).


## Table of contents

- Requirements
- Recommended modules
- Installation
- Configuration
- Maintainers


## Requirements

- You need to have login access to a Mailchimp Account.
- You need to have at least one audience created in Mailchimp to use the
  mailchimp_lists module.


## Recommended modules

- mailchimp_signup: Create anonymous signup forms for your Mailchimp audiences,
  and display them as blocks or as standalone pages. Provide multiple-audience
  subscription from a single form, include merge variables as desired, and
  optionally include Interest Group selection.
- mailchimp_lists: Subscribe any entity with an email address to Mailchimp
  audiences by creating a mailchimp_list field, and allow anyone who can edit
  such an entity to subscribe, unsubscribe, and update member information.
  Also allows other entity fields to be synced to Mailchimp audience Merge
  Fields. Add a Mailchimp Subscription field to your User bundle to allow
  Users to control their own subscriptions & subscribe during registration.
- mailchimp_campaign: Create and send campaigns directly from Drupal, or
  just create them and use the Mailchimp UI to send them. Embed content from
  your Drupal site by dropping in any Entity with a title and a View Mode
  configured into any area of your email template.
- [Mandrill](http://drupal.org/project/mandrill):
  Mandrill is Mailchimp's transactional email service. The module provides the
  ability to send all site emails through Mandrill with reporting available
  from within Drupal. Please refer to the project page for more details.


## Installation

- Use composer to download mailchimp, which will download all the dependencies
  required by mailchimp: `composer require drupal/mailchimp`
- In case you have manually downloaded this module, then you will need to
  install the thinkshout/mailchimp-api-php library manually as well:
    - Use composer: `composer require thinkshout/mailchimp-api-php`
    - Or if you cannot use composer, another option is to install it using the
      Ludwig module.

Composer is the recommended way to install and maintain a site. Site
administrators using Ludwig need to be careful when combining modules that
depend on external libraries, since there are no safeguards against incompatible
library versions or overlapping requirements.

Steps:

1. Download and install the [Ludwig module](https://www.drupal.org/project/ludwig).
1. Download and install the Mailchimp module.
1. Visit packages status at Reports > Packages (admin/reports/packages) and
   use "Download and unpack all missing libraries" button.
1. Rebuild the cache. Done!

For more about using Ludwig:
[Ludwig Usage](https://www.drupal.org/docs/contributed-modules/ludwig/installation-and-usage)


## Configuration


### Using OAuth

1. From the Global Settings tab check the box for “Use OAuth Authentication."
1. Leave all other fields they are and click “Save Configuration.”
1. Visit the OAuth Settings Tab.
1. Enter a domain (or leave the default value).
1. Click "Authenticate."
1. This will trigger a new window (or tab) to open where you will be prompted to
   log in to Mailchimp and grant access to Mailchimp for Drupal.
1. Follow the prompts to do so and then you will be returned to the OAuth
   Settings tab. That page will automatically refresh and present a success
   message stating that you are connected to the Mailchimp API.
1. That's it! Now that you have granted access to Mailchimp for Drupal. You can
   configure your Mailchimp audiences, campaigns, forms, and pages.


### Using API Key (Deprecated)

1. Direct your browser to admin/config/services/mailchimp to configure the
   module.
1. You will need to put in your Mailchimp API key for your Mailchimp
   account.

   If you do not have a Mailchimp account, go to [http://www.mailchimp.com]
   (http://www.mailchimp.com) and sign up for a new account. Once you have set
   up your account and are logged in, visit 'Account Settings -> Extras -> API
   Keys' to generate a key.
1. Copy your newly created API key and go to the
   [Mailchimp config](http://example.com/admin/config/services/mailchimp) page
   in your Drupal site and paste it into the Mailchimp API Key field.
1. Batch limit - Maximum number of changes to process in a single cron run.
   Mailchimp suggests keeping this below 10000.


## Maintainers

- Ben Di Maggio - [bdimaggio](https://www.drupal.org/u/bdimaggio)
- Lev Tsypin - [levelos](https://www.drupal.org/u/levelos)
- Michael Shaver - [mshaver](https://www.drupal.org/u/mshaver)
- Tauno Hogue - [tauno](https://www.drupal.org/u/tauno)
- Andy Price - [aprice42](https://www.drupal.org/u/aprice42)
- Natalie Lysne - [xenophyle](https://www.drupal.org/u/xenophyle)
- Brendan Jercich - [brendanthinkshout](https://www.drupal.org/u/brendanthinkshout)
- Gabriel Carleton-Barnes - [gcb](https://www.drupal.org/u/gcb)
- Katie Escoto - [kescoto-thinkshout](https://www.drupal.org/u/kescoto-thinkshout)
- Maria Fisher - [mariacha1](https://www.drupal.org/u/mariacha1)
- Bob Potter - [maximumpotter](https://www.drupal.org/u/maximumpotter)
- Olivier Bouwman - [olivier.bouwman](https://www.drupal.org/u/olivierbouwman)
- Jaymz Rhime - [wxactly](https://www.drupal.org/u/wxactly)
