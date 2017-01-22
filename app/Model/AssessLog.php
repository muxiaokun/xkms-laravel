<?php

namespace App\Model;


class AssessLog extends Common
{
    protected $casts = [
        'score' => 'array',
    ];
}
