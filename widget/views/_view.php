<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 19.11.2015
 * Time: 15:47
 *
 */

use app\models\User;
use jonasw91\loggablebehavior\widget\LogEntryWidget;


$element = "";
$action = $model->action;
$user = User::findIdentity($model->created_by)->username;


if (strcmp($action, ACTION_CREATE) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_CREATE])) {
    $element .= LogEntryWidget::$icons['create'] . '&emsp;Erstellt von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
} else if (strcmp($action, ACTION_UPDATE) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_UPDATE])) {
    $element .= LogEntryWidget::$icons['edit'] . '&emsp;Bearbeitet von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
    $attributes = json_decode($model->old_attr, true);
    if (!is_null($attributes)) {
        $element .= '<div class="box collapsed-box ">';
        $element .= '<div class="box-tools pull-right"><button class="btn btn-box-tool" data-widget="collapse" aria-expanded="true"><i class="fa fa-plus"></i></button></div>';
        foreach ($attributes as $key1 => $value1) {
            $element .= LogEntryWidget::createChangeOutput($key1, $value1, $model);
        }
        $element .= '</div>';
    }
} else if (strcmp($action, ACTION_VIEW) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_VIEW])) {
    $element .= LogEntryWidget::$icons['view'] . '&emsp;Angesehen von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
} else if (strcmp($action, ACTION_DELETE) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_DELETE])) {
    $element .= LogEntryWidget::$icons['delete'] . '&emsp;Gelöscht von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
}

echo '<li class="list-group-item" id="collapseExample">' . $element . '</li>';