<?php

class DB extends Pattern_Singleton {
	// adapters, etc would be great, just need this done for PL2015.
	// Maybe later, hence so many TODOs

	public $connection;

	public function connect($isRequired = false) {

		if ($this->connection && !$isRequired) {
			return;
		}
		unset($this->connection);

		// A db connection class normally requires a resource target
		// but this example only has one possible target
		$tries = 0;
		do {
			$tries++;
			try {
				$this->connection = new CustomDB();
			} catch (Exception $e) {
				if (!$this->isReconnectError($e)) {
					throw $e;
				}
				$config = Configuration::getInstance();
				if ($tries > $config->DB_MAX_TRIES) {
					// TODO proper exceptions
					throw new Exception('Failed to create connection.', $e->getCode(), $e);
				}
				usleep(rand($config->DB_USLEEP_MIN, $config->DB_USLEEP_MAX));
			}
		} while (!$this->connection);

	}

	public function find($className, $args) {
		$config = Configuration::getInstance();
		$object = new $className();

		$whereClause = $this->whereClause($className, $args);
		$params = $this->params($className, $args);
		$sql = 'SELECT * FROM ' . $className::getTableName() . $whereClause;

		$forceReconnect = false;
		$tries = 0;
		while (true) {
			$tries++;
			$this->connect($forceReconnect);
			try {
				$result = $this->connection->execute($sql, $params); 
				break;
			} catch (Exception $e) {
				if ($tries > $config->DB_MAX_TRIES) {
					// TODO proper exceptions
					throw new Exception('Failed to find.', $e->getCode(), $e);
				}
				if (!$this->isRetryableError($e)) {
					throw $e;
				}
				if ($this->isReconnectError($e)) {
					$forceReconnect = true;
				}
				usleep(rand($config->DB_USLEEP_MIN, $config->DB_USLEEP_MAX));
				continue;
			}
		} 
		return $result;
	}

	public function isReconnectError(Exception $e) {
		// TODO Full set of reconnect error codes
		if (in_array($e->getCode(), array(1040,2013))) {
			return true;
		}
		return false;
	}

	public function isRetryableError(Exception $e) {
		// TODO Full set of retry error codes
		if (in_array($e->getCode(), array(1205,1213))) {
			return true;
		}
		return false;
	}

	private function params($className, $args) {
		$metadata = $className::getMetadata();
		foreach ($args as $columnName => $columnValue) {
			if (!$metadata[$columnName]) {
				// TODO proper exceptions
				throw Exception('Field does not exist'); 
			}
			// @TODO implement non-equality, nulls, input normalization, etc
			$params[$columnName] = $columnValue;
		}
		return $params;
	}

	private function whereClause($className, $args) {
		$metadata = $className::getMetadata();
		foreach ($args as $columnName => $columnValue) {
			if (!$metadata[$columnName]) {
				// TODO proper exceptions
				throw Exception('Field does not exist'); 
			}
			// @TODO implement non-equalities, nulls, etc etc
			if (isset($sql)) {
				$sql .= " AND $columnName = :$columnName";
			} else {
				$sql = "WHERE $columnName = :$columnName";
			}
		}
		return $sql;
	}
}
