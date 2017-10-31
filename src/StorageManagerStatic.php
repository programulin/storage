<?php
namespace Programulin\Storage;

class StorageManagerStatic
{
	private static $manager;

	private static function manager()
	{
		if(!self::$manager)
			self::$manager = new StorageManager();
		
		return self::$manager;
	}
	
	public static function config(array $config)
	{
		self::manager()->config($config);
	}
	
	public static function make($storage, $name, $ext)
	{
		return self::manager()->make($storage, $name, $ext);
	}
}