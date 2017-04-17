<?php
/**
 * index.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 */

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var p2m\users\modules\UsersModule $module
 * @var array $actions
 */

$module = $this->context->module;

$this->title = Yii::t('user', 'Yii 2 User');
?>
<div class="user-default-index">

	<h1>Yii 2 User Module - <?= $module->getVersion(); ?></h1>
	<h3>Actions in this module</h3>

	<p>
		<em><strong>Note:</strong> Some actions may be unavailable depending on if you are logged in/out, or as an
			admin/regular user</em>
	</p>

	<table class="table table-bordered">
		<tr>
			<th>URL</th>
			<th>Description</th>
		</tr>

		<?php foreach ($actions as $url => $description): ?>

			<tr>
				<td>
					<strong><?= Html::a($url, [$url]) ?></strong>
				</td>
				<td>
					<?= $description ?>
				</td>
			</tr>

		<?php endforeach; ?>

	</table>

</div>
