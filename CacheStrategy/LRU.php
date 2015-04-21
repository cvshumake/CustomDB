<?php

class CacheStrategy_LRU2 extends CacheStrategy_Abstract {

	const CACHESTRATEGY_POINTER = 'pointer';

	public function get(&$cache, $key) {
		$value = $cache[self::CACHESTRATEGY_POINTER][$key];
		unset($cache[self::CACHESTRATEGY_POINTER][$key]);
		$cache[self::CACHESTRATEGY_POINTER][$key] = $value;
		if (!isset($cache[Cache_Abstract::CACHE_VALUE][$key])) {
			// Todo better exceptions
			throw Exception('Value does not exist for key (key=' . $key . ').');
		}
		return $cache[Cache_Abstract::CACHE_VALUE][$key];
	}

	public function set(&$cache, $key, $value, $max) {

		if (isset($cache[Cache_Abstract::CACHE_VALUE][$key])
			&& $cache[Cache_Abstract::CACHE_VALUE][$key] === $value
			&& isset($cache[self::CACHESTRATEGY_POINTER][$key])) {
			return;
		}

		unset($cache[self::CACHESTRATEGY_POINTER][$key]);
		$cache[self::CACHESTRATEGY_POINTER][$key] = true;
		$cache[Cache_Abstract::CACHE_VALUE][$key] = $value; 
		$count = count($cache[Cache_Abstract::CACHE_VALUE]);
		$countPurged = 0;
		if ($count >= $max) {
			reset($cache[self::CACHESTRATEGY_POINTER]);
		//	do {
				$keyPurged = key($cache[self::CACHESTRATEGY_POINTER]);
				$this->purge($cache, $keyPurged);
				$countPurged++;
			//} while (count($cache[Cache_Abstract::CACHE_VALUE]) >= $max);
		}
		return $countPurged;
	}
}
