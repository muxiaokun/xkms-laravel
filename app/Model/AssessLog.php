<?php

namespace App\Model;


class AssessLog extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        static::mGetPage($page);
        null !== static::option['order'] && static::order('add_time desc');
        $data = static::where($where)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
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
            $assessInfo = static::where($where)->first();
        }
        (new static)->mEncodeData($data);
        $data['add_time'] = Carbon::now();
        //是否已经评价 决定编辑还是添加
        if ($assessInfo) {
            $result = static::where($where)->data($data)->save();
        } else {
            $result = static::add($data);
        }
        return $result;
    }

    protected function mEncodeData(&$data)
    {
        isset($data['score']) && $data['score'] = serialize($data['score']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['score']) && $data['score'] = unserialize($data['score']);
    }
}
