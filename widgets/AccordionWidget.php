<?php
/**
 * Created by PhpStorm.
 * User: jwehner
 * Date: 18.11.2015
 * Time: 12:54
 */

namespace jonasw91\loggablebehavior\widgets;

use yii\base\Widget;


class AccordionWidget extends Widget
{
    public $dataProvider;
    public $id;
    public $title;
    public $itemView;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return $this->render('default', [
            'accordions' => $this->dataProvider,
            'id' => $this->id,
            'title' => $this->title,
            'itemView' => $this->itemView,
        ]);
    }
}

?>