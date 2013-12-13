<?php
use yii\helpers\Html;

/**
 * @var array $actions
 */
?>

<div class="user-default-index">
	<h1>Actions in this module</h1>

    <table class="table table-bordered">
        <tr>
            <th>Link</th>
            <th>Description</th>
        </tr>

        <?php foreach ($actions as $text => $info): ?>

            <?php
                $url = isset($info["url"]) ? $info["url"] : $info;
                $description = isset($info["description"]) ? $info["description"] : "";
            ?>

            <tr>
                <td><?= Html::a($text, $url) ?></td>
                <td>
                    URL: <strong><?= $url[0] ?></strong><br/>
                    <?= $description ?>
                </td>
            </tr>

        <?php endforeach; ?>

    </table>

</div>
