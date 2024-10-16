<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%progress}}".
 *
 * @property int $id
 * @property int $task_id
 * @property int $year_id
 * @property string|null $value
 * @property string|null $unit
 * @property string|null $progress
 * @property string|null $plan
 * @property int|null $bar
 * @property  float $otklon
 *
 * @property Task $task
 * @property Year $year
 */
class Progress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%progress}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['otklon'], 'number'],

            [['task_id', 'year_id'], 'required'],
            [['task_id', 'year_id', 'bar'], 'integer'],
            [['value'], 'string', 'max' => 1000],
            [['unit', 'progress', 'plan'], 'string', 'max' => 255],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
            [['year_id'], 'exist', 'skipOnError' => true, 'targetClass' => Year::class, 'targetAttribute' => ['year_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'task_id' => Yii::t('app', 'Task ID'),
            'year_id' => Yii::t('app', 'Year ID'),
            'value' => Yii::t('app', 'Value'),
            'unit' => Yii::t('app', 'Unit'),
            'progress' => Yii::t('app', 'Progress'),
            'plan' => Yii::t('app', 'Plan'),
            'bar' => Yii::t('app', 'Bar'),
        ];
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\TaskQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Gets query for [[Year]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\YearQuery
     */
    public function getYear()
    {
        return $this->hasOne(Year::class, ['id' => 'year_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ProgressQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProgressQuery(get_called_class());
    }
}
