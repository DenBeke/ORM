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


class ORMTest extends PHPUnit_Framework_TestCase {
    
    
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
     * @depends testInit
     */
    public function testGet() {
        $persons = Person::get();
        
        $this->assertEquals(sizeof($persons), 2);
        $this->assertEquals(get_class($persons[0]), 'Person');
    }
    
}
