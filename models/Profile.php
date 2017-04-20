<?php
/**
 * Profile.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\models\Profile
 */

namespace p2m\users\models;

use Yii;

/**
 * This is the model class for table "{{%profile}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $givenName
 * @property string $familyName
 * @property string $preferredName
 * @property string $fullName
 * @property string $phone1
 * @property string $phone2
 * @property string $address1
 * @property string $address2
 * @property string $locality
 * @property string $state
 * @property string $postcode
 * @property string $country
 * @property string $timezone
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
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
	public function init()
	{
		if (!$this->module) {
			$this->module = Yii::$app->getModule("user");
		}
	}

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
			[['user_id', 'givenName', 'locality'], 'required'],
			[['user_id', 'created_by', 'updated_by'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['givenName', 'familyName', 'preferredName', 'locality'], 'string', 'max' => 64],
			[['fullName', 'address1', 'address2', 'timezone'], 'string', 'max' => 255],
			[['phone1', 'phone2', 'state', 'postcode', 'country'], 'string', 'max' => 32],
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
			'givenName' => 'Given Name',
			'familyName' => 'Family Name',
			'preferredName' => 'Preferred Name',
			'full_name' => Yii::t('user', 'Full Name'),
			'phone1' => 'Phone1',
			'phone2' => 'Phone2',
			'address1' => 'Address1',
			'address2' => 'Address2',
			'locality' => 'Locality',
			'state' => 'State',
			'postcode' => 'Postcode',
			'country' => 'Country',
			'timezone' => Yii::t('user', 'Time zone'),
			'created_at' => Yii::t('user', 'Created At'),
			'created_by' => 'Created By',
			'updated_at' => Yii::t('user', 'Updated At'),
			'updated_by' => 'Updated By',
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
	 * @return ProfileQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new ProfileQuery(get_called_class());
	}
}
