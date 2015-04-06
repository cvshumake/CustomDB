<?php

namespace DB;

// Would be nice to switch to Zend, but want to stay lighter for a demo
function customAutoloader($class) {
	require_once(str_replace('_', '/', $class) . '.php');
}

spl_autoload_register(__NAMESPACE__ . '\customAutoloader');

