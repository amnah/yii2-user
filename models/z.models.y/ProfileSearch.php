<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Profile;

/**
 * ProfileSearch represents the model behind the search form about `common\models\Profile`.
 */
class ProfileSearch extends Profile
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'user_id', 'created_by', 'updated_by'], 'integer'],
			[['givenName', 'familyName', 'preferredName', 'fullName', 'phone1', 'phone2', 'address1', 'address2', 'locality', 'state', 'postcode', 'country', 'timezone', 'created_at', 'updated_at'], 'safe'],
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
		$query = Profile::find();

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
			'user_id' => $this->user_id,
			'created_at' => $this->created_at,
			'created_by' => $this->created_by,
			'updated_at' => $this->updated_at,
			'updated_by' => $this->updated_by,
		]);

		$query->andFilterWhere(['like', 'givenName', $this->givenName])
			->andFilterWhere(['like', 'familyName', $this->familyName])
			->andFilterWhere(['like', 'preferredName', $this->preferredName])
			->andFilterWhere(['like', 'fullName', $this->fullName])
			->andFilterWhere(['like', 'phone1', $this->phone1])
			->andFilterWhere(['like', 'phone2', $this->phone2])
			->andFilterWhere(['like', 'address1', $this->address1])
			->andFilterWhere(['like', 'address2', $this->address2])
			->andFilterWhere(['like', 'locality', $this->locality])
			->andFilterWhere(['like', 'state', $this->state])
			->andFilterWhere(['like', 'postcode', $this->postcode])
			->andFilterWhere(['like', 'country', $this->country])
			->andFilterWhere(['like', 'timezone', $this->timezone]);

		return $dataProvider;
	}
}
