<?php

namespace amnah\yii2\user;

use Yii;
use yii\base\InvalidConfigException;

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

    /**
     * @var string Alias for module
     */
    public $alias = "@user";

    /**
     * @var bool If true, users will have to confirm their email address after registering or updating their profile.
     *           This is the same as email activation
     */
    public $emailConfirmation = true;

    /**
     * @var bool If true, users are required to enter an email
     */
    public $requireEmail = true;

    /**
     * @var bool If true, users are required to enter a username
     */
    public $requireUsername = false;

    /*
     * @var bool If true, users can enter an email. This is automatically set to true if $requireEmail = true
     */
    public $useEmail = true;

    /**
     * @var bool If true, users can enter a username. This is automatically set to true if $requireUsername = true
     */
    public $useUsername = true;

    /**
     * @var bool If true, users can log in by entering their email
     */
    public $loginEmail = true;

    /**
     * @var bool If true, users can log in by entering their username
     */
    public $loginUsername = true;

    /**
     * @var int Login duration
     */
    public $loginDuration = 2592000;

    /**
     * @var string Email view path
     */
    public $emailViewPath = "@user/views/_email";

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // set alias
        $this->setAliases([
            $this->alias => __DIR__,
        ]);

        // set use fields based on required fields
        if ($this->requireEmail) {
            $this->useEmail = true;
        }
        if ($this->requireUsername) {
            $this->useUsername = true;
        }

        // get class name for error messages
        $className = get_called_class();

        // check required fields
        if (!$this->requireEmail and !$this->requireUsername) {
            throw new InvalidConfigException("{$className}: \$requireEmail and/or \$requireUsername must be true");
        }
        // check login fields
        if (!$this->loginEmail and !$this->loginUsername) {
            throw new InvalidConfigException("{$className}: \$loginEmail and/or \$loginUsername must be true");
        }
        // check email fields if emailConfirmation is true
        if ($this->emailConfirmation and !$this->useEmail) {
            throw new InvalidConfigException("{$className}: \$useEmail must be true if \$emailConfirmation is true");
        }
    }

    /**
     * Modify createController() to handle routes in the default controller
     *
     * This is a temporary hack until they add in url management via modules
     * @link https://github.com/yiisoft/yii2/issues/810
     * @link http://www.yiiframework.com/forum/index.php/topic/21884-module-and-url-management/
     *
     * @inheritdoc
     */
    public function createController($route) {

        /**
         * handle routes
         *
         * "user" and "user/default" work like normal
         * "user/xxx" gets changed to "user/default/xxx"
         */
        return (empty($route) or $route == $this->defaultRoute or strpos($route, "admin") === 0)
            ? parent::createController($route)
            : parent::createController("{$this->defaultRoute}/{$route}");
    }
}
