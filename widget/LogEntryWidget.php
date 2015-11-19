<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 18.11.2015
 * Time: 12:54
 */

namespace jonasw91\loggablebehavior\widget;

use jonasw91\loggablebehavior\models\LogEntry;
use yii\base\Widget;

class LogEntryWidget extends Widget
{
    const HAVE_SEEN_VIEW = "seen";

    public $model;
    public $type;
    public $haveSeen;
    public $behavior;
    public $ausgabe;
    private $logs = [];

    public function init()
    {
        parent::init();
        $this->behavior = $this->model->behaviors['LoggableBehavior'];
        if (!is_null($this->type)) {
            $this->haveSeen = LogEntry::findOne (['model_id' => $this->model->id, 'model_type' => $this->model->className()]);
        } else {
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
            default :
                return $this->render('default', ['data' => $this->logs, 'behavior' => $this->behavior]);
        }
    }


}