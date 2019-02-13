<?php

namespace pvsaintpe\search\components;

use pvsaintpe\boost\db\ActiveQuery as BoostActiveQuery;
use pvsaintpe\search\traits\ActiveRelationTrait;
use Yii;

/**
 * Class ActiveQuery
 * @package pvsaintpe\search\components
 */
class ActiveQuery extends BoostActiveQuery
{
    //use ActiveRelationTrait;

    /**
     * Raw SQL
     * @return string
     */
    public function getRawSql()
    {
        return $this->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql;
    }
}
