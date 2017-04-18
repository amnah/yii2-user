<?php
/**
 * ProfileQuery.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\models\ProfileQuery
 */

namespace p2m\users\models;

/**
 * This is the ActiveQuery class for table "{{%profile}}".
 *
 * @see Profile
 */
class ProfileQuery extends \yii\db\ActiveQuery
{
	/*public function active()
	{
		return $this->andWhere('[[status]]=1');
	}*/

	/**
	 * @inheritdoc
	 * @return Profile[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return Profile|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
?>


<?php // Yii generated

namespace common\models;

/**
 * This is the ActiveQuery class for [[Profile]].
 *
 * @see Profile
 */
class ProfileQuery extends \yii\db\ActiveQuery
{
	/*public function active()
	{
		return $this->andWhere('[[status]]=1');
	}*/

	/**
	 * @inheritdoc
	 * @return Profile[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return Profile|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
