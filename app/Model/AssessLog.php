<?php

namespace App\Model;


class AssessLog extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        if (isset($data['a_id']) && isset($data['grade_id']) && isset($data['re_grade_id'])) {
            $where       = array('a_id' => $data['a_id'], 'grade_id' => $data['grade_id'], 're_grade_id' => $data['re_grade_id']);
            $assess_info = $this->where($where)->find();
        }
        $this->encodeData($data);
        $data['add_time'] = time();
        //是否已经评价 决定编辑还是添加
        if ($assess_info) {
            $result = $this->where($where)->data($data)->save();
        } else {
            $result = $this->add($data);
        }
        return $result;
    }

    protected function encodeData(&$data)
    {
        isset($data['score']) && $data['score'] = serialize($data['score']);
    }

    protected function decodeData(&$data)
    {
        isset($data['score']) && $data['score'] = unserialize($data['score']);
    }
}
