<?php

namespace pvsaintpe\search\interfaces;

interface CalcInterface
{
    /**
     * @return array
     */
    public function calcAttributes();

    /**
     * @param string $attribute
     * @return bool
     */
    public function isCalcAttribute($attribute);
}
