<?php

class Timer {
    public static function timeServerInt($timeServer = NULL) {
        if ($timeServer == NULL) {
            $timeServer = Timer::timeCalc();
        }
        $timeReturn = null;
        if (Timer::isSimulated()) {
            $m2 = explode(".", $timeServer . "");
            $md = substr($m2[1], 0, 3);
            switch (strlen($md)) {
                case 0:
                    $md = "000";
                case 1:
                    $md = $md . "00";
                case 2:
                    $md = $md . "0";
            }
            $timeReturn = (int) (substr($m2[0], -4) . $md);
        } else {
            $timeReturn = (int) ($timeServer * 1000);
        }
        return $timeReturn;
    }

    public static function isSimulated() {
        return PHP_INT_MAX <= 2147483647;
    }

    public static function timeCalc() {
        return microtime(1);
    }

    public static function timeServer() {
        if (Timer::isSimulated()) {
            return Timer::timeServerInt() / 1000;
        } else {
            return Timer::timeCalc();
        }
    }
    public static function getPreparedTime() {
        return Property::get('timer-prepared');
    }
    public static function setPreparedTime($val) {
        Property::set('timer-prepared', round($val) * 1);
        Property::set('timer-start', NULL);
        Property::set('timer-end', NULL);
    }
    public static function start($time) {
        $preparedTimer = Timer::getPreparedTime();
        if (!$time) {
            $time = round(Timer::timeServer());
        }
        $timeServer = $time;
        Property::set('timer-start', $timeServer);
        Property::set('timer-end', $timeServer + $preparedTimer);
    }
}
