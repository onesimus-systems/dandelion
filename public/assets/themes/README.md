Themes for Dandelion
====================

Themes in Dandelion are very simple. They are nothing more than CSS files with special names. All the packaged themes use the LESS CSS preprocessor. This allows for quick and easy improvements and testing. You may also utilize LESS or SASS or any other preprocessor or you can use plain old CSS. Below is an explanation of required theme files and structure.

All themes are in their own folder under the 'themes' folder of the Dandelion root. The name of the folder is the name of the theme.

File structure
--------------

ROOT/themes/Your Theme/

* cheesto.css - Styles for the mini version of the Cheesto user status module
* main.css - The main stylesheet for Dandelion. This file is used on almost every page and dictates background color, button colors, etc.
* presenceWin.css - Styles for the windowed version of the Cheesto module
* tutorial.css - Styles for the tutorial page

Notes
-----

When making a themes remember:

* The four files above are required for Dandelion to look correct. There are fallback styles that are colorless but keep the overall structure.
* Be mindful of filename case. Paths are CaSe SenSiTive. Meaning, presenceWin.css and presencewin.css are NOT the same.
* Feel free to mess around with the fallback styles and make a theme that does more than change colors. The packaged themes are more of a starting point and a way to quickly give it a new look.