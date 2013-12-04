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
* Admin crud via GridView (coming soon)

## Installation

1. Install [Yii2](https://github.com/yiisoft/yii2/tree/master/apps/basic) using your preferred method
2. Install package via [composer](http://getcomposer.org/download/)
    * Run ```php composer.phar require amnah/yii2-user "dev-master"```
    * OR add to composer.json require section ```"amnah/yii2-user": "dev-master"```
3. Update config file *config/web.php*

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

4. Run migration file
    * ```php yii migrate --migrationPath=@vendor/amnah/yii2-user/amnah/yii2/user/migrations```
5. Go to your application in your browser
    * ```http://localhost/pathtoapp/web/user```
6. Log in as admin using ```neo/neo``` (change it!)
