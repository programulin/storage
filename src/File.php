<?php
namespace Programulin\Storage;

use Intervention\Image\ImageManagerStatic;

class File
{
	private $storage;
	private $id;
	private $ext;

	public function __construct(Storage $storage, $id, $ext = null)
	{
		$this->storage = $storage;
		$this->id = $id;
		$this->ext = $ext;
	}

	public function path()
	{
		return $this->storage->path . $this->getLevelFolders() . $this->getName();
	}
	
	public function has()
	{
		return file_exists($this->path());
	}
	
	public function url()
	{
		return $this->storage->url . $this->getLevelFolders(false, '/') . $this->getName();
	}

	public function cachePath($size)
	{	
		return $this->storage->cache_path . $this->getLevelFolders(true) . "{$this->id}-{$size}.{$this->storage->cache_ext}";  
	}

	public function hasCache($size)
	{
		return file_exists($this->cachePath($size));
	}
	
	public function cacheUrl($size)
	{
		return $this->storage->cache_url . $this->getLevelFolders(true, '/') . "{$this->id}-{$size}.{$this->storage->cache_ext}"; 
	}

	public function cache($size)
	{
		$sizes = $this->storage->cache_sizes[$size];

		$dir = $this->storage->cache_path . $this->getLevelFolders(true);

		if(!file_exists($dir))
			mkdir($dir, $this->storage->mode, true);

		$img = ImageManagerStatic::make($this->path());

		if($sizes[0] === 'auto' and $sizes[1] === 'auto'){}
		
		elseif($sizes[0] === 'auto')
			$img->heighten($sizes[1]);
		
		elseif($sizes[1] === 'auto')
			$img->widen($sizes[0]);

		else
		{
			if($img->width() > $img->height())
				$img->widen($sizes[0]);
			else
				$img->heighten($sizes[1]);
			
			$img->resizeCanvas($sizes[0], $sizes[1], 'center', false, 'ffffff');
		}

		$img->save($this->cachePath($size));
	}

	public function load($path)
	{
		$dir = $this->storage->path . $this->getLevelFolders();

		if(!file_exists($dir))
			mkdir($dir, $this->storage->mode, true);
		
		copy($path, $this->path());
	}
	
	public function delete()
	{
		$path = $this->path();
		
		if(file_exists($path))
			unlink($path);
		
		$sizes = $this->storage->cache_sizes;
		
		foreach($sizes as $name => $size)
		{
			$path = $this->cachePath($name);
			
			if(file_exists($path))
				unlink($path);
		}
	}
	
	public function response($ext = null, $quality = 90)
	{
		if(!$ext)
			$ext = $this->storage->cache_ext;
		
		echo ImageManagerStatic::make($this->path())->response($ext, $quality);
	}

	public function responseCache($size, $ext = null, $quality = 90)
	{
		if(!$ext)
			$ext = $this->storage->cache_ext;
		
		$path = $this->cachePath($size);
		
		if(!file_exists($path))
			$this->cache($size);
		
		echo ImageManagerStatic::make($path)->response($ext, $quality);
	}
	
	private function getLevelFolders($is_cache = false, $ds = null)
	{
		if(!$ds)
			$ds = DIRECTORY_SEPARATOR;
		
		$level = $is_cache ? $this->storage->cache_level : $this->storage->level;
		
		$dir1 = ceil($this->id / 100) * 100;
		$dir2 = ceil($this->id / 10000) * 10000;
		$dir3 = ceil($this->id / 1000000) * 1000000;
		
		$url = $ds;

		if($level === 1)
			$url .= $dir1 . $ds;
		elseif($level === 2)
			$url .= $dir2 . $ds . $dir1 . $ds;
		elseif($level === 3)
			$url .= $dir3 . $ds . $dir2 . $ds . $dir1 . $ds;
		
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