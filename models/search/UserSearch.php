<?php

namespace amnah\yii2\user\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use amnah\yii2\user\models\User;

/**
 * UserSearch represents the model behind the search form about `amnah\yii2\user\models\User`.
 */
class UserSearch extends User {

    /**
     * @var string Full name from profile
     */
    public $full_name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'role_id', 'status'], 'integer'],
            [['email', 'new_email', 'username', 'password', 'auth_key', 'api_key', 'login_ip', 'login_time', 'create_ip', 'create_time', 'update_time', 'ban_time', 'ban_reason', 'full_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Search
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params) {

        // get models
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\Profile $profile */
        $user = Yii::$app->getModule("user")->model("User");
        $profile = Yii::$app->getModule("user")->model("Profile");
        $userTable = $user::tableName();
        $profileTable = $profile::tableName();

        // set up query with innerJoin on profile data for search/filter
        // note: we call 'with("profile")' to eager-load data for displaying grid
        $query = $user::find();
        $query->innerJoin($profileTable, "$userTable.id=$profileTable.user_id");
        $query->with("profile");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // add extra sort attributes
        $addSortAttributes = ["full_name"];
        foreach ($addSortAttributes as $addSortAttribute) {
            $dataProvider->sort->attributes[$addSortAttribute] = [
                'asc' => [$addSortAttribute => SORT_ASC],
                'desc' => [$addSortAttribute => SORT_DESC],
                'label' => $this->getAttributeLabel($addSortAttribute),
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
            ->andFilterWhere(['like', 'new_email', $this->new_email])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'login_ip', $this->login_ip])
            ->andFilterWhere(['like', 'create_ip', $this->create_ip])
            ->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
            ->andFilterWhere(['like', 'login_time', $this->login_time])
            ->andFilterWhere(['like', "{$userTable}.create_time", $this->create_time])
            ->andFilterWhere(['like', "{$userTable}.update_time", $this->update_time])
            ->andFilterWhere(['like', 'ban_time', $this->ban_time]);

        return $dataProvider;
    }
}