<?php

namespace App\Model;


class Itlink extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        null !== self::option['order'] && self::order('id desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            (new self)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mFind_data($shortName)
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
        $itlinkInfo = self::where($where)->first();
        (new self)->mDecodeData($itlinkInfo);
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
        $itlinkInfo['max_show_num'] > 0 && self::where(['id' => $itlinkInfo['id']])->setInc('show_num');
        return is_array($links) ? $links : [];
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
