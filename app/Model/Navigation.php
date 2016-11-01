<?php

namespace App\Model;


class Navigation extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        static::mGetPage($page);
        null !== static::option['order'] && static::order('id desc');
        $data = static::where($where)->page($page)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mFind_data($shortName)
    {
        if (!$shortName) {
            return [];
        }

        $navigation = static::where(['is_enable' => 1, 'short_name' => $shortName])->first();
        (new static)->mDecodeData($navigation);
        $navigationData = static::_decode_navigation_data($navigation['ext_info']);
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
                $nav['nav_child'] = static::_decode_navigation_data($nav['nav_child']);
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
