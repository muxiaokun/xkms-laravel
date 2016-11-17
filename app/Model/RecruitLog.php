<?php

namespace App\Model;


class RecruitLog extends Common
{
    public function scopeMEncodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
