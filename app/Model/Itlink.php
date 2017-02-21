<?php

namespace App\Model;


class Itlink extends Common
{
    public function setMaxShowNumAttribute($value)
    {
        $this->attributes['max_show_num'] = $value ? $value : 0;
    }

    public function setMaxHitNumAttribute($value)
    {
        $this->attributes['max_hit_num'] = $value ? $value : 0;
    }

    public function setShowNumAttribute($value)
    {
        $this->attributes['show_num'] = $value ? $value : 0;
    }

    public function setHitNumAttribute($value)
    {
        $this->attributes['hit_num'] = $value ? $value : 0;
    }

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


    public function scopeMFind_data($query, $shortName)
    {
        if (!$shortName) {
            return [];
        }

        //显示限制 最大显示次数
        $whereString = '(max_show_num = 0 OR show_num < max_show_num)';
        //显示限制 最大点击次数
        $whereString .= ' AND (max_hit_num = 0 OR hit_num < max_hit_num)';
        //显示限制 时间范围
        $currentTime = Carbon::now();
        $whereString .= ' AND ((start_time = 0 AND end_time = 0) OR (start_time < ' . $currentTime . ' AND ' . $currentTime . ' < end_time))';
        $where      = [
            'short_name' => $shortName,
            'is_enable'  => 1,
            '_string'    => $whereString,
        ];
        $itlinkInfo = $query->where($where)->first();
        $query->mDecodeData($itlinkInfo);
        $links = $itlinkInfo['ext_info'];
        foreach ($links as &$link) {
            if (0 < $itlinkInfo['max_hit_num']) {
                $link['itl_link'] = M_U('itlink',
                    ['id' => $itlinkInfo['id'], 'link' => base64_encode($link['itl_link'])]);
            } else {
                $link['itl_link'] = M_str2url($link['itl_link']);
            }
        }
        //只有限制了显示次数才进行计数
        $itlinkInfo['max_show_num'] > 0 && $query->where(['id' => $itlinkInfo['id']])->setInc('show_num');
        return is_array($links) ? $links : [];
    }
}
