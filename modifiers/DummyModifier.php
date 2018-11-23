<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;

/**
 * Class DummyModifier
 * @package pvsaintpe\search\modifiers
 */
class DummyModifier implements ModifierInterface
{
    /**
     * @param ActiveQuery $query
     * @param mixed $conditions
     */
    public static function modifyQuery(&$query, $conditions)
    {
        return;
    }
}
