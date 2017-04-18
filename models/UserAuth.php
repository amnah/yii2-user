<?php
/**
 * UserAuth.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\models\UserAuth
 */

namespace p2m\users\models;

use Yii;

/**
 * This is the model class for table "{{%user_auth}}".
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
		return '{{%user_auth}}';
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
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'value' => function ($event) {
					return gmdate("Y-m-d H:i:s");
				},
			],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		$user = $this->module->model("User");
		return $this->hasOne($user::className(), ['id' => 'user_id']);
	}

	/**
	 * Set user id
	 * @param int $userId
	 * @return static
	 */
	public function setUser($userId)
	{
		$this->user_id = $userId;
		return $this;
	}

	/**
	 * Set provider attributes
	 * @param array $attributes
	 * @return static
	 */
	public function setProviderAttributes($attributes)
	{
		$this->provider_attributes = json_encode($attributes);
		return $this;
	}

	/**
	 * Get provider attributes
	 * @return array
	 */
	public function getProviderAttributes()
	{
		return json_decode($this->provider_attributes, true);
	}
}
?>


<?php // Yii generated

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
?>


<?php // amnah

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_user_auth".
 *
 * @property string $id
 * @property string $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $provider_attributes
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserAuth extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('user', 'ID'),
			'user_id' => Yii::t('user', 'User ID'),
			'provider' => Yii::t('user', 'Provider'),
			'provider_id' => Yii::t('user', 'Provider ID'),
			'provider_attributes' => Yii::t('user', 'Provider Attributes'),
			'created_at' => Yii::t('user', 'Created At'),
			'updated_at' => Yii::t('user', 'Updated At'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'value' => function ($event) {
					return gmdate("Y-m-d H:i:s");
				},
			],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * Set user id
	 * @param int $userId
	 * @return static
	 */
	public function setUser($userId)
	{
		$this->user_id = $userId;
		return $this;
	}

	/**
	 * Set provider attributes
	 * @param array $attributes
	 * @return static
	 */
	public function setProviderAttributes($attributes)
	{
		$this->provider_attributes = json_encode($attributes);
		return $this;
	}

	/**
	 * Get provider attributes
	 * @return array
	 */
	public function getProviderAttributes()
	{
		return json_decode($this->provider_attributes, true);
	}
}
?>


