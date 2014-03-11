Dandelion v4.2.1
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
4. Choose your database type, for MySQL/Maria type in database address, username, password, and table name
5. The installer will direct you to the index page. Login with:

   ```
   Username: admin
   Password: admin
   ```

6. Change the admin password, login again, and see your new empty log.

Notes for Install
-----------------

Dandelion needs write permissions to the config directory in order to install. If it doesn't have the proper permissions it will give you an error when you go to install.php. The permissions on the directory will be changed to read-only once Dandelion has done its thing.

New Features
------------

* Themes! - You can now develop and use your own themes. Use the three provided as templates for now. I'm going to write up proper documentation for how to develop and use themes. All themes are in their own directory under the themes directory.

* Improved layout
    * The control panel has been rearranged to look better.
    * The refresh timer has been removed (the page still refreshes, it just doesn't count down any more).
    * The User Management page has been improved as well.
    * The add/edit entry textbox has been enlarged.
    
Release Notes
-------------

v4.2.1 - Fixed bug where category filter wasn't using updated AJAX API
	   - Fixed bug where time and date window for Cheesto wasn't loading

Known Things That Don't Work
----------------------------

* Category Management still doesn't work. You will only ever see it if you're an admin because there's a link on the admin portal. But it will only show a blank select box. Don't worry, more will come soon.

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
