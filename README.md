# Dandelion 4.1.0 - Alpha 2

Dandelion is a web-based entry log system almost like a journal.

## Is it any good?

[Yes](https://news.ycombinator.com/item?id=3067434)

## Requirements

* Apache >= 2.4
* PHP >= 5.5
* MySQL/Maria DB >= 5.0

## Install

1. Create a MySQL/Maria database to house Dandelion
3. Browse to install.php in the root of Dandelion
4. Fill in the information, check the box, and let it go
5. The installer will direct you to the index page. Login with:

   ```
   Username: admin
   Password: admin
   ```

6. Change the admin password, login again, and see your new empty log.

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
