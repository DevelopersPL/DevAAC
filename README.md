DevAAC
=====
DevAAC for TFS 1.0 by developers.pl

This software is designed to:
* be easy to understand and modify
* provide an example of good programming practices in PHP
* require zero configuration for simple and secure use
* emphasize simplicity and security
* follow [good practices of REST API](http://www.vinaysahni.com/best-practices-for-a-pragmatic-restful-api)
* follow [PSR-2 coding guidelines](http://www.php-fig.org/psr/psr-2/)

Requirements
=====
* PHP >= 5.3.0
* php json extension

Nice to have
=====
* APC(u) if you want plugins/ratelimiter.php to work

Installation (dev release)
=====
* [Get composer](https://getcomposer.org/download) ```curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin```
* Clone this repo: ```git clone https://github.com/DonDaniello/DevAAC.git```
* Install dependencies: ```composer.phar install```
* Set up your web server to serve DevAAC/public_html as document root

Authors
=====
* Don Daniello
* mrwogu
* Znote

Check [LICENSE](LICENSE).
