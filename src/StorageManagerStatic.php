<?php
namespace Programulin\Storage;

class StorageManagerStatic
{
	private static $manager;

	private static function instance()
	{
		if(!self::$manager)
			self::$manager = new StorageManager();
		
		return self::$manager;
	}

	public static function storage($name)
	{
		return self::instance()->storage($name);
	}
	
	public static function make($name)
	{
		return self::instance()->make($name);
	}
}