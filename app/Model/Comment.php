<?php

namespace App\Model;


class Comment extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        $data = $this->field('*,inet_ntoa(add_ip) as aip')->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_add($data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = time();
        $data['add_ip']   = array('exp', 'inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")');
        return parent::m_add($data);
    }
}
