<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "p2m_role".
 *
 * @property integer $id
 * @property string $name
 * @property integer $can_admin
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property User[] $users
 */
class Role extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'p2m_role';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['can_admin', 'created_by', 'updated_by'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['name'], 'string', 'max' => 32],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Name',
			'can_admin' => 'Can Admin',
			'created_at' => 'Created At',
			'created_by' => 'Created By',
			'updated_at' => 'Updated At',
			'updated_by' => 'Updated By',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUsers()
	{
		return $this->hasMany(User::className(), ['role_id' => 'id']);
	}

	/**
	 * @inheritdoc
	 * @return RoleQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new RoleQuery(get_called_class());
	}
}
