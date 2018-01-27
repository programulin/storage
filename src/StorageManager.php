<?php
namespace Programulin\Storage;

use Programulin\Exceptions\WrongStorage;

class StorageManager
{
	private $storages;
	
	public function storage($name)
	{
		return $this->storages[$name];
	}

	public function make($name)
	{	
		return $this->storages[$name] = new Storage;
	}
}