<?php
/**
 * ProfileSearch.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @package p2made/yii2-p2y2-users
 * @license MIT
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProfileSearch represents the model behind the search form about `common\models\Profile`.
 *
 * class p2m\users\models\ProfileSearch
 *
 * @see Profile
 */
class ProfileSearch extends \common\models\Profile
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'user_id'], 'integer'],
			[['given_name', 'family_name', 'preferred_name', 'timezone', 'created_at', 'updated_at'], 'safe'],
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
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'given_name', $this->given_name])
			->andFilterWhere(['like', 'family_name', $this->family_name])
			->andFilterWhere(['like', 'preferred_name', $this->preferred_name])
			->andFilterWhere(['like', 'timezone', $this->timezone]);

		return $dataProvider;
	}
}
