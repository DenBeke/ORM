<?php

namespace DenBeke\ORM {


    $config = [
        'driver'    => 'mysql', // Db driver
        'host'      => 'localhost',
        'database'  => 'my_database',
        'username'  => 'root',
        'password'  => 'root',
    ];
    
    new \Pixie\Connection('mysql', $config, 'DenBeke\ORM\DB');


}