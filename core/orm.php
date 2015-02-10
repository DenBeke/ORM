<?php

/*
 * Object-relational mapping implementation
 * 
 * Author: Mathias Beke
 * Date:   January 2015
 * 
 */


namespace DenBeke\ORM {
   
   require __DIR__ . '/db.php';
    
    
    /**
     * NOTE: options not yet implemented
     */
    abstract class ORM {

        
        private static $get_by = "getBy";

        
        /**
         * Constructor
         * 
         *   Leave emtpy for default constructor
         *   Otherwise use associative array, for each of the properties
         *   in the class you want to assign.
         * 
         *   e.g. if your class has the fields $name and $city
         *        new Person(['name' => 'Bob', 'city' => 'Amsterdam']);
         * 
         * @param fields
         */
        public function __construct($fields = Null) {
            if(!is_array($fields)) {
                $fields = (array)$fields;
                if(!isset($fields)) {
                    return;
                }
            }
            foreach ($fields as $field => $value) {
                $this->$field = $value;
            }
        }
        
        
        
        /**
         * Get all the records from database with given field matched
         *    function documented for actual use
         *
         *    e.g. Person::getById(1, $options);
         *         where Id is the field, 1 is the field value
         *
         * @pre field exists
         * @param field value
         * @param options
         */
        public static function __callStatic($name, $arguments) {
        
            // check if we know the given method
            if(strpos($name, self::$get_by) === false) {
                throw new \exception("Undefinied ORM method: $name");
            }
        
            $field = substr($name, strlen(self::$get_by));
            $field = strtolower($field);
        
            // check for the field.
            if( !property_exists(get_called_class(), $field)) {
                throw new \exception("Undefinied ORM field: $field"); 
            }
            
            // check for the value
            if( !isset($arguments[0]) ) {
                throw new \exception("Value not given");
            }
            
            $value = $arguments[0];
            
            
            // options...
            // TODO
            if( isset($arguments[1]) ) {
                $options = $arguments[1];
                
            }
            
            
            // get result and return
            $query = DB::table(static::getTable())->select('*')->where($field, '=', $value);
            $result = $query->get();
            
            return static::unpackResult($result);
        
        }
        
        
        /**
         * Get all records from database
         *
         * @param (optional) options
         */
        public static function get($options = Null) {
            $query = DB::table(static::getTable())->select('*');
            $result = $query->get();
            
            return static::unpackResult($result);
        }
        
        
        /**
         * Update this record in the database
         *
         * @pre id is set (and is a table field)
         * @pre record must already exist in database
         */
        public function update() {
            if( !isset($this->id)) {
                throw new \exception("update() only works when id is set");
            }
            $data  = $this->getFields();
            unset($data['id']);
            
            DB::table(static::getTable())->where('id', '=', $this->id)->update($data);
        }
        
        
        /**
         * Add this record to the database
         */
        public function add() {
            $table = static::getTable();
            $data  = $this->getFields();
 
            $this->id = DB::table($table)->insert($data);
            return $this->id;
        }
        
        
        /**
         * Remove this record from the database
         *
         * @pre id is set (and is a table field)
         */
        public function remove() {
            if( !isset($this->id)) {
                throw new \exception("remove() only works when id is set");
            }
            DB::table(static::getTable())->where('id', '=', $this->id)->delete();
        }
        
        
        
        /**
         * Returns associative array containing the fields and their value
         */ 
        private function getFields() {
            return get_object_vars($this);
        }
        
        
        /**
         * Get the table in which the records for this object are stored
         */
        static protected function getTable() {
            return strtolower( (new \ReflectionClass(get_called_class()))->getShortName() );
        }
        
        
        /**
         * Unpack the result(s)
         *
         *   When one result is given, an object is returend
         *   else an array of objects is returned
         */
        static protected function unpackResult($result) {
            $out = [];
            if(is_array($result)) {
                foreach ($result as $item) {
                    $out[] = new static($item);
                }
                return $out;
            }
            else {
                return new static($result);
            }
        }
        
    }
    
   
}