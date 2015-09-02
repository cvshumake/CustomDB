<?php

class Cache_PreparedStatement extends Cache_Abstract implements Cache {

	const CACHE_MAX_LEN = 200;
	const CACHESTRATEGY_NAME = CacheStrategy_LRU::CACHESTRATEGY_NAME;

}
