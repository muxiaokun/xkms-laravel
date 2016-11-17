<?php

namespace App\Model;


class AssessLog extends Common
{
    public function scopeMEncodeData($query, $data)
    {
        isset($data['score']) && $data['score'] = serialize($data['score']);
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['score']) && $data['score'] = unserialize($data['score']);
    }
}
