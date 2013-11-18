<?php
namespace Bread\Types;

class DateInterval extends \DateInterval
{

    public function __construct($intervalSpecification)
    {
        parent::__construct($intervalSpecification);
        static::normalize($this);
    }

    public function toSeconds()
    {
        return static::getSeconds($this);
    }

    public static function getDays($from, $to)
    {
        return intval(($to->getTimestamp()-$from->getTimestamp())/(60*60*24));
    }

    /**
     * @param string $period "$from-$to";  0 (for Sunday) through 6 (for Saturday)
     */
    public static function isInPeriod($period, DateTime $date)
    {
        $dayOfWeek = (int) $date->format('w');
        $from = (int) explode('-', $period)[0];
        $to = (int) explode('-', $period)[1];
        return $dayOfWeek >= $from && $dayOfWeek <= $to;
    }

    public static function getSeconds(\DateInterval $interval)
    {
        return ($interval->y * 365 * 24 * 60 * 60) + ($interval->m * 30 * 24 * 60 * 60) + ($interval->d * 24 * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + ($interval->s);
    }

    public static function normalize(\DateInterval $interval)
    {
        $seconds = static::getSeconds($interval);
        $interval->y = floor($seconds / 60 / 60 / 24 / 365);
        $seconds -= $interval->y * 31536000;
        $interval->m = floor($seconds / 60 / 60 / 24 / 30);
        $seconds -= $interval->m * 2592000;
        $interval->d = floor($seconds / 60 / 60 / 24);
        $seconds -= $interval->d * 86400;
        $interval->h = floor($seconds / 60 / 60);
        $seconds -= $interval->h * 3600;
        $interval->i = floor($seconds / 60);
        $seconds -= $interval->i * 60;
        $interval->s = $seconds;
    }

    public static function createFromDateString($time)
    {
        $interval = parent::createFromDateString($time);
        return static::createFromDateInterval($interval);
    }

    public static function createFromDateInterval(\DateInterval $interval)
    {
        $intervalSpecification = static::getIntervalSpecification($interval);
        return new static($intervalSpecification);
    }

    public static function getIntervalSpecification(\DateInterval $interval)
    {
        self::normalize($interval);
        $intervalSpecification = 'P';
		$intervalSpecification .= $interval->y . 'Y';
		$intervalSpecification .= $interval->m . 'M';
		$intervalSpecification .= $interval->d . 'D';
		$intervalSpecification .= 'T';
		$intervalSpecification .= $interval->h . 'H';
		$intervalSpecification .= $interval->i . 'M';
		$intervalSpecification .= $interval->s . 'S';
        return $intervalSpecification;
    }
}
