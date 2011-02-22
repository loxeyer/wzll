<?php

abstract class Cache_Adapter extends SDK_Base
{
	abstract public function add($id, $data, $lifetime = NULL);

	abstract public function get($id, $default = null);

	abstract public function set($id, $data, $lifetime = NULL);

	abstract public function delete($id);
}
