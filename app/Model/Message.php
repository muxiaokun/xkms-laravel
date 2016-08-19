<?php

namespace App\Model;


class Message extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('send_time desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_add($data)
    {
        if (!$data) {
            return false;
        }

        $data['send_time'] = time();
        return parent::m_add($data);
    }
}
