<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "p2m_user".
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $status
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $access_token
 * @property string $logged_in_ip
 * @property string $logged_in_at
 * @property string $created_ip
 * @property string $created_at
 * @property string $updated_at
 * @property string $banned_at
 * @property string $banned_reason
 *
 * @property Profile $profile
 * @property Role $role
 * @property UserAuth[] $userAuths
 * @property UserToken[] $userTokens
 */
class User extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'p2m_user';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['role_id', 'status'], 'integer'],
			[['status'], 'required'],
			[['logged_in_at', 'created_at', 'updated_at', 'banned_at'], 'safe'],
			[['email', 'username', 'password', 'auth_key', 'access_token', 'logged_in_ip', 'created_ip', 'banned_reason'], 'string', 'max' => 255],
			[['email'], 'unique'],
			[['username'], 'unique'],
			[['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'role_id' => 'Role ID',
			'status' => 'Status',
			'email' => 'Email',
			'username' => 'Username',
			'password' => 'Password',
			'auth_key' => 'Auth Key',
			'access_token' => 'Access Token',
			'logged_in_ip' => 'Logged In Ip',
			'logged_in_at' => 'Logged In At',
			'created_ip' => 'Created Ip',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'banned_at' => 'Banned At',
			'banned_reason' => 'Banned Reason',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProfile()
	{
		return $this->hasOne(Profile::className(), ['user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRole()
	{
		return $this->hasOne(Role::className(), ['id' => 'role_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserAuths()
	{
		return $this->hasMany(UserAuth::className(), ['user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserTokens()
	{
		return $this->hasMany(UserToken::className(), ['user_id' => 'id']);
	}

	/**
	 * @inheritdoc
	 * @return UserQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new UserQuery(get_called_class());
	}
}
