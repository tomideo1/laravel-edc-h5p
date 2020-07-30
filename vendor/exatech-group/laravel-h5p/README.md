

# H5P Plugin in Laravel Framework 

## Description
Fork from Djoudi

## Installation

Require it in the Composer.

```bash
composer require exatech-group/laravel-h5p
```

Publish the Views, Config and so things.

```bash
php artisan vendor:publish --provider=Djoudi\LaravelH5p\LaravelH5pServiceProvider --force
```

Migrate the Database

```bash
php artisan migrate
```

Sur linux
```bash
cd public/vendor/h5p
ln -s ../../../storage/h5p/content
ln -s ../../../storage/h5p/editor
ln -s ../../../storage/h5p/libraries
```
Ou sur windows
```cmd admin window
cd public/vendor/h5p
mklink /d content ..\..\..\storage\h5p\content
mklink /d editor ..\..\..\storage\h5p\editor
mklink /d libraries ..\..\..\storage\h5p\libraries
```