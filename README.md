<p align="center">
<img src="https://laravel.com/img/logomark.min.svg" width="100">
<img src="https://laravel-livewire.com/img/twitter.png" width="100">
</p>

## Laravel and Livewire


# Setup
1. From the projects root folder run `composer update`
2. From the projects root folder run `php artisan key:generate`
3. From the projects root folder run `php artisan db:seed`
4. From the projects root folder run `composer dump-autoload`
5. From the projects root folder run `php artisan storage:link`
6.  From the projects root folder run (local) `php artisan schedule:work`


### Folder Ownership and Permission
1. Check the permissions on the storage directory: `chmod -R 775 storage`    
1. Check the ownership of the storage directory: : `chown -R www-data:www-data storage`


### Seeds
##### Seeded Roles
  * Unverified
  * Cusotmer
  * Admin
  * Vendor


##### Seeded Users
|Email|Password|Access|
|:------------|:------------|:------------|
|admin@admin.com|admin123| Admin Access|


## Remove public from url
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteRule ^(.*)$ public/$1 [L]
</IfModule>