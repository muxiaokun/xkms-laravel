<?php
return [
    'control_group' => '成员管理',
    'control_info'  => '会员管理',
    'privilege'     => [
        'Admin' => [
            'Member' => ['setting' => '会员配置',
                         'index'   => '会员管理',
                         'add'     => '添加会员',
                         'del'     => '删除会员',
                         'edit'    => '编辑会员'],
            'MemberGroup' => ['index' => '会员组管理', 'add' => '添加会员组', 'del' => '删除会员组', 'edit' => '编辑会员组'],
        ],
        'Home'  => [
            'Member' => ['index' => '会员中心', 'edit' => '编辑会员'],
        ],
    ],
];
