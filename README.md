DevAAC by developers.pl for [TFS 1.0](https://github.com/otland/forgottenserver)
=====
Quick facts:

* This AAC is built as a SPA (Single Page Application) on the front-end and a RESTful API on the back-end.
* It is supposed to be easily extensible via plugins (check ```plugins/example.php```.
* The core of this AAC does not modify TFS' database schema. News are loaded as static markdown files from ```public_html/news```.
* The REST API is planned to be utilized by many external projects like OT server lists and OT Client.


__Check out the [REST API documentation](http://developerspl.github.io/DevAAC)!__

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
* PHP MySQL NATIVE DRIVER (mysqlnd) extension (or [this](http://forumsarchive.laravel.io/viewtopic.php?pid=58151) will happen)
* PHP APC or APCu extension (if you want ```plugins/ratelimiter.php``` to work)

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
* Send JSON with ```Content-Type: application/json``` or form input with ```Content-Type: application/x-www-form-urlencoded``` but don't mix them!
* You can override method with X-HTTP-Method-Override header

Hacking
=====
* [Slim](http://slimframework.com) framework [documentation](http://docs.slimframework.com/)
* API documentation is awesome thanks to [Swagger](https://helloreverb.com/developers/swagger). Put [Swagger Annotations](http://zircote.com/swagger-php/annotations.html) in the code!
* You can use [Vagrant](http://www.vagrantup.com/) to setup a development machine. [Install Vagrant](http://www.vagrantup.com/downloads), execute ```vagrant up``` in project root and connect to [http://localhost:8044/](http://localhost:8044/)
* Swagger docs are dynamically server at /api-docs

You can generate Swagger API docs manually:
```
php vendor/zircote/swagger-php/swagger.phar DevAAC/ -o api-docs
```

Authors
=====
* [Daniel Speichert](https://github.com/DSpeichert)
* [Wojciech Guziak](https://github.com/mrwogu)
* with contributions from [Znote](https://github.com/Znote)

Contributions are always welcome, please submit pull requests!
We are looking for back-end plugins and front-end functionality of themes.
(It is proper to keep non-essential functionality in the form of plugins.)

Check [LICENSE](LICENSE).
