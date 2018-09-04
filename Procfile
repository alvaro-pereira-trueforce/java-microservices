web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:work database --queue=default --tries=3
instagram: php artisan queue:work database --queue=instagram --tries=3
horizon: php artisan horizon