<?php


namespace App\Helper;


class TimeHelper
{
    public static function getNowTime(){
        return date("Y-m-d H:i:s",time());
    }

    public static function getNowMonth(){
        return date("Ym",time());
    }
}