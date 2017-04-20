<?php
/**
 * UserQuery.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\models\UserQuery
 */

namespace p2m\users\models;

/**
 * This is the ActiveQuery class for table "{{%user}}".
 *
 * @see User
 */
class UserQuery extends \yii\db\ActiveQuery
{
	/*public function active()
	{
		return $this->andWhere('[[status]]=1');
	}*/

	/**
	 * @inheritdoc
	 * @return User[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return User|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
