<?php

class CacheStrategy_Abstract implements CacheStrategy {

	public function get(&$cache, $key) {
		return $cache[Cache_Abstract::CACHE_VALUE][$key];
	}

	public function set(&$cache, $key, $value, $max) {
		$cache[Cache_Abstract::CACHE_VALUE][$key] = $value;
	}

	public function purge(&$cache, $key) {
		foreach (&$cache as $cacheListName => $cacheList) {
			unset($cache[$cacheListName][$key]);
		}
	}
		
}
