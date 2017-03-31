<?php

namespace App\Model;

use Carbon\Carbon;


class Itlink extends Common
{
    public function getExtInfoAttribute($value)
    {
        $value = json_decode($value, true);
        if (!is_array($value)) {
            return [];
        }

        foreach ($value as &$info) {
            $info['itl_image'] = mMakeUploadUrl($info['itl_image']);
        }

        return $value;
    }

    public function setExtInfoAttribute($value)
    {
        foreach ($value as &$info) {
            $info['itl_image'] = mParseUploadUrl($info['itl_image']);
        }

        $this->attributes['ext_info'] = json_encode($value);
    }


    public function scopeMFindData($query, $shortName)
    {
        if (!$shortName) {
            return [];
        }

        $query->where(function ($query) {
            $query->orWhere('max_show_num', '=', 0);
            $query->orWhereColumn('show_num', '<', 'max_show_num');
        });

        $query->where(function ($query) {
            $query->orWhere('max_hit_num', '=', 0);
            $query->orWhereColumn('hit_num', '<', 'max_hit_num');
        });

        $query->where(function ($query) {
            $query->orWhere(function ($query) {
                $query->whereNull('start_time');
                $query->whereNull('end_time');
            });
            $query->orWhere(function ($query) {
                $currentTime = Carbon::now();
                $query->where('start_time', '<', $currentTime);
                $query->where('end_time', '>', $currentTime);
            });
        });

        $itlinkInfo = $query->where(['is_enable' => 1, 'short_name' => $shortName])->first();
        $links      = [];
        if (null !== $itlinkInfo && isset($itlinkInfo['ext_info'])) {
            $links = $itlinkInfo['ext_info'];
            foreach ($links as &$link) {
                $link['itl_link'] = mStr2url($link['itl_link']);
                if (0 < $itlinkInfo['max_hit_num']) {
                    $link['itl_link'] = route('Home::Itlink::index',
                        ['id' => $itlinkInfo['id'], 'link' => base64_encode($link['itl_link'])]);
                }
            }
            //只有限制了显示次数才进行计数
            $itlinkInfo['max_show_num'] > 0 && Itlink::colWhere($itlinkInfo['id'])->increment('show_num');

        }
        return collect($links);
    }
}
