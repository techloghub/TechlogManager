<?php
/**
 * Created by PhpStorm.
 * User: liuzeyu
 * Date: 2018/11/21
 * Time: 17:15
 */

namespace Component\Library;


use Manager\TechlogBundle\Entity\CalendarAlert;

class LunarHelper
{
    /**
     * @var Lunar
     */
    static $lunar;
    static $defaultString = '0000-00-00 00:00:00';

    /**
     * 通过 Entity 获取时间戳
     * @param CalendarAlert $entity
     * @return string 时间字符串
     */
    public static function getNextAlert($entity) {
        if ($entity->getStatus() != 1 && $entity->getStatus() != 2) {
            return self::$defaultString;
        }
        $startTime = $entity->getStartTime();
        $endTime = $entity->getEndTime();
        if ($entity->getLunar() == 1) {
            self::$lunar = new Lunar();
            $startTime = self::getSorlarDate($entity->getStartTime());
            $endTime = self::getSorlarDate($entity->getEndTime());
        }
        if ($entity->getStatus() == 1) {
            return strtotime($startTime) < time() ? self::$defaultString : $startTime;
        }
        if ($entity->getCycleType() == 0 || strtotime($endTime) < time()) {
            return self::$defaultString;
        }
        $cycleTime = 0;
        switch ($entity->getCycleType()) {
            case 1:
                $cycleTime = $entity->getPeriod() * 24 * 3600;
                break;
            case 2:
                $cycleTime = $entity->getPeriod() * 7 * 24 * 3600;
                break;
            default:
                break;
        }
        $startTimestamp = strtotime($startTime);
        if ($cycleTime != 0) {
            while ($startTimestamp <= strtotime($endTime)) {
                if ($startTimestamp >= time()) {
                    return date('Y-m-d H:i:s', $startTimestamp);
                }
            }
            return self::$defaultString;
        }
        if ($entity->getCycleType() == 3) {

        }

        return self::$defaultString;
    }

    private static function getSorlarDate($date) {
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $date = date('d', $timestamp);
        $leapMonth = self::$lunar->getLeapMonth($year);
        if ($leapMonth > 0 && $leapMonth < $month) {
            $month++;
        }
        $solar = self::$lunar->convertLunarToSolar($year, $month, $date);
        return $solar[0].'-'.$solar[1].'-'.$solar[2].' '.date('H:i:s', $date);
    }
}