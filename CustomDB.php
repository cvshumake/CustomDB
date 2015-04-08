<?php

class CustomDB {

	private $connection;
	private $preparedStatementHandleCache;

	public function __construct() {
		$this->connection = CustomDB::getDBH();
		$this->preparedStatementHandleCache = new Cache_PreparedStatement();
	}
	
	public static function getDBH() {

		$config = Configuration::getInstance();

		$username = $config->username;
		$password = $config->password;
		$ip = $config->ip;
		$port = $config->port;
		$dbname = $config->dbname;

		// Set other attributes as needed: http://php.net/manual/en/pdo.constants.php
		$options = array(
				// Errors should throw exceptions
				PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
				// Fetch associative arrays (@TODO: Fetch hydrated objects)
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				// No conversion of nulls
				PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
			);

		$dsn = CustomDB::getDSN($ip, $dbname, $port);
		$connection = new PDO($dsn, $username, $password, $options);

		return $connection;
	}

	static function getDSN($ip, $dbname, $port) {
		return "mysql:host=$ip;dbname=$dbname;port=$port";
	}

	public function query($sql) {
		$result = $this->connection->query($sql);
		return $result->fetchAll();
	}

	public function prepare($sql) {
		$prepStmtHandle = $this->connection->prepare($sql);
		$purgedHandles = $this->preparedStatementHandleCache->set($sql, $prepStmtHandle);
		while ($purgedHandles > 0) {
			CustomStat::increment('CustomDB.prepStmt.purge');
			$purgedHandles--;
		}
		return $prepStmtHandle;
	}

	public function execute($sql, $params) {
		if (!$this->preparedStatementHandleCache->is_set($sql)) {
			CustomStat::increment('CustomDB.prepStmt.miss');
			$this->prepare($sql);
		} else {
			CustomStat::increment('CustomDB.prepStmt.hit');
		}
		$this->preparedStatementHandleCache->get($sql)->execute($params);
		return $this->preparedStatementHandleCache->get($sql)->fetchAll();

	}
		
}
