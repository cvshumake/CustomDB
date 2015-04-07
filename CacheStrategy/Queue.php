<?php

class CacheStrategy_LRU extends CacheStrategy_Abstract {

	public function get(&$cache, $key) {
		return $cache[Cache_Abstract::CACHE_VALUE][$key];
	}

	public function set(&$cache, $key, $value, $max) {

		if (isset($cache[Cache_Abstract::CACHE_VALUE][$key]
			&& $cache[Cache_Abstract::CACHE_VALUE][$key] === $value)) {
			return;
		}

		$cache[Cache_Abstract::CACHE_VALUE][$key] = $value; 
		$count = count($cache[Cache_Abstract::CACHE_VALUE]);
		$countPurged = 0;
		if ($count >= $max) {
			do {
				array_shift($cache[Cache_Abstract::CACHE_VALUE]);
				$count = count($cache[Cache_Abstract::CACHE_VALUE]);
				$countPurged++;
			} while ($count >= $max);
		}
		return $countPurged;
	}
		
}
