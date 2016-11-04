<?php

namespace App\Model;


class Wechat extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->orderBy('id', 'desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

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
