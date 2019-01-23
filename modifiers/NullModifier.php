<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;
use yii\base\BaseObject;

/**
 * Class NullModifier
 * @package pvsaintpe\search\modifiers
 */
class NullModifier extends BaseObject implements ModifierInterface
{
    const DEFAULT_NOT_SET_VALUE = -1;

    /**
     * @param ActiveQuery $query
     * @param array $conditions attribute => value
     */
    public function modifyQuery(&$query, $conditions)
    {
        if (empty($conditions)) {
            return;
        }
        foreach ($conditions as $attribute => $value) {
            if ($value == static::DEFAULT_NOT_SET_VALUE) {
                $query->andWhere([
                    strpos($attribute, '.') ? $attribute : $query->a($attribute) => null
                ]);
            } else {
                $query->andFilterWhere([
                    strpos($attribute, '.') ? $attribute : $query->a($attribute) => $value
                ]);
            }
        }
    }
}
