<?php
namespace Programulin\Storage;

class StorageManager
{
	private $configs;
	
	public function config(array $configs)
	{
		foreach($configs as $config)
		{
			$object = new Config($config);
			$this->configs[$object->name] = $object;
		}
	}
	
	public function make($storage, $name, $ext)
	{
		if(!isset($this->configs[$storage]))
			throw new WrongStorage("Не найдены настройки хранилища $storage.");
		
		return new File($this->configs[$storage], $name, $ext);
	}
}