theme_diverse
=============

DIVERSE European University theme for Moodle 5.0: a Boost Union child theme.

It is based on the official [Boost Union Child](https://github.com/moodle-an-hochschulen/moodle-theme_boost_union_child)
boilerplate (commit 78855a5b), renamed as described in that project's README. Everything Boost Union offers
(flavours, smart menus, info banners, static pages, footer, login page settings) keeps working and is configured in
*Site administration → Appearance → Boost Union*.

What this theme adds
--------------------

* `scss/pre.scss`: the DIVERSE palette, typography and shapes as SCSS variables. These are added after Boost's and
  Boost Union's pre-SCSS, so they override the brand colours stored in the Boost / Boost Union settings.
* `scss/post.scss`: the self-hosted font declarations and the few component details from the DIVERSE mockups that no
  variable covers.
* `fonts/`: Inter 4.1 and Source Sans 3 (3.052R) as variable WOFF2 files, both under the SIL Open Font License 1.1
  (licence texts next to the files, listed in `thirdpartylibs.xml`). They are served by Moodle itself, not by a
  third-party CDN.

* `classes/output/core_renderer.php`: extends Boost Union's renderer (no layout or template of Boost Union is
  copied or overridden):
  * `main_content()` wraps the site home with the landing page sections for visitors (not logged in, or guest).
    Partners are the active tenants of tool_mutenancy that are listed on the login page; each card links to the
    tenant's own login page. Course, user and partner figures are read live from the database.
  * `render_login()` adds the orange brand panel next to the login form (large screens only).
* `templates/landing_top.mustache`, `templates/landing_bottom.mustache`, `templates/login_brand.mustache` and
  `scss/landing.scss`, `scss/login.scss`: markup and styles for the two pages. All texts are language strings in
  `lang/en/theme_diverse.php`, so they can be translated or changed under *Language customisation*.

The design reference (palette with contrast ratios, typography, mockups) lives in `docs/design/` at the repository
root.

Requirements
------------

* Moodle 5.0 (2025041400 or later)
* theme_boost_union v5.0-r26 (2025041466) or later

Not done yet
------------

* The official DIVERSE logo: the landing page and login panel show a text wordmark until the logo file is added.
* Translations of the landing and login texts (Turkish, German, Croatian).
