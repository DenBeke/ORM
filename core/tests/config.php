<?php


$dump = __DIR__ . '/denbeke_orm_orm_test.sql';

if (getenv('TRAVIS') == 'true') {
    $db_config = [
        'driver'    => 'mysql', // Db driver
        'host'      => '127.0.0.1',
        'database'  => 'denbeke_orm_orm_test',
        'username'  => 'root',
        'password'  => NULL,
    ];
}
else {
    $db_config = [
        'driver'    => 'mysql', // Db driver
        'host'      => '127.0.0.1',
        'database'  => 'denbeke_orm_orm_test',
        'username'  => 'root',
        'password'  => 'root',
    ];   
}
