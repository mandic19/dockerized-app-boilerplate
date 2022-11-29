<?php

namespace api\helpers;

use DateTime;
use DateTimeZone;

class TimeHelper
{
    const DATE_FORMAT = 'd/m/Y';
    const TIME_FORMAT = 'h:i A';
    const DATETIME_FORMAT = 'd/m/Y h:i A';
    const SQL_DATE_FORMAT = 'Y-m-d';
    const SQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    const YEAR_SECONDS = 356 * 24 * 60 * 60;

    const DEFAULT_SERVER_TIMEZONE = 'Europe/Sarajevo';


    /**
     * @param $timestamp int in milliseconds
     * @param $format string see http://www.php.net/manual/en/function.date.php
     *
     * @return string - formatted date/time
     */
    public static function format($timestamp, $format = self::DATE_FORMAT)
    {
        if (empty($timestamp)) {
            return null;
        }

        return date($format, $timestamp);
    }

    public static function formatTime($time)
    {
        return date(self::TIME_FORMAT, strtotime($time));
    }

    public static function createDateObjectFromTimestamp($timestamp)
    {
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone(static::DEFAULT_SERVER_TIMEZONE));
        $date->setTimestamp($timestamp);

        return $date;
    }

    protected static function createDateObjectFromDateFormatString($value, $format = null)
    {
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone(static::DEFAULT_SERVER_TIMEZONE));
        $format = is_null($format) ? static::DATE_FORMAT : $format;
        return $date->createFromFormat($format, $value);
    }

    public static function formatAsSqlDate($value)
    {
        return static::createDateObjectFromDateFormatString($value)->format(static::SQL_DATE_FORMAT);
    }

    public static function formatAsDateTimeFromSqlDateTime($value)
    {
        return static::createDateObjectFromDateFormatString($value, static::SQL_DATETIME_FORMAT)->format(static::DATETIME_FORMAT);
    }

    public static function formatAsDateFromSqlDateTime($value)
    {
        return static::createDateObjectFromDateFormatString($value, static::SQL_DATETIME_FORMAT)->format(static::DATE_FORMAT);
    }

    public static function formatAsDateFromSqlDate($value)
    {
        return static::createDateObjectFromDateFormatString($value, static::SQL_DATE_FORMAT)->format(static::DATE_FORMAT);
    }

    public static function formatAsDate($timestamp)
    {
        return static::createDateObjectFromTimestamp($timestamp)->format(static::DATE_FORMAT);
    }

    public static function formatDateAsSqlDate($value)
    {
        return static::createDateObjectFromDateFormatString($value, self::DATE_FORMAT)->format(static::SQL_DATE_FORMAT);
    }

    public static function formatAsDateTime($timestamp, $format = null)
    {
        return static::createDateObjectFromTimestamp($timestamp)->format($format ? $format : static::DATETIME_FORMAT);
    }

    public static function formatAsYear($timestamp)
    {
        return static::createDateObjectFromTimestamp($timestamp)->format('Y');
    }

    public static function formatAsMonth($timestamp, $short = false)
    {
        return static::createDateObjectFromTimestamp($timestamp)->format($short ? 'M' : 'F');
    }

    public static function formatAsTime($timestamp)
    {
        return static::createDateObjectFromTimestamp($timestamp)->format(self::TIME_FORMAT);
    }
}
