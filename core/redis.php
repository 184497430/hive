<?php
/**
 * Created by PhpStorm.
 * User: zhenglinqiu
 * Date: 14-5-18
 * Time: 上午11:44
 */
class RedisFactory
{
	static private $instances;
	private $redis_name;
	private $node_master;
	private $node_slaves = array();
	private $node_slave_rand = '';
	//为了安全，白名单内的读操作走从节点
	private $read_methods = array(
		'exists',
		'type',
		'ttl',
		'keys',
		'getkeys',
		'get',
		'mget',
		'srandmember',
		'scard',
		'ssize',
		'smembers',
		'sismember',
		'scontains',
		'sunion',
		'sdiff',
		'sinter',
		'llen',
		'lrange',
		'lget',
		'sort',
		'hgetall',
		'hlen',
		'hexists',
		'hmget',
		'hget',
		'hkeys',
		'hvals',
		'zcard',
		'zsize',
		'zcount',
		'zrange',
		'zrangebyscore',
		'zrangebypage',
		'zrank',
		'zrevrange',
		'zrevrangebyscore',
		'zrevrangebypage',
		'zrevrank',
		'zscore',
		'multislave',
	);

	function __construct($redis_name) {
		$config = Core::getConfig('redis.' . $redis_name);
		if (!is_array($config) || empty($config['master'])) {
			core::debugLog('redis_node_config_error', 'redis node config error:' . $redis_name);
		}
		$this->redis_name = $redis_name;
		//主节点
		$this->node_master = $config['master'];
		//从节点
		if (isset($config['slave'])) {
			if (is_array($config['slave'])) {
				$this->node_slaves = $config['slave'];
			} elseif (!empty($config['slave'])) {
				$this->node_slaves = array($config['slave']);
			}
			//随机一个从节点提供服务
			$this->node_slave_rand = $this->node_slaves[array_rand($this->node_slaves)];
		}
	}

	//获取所有实例
	static public function getInstances() {
		return self::$instances;
	}

	/**
	 * 单利模式
	 * @static
	 * @param string $redis_name
	 * @return RedisStorage
	 */
	static public function getInstance($redis_name) {
		if (!isset(self::$instances[$redis_name]) || !is_object(self::$instances[$redis_name])) {
			self::$instances[$redis_name] = new self($redis_name);
		}

		return self::$instances[$redis_name];
	}

	//魔术方法(读写分离全靠这货了)
	public function __call($func, $params) {
		$func  = strtolower($func);
		$redis = $this->getRedisObj($func);
		if ($redis === NULL) {
			return FALSE;
		}

		$ret = FALSE;
		$_st = microtime(TRUE);
		try {
			$ret = call_user_func_array(array(&$redis, $func), $params);
		} catch (RedisException $e) {
			Core::sysLog('err_exec_redis', $e->getMessage());
		}
		$ut = microtime(TRUE) - $_st;
		if ($ut >= 0.05 && !in_array($func, array('blpop', 'brpop'))) {
			Core::sysLog('slow_exec_redis', array(
				'ut'     => round($ut, 4),
				'node'   => $redis->node,
				'func'   => $func,
				'params' => $params
			));
		}

		return $ret;
	}

	/**
	 * 根据读写操作分配不同的节点
	 * @return RedisStorage
	 */
	protected function getRedisObj($func) {
		$_redis = NULL;
		//读操作
		if (in_array($func, $this->read_methods) && !empty($this->node_slave_rand)) {
			for ($try = 1; $try <= 3; $try++) {
				try {
					$_redis = RedisStorage::getInstance($this->node_slave_rand);
					break;
				} catch (RedisException $e) {
					//剔除后重新尝试
					if (count($this->node_slaves) > 1) {
						unset($this->node_slaves[array_search($this->node_slave_rand, $this->node_slaves)]);
						$this->node_slave_rand = $this->node_slaves[array_rand($this->node_slaves)];
					}
				}
			}
		} //写操作
		else {
			for ($try = 1; $try <= 3; $try++) {
				try {
					$_redis = RedisStorage::getInstance($this->node_master);
					break;
				} catch (RedisException $e) {
				}
			}
		}

		return $_redis;
	}
}

class RedisStorage extends Redis
{
	public $node = '';
	private static $instances = array();

	/**
	 * @return RedisStorage
	 */
	public static function getInstance($node) {
		list($ip, $port) = explode(':', $node);
		if (!isset(self::$instances[$node])) {
			// 返回新的实例
			$new_connect = new self();
			$result      = $new_connect->connect($ip, $port);
			if ($result == FALSE) {
				throw new RedisException("{$ip}:{$port} Connected Faild");

				return NULL;
			}
			self::$instances[$node] = $new_connect;
		}

		return self::$instances[$node];
	}

	public function connect($ip, $port = '6379', $timeout = NULL) {
		$connect = FALSE;
		for ($try = 1; $try <= 3; $try++) {
			$start   = microtime(TRUE);
			$connect = parent::connect($ip, $port, $timeout);
			$time    = microtime(TRUE) - $start;
			if ($time > 0.05) {
				Core::sysLog('slow_conn_redis', array(
					'ut'   => round($time, 4),
					'node' => $ip . ':' . $port,
					'try'  => $try,
				));
			}

			if ($connect == TRUE) {
				break;
			} else {
				Core::sysLog('err_conn_redis', array(
					'node' => $ip . ':' . $port,
					'try'  => $try
				));
			}
		}
		$this->node = $ip . ':' . $port;

		return $connect;
	}

	public function multiMaster($mode = Redis::PIPELINE) {
		return $this->multi($mode);
	}

	public function multiSlave($mode = Redis::PIPELINE) {
		return $this->multi($mode);
	}

	//zset正续分页
	public function zRangeByPage($key, $page, $page_size) {
		$obj  = $this->multiSlave();
		$data = $obj->zCard($key)->zRange($key, ($page - 1) * $page_size, $page * $page_size - 1)->exec();

		return $this->appendPageInfo($page, $page_size, $data[0], $data[1]);
	}

	//zset逆序分页
	public function zRevRangeByPage($key, $page, $page_size) {
		$obj  = $this->multiSlave();
		$data = $obj->zCard($key)->zRevRange($key, ($page - 1) * $page_size, $page * $page_size - 1)->exec();

		return $this->appendPageInfo($page, $page_size, $data[0], $data[1]);
	}

	//输出分页信息
	private function appendPageInfo($page, $page_size, $total, $items) {
		return array(
			'page_info' => array(
				'page'       => $page,
				'page_size'  => $page_size,
				'total'      => $total,
				'total_page' => ($total > 0 && $page_size > 0) ? ceil($total / $page_size) : 0
			),
			'items'     => $items,
		);
	}
}