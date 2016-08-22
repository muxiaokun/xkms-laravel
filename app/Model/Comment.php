<?php

namespace App\Model;


class Comment extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        $data = $this->field('*,inet_ntoa(add_ip) as aip')->where($where)->select();
        foreach ($data as &$dataRow) {$this->mDecodeData($dataRow);}
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
}
