# Dandelion

Dandelion is a web-based entry log application.

## Requirements

* Apache >= 2.4
* PHP >= 5.5
* MySQL/Maria DB >= 5.0

## Notes

This is version 3.1 and for the time being has the feature set in place. I'm currently working on cleaning up the code and adding database driven category management (instead of the horrendious (sp) categories.js script).

## Is it any good?

[Yes](https://news.ycombinator.com/item?id=3067434)

## Requirements

* Apache >= 2.4
* PHP >= 5.5
* MySQL/Maria DB >= 5.0

## Install

1. Create a MySQL/Maria database to house Dandelion
2. Browse to install.php in the root of Dandelion
3. Fill in the information, check the box, and let it go
4. The installer will direct you to the index page. Login with:

   ```
   Username: admin
   Password: admin
   ```

5. Change the admin password, login again, and see your new empty log.

## TODO

* Continue code clean-up

## Versioning

For transparency into the release cycle and in striving to maintain backward compatibility, Dandelion is maintained under the Semantic Versioning guidelines. Sometimes we screw up, but we'll adhere to these rules whenever possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

- Breaking backward compatibility **bumps the major** while resetting minor and patch
- New additions without breaking backward compatibility **bumps the minor** while resetting the patch
- Bug fixes and misc changes **bumps only the patch**

For more information on SemVer, please visit <http://semver.org/>.
