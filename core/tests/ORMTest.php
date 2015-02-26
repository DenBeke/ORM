<?php
/**
 * Tests for DenBeke ORM package
 */


require_once __DIR__ . '/config.php';


class Person extends \DenBeke\ORM\ORM {

    public $id;
    public $name;
    public $city;

}

class DenBekePHPUnit extends PHPUnit_Framework_TestCase {
    
    /**
     * Get a private or protected method for testing/documentation purposes.
     * How to use for MyClass->foo():
     *      $cls = new MyClass();
     *      $foo = PHPUnitUtil::getPrivateMethod($cls, 'foo');
     *      $foo->invoke($cls, $...);
     * @param object $obj The instantiated instance of your class
     * @param string $name The name of your private/protected method
     * @return ReflectionMethod The method you asked for
     */
    
    public static function getPrivateMethod($obj, $name) {
      $class = new ReflectionClass($obj);
      $method = $class->getMethod($name);
      $method->setAccessible(true);
      return $method;
    }

}



class ORMTest extends DenBekePHPUnit {
    
    
    public function setUp() {
        
        global $db_config;
        $db = new PDO($db_config['driver'] . ':host=' . $db_config['host'] . ';dbname=' . $db_config['database'] . ';charset=utf8', $db_config['username'], $db_config['password']);

        global $dump;
        $db->exec(file_get_contents($dump));

    }
    

    /**
     * Basic testing for the constructor and the assignments
     *
     * @test
     */
    public function testConstructor() {
        
        // Person created from associative array
        $person = new Person(['name' => 'Bob', 'city' => 'Amsterdam']);
        
        // Person created from stdClass
        $c = new stdClass;
        $c->name = 'Bob';
        $c->city = 'Amsterdam';
        $person2 = new Person($c);
        
        
        // Some trivial tests, just to have PHPUnit display more things :)
        $this->assertEquals($person->name, 'Bob');
        $this->assertEquals($person2->name, 'Bob');
        $this->assertEquals($person->city, 'Amsterdam');
        $this->assertEquals($person2->city, 'Amsterdam');
        
        $person->name = 'Alice';
        $this->assertEquals($person->name, 'Alice');
        $this->assertEquals($person->city, 'Amsterdam');
        
        $person->city = 'Brussels';
        $this->assertEquals($person->city, 'Brussels');
        
    }
    
    
    /**
     * Testing the \DenBeke\ORM\ORM::init() function
     *
     * @test
     */
    public function testInit() {
        global $db_config;
        
        \DenBeke\ORM\ORM::init($db_config);
        
    }
    
    
    /**
     * Test the \DenBeke\ORM\ORM::get() method which gets all the records
     *
     * @depends testInit
     */
    public function testGet() {
        $persons = Person::get();
        
        $this->assertEquals(sizeof($persons), 2);
        $this->assertEquals(get_class($persons[0]), 'Person');
    }
    

    /**
     * Test the \DenBeke\ORM\ORM::getBy*() method
     * which shoudl return one occurence for given column.
     *
     * @depends testInit
     */
    public function testGetBy() {
        
        $person = Person::getById(1);
        $this->assertEquals(sizeof($person), 1);
        $this->assertEquals($person[0]->name, 'Bob');
        
        $person = Person::getById(2);
        $this->assertEquals(sizeof($person), 1);
        $this->assertEquals($person[0]->name, 'Alice');
        
        $person = Person::getByName('Bob');
        $this->assertEquals(sizeof($person), 1);
        $this->assertEquals($person[0]->name, 'Bob');
        
        $person = Person::getByCity('Brussels');
        $this->assertEquals(sizeof($person), 1);
        $this->assertEquals($person[0]->name, 'Alice');

        
    }
    

    /**
     * Test the \DenBeke\ORM\ORM::add() method
     *
     * @depends testInit
     */
    public function testAdd() {
        
        $person = new Person(['name' => 'Mathias', 'city' => 'Antwerp']);
        $id = $person->add();
        
        $this->assertEquals($id, 3);
        
        $person = Person::getById(3);
        $this->assertEquals(sizeof($person), 1);
        $this->assertEquals($person[0]->name, 'Mathias');
        
    }
    
    /**
     * Test the \DenBeke\ORM\ORM::update() method
     *
     * @depends testInit
     */
    public function testUdate() {
                
        $person = Person::getById(1)[0];
        $person->name = 'John';
        
        $person->update();
        
        $person = Person::getById(1);
        $this->assertEquals($person[0]->name, 'John');
        $this->assertEquals($person[0]->city, 'Amsterdam');
        
    }
    
    
    /**
     * Test the \DenBeke\ORM\ORM::remove() method
     *
     * @depends testInit
     */
    public function testRemove() {
                
        $person = Person::getById(1)[0];
        
        $person->remove();
        
        $person = Person::getById(1);
        $this->assertEquals(sizeof($person), 0);
        
        $persons = Person::get();
        $this->assertEquals(sizeof($persons), 1);
        
    }
    

    /**
     * Test the \DenBeke\ORM\ORM::setOptions() method for 'orderBy' options
     *
     * @depends testInit
     */
    public function testOrderBy() {
        
        // order by DESC
        $options = [
            'orderBy' => ['name', 'DESC'],
        ];
        
        $persons = Person::get($options);
        $this->assertEquals('Bob', $persons[0]->name);
        
        
        // order by without option (= default = ASC)
        $options = [
            'orderBy' => ['name'],
        ];
        
        $persons = Person::get($options);
        $this->assertEquals('Alice', $persons[0]->name);
        
        
        // order by ASC
        $options = [
            'orderBy' => ['name', 'ASC'],
        ];
        
        $persons = Person::get($options);
        $this->assertEquals('Alice', $persons[0]->name);
        
        
        // add other records and check again
        $person = new Person(['name' => 'M', 'city' => 'Gent']);
        $id = $person->add();
        
        $options = [
            'orderBy' => ['name', 'DESC'],
        ];
        
        $persons = Person::get($options);
        $this->assertEquals('M', $persons[0]->name);
        
        $person = new Person(['name' => 'Z', 'city' => 'Antwerp']);
        $id = $person->add();
        
        $persons = Person::get($options);
        $this->assertEquals('Z', $persons[0]->name);
        
    }
    
    
    /**
     * Test the \DenBeke\ORM\ORM::setOptions() method for 'limit' option
     *
     * @depends testInit
     */
    public function testLimit() {
        
        $person = new Person(['name' => 'A', 'city' => 'B']);
        $id = $person->add();

        $person = new Person(['name' => 'C', 'city' => 'D']);
        $id = $person->add();

    
        $persons = Person::get(['limit' => 1]);
        $this->assertEquals(1, sizeof($persons));
        
        $persons = Person::get(['limit' => 4]);
        $this->assertEquals(4, sizeof($persons));
    
    }
    
    
    /**
     * Test the \DenBeke\ORM\ORM::setOptions() method for 'limit' and 'orderBy' options
     *
     * @depends testInit
     */
    public function testLimitAndOrderBy() {
        
        $person = new Person(['name' => 'C', 'city' => 'CC']);
        $id = $person->add();
        
        $person = new Person(['name' => 'D', 'city' => 'DD']);
        $id = $person->add();
        
        $options = [
            'orderBy' => ['name', 'ASC'],
            'limit' => 2,
        ];
        
        $persons = Person::get($options);
        $this->assertEquals('Alice', $persons[0]->name);
        $this->assertEquals('Bob', $persons[1]->name);
        $this->assertEquals(2, sizeof($persons));
        
        

        
    }
    
    
}
