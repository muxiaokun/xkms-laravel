<?php

namespace App\Model;


class Wechat extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$dataRow) {
            $this->mDecodeData($dataRow);
        }
        return $data;
    }

    public function bind_wechat($data)
    {
        if (!$data) {
            return false;
        }

        $wechatId = $this->mFindId($data['open_id']);
        if ($wechatId) {
            return $this->mEdit($data['open_id'], $data);
        } else {
            return $this->mAdd($data);
        }
    }
}
