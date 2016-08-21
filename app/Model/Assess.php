<?php

namespace App\Model;


class Assess extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    protected function encodeData(&$data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = serialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    protected function decodeData(&$data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = unserialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
