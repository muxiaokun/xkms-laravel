<?php

namespace App\Model;


class Assess extends Common
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

    protected function mEncodeData(&$data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = serialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['cfg_info']) && $data['cfg_info'] = unserialize($data['cfg_info']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
