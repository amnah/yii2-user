Yii 2 User
=========

Yii 2 User - User authentication module

## Notes
**2014/4/28** - I have just released 2.0.0-alpha. This release is basically a code overhaul
and does not contain any functionality changes.

**IF THIS BROKE YOUR APP**, the quick fix is to limit your version via composer:
```"amnah/yii2-user": "1.0.0@beta"```. Otherwise, please see the [Upgrade Notes](UPGRADE.md)
to sync your app with this latest version.

## Demo

* [Demo](http://yii2.amnahdev.com/user)

## Features

* Quick setup - works out of the box so you can see what it does
* Easily [extendable](#how-do-i-extend-this-package)
* Registration using email and/or username
* Login using email and/or username
* Email confirmation (+ resend functionality)
* Account page
    * Updates email, username, and password
    * Requires current password
* Profile page
    * Lists custom fields for users, e.g., *full_name*
* Password recovery
* Admin crud via GridView

## Installation

* Install [Yii 2](http://www.yiiframework.com/download) using your preferred method
* Install package via [composer](http://getcomposer.org/download/) ```"amnah/yii2-user": "~2.0"```
* Update config file *config/web.php* and *config/db.php*

```php
// app/config/web.php
return [
    'components' => [
        'user' => [
            'class' => 'amnah\yii2\user\components\User',
        ],
        'mail' => [
            // set up mail for emails
        ]
    ],
    'modules' => [
        'user' => [
            'class' => 'amnah\yii2\user\Module',
            // set custom module properties here ...
        ],
    ],
];
// app/config/db.php
return [
    'class' => 'yii\db\Connection',
    // set up db info
];
```

* Run migration file
    * ```php yii migrate --migrationPath=@vendor/amnah/yii2-user/migrations```
* Go to your application in your browser
    * ```http://localhost/pathtoapp/web/user```
* Log in as admin using ```neo/neo``` (change it!)
* Set up [module properties](PROPERTIES.md) as desired
* *Optional* - Update the nav links in your main layout *app/views/layouts/main.php*

```php
// app/views/layouts/main.php
<?php
'items' => [
    ['label' => 'Home', 'url' => ['/site/index']],
    ['label' => 'About', 'url' => ['/site/about']],
    ['label' => 'Contact', 'url' => ['/site/contact']],
    ['label' => 'User', 'url' => ['/user']],
    Yii::$app->user->isGuest ?
        ['label' => 'Login', 'url' => ['/user/login']] :
        ['label' => 'Logout (' . Yii::$app->user->displayName . ')',
            'url' => ['/user/logout'],
            'linkOptions' => ['data-method' => 'post']],
],
```

## Release Notes ([Upgrade Notes](UPGRADE.md))
* 2014/4/28 - Release 2.0.0-alpha
* 2014/4/17 - Release 1.0.0-beta

## Development Notes

### How do I check user permissions?

This package contains a custom permissions system. Every user has a role, and that role has permissions
in the form of database columns. It should follow the format: ```can_{permission name}```.

For example, the ```role``` table has a column named ```can_admin``` by default. To check if the user can
perform admin actions:

```php
if (!Yii::$app->user->can("admin")) {
    throw new HttpException(403, 'You are not allowed to perform this action.');
}
// --- or ----
$user = User::findOne(1);
if ($user->can("admin")) {
    // do something
};
```

Add more database columns for permissions as needed. If you need something more powerful, look into setting
up [RBAC] (https://github.com/yiisoft/yii2/blob/master/docs/guide/authorization.md).

**Note:** If you set up an ```authManager``` component for RBAC, then ```Yii::$app->user->can()``` will use
that instead of this module's custom ```role``` table.

### How do I extend this package?

You can extend the classes directly. Depending on which ones you need, set the proper config
property:

```php
// app/config/web.php
'components' => [
    'user' => [
        'class' => 'app\components\MyUser',
    ],
],
'modules' => [
    'user' => [
        'class' => 'app\modules\MyModule',
        'controllerMap' => [
            'default' => 'app\controllers\MyDefaultController',
        ],
        'modelClasses'  => [
            'Profile' => 'app\models\MyProfile',
        ],
        'emailViewPath' => '@app/mail/user', // example: @app/mail/user/confirmEmail.php
    ],
],
```

For view files, you can use the ```theme``` component.

```php
// app/config/web.php
'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@vendor/amnah/yii2-user/views' => '@app/views/user', // example: @app/views/user/default/login.php
            ],
        ],
    ],
],
```

### I need more control. Can I just extend the whole thing?

You can always fork the package and modify it as needed.

Or, if you want, you can integrate the package directly into your app by copying the files. This would
make it more difficult to get updates, but it also guarantees that your app won't break after running
```composer update```.

To do so, you can use the helper command ```CopyController```.

* Add the module to your *config/console.php* to gain access to the command (**Note: this is CONSOLE config**)

```php
// app/config/console.php
'modules' => [
    'user' => [
        'class' => 'amnah\yii2\user\Module',
    ],
],
```

* Use the ```php yii user/copy``` command. For a [basic app]
(https://github.com/yiisoft/yii2-app-basic), you can call the default command without any options

```
php yii user/copy --from=@vendor/amnah/yii2-user --to=@app/modules/user --namespace=app\\modules\\user
```

* Update config to point to your new package

```php
// app/config/web.php + app/config/console.php
'modules' => [
    'user' => [
        'class' => 'app\modules\user\Module',
    ],
],
```

**Alternatively,** you can do this manually. Just copy/paste the files wherever you'd like and
change the namespaces in the files. Replace ```amnah\yii2\user``` with ```app\modules\user```.

## Todo
* il8n
* Tests
* Issues/requests? Submit a [github issue](https://github.com/amnah/yii2-user/issues)