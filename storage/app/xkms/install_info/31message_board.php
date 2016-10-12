<?php
return [
    'control_group' => '扩展管理',
    'control_info'  => '留言板',
    'privilege'     => [
        'Admin' => [
            'MessageBoard'    => ['index' => '留言板管理', 'add' => '添加留言板', 'del' => '删除留言板', 'edit' => '编辑留言板'],
            'MessageBoardLog' => ['index' => '留言管理', 'del' => '删除留言', 'edit' => '审核回复'],
        ],
        'Home'  => [
            'MessageBoard' => ['index' => '留言板'],
        ],
    ],
    'tables'        => [
        'message_boards'     => ['table_name' => '站内信'],
        'message_board_logs' => ['table_name' => '站内信'],
    ],
];
