# classic_api_php
Salsalabs Classic API via PHP.

This repository contains demonstrations of using the Salsa Classic API with PHP.

These demos were *not* written by a PHP developer.  Please feel free to make a pull request if you'd like to improve them.

Read the `LICENSE` file.

Now read this:

Salsalabs provides these sources only as demonstration material and makes no assertions of any sort with regard to suitability.

Salsalabs does not support these files. If you have questions or comments, use the `Issues` tab in the Github menu to report those questions and comments.  Contacting Salsalabs Support about the contents of this repository is a waste of your time.  Don't do that.

You use these materials at your own risk.  Salsa is not now, nor will never be, responsible for the contents of this repository.

# Dependencies

These apps depend upon these tools and libraries.

* [Composer](https://getcomposer.org/)
* [GuzzleHTTP](http://docs.guzzlephp.org/en/stable/)
* [Symfony YAML](http://symfony.com/doc/current/components/yaml.html)
* [Salsa Classic API Doc](https://help.salsalabs.com/hc/en-us/articles/115000341773)

# Installation (brief)

Use these steps to install and equip this repository.

1. [Clone this repository.](https://github.com/salsalabs/classic_api_php)
1. [Install composer.](https://getcomposer.org/)
1. Install dependencies
``` bash
composer require guzzlehttp/http
composer require symfony/yaml
composer upgrade
```
# Logging in to the API

The apps in this repository use `credentials.yaml` to provide the parameters for gaining access to the Salsa Classic API.

* API URL
* email
* password

Here is a sample credentials.yaml that you can use.
```
api_host: https://wfc2.wiredforchange.com
email: aleonard@salsalabs.com
password: a-really-long-and-complicated-password
whatever: 123456
```
# Usage

Make sure that the contents of `credentials.yaml` are correct, then type

`php any_php_filename.php`

