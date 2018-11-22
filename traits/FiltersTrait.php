<?php

namespace pvsaintpe\search\traits;

use backend\components\modifiers\DateModifier;
use backend\components\modifiers\DatetimeModifier;

/**
 * Class FiltersTrait
 *
 * @method dateAttributes()
 * @method datetimeAttributes()
 * @method static getAttributes()
 *
 * @package pvsaintpe\search\traits
 */
trait FiltersTrait
{
    public function initDateFilters()
    {
        $attributes = array_intersect_key(static::getAttributes(), array_flip(static::dateAttributes()));
        DateModifier::modifyQuery($this->query, $attributes);
    }

    /**
     * @todo delete $customAttributes
     * @param array $customAttributes
     */
    public function initDatetimeFilters($customAttributes = [])
    {
        $attributes = array_merge(
            array_intersect_key(
                array_merge(static::getAttributes(), array_flip(array_keys($customAttributes))),
                array_flip(static::datetimeAttributes())
            ),
            $customAttributes
        );
        DatetimeModifier::modifyQuery($this->query, $attributes);
    }
}
