<?php

namespace App\Model;


class Wechat extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        null !== self::option['order'] && self::order('id desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            self::mDecodeData($dataRow);
        }
        return $data;
    }

    public static function bind_wechat($data)
    {
        if (!$data) {
            return false;
        }

        $wechatId = self::mFindId($data['open_id']);
        if ($wechatId) {
            return self::mEdit($data['open_id'], $data);
        } else {
            return self::mAdd($data);
        }
    }
}
