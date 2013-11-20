<?php

namespace amnah\yii2\user;

use Yii;

class Module extends \yii\base\Module {
    /**
     * Controller namespace
     * @var string
     */
    public $controllerNamespace = 'amnah\yii2\user\controllers';

    /**
     * Params
     * @var array
     */
    public $params = [
        "useEmail" => true,
        "useUsername" => true,
        "requireEmail" => false,
        "requireUsername" => true,
        "loginEmail" => true,
        "loginUsername" => true,
        "emailConfirmation" => true,
    ];

    /**
     * Init
     */
    public function init() {
		parent::init();

		// custom initialization code goes here

        // change user component
        Yii::$app->setComponent("user", null);
        Yii::$app->setComponent("user", [
            'class' => 'yii\web\User',
            'identityClass' => 'amnah\yii2\user\models\User',
            'enableAutoLogin' => true,
        ]);
	}

    /**
     * Modify to handle routes in the default controller
     * This is a temporary hack until they add in url management via modules
     * @link https://github.com/yiisoft/yii2/issues/810
     * @link http://www.yiiframework.com/forum/index.php/topic/21884-module-and-url-management/
     */
    public function createController($route) {

        /**
         * handle routes
         * "user" and "user/default" work like normal
         * "user/xxx" gets changed to "user/default/xxx"
         */
        return (empty($route) or $route == $this->defaultRoute)
            ? parent::createController($route)
            : parent::createController("{$this->defaultRoute}/{$route}");
    }
}
