<?php

namespace App\Model;


class QuestsAnswer extends Common
{
    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        !isset($data['add_time']) && $data['add_time'] = time();
        return parent::mAdd($data);
    }

    //统计答案
    public function count_quests_answer($questsId, $answer = false)
    {
        if (!$questsId) {
            return false;
        }

        $answer    = ($answer) ? 'answer like "' . $answer . '"' : '1 = 1';
        $countNum = $this->where(array('quests_id' => $questsId, $answer))->count();
        return $countNum;
    }
}
