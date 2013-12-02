<?php

namespace amnah\yii2\user\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use amnah\yii2\user\models\User;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
	public $id;
	public $role_id;
	public $email;
	public $new_email;
	public $username;
	public $password;
	public $status;
	public $auth_key;
	public $create_time;
	public $update_time;
	public $ban_time;
	public $ban_reason;

	public function rules()
	{
		return [
			[['id', 'role_id', 'status'], 'integer'],
			[['email', 'new_email', 'username', 'password', 'auth_key', 'create_time', 'update_time', 'ban_time', 'ban_reason'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'role_id' => 'Role ID',
			'email' => 'Email',
			'new_email' => 'New Email',
			'username' => 'Username',
			'password' => 'Password',
			'status' => 'Status',
			'auth_key' => 'Auth Key',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'ban_time' => 'Ban Time',
			'ban_reason' => 'Ban Reason',
		];
	}

	public function search($params)
	{
		$query = User::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'id');
		$this->addCondition($query, 'role_id');
		$this->addCondition($query, 'email', true);
		$this->addCondition($query, 'new_email', true);
		$this->addCondition($query, 'username', true);
		$this->addCondition($query, 'password', true);
		$this->addCondition($query, 'status');
		$this->addCondition($query, 'auth_key', true);
		$this->addCondition($query, 'create_time', true);
		$this->addCondition($query, 'update_time', true);
		$this->addCondition($query, 'ban_time', true);
		$this->addCondition($query, 'ban_reason', true);
		return $dataProvider;
	}

	protected function addCondition($query, $attribute, $partialMatch = false)
	{
		$value = $this->$attribute;
		if (trim($value) === '') {
			return;
		}
		if ($partialMatch) {
			$value = '%' . strtr($value, ['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']) . '%';
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}
