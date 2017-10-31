<?php
namespace Programulin\Storage;

use Programulin\Storage\Exceptions\WrongConfig;

class Config
{
	private $name;
	private $path;
	private $level;
	private $url;
	private $cache_path;
	private $cache_url;
	private $cache_level;
	private $cache_sizes;
	private $cache_ext;
	private $cache_func;
	
	public function __construct(array $config)
	{
		if(!isset($config['name'], $config['path'], $config['level']))
			throw new WrongConfig('Параметры name, path, level обязательны для заполнения.');

		if($config['level'] < 0 or $config['level'] > 2)
			throw new WrongConfig('Параметр level может принимать значение от 0 до 2.');
		
		$this->name = $config['name'];
		$this->path = rtrim($config['path'], '/\\');
		$this->level = (int) $config['level'];

		if(isset($config['url']))
			$this->url = rtrim($config['url'], '/\\');

		if(isset($config['cache_path']))
			$this->cache_path = rtrim($config['cache_path'], '/\\');
		
		if(isset($config['cache_url']))
			$this->cache_url = rtrim($config['cache_url'], '/\\');

		if(isset($config['cache_level']))
		{
			if($config['cache_level'] < 0 or $config['cache_level'] > 2)
				throw new WrongConfig('Параметр cache_level может принимать значение от 0 до 2.');
		
			$this->cache_level = $config['cache_level'];
		}
		
		if(isset($config['cache_sizes']))
			$this->cache_sizes = $config['cache_sizes'];
		
		if(isset($config['cache_ext']))
			$this->cache_ext = $config['cache_ext'];
		
		if(isset($config['cache_resize']))
			$this->cache_resize = $config['cache_resize'];

		if(isset($config['response']))
			$this->response = $config['response'];
	}
	
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}
}