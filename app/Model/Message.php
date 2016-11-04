<?php

namespace App\Model;


class Message extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->orderBy('send_time', 'desc');
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

        $data['send_time'] = Carbon::now();
        return $query->mAdd($data);
    }
}
