<?php
return [
    'control_group' => '内容管理',
    'control_info'  => '图文链接',
    'privilege'     => [
        'Admin' => [
            'Itlink' => ['index' => '图文管理', 'add' => '添加图文', 'del' => '删除图文', 'edit' => '编辑图文'],
        ],
    ],
    'tables'        => [
        'itlinks' => ['table_name' => '图文链接'],
    ],
];
