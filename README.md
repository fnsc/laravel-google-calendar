<h1 align="center">Laravel Google Calendar</h1>

<p align="center">
    <a href="https://github.com/fnsc/laravel-google-calendar/graphs/contributors" alt="Contributors"><img src="https://img.shields.io/github/contributors/fnsc/laravel-google-calendar" /></a>
    <a href="https://github.com/fnsc/laravel-google-calendar/actions?query=workflow%3ATests"><img src="https://github.com/fnsc/laravel-google-calendar/workflows/Tests/badge.svg" alt="Tests Status"></a>
    <a href="https://app.codacy.com/gh/fnsc/laravel-google-calendar/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade"><img src="https://app.codacy.com/project/badge/Grade/af692cca78d0491583ce1cc3ea40f443"/></a>
</p>


- [Introduction](#introduction)
- [Requirements](#requirements) 
- [Installation](#installation)
- [Usage Guide](#guide)
- [License](#license)

## Introduction
This library provides a simple and easy way to deal with [Google Drive](https://drive.google.com) files.

## Requirements
- PHP >= 8.2^
- Laravel >= 10.*

## Installation
You can install the library via Composer:
```bash
composer require fnsc/laravel-google-calendar
```

## Guide
First, add this file `LaravelGoogleDrive\ServiceProvider::class` to your `config/app.php` file.

[//]: # (<p align="center"><img src="./docs/img/config_app.png" alt="app.php"/></p>)

Then publish the `google_drive.php` config file using the following command. That will add `google_drive.php` config file into you `config` directory. 
```bash
php artisan vendor:publish --provider="LaravelGoogleCalendar\ServiceProvider"
```

[//]: # (<p align="center"><img src="./docs/img/google_drive.png" alt="config dir"/></p>)

Now go to [Google Cloud Platform](https://console.cloud.google.com) and create a service account using this [link](https://console.cloud.google.com/apis/credentials) and click on Service Account.
<p align="center"><img src="./docs/img/service_account/step_1.png" alt="step 1"/></p>

When you finish, the Google Service Manager will generate a .json file. That file contains your credentials. Download it and keep it safe. 

Add this file to your project, and **DO NOT ADD THIS FILE TO YOUR GIT REPOSITORY**.

Now add the following `env_vars` into your `.env` file. The `GOOGLE_APPLICATION_CREDENTIALS` is the path to your `service-account.json` file, and the `GOOGLE_DRIVE_FOLDER_ID` is your directory on Google Drive.
<p align="center"><img src="./docs/img/service_account/.env.png" alt=".env file"/></p>

Now you must share the Google Drive directory with the `client_email` present in your `service-account.json` file, granting privileges to read and write.

Finally, you can follow the [examples](./examples/web.php). 

## License
This package is free software distributed under the terms of the [MIT license](http://opensource.org/licenses/MIT)
