<?php

class CacheStrategy_RR extends CacheStrategy_Abstract {

	const CACHESTRATEGY_NAME = 'random_replacement';

	public function get(&$cache, $key) {
		if (!isset($cache[Cache_Abstract::CACHE_VALUE][$key])) {
			// Todo better exceptions
			throw Exception('Value does not exist for key (key=' . $key . ').');
		}
		return $cache[Cache_Abstract::CACHE_VALUE][$key];
	}

	public function set(&$cache, $key, $value, int $max) {

		if (isset($cache[Cache_Abstract::CACHE_VALUE][$key])
			&& $cache[Cache_Abstract::CACHE_VALUE][$key] === $value) {
			return;
		}

		$cache[Cache_Abstract::CACHE_VALUE][$key] = $value; 
		$count = count($cache[Cache_Abstract::CACHE_VALUE]);
		$countPurged = 0;
		$keysPurged = array();
		if ($count > $max) {
			$keysPurged = array_rand($cache[Cache_Abstract::CACHE_VALUE], ($count - $max));
		}
		foreach ($keysPurged as $keyPurged) {
			$this->purge($cache, $keyPurged);
			$countPurged++;
		}
		return $countPurged;
	}
}

