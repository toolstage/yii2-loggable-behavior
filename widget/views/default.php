<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 18.11.2015
 * Time: 12:54
 */
use yii\widgets\ListView;

?>
<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title">Logs</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" aria-expanded="false"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="list-group" style="padding:10px;">
            <?php
                echo ListView::widget([
                    'dataProvider' => $data,
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => '_view',
                    'viewParams' => ['behavior' => $behavior],
                    'layout' => "{pager}\n{items}\n{summary}"
                ]);
            ?>
        </div>
    </div>
</div>
