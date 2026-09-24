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

The design reference (palette with contrast ratios, typography, mockups) lives in `docs/design/` at the repository
root.

Requirements
------------

* Moodle 5.0 (2025041400 or later)
* theme_boost_union v5.0-r26 (2025041466) or later

Not done yet
------------

* The DIVERSE landing page and login page layouts still live in theme_moove (as GT4T) and need to be ported here.
