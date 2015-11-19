<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 19.11.2015
 * Time: 15:47
 *
 */


$icons = [
    'create' => '<i class="glyphicon glyphicon-plus-sign"></i>',
    'edit' => '<i class="glyphicon glyphicon-pencil"></i>',
    'view' => '<i class="glyphicon glyphicon-eye-open"></i>',
    'delete' => '<i class="glyphicon glyphicon-trash"></i>'
];

use app\models\User;

$element = "";
$action = $model->action;
$user = User::findIdentity($model->created_by)->username;

if (strcmp($action, ACTION_CREATE) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_CREATE])) {
    $element .= $icons['create'] . '&emsp;Erstellt von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
} else if (strcmp($action, ACTION_UPDATE) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_UPDATE])) {
    $element .= $icons['edit'] . '&emsp;Bearbeitet von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
    $attributes = json_decode($model->old_attr, true);
    if (!is_null($attributes)) {
        $element .= '<div class="box collapsed-box">';
        $element .= '<div class="box-tools"><button class="btn btn-box-tool" data-widget="collapse" aria-expanded="true"><i class="fa fa-plus"></i></button></div>';
        foreach ($attributes as $key1 => $value1) {
            $element .= '<div class="box-body"><b style="min-width:100px;display: inline-block;">' . $model->getAttributeLabel($key1) . '</b> : &emsp;' . $value1 . ' -> ' . json_decode($model->new_attr, true)[$key1] . '</div>';
        }
        $element .= '</div>';
    }
} else if (strcmp($action, ACTION_VIEW) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_VIEW])) {
    $element .= $icons['view'] . '&emsp;Angesehen von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
} else if (strcmp($action, ACTION_DELETE) == 0 || $behavior->containsActionType($action, $behavior->actions[ACTION_DELETE])) {
    $element .= $icons['delete'] . '&emsp;Gelöscht von <b>' . $user . '</b><div class="pull-right">' . date('d.m.y, H:i', $model->created_at) . '</div>';
}

echo '<li class="list-group-item" id="collapseExample">' . $element . '</li>';