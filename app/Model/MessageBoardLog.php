<?php

namespace App\Model;


class MessageBoardLog extends Common
{
    public function check_dont_submit($query, $second)
    {
        $second = Carbon::now() - $second;
        $where  = $second . ' < add_time AND add_ip = "' . request()->ip() . '"';
        return ($query->where($where)->count()) ? true : false;
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['send_info']) && $data['send_info'] = serialize($data['send_info']);
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['send_info']) && $data['send_info'] = unserialize($data['send_info']);
    }
}
