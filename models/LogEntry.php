<?php

namespace toolstage\loggablebehavior\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "log_entry".
 *
 * @property integer $id
 * @property integer $model_id
 * @property string $model_type
 * @property string $action
 * @property string $old_attr
 * @property string $new_attr
 * @property integer $created_by
 * @property integer $created_at
 */
class LogEntry extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_entry';
    }

    /**
     * @param ActiveRecord $model
     * @param int $pageSize
     * @return array|ActiveDataProvider
     */
    public static function getLogs(ActiveRecord $model, $pageSize = 5)
    {
        if (!is_null($model)) {
            $id = $model->id;
            $type = $model->className();
            return
                new ActiveDataProvider([
                    'query' => LogEntry::find()
                        ->where(['model_id' => $id, 'model_type' => $type])
                        ->orderBy('created_at DESC'),
                    'pagination' => [
                        'pageSize' => $pageSize,
                        'pageParam' => 'log'
                    ]
                ]);
        }
        return [];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_type', 'action'], 'required'],
            [['model_id', 'created_by', 'created_at'], 'integer'],
            [['old_attr', 'new_attr'], 'string'],
            [['model_type', 'action'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'Model ID',
            'model_type' => 'Model Type',
            'action' => 'Action',
            'old_attr' => 'Old Attr',
            'new_attr' => 'New Attr',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'blameableBehavior' => [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by'
                ]
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at'
                ]
            ],
        ];
    }

    /**
     * @param $model_type
     * @param $attributes
     * @return null
     */
    public static function loadObject($model_type, $attributes)
    {
        if (!is_null($model_type)) {
            $object = new $model_type ();
            if (!is_null($attributes)) {
                $attributes = [StringHelper::basename(get_class($object)) => (array)json_decode($attributes)];
                $object->load($attributes);
            }
            return $object;
        }
        return null;
    }
}
