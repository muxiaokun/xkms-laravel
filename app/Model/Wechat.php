<?php

namespace App\Model;


class Wechat extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function bind_wechat($data)
    {
        if (!$data) {
            return false;
        }

        $wechat_id = $this->mFindId($data['open_id']);
        if ($wechat_id) {
            return $this->mEdit($data['open_id'], $data);
        } else {
            return $this->mAdd($data);
        }
    }
}
