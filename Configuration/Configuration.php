<?php

class Configuration implements configWrapper {

	private $filename = 'configuration.ini';
	protected $cache = array();

	public function __construct() {

		$settings = parse_ini_file($this->filename, true);

		$this->cache = array_merge($settings['local'], $settings['override']);

	}

	public function getSettingValue($key) {
		if (!isset($this->cache[$key])) {
			return null;
		}
		return $this->cache[$key];
	}

	public function __get($key) {
		return $this->getSettingValue($key);
	}
}
