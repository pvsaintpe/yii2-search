<?php

namespace pvsaintpe\search\interfaces;

use pvsaintpe\search\components\ActiveQuery;

/**
 * Interface ModifierInterface
 * @package pvsaintpe\search\interfaces
 */
interface ModifierInterface
{
    /**
     * @param ActiveQuery $query
     * @param mixed $conditions
     */
    public function modifyQuery(&$query, $conditions);
}
