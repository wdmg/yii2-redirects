[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.35-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-redirects.svg)](https://packagist.org/packages/wdmg/yii2-redirects)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-redirects.svg)](https://packagist.org/packages/wdmg/yii2-redirects)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-redirects.svg)](https://github.com/wdmg/yii2-redirects/blob/master/LICENSE)

<img src="./docs/images/yii2-redirects.png" width="100%" alt="Yii2 Redirects Module" />

# Yii2 Redirects Module
Redirects module for Yii2.

This module is an integral part of the [Butterfly.СMS](https://butterflycms.com/) content management system, but can also be used as an standalone extension.

Copyrights (c) 2019-2020 [W.D.M.Group, Ukraine](https://wdmg.com.ua/)

# Requirements
* PHP 5.6 or higher
* Yii2 v.2.0.35 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 SelectInput](https://github.com/wdmg/yii2-selectinput) widget

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-redirects"`

After configure db connection, run the following command in the console:

`$ php yii redirects/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-redirects/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'redirects' => [
            'class' => 'wdmg\redirects\Module',
            'autocheck' => true, // Autocheck requested URL
            'allowExternal' => false, // Allow external URL for add
            'routePrefix' => 'admin'
        ],
        ...
    ],

# Usage

    <?php
        
        // Check for redirection
        $url = Yii::$app->request->getUrl();
        Yii::$app->redirects->check($url);
    ?>
    

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'redirects' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('redirects')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [ready to use]
* v.1.0.12 - Update dependencies, README.md
* v.1.0.11 - Added log activity, fixed check for redirection
* v.1.0.10 - Update dependencies, fixed migrations
* v.1.0.9 - Added pagination, up to date dependencies
* v.1.0.8 - Migrations bugfix
* v.1.0.7 - Added allowExternal option for add absolute URL
* v.1.0.6 - Fixed deprecated class declaration
* v.1.0.5 - Fixing redirects model rules and redirect component