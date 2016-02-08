<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 18.11.2015
 * Time: 12:54
 */
use yii\widgets\ListView;
use \yii\data\ActiveDataProvider;
use \toolstage\loggablebehavior\behavior\LoggableBehavior;

/**
 * @var ActiveDataProvider $data
 * @var string $view
 * @var LoggableBehavior $behavior
 * @var string $widgetClass
 */
?>
<div class="box panel-default">
    <div class="box-header panel-heading">
        <h3 class="box-title">Logs</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" aria-expanded="false"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="" style="padding:10px;">

            <?php
            echo ListView::widget([
                'dataProvider' => $data,
                'itemOptions' => [
                    'class' => 'item'
                ],
                'itemView' => $view,
                'viewParams' => [
                    'behavior' => $behavior,
                    'widgetClass' => $widgetClass],
                'layout' => "{pager}\n{items}\n{summary}"
            ]);
            ?>
        </div>
    </div>
</div>
