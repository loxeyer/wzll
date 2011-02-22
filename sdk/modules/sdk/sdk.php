<?php
/**
 * SDK Class
 *
 * @dependency arr
 * @author lizhong <lizhong@ifeng.com>
 * @version 0.1
 */
class SDK
{
	protected static $_configPath;
	protected static $_configPrefix;

	protected static $_modulePath;
	protected static $_helperPath;
	protected static $_vendorPath;

	public static $isCli = false;

	/**
	 * init system
	 */
	public static function init()
	{
		static $init;
		if (!empty($init))
			return ;

		SDK::$_modulePath= realpath(dirname(__FILE__).'/../../modules').'/';
		SDK::$_helperPath = realpath(dirname(__FILE__).'/../../helpers').'/';
		SDK::$_vendorPath = realpath(dirname(__FILE__).'/../../vendors').'/';
		if (PHP_SAPI == 'cli')
			SDK::$isCli = true;

		spl_autoload_register('SDK::autoload');
		SDK::initModules();

		$init = true;
	}

	/**
	 * set config dir. if a module needs config, you can put it in a config dir,
	 * eg. config/cache.php, when SDK::instance('cache'), it will search for config/cache.php,
	 * if file exists, load it, and merge with default config
	 *
	 * @param string $path directory name
	 * @param string $prefix config file's prefix, like 'SDK'
	 */
	public static function setConfigDir($path, $prefix = '')
	{
		SDK::$_configPath = rtrim($path, '/').'/';
		SDK::$_configPrefix = $prefix;
	}

	/**
	 * init module, if there's init.php in it ,require it
	 */
	public static function initModules()
	{
		foreach (glob(SDK::$_modulePath.'*', GLOB_ONLYDIR) as $dir)
		{
			// without "_" prefixed is enabled, and if there is init.php, require it
			if ($dir[0] !== '_' && file_exists($dir.'/init.php'))
			{
				require $dir.'/init.php';
			}
		}
	}

	/**
	 * fetch item's config if config_dir is set
	 *
	 * @param string $item
	 * @return mixed
	 */
	public static function getConfig($item)
	{
		static $configs = array();
		if (isset($configs[$item])) 
			return $configs[$item];

		$file = strtolower($item);

		if (strpos($item, '_') !== false)
		{
			list($file,) = explode('_', $item, 2);
			$file = strtolower($file);
		}
		
		if (!isset($configs[$file]))
		{
			$configs[$file] = array();

			if (!empty(self::$_configPath))
			{
				$config_file = self::$_configPath.self::$_configPrefix.$file.'.php';
				if (is_file($configFile))
				{
					$configs[$file] = include $configFile;
				}
			}
			elseif (is_file($configFile = SDK::$_modulePath.$file.'/config/'.$file.'.php'))
			{
				$configs[$file] = include $configFile;
			}
			else
			{
				return false;
			}
		}

		return isset($configs[$file][$item]) ? $configs[$file][$item] : null;
		
	}

	/**
	 * global factory method
	 *
	 * @param string $class class name
	 * @param string $id factory use this id to return different object
	 * @param array $config class's config
	 * @return object
	 */
	public static function factory($class, $id = null, $config = null)
	{
		if (is_null($config))
			$config = SDK::getConfig($class);

		$obj = new $class($config, $id);
		return $obj;
	}

	/**
	 * global instance method
	 *
	 * @param string $class class name
	 * @param array $config class's config
	 * @return object
	 */
	public static function instance($class, $id = null, $config = null)
	{
		static $classes = array();

		if (is_null($config))
			$config = SDK::getConfig($class);

		if (!isset($classes[$class]))
		{
			$classes[$class] = new $class($config, $id);
		}

		return $classes[$class];
	}

	/**
	 * auto load class
	 * 
	 * @param string $classname class name
	 * @return boolean
	 */
	public static function autoload($classname)
	{
		$classname  = strtolower($classname);
		$maybeHelper = false;
		if (strpos($classname, '_') === false)
		{
			$classfile = $classname.'/'.$classname.'.php';
			$maybeHelper = true;
		}
		else
		{
			$classfile = str_replace('_', '/', $classname).'.php';
		}
		if (file_exists(SDK::$_modulePath.$classfile))
		{
			require SDK::$_modulePath.$classfile;
			return true;
		}
		if ($maybeHelper)
		{
			$classfile = SDK::$_helperPath.$classname.'.php';
			if (file_exists($classfile))
			{
				require $classfile;
				return true;
			}
		}
		return false;
	}
}
