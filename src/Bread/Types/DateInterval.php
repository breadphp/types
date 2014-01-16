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
        return (strtotime($to->format('Y-m-d')) - strtotime($from->format('Y-m-d'))) / (60 * 60 * 24);
    }

    public static function getSecondsInPeriod($from, $to, $period)
    {
        $seconds = 0;
        if(!$from || !$to) {
            return $seconds;
        }
        $to = static::setRealTo($from, $to, $period);
        $days = static::getDays($from, $to);
        if ($days === 0) {
            return $to->diff($from)->toSeconds();
        } elseif ($days > 1) {
            $last = new DateTime($from->format('Y-m-d H:i:s'));
            $day = 1;
            $min = (new DateTime())->setTime((int) $period['minHour'], (int) $period['minMinute']);
            $max = (new DateTime())->setTime((int) $period['maxHour'], (int) $period['maxMinute']);
            $secondsDaily = $max->diff($min)->toSeconds();
            while ($day < $days) {
                if (DateInterval::isInPeriod($period, $last->modify('+1 day'))) {
                    $seconds += $secondsDaily;
                }
                $day ++;
            }
        }
        $minToday = (new DateTime($to->format('Y-m-d H:i:s')))->setTime((int) $period['minHour'], (int) $period['minMinute']);
        $maxLast = (new DateTime($from->format('Y-m-d H:i:s')))->setTime((int) $period['maxHour'], (int) $period['maxMinute']);
        $seconds += $maxLast->diff($from)->toSeconds() + $to->diff($minToday)->toSeconds();
        return $seconds;
    }

    public static function setRealTo(DateTime $from, DateTime $to, $period)
    {
        if(!DateInterval::isInPeriod($period, $to)) {
            $to->setTime((int) $period['maxHour'], (int) $period['maxMinute']);
        }
        if(!DateInterval::isInPeriod($period, $to)) {
            return static::setRealTo($from, $to->modify('-1 day'), $period);
        }
        return $to;
    }

    /**
     *
     * @param string $period
     *            from-to,minHour:minMinute-maxHour:maxMinute
     * @param DateTime $date
     */
    public static function isInPeriod($period, DateTime $date)
    {

        $min = (new DateTime($date->format('Y-m-d H:i:s')))->setTime((int) $period['minHour'], (int) $period['minMinute']);
        $max = (new DateTime($date->format('Y-m-d H:i:s')))->setTime((int) $period['maxHour'], (int) $period['maxMinute']);
        $dayOfWeek = (int) $date->format('w');
        $from = (int) explode('-', $period['days'])[0];
        $to = (int) explode('-', $period['days'])[1];
        return $dayOfWeek >= $from && $dayOfWeek <= $to && $date <= $max && $date >= $min;
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
