<?php

interface CacheStrategy {
	public function set(&$cache, $key, $value, $max);
	public function get(&$cache, $key);
}
