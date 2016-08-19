<?php

namespace App\Model;


class Recruit extends Common
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
        isset($data['explains']) && $data['explains'] = $this->_encode_content($data['explains']);
        if (isset($data['ext_info']) && is_array($data['ext_info'])) {
            $new_ext_info = array();
            foreach ($data['ext_info'] as $key => $value) {
                $new_ext_info[] = $key . ':' . $value;
            }
            $data['ext_info'] = '|' . implode('|', $new_ext_info) . '|';
        }
    }

    protected function _decode_data(&$data)
    {
        if (isset($data['ext_info']) && $data['ext_info']) {
            $data['ext_info'] = explode('|', substr($data['ext_info'], 1, strlen($data['ext_info']) - 2));
            $new_ext_info     = array();
            foreach ($data['ext_info'] as $value_str) {
                list($key, $value)  = explode(':', $value_str);
                $new_ext_info[$key] = $value;
            }
            $data['ext_info'] = $new_ext_info;
        }
    }
}
