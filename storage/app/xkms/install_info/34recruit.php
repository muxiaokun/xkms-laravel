<?php
return [
    'control_group' => '扩展管理',
    'control_info'  => '招聘',
    'privilege'     => [
        'Admin' => [
            'Recruit'    => ['index' => '招聘管理', 'add' => '新增招聘', 'del' => '删除招聘', 'edit' => '编辑招聘'],
            'RecruitLog' => ['index' => '应聘记录', 'add' => '新增招聘', 'del' => '删除招聘', 'edit' => '编辑招聘'],
        ],
        'Home'  => [
            'Recruit' => ['index' => '招聘列表', 'add' => '应聘职位', 'edit' => '查看招聘'],
        ],
    ],
    'tables'        => [
        'recruits'     => ['table_name' => '招聘'],
        'recruit_logs' => ['table_name' => '应聘记录'],
    ],
];
