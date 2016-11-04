<?php

namespace App\Model;


class Itlink extends Common
{
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

    public function scopeMEncodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
