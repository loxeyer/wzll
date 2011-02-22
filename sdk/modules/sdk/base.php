<?php
/**
 * Base SDK Class
 *
 * @author lizhong <lizhong@ifeng.com>
 * @version 0.1
 */
class SDK_Base
{
	protected $_config = array();

	protected $_importedFuncs = array();

	/**
	 * something to do before set config
	 */
	protected function _beforeConfig($config) {}

	/**
	 * something to do after config
	 */
	protected function _afterConfig() {}

	/**
	 * something to do after construct
	 */
	protected function _afterConstruct($id) {}

	/**
	 * config construct
	 *
	 * @param array $config
	 * @param string $id 
	 */
	public function __construct($config = null, $id = null)
	{
		// pass config param
		$this->_beforeConfig($config);

		if (!empty($config))
		{
			$this->setConfig($config);
		}

		$this->_afterConfig();

		$this->_afterConstruct($id);
	}

	/**
	 * set config
	 *
	 * @param string | array $key config's key or an array, if array is provided , ignore the 2nd param
	 * @param string $val config value
	 */
	public function setConfig($key , $val = null)
	{
		if (is_string($key))
		{
			$this->_config[$key] = $val;
		}
		elseif (is_array($key))
		{
			$this->_config = Arr::merge($this->_config, $key);
		}
	}

	/**
	 * get config
	 *
	 * @param string $key config key
	 * @param mixed $default default
	 * @return mixed
	 */
	public function getConfig($key = null, $default = null)
	{
		if (empty($key))
		{
			return $this->_config;
		}
		else
		{
			return isset($this->_config[$key]) ? $this->_config[$key] : $default;
		}
	}

	/**
	 * if has attachBehavior, check if func exists
	 *
	 * @param string $method method
	 * @param mixed $args args
	 */
	public function __call($method, $args)
	{
		//todo I intend to add & before $this, but it keeps reporting warning: Call-time pass-by-reference has been deprecated
		array_unshift($args, $this);
		if (isset($this->_importedFuncs[$method]))
		{
			call_user_func_array(array($this->_importedFuncs[$method], $method), $args);
		}
	}

	/**
	 * extends class by import other class
	 *
	 * @param string $class class name
	 */
	public function attachBehavior($class)
	{
		$obj = new $class();
		$funcs = get_class_methods($obj);
		foreach ($funcs as $func)
		{
			$this->_importedFuncs[$func] = &$obj;
		}
	}

	/**
	 * set key value
	 *
	 * @param string $key key
	 * @param mixed $val value
	 * @return mixed
	 */
	public function __set($key, $val)
	{
		$setter = 'set_'.$key;
		if (method_exists($this, $setter))
		{
			return $this->$setter($val);
		}
	}

	/**
	 * get key value
	 *
	 * @param string $key key
	 * @param mixed $val value
	 * @return mixed
	 */
	public function __get($key)
	{
		$getter = 'get_'.$key;
		if (method_exists($this, $getter))
		{
			return $this->$getter();
		}
	}
}
