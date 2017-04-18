<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'role_id', 'status'], 'integer'],
			[['email', 'username', 'password', 'auth_key', 'access_token', 'logged_in_ip', 'logged_in_at', 'created_ip', 'created_at', 'updated_at', 'banned_at', 'banned_reason'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = User::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'role_id' => $this->role_id,
			'status' => $this->status,
			'logged_in_at' => $this->logged_in_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'banned_at' => $this->banned_at,
		]);

		$query->andFilterWhere(['like', 'email', $this->email])
			->andFilterWhere(['like', 'username', $this->username])
			->andFilterWhere(['like', 'password', $this->password])
			->andFilterWhere(['like', 'auth_key', $this->auth_key])
			->andFilterWhere(['like', 'access_token', $this->access_token])
			->andFilterWhere(['like', 'logged_in_ip', $this->logged_in_ip])
			->andFilterWhere(['like', 'created_ip', $this->created_ip])
			->andFilterWhere(['like', 'banned_reason', $this->banned_reason]);

		return $dataProvider;
	}
}
