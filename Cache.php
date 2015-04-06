<?php

interface Cache {
	public function get($key);
	public function set($key, $value);
	public function is_set($key);
	public function setStrategy(CacheStrategy $cacheStrategy);
	public function purge();
	// @TODO, maybe?
	//public function getStats();
}
