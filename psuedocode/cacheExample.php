<?php

class City extends CustomDB {

	public function findById($id, $useCache=true) {

		// Throw exceptions if not integer or boolean, also is Not nullable
		CustomDB_Util::assertInteger($id, false);
		CustomDB_Util::assertBoolean($useCache, false);

		if ($useCache) {
			$result = static::getRegistryValue($id);
			if ($result) return $result->value;
			$result = static::getMemcacheValue($id);
			if ($result) return $result->value;
		}
		$result = static::getSlaveValue($id);
		if ($result) return $result->value;
		$result = static::getMasterValue($id);
		if ($result) return $result->value;

		throw Exception('Not found');
	}
}
