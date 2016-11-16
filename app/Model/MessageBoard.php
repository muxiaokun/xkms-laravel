<?php

namespace App\Model;


class MessageBoard extends Common
{
    public function scopeMEncodeData($query, $data)
    {
        isset($data['config']) && $data['config'] = serialize($data['config']);
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['config']) && $data['config'] = unserialize($data['config']);
    }
}
