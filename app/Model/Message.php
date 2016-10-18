<?php

namespace App\Model;


class Message extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        null !== self::option['order'] && self::order('send_time desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            (new self)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['send_time'] = Carbon::now();
        return parent::mAdd($data);
    }
}
