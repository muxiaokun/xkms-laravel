<?php

namespace App\Model;


class RecruitLog extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        static::mGetPage($page);
        null !== static::options['order'] && static::order('add_time desc');
        $data = static::where($where)->select();
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
        return parent::mAdd($data);
    }

    protected function mEncodeData(&$data)
    {
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
