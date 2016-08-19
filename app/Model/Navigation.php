<?php

namespace App\Model;


class Navigation extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->page($page)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_find_data($short_name)
    {
        if (!$short_name) {
            return array();
        }

        $navigation = $this->where(array('is_enable' => 1, 'short_name' => $short_name))->find();
        $this->_decode_data($navigation);
        $navigation_data = $this->_decode_navigation_data($navigation['ext_info']);
        return ($navigation_data) ? $navigation_data : array();
    }

    private function _decode_navigation_data($navigation_data)
    {
        $navigation_data = json_decode($navigation_data, true);
        foreach ($navigation_data as &$nav) {
            $nav['nav_active'] = false;
            $nav['nav_link']   = M_str2url($nav['nav_link']);
            if (false !== stripos($nav['nav_link'], __SELF__)) {
                $nav['nav_active'] = true;
            }
            if ($nav && $nav['nav_child']) {
                $nav['nav_child'] = $this->_decode_navigation_data($nav['nav_child']);
                foreach ($nav['nav_child'] as $nav_child) {
                    if ($nav_child['nav_active']) {
                        $nav['nav_active'] = true;
                    }

                }
            }
        }
        return $navigation_data;
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
