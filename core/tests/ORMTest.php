<?php
/**
 * Tests for DenBeke ORM package
 */


class Person extends \DenBeke\ORM\ORM {

    public $id;
    public $name;
    public $city;

}


class ORMTest extends PHPUnit_Framework_TestCase {
    

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
    
}
