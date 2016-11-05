<?php
return [
    'control_group' => '系统管理',
    'control_info'  => '系统配置',
    'privilege'     => [
        'Admin' => [
            'Index'    => [
                'websiteSet'  => '网站配置',
                'systemSet'   => '系统配置',
                'databaseSet' => '数据库配置',
                'editMyPass'  => '修改密码',
                'cleanCache'  => '清空缓存',
                'cleanLog'    => '清除日志',
            ],
            'Template' => ['index' => '模板管理', 'add' => '添加模板', 'del' => '删除模板', 'edit' => '添加模板'],
        ],
    ],
    'tables'        => [
    ],
];
