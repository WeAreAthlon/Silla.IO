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

* **Code API Reference:** http://api.silla.io
* **Demo of the CMS app:** http://demo.silla.io/cms/
    * *Credentials*
        * email: demo@silla.io
        * password: demo

***

### Overview

Silla.IO is a PHP application development framework based on the *MVC* software architectural pattern. The framework includes *CMS Application* to enable building content management systems.

The code-base features *Model-View-Controller* pattern with additional support for:
* Configuration per environment
* ORM feature for persistent data management
* Template rendering engine support for output management
* Custom URL Routing
* `Base\Resource` for consolidation of `CRUD` operations
* Mailer API
* Cache API
* Session API
* Crypt API
* CLI task manager
* ... *any many more*

***

### Server Software Requirements

* **Operating system**
  * *Type*: `*nix`, `Windows`
* **Web server**
  * *Type:* `Apache`, `nginx` or compatible
  * *Modules:* (*below are `Apache` module names. Each of them has equivalent for `nginx`*)
    * `mod_rewrite` - *in order to have custom(pretty) URLs*
    * `mod_env` - *in order to easily switch configuration environments*
* **PHP**
  * *Type:* Standard (*SAPI - Apache Handler or FCGI*)
  * *Version:* `5.3.7` (*or newer*)
  * *Configuration:*
    * Runtime change enabeled via `ini_set()`
    * *Extensions*
      * `PDO`, `PDO_mysql`, `mysql`, `SQLite` (*depends on on the configured database driver*)
      * `GD` - *in order to work with media*
* **Database**
  * *Type:* `MySQL` (*depends on on the configured database driver*)
  * *Version:* `5` (*or newer, recommended `5.5+`*)
* **File system**
  * *Type:* Standard file system(*all `PHP` file function should work as expected*)
  * *Permissions:* `Writable` directories:
    * *`/temp`*
    * *`/public`*
* **Package managers**
  * *`Composer`* - PHP package manager
  * *`Bower`* - Front-end Assets package manager

***

### Installation
* Place files on the virtual host document root.
* Run `composer install --no-dev` command.
* Run `bower install` command in `/app` and `/cms` directories.
* Amend `configurations/<environment>/configuration.php` file.
* Amend `configurations/<environment>/environment.php` file.
* Choose `Silla.IO` run environment by amending the `.htaccess` file.
* To enable the `CMS Application`:
    * Import the database schema and user credentials from *`vendor/athlon/db/schema.sql`*
    * Navigate to *`/cms`*
