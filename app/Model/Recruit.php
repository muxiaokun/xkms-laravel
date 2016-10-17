<?php

namespace App\Model;


class Recruit extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        null !== self::options['order'] && self::order('id desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            self::mDecodeData($dataRow);
        }
        return $data;
    }

    protected static function mEncodeData(&$data)
    {
        isset($data['explains']) && $data['explains'] = self::mEncodeContent($data['explains']);
        if (isset($data['ext_info']) && is_array($data['ext_info'])) {
            $newExtInfo = [];
            foreach ($data['ext_info'] as $key => $value) {
                $newExtInfo[] = $key . ':' . $value;
            }
            $data['ext_info'] = '|' . implode('|', $newExtInfo) . '|';
        }
    }

    protected static function mDecodeData(&$data)
    {
        if (isset($data['ext_info']) && $data['ext_info']) {
            $data['ext_info'] = explode('|', substr($data['ext_info'], 1, strlen($data['ext_info']) - 2));
            $newExtInfo       = [];
            foreach ($data['ext_info'] as $valueStr) {
                list($key, $value) = explode(':', $valueStr);
                $newExtInfo[$key] = $value;
            }
            $data['ext_info'] = $newExtInfo;
        }
    }
}
