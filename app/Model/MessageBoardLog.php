<?php

namespace App\Model;


class MessageBoardLog extends Common
{
    protected $casts = [
        'send_info' => 'array',
    ];
}
