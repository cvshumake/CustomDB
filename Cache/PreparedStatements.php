<?php

class Cache_PreparedStatements extends Cache_Abstract implements Cache {

	const CACHE_MAX_LEN = 200;
	const CACHE_STRATEGY_NAME = 'CacheStrategy_LRU';

}
