<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int|null $indicator_id
 * @property string|null $value
 * @property string|null $unit
 * @property int|null $progress
 * @property int|null $plan
 *
 * @property Indicator $indicator
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['indicator_id', 'progress', 'plan'], 'integer'],
            [['value', 'unit'], 'string', 'max' => 255],
            [['indicator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Indicator::class, 'targetAttribute' => ['indicator_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'indicator_id' => Yii::t('app', 'Indicator ID'),
            'value' => Yii::t('app', 'Value'),
            'unit' => Yii::t('app', 'Unit'),
            'progress' => Yii::t('app', 'Progress'),
            'plan' => Yii::t('app', 'Plan'),
        ];
    }

    /**
     * Gets query for [[Indicator]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\IndicatorQuery
     */
    public function getIndicator()
    {
        return $this->hasOne(Indicator::class, ['id' => 'indicator_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TaskQuery(get_called_class());
    }
}
