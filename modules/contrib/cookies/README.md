COOKiES Consent Management
-------

User consent management module for Drupal 8 and 9.

The module enables existing third-party-integration modules continued to be
used in compliance with the GDPR (of course without manipulating their code).
It solves the basic problem that when using third-party-integration modules,
the user must agree to the use of cookies (according to GDPR) before they are
installed.

IMPORTANT: No liability is assumed regarding compliance with the GDPR.

The COOKiES module provides (with the Library [Cookies JSR][cookiesjsr]) a
  fully configurable user interface for user decisions. It also supports out-
  of-the-box some key third-party integration modules (especially those
  included in the Thunder distribution):

 * [Google Analytics module][analytics]
 * [Google Tag Manager][gtag]
 * [Video embed, core:media][mvideoembed]
 * [Twitter media module][mtwitter]
 * [Instagram media module][minstagram]

Develop
-------

For developers, with these Modules there are some easy-to-understand sample
  modules available for integrating further third-party integration modules
  into user consent management. It contains code that controls the interfaces
  and best practices to implement practically any requirement quickly and
  easily.

Features
--------

1. Full responsive design.
2. Full translatable by Drupal UI
3. Styling is customizable.
   1. **Light:** Use CSS-vars to customize colors and some params
  [as described here][cssvar].
   2. **Heavy:** Disable original CSS in the config and start  from scratch -
  or [with original SCSS][sasssrc] download here.
4. Translatable documentation for all used services (Google Analytics, Adds,
   ...) as required by the GDPR created from config. Can be included as Page,
   Block or as Token ```[cookies:docs]```.

INSTALLATION
------------
* Just download and install COOKiES [as described here][install].

CONFIGURATION
-------------

 * Add the "COOKiES UI" block in the block configuration at
  Admin>Structure>Blocks (/admin/structure/block) place the block anywhere.
 * Activate additional modules under Admin>Modules (e.g. cookies_ga to support
  Google Analytics)
 * Configure the COOKiES module under Admin > Config > System > COOKiES
  (/admin/config/system/cookies/config)
 * Add a link to the (footer) menu with target "#editCookieSettings" so users
  can re-set their consents.


MAINTAINERS
-----------

Current maintainer:
 * [Joachim Feltkamp (JFeltkamp)][jfeltkamp]
 * [MONOKI][monoki]

[jfeltkamp]: https://www.drupal.org/u/jfeltkamp
[monoki]: https://www.drupal.org/monoki
[cookiesjsr]: https://github.com/jfeltkamp/cookiesjsr
[analytics]: https://www.drupal.org/project/google_analytics
[gtag]: https://www.drupal.org/project/google_tag
[mvideoembed]: https://www.drupal.org/docs/contributed-modules/video-embed-field
[mtwitter]: https://www.drupal.org/project/media_entity_twitter
[minstagram]: https://www.drupal.org/project/media_entity_instagram
[cssvar]: https://github.com/jfeltkamp/cookiesjsr/blob/master/README.md#styling
[sasssrc]: https://github.com/jfeltkamp/cookiesjsr/tree/master/styles
[install]: https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules
