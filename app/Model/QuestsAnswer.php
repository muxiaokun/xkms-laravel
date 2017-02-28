<?php

namespace App\Model;


class QuestsAnswer extends Common
{
    protected $casts = [
        'answer' => 'array',
    ];

    //统计答案
    public function scopeWhereQuestsAnswer($query, $questsId, $answer = false)
    {
        $query->where('quests_id', '=', $questsId);

        if ($answer) {
            $query->where('answer', 'like', $answer);
        }

        return $query;
    }
}
