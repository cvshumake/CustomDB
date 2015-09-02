<?php

class CacheStrategy_LRU extends CacheStrategy_Abstract {

	const CACHESTRATEGY_NAME = 'lru';

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

	public function set(&$cache, $key, $value, int $max) {

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
		while ($count - $countPurged >= $max) {
			reset($cache[self::CACHESTRATEGY_POINTER]);
			$keyPurged = key($cache[self::CACHESTRATEGY_POINTER]);
			$this->purge($cache, $keyPurged);
			$countPurged++;
		}
		return $countPurged;
	}
}
