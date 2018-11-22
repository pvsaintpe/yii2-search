<?php

namespace pvsaintpe\search\components;

use yii\data\ActiveDataProvider as BaseActiveDataProvider;

/**
 * Class ActiveDataProvider
 * @package pvsaintpe\search\components
 */
class ActiveDataProvider extends BaseActiveDataProvider
{
    /**
     * @var int
     */
    protected $totalOffset = null;

    /**
     * @var int
     */
    protected $totalLimit = null;

    /**
     * @param int $offset
     */
    public function setTotalOffset($offset)
    {
        $this->totalOffset = $offset;
    }

    /**
     * @param int $limit
     */
    public function setTotalLimit($limit)
    {
        $this->totalLimit = $limit;
    }
}
