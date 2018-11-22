<?php

namespace pvsaintpe\search\interfaces;

use pvsaintpe\search\components\ActiveQuery;

/**
 * Interface ModifierInterface
 * @package backend\components
 */
interface ModifierInterface
{
    /**
     * @param ActiveQuery $query
     * @param mixed $conditions
     */
    public static function modifyQuery(&$query, $conditions);
}
