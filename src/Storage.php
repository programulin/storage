<?php
namespace Programulin\Storage;

use Programulin\Exceptions\WrongParam;

class Storage
{
	private $path;
	private $level;
	private $url;
	private $mode = 0777;
	private $cache_path;
	private $cache_url;
	private $cache_level;
	private $cache_sizes = [];
	private $cache_ext;

	public function path($path)
	{
		$this->path = rtrim($path, '/\\');
		return $this;
	}
	
	public function level($level)
	{
		if($level < 0 or $level > 3)
			throw new WrongParam('Параметр level может принимать значение от 0 до 3.');
		
		$this->level = $level;
		
		return $this;
	}

	public function url($url)
	{
		$this->url = rtrim($url, '/\\');
		return $this;
	}

	public function mode($mode)
	{
		$this->mode = $mode;
		return $this;
	}
	
	public function cachePath($path)
	{
		$this->cache_path = rtrim($path, '/\\');
		return $this;
	}

	public function cacheUrl($url)
	{
		$this->cache_url = rtrim($url, '/\\');
		return $this;
	}

	public function cacheLevel($level)
	{
		if($level < 0 or $level > 3)
			throw new WrongParam('Параметр cache_level может принимать значение от 0 до 3.');
		
		$this->cache_level = $level;
		
		return $this;
	}	

	public function cacheSizes(array $sizes)
	{
		$this->cache_sizes = $sizes;
		return $this;
	}

	public function cacheExt($ext)
	{
		$this->cache_ext = $ext;
		return $this;
	}

	public function file($id, $ext = null)
	{
		return new File($this, $id, $ext);
	}

	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}
}