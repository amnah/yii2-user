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

* Install [Yii2](http://www.yiiframework.com/download) using your preferred method
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
              // ... params here ...
          ],
      ],
  ];
```

* Run migration file
    * ```php yii migrate --migrationPath=@vendor/amnah/yii2-user/amnah/yii2/user/migrations```
* Go to your application in your browser
    * ```http://localhost/pathtoapp/web/user```
* Log in as admin using ```neo/neo``` (change it!)

## FAQs

### How do I check user permissions?

This package contains a very simple permissions system. Every user has a role, and that role has permissions
in the form of database columns. It should follow the format: ```can_{permission name}```.

For example, the ```role``` table has a column named ```can_admin``` by default. To check if the user can
perform admin actions:

```
if (!Yii::$app->user->can("admin")) {
    throw new HttpException(404, 'The requested page does not exist.');
}
```

Add more database columns for permissions as needed. If you need something more powerful, look into setting
up [RBAC] (https://github.com/yiisoft/yii2/blob/master/docs/guide/authorization.md).

**Note: I may decide to switch out my current permissions implementation to a basic RBAC implementation so
developers will have a better base to start with ...**

### How can I extend this package?

Unfortunately you can't. The classes are all intertwined, so you have no choice but to copy the
files somewhere and then modify them as desired. Until someone figures out a better way to architect
this, I've created a helper command to copy the files for you.

```
php yii user/copy [from] [to] [namespace]
```

For a [basic](https://github.com/yiisoft/yii2-app-basic) app, you can call the default command:

```
php yii user/copy
   (which will automatically fill in the defaults below)
php yii user/copy @vendor/amnah/yii2-user/amnah/yii2/user @app/modules/user app\\modules\\user
```

After that, you'll need to update your config:

```
'modules' => [
    'user' => [
        'class' => 'app\modules\user\Module',
        // ... params here ...
    ],
],
```

### Todo
* Convert permissions to RBAC ???
* Add functionality for user groups (possibly as another package)
* Have a request? Submit an [issue](https://github.com/amnah/yii2-user/issues)