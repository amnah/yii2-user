<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Role]].
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
