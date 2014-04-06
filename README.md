Dandelion v4.5.0
================

Dandelion is a web-based entry log application.

Requirements
------------

* Apache >= 2.4
* PHP >= 5.5
* MySQL/Maria DB >= 5.0
* PHP SQLite library (php5-sqlite) (if applicable)

Is it any good?
---------------

[Yes](https://news.ycombinator.com/item?id=3067434)

Install
-------

1. Decide whether you want to use MySQL/Maria or SQLite for the database
2. Create a MySQL/Maria database to house Dandelion if you are going that route
3. Browse to install.php in the root of Dandelion
4. Choose your database type, for MySQL/Maria type in database address, username, password, and database name
5. The installer will direct you to the index page. Login with:

   ```
   Username: admin
   Password: admin
   ```

6. Change the admin password, login again, and see your new empty log.

Notes for Install
-----------------

Dandelion needs write permissions to the config directory in order to install. If it doesn't have the proper permissions it will give you an error when you go to install.php. The permissions on the directory will be changed to read-only once Dandelion has done its thing.

Release Notes
-------------

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

- Fixed compatibility bug with IE on Windows 7 (Dandelion is compatibly with IE9+)
- Added category management
- Other bugs

v4.2.1

- Fixed bug where category filter wasn't using updated AJAX API
- Fixed bug where time and date window for Cheesto wasn't loading

Third-Party Libraries
---------------------

Dandelion utilizes the following third-party libraries:

[password-compat](https://github.com/ircmaxell/password_compat) - Provides PHP 5.3 compatibility with the new PHP 5.5 password functions. License can be found in ROOT/scripts/password_combat/LICENSE.md

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
