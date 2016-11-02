<?php

namespace App\Model;


class QuestsAnswer extends Common
{
    public function scopeMAdd($query, $data)
    {
        if (!$data) {
            return false;
        }

        !isset($data['add_time']) && $data['add_time'] = Carbon::now();
        return $query->mAdd($data);
    }

    //统计答案
    public function count_quests_answer($query, $questsId, $answer = false)
    {
        if (!$questsId) {
            return false;
        }

        $answer   = ($answer) ? 'answer like "' . $answer . '"' : '1 = 1';
        $countNum = $query->where(['quests_id' => $questsId, $answer])->count();
        return $countNum;
    }
}
