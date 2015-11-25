<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 18.11.2015
 * Time: 12:54
 */

namespace jonasw91\loggablebehavior\widget;

use jonasw91\loggablebehavior\models\LogEntry;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\StringHelper;


/**
 *
 * usage:
 *
 * #shows log entry list#################
 * LogEntryWidget::widget([
 *      'model' => $model
 * ]);
 *
 * #shows whether an model has seen #################
 * @Model init ()
 * spezified the view event e.g:
 * $this->on(self::EVENT_BEFORE_VIEW, [$this->getBehavior('LoggableBehavior'), 'handleEvent']);
 *
 * @Controller actionView ()
 * trigger the event
 * $model->trigger(ExampleModel::EVENT_BEFORE_VIEW);
 *
 * @View
 * LogEntryWidget::widget([
 *   'model' => $model,
 *   'type' => LogEntryWidget::VIEW_NEW
 * ]);
 *
 * #shows amount of models the actual user haven't seen yet #################
 *
 *  LogEntryWidget::widget([
 *      'model' => $model
 *      'type' => LogEntryWidget::VIEW_COUNT_SEEN
 *  ]);
 * your also have to add an view event for this widget type
 *
 *
 * Class LogEntryWidget
 * @package jonasw91\loggablebehavior\widget
 */
class LogEntryWidget extends Widget
{
    const VIEW_NEW = "seen";
    const VIEW_COUNT_SEEN = "countSeen";
    const VIEW_DEFAULT = "default";

    const ACTION_CREATE = "create";
    const ACTION_UPDATE = "update";
    const ACTION_VIEW = "view";
    const ACTION_DELETE = "delete";

    public $model;
    public $type;
    public $haveSeen;
    public $behavior;
    public $ausgabe;
    protected $logs = [];
    protected $countSeen;
    protected $modelType;

    public static $icons = [
        'create' => '<i class="glyphicon glyphicon-plus-sign"></i>',
        'update' => '<i class="glyphicon glyphicon-pencil"></i>',
        'view' => '<i class="glyphicon glyphicon-eye-open"></i>',
        'delete' => '<i class="glyphicon glyphicon-trash"></i>',
        'right' => '<i class="glyphicon glyphicon-arrow-right"></i>'
    ];

    public function init()
    {
        parent::init();
        if (is_null($this->model)) {
            throw new Exception("model darf nicht null sein");
        }
        $this->logs = $this->loadLogs();
        $this->type = is_null($this->type) ? self::VIEW_DEFAULT : $this->type;
        switch ($this->type) {
            case self::VIEW_NEW:
                $this->haveSeen =
                    LogEntry::findOne([
                        'model_id' => $this->model->id,
                        'model_type' => $this->model->className(),
                        'created_by' => User::getCurrentUser()->id,
                        'action' => 'view']);
                break;
            case self::VIEW_COUNT_SEEN:
                $model = $this->model;
                $modelClass = $model->className();
                if (!class_exists($modelClass)) {
                    throw new Exception ("Klasse konnte nicht gefunden werden!");
                }
                $this->countSeen = $modelClass::find(['id' => $model->id])->count() -
                    LogEntry::find(['model_id' => $model->id, 'model_type' => $model->className(), 'created_by' => User::getCurrentUser()->id, 'action' => 'view'])->count();
                break;
            case self::VIEW_DEFAULT:
                $this->behavior = $this->model->behaviors['LoggableBehavior'];
                break;
        }
    }

    public function run()
    {
        if (is_null($this->type)) {
            throw new Exception("type darf nicht null sein");
        }
        switch ($this->type) {
            case self::VIEW_NEW :
                return $this->render('_seen', ['haveSeen' => $this->haveSeen, 'class' => 'btn btn-flat btn-sm btn-info', 'style' => 'float:right;margin-top:-5px;']);
            case self::VIEW_COUNT_SEEN :
                return $this->render('_countSeen', ['countSeen' => $this->countSeen, 'modelType' => StringHelper::basename(get_class($this->model))]);
            case self::VIEW_DEFAULT:
                return $this->render('default', ['data' => $this->logs, 'behavior' => $this->behavior, 'widgetClass' => $this->className(), 'view' => '_view']);
        }
        return null;
    }

    /**
     * Creates a list of the changed properties
     *
     * @param $model
     * @return string
     * @throws Exception
     */
    public static function createLogEntryBodyUpdate($model)
    {
        if (!is_null($model)) {
            $labels = self::getLables($model->model_type);
            $output = '';
            $oldAttr = json_decode($model->old_attr, true);
            $newAttr = json_decode($model->new_attr, true);
            foreach ($oldAttr as $key => $oldValue) {
                $label = array_key_exists($key, $labels) ? $labels[$key] : $model->getAttributeLabel($key);
                $output .=
                    '<li><b style="min-width:100px;display: inline-block;">' . $label . '</b> : &emsp;' .
                    $oldValue . ' &emsp; ' . self::$icons['right'] . ' &emsp;'
                    . $newAttr [$key] . "</li>";
            }

            $element = '<div class="panel bodyBox collapse" aria-expanded="false">';
            $element .= '<ul class="panel-body" style="list-style: none">';

            $element .= $output;
            $element .= '</ul></div>';
            return $element;
        }
        throw new Exception ('Params are null!');
    }

    /**
     * Creates a default header view of an log entry
     *
     * @param $logModel
     *      the log entry
     * @param $behavior
     *      the LoggableBehavior instance of this log entry
     * @return string
     *      default header view of an log entry
     * @throws Exception
     *      if a param is null
     */
    public static function createLogEntryHeader($behavior, $logModel)
    {
        if (!is_null($behavior) && !is_null($logModel)) {
            $user = Yii::$app->getUser();
            $action = $logModel->action;
            $date = $logModel->created_at;
            $action = $behavior->getActionType($action);
            $output = self::$icons[$action];
            switch ($action) {
                case self::ACTION_CREATE:
                    $output .= '&emsp;Erstellt';
                    break;
                case self::ACTION_UPDATE:
                    $output .= '&emsp;Bearbeitet';
                    break;
                case self::ACTION_VIEW:
                    $output .= '&emsp;Angesehen';
                    break;
                case self::ACTION_DELETE:
                    $output .= '&emsp;Gelöscht';
                    break;
            }
            $output .= ' von <b>' . $user->username . '</b><div class="pull-right">' . date('d.m.y, H:i', $date) . '</div>';
            return $output;
        }
        throw new Exception ('Params are null!');
    }

    /**
     * Get the label array of the modelType
     * see @ yii\db\ActiveRecord -> attributesLabels()
     *
     * @param $modelType
     *      class name
     * @return array
     *      labels for the properties
     * @throws Exception
     *      if the model type is unknown
     */
    protected static function getLables($modelType)
    {
        if (!class_exists($modelType)) {
            throw new Exception ("Unkown model type! Type: " . $modelType);
        }
        $instance = new $modelType ();
        return $instance->attributeLabels();
    }

    /**
     * See @jonas91\loggablebehavior\models\LogEntry
     *
     * @return mixed
     */
    protected function loadLogs($logClass = null)
    {
        if (!is_null($logClass)) {
            $logs = LogEntry::getLogs($logClass);
        } else {
            $logs = LogEntry::getLogs($this->model);
        }
        return $logs;
    }
}