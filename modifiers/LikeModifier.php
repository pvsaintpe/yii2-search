<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;

/**
 * Class LikeModifier
 * @package pvsaintpe\search\modifiers
 */
class LikeModifier implements ModifierInterface
{
    /**
     * @param ActiveQuery $query
     * @param mixed $conditions
     */
    public static function modifyQuery(&$query, $conditions)
    {
        if (empty($conditions)) {
            return;
        }
        foreach ($conditions as $attribute => $value) {
            $column = strpos($attribute, '.') ? $attribute : $query->a($attribute);
            if ((preg_match('#^(~|!~|!)(.*)#', $value, $matches) && isset($matches[1]) && isset($matches[2]))) {
                switch ($matches[1]) {
                    case '~':
                        $query->andFilterWhere(['LIKE', $column, $matches[2]]);
                        break;
                    case '!~':
                        $query->andFilterWhere(['NOT LIKE', $column, $matches[2]]);
                        break;
                    default:
                        $query->andFilterWhere(['NOT', [$column => $matches[2]]]);
                }
            } else {
                $query->andFilterWhere([$column => $value]);
            }
        }
    }
}
