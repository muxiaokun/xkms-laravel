<?php

namespace App\Model;


class Comment extends Common
{
    public function scopeMAdd($query, $data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = Carbon::now();
        $data['add_ip']   = request()->ip();
        return $query->mAdd($data);
    }
}
