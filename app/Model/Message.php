<?php

namespace App\Model;


class Message extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('send_time desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['send_time'] = time();
        return parent::mAdd($data);
    }
}
