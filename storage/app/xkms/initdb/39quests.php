<?php
return [
    'control_group' => '扩展管理',
    'control_info'  => '问卷',
    'privilege'     => [
        'Admin' => [
            'Quests'       => ['index' => '问卷管理', 'add' => '新增问卷', 'del' => '删除问卷', 'edit' => '编辑问卷'],
            'QuestsAnswer' => ['index' => '答案管理', 'del' => '清空答案', 'edit' => '统计问卷'],
        ],
        'Home'  => [
            'Quests' => ['index' => '问卷管理', 'add' => '填写问卷'],
        ],
    ],
];
