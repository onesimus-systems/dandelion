Dandelion v6.0.0
================

Dandelion is a web-based journal design to make it dead simple to keep logs. Dandelion helps you remember what you did four months ago. Dandelion was developed out of the mindset of IT. However, it is versatile enough to be used in just about any situation.

Requirements
------------

* Apache or Nginx web server
    - mod_rewrite must be enabled for Apache
* PHP >= 5.4.0
* MySQL/Maria DB

Dandelion has only been tested on Ubuntu with Apache and Nginx. Other combos may probably work but YMMV.

Is it any good?
---------------

[Yes](https://news.ycombinator.com/item?id=3067434)

Install - Auto
--------------

Installing Dandelion is an easy and simple task. Just follow these steps and you'll be up and running in no time.

1. Grab a copy of Dandelion of Github.
2. Run ```composer install --no-dev``` from the root Dandelion directory. If you don't have Composer installed please see the [Getting Started](https://getcomposer.org/doc/00-intro.md#locally) guide. This will install PHP dependencies.
3. Run ```npm install``` and ```gulp```. This will compile the javascript and stylesheets.
4. Create a database in MySQL/MariaDB to house Dandelion.
5. Setup your web server to serve the ```public``` directory of Dandelion.
6. Browse to ```http://[dandelion server]/install.php```.
7. Fill out the information, astericks mark required fields.
8. Click Finish Install
9. If it was successful, you'll be redirected to the login page of Dandelion. Login with:

   ```
   Username: admin
   Password: admin
   ```

    and change the password when prompted.
10. Congratulations! You've now installed Dandelion. Go and make your first log.

Sample Auto Install Commands (Ubuntu, Nginx)
--------------------------------------------

```bash
$ mkdir -p /var/www
$ cd /var/www
$ git clone https://github.com/dragonrider23/dandelion
$ cd dandelion
$ composer install --no-dev
$ npm install && gulp
$ cp app/install/Nginx-sample-config.conf /etc/nginx/sites-available/default
$ service nginx restart
```

Now browse to ```http://[dandelion hostname]/install.php``` to finish the installation.

Install - Manual
----------------

Sometimes we just want to do things ourselves. And that's fine! Here's the verbose way of installing Dandelion without using our install page.

1. Grab a copy of Dandelion off GitHub. Either via a source download from the web UI or from the git command.
2. Import the file ```base_MySQL_DB.sql``` under the app/install directory. Dandelion currently only supports MySQL/MariaDB. Supporting other databases is in the works.
3. Copy ```config.sample.php``` under app/config to ```config.php``` under the same folder. Use your favorite text editor (ie. Vim) and edit the configuration to fit your environment. The comments in the file explain what each setting is.
4. Run ```composer install --no-dev``` from the root Dandelion directory. If you don't have Composer installed please see the [Getting Started](https://getcomposer.org/doc/00-intro.md) guide.
5. Run ```npm install``` and ```gulp```. This will compile the javascript and stylesheets.
6. Setup your web server to use the public directory under Dandelion as its root. Under the app/install directory is a sample configuration for Nginx and Apache2. For apache, you will need to enable mod_rewrite and install the apache2 PHP5 module. For Nginx, you will need to install the php-fpm package.
7. Browse to your Dandelion install in a web browser and login with:

   ```
   Username: admin
   Password: admin
   ```

8. Dandelion will prompt you to change the password, change it to something you'll remember then relogin.
9. Congratulations! You've now installed Dandelion. Go and make your first log.

Sample Install Commands (Ubuntu, Nginx)
---------------------------------------

```bash
$ mkdir -p /var/www
$ cd /var/www
$ git clone https://github.com/dragonrider23/dandelion
$ cd dandelion
$ mysql -u [username] -p
mysql> CREATE DATABASE [some name];
mysql> exit;
$ mysql -u [username] -p [some name] < app/install/base_MySQL_DB.sql
$ cp app/config/config.sample.php app/config/config.php
$ vim app/config/config.php
$ composer install --no-dev
$ npm install && gulp
$ cp app/install/Nginx-sample-config.conf /etc/nginx/sites-available/default
$ service nginx restart
```

Chrome Extension for Cheesto
----------------------------

I've also taken the time to develop a small Chrome extension that can be utilized with any Dandelion v5+ installation. The extension can be install from the [Chrome Store](https://chrome.google.com/webstore/detail/cheesto-user-status/npggfenlbmepblpeenickeifmiionmli) and is free and released under the GPL v3 like Dandelion. The source is available on [GitHub](https://github.com/dragonrider23/Cheesto-Chrome).

Release Notes
-------------

v6.0.0

- The category of a log can be edited
- The public API is completed. All tasks can now be done through the API. Documentation to come.
- Major source rewrite
    * Isolated public and application folders
    * Improved application structure for looser dependencies
    * Ability to easily implement different databases (Postgres and SQLite coming)
    * New routing functionality
    * Better templating
    * Cleaner bootstrap and application initialization

v5.0.2

- Bug fix: No spellcheck in TinyMCE editor

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

Contributing
------------

Thank you for considering contributing to Dandelion, please make sure to follow these guidelines:

* Use PSR-2 code style
* For items that are not specified in PSR-2, please conform to the surrounding code
* If you are wanting to contribute a significantly sized feature, please let me know before you start

I'm using [this style of git branching](http://nvie.com/posts/a-successful-git-branching-model/). So for development, you should clone the Dandelion repository here on Github, and checkout a new feature branch off from the develop branch (rather than master) such as issue-48-feature. Commit your changes to that branch and then send pull-requests back into the develop branch.

The exception to this rule is urgent hotfixes to master. To perform a hotfix, make a new branch off of master. When you're ready to submit the changes, make a pull request to both the master and develop branches.

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

Dandelion - Web-based entry log journal.
Copyright (C) 2014  Lee Keitel, Onesimus Systems

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
