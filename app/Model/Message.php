<?php

namespace App\Model;


class Message extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        if (!$query->getQuery()->orders) {
            $query->orderBy('send_time', 'desc');
        }
        return parent::scopeMList($query, $where, $page);
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
