## Silla.IO - PHP Application Development Framework#
[![Build Status](https://img.shields.io/jenkins/s/http/jenkins.athlonsofia.com/silla.io.svg?style=flat-square)](http://jenkins.athlonsofia.com/job/silla.io/)
[![Tests Status](https://img.shields.io/jenkins/t/http/jenkins.athlonsofia.com/silla.io.svg?style=flat-square)](http://jenkins.athlonsofia.com/job/silla.io/)
[![Github Issues](https://img.shields.io/github/issues/WeAreAthlon/silla.io.svg?style=flat-square)](https://github.com/WeAreAthlon/silla.io/issues)
[![Release](https://img.shields.io/github/release/WeAreAthlon/silla.io.svg?style=flat-square)] (https://github.com/WeAreAthlon/silla.io/releases)
[![License](https://img.shields.io/badge/license-GPL-blue.svg?style=flat-square)](https://github.com/WeAreAthlon/silla.io/blob/master/LICENSE.txt)
[![Packagist](https://img.shields.io/packagist/dt/weareathlon/silla.io.svg?style=flat-square)](https://packagist.org/packages/weareathlon/silla.io)

http://silla.io by [Athlon](http://weareathlon.com)

***

### Reference

* **Code Reference:** http://api.silla.io
* **Demo of the CMS app:** http://demo.silla.io/cms/
    * *Credentials*
        * email: demo@silla.io
        * password: demo

***

### Overview

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

***

### Server Software Requirements

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

***

### Installation
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
