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

    public function init()
    {
        parent::init();
        if (!is_null($this->type)) {
            if ($this->type == self::HAVE_SEEN_VIEW) {
                $this->haveSeen = LogEntry::findOne(['model_id' => $this->model->id, 'model_type' => $this->model->className(), 'created_by' => User::getCurrentUser()->id, 'action' => 'view']);
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
                return $this->render('_seen', ['haveSeen' => $this->haveSeen]);
            case self::COUNT_SEEN :
                return $this->render('_countSeen', ['countSeen' => $this->countSeen, 'modelType' => $this->modelType]);
            default :
                return $this->render('default', ['data' => $this->logs, 'behavior' => $this->behavior]);
        }
    }


}