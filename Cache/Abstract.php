<?php

class Cache_Abstract implements Cache {

	private $cache = array();
	private $strategy;
	const CACHE_VALUE = 'value';

	static function getCacheMaxLength() {
		return static::CACHE_MAX_LEN;
	}

	public static function getCacheStrategyType() {
		return static::CACHE_STRATEGY_NAME;
	}

	public function __construct() {
		$strategyName = static::getCacheStrategyType();
		$strategy = new $strategyName();
		$this->setStrategy($strategy);
	}
	
	public function get($key) {
		if (!isset($this->cache[Cache_Abstract::CACHE_VALUE][$key])) {
			// TODO proper exceptions
			throw new Exception("Key $key not set.");
		}
		return $this->strategy->get($this->cache, $key);
	}

	public function is_set($key) {
		if (isset($this->cache[Cache_Abstract::CACHE_VALUE][$key])) {
			return true;
		}
		return false;
	}

	public function purge() {
		$this->cache = array();
	}
		
	public function set($key, $value) {
		return $this->strategy->set($this->cache, $key, $value, static::CACHE_MAX_LEN);
	}

	public function setStrategy(CacheStrategy $strategy) {
		$this->strategy = $strategy;
	}

}
