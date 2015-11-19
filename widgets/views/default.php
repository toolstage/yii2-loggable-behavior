<?php
/**
 * Created by PhpStorm.
 * User: Jonas
 * Date: 19.11.2015
 * Time: 22:28
 */

use jonasw91\loggablebehavior\widget\LogEntryWidget;

$index = 0;
?>
<div class="box box-solid">
    <!-- /.box-header -->
    <div class="box-body">
        <div class="box-group" id="accordion" role="tablist">
            <?php
            for ($i = 0;
                 $i < count($accordions);
                 $i++) :
                $item = $accordions[$i];
                ?>
                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                <div class="panel box box-primary">
                    <div class="box-header">
                        <h4 class="box-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $index; ?>"
                               aria-expanded="false"
                               class="collapsed">
                                <?= $title[$i]; ?>
                            </a>
                        </h4>

                        <div class="box-tools pull-right">
                            <?php
                            echo LogEntryWidget::widget([
                                'models' => $item->models,
                                'type' => LogEntryWidget::COUNT_SEEN,
                                'modelType' => ucfirst($item->models[0]->tableName()),
                            ]);
                            ?>
                        </div>
                    </div>
                    <div id="collapse<?= $index; ?>" class="panel-collapse collapse" aria-expanded="false"
                         style="height: 0px;" role="tabpanel">
                        <div class="box-body">
                            <?= \yii\widgets\ListView::widget([
                                'dataProvider' => $item,
                                'itemOptions' => ['class' => 'item'],
                                // TODO DEFAULT VIEW FOR ACTIVERECORD
                                'itemView' => $itemView[$i],
                                'layout' => "{items}\n{pager}\n{summary}"
                            ]); ?>
                        </div>
                    </div>
                </div>
                <?php $index++;
            endfor; ?>
        </div>
    </div>
    <!-- /.box-body -->
</div>