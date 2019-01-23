<?php

namespace pvsaintpe\search\modifiers;

use pvsaintpe\search\interfaces\ModifierInterface;
use pvsaintpe\search\components\ActiveQuery;
use yii\base\BaseObject;

/**
 * Class DummyModifier
 * @package pvsaintpe\search\modifiers
 */
class DummyModifier extends BaseObject implements ModifierInterface
{
    /**
     * @param ActiveQuery $query
     * @param mixed $conditions
     */
    public function modifyQuery(&$query, $conditions)
    {
        return;
    }
}
