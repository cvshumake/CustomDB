<?php

class CacheStrategy_Abstract implements CacheStrategy {

	// @TODO implement a cache reconciliation in case of strategy change

	public function get(&$cache, $key) {
		return $cache[Cache_Abstract::CACHE_VALUE][$key];
	}

	public function set(&$cache, $key, $value, $max) {
		$cache[Cache_Abstract::CACHE_VALUE][$key] = $value;
	}
		
}
