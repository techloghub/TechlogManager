<?php

/** @noinspection PhpLanguageLevelInspection */

/**
 * Created by PhpStorm.
 * User: liuzeyu
 * Date: 2018/11/21
 * Time: 17:15
 */

namespace Component\Library;


use DateInterval;
use DateTime;
use Exception;
use Manager\TechlogBundle\Entity\CalendarAlert;

class LunarHelper
{
    /**
     * @var Lunar
     */
    static $lunar;
    static $defaultString = '1970-01-01 08:00:00';

    /**
     * 通过 Entity 获取下一个提醒日期
     * @param CalendarAlert $entity
     * @return string 时间字符串
     * @throws Exception
     */
    public static function getNextAlert($entity) {
        if ($entity->getStatus() != 1 && $entity->getStatus() != 0) {
            return self::$defaultString;
        }

        $startTime = $entity->getStartTime();
        $startTimestamp = (new DateTime($startTime))->format("U");
        $endTime = $entity->getEndTime();
        $period = $entity->getPeriod();

        /*
         * **********************************
         * 单次执行
         * **********************************
         */
        if ($entity->getStatus() == 0) {
            // 单次执行
            return $startTime;
        }

        /*
         * **********************************
         * 停止执行
         * **********************************
         */
        if ($entity->getCycleType() == 0 || (new DateTime($endTime))->format("U") < time()) {
            // 停止执行或已停止
            return self::$defaultString;
        }

        /*
         * **********************************
         * 按日或周执行，循环周期固定
         * **********************************
         */
        $cycleTime = 0;
        switch ($entity->getCycleType()) {
            case 1:
                // 按日循环
                $cycleTime = $period * 24 * 3600;
                break;
            case 2:
                // 按周循环
                $cycleTime = $period * 7 * 24 * 3600;
                break;
            default:
                break;
        }
        if ($cycleTime != 0) {
            while ($startTimestamp <= (new DateTime($endTime))->format("U")) {
                if ($startTimestamp >= time()) {
                    return date('Y-m-d H:i:s', $startTimestamp);
                } else {
                    $startTimestamp += $cycleTime;
                }
            }
            return $startTime;
        }

        /*
         * **********************************
         * 按月或年执行，需考虑阴历及闰月
         * **********************************
         */
        if ($entity->getCycleType() == 3 || $entity->getCycleType() == 4) {
            while ($startTimestamp <= (new DateTime($endTime))->format("U")) {
                if ($startTimestamp >= time()) {
                    return date('Y-m-d H:i:s', $startTimestamp);
                } else {
                    if ($entity->getLunar() == 0) {
                        // 阳历
                        $date = date_create(date('Y-m-d H:i:s', $startTimestamp));
                        $date->add(new DateInterval('P1Y'));
                        $startTimestamp = $date->format("U");
                    } else {
                        // 阴历
                        if (!isset(self::$lunar)) {
                            self::$lunar = new Lunar();
                        }
                        $year = date('Y', $startTimestamp);
                        $month = date('m', $startTimestamp);
                        $date = date('d', $startTimestamp);
                        $extra = date('H:i:s', $startTimestamp);
                        $lunardate = self::$lunar->convertSolarToLunar($year, $month, $date);
                        $year = $entity->getCycleType() == 3 ? $lunardate[0] + $period : $lunardate[0];
                        $month = $entity->getCycleType() == 4 ? $lunardate[4] + $period : $lunardate[4];
                        $date = $lunardate[5];

                        // 闰月处理
                        $leapmonth = self::$lunar->getLeapMonth($year);
                        if ($leapmonth < $month) {
                            $month--;
                        }

                        $solararray = self::$lunar->convertLunarToSolar($year, $month, $date);
                        $startTimestamp = strtotime($solararray[0].'-'.$solararray[1].'-'.$solararray[2].' '.$extra);
                    }
                }
            }
            return self::$defaultString;
        }

        /*
         * **********************************
         * 按工作日执行
         * **********************************
         */
        if ($entity->getCycleType() == 5) {
            while($startTimestamp <= (new DateTime($endTime))->format("U")) {
                $jsoninfo = file_get_contents("http://api.goseek.cn/Tools/holiday?date="
                    .date('Ymd', $startTimestamp));
                $info = json_decode($jsoninfo, true);
                if (empty($info)) {
                    return $startTime;
                }
                if ($info['data'] == 0 && $startTimestamp >= time()) {
                    if (--$period === 0) {
                        return date('Y-m-d H:i:s', $startTimestamp);
                    }
                }
                $startTimestamp += 24 * 3600;
            }
            return self::$defaultString;
        }

        /*
         * **********************************
         * 传入数据错误兜底
         * **********************************
         */
        return self::$defaultString;
    }

    /**
     * @param String $date 日期字符串
     * @return string
     * @throws Exception
     */
    public static function getLunarDate($date) {
        if (!isset(self::$lunar)) {
            self::$lunar = new Lunar();
        }
        $timestamp = (new DateTime($date))->format("U");
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $date = date('d', $timestamp);
        $solar = self::$lunar->convertSolarToLunar($year, $month, $date);
        return $solar[0].'-'.$solar[1].'-'.$solar[2].' '.date('H:i:s', $date);
    }
}
