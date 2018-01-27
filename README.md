Библиотека для удобного управления файлами.
=====================

Библиотека позволяет удобно работать с хранилищами файлов и генерировать кешированные копии изображений.

Установка
-----------------------------------

Устанавливаем c помощью Composer:

```php
composer require programulin/storage:dev-master
```

Как работать
-----------------------------------

Сначала настраиваем все хранилища.


```php
use Programulin\Storage\StorageManagerStatic;

// Создаём простое хранилище:
StorageManagerStatic::make('product_files') // Название хранилища
        ->path(__DIR__ . '\data\product_files') // Путь к хранилищу
        ->level(1) // Сколько уровней подпапок создавать (от 0 до 3)
        ->url('/data/product_files/'); // URL хранилища относительно корня сайта

// Создаём простое хранилище + хранилище кешированных копий:
StorageManagerStatic::make('product_images')
        ->path(__DIR__ . '\data\product_images')
        ->level(1)
        ->url('/data/product_images/')

        ->cachePath(__DIR__ . '\data\cache\product_images') // Путь к хранилищу кеша
        ->cacheLevel(2) // Сколько уровней подпапок создавать (от 0 до 3)
        ->cacheUrl('/data/cache/product_images/') // URL хранилища кэша относительно корня сайта
        ->cacheSizes([ // Размеры кешированных копий изображений. Указываем название кеша, ширину и высоту
            's' => [200, 200], // Если изображение изначально не квадратное, с меньших сторон появятся белые полосы
            'w' => [400, 'auto'], // Ширина 400, высота вычислится автоматически
            'h' => ['auto', 400], // Высота 400, ширина вычислится автоматически
            'o' => ['auto', 'auto'], // Изображение будет оригинальных размеров
        ])
        ->cacheExt('jpg'); // Расширение всех кешированных копий
```

Если не нравится статика:
```php
use Programulin\Storage\StorageManager;

$m = new StorageManager();
$m->make('product_files');
... // И далее как в примере выше
```

Теперь можно работать с файлами:

```
// Получаем объект файла по его id и расширению:
$file = StorageManagerStatic::storage('product_images')->file(1058, 'jpg');

// Получаем информацию о файле:
echo $file->path(); // Абсолютный путь к файлу
echo $file->url(); // URL файла от корня сайта
echo $file->cachePath('s'); // Абсолютный путь к кешу
echo $file->cacheUrl('s'); // URL кеша от корня сайта

echo $file->has(); // Проверка существования файла
echo $file->hasCache('s'); // Проверка существования кеша

$file->cache('s'); // Создание кеша

$file->load('some/path/to/file.jpg'); // Загружаем новый файл
$file->delete(); // Удаляем файл и кеш

// Быстрый вывод файла на экран
$file->response();
$file->response('png', 20); // Можно указать расширение (null если не хотите менять) и качество
$file->responseCache('s'); // Аналогично для кеша
$file->responseCache('s', 'jpg', 80);
```