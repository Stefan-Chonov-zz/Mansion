# Mansion
UK Posts REST API

### Dependencies
```
- PHP 8.0.3
  - Windows - https://windows.php.net/downloads/releases/php-8.0.3-nts-Win32-vs16-x64.zip
  - Linux - https://www.php.net/distributions/php-8.0.3.tar.gz
- Redis
  - Windows - https://github.com/downloads/dmajkic/redis/redis-2.4.5-win32-win64.zip
  - Linux - https://download.redis.io/releases/redis-6.2.1.tar.gz?_ga=2.35244200.820840318.1615960662-497991518.1615448615
- Composer 
  - Windows - https://getcomposer.org/Composer-Setup.exe
  - Linux - https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos
```

### Installation

```
https://github.com/Stefan-Chonov/Mansion.git
cd Mansion
cd api
composer install
```

### Configs

```
- PHP - php.ini
  - variables_order = "EGPCS"
  - extension=mbstring
  - extension=curl
  - extension=redis
  - extension=openssl
  
- Environment
  - .env - Location: Mansion\api\.env
```

### Run

PHP Built-in web server: https://www.php.net/manual/en/features.commandline.webserver.php

```
php -S localhost:8001 -t public
```
