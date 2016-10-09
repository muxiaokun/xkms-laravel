<?php

namespace App\Model;


class Navigation extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->page($page)->select();
        foreach ($data as &$dataRow) {
            $this->mDecodeData($dataRow);
        }
        return $data;
    }

    public function mFind_data($shortName)
    {
        if (!$shortName) {
            return [];
        }

        $navigation = $this->where(['is_enable' => 1, 'short_name' => $shortName])->find();
        $this->mDecodeData($navigation);
        $navigationData = $this->_decode_navigation_data($navigation['ext_info']);
        return ($navigationData) ? $navigationData : [];
    }

    private function _decode_navigation_data($navigationData)
    {
        $navigationData = json_decode($navigationData, true);
        foreach ($navigationData as &$nav) {
            $nav['nav_active'] = false;
            $nav['nav_link']   = M_str2url($nav['nav_link']);
            if (false !== stripos($nav['nav_link'], __SELF__)) {
                $nav['nav_active'] = true;
            }
            if ($nav && $nav['nav_child']) {
                $nav['nav_child'] = $this->_decode_navigation_data($nav['nav_child']);
                foreach ($nav['nav_child'] as $navChild) {
                    if ($navChild['nav_active']) {
                        $nav['nav_active'] = true;
                    }

                }
            }
        }
        return $navigationData;
    }

    protected function mEncodeData(&$data)
    {
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
