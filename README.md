Библиотека для хранения файлов.
=====================

Библиотека позволяет относительно удобно работать с файлами, раскиданными по разным папкам, а также управлять кешированными копиями изображений.
Библиотека в разработке, используйте на свой страх и риск.

Начало работы
-----------------------------------

1. Устанавливаем c помощью Composer:
```json
{
    "require":{
        "programulin/storage": "dev-master"
    },
    "repositories":[
        {
            "type":"github",
            "url":"https://github.com/programulin/storage"
        }
    ]
}
```

2. Настраиваем все хранилища.

- name - название хранилища,
- path - абсолютный путь к нему,
- level - уровень вложенности папок, от 0 до 2,
- url - абсолютный путь относительно корня сайта. 

```php
use Programulin\Storage\StorageManagerStatic;

$config = [
    [
        'name' => 'product_images',
        'path' => __DIR__ . '\public\images',
        'level' => 0,
        'url'  => '/public/images'
    ],
    [
        'name' => 'product_documents',
        'path' => __DIR__ . '\public\documents',
        'level' => 0,
        'url'  => '/public/documents'
    ],
];

StorageManagerStatic::config($config);
```

Или вариант без статики:

```php
use Programulin\Storage\StorageManager;

$config = [
    [
        'name' => 'product_images',
        'path' => __DIR__ . '\public\images',
        'level' => 0,
        'url'  => '/public/images'
    ],
    [
        'name' => 'product_documents',
        'path' => __DIR__ . '\public\documents',
        'level' => 0,
        'url'  => '/public/documents'
    ],
];
```

Если хотите кеш, для вас приготовлен маленький ад:

- response - колбек для вывода изображения на экран,
- cache_path - абсолютный путь к папке с кешированными копиями изображений,
- cache_url - путь к той же папке относительно корня сайта,
- cache_level - уровень вложенности папок, от 0 до 2,
- cache_sizes - допустимые размеры кеша и наименования этих размеров,
- cache_ext - расширение кешированных копий,
- cache_resize - колбек ресайза.

Ниже полный пример конфигурации. Для response и cache_resize используется библиотека intervention/image.

```php
use Programulin\Storage\StorageManagerStatic;

$func_make = function($path, $end_path, $width, $height)
{
	$img = \Intervention\Image\ImageManagerStatic::make($path);
	
	if($img->width() > $img->height())
		$img->widen($width);
	else
		$img->heighten($height);
	
	$img->resizeCanvas($width, $height)->save($end_path);
};

$func_response = function($path)
{
	echo \Intervention\Image\ImageManagerStatic::make($path)->response();
};

$config = [
	[
		'name'         => 'product_image',
		'path'         => __DIR__ . '\private\product_images',
		'level'        => 0,
		'url'          => '/private/product_images/',
		'response'     => $func_response,
		'cache_path'   => __DIR__ . '\public\product_images',
		'cache_url'    => '/public/product_images/',
		'cache_level'  => 2,
		'cache_sizes'  => [
			'large'    => [800, 800],
			'medium'   => [400, 400],
			'small'    => [100, 100]
		],
		'cache_ext'    => 'jpg',
		'cache_resize' => $func_make
	],
	[
		'name' => 'product_document',
		'path' => __DIR__ . '\public\product_documents',
		'level' => 1,
		'url'  => '/public/product_documents/',
	]
];
```

Работа с файлами
-----------------------------------

```php
// Получение объекта файла. Передаём название хранилища, id, расширение.
$file = StorageManagerStatic::make('product_image', 1, 'png');

// Получаем различные пути
echo $file->getUrl() . '<br>';
echo $file->getPath() . '<br>';
echo $file->getCacheUrl('large') . '<br>';
echo $file->getCachePath('medium') . '<br>';

// Загружаем новый файл
$file->saveFile(__DIR__ . '/img.jpg');

// Создаём кеш
$file->makeCache('medium');

// Выводим исходный файл на экран
$file->response();

// Создаём кеш и выводим его на экран
$file->responseCache('large');

// Удаляем исходный файл
$file->deleteFile();
```