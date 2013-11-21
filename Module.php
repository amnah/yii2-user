<?php

namespace amnah\yii2\user;

use Yii;

/**
 * User module
 *
 * @author amnah <amnah.dev@gmail.com>
 */
class Module extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = "amnah\yii2\user\controllers";

    /*
     * @var bool flag for whether or not to use user.email. if set to false, users will have [null] emails
     */
    public $useEmail = true;

    /**
     * @var bool flag for whether or not to use user.username. if set to false, users will have [null] usernames
     */
    public $useUsername = true;

    /**
     * @var bool flag for whether or not to require user.email
     */
    public $requireEmail = false;

    /**
     * @var bool flag for whether or not to require user.username
     */
    public $requireUsername = true;

    /**
     * @var bool flag for whether or not users can login using their email. both email and username can be allowed
     */
    public $loginEmail = true;

    /**
     * @var bool flag for whether or not users can login using their username. both email and username can be allowed
     */
    public $loginUsername = true;

    /**
     * @var bool flag for whether or not users have to confirm their email addresses (registering or updating)
     */
    public $emailConfirmation = true;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // changes user component
        Yii::$app->setComponent("user", null);
        Yii::$app->setComponent("user", [
            "class" => "amnah\yii2\components\User",
        ]);
    }

    /**
     * Modifies function to handle routes in the default controller
     *
     * This is a temporary hack until they add in url management via modules
     * @link https://github.com/yiisoft/yii2/issues/810
     * @link http://www.yiiframework.com/forum/index.php/topic/21884-module-and-url-management/
     *
     * @inheritdoc
     */
    public function createController($route) {

        /**
         * handles routes
         * "user" and "user/default" work like normal
         * "user/xxx" gets changed to "user/default/xxx"
         */
        return (empty($route) or $route == $this->defaultRoute)
            ? parent::createController($route)
            : parent::createController("{$this->defaultRoute}/{$route}");
    }
}
