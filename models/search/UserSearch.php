<?php

namespace amnah\yii2\user\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use amnah\yii2\user\models\User;

/**
 * UserSearch represents the model behind the search form about `amnah\yii2\user\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%user}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'role_id', 'status'], 'integer'],
            [['email', 'username', 'password', 'auth_key', 'access_token', 'logged_in_ip', 'logged_in_at', 'created_ip', 'created_at', 'updated_at', 'banned_at', 'banned_reason', 'profile.full_name'], 'safe'],
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
     * @inheritdoc
     */
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['profile.full_name']);
    }

    /**
     * Search
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\Profile $profile */

        // get models
        $user = $this->module->model("User");
        $profile = $this->module->model("Profile");
        $userTable = $user::tableName();
        $profileTable = $profile::tableName();

        // set up query relation for `user`.`profile`
        // http://www.yiiframework.com/doc-2.0/guide-output-data-widgets.html#working-with-model-relations
        $query = $user::find();
        $query->joinWith(['profile' => function ($query) use ($profileTable) {
            $query->from(['profile' => $profileTable]);
        }]);

        // create data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // enable sorting for the related columns
        $addSortAttributes = ["profile.full_name"];
        foreach ($addSortAttributes as $addSortAttribute) {
            $dataProvider->sort->attributes[$addSortAttribute] = [
                'asc' => [$addSortAttribute => SORT_ASC],
                'desc' => [$addSortAttribute => SORT_DESC],
            ];
        }

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            "{$userTable}.id" => $this->id,
            'role_id' => $this->role_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'logged_in_ip', $this->logged_in_ip])
            ->andFilterWhere(['like', 'created_ip', $this->created_ip])
            ->andFilterWhere(['like', 'banned_reason', $this->banned_reason])
            ->andFilterWhere(['like', 'logged_in_at', $this->logged_in_at])
            ->andFilterWhere(['like', "{$userTable}.created_at", $this->created_at])
            ->andFilterWhere(['like', "{$userTable}.updated_at", $this->updated_at])
            ->andFilterWhere(['like', 'banned_at', $this->banned_at])
            ->andFilterWhere(['like', "profile.full_name", $this->getAttribute('profile.full_name')]);

        return $dataProvider;
    }
}