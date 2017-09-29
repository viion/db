<?php

// composer autoload
include_once __DIR__ . '/vendor/autoload.php';

$dbs = new \Viion\DB\Database();

$dbs->QueryBuilder
    ->select(['x','y','z'])
    ->from('my_table');


$dbs->QueryBuilder->show();