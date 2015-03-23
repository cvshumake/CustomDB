<?php

// @TODO Autoloader, docroot, etc
$here = dirname(__FILE__);
if (basename($here) != 'CustomDB') {
	chdir($here . '/..');
}
require_once('cli.php');


$dbHandle = CustomDB::getDBH();
$sql = 'SELECT ID, Name FROM city LIMIT 3';
$PdoStatement = $dbHandle->query($sql);
print_r($PdoStatement->fetchAll());

