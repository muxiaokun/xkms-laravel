<?php

namespace App\Model;


class Quests extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->options['order'] && $query->order('id desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
        return $data;
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
        return $data;
    }
}
