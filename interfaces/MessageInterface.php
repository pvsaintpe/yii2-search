<?php

namespace pvsaintpe\search\interfaces;

/**
 * Interface MessageInterface
 * @package pvsaintpe\search\interfaces
 */
interface MessageInterface
{
    /**
     * @param null $language
     * @return mixed
     */
    public static function getMessages($language = null);

    /**
     * @param $code
     * @param null $language
     * @return mixed
     */
    public static function getMessage($code, $language = null);
}