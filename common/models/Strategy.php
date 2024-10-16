<?php

namespace common\models;

use app\models\Year;
use Yii;

/**
 * This is the model class for table "strategy".
 *
 * @property int $id
 * @property int|null $year_id
 * @property string|null $value
 *
 * @property Indicator[] $indicators
 * @property Year $year
 */
class Strategy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'strategy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
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
            'year_id' => Yii::t('app', 'Year ID'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * Gets query for [[Indicators]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\IndicatorQuery
     */
    public function getIndicators()
    {
        return $this->hasMany(Indicator::class, ['strategy_id' => 'id']);
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
     * @return \common\models\query\StrategyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\StrategyQuery(get_called_class());
    }
}
