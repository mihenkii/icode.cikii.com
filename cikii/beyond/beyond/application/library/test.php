<?php

require 'vendor/autoload.php';
$manager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
$collection = new MongoDB\Collection($manager, "abc.post");

// var_dump($collection);
$result = $collection->insertOne( array( 'name' => 'Hinterland', 'brewery' => 'BrewDog' ) );

echo "Inserted with Object ID '{$result->getInsertedId()}'";
// $json = array("username"=>"one", "passwd"=>"12345");
// $collection->insertOne($json);

?>
