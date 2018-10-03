<?php

namespace pvsaintpe\search\components;

use pvsaintpe\boost\db\ActiveQuery as BoostActiveQuery;
use Yii;

/**
 * Class ActiveQuery
 * @package pvsaintpe\search\components
 */
class ActiveQuery extends BoostActiveQuery
{
    /**
     * Raw SQL
     * @return string
     */
    public function getRawSql()
    {
        return $this->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql;
    }
}
