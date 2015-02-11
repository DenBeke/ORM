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

The ORM implements a default constructor which takes an associative array or an stdClass and assigns the values from the input to the fields of the new object.

```php
$person = new Person(['name' => 'Bob', 'city' => 'Amsterdam']);
```


### Get methods

Once the class inherits from `\DenBeke\ORM\ORM` you can access all the ORM methods. Starting with the get methods.

#### get()

The predefined `get()` method fetches all records of the given type from the database.  
If we have 3 records in the table `person` we can get all of them using the `get()` function:

```php
$persons = Person::get();
```

In this example `$persons` is an array of size 3, containing objects of type `Person`.

#### getBy*()

Whenever a class has a field, the caller can get records from the database by those fields. In this example the class Person has the fields `id`, `name`, `city`. So you can call the following functions:

* `Person::getById($id);`
* `Person::getByName($name);`
* `Person::getByCity($city);`

Those static functions will return an array of Person elements, where the input parameter matches the table column.


### add()

Adding records to the database is quite simple, just create an instance of the class and call the `add()` method on it.

```php
$person = new Person;
$person->name = 'Alice';
$person->city = 'Brussels';

$person->add();
```


### update()

Updating a record is as easy as adding records.  
Just alter a field and call the `update()` method.

```php
$person = Person::getByName('Bob')[0];
$person->city = 'Brussels';

$person->update();
```


### remove()

Deleting records can be done using the `remove()` method.

```php
$person = Person::getById(3)[0];

$person->remove();
```


Author
------

Mathias Beke - [denbeke.be](http://denbeke.be/)