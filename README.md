## Silla.IO - PHP Application Development Framework

[![build status](http://gitlab.athlonsofia.com/athlon/silla.io/badges/master/build.svg)](http://gitlab.athlonsofia.com/athlon/silla.io/commits/master)
[![coverage report](http://gitlab.athlonsofia.com/athlon/silla.io/badges/master/coverage.svg)](http://gitlab.athlonsofia.com/athlon/silla.io/commits/master)
[![Github Issues](https://img.shields.io/github/issues/WeAreAthlon/silla.io.svg?style=flat-square)](https://github.com/WeAreAthlon/silla.io/issues)
[![License](https://img.shields.io/badge/license-GPL-blue.svg?style=flat-square)](https://github.com/WeAreAthlon/silla.io/blob/master/LICENSE.txt)
[![Packagist](https://img.shields.io/packagist/dt/weareathlon/silla.io.svg?style=flat-square)](https://packagist.org/packages/weareathlon/silla.io)
[![Release](https://img.shields.io/github/release/WeAreAthlon/silla.io.svg?style=flat-square)](https://github.com/WeAreAthlon/silla.io/releases)

http://silla.io by [Athlon](http://weareathlon.com)

***

### Reference

* **Code API Reference:** http://api.silla.io
* **Demo of the CMS app:** http://demo.silla.io/cms/
    * *Credentials*
        * **email:** _demo@silla.io_
        * **password:** _DemoDemo1!_

***

### Overview

_Silla.IO_ is a lightweight PHP application development framework based on the *MVC* software architecture pattern. 
The framework includes *CMS Application* to provide assistance building custom content management systems.

The code base features *Model-View-Controller* pattern with additional support for:
* `Configuration per environment`
* `ORM` layer for persistent data management
* `Template rendering` engine support for output management
* `URL Routing`
* `Base\Entity` for consolidation of `CRUD` operations
* `Mailer API`
* `Cache API`
* `Session API`
* `Crypt API`
* `i18n`
* `CLI` task manager
* ... *any many more*

***

### Server Software Requirements

* **Operating System**
  * *Type*: `*nix`, `Windows`
* **Web Server**
  * *Type:* `Apache`, `nginx` or compatible(`PHP built-in web server`)
  * *Modules:* (*below are `Apache` module names. Each of them has equivalent for `nginx`*)
    * `mod_rewrite` - *in order to have custom(pretty) URLs*
    * `mod_env` - *in order to easily switch configuration environments*
* **PHP**
  * *Type:* `Standard` (*SAPI - Apache Handler or CLI/CGI/FCGI*)
  * *Version:* `5.3.7` (*or newer*)
  * *Configuration:*
    * Runtime change enabled via `ini_set()`
    * *Extensions*
      * `mbstring` - *Provides multibyte specific string functions that help dealing with multi-byte encodings.*
      * `PDO`, `PDO_mysql`, `mysqli`, `SQLite3` (*depends on on the configured database adapter*)
      * `GD` - *in order to work with media*
* **Database**
  * *Type:* `MySQL` (*depends on on the configured database adapter*)
  * *Version:* `5` (*or newer, recommended `5.5+`*)
* **File System**
  * *Type:* Standard file system(*all `PHP` file functions should work as expected*)
  * *Permissions:* `Writable` directories:
    * *`/temp`*
    * *`/public`*
* **Package Managers**
  * *`Composer`* - *PHP package manager*
