<?php

// @TODO Create actual autoloader, etc etc

// @TODO Convert to Zend configuration manager
require_once('Configuration.php');
require_once('Configuration/Configuration.php');
if ($username == 'FIXME' || $password == 'FIXME') {
	echo "Cannot continue until username and password are overridden in the configuration.ini file, in the override section.\n";
	exit(1);
}

// @TODO Convert to Zend database adapters, data abstraction objects, etc, etc
require_once('CustomDB.php');
require_once('CustomDB/CustomDB.php');
