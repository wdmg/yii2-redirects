[![Progress](https://img.shields.io/badge/required-Yii2_v2.0.13-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-redirects/total.svg)](https://GitHub.com/wdmg/yii2-redirects/releases/)
[![GitHub version](https://badge.fury.io/gh/wdmg/yii2-redirects.svg)](https://github.com/wdmg/yii2-redirects)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-redirects.svg)](https://github.com/wdmg/yii2-redirects/blob/master/LICENSE)

# Yii2 Redirects Module
Redirects module for Yii2

# Requirements
* PHP 5.6 or higher
* Yii2 v.2.0.20 and newest
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

# Status and version [in progress development]
* v.1.0.1 - Added views, common models for import and search
* v.1.0.0 - Added base model, console controller, bootstrap, base module, translations and migrations