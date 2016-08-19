<?php

namespace App\Model;


class MessageBoard extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_del($id)
    {
        //不能删除默认的留言板
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return parent::m_del($id);
    }

    protected function _encode_data(&$data)
    {
        isset($data['config']) && $data['config'] = serialize($data['config']);
    }

    protected function _decode_data(&$data)
    {
        isset($data['config']) && $data['config'] = unserialize($data['config']);
    }
}
