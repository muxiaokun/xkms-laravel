<?php
return [
    'control_group' => '内容管理',
    'control_info'  => '文章',
    'privilege'     => [
        'Admin' => [
            'Article' => ['setting' => '文章配置',
                          'index'   => '文章管理',
                          'add'     => '添加文章',
                          'del'     => '删除文章',
                          'edit'    => '编辑文章'],
            'ArticleCategory' => ['index' => '文章分类', 'add' => '添加分类', 'del' => '删除分类', 'edit' => '编辑分类'],
            'ArticleChannel' => ['index' => '文章频道', 'add' => '添加频道', 'del' => '删除频道', 'edit' => '编辑频道'],
        ],
    ],
];
