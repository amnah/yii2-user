Yii2 User
=========

Yii2 User - User authentication module

**STILL IN DEVELOPMENT. EXPECT CHANGES AND B0RKS**

[DEMO coming soon](http://yii2user.amnahdev.com)

## Features

* Quick setup (works out of the box)
* Registration using email and/or username
* Login using email and/or username
* Email confirmation (+resend functionality)
* Account page
    * Updates email, username, and password
    * Requires current password
* Profile page
    * Adds custom fields for users, e.g., *full_name*
* Password recovery
* Admin crud via GridView

## Installation

* Install [Yii2](https://github.com/yiisoft/yii2/tree/master/apps/basic) using your preferred method
* Install package via [composer](http://getcomposer.org/download/)
    * Run ```php composer.phar require amnah/yii2-user "dev-master"```
    * OR add to composer.json require section ```"amnah/yii2-user": "dev-master"```
* Update config file *config/web.php*
    * **NOTE: You will also need to add the db component to *config/console.php* for the migration**

```php
  return [
      'components' => [
          'user' => [
              'class' => 'amnah\yii2\user\components\User',
          ],
          'db' => [
              'class' => '\yii\db\Connection',
              'dsn' => 'mysql:host=localhost;dbname=dbname',
              'username' => '',
              'password' => '',
              'charset' => 'utf8',
              'tablePrefix' => 'tbl_',
          ]
      ],
      'modules' => [
          'user' => [
              'class' => 'amnah\yii2\user\Module',
              ... params here ...
          ],
      ],
  ];
```
* Run migration file
    * ```php yii migrate --migrationPath=@vendor/amnah/yii2-user/amnah/yii2/user/migrations```
* Go to your application in your browser
    * ```http://localhost/pathtoapp/web/user```
* Log in as admin using ```neo/neo``` (change it!)
