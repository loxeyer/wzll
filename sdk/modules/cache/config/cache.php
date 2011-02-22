<?php

return array(
	'Cache' => array(
		'adapter' => 'Cache_Adapter_Memcache',
	),
	'Cache_Adapter_Memcache' => array(
		'compression' => true,
		'lifetime' => 3600,
		'servers' => array(
			'server1' => array(
				'host' => 'localhost',
				'port' => 11211,
				'persistent' => false,
				'weight' => 1,
				'timeout' => 1,
				'retryInterval' => 15,
				'status' => true,
				'compression' => false,
			),
		),
	),
	'Cache_Adapter_File' => array(
		'cacheDir' => '/tmp',
		'lifetime' => 3600,
	),
);
