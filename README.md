## laradmin

Laravel 8 + VueJS 2 admin

### Features

- Role and permission management
- File manager
- Configuration management

### Artisan Command

- `admin:install` - initialize the system
- `admin:make-resource` - all relevant files needed to generate a resource

For example, develop a new carousel function

Execute `php artisan admin:make-resource banner`

It can generate front-end and back-end files for adding, deleting, modifying and testing required for a function


## Install

- `composer install`
- `cp .env.example .env`
- Modify database connection configuration in `.env` file
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan admin:install`
- `npm install && npm run build`

## Nginx Configuration

````nginx
# Path after npm run build
location /admin/ {
    try_files $uri $uri/ /admin/index.html;
}

# Path after npm run watch
location /admin-dev/ {
    try_files $uri $uri/ /admin-dev/index.html;
}
# The above two configurations are optional

# Laravel
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
````

## License
[MIT](LICENSE)
