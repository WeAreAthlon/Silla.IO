# Silla.IO #
[![Build Status](http://jenkins.athlonsofia.com/view/Silla/job/Silla/badge/icon)](http://jenkins.athlonsofia.com/view/Silla/job/Silla/)
## PHP CMS Framework by Athlon ##

http://silla.io

----
## Reference ##

* **Code Reference:** http://api.silla.io
* **Demo of the CMS app:** http://demo.silla.io/cms/
    * *Credentials*
        * email: demo@silla.io
        * password: demo

## Overview ##

_MVC-L_ lightweight PHP CMS Framework for managing bespoke PHP projects.
The framework includes _CMS application_ to enable building content management systems.

The code-base features _Model-View-Controller_ pattern with additional support for:

* Configuration per environment
* ORM feature for managing persistent data
* Template engine for views
* Pretty URL Routing
* Base Resource for consolidation of `CRUD` operations
* Mailer API
* Cache API
* Session API
* Crypt API
* CLI task manager

## Server Software Requirements ##

* **Apache** web server or compatible
    * *Modules*
        * `mod_rewrite` - *in order to have custom(pretty) URLs*
        * `mod_env` - *in order to easily switch configuration environments*
* **PHP 5.3.7+**
    * *Configuration*
        * `memory_limit 32M` ( _at least_ )
    * *Extensions*
        * `PDO`, `PDO_mysql`, `mysql`, `SQLite` - *depends on on the configured database driver*
        * `GD` - *in order to manage images*
* **MySQL 5+**
* **Writable directories**
    * `/temp`
    * `/public` - _applicable only when uploading assets via the application_
* **Composer** - _recommended_
* **Bower** - _recommended_

----
## Installation ##
* Place files on the virtual host document root
    * For better security place only contents of `public` directory and configure the application(via `.htaccess` to load the framework core from non-public directory)
* Run `composer install --no-dev`
* Run `bower install` in `/app` and `/cms`
* Edit `configurations/<environment>/configuration.php`
* Edit `configurations/<environment>/environment.php`
* Choose run environment in `.htaccess`
* To enable the CMS application
    * Import the database schema and user credentials from `vendor/athlon/db/schema.sql`
    * Navigate to `/cms`

----
## Dependency manager ##

* [Composer](https://getcomposer.org/)
* [Bower](http://bower.io/)

----
## Third-party Libraries managed via `Composer` ##

* [Smarty](http://www.smarty.net/)
* [TCPDF](http://www.tcpdf.org/)
* [Spyc](https://github.com/mustangostang/spyc/)
* [Imagine](https://imagine.readthedocs.org)
* [PHPMailer](http://phpmailer.worxware.com/)

----
## Front-end libraries managed via `Bower` ##

* [jQuery](http://jquery.com/download/)
* [Chosen - drop-down select](https://github.com/harvesthq/chosen/)
* [Bootstrap 3](http://getbootstrap.com/)
* [Datepicker](http://www.eyecon.ro/bootstrap-datepicker)
* [Colorpicker](http://www.eyecon.ro/bootstrap-colorpicker)
* [Lightbox](https://github.com/ashleydw/lightbox)
* [Maxlength](https://github.com/mimo84/bootstrap-maxlength)
* [Daterangepicker](https://github.com/dangrossman/bootstrap-daterangepicker)
* [Spin.js](http://fgnass.github.io/spin.js/)

----
## Fonts used in the CMS namespace ##

* Helvetica Neue
* [Open Sans](http://www.google.com/fonts/specimen/Open+Sans)
