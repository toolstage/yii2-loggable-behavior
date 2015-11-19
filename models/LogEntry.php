<?php

namespace jonasw91\loggablebehavior\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

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
class LogEntry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_entry';
    }

    /**
     * @param $model ActiveRecord
     * @return $this
     */
    public static function getLogs($model)
    {

        if (!is_null($model)) {
            $id = $model->id;
            $type = $model->className();
            $query = LogEntry::find()
                ->where(['model_id' => $id, 'model_type' => $type])
                ->orderBy('created_at DESC');
            return new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 5, 'pageParam' => 'log']]);
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
}
