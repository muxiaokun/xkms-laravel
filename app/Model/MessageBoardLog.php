<?php

namespace App\Model;


class MessageBoardLog extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        $data = $this->field('*,inet_ntoa(add_ip) as aip')->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = time();
        $data['add_ip']   = array('exp', 'inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")');
        return parent::mAdd($data);
    }

    public function mFind($id)
    {
        $this->field('*,inet_ntoa(add_ip) as aip');
        return parent::mFind($id);
    }

    public function check_dont_submit($second)
    {
        $second = time() - $second;
        $where  = $second . ' < add_time AND add_ip = inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")';
        return ($this->where($where)->count()) ? true : false;
    }

    protected function encodeData(&$data)
    {
        isset($data['send_info']) && $data['send_info'] = serialize($data['send_info']);
    }

    protected function decodeData(&$data)
    {
        isset($data['send_info']) && $data['send_info'] = unserialize($data['send_info']);
    }
}
