<?php

namespace App\Model;


class RecruitLog extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->options['order'] && $query->orderBy('add_time', 'desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMAdd($query, $data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = Carbon::now();
        return $query->mAdd($data);
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
