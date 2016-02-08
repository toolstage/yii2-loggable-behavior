<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 19.11.2015
 * Time: 15:47
 *
 */

use app\models\User;
use toolstage\loggablebehavior\behavior\LoggableBehavior;
use yii\base\Exception;

/**
 * @var LoggableBehavior $behavior
 * @var string $widgetClass
 */

if (!class_exists($widgetClass)) {
    throw new Exception ("Class file doesn't exists!");
}
$element = "";
$action = $model->action;
$user = User::findIdentity($model->created_by)->username;

//$element .= TaskLogEntryWidget::createLogEntryHeader($action, $user, $model->created_at, $behavior, $attributes, $model);
$element .= $widgetClass::createLogEntryHeader($behavior, $model);
if (isset($model->old_attr) && isset($model->new_attr) && !is_null($model->old_attr) && !is_null($model->new_attr)) {
    $element .= $widgetClass::createLogEntryBodyUpdate($model);
}
echo '<div class="logheader" id="collapseExample">' . $element . '</div>';