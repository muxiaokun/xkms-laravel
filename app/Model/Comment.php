<?php

namespace App\Model;


class Comment extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        !isset(self::options['order']) && self::order('add_time desc');
        $data = self::field('*,inet_ntoa(add_ip) as aip')->where($where)->select();
        foreach ($data as &$dataRow) {
            self::mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = Carbon::now();
        $data['add_ip']   = ['exp', 'inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")'];
        return parent::mAdd($data);
    }
}
