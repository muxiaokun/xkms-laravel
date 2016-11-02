<?php

namespace App\Model;


class AssessLog extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->order('add_time desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMAdd($query, $data)
    {
        if (!$data) {
            return false;
        }

        if (isset($data['a_id']) && isset($data['grade_id']) && isset($data['re_grade_id'])) {
            $where      = [
                'a_id'        => $data['a_id'],
                'grade_id'    => $data['grade_id'],
                're_grade_id' => $data['re_grade_id'],
            ];
            $assessInfo = $query->where($where)->first();
        }
        $query->mEncodeData($data);
        $data['add_time'] = Carbon::now();
        //是否已经评价 决定编辑还是添加
        if ($assessInfo) {
            $result = $query->where($where)->data($data)->save();
        } else {
            $result = $query->add($data);
        }
        return $result;
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['score']) && $data['score'] = serialize($data['score']);
        return $data;
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['score']) && $data['score'] = unserialize($data['score']);
        return $data;
    }
}
