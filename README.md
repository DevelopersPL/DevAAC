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
* PHP >= 5.4
* PHP JSON extension
* PHP MySQL extension
* PHp APC or APCu extension (if you want plugins/ratelimiter.php to work)

Installation (dev release)
=====
* [Get composer](https://getcomposer.org/download) ```curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin```
* Clone this repo: ```git clone https://github.com/DonDaniello/DevAAC.git```
* Install dependencies: ```composer.phar install```
* Set up your web server to serve DevAAC/public_html as document root
* Rename ```config.sample.php``` to ```config.php``` and follow instructions

REST API
=====
* Receive JSON
* Send JSON with Content-Type: application/json or form input with Content-Type: application/x-www-form-urlencoded but don't mix them!
* You can override method with X-HTTP-Method-Override header

Hacking
=====
* [Slim](http://slimframework.com) framework [documentation](http://docs.slimframework.com/)
* API documentation is awesome thanks to [Swagger](https://helloreverb.com/developers/swagger). Put [Swagger Annotations](http://zircote.com/swagger-php/annotations.html) in the code,
* Swagger docs are dynamically server at /api-docs

Generate Swagger API docs manually:
```
php vendor/zircote/swagger-php/swagger.phar DevAAC/ -o api-docs
```

Authors
=====
* Don Daniello
* mrwogu
* with contributions from Znote

Check [LICENSE](LICENSE).
