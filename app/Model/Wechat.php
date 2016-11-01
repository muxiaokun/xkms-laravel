<?php

namespace App\Model;


class Wechat extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        static::mGetPage($page);
        null !== static::option['order'] && static::order('id desc');
        $data = static::where($where)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function bind_wechat($data)
    {
        if (!$data) {
            return false;
        }

        $wechatId = static::mFindId($data['open_id']);
        if ($wechatId) {
            return static::mEdit($data['open_id'], $data);
        } else {
            return static::mAdd($data);
        }
    }
}
