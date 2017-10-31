<?php
namespace Programulin\Storage;

class File
{
	private $config;
	private $id;
	private $ext;

	public function __construct(Config $config, $id, $ext = '')
	{
		$this->config = $config;
		$this->id = $id;
		$this->ext = $ext;
	}
	
	public function getPath()
	{
		return $this->config->path . $this->getLevelFolders() . $this->getName();
	}
	
	public function getUrl()
	{
		return $this->config->url . $this->getLevelFolders(false, '/') . $this->getName();
	}

	public function getCachePath($size)
	{
		$sizes = $this->config->cache_sizes[$size];
		
		return $this->config->cache_path . $this->getLevelFolders(true) . "{$this->id}_{$sizes[0]}x{$sizes[1]}.{$this->config->cache_ext}";  
	}

	public function getCacheUrl($size)
	{
		$sizes = $this->config->cache_sizes[$size];
		
		return $this->config->cache_url . $this->getLevelFolders(true, '/') . "{$this->id}_{$sizes[0]}x{$sizes[1]}.{$this->config->cache_ext}"; 
	}

	public function makeCache($size)
	{
		$sizes = $this->config->cache_sizes[$size];

		$dir = $this->config->cache_path . $this->getLevelFolders(true);

		if(!file_exists($dir))
			mkdir($dir, 0777, true);

		$callback = $this->config->cache_resize;
		$callback($this->getPath(), $this->getCachePath($size), $sizes[0], $sizes[1]);
	}

	public function saveFile($path)
	{
		$dir = $this->config->path . $this->getLevelFolders();

		if(!file_exists($dir))
			mkdir($dir, 0777, true);
		
		copy($path, $this->getPath());
	}
	
	public function deleteFile()
	{
		$path = $this->getPath();
		
		if(file_exists($path))
			unlink($path);
	}
	
	public function response()
	{
		$func = $this->config->response;
		$func($this->getPath());
	}
	
	public function responseCache($size)
	{
		$path = $this->getCachePath($size);
		
		if(!file_exists($path))
			$this->makeCache($size);
		
		$func = $this->config->response;
		$func($this->getCachePath($size));
	}
	
	private function getLevelFolders($cache = false, $ds = null)
	{
		if(!$ds)
			$ds = DIRECTORY_SEPARATOR;
		
		$level = $cache ? $this->config->cache_level : $this->config->level;
		
		$first_dir = ceil($this->id / 10000) * 10000;
		$second_dir = ceil($this->id / 100) * 100;

		$url = $ds;
		
		if($level === 1)
			$url .= $first_dir . $ds;
		elseif($level === 2)
			$url .= $first_dir . $ds . $second_dir . $ds;
		
		return $url;
	}
	
	private function getName()
	{
		if($this->ext)
			return "{$this->id}.{$this->ext}";
		else
			return $this->id;
	}
}