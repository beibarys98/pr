<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Strategy]].
 *
 * @see \common\models\Strategy
 */
class StrategyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\Strategy[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Strategy|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
