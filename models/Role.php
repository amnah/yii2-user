<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_role".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $create_time
 * @property string  $update_time
 * @property integer $can_admin
 *
 * @property User[]  $users
 */
class Role extends ActiveRecord
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return static::getDb()->tablePrefix . "role";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
//            [['can_admin'], 'integer'],
//            [['create_time', 'update_time'], 'safe'],
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
            'id'          => Yii::t('user', 'ID'),
            'name'        => Yii::t('user', 'Name'),
            'create_time' => Yii::t('user', 'Create Time'),
            'update_time' => Yii::t('user', 'Update Time'),
            'can_admin'   => Yii::t('user', 'Can Admin'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'value'      => function () { return date("Y-m-d H:i:s"); },
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        $user = Yii::$app->getModule("user")->model("User");
        return $this->hasMany($user::className(), ['role_id' => 'id']);
    }

    /**
     * Check permission
     *
     * @param string $permission
     * @return bool
     */
    public function checkPermission($permission)
    {
        $roleAttribute = "can_{$permission}";
        return $this->$roleAttribute ? true : false;
    }

    /**
     * Get list of roles for creating dropdowns
     *
     * @return array
     */
    public static function dropdown()
    {
        // get and cache data
        static $dropdown;
        if ($dropdown === null) {

            // get all records from database and generate
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->name;
            }
        }

        return $dropdown;
    }
}