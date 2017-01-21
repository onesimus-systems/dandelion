# Contributing

Dandelion is designed to be a useful asset to any organization by providing an easy way to document just about anything. It was conceived from the viewpoint of IT and the need to keep track of changes to network infrastructure. My hope is that Dandelion evolves into an application that can be utilized in any scenario.

## Coding Style

Thank you for considering to contribute to Dandelion, please make sure to follow these guidelines:

* Use PSR-2 code style
* For items that are not specified in PSR-2, please conform to the surrounding code
* If you are wanting to contribute a large feature, please let me know before you start

I'm using [this style of git branching](http://nvie.com/posts/a-successful-git-branching-model/). For development, you should clone the Dandelion repository, and checkout a new feature branch off of the develop branch (not the master) and name it appropriately such as "issue-48-feature". Commit your changes to that branch and then send pull-requests back into the develop branch.

The exception to this rule is urgent hotfixes to master. To perform a hotfix, make a new branch off of master. When you're ready to submit the changes, make a pull request back to the master branch. Once accepted, the changes will also be pulled into the develop branch.

## Tools

Dandelion requires the following software to build and run:

- NPM
- MySQL or equivalent
- PHP 5.6+
- Composer

Here's how to build Dandelion from scratch:

1. Clone the repo
2. Run `npm install && ./node_modules/bin/gulp` to build the JavaScript and CSS files
3. Run `composer install` to download PHP dependencies

## Using Vagrant

The repository contains a vagrant file that you can use for getting started quickly. It will expose 8081 as the web port. The box is automatically configured with MariaDB, NPM, Composer, and git. It will automatically run gulp and composer install. Apart from personal customizations, it should be ready to go out of the box.

## Using Docker

The repository contains a Docker Compose file that will start three Docker containers: dandy-web (nginx:alpine), dandy-app (bitnami/php-fpm:5.6.18-0), and dandy-db (mariadb). These are the three components for Dandelion to function correctly. Ideal you should never need to mess with the web or database containers. All code is available in the app container. You may use the app container to run composer although you will need to install it yourself. To start the containers, simply run `docker-compose up -d`. Like Vagrant, the web interface is exposed on port 8081.

For the NPM tools, you can use the `mkenney/npm:alpine` image like so: `docker run --rm -i -v $(pwd):/src:rw mkenney/npm:alpine /run-as-user /usr/local/bin/gulp`. Or to run npm install: `docker run --rm -i -v $(pwd):/src:rw mkenney/npm:alpine /run-as-user /usr/local/bin/npm install`.

## Database Schema

Note on Database schema versions: The database schema for the current *stable* version is located in `app/install/[dbtype]_schema.sql`. Any additions being made in the current development version are recorded in an appropiate db_upgrade file. In order to have the most current schema, load the `[dbtype]_schema.sql` file then any db_upgrade file after the current stable. E.g. Stable is 6.0.0, dev is 6.1.0. `[dbtype]_schema.sql` contains the database as of 6.0.0. db_upgrade_6.1.0.sql contains the changes from the current stable (6.0.0) to the current dev (6.1.0). The upgrade script will be merged with the main script at release.
