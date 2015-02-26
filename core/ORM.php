<?php

/*
 * Object-relational mapping implementation
 *
 * Copyright (C) 2015 Mathias Beke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


namespace DenBeke\ORM {
   
   //require __DIR__ . '/db.php';
    
    
    /**
     * NOTE: options are WIP
     */
    abstract class ORM {

        
        private static $get_by = "getBy";
        protected static $init = false;

        /**
         * Initialize ORM
         *
         * Pass a config of the following format to the function:
         *   $config = [
         *       'driver'    => 'mysql', // Db driver
         *       'host'      => 'localhost',
         *       'database'  => 'my_database',
         *       'username'  => 'root',
         *       'password'  => 'root',
         *   ];
         *
         * @param config
         */
        public static function init($config) {
            new \Pixie\Connection('mysql', $config, 'DenBeke\ORM\DB');
            static::$init = true;
        }

        
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
        
            static::requireInit();
        
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
            } else {
                $options = [];
            }
            
            
            // get result, add options and return
            $query = DB::table(static::getTable())->select('*')->where($field, '=', $value);
            $query = self::setOptions($query, $options);
            $result = $query->get();
            
            return static::unpackResult($result);
        
        }
        
        
        
        /**
         * Add options to Pixie query
         *
         * @param query object
         * @param options
         * @return query object
         */
        protected static function setOptions($query, $options) {
            if(isset($options['orderBy'])) {
                $options = $options + [Null, Null];
                $query = $query->orderBy($options['orderBy'][0], $options['orderBy'][1]);
            }
            
            return $query;
        }
        
        
        /**
         * Get all records from database
         *
         * @param (optional) options
         */
        public static function get($options = []) {
            
            static::requireInit();
            
            $query = DB::table(static::getTable())->select('*');
            $query = self::setOptions($query, $options);
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
            
            static::requireInit();
            
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
            
            static::requireInit();
            
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
            
            static::requireInit();
            
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
        
        
        static protected function requireInit() {
            if(!static::$init) {
                throw new \exception('\DenBeke\ORM\ORM not initialized.');
            }
        }
        
    }
    
   
}
