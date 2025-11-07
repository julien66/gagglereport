# Examples for Developers

- **Project site**: https://www.drupal.org/project/examples
- **Code**: https://www.drupal.org/project/examples/git-instructions
- **Issues**: https://www.drupal.org/project/issues/examples


## What Is This?

This set of modules is intended to provide working examples of Drupal's
features and APIs. The modules strive to be simple, well documented and
modification friendly, in order to help developers quickly learn their inner
workings.

These examples are meant to teach you about code-level development for Drupal.
Some solutions might be better served using a contributed module, so that
you don't end up having to re-invent the wheel in PHP. When in doubt, look for
an existing contrib project that already does what you want, and contribute to
that project.


## How To Use The Examples

There are three main ways to interact with the examples in this project:

1. Enable the modules and use them within Drupal. Not all modules will have
   obvious things to see within your Drupal installation. For instance, while
   the Page and Form API examples will display forms, the Database API example
   does not have much that is visible within Drupal.
1. Read the code. Much effort has gone into making the example code readable,
   not only in terms of the code itself, but also the extensive inline comments
   and documentation blocks.
1. Browse the code and documentation on the web. There are two main places to
   do this:
    - https://api.drupal.org/api/examples is the main page for the Examples
      project. It has all manner of cross-linked references between the example
      code and the APIs being demonstrated. All the Doxygen-based comments in
      code are parsed and converted to HTML markup.
    - https://drupalcode.org/project/examples.git is the repository for the
      Examples project.

This project ships with a `composer.json` file. This is meant to illustrate how
to provide a `composer.json` file for a Drupal contrib project. You can read
more about how to use Composer with Drupal on
[Using Composer to Install Drupal and Manage Dependencies](https://www.drupal.org/docs/develop/using-composer/manage-dependencies).


## How To Install The Modules

1. The Examples project installs like any other Drupal module. There is
   extensive documentation on how to do this on
   [Installing Modules](https://www.drupal.org/docs/extending-drupal/installing-modules).
1. Within Drupal, enable any Example sub-module you wish to explore in Admin
   menu > Extend.
1. Rebuild access permissions if you are prompted to.
1. Profit! The links for Examples material will appear in your Tools menu. This
   menu appears on the left sidebar by default. You'll need to re-enable it if
   you removed it.

Having seen the behavior of the various example modules, you can move on to
reading the code, experimenting with it, and hopefully grasp how things work.

If you find a problem, incorrect comment, obsolete or improper code or such,
please search for an issue about it in the
[issue queue](https://www.drupal.org/project/issues/examples). If there isn't
already an issue for it, please create a new one.

Thanks.

Note: In this file, `1.`'s are used for the ordered list, which is a
"best practice" (see
[README.md template](https://www.drupal.org/docs/develop/managing-a-drupalorg-theme-module-or-distribution-project/documenting-your-project/readmemd-template))
