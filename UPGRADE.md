Yii 2 User - Upgrade notes
=========

## Upgrading from 4.0.0 to 5.0.0

This release contains a few small updates and bug fixes. Most notably, I've changed
`LoginForm.username` to `LoginForm.email` and added a timezone field to `app\models\Profile`
(thanks [mnglkhn](https://github.com/amnah/yii2-user/pull/127))

To upgrade to this version:

* Change all references of `LoginForm.username` to `LoginForm.email`. This would be in 
the [model](models/forms/LoginForm.php) and [view](views/default/login.php) files that 
you extended

* *Optional* - Add a `timezone` column to profile table in the database

* *Optional* - Add [validation rules / attribute label](models/Profile.php#L29-L59) 
(lines 29 and 59) for `timezone` field in Profile model 

## Upgrading from 3.0.0 to 4.0.0

This release is a major code overhaul. Lots of refactoring, cleaning up, updating comments,
etc. Aside from the migration, the other changes apply to any code that you've extended 
from the module.

(Note: This list may not be entirely comprehensive ... If I forgot something, please 
[let me know](https://github.com/amnah/yii2-user/issues))

* **Back up your database**

* Download migration and run it

```php
cd /path/to/app/migrations
wget https://gist.githubusercontent.com/amnah/384e607f5e2d3cbfbc72/raw/f420380ea0e0ca4d73e7fc1ab2bba11377913868/m151111_122541_upgrade_user_300_to_400.php
cd /path/to/app
php yii migrate
```

* Change table references ```UserKey``` to ```UserToken```

* Change field references ```UserToken.key_value``` to ```UserToken.token```
 
* Change ```$key``` parameters to ```$token``` in DefaultController functions **AND views**
(eg, ```actionConfirm($key)``` to ```actionConfirm($token)``` and ```actionReset($key)```
to ```actionReset($token)```)

* Change references ```User.new_email``` to ```UserToken.data```

## Upgrading from ~2.0 to 3.0.0

This release just has some minor updates, but unfortunately contains backwards-compatibility
breaking changes.

To update to this version:

* Change table column ```tbl_user_key.key``` to ```tbl_user_key.key_value``` in your sql database

## Upgrading from 1.0.0-beta to 2.0.0-alpha

This release is basically a code overhaul and does not contain any functionality changes.
In short, I've updated the code to fit PSR-2 standards and re-created the models/crud via
gii (to incorporate the many changes since then).

To sync your app with this version:

**(Major changes)**

* Change table name ```tbl_userkey``` to ```tbl_user_key``` in your sql database

* If you extended the DefaultController and/or email view files, change variable
names ```$userkey``` to ```$userKey``` (notice the capitalization of the letter "K")

* If you extended the view files, remove ```viewPath``` from module configuration
and use ```components => view => theme``` instead

```php
// @app/config/web.php
'modules' => [
    'user' => [
        'viewPath' => '@app/views/user', // REMOVE THIS
    ],
],
'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [ // SET THIS INSTEAD
                '@vendor/amnah/yii2-user/views' => '@app/themes/user', // example: @app/themes/user/default/profile.php
            ],
        ],
    ],
],
```

* If you overrode ```DefaultController::actionReset()```,
```models\forms\ResetForm```, or the ```default/reset.php``` view file,
you will need to change the references of ```$model``` to ```$user```. This is
because the ResetForm model has been removed, and the reset functionality now
uses the User model instead (with scenario "reset").

**(Minor changes - you most likely won't need to do these)**

* If you used the guest role, re-add it back into the Role model
```const ROLE_GUEST = 3```

* If you overrode ```models\User::setLoginIpAndTime()```, change the name
to ```models\User::updateLoginMeta()```

* If you overrode ```Module::_checkEmailUsername()```, change the name
to ```Module::checkModuleProperties()```

* If you overrode ```Module::_getDefaultModelClasses()```, change the name
to ```Module::getDefaultModelClasses()``` *(no underscore)*

* If you overrode ```DefaultController::_calcEmailOrLogin()```, change the name
to ```DefaultController::afterRegister()```

* If you overrode ```AdminController::actionIndex()```,
```models\search\UserSearch```, or the ```admin/index.php``` view file,
you will need to check to see if the $profile->full_name works properly. Typically,
you would need to change this in the view file:

```php
// views/admin/index.php
// change this
[
    'attribute' => 'full_name',
    'label' => 'Full Name',
    'value' => function($model, $index, $dataColumn) {
        return $model->profile->full_name;
    }
],
// to this
'profile.full_name',
```