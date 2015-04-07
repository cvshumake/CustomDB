<?php

$here = dirname(__FILE__);
require_once($here . '/../cli.php');

// Note that this script does not use the CustomDB methods but, instead, the PDO methods. As a demo, it is more useful to use PDO directly.

// SQL Statement Code
$pdoHandle = CustomDB::getDBH();
$sql = 'SELECT ID, Name FROM city LIMIT 3';
$pdoStatement = $pdoHandle->query($sql);
//print_r($pdoStatement->fetchAll());

// Prep Stmt Code
$pdoHandle = CustomDB::getDBH();
$sql = 'SELECT ID, Name FROM city LIMIT 3';
$pdoStatement = $pdoHandle->prepare($sql);
$pdoStatement->execute();
//print_r($pdoStatement->fetchAll());

// SQL Statement Code w/ Parameter
$pdoHandle = CustomDB::getDBH();
$sql = 'SELECT ID, Name FROM city WHERE Name = ' . $pdoHandle->quote('Kabul') . ' LIMIT 3';
$pdoStatement = $pdoHandle->query($sql);
print_r($pdoStatement->fetchAll());

// Prep Stmt Code w/ Parameter
$pdoHandle = CustomDB::getDBH();
$sql = 'SELECT ID, Name FROM city WHERE Name = :name LIMIT 3';
$pdoStatement = $pdoHandle->prepare($sql);
$pdoStatement->execute(array('name' => 'Kabul'));
print_r($pdoStatement->fetchAll());

// SQL Statement Code w/ Injection
$pdoHandle = CustomDB::getDBH();
$_SERVER['urlParameter'] = "Kabul'; DROP DATABASE world;";
$name = $_SERVER['urlParameter'];
$sql = 'SELECT ID, Name FROM city WHERE Name = ' . $name . ' LIMIT 3';
//$pdoStatement = $pdoHandle->query($sql);
//print_r($pdoStatement->fetchAll());

// Prep Stmt Code w/ Injection
$pdoHandle = CustomDB::getDBH();
$_SERVER['urlParameter'] = "Kabul'; DROP DATABASE world;";
$name = $_SERVER['urlParameter'];
$sql = 'SELECT ID, Name FROM city WHERE Name = :name LIMIT 3';
$pdoStatement = $pdoHandle->prepare($sql);
$pdoStatement->execute(array('name' => $name));
print_r($pdoStatement->fetchAll());
