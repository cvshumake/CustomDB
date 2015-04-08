<?php

class CacheStrategy_LRU extends CacheStrategy_Abstract {

	const CACHESTRATEGY_UTIME = 'utime';

	public function get(&$cache, $key) {
		$cache[self::CACHESTRATEGY_UTIME][$key] = microtime(true);
		return $cache[Cache_Abstract::CACHE_VALUE][$key];
	}

	public function set(&$cache, $key, $value, $max) {

		$cache[self::CACHESTRATEGY_UTIME][$key] = microtime(true);
		if (isset($cache[Cache_Abstract::CACHE_VALUE][$key])
			&& $cache[Cache_Abstract::CACHE_VALUE][$key] === $value) {
			return;
		}

		$cache[Cache_Abstract::CACHE_VALUE][$key] = $value; 
		$count = count($cache[Cache_Abstract::CACHE_VALUE]);
		$countPurged = 0;
		if ($count >= $max) {
			asort($cache[self::CACHESTRATEGY_UTIME]);
			do {
				$keyPurged = key($cache[self::CACHESTRATEGY_UTIME]);
				$this->purge($cache, $key);
				$countPurged++;
			} while (count($cache[Cache_Abstract::CACHE_VALUE]) >= $max);
		}
		return $countPurged;
	}
		
}
