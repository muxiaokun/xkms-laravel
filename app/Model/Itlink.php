<?php

namespace App\Model;


class Itlink extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_find_data($short_name)
    {
        if (!$short_name) {
            return array();
        }

        //显示限制 最大显示次数
        $_string = '(max_show_num = 0 OR show_num < max_show_num)';
        //显示限制 最大点击次数
        $_string .= ' AND (max_hit_num = 0 OR hit_num < max_hit_num)';
        //显示限制 时间范围
        $current_time = time();
        $_string .= ' AND ((start_time = 0 AND end_time = 0) OR (start_time < ' . $current_time . ' AND ' . $current_time . ' < end_time))';
        $where = array(
            'short_name' => $short_name,
            'is_enable'  => 1,
            '_string'    => $_string,
        );
        $itlink_info = $this->where($where)->find();
        $this->_decode_data($itlink_info);
        $links = $itlink_info['ext_info'];
        foreach ($links as &$link) {
            if (0 < $itlink_info['max_hit_num']) {
                $link['itl_link'] = M_U('itlink', array('id' => $itlink_info['id'], 'link' => base64_encode($link['itl_link'])));
            } else {
                $link['itl_link'] = M_str2url($link['itl_link']);
            }
        }
        //只有限制了显示次数才进行计数
        $itlink_info['max_show_num'] > 0 && $this->where(array('id' => $itlink_info['id']))->setInc('show_num');
        return is_array($links) ? $links : array();
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
