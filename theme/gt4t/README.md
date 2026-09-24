theme_gt4t
==========

GT4T (GreenTech4Transformation) theme for Moodle 5.0: a Boost Union child theme.

It is based on the official [Boost Union Child](https://github.com/moodle-an-hochschulen/moodle-theme_boost_union_child)
boilerplate (commit 78855a5b), renamed as described in that project's README. Everything Boost Union offers
(flavours, smart menus, info banners, static pages, footer, login page settings) keeps working and is configured in
*Site administration → Appearance → Boost Union*.

What this theme adds
--------------------

* `scss/pre.scss`: the GT4T palette, typography and shapes as SCSS variables. These are added after Boost's and
  Boost Union's pre-SCSS, so they override the brand colours stored in the Boost / Boost Union settings.
* `scss/post.scss`: the few component details from the GT4T mockups that no variable covers.

The design reference (palette with contrast ratios, typography, mockups) lives in `docs/design/` at the repository
root.

Requirements
------------

* Moodle 5.0 (2025041400 or later)
* theme_boost_union v5.0-r26 (2025041466) or later

Not done yet
------------

* Font files: Inter and Source Sans 3 are referenced in the font stacks but not bundled yet, so browsers fall back
  to the system font until they are added under `fonts/`.
* The GT4T landing page and login page layouts still live in theme_moove and need to be ported here.
