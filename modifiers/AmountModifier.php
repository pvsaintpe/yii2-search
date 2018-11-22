<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;
use yii\db\Expression;

/**
 * Class AmountModifier
 * @package pvsaintpe\search\modifiers
 */
class AmountModifier implements ModifierInterface
{
    /**
     * @param ActiveQuery $query
     * @param array $conditions attribute => value
     */
    public static function modifyQuery(&$query, $conditions)
    {
        if (empty($conditions)) {
            return;
        }
        foreach ($conditions as $attribute => $value) {
            $column = strpos($attribute, '.') ? $attribute : $query->a($attribute);
            if ((preg_match('#^(>=|<=|=|<>|!=|<|>)\d+#', $value, $matches)
                    || preg_match('#^(>=|<=|=|<>|!=|<|>)-\d+#', $value, $matches)) && isset($matches[1])
            ) {
                $splitMatches = preg_split(
                    '#('. $matches[1] .')#',
                    $value,
                    -1,
                    PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY
                );
                $operation = $splitMatches[0];
                $value = $splitMatches[1];
                $query->andFilterWhere([$operation, $column, $value]);
            } elseif (preg_match('#^(abs:)(\d{1,15})(\.)?(\d{1,8})?#', $value, $matches)) {
                $decimals = isset($matches[4]) ? strlen($matches[4]) : 0;
                $value = $matches[2] . (isset($matches[4]) ? '.' . $matches[4] : '');
                $query->andWhere(
                    new Expression("ROUND(ABS({$column}), {$decimals}) = ROUND(ABS(:value), {$decimals})", [
                        'value' => $value,
                    ])
                );
            } elseif ((preg_match('#^(~|!~)(.*)#', $value, $matches) && isset($matches[1]) && isset($matches[2]))) {
                $query->andFilterWhere([$matches[1] == '~' ? 'LIKE' : 'NOT LIKE', $column, $matches[2]]);
            } elseif ((preg_match('#^(.*):(.*)#', $value, $matches) && isset($matches[1]) && isset($matches[2]))) {
                $query->andWhere(['BETWEEN', $column, $matches[1], $matches[2]]);
            } elseif (preg_match('#^\d+|^-\d+#', $value) && preg_match('#\d+$|-\d+$#', $value)) {
                $query->andFilterWhere([$column => $value]);
            }
        }
    }
}
