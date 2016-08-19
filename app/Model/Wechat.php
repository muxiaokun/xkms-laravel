<?php

namespace App\Model;


class Wechat extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function bind_wechat($data)
    {
        if (!$data) {
            return false;
        }

        $wechat_id = $this->m_find_id($data['open_id']);
        if ($wechat_id) {
            return $this->m_edit($data['open_id'], $data);
        } else {
            return $this->m_add($data);
        }
    }
}
