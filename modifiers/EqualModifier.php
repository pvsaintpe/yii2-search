<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;
use yii\base\BaseObject;

/**
 * Class EqualModifier
 * @package pvsaintpe\search\modifiers
 */
class EqualModifier extends BaseObject implements ModifierInterface
{
    /**
     * @var string
     */
    public $method = 'andFilterWhere';

    /**
     * @param ActiveQuery $query
     * @param mixed $conditions
     */
    public function modifyQuery(&$query, $conditions)
    {
        if (empty($conditions)) {
            return;
        }
        foreach ($conditions as $attribute => $value) {
            $query->{$this->method}([
                (strpos($attribute, '.') || $this->method == 'andFilterHaving') ? $attribute : $query->a($attribute) => $value
            ]);
        }
    }
}
