<?php

namespace App\Model;


class QuestsAnswer extends Common
{
    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        !isset($data['add_time']) && $data['add_time'] = Carbon::now();
        return parent::mAdd($data);
    }

    //统计答案
    public static function count_quests_answer($questsId, $answer = false)
    {
        if (!$questsId) {
            return false;
        }

        $answer   = ($answer) ? 'answer like "' . $answer . '"' : '1 = 1';
        $countNum = self::where(['quests_id' => $questsId, $answer])->count();
        return $countNum;
    }
}
