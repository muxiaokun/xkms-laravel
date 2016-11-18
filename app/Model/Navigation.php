<?php

namespace App\Model;


class Navigation extends Common
{
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
}
