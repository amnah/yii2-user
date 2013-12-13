Yii2 User
=========

Yii2 User - User authentication module

**STILL IN DEVELOPMENT. EXPECT CHANGES AND B0RKS**

## Demo

[See here](http://yii2user.amnahdev.com/user)

## Features

* Quick setup (works out of the box so you can see what it does)
* Registration using email and/or username
* Login using email and/or username
* Email confirmation (+resend functionality)
* Account page
    * Updates email, username, and password
    * Requires current password
* Profile page
    * Lists custom fields for users, e.g., *full_name*
* Password recovery
* Admin crud via GridView (with some examples to display custom data)

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
            // set up db info here
        ],
        'mail' => [
            // set up mail for email confirmation
        ]
    ],
    'modules' => [
        'user' => [
            'class' => 'amnah\yii2\user\Module',
            // set custom module properties here ...
        ],
    ],
];
```

* Run migration file
    * ```php yii migrate --migrationPath=@vendor/amnah/yii2-user/amnah/yii2/user/migrations```
* Go to your application in your browser
    * ```http://localhost/pathtoapp/web/user```
* Log in as admin using ```neo/neo``` (change it!)
* *Optional* - Update the nav links in your main layout *app/views/layouts/main.php*

```
<?php
'items' => [
    ['label' => 'Home', 'url' => ['/site/index']],
    ['label' => 'About', 'url' => ['/site/about']],
    ['label' => 'Contact', 'url' => ['/site/contact']],
    ['label' => 'User', 'url' => ['/user']],
    Yii::$app->user->isGuest ?
        ['label' => 'Login', 'url' => ['/user/login']] :
        ['label' => 'Logout (' . Yii::$app->user->displayName . ')' , 'url' => ['/user/logout']],
],
```


## Development

### How do I check user permissions?

This package contains a very simple permissions system. Every user has a role, and that role has permissions
in the form of database columns. It should follow the format: ```can_{permission name}```.

For example, the ```role``` table has a column named ```can_admin``` by default. To check if the user can
perform admin actions

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

Unfortunately you can't. The classes are all intertwined, so you have no choice but to either fork the
package or to copy the files somewhere and then modify them as desired. Until someone figures out a better
way to architect this, I've created a helper command to copy the files for you.

* Add the module to your *config/console.php* to gain access to the command **note: this is CONSOLE config**

```
'modules' => [
    'user' => [
        'class' => 'amnah\yii2\user\Module',
    ],
],
```

* Use the ```php yii user/copy``` command. For a [basic]
(https://github.com/yiisoft/yii2-app-basic) app, you can call the default command without any options

```
php yii user/copy --from=@vendor/amnah/yii2-user/amnah/yii2/user --to=@app/modules/user --namespace=app\\modules\\user
```

* Update *config/web.php* to point to your new package **note: this is WEB config**

```
'modules' => [
    'user' => [
        'class' => 'app\modules\user\Module',
        // ... params here ...
    ],
],
```

**Alternatively,** you can do this manually. Just copy/paste the files wherever you'd like,
but you'll need to change the namespaces in the files. Replace ```amnah\yii2\user``` with ```your\namespace```

### Todo
* Userkey expiration functionality
* Convert permissions to RBAC ???
* Add functionality for user groups (possibly as another package)
* Have a request? Submit an [issue](https://github.com/amnah/yii2-user/issues)