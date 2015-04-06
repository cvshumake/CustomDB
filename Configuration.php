<?php

class Configuration extends Pattern_Singleton {

	private $filename = 'configuration.ini';
	private $cache = array();
	private $isParsed = false;

	public function getSettingValue($key) {
		$this->hydrate();
		if (!isset($this->cache[$key])) {
			return null;
		}
		return $this->cache[$key];
	}

	public function __get($key) {
		return $this->getSettingValue($key);
	}

	public function hydrate() {
		if ($this->isParsed) {
			return;
		}
		$settings = parse_ini_file($this->filename, true);
		$this->cache = array_merge($settings['local'], $settings['override']);
		$this->isParsed = true;
	}

}
