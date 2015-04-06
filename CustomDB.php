<?php

class CustomDB {

	private $connection;
	private $preparedStatementHandleCache;

	public function __construct() {
		$this->connection = CustomDB::getDBH();
		$this->preparedStatementHandleCache = new Cache_PreparedStatement();
	}
	
	static function getDBH() {
		$config = new Configuration();

		$username = $config->username;
		$password = $config->password;
		$ip = $config->ip;
		$port = $config->port;
		$dbname = $config->dbname;

		$dsn = CustomDB::getDSN($ip, $dbname, $port);
		$connection = new PDO($dsn, $username, $password);

		// Errors should throw exceptions
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Set other attributes as needed: http://php.net/manual/en/pdo.constants.php
		return $connection;
	}

	static function getDSN($ip, $dbname, $port) {
		return "mysql:host=$ip;dbname=$dbname;port=$port";
	}

	public function query($sql) {
		$result = $this->connection->query($sql);

		// Fetch associative arrays (@TODO: Fetch hydrated objects)
		$result->setFetchMode(PDO::FETCH_ASSOC);

		return $result->fetchAll();
	}

	public function prepare($sql) {
		$prepStmtHandle = $this->connection->prepare($sql);
		$prepStmtHandle->setFetchMode(PDO::FETCH_ASSOC);
		$purgedHandles = $this->preparedStatementHandleCache->set($sql, $prepStmtHandle);
		// Call to StatsD for purges
		return $prepStmtHandle;
	}

	public function execute($sql, $params) {
		if (!$this->preparedStatementHandleCache->is_set($sql)) {
			// Call to StatsD for misses
			$this->prepare($sql);
		} else {
			// Call to StatsD for hits
		}
		$this->preparedStatementHandleCache->get($sql)->execute($params);
		return $this->preparedStatementHandleCache->get($sql)->fetchAll();

	}
		
}
