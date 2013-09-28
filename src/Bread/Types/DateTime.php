<?php
namespace Bread\Types;

use DateTimeZone;

class DateTime extends \DateTime
{

    public function diff($datetime, $absolute = null)
    {
        $interval = parent::diff($datetime, $absolute);
        return DateInterval::createFromDateInterval($interval);
    }

    public static function __set_state(array $array)
    {
        $datetime = parent::__set_state($array);
        return static::createFromDateTime($datetime);
    }
    
    public static function createFromFormat($format, $time, $timezone = null)
    {
        $datetime = call_user_func_array(array('parent', 'createFromFormat'), func_get_args());
        return static::createFromDateTime($datetime);
    }

    public static function createFromDateTime($datetime)
    {
        return new static($datetime->format('Y-m-d H:i:s'), $datetime->getTimezone());
    }
}