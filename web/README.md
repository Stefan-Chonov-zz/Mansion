# Mansion
UK Posts Web

### Dependencies
```
- PHP 8.0.3
  - Windows - https://windows.php.net/downloads/releases/php-8.0.3-nts-Win32-vs16-x64.zip
  - Linux - https://www.php.net/distributions/php-8.0.3.tar.gz
- Composer 
  - Windows - https://getcomposer.org/Composer-Setup.exe
  - Linux - https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos
```

### Installation

```
https://github.com/Stefan-Chonov/Mansion.git
cd Mansion
cd web
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
  - .env - Location: Mansion\web\.env
```

### Run

PHP Built-in web server: https://www.php.net/manual/en/features.commandline.webserver.php

```
php -S localhost:8000 -t public
```
