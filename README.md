Dandelion v5.0.1
================

Dandelion is a web-based entry log application.

Requirements
------------

* Apache web server
    - mod_rewrite must be enabled
* PHP >= 5.3.7
* MySQL/Maria DB >= 5.0

Is it any good?
---------------

[Yes](https://news.ycombinator.com/item?id=3067434)

Install - Automatic
-------------------

1. Grab a copy of the source either via git clone or download and put it on your web server
2. Create a MySQL/Maria database to house Dandelion
3. Browse to [hostname]/install in Dandelion
4. Type in database hostname, username, password, and database name
5. The installer will direct you to the index page. Login with:

   ```
   Username: admin
   Password: admin
   ```

6. Change the admin password, login again, and see your new empty log.

Install - Manually
------------------

Do you enjoy doing things by hand? That's ok! Just follow these instructions to install Dandelion:

1. Grab a copy of the source either via git clone or download and put it on your web server
2. Import the file ```base_MySQL_DB.sql``` under the install folder into your MySQL/Maria database. This can
be done either through the command line or through a utility such as PHPMyAdmin. Please refer to your
database documentation for specifics.
3. Copy the ```config.sample.php``` file under the config folder to ```config.php```. Keep it in the config folder!
Use your favorite editor (ie. Vim) and fill in the configuration options. Each option has a short description
explaining what it is.
4. Browse to your Dandelion install in a web browser (ie. Chrome) and login with:

   ```
   Username: admin
   Password: admin
   ```

5. Change the admin password, login again, and see your new empty log.

Notes for Install
-----------------

* Dandelion needs write permissions to the config directory in order to install.
If it doesn't have the proper permissions it will give you an error when you go to install.php.
The permissions on the directory will be changed to read-only once Dandelion has done its thing.
* Before you can create a log, you need to create some categories. Go to the Administration page
and click Manage Categories. Create a couple three categories as you see fit.

Other Stuff
-----------

With the new Cheesto API, I've also taken the time to develop a small Chrome extension that can be utilized
with any Dandelion v5+ installation. The extension can be install from the [Chrome Store](https://chrome.google.com/webstore/detail/cheesto-user-status/npggfenlbmepblpeenickeifmiionmli) and is free and released under the GPL v3 like Dandelion. The source is available on [GitHub](https://github.com/dragonrider23/Cheesto-Chrome).

Release Notes
-------------

v5.0.1

- Bug fix: User's name not showing on filtered logs

v5.0.0

- Rights management
- Internal email system
- Better look and feel when adding/editing logs
- Migration to fill jQuery utilization
- Cheesto status is set without extra button click
- Internal and Public API (the public API is disabled by default)
    * Can be disabled/enabled at will
    * Per user API keys
    * Available APIs:
        - Read/Update Cheesto Status
        - Read log entries
        - Key management
        - Test API key
    * Full documentation coming soon
- Code refactoring
    * Consolidation of methods
    * Working on consistant formatting
    * Namespacing
    * Modularization

v4.6.0

- Better theme handling
	- Easier to create themes
	- Admins can set a default theme
- Cheesto can be disabled site-wide
- Internal APIs for stylesheet and JS loading
- PHP session cookies are identified with a unique prefix
- Tutorial no longer show for new users, it's still available in the menu
- Bug fixes
- Code refactoring

v4.5.1

- Added compatibility libraries for older PHP versions
    * password_combat for PHP 5.5 password_* functions
	
- Major bug fixes
    * JSON syntax error for category display
    * Incorrect boolean value for add logs
    * Old column name for presence table when adding user
    * Incorrect file names

v4.5.0

- Filter by category from each log entry
- Admin ability to backup database
- Database prefix so Dandelion doesn't conflict for other apps
- Bug fixes

v4.4.0

- Lots of under the hood changes
- Mainly bug fix and optimization

v4.3.1

- Fixed issue where Cheesto couldn't get a time and message from user

v4.3.0

- Fixed compatibility bug with IE on Windows 7 (Dandelion is compatible with IE9+)
- Added category management
- Other bugs

v4.2.1

- Fixed bug where category filter wasn't using updated AJAX API

Third-Party Libraries
---------------------

Dandelion utilizes the following third-party libraries:

* [password-compat](https://github.com/ircmaxell/password_compat) - Provides PHP 5.3 compatibility with the new PHP 5.5 password functions. Released under the MIT license.
* jQuery and jQuery UI - Released under the MIT license. See jquery.org/license for more details.
* TinyMCE - Released under the Lesser GPL v2. See ROOT/tinymce/LICENSE.TXT for more details.

Versioning
----------

For transparency into the release cycle and in striving to maintain backward compatibility, Dandelion is maintained under the Semantic Versioning guidelines. Sometimes we screw up, but we'll adhere to these rules whenever possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

- Breaking backward compatibility **bumps the major** while resetting minor and patch
- New additions without breaking backward compatibility **bumps the minor** while resetting the patch
- Bug fixes and misc changes **bumps only the patch**

For more information on SemVer, please visit <http://semver.org/>.
