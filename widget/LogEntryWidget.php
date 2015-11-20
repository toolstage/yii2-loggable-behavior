<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 18.11.2015
 * Time: 12:54
 */

namespace jonasw91\loggablebehavior\widget;

use jonasw91\loggablebehavior\models\LogEntry;
use webvimark\modules\UserManagement\components\UserIdentity;
use webvimark\modules\UserManagement\models\User;
use yii\base\Widget;

class LogEntryWidget extends Widget
{
    const HAVE_SEEN_VIEW = "seen";
    const COUNT_SEEN = "countSeen";

    public $model;
    public $models;
    public $modelType;
    public $type;
    public $haveSeen;
    public $behavior;
    public $ausgabe;
    private $logs = [];
    private $countSeen;
    public static $icons = [
        'create' => '<i class="glyphicon glyphicon-plus-sign"></i>',
        'edit' => '<i class="glyphicon glyphicon-pencil"></i>',
        'view' => '<i class="glyphicon glyphicon-eye-open"></i>',
        'delete' => '<i class="glyphicon glyphicon-trash"></i>',
        'person' => '<i class="glyphicon glyphicon-user"></i>',
        'right' => '<i class="glyphicon glyphicon-arrow-right"></i>'
    ];

    public function init()
    {
        parent::init();
        if (!is_null($this->type)) {
            if ($this->type == self::HAVE_SEEN_VIEW) {
                $this->haveSeen =
                    LogEntry::findOne([
                        'model_id' => $this->model->id,
                        'model_type' => $this->model->className(),
                        'created_by' => User::getCurrentUser()->id,
                        'action' => 'view']);
            } else if ($this->type == self::COUNT_SEEN) {
                if (is_array($this->models) && count($this->models) > 0) {
                    $index = 0;
                    foreach ($this->models as $model) {
                        $log = LogEntry::findOne(['model_id' => $model->id, 'model_type' => $model->className(), 'created_by' => User::getCurrentUser()->id, 'action' => 'view']);
                        if (is_null($log)) {
                            $index++;
                        }
                    }
                    $this->countSeen = $index;
                }
            }
        } else {
            $this->behavior = $this->model->behaviors['LoggableBehavior'];
            $this->logs = $this->loadLogs();
        }
    }

    protected function loadLogs()
    {
        return LogEntry::getLogs($this->model);
    }

    public function run()
    {
        switch ($this->type) {
            case self::HAVE_SEEN_VIEW :
                if ($this->model->tableName() == 'task') {
                    if (!is_null($this->model->user)) {
                        if ($this->model->user->id == User::getCurrentUser()->id) {
                            return $this->render('_seen', ['haveSeen' => $this->haveSeen, 'class' => 'btn btn-flat btn-sm btn-info', 'style' => 'float:right']);
                        }
                    }
                } else {
                    return $this->render('_seen', ['haveSeen' => $this->haveSeen, 'class' => 'badge bg-light-blue pull-right', 'style' => '']);
                }
            case self::COUNT_SEEN :
                return $this->render('_countSeen', ['countSeen' => $this->countSeen, 'modelType' => $this->modelType]);
            default :
                return $this->render('default', ['data' => $this->logs, 'behavior' => $this->behavior]);
        }
    }

    public static function createChangeOutput($property, $old, $model)
    {
        $output = '';
        $icon = '';
        switch ($property) {

            case 'description':
                $output .= json_decode($model->new_attr, true)[$property];
                break;
            case 'user_id':
                $icon .= self::$icons['person'];
            default:
                $output .=
                    $old . ' &emsp; ' . self::$icons['right'] . ' &emsp;'
                    . json_decode($model->new_attr, true)[$property];
                break;
        }
        $label = '<span>' . $icon . '<b style="min-width:100px;display: inline-block;">' . $model->getAttributeLabel($property) . '</b></span> : &emsp;';

        return '<div class="box-body">' . $label . $output . '</div>';
    }
}