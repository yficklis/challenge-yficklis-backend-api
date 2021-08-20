# Github tag stars API 
###### Developed by Yficklis Santos

## Requirements

- [Required] PHP version 8.0.9 https://www.php.net/downloads.php
- [Required] Laravel Framework version 8.54.0 https://laravel.com/docs/8.x/installation
- [Required] Composer version 2.1.5 https://getcomposer.org/download/

## Development

If you contribute in any way to this aplication, at the end you will need to validate the code, executing the codesniffer.

```bash
$ composer require friendsofphp/php-cs-fixer --dev

or 

$ composer global update friendsofphp/php-cs-fixer

$ php-cs-fixer fix .\php-cs-fixer.dist.php
```

## Database

For SQLite, add
```
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
```

If not exists create a _database.sqlite_ file in the _database\database.sqlite_ directory

## Webserver
```
# Run the webserver on port 8000
$ php artisan serve
```

## API Documentation

To Check API documentation - [Click here](http://127.0.0.1:8000/)

__Is necessary start the webserver to checkou the API documentation__
