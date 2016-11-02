<?php

namespace App\Model;


class Navigation extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->order('id desc');
        $data = $query->where($where)->page($page)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMFind_data($query, $shortName)
    {
        if (!$shortName) {
            return [];
        }

        $navigation = $query->where(['is_enable' => 1, 'short_name' => $shortName])->first();
        $query->mDecodeData($navigation);
        $navigationData = $query->_decode_navigation_data($navigation['ext_info']);
        return ($navigationData) ? $navigationData : [];
    }

    private function _decode_navigation_data($query, $navigationData)
    {
        $navigationData = json_decode($navigationData, true);
        foreach ($navigationData as &$nav) {
            $nav['nav_active'] = false;
            $nav['nav_link']   = M_str2url($nav['nav_link']);
            if (false !== stripos($nav['nav_link'], __SELF__)) {
                $nav['nav_active'] = true;
            }
            if ($nav && $nav['nav_child']) {
                $nav['nav_child'] = $query->_decode_navigation_data($nav['nav_child']);
                foreach ($nav['nav_child'] as $navChild) {
                    if ($navChild['nav_active']) {
                        $nav['nav_active'] = true;
                    }

                }
            }
        }
        return $navigationData;
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
        return $data;
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
        return $data;
    }
}
