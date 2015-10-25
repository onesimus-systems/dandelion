Dandelion v6.1.0
================

Dandelion is a web-based logbook designed to make it dead simple to keep logs. Dandelion helps you remember what you did four months ago. Dandelion developed out of the mindset of IT but is versatile enough to use in just about any situation.

Website and Docs: http://blog.onesimussystems.com/dandelion

Requirements
------------

* Apache or Nginx web server
    - mod_rewrite must be enabled for Apache
* PHP >= 5.4.0
* MySQL/Maria DB or SQLite PHP module

Dandelion has been tested on Ubuntu with Apache and Nginx. Other combos may probably work but YMMV.

Is it any good?
---------------

[Yes](https://news.ycombinator.com/item?id=3067434)

Installation Instructions
-------------------------

Installation docs are available on the website [here](http://onesimussystems.com/dandelion/install/).

Chrome Extension for Cheesto
----------------------------

I've also taken the time to develop a small Chrome extension that can interface with any Dandelion installation version 5 and above. The extension is available for install on the [Chrome Store](https://chrome.google.com/webstore/detail/cheesto-user-status/npggfenlbmepblpeenickeifmiionmli) and is free and released under the GPL v3 like Dandelion. The source is available on [GitHub](https://github.com/dragonrider23/Cheesto-Chrome).

Release Notes
-------------

v6.1.0

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

v6.0.0

- New look
    * Dandelion has been updated with a fresh, new look. But don't worry, if you don't like the new look, just switch to the Legacy theme.
- The category of a log can be edited
- Completed and cleaned public API
    * All tasks can now be done through the API.
    * [Documentation](http://onesimussystems.com/dandelion/api)
- Major source rewrite
    * Isolated public and application folders
    * Improved application structure for looser dependencies
    * Ability to interface with different databases (Postgres and SQLite coming)
    * New routing functionality
    * Better templating
    * Cleaner bootstrap and application initialization
- Unified, simplified, and more powerful search!!!
    * Search now uses a simple syntax to search any part of a log
    * Sample syntax: ```title:"router 1" categories:"Configuration:Routes"```
    * [Documentation](http://onesimussystems.com/dandelion/search)
- New theme management system
    * Themes are handled a bit more elegantly and the structure has been simplified. Making creating themes much easier.

[Full Release Notes](http://onesimussystems.com/dandelion/release-notes)

Contributing
------------

Thank you for considering contributing to Dandelion, please make sure to follow these guidelines:

* Use PSR-2 code style
* For items that are not specified in PSR-2, please conform to the surrounding code
* If you are wanting to contribute a large feature, please let me know before you start

I'm using [this style of git branching](http://nvie.com/posts/a-successful-git-branching-model/). For development, you should clone the Dandelion repository, and checkout a new feature branch off of the develop branch (not the master) and name it appropriately such as "issue-48-feature". Commit your changes to that branch and then send pull-requests back into the develop branch.

The exception to this rule is urgent hotfixes to master. To perform a hotfix, make a new branch off of master. When you're ready to submit the changes, make a pull request to both the master and develop branches.

The repository has a vagrant file that you can use for getting started quickly. It will expose 8081 as the web port. The box is automatically configured with MariaDB, NPM, Composer, and git. It will automatically run gulp and composer install. Apart from personal customizations, it should be ready to go out of the box.

Note on Database schema versions: The database schema for the current *stable* version is located in app/install/[dbtype]_schema.sql. Any additions being made in the current development version are recorded in an appropiate db_upgrade file. In order to have the most current schema, load the [dbtype]_schema.sql file then any db_upgrade file after the current stable. E.g. Stable is 6.0.0, dev is 6.1.0. [dbtype]_schema.sql contains the database as of 6.0.0. db_upgrade_6.1.0.sql contains the changes from the current stable (6.0.0) to the current dev (6.1.0). The upgrade script will be merged with the main script at release.

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

License - GPL v3
----------------

Dandelion - Web-based logbook.
Copyright (C) 2015  Lee Keitel, Onesimus Systems

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
