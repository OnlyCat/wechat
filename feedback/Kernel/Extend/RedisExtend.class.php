<?php

	class RedisExtend
	{
		private  $redis = null;
		public function __construct($host = "127.0.0.1", $port = "6379", $key = ""){
			$this->redis = new redis();
			if(!empty($key)){
				$this->redis->connect($host, $port, $key);
			} else {
				$this->redis->connect($host, $port);
			}
		}
		
		public function set($key, $value, $ex = 30)
		{
			$this->redis->set($key, serialize($value), $ex);
		}

		public function get($key)
		{
			return unserialize($this->redis->get($key));
		}
	}

