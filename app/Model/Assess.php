<?php

namespace App\Model;


class Assess extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        null !== self::option['order'] && self::order('id desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
        }
        return $data;
    }

    protected function mEncodeData(&$data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = serialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = unserialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
