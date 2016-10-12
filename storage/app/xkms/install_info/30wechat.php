<?php
return [
    'control_group' => '扩展管理',
    'control_info'  => '微信',
    'privilege'     => [
        'Admin' => [
            'Wechat' => ['index' => '微信管理', 'add' => '配置微信', 'del' => '删除微信绑定', 'edit' => '推送消息'],
        ],
    ],
    'tables'        => [
        'wechats' => ['table_name' => '微信'],
    ],
];
