<?php
/**
 * Created by PhpStorm.
 * User: Jonas
 * Date: 19.11.2015
 * Time: 22:28
 */


use jonasw91\loggablebehavior\widget\LogEntryWidget;
use yii\helpers\Url;

echo '<li class="list-group-item" style="width: 100%;">
        <a href="' . Url::to([$model->tableName() . '/view', 'id' => $model->id]) . '">' . $model->name . '</a>'
            . LogEntryWidget::widget([
            'model' => $model,
            'type' => LogEntryWidget::HAVE_SEEN_VIEW]
            ) .
    '</li>';
?>