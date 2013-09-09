<?php
/**
 * Bread PHP Framework (http://github.com/saiv/Bread)
 * Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 *
 * Licensed under a Creative Commons Attribution 3.0 Unported License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 * @link       http://github.com/saiv/Bread Bread PHP Framework
 * @package    Bread
 * @since      Bread PHP Framework
 * @license    http://creativecommons.org/licenses/by/3.0/
 */
namespace Bread\Types;

class Number
{

    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function ceil()
    {
        return ceil($this->value);
    }

    public function floor()
    {
        return floor($this->value);
    }

    public function round($precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        return round($this->value, $precision, $mode);
    }

    public function toInteger()
    {
        return (int) $this->value;
    }

    public function toDouble()
    {
        return (double) $this->value;
    }
}