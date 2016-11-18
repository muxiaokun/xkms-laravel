<?php

namespace App\Model;


class Recruit extends Common
{
    public function scopeMEncodeData($query, $data)
    {
        if (isset($data['ext_info']) && is_array($data['ext_info'])) {
            $newExtInfo = [];
            foreach ($data['ext_info'] as $key => $value) {
                $newExtInfo[] = $key . ':' . $value;
            }
            $data['ext_info'] = '|' . implode('|', $newExtInfo) . '|';
        }
    }

    public function scopeMDecodeData($query, $data)
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
