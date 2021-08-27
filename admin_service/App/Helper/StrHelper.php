<?php


namespace App\Helper;


class StrHelper
{
    public static function isEmpty($val)
    {
        if(isset($val) && ($val == '0' || $val == 0))
        {
            return false;
        }
        return empty($val);
    }

    public static function randStr($length)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = str_shuffle($str);//打乱字符串
        $len = strlen($str)-1;
        $rands = '';
        for ($i=0;$i<$length;$i++) {
            $num=mt_rand(0,$len);
            $rands .= $str[$num];
        }
        return $rands;
    }
}