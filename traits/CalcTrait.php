<?php

namespace pvsaintpe\search\traits;

trait CalcTrait
{
    /**
     * @return array
     */
    public function calcAttributes()
    {
        return [];
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function isCalcAttribute($attribute)
    {
        return in_array($attribute, $this->calcAttributes());
    }
}
