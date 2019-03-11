<?php

namespace pvsaintpe\search\traits;

use pvsaintpe\search\components\ActiveQuery;

/**
 * Trait ExpressionTrait
 */
trait ExpressionTrait
{
    /**
     * @var array
     */
    protected $sort = [];

    /**
     * @var ActiveQuery
     */
    protected $query;

    /**
     * @param string $attribute
     * @return bool
     */
    public function hasExpression(string $attribute): bool
    {
        return isset($this->query->expressions()[$attribute]);
    }

    /**
     * @param string $attribute
     * @return null|string
     */
    public function getAttributeExpression(string $attribute): ?string
    {
        return $this->query->expressions()[$attribute] ?? null;
    }

    /**
     * Sort for expression columns
     */
    public function expressionSort()
    {
        foreach ($this->query->expressions() as $attribute => $realAttribute) {
            $this->sort['attributes'][$attribute] = [
                'asc' => [$realAttribute => SORT_ASC],
                'desc' => [$realAttribute => SORT_DESC]
            ];
        }
    }
}
