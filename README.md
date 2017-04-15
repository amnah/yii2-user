# P2Y2 Users v0.1.6

## ¡¡¡ ===== NOT READY FOR USE ===== !!!

## Yii 2 Users & RBAC

## Installation

The preferred way to install P2Y2 Users is through
[composer](http://getcomposer.org/download/). Depending on your composer
installation, run *one* of the following commands:

```
	composer require p2made/yii2-p2y2-users "^0.1"
```

or

```
	php composer.phar require p2made/yii2-p2y2-users "^0.1"
```

Alternatively add:

```
	"p2made/yii2-p2y2-users": "^0.1"
```

to the requires section of your `composer.json` file & P2Y2 Users will be
installed next time you run `composer update`.



And then...
-----------

*	Configure your database. Edit
	`common/config/main-local.php` in yii2-advanced-app, or
	`app/config/db.php` in yii2-basic-app, to include...

	```
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=your_yii_app_db',
			'username' => 'your_yii_app_db_user',
			'password' => 'your_yii_app_db_password',
			'tablePrefix' => 'tbl_',
			'charset' => 'utf8',
		],
		...
	],
	```

	I recommend following the common practice of configuring the database with the username, `your_yii_app_db_user`, the same as the database name, `your_yii_app_db`.

*	Now edit `common/config/main.php` in yii2-advanced-app, or `app/config/web.php` in yii2-basic-app
to include...

	```
	'components' => [
		'user' => [
			'class' => 'p2m\users\components\User',
		],
		...
	],
	'modules' => [
		'user' => [
			'class' => 'p2m\users\modules\UsersModule',
		],
		...
	],
	```

	In `yii2-advanced-app` may have these configured at
	- `frontend/config/main.php` &
	- `backend/config/main.php`.
	However since these will all be the same for both ends,
	it's best to keep only one copy in `common/config`. If there are user configurations in either of those files, remove them or comment them out.

*	Now configure the mailer...
	- In yii2-advanced-app, either...
		+ `common/config/main.php` or
		+ `common/config/main-local.php`
	- In yii2-basic-app, `app/config/web.php`

	```
	'components' => [
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'useFileTransport' => true,
			'messageConfig' => [
				'from' => ['admin@website.com' => 'Admin'],
				'charset' => 'UTF-8',
			]
		],
	],
	```

*	Optionally perform any customisation in
	`common/config/params.php` in yii2-advanced-app, or
	`app/config/params.php` in yii2-basic-app, to include...

	```
	...
	'p2m' => [
		...
		'users' => [
			'requireEmail' => true,
			'requireUsername' => false,
			'useEmail' => true,
			'useUsername' => true,
			'loginEmail' => true,
			'loginUsername' => true,
			'loginDuration' => 2551443, // one mean lunar month
			'emailConfirmation' => true,
			'emailChangeConfirmation' => true,
			'loginRedirect' => null,
			'logoutRedirect' => null,
			'resetExpireTime' => '2 days',
			'loginExpireTime' => '15 minutes',
			'usersEmailViewPath' => '@user/mail',
			'usersForceTranslation' => false,
			'usersModelClasses' => [],
		],
	],
	```

	Settings above are the defaults & only need to be set if you want to change them.
	`p2p` is the params space for setting all of my Yii2 packages.

