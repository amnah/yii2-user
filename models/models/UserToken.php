<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "p2m_user_token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $token
 * @property string $data
 * @property string $created_at
 * @property string $expired_at
 *
 * @property User $user
 */
class UserToken extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'p2m_user_token';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'type'], 'integer'],
			[['type', 'token'], 'required'],
			[['created_at', 'expired_at'], 'safe'],
			[['token', 'data'], 'string', 'max' => 255],
			[['token'], 'unique'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_id' => 'User ID',
			'type' => 'Type',
			'token' => 'Token',
			'data' => 'Data',
			'created_at' => 'Created At',
			'expired_at' => 'Expired At',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
