<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 27.11.2015
 * Time: 12:01
 */

namespace toolstage\loggablebehavior\event;

use yii\base\Event;

/**
 * Class LogEvent
 * @package jonasw91\loggablebehavior\event
 *
 * @var array $attributes
 * @var string $action
 */
class LogEvent extends Event
{
    public $attributes = [];
    public $action;
}