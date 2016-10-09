<?php

namespace App\Model;


class AssessLog extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        $data = $this->where($where)->select();
        foreach ($data as &$dataRow) {
            $this->mDecodeData($dataRow);
        }
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        if (isset($data['a_id']) && isset($data['grade_id']) && isset($data['re_grade_id'])) {
            $where      = ['a_id'        => $data['a_id'],
                           'grade_id'    => $data['grade_id'],
                           're_grade_id' => $data['re_grade_id'],
            ];
            $assessInfo = $this->where($where)->find();
        }
        $this->mEncodeData($data);
        $data['add_time'] = time();
        //是否已经评价 决定编辑还是添加
        if ($assessInfo) {
            $result = $this->where($where)->data($data)->save();
        } else {
            $result = $this->add($data);
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
