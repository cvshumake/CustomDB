<?php

$here = dirname(__FILE__);
require_once($here . '/../cli.php');

/*
// Prep Stmt Code w/ Parameter
$pdoHandle = CustomDB::getDBH();
$sql = 'SELECT ID, Name FROM city WHERE Name = :name LIMIT 3';
$pdoStatement = $pdoHandle->prepare($sql);
$pdoStatement->execute(array('name' => 'Kabul'));
print_r($pdoStatement->fetchAll());

// Slide 36: The above block becomes this with caching of handles:
$pdoHandle = CustomDB::getDBH();
$preparedStatementHandleCache = new Cache_PreparedStatement();
$sql = 'SELECT ID, Name FROM city WHERE Name = :name LIMIT 3';
if (!$preparedStatementHandleCache->is_set($sql)) {
	$preparedStatementHandleCache->set($sql, $pdoHandle->prepare($sql));
}
$pdoStatement = $preparedStatementHandleCache->get($sql);
$pdoStatement->execute(array('name' => 'Kabul'));
print_r($pdoStatement->fetchAll());
*/

// (Bad) Error Handling
$preparedStatementHandleCache = new Cache_PreparedStatement();
$sql = 'SELECT ID, Name FROM city WHERE Name = :name LIMIT 3';
do {
	try {
		$tries = 0;
		$pdoHandle = CustomDB::getDBH();
	} catch (Exception $e) {
		// Example unobfuscated error code handling
		if (in_array($e->getCode(), array(2002))) {
			$tries++;
			usleep(rand($config->DB_USLEEP_MIN, $config->DB_USLEEP_MAX));
		} else {
			throw $e;
		}
	}
} while (!$pdoHandle && $tries < $config->DB_MAX_TRIES);
if (!$pdoHandle) {
	throw new Exception('Failed to generate database handle.');
}

if (!$preparedStatementHandleCache->is_set($sql)) {
	$loops = 0;
	while (true) {
		$loops++;
		if ($loops > 4) {
			throw Exception('Infinite loop.');
		}
		try {
			$preparedStatementHandleCache->set($sql, $pdoHandle->prepare($sql));
			break;
		} catch (Exception $e) {
			if ($loops > 3) {
				throw $e;
			}
			if ($loops > 2) {
				$pdoHandle->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
				continue;
			}
			// Example unobfuscated error code handling
			if (in_array($e->getCode(), array(1461))) {
				$preparedStatementHandleCache->purge();
				continue;
			}
		}
	}
}
$pdoStatement = $preparedStatementHandleCache->get($sql);
$pdoStatement->execute(array('name' => 'Kabul'));
print_r($pdoStatement->fetchAll());

// A good DB Wrapper
$city = CustomDB::find('City', array(CustomDB_Model::FIELD_ID => 4));
$city = DB_Model_City::findById(4);
