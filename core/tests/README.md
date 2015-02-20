Unit Tests
==========

Before you can run any test, you must have a test database installed.
Just create a database `denbeke_orm_orm_test` with username 'root' and password 'root' 
(or any other credentials/names, but then you'll have to edit `core/tests/config.php`).

Of course you must also make sure to run `composer install` (in the root dir of the project) otherwise nothing will work...

Running the unit tests is very simple (if you have [PHPUnit](https://phpunit.de/manual/current/en/installation.html) installed):

    $ phpunit