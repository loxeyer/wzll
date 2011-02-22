<?php

class Cache_Adapter_File extends Cache_Adapter
{
	protected $_cacheDir;

	protected function _afterConfig()
	{
		$directory = rtrim($this->_config['cache_dir'], '/').'/';

		if (!is_dir($directory))
		{
			throw new Cache_Exception('directory :dir not exists', array(':dir' => $directory));
		} 
		if (!is_writable($directory))
		{
			throw new Cache_Exception('directory :dir is not writable', array(':dir' => $directory));
		}

		$this->_cacheDir = $directory;
	}

	protected function _filename($key)
	{
		$filename = $this->_cacheDir.sha1($key).'.php';
		return $filename;
	}

	public function get($id, $default = NULL)
	{
		if ($filename = $this->exists($id))
		{
			$data = $this->load($filename);

			if($data['expired'] > time())
			{
				return $data['data'];
			}
			else
			{
				@unlink($filename);
				return $default;
			}
		}
		else
		{
			return $default;
		}
	}

	public function load($file)
	{
		return unserialize(file_get_contents($file));
	}

	public function exists($key)
	{
		$filename = $this->_filename($key);
		return file_exists($filename) ? $filename : false;
	}

	public function set($id, $data, $lifetime = NULL)
	{
		empty($lifetime) && $lifetime = $this->_config['lifetime'];
		$filename = $this->_filename($id);
		$writeData = array(
			'data' => $data,
			'expired' => time() + (int)$lifetime,
			);
		
		$content = serialize($writeData);
		return file_put_contents($filename, $content);
	}

	public function delete($id)
	{
		return @unlink($this->_filename($id));
	}

	public function add($id, $data, $lifetime = NULL)
	{
		empty($lifetime) && $lifetime = $this->_config['lifetime'];

		if ($this->get($id) == NULL)
		{
			$this->set($id, $data, $lifetime);
		}
	}
}
