<?php

namespace App\Model;


class MessageBoardLog extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->order('add_time desc');
        $data = $query->select('*,inet_ntoa(add_ip) as aip')->where($where)->select();
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
        $data['add_ip']   = request()->ip();
        return $query->mAdd($data);
    }

    public function scopeMFind($query, $id)
    {
        $query->select('*,inet_ntoa(add_ip) as aip');
        return $query->mFind($id);
    }

    public function check_dont_submit($query, $second)
    {
        $second = Carbon::now() - $second;
        $where  = $second . ' < add_time AND add_ip = "' . request()->ip() . '"';
        return ($query->where($where)->count()) ? true : false;
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['send_info']) && $data['send_info'] = serialize($data['send_info']);
        return $data;
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['send_info']) && $data['send_info'] = unserialize($data['send_info']);
        return $data;
    }
}
