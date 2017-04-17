<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "p2m_user_auth".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $provider_attributes
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserAuth extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'p2m_user_auth';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'provider', 'provider_id', 'provider_attributes'], 'required'],
			[['user_id'], 'integer'],
			[['provider_attributes'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['provider', 'provider_id'], 'string', 'max' => 255],
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
			'provider' => 'Provider',
			'provider_id' => 'Provider ID',
			'provider_attributes' => 'Provider Attributes',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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
