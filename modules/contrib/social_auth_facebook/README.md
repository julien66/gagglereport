CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Installation
* Configuration
* How it works
* Support requests
* Maintainers


INTRODUCTION
------------

Social Auth Facebook is a Facebook authentication integration for
Drupal. It is based on the Social Auth and Social API projects

It adds to the site:

* A new url: `/user/login/facebook`.

* A settings form at `/admin/config/social-api/social-auth/facebook`.

* A Facebook logo in the Social Auth Login block.


REQUIREMENTS
------------

This module requires the following modules:

* [Social Auth](https://drupal.org/project/social_auth)
* [Social API](https://drupal.org/project/social_api)


INSTALLATION
------------

Install as you would normally install a contributed Drupal module. See
[Installing Modules](https://www.drupal.org/docs/extending-drupal/installing-modules)
for more details.


CONFIGURATION
-------------

In Drupal:

1. Log in as an admin.

2. Navigate to Configuration » User authentication » Facebook and copy
   the Authorized redirect URL field value (the URL should end in
   `/user/login/facebook/callback`).

In [Meta for Developers](https://developers.facebook.com/):

3. Log in to a Facebook account.

4. Navigate to [My Apps](https://developers.facebook.com/apps/).

5. Click [Create app](https://developers.facebook.com/apps/create/).

6. Enter a Display name for the app and click Create app.

7. From the app landing page under Add products to your app > Facebook Login
   click Set up.

8. Under Products > Facebook Login click Settings (do not use "Quickstart").

9. Paste the URL copied from Step 2 in the Valid OAuth Redirect URIs field.

10. Navigate to Settings > Basic.

11. Copy and save the App ID value somewhere safe.

12. Click Show for the App secret field. Copy and App secret value and save it
    somewhere safe.

13. Navigate to Settings > Advanced.

14. Under Upgrade API version note the API version in use (e.g., "14.0").

In Drupal:

15. Return to Configuration » User authentication » Facebook

16. Enter the Facebook app ID in the App ID field.

17. Enter the Facebook app secret in the App secret field.

18. Enter the API version in the Facebook Graph API version field.

19. Click Save configuration.

20. Navigate to Structure » Block Layout and place a Social Auth login block
    somewhere on the site (if not already placed).

That's it! Test the connection by logging in with your own account. For further
testing navigate to Roles > Test Users to create and add other testers.

When ready log in to Meta for Developers, navigate to OAuth app, and change
the App Mode to "Live" to allow any user with a Facebook account to log in.


HOW IT WORKS
------------

The user can click on the Facebook logo on the Social Auth Login block
You can also add a button or link anywhere on the site that points
to `/user/login/facebook`, so theming and customizing the button or link
is very flexible.

After Facebook has returned the user to your site, the module compares the user
ID or email address provided by Facebook. If the user has previously registered
using Facebook or your site already has an account with the same email address,
the user is logged in. If not, a new user account is created. Also, a Facebook
account can be associated with an authenticated user.


SUPPORT REQUESTS
----------------

* Before posting a support request, carefully read the installation
  instructions provided in module documentation page.

* Before posting a support request, check the Recent Log entries at
  admin/reports/dblog

* Once you have done this, you can post a support request at module issue
  queue: [https://www.drupal.org/project/issues/social_auth_facebook](https://www.drupal.org/project/issues/social_auth_facebook)

* When posting a support request, please inform if you were able to see any
  errors in the Recent Log entries.


MAINTAINERS
-----------

Current maintainers:

* [Christopher C. Wells (wells)](https://www.drupal.org/u/wells)

Development sponsored by:

* [Cascade Public Media](https://www.drupal.org/cascade-public-media)
