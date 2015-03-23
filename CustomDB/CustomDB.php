<?php

class CustomDB implements dbWrapper {

	private $connection;
	// @TODO proper Prepared Statement Handle Cache container
	private $preparedStatementHandleCache = array();

	public function __construct() {
		$this->connection = CustomDB::getDBH();
	}
	
	static function getDbh() {
		$config = new Configuration();

		$username = $config->username;
		$password = $config->password;
		$ip = $config->ip;
		$port = $config->port;
		$dbname = $config->dbname;

		$dsn = CustomDB::getDsn($ip, $dbname, $port);
		$connection = new PDO($dsn, $username, $password);

		// Errors should throw exceptions
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Set other attributes as needed: http://php.net/manual/en/pdo.constants.php
		return $connection;
	}

	static function getDsn($ip, $dbname, $port) {
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
		$this->preparedStatementHandleCache[$sql] = $prepStmtHandle;
	}

	public function execute($sql, $params) {
		if (!isset($this->preparedStatementHandleCache[$sql])) {
			$this->prepare($sql);
		}
		$this->preparedStatementHandleCache[$sql]->execute($params);
		return $this->preparedStatementHandleCache[$sql]->fetchAll();

	}
		
}
