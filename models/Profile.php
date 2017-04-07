<?php
/**
 * Profile.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @package p2made/yii2-p2y2-users
 * @license MIT
 */

namespace p2m\users\models;

use Yii;

/**
 * This is the model class for table "{{%profile}}".
 *
 * class p2m\users\models\Profile
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $given_name
 * @property string $family_name
 * @property string $preferred_name
 * @property string $timezone
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{
	/**
	 * @var \p2m\users\modules\UsersModule
	 */
	public $module;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%profile}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id'], 'integer', 'required'],
			[['created_at', 'updated_at'], 'safe'],
			[['given_name', 'family_name', 'preferred_name'], 'string', 'max' => 255],
			[['timezone'], 'string', 'max' => 32],
			[['user_id'], 'unique'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
			'id' => Yii::t('user', 'ID'),
			'user_id' => Yii::t('user', 'User ID'),
			'given_name' => 'Given Name',
			'family_name' => 'Family Name',
			'preferred_name' => 'Preferred Name',
			'timezone' => Yii::t('user', 'Time zone'),
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
	 * @inheritdoc
	 */
	public function init()
	{
		if (!$this->module) {
			$this->module = Yii::$app->getModule("user");
		}
	}
}
