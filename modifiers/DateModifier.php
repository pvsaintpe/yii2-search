<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;
use Datetime;
use yii\base\BaseObject;

/**
 * Class DateModifier
 * @package pvsaintpe\search\modifiers
 */
class DateModifier extends BaseObject implements ModifierInterface
{
    const DATE_FORMAT = 'd/m/Y';

    const DATE_SEPARATOR = ' - ';

    /**
     * @param ActiveQuery $query
     * @param array $conditions attribute => value
     * @return array
     */
    public function modifyQuery(&$query, $conditions)
    {
        if (empty($conditions)) {
            return [];
        }
        $columns = [];
        foreach ($conditions as $attribute => $value) {
            if (preg_match('~^(.+)' . preg_quote(static::DATE_SEPARATOR) . '(.+)$~', $value, $match)) {
                $date1 = DateTime::createFromFormat(static::DATE_FORMAT, $match[1]);
                $date2 = DateTime::createFromFormat(static::DATE_FORMAT, $match[2]);

                if ($date1 && $date2) {
                    $columns[$attribute] = $date1 . static::DATE_SEPARATOR . $date2;
                    $query->andWhere([
                        'BETWEEN',
                        strpos($attribute, '.') ? $attribute : $query->a($attribute),
                        $date1->format('Y-m-d'),
                        $date2->format('Y-m-d')
                    ]);
                }
            }
        }
        return $columns;
    }
}
