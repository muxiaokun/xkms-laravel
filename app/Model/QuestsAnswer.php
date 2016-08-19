<?php

namespace App\Model;


class QuestsAnswer extends Common
{
    public function m_add($data)
    {
        if (!$data) {
            return false;
        }

        !isset($data['add_time']) && $data['add_time'] = time();
        return parent::m_add($data);
    }

    //统计答案
    public function count_quests_answer($quests_id, $answer = false)
    {
        if (!$quests_id) {
            return false;
        }

        $answer    = ($answer) ? 'answer like "' . $answer . '"' : '1 = 1';
        $count_num = $this->where(array('quests_id' => $quests_id, $answer))->count();
        return $count_num;
    }
}
