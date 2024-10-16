<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%year}}".
 *
 * @property int $id
 * @property int|null $year
 *
 * @property Progress[] $progresses
 */
class Year extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%year}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'year' => Yii::t('app', 'Year'),
        ];
    }

    /**
     * Gets query for [[Progresses]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ProgressQuery
     */
    public function getProgresses()
    {
        return $this->hasMany(Progress::class, ['year_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\YearQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\YearQuery(get_called_class());
    }
}
