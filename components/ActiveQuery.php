<?php

namespace pvsaintpe\search\components;

use pvsaintpe\boost\db\Expression;
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

    /**
     * Expressions
     */

    /**
     * @return array
     */
    public function expressions()
    {
        return [];
    }

    /**
     * @param array $activeAttributes
     * @return ActiveQuery
     */
    public function addExpressionsToSelect(array $activeAttributes = []): ActiveQuery
    {
        foreach ($this->expressions() as $attribute => $expression) {
            if (!$activeAttributes || in_array($attribute, $activeAttributes)) {
                $this->addExpressionToSelect($attribute, $expression);
            }
        }
        return $this;
    }

    /**
     * Add one attribute into select
     * @param string $attribute
     * @param string $expression
     */
    protected function addExpressionToSelect($attribute, $expression)
    {
        if (is_null($this->select)) {
            $this->addSelect([$this->a('*')]);
        }
        $this->addSelect(new Expression("{$expression} {$attribute}"));
    }
}
