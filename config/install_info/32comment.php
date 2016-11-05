<?php
return [
    'control_group' => '扩展管理',
    'control_info'  => '评论',
    'privilege'     => [
        'Admin' => [
            'Comment' => ['index' => '评论管理', 'add' => '审核评论', 'del' => '删除评论', 'edit' => '回复评论'],
        ],
    ],
    'tables'        => [
        'comments' => ['table_name' => '评论'],
    ],
];
