<?php

class Cache_Adapter_Memcache extends Cache_Adapter {
	
	protected $_memcache;

	protected $_compress;

	protected function _beforeConfig($config)
	{
		if(!class_exists('memcache'))
		{
			throw new Cache_Exception('Memcache PHP extention not loaded');
		}
	}

	protected function _afterConstruct($id)
	{
		$config = $this->_config;

		$this->_memcache = new Memcache;

		foreach ($config['servers'] as $server)
		{
			$this->_memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retryInterval'], $server['status'], array($this, 'failure'));
		}

		$this->_compress = $config['compression'] ? MEMCACHE_COMPRESSED : FALSE;

	}

	public function failure($host, $port)
	{
		throw new Cache_Exception('Memcache could not connect to host "{host}" using port "{port}"',
		   array('{host}' => $host, '{port}' => $port));
	}

	public function get($id, $default = NULL)
	{
		return $this->_memcache->get($id);
	}

	public function set($id, $data, $lifetime = NULL)
	{
		empty($lifetime) && $lifetime = $this->_config['lifetime'];
		return $this->_memcache->set($id, $data, $this->_compress, $lifetime);
	}

	public function add($id, $data, $lifetime = NULL)
	{
		empty($lifetime) && $lifetime = $this->_config['lifetime'];
		return $this->_memcache->add($id, $data, $this->_compress, $lifetime);
	}

	public function delete($id)
	{
		return $this->_memcache->delete($id);
	}

}
