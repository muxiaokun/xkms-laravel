<?php

namespace App\Model;


class AssessLog extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        !isset(self::options['order']) && self::order('add_time desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            self::mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        if (isset($data['a_id']) && isset($data['grade_id']) && isset($data['re_grade_id'])) {
            $where      = ['a_id'        => $data['a_id'],
                           'grade_id'    => $data['grade_id'],
                           're_grade_id' => $data['re_grade_id'],
            ];
            $assessInfo = self::where($where)->first();
        }
        self::mEncodeData($data);
        $data['add_time'] = Carbon::now();
        //是否已经评价 决定编辑还是添加
        if ($assessInfo) {
            $result = self::where($where)->data($data)->save();
        } else {
            $result = self::add($data);
        }
        return $result;
    }

    protected static function mEncodeData(&$data)
    {
        isset($data['score']) && $data['score'] = serialize($data['score']);
    }

    protected static function mDecodeData(&$data)
    {
        isset($data['score']) && $data['score'] = unserialize($data['score']);
    }
}
