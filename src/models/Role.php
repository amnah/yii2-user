<?php

namespace faro\core\user\models;

use faro\core\models\FaroBaseActiveRecord;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "tbl_role".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $can_admin
 *
 * @property User[] $users
 */
class Role extends FaroBaseActiveRecord
{
    /**
     * @var int Admin user role
     */
    const ROLE_ADMIN = 1;

    /**
     * @var int Default user role
     */
    const ROLE_USER = 2;

    /**
     * @var \faro\core\user\Module
     */
    public $module;

    public static function tableName()
    {
        return '{{%core_acl_rol}}';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->module) {
            $this->module = Yii::$app->getModule("user");
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['can_admin'], 'integer'],
        ];

        // add can_ rules
        foreach ($this->attributes() as $attribute) {
            if (strpos($attribute, 'can_') === 0) {
                $rules[] = [[$attribute], 'integer'];
            }
        }

        return $rules;

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'name' => Yii::t('user', 'Name'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_at' => Yii::t('user', 'Updated At'),
            'can_admin' => Yii::t('user', 'Can Admin'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function ($event) {
                    return gmdate("Y-m-d H:i:s");
                },
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        $user = $this->module->model("User");
        return $this->hasMany($user::className(), ['role_id' => 'id']);
    }

    /**
     * Check permission
     * @param string $permission
     * @return bool
     */
    public function checkPermission($permission)
    {
        $roleAttribute = "can_{$permission}";
        return isset($this->$roleAttribute) ? (bool)$this->$roleAttribute : false;
    }

    /**
     * Get list of roles for creating dropdowns
     * @return array
     */
    public static function dropdown()
    {
        // get all records from database and generate
        static $dropdown;
        if ($dropdown === null) {
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->name;
            }
        }
        return $dropdown;
    }
}