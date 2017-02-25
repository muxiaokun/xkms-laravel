<?php

namespace App\Model;


class Navigation extends Common
{
    protected $casts = [
        'ext_info' => 'array',
    ];

    public function scopeFindData($query, $shortName)
    {
        if (!$shortName) {
            return [];
        }

        $navigation = $query->where(['is_enable' => 1, 'short_name' => $shortName])->first();
        $currentFullUrl = request()->fullUrl();
        $navigationData = [];
        if (is_array($navigation['ext_info'])) {
            $navigationData = (new static)->_make_navigation($navigation['ext_info'], $currentFullUrl);
        }
        return collect($navigationData);
    }

    private function _make_navigation($navigationData, $currentFullUrl)
    {
        foreach ($navigationData as &$nav) {
            $nav['nav_active'] = false;
            $nav['nav_link'] = mStr2url($nav['nav_link']);
            if (false !== stripos($nav['nav_link'], $currentFullUrl)) {
                $nav['nav_active'] = true;
            }
            if ($nav && $nav['nav_child']) {
                $nav['nav_child'] = $this->_make_navigation($nav['nav_child'], $currentFullUrl);
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
