<?php

namespace App\Model;


class Wechat extends Common
{
    public function bind_wechat($query, $data)
    {
        if (!$data) {
            return false;
        }

        $wechatId = $query->mFindId($data['open_id']);
        if ($wechatId) {
            return $query->mEdit($data['open_id'], $data);
        } else {
            return $query->mAdd($data);
        }
    }
}
