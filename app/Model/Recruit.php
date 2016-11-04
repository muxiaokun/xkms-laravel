<?php

namespace App\Model;


class Recruit extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->options['order'] && $query->orderBy('id', 'desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['explains']) && $data['explains'] = $query->mEncodeContent($data['explains']);
        if (isset($data['ext_info']) && is_array($data['ext_info'])) {
            $newExtInfo = [];
            foreach ($data['ext_info'] as $key => $value) {
                $newExtInfo[] = $key . ':' . $value;
            }
            $data['ext_info'] = '|' . implode('|', $newExtInfo) . '|';
        }
        return $data;
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
        return $data;
    }
}
