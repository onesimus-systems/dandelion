Release Notes
=============

**v6.2.0**

- **PHP 7.0 is now the minimum version to run Dandelion**
- **Config location changed** - The configuration is now located in $approot/config instead of $approot/app/config. From the $approot simply run `mv app/config config`.
- New configuration syntax - Settings can now use the syntax `$config[$key] = $value;` to set configuration settings. The old syntax where config.php returned an array is still supported but is now deprecated and will be removed in a future release.

**v6.1.1**

- Fixed bug when changing the category of an existing log
- Fixed application crash when showing error page with no database
- Fixed MySQL schema file
- Added full Docker support

**v6.1.0**

- Ability to disable users instead of deleting them.
- Commenting on logs.
- Fixed display issues on the dashboard.
- Permalink for individual logs
- Creating and editing logs moved to separate page
- Centralized session manager
- Improved build script
- Created development Vagrant Configuration
- Improved internal permissions
- Validation of API request parameters
- Misc. fixes and improvements

**v6.0.3**

- Fixed: Non admin users couldn't edit logs

**v6.0.2**

- Fixed: Category rendering in IE
- Fixed (in 6.0.1): Theme rendering in IE
- Fixed: Session expiration
- Fixed: Updating application version causes infinite redirect

**v6.0.0**

- New: Interface update
    * Dandelion has been updated with a fresh, new look. But don't worry, if you don't like the new look, just switch to the Legacy theme.
- New: The category of a log can be edited
- New: Unified, simplified, and more powerful search!!!
    * Search now uses a simple syntax to search any part of a log
    * Sample syntax: ```title:"router 1" categories:"Configuration:Routers"```
    * [Documentation](http://blog.onesimussystems.com/dandelion/search)
- Improved: Completed and cleaned public API
    * All tasks can now be done through the API.
    * [Documentation](http://blog.onesimussystems.com/dandelion/api)
- Improved: Major source rewrite
    * This helps development in several ways:
        * Isolated public and application folders
        * Improved application structure for looser dependencies
        * Ability to easily implement different databases (Postgres and SQLite coming)
        * New routing functionality
        * Better templating
        * Cleaner bootstrap and application initialization
- Improved: Theme management system
    * Themes are handled a bit more elegantly and the structure have been simplified. Making creating themes much easier.
- Improved: Replaced TinyMCE with jHtmlArea
- Removed: The mail system has been removed because it didn't fit well with the purpose of Dandelion

**v5.0.3**

- Bug fix: No spellcheck in TinyMCE editor

**v5.0.1**

- Bug fix: User's name not showing on filtered logs

**v5.0.0**

- Rights management
- Internal email system
- Better look and feel when adding/editing logs
- Migration to full jQuery utilization
- Cheesto status is set without extra button click
- Internal and Public API (the public API is disabled by default)
    * Can be disabled/enabled at will
    * Per user API keys
    * Available APIs:
        - Read/Update Cheesto Status
        - Read log entries
        - Key management
        - Test API key
- Code refactoring
    * Consolidation of methods
    * Working on consistant formatting
    * Namespacing
    * Modularization

**v4.6.0**

- Better theme handling
    * Easier to create themes
    * Admins can set a default theme
- Cheesto can be disabled site-wide
- Internal APIs for stylesheet and JS loading
- PHP session cookies are identified with a unique prefix
- Tutorial no longer show for new users, it's still available in the menu
- Bug fixes
- Code refactoring

**v4.5.1**

- Added compatibility libraries for older PHP versions
    * password_combat for PHP 5.5 password_* functions

- Major bug fixes
    * JSON syntax error for category display
    * Incorrect boolean value for add logs
    * Old column name for presence table when adding user
    * Incorrect file names

**v4.5.0**

- Filter by category from each log entry
- Admin ability to backup database
- Database prefix so Dandelion doesn't conflict for other apps
- Bug fixes

**v4.4.0**

- Lots of under the hood changes
- Mainly bug fix and optimization

**v4.3.1**

- Fixed issue where Cheesto couldn't get a time and message from user

**v4.3.0**

- Fixed compatibility bug with IE on Windows 7 (Dandelion is compatible with IE9+)
- Added category management
- Other bugs

**v4.2.1**

- Fixed bug where category filter wasn't using updated AJAX API

**< v4.2.1**

- Dark times of not understanding the need of a change log and still getting used to git.
