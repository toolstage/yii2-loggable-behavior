<?php

namespace toolstage\loggablebehavior\behavior;

use toolstage\loggablebehavior\models\LogEntry;
use yii\base\Behavior;
use yii\db\ActiveRecord;

define("ACTION_CREATE", 'create');
define("ACTION_UPDATE", 'update');
define("ACTION_DELETE", 'delete');
define("ACTION_VIEW", 'view');

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
 *        'properties' => ['exampleProp', ...]
 *    ]
 * }
 *
 *
 */
class LoggableBehavior extends Behavior
{
	/**
	 * @var array - contains all properties of the behavior model witch should be observed
	 */
	public $properties;

	/**
	 * {@inheritdoc}
	 *
	 * @return array
	 */
	public function events()
	{
		return [
				ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
				ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
				ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
				ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete'
		];
	}

	/**
	 * Adds a new log entry if this model has been viewed
	 */
	public function afterFind()
	{
		/**
		 * Get the action
		 */
		$action = \Yii::$app->requestedAction->id;

		/**
		 *  Get the Model
		 */
		$model = $this->owner;

		if (strcmp($action, ACTION_VIEW) == 0) {
			return $this->addLogEntry($model->id, $model->className(), $action);
		}
		return false;
	}

	/**
	 * Adds a new log entry if this model has been deleted
	 */
	public function beforeDelete()
	{
		/**
		 * Get the action
		 */
		$action = \Yii::$app->requestedAction->id;

		/**
		 *  Get the Model
		 */
		$model = $this->owner;

		if (strcmp($action, ACTION_DELETE) == 0) {
			return $this->addLogEntry($model->id, $model->className(), $action);
		}
		return false;
	}

	/**
	 * Adds new log entry if a new model has been saved
	 *
	 * @return bool
	 */
	public function afterInsert()
	{
		/**
		 * Get the action
		 */
		$action = \Yii::$app->requestedAction->id;

		if (strcmp($action, ACTION_CREATE) == 0) {

			/**
			 *  Get the Model
			 */
			$model = $this->owner;

			return $this->addLogEntry($model->id, $model->className(), $action);
		}
		return false;
	}

	/**
	 * Adds a new log entry if this model has changed
	 *
	 * @return bool
	 */
	public function beforeUpdate()
	{
		/**
		 * Get the action
		 */
		$action = \Yii::$app->requestedAction->id;

		if (strcmp($action, ACTION_UPDATE) == 0) {

			/**
			 *  Get the Model
			 */
			$model = $this->owner;

			/**
			 * Get the attributes
			 */
			$attr = $this->removeUnusedAttributes($model->getAttributes());
			$oldAttr = $this->removeUnusedAttributes($model->getOldAttributes());

			return $this->addLogEntry($model->id, $model->className(), $action, $oldAttr, $attr);
		}
		return false;
	}

	/**
	 * Creates and saves a new log entry. Only properties that have been changed will be saved.
	 *
	 * @param $model_id
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
		$logEntry = new LogEntry();
		$logEntry->model_id = $model_id;
		$logEntry->model_type = $model_type;
		$logEntry->action = $action;

		if (!is_null($old_attr) && !is_null($new_attr)) {
			$logEntry->old_attr = json_encode(array_diff_assoc($old_attr, $new_attr));
			$logEntry->new_attr = json_encode(array_diff_assoc($new_attr, $old_attr));
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
		foreach ($attr as $key => $value) {
			if (!in_array($key, $this->properties)) {
				unset($attr[$key]);
			}
		}
		return $attr;
	}
}