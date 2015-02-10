ORM
===

*Simple ORM implementation for PHP*

When working with SQL databases, there is always a same monkey work that has to be done,
like selecting using the table parameters, putting the result back in to a class, ...

ORM (object-relational mapping) solves this problem, you can just call some methods on classes,
using the properties defined in your classes.

This implementation is naive and very simple, but can spare you a lot of time and work.


Install
-------

Since writing queries is something I don't always enjoy, this ORM packages depends on a query builder: [Pixie](https://github.com/usmanhalalit/pixie) (by Muhammad Usman).

Dependencies are installed through [Composer](https://getcomposer.org):

    $ composer install


You can of course also install this package using Composer:  
Add `"denbeke/orm": "dev-master"` to your requirements and add the following code to the root
of the `composer.json` file to add the Github repo:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/DenBeke/ORM.git"
    }
],
```

Usage
-----

*Before you can do anything you must edit the config in the `db.php` file
(this is the configuration of Pixie, the query builder).*


### ORM class

Creating an "ORM-ready" class is very simple, just inherit from `\DenBeke\ORM\ORM`.

```php
class Person extends \DenBeke\ORM\ORM {
    
    public $id;
    public $name;
    public $city;
    
}
```

After writing the code, you must also add a table `person` to the database, with the fields `id`, `name`, `city`.  
As you may have noticed, the table name is derived from the Class name (without namespaces) and the column names are just the names of the PHP fields.

### Get methods

Once the class inherits from `\DenBeke\ORM\ORM` you can access all the ORM methods. Starting with the get methods.

#### get()


#### getBy*()


### add()


### update()


### remove()
