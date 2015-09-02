<?php

class CacheStrategy_LFU extends CacheStrategy_Abstract {

	const CACHESTRATEGY_COUNT = 'count';

	public function get(&$cache, $key) {
		$cache[self::CACHESTRATEGY_COUNT][$key]++;
		return $cache[Cache_Abstract::CACHE_VALUE][$key];
	}

	public function set(&$cache, $key, $value, $max) {

		if (isset($cache[Cache_Abstract::CACHE_VALUE][$key])
			&& $cache[Cache_Abstract::CACHE_VALUE][$key] === $value) {
			return;
		}

		$cache[self::CACHESTRATEGY_COUNT][$key] = 0;
		$cache[Cache_Abstract::CACHE_VALUE][$key] = $value; 
		$count = count($cache[Cache_Abstract::CACHE_VALUE]);
		$countPurged = 0;
		if ($count >= $max) {
			asort($cache[self::CACHESTRATEGY_COUNT]);
		}
		while ($count - $countPurged > $max) {
			$keyPurged = key($cache[self::CACHESTRATEGY_COUNT]);
			$this->purge($cache, $keyPurged);
			array_shift($cache[self::CACHESTRATEGY_COUNT]);
			$count = count($cache[Cache_Abstract::CACHE_VALUE]);
			$countPurged++;
		}
		return $countPurged;
	}
