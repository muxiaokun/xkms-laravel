<?php

namespace App\Model;


class Quests extends Common
{
    protected $casts = [
        'access_info' => 'array',
        'ext_info'    => 'array',
    ];
}
