<?php
return [
    'control_group' => '成员管理',
    'control_info'  => '管理员',
    'privilege'     => [
        'Admin' => [
            'Admin'      => ['setting' => '管理员配置',
                             'index'   => '管理员管理',
                             'add'     => '添加管理员',
                             'del'     => '删除管理员',
                             'edit'    => '编辑管理员'],
            'AdminGroup' => ['index' => '管理组管理', 'add' => '添加管理组', 'del' => '删除管理组', 'edit' => '编辑管理组'],
            'AdminLog'   => ['index' => '查看管理日志', 'del' => '删除管理日志'],
        ],
    ],
];
