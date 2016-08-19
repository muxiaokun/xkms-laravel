<?php

namespace App\Model;


class RecruitLog extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_add($data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = time();
        return parent::m_add($data);
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
