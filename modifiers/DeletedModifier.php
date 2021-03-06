<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;
use yii\base\BaseObject;

/**
 * Class DeletedModifier
 * @package pvsaintpe\search\modifiers
 */
class DeletedModifier extends BaseObject implements ModifierInterface
{
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
            if ($value) {
                $query->andFilterWhere(['>=', strpos($attribute, '.') ? $attribute : $query->a($attribute), $value]);
            } else {
                $query->andFilterWhere([
                    strpos($attribute, '.') ? $attribute : $query->a($attribute) => $value
                ]);
            }
        }
    }
}
