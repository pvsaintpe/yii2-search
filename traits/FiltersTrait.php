<?php

namespace pvsaintpe\search\traits;

use pvsaintpe\search\modifiers\DateModifier;
use pvsaintpe\search\modifiers\DatetimeModifier;

/**
 * Class FiltersTrait
 *
 * @method static dateAttributes()
 * @method static datetimeAttributes()
 * @method getAttributes($names = null, $except = [])
 *
 * @package pvsaintpe\search\traits
 */
trait FiltersTrait
{
    public function initDateFilters()
    {
        $attributes = array_intersect_key($this->getAttributes(), array_flip(static::dateAttributes()));
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
                array_merge($this->getAttributes(), array_flip(array_keys($customAttributes))),
                array_flip(static::datetimeAttributes())
            ),
            $customAttributes
        );
        DatetimeModifier::modifyQuery($this->query, $attributes);
    }
}
