DevAAC
=====
DevAAC for TFS 1.0 by developers.pl

__Check out the [REST API documentation](http://dondaniello.github.io/DevAAC)!__

This software is designed to:

* be easy to understand and modify
* emphasize simplicity and security
* provide an example of good programming practices in PHP
* require zero configuration for simple and secure use
* follow [good practices of REST API](http://www.vinaysahni.com/best-practices-for-a-pragmatic-restful-api)
* follow [PSR-2 coding guidelines](http://www.php-fig.org/psr/psr-2/)

Requirements
=====
* PHP >= 5.3.0
* PHP JSON extension
* PHP MySQL
* APC or APCu if you want plugins/ratelimiter.php to work

Installation (dev release)
=====
* [Get composer](https://getcomposer.org/download) ```curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin```
* Clone this repo: ```git clone https://github.com/DonDaniello/DevAAC.git```
* Install dependencies: ```composer.phar install```
* Set up your web server to serve DevAAC/public_html as document root
* Rename ```config.sample.php``` to ```config.php``` and follow instructions

Authors
=====
* Don Daniello
* mrwogu
* with contributions from Znote

Check [LICENSE](LICENSE).

Hacking
=====
Generate Swagger API docs:
```
php vendor/zircote/swagger-php/swagger.phar DevAAC/ -o api-docs
```