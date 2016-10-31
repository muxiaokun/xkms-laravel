<?php

namespace App\Model;


class Comment extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        null !== self::option['order'] && self::order('add_time desc');
        $data = self::select('*,inet_ntoa(add_ip) as aip')->where($where)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = Carbon::now();
        $data['add_ip']   = request()->ip();
        return parent::mAdd($data);
    }
}
