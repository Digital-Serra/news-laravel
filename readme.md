##In development!
####Installation
Require the package:
```
composer require digitalserra/news-laravel
```
Register the service provider:
```
'providers' => [
        ...
        DigitalSerra\NewsLaravel\NewsServiceProvider::class,
     ];   
```
Publish resources:
```
php artisan vendor:publish
```
Enjoy!
