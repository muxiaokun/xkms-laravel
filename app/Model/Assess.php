<?php

namespace App\Model;


class Assess extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->orderBy('id', 'desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = serialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
        return $data;
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = unserialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
        return $data;
    }
}
