<?php

namespace App\Model;


class Quests extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    protected function _encode_data(&$data)
    {
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    protected function _decode_data(&$data)
    {
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
