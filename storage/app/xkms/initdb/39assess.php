<?php
return [
    'control_group' => '扩展管理',
    'control_info'  => '考核',
    'privilege'     => [
        'Admin' => [
            'Assess'    => ['index' => '考核管理', 'add' => '新增考核', 'del' => '删除考核', 'edit' => '编辑考核'],
            'AssessLog' => ['edit' => '统计记录', 'del' => '删除记录'],
        ],
        'Home'  => [
            'Assess' => ['index' => '考核管理', 'add' => '考核评分'],
        ],
    ],
];
