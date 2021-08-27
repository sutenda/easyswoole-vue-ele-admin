<?php


namespace App\Helper\Backstage;


class BackstageCommonHelper
{
    /**
     * 以pid——id对应，生成树形结构
     * @param array $array
     * @return array|bool
     */
    public static function tree($array)
    {
        $tree = [];     // 生成树形结构
        $newArray = []; // 中转数组，将传入的数组转换

        if (is_array($array) && !empty($array)) {
            foreach ($array as $item) {
                $newArray[$item['id']] = $item;  // 以传入数组的id为主键，生成新的数组
            }
            foreach ($newArray as $k => $val) {
                if ($val['pid'] > 0) {           // 默认pid = 0时为一级
                    $newArray[$val['pid']]['children'][] = &$newArray[$k];   // 将pid与主键id相等的元素放入children中
                } else {
                    $tree[] = &$newArray[$val['id']];   // 生成树形结构
                }
            }
            return $tree;
        } else {
            return false;
        }
    }

    /**
     * 随机生成数字+字母组合随机字符串密码盐（包含大小写字母）
     * @param int $len 生成随机字符串的长度，默认6个字符
     * @return false|string
     */
    public static function alnum($len = 6)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
    }
}