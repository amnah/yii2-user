<?php
/**
 * RoleQuery.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @package p2made/yii2-p2y2-users
 * @license MIT
 */

namespace p2m\users\models;

/**
 * This is the ActiveQuery class for table "{{%role}}".
 *
 * class p2m\users\models\RoleQuery
 *
 * @see Role
 */
class RoleQuery extends \yii\db\ActiveQuery
{
	/*public function active()
	{
		return $this->andWhere('[[status]]=1');
	}*/

	/**
	 * @inheritdoc
	 * @return Role[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return Role|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
?>


