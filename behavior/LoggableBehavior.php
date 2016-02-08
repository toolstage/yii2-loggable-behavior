<?php

namespace toolstage\loggablebehavior\behavior;

use toolstage\loggablebehavior\models\LogEntry;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * Class LoggableBehavior
 * @package toolstage\loggablebehavior\behavior
 *
 * Usage:
 *
 * add to your Model:
 *
 * public function behaviors () {
 *    'LoggableBehavior' => [
 *        'class' => LoggableBehavior::className(),
 *        'properties' => ['exampleProp', '...', ..], // $this->getAttributes() for all properties
 *         'actions' => [
 *              // Make sure that the event is triggered on your action:
 *              'create' => ['myCreate'],   // EVENT_AFTER_INSERT
 *              'update' => ['myUpdate'],   // EVENT_BEFORE_UPDATE
 *              'view' => ['myView'],       // EVENT_AFTER_FIND
 *              'delete' => ['myDelete'],   // EVENT_BEFORE_DELETE
 *         ]
 *    ]
 * }
 *
 * set your own event
 *
 * public function init() {
 *       parent::init();
 *      $this->on(self::YOUR_EVENT, [$this->getBehavior('LoggableBehavior'), 'handleEvent']);
 * }
 *
 * now you can trigger this event anywhere at your controller. You only need to declare the action.
 *
 */
class LoggableBehavior extends Behavior
{
    const ACTION_CREATE = "create";
    const ACTION_UPDATE = "update";
    const ACTION_DELETE = "delete";
    const ACTION_VIEW = "view";

    /**
     * @var array - contains all properties of the behavior model witch should be observed
     */
    public $properties;

    /**
     * @var array - contains your custom actions
     */
    public $actions = [];

    public function init()
    {
        parent::init();
        $this->actions = array_merge_recursive([
            'create' => ['create'],
            'update' => ['update'],
            'delete' => ['delete'],
            'view' => ['view'],
        ], $this->actions);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'handleEvent',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'handleEvent',
            ActiveRecord::EVENT_BEFORE_DELETE => 'handleEvent',
        ];
    }

    /**
     * Handles the event callback
     */
    public function handleEvent($event)
    {
        /**
         * Get action
         */
        $action = \Yii::$app->requestedAction->id;

        if ($this->containsAction($action)) {
            /**
             *  Get model
             */
            $model = $this->owner;

            $attr = null;
            $oldAttr = null;
            if (strcmp($action, self::ACTION_UPDATE) == 0 || strcmp($action, 'createajax') == 0) {
                /**
                 * Get the attributes
                 */
                $attr = $this->removeUnusedAttributes($model->getAttributes());
                $oldAttr = $this->removeUnusedAttributes($model->getOldAttributes());
            }
            return $this->addLogEntry($model->id, $model->className(), $action, $oldAttr, $attr);
        }
        return false;
    }

    /**
     * Creates and saves a new log entry. Only properties that have been changed will be saved.
     *
     * @param int $model_id
     *      the id of this model
     * @param $model_type
     *      the class name of the model
     * @param $action
     *      the controller action
     * @param null $old_attr
     *      array of properties
     * @param null $new_attr
     *      array of properties
     * @return bool
     *      whether the log entry has been saved
     */
    protected function addLogEntry($model_id, $model_type, $action, $old_attr = null, $new_attr = null)
    {
        if ($action == self::ACTION_VIEW || $action == self::ACTION_CREATE) {
            if (LogEntry::findOne(['model_id' => $model_id, 'model_type' => $model_type])) {
                return false;
            }
        }
        $logEntry = new LogEntry();
        $logEntry->model_id = $model_id;
        $logEntry->model_type = $model_type;
        $logEntry->action = $action;
        if (!is_null($old_attr) && !is_null($new_attr) && count(array_diff_assoc($old_attr, $new_attr))) {
            $logEntry->old_attr = json_encode(array_diff_assoc($old_attr, $new_attr));
            $logEntry->new_attr = json_encode(array_diff_assoc($new_attr, $old_attr));
        } else {
            if ($action == self::ACTION_UPDATE) {
                return false;
            }
        }
        return $logEntry->save(true);
    }

    /**
     * Removes all unused objects. Only the specified properties are preserved.
     *
     * ... => [
     *    ...
     *    'properties => ['exmapleProp']
     * ]
     * ...
     *
     * In this case you get
     *
     * ['exmapleProp' => $attr['exmapleProp']]
     *
     * @param $attr
     *    the properties of the model as an associative array
     * @return mixed
     *    array of attributes which are contained in $properties
     */
    protected function removeUnusedAttributes($attr)
    {
        foreach ($attr as $value) {
            if (!in_array($value, $this->properties)) {
                unset($attr[$value]);
            }
        }
        return $attr;
    }

    /**
     * Checks if the action should be logged
     *
     * @param $action
     * @return bool
     */
    public function containsAction($action)
    {
        foreach ($this->actions as $actionType) {
            if (in_array($action, $actionType)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return string one of ['create','update','view','delete']
     * @throws Exception
     *      if the given action has not been specified
     */
    public function getActionType($action)
    {
        foreach ($this->actions as $key => $value) {
            if (in_array($action, $value)) {
                return $key;
            }
        }
        throw new Exception ("Unkown action: " . $action . ".");
    }

    /**
     * @return string
     */
    public function _toString()
    {
        return "LoggableBehavior of " . $this->owner->id;
    }
}