<?php

namespace App\Model;


class MessageBoard extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function mDel($id)
    {
        //不能删除默认的留言板
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return parent::mDel($id);
    }

    protected function encodeData(&$data)
    {
        isset($data['config']) && $data['config'] = serialize($data['config']);
    }

    protected function decodeData(&$data)
    {
        isset($data['config']) && $data['config'] = unserialize($data['config']);
    }
}
