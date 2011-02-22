<?php
/**
 * Cache Class
 *
 * @author lizhong <lizhong@ifeng.com>
 * @version 0.1
 */
class Cache extends SDK_Base
{
	protected $_adapter;

	protected function _afterConstruct($id)
	{
		$adapterClass = $this->_config['adapter'];
		$this->_adapter = SDK::instance($adapterClass);
	}

	public function add($id, $data, $lifetime = NULL)
	{
		return $this->_adapter->add($id, $data, $lifetime);
	}

	public function get($id, $default = null)
	{
		return $this->_adapter->get($id, $default);
	}

	public function set($id, $data, $lifetime = NULL)
	{
		return $this->_adapter->set($id, $data, $lifetime);
	}

	public function delete($id)
	{
		return $this->_adapter->delete($id);
	}
}
