# Dandelion 3.5

Dandelion is a web-based entry log system almost like a journal.

## Is it any good?

[Yes](https://news.ycombinator.com/item?id=3067434)

## Requirements

* Apache >= 2.4
* PHP >= 5.5
* MySQL/Maria DB >= 5.0

## Install

1. Import Base_DB.sql into your MySQL/Maria database
2. Create a user and give it rw permissions to the new database
3. Edit scripts/dbconnect.php with the correct database parameters
4. Browse to index.php and login with:

   ```
   Username: admin
   Password: admin
   ```

5. Change the admin password, login again, and see your new empty log.

## TODO

* Make an install script
* Continue code clean-up from 3.1
