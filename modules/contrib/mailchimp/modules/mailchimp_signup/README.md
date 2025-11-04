Create pages and blocks for subscribing to Mailchimp Audiences anywhere on your
Drupal site.

## Installation

1. Enable the Mailchimp Signup module

2. If you haven't done so already, add an Audience in your Mailchimp account.
You can follow these directions provided by Mailchimp on how to
[add or import an audience](http://kb.mailchimp.com/article/how-do-i-create-and-import-my-list).

## Usage
To create a signup form:
* Go to admin/config/services/mailchimp/signup
* Click the "Add a signup form" button
* Complete the form
* You may need to clear caches to make your blocks and pages appear

Merge Fields
You will see Merge Field options based on the configuration of your audience
through Mailchimp. You can expose these merge fields to the end user to
complete. These fields are automatically set to "required" or "optional"
based on Mailchimp's merge field settings.
