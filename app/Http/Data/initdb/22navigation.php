<?php
return array(
    'control_group' => '内容管理',
    'control_info'  => '导航',
    'tables'        => array(
        'navigation' => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'name VARCHAR(128) DEFAULT NULL COMMENT "名称"',
                'short_name VARCHAR(32) NOT NULL UNIQUE COMMENT "调用短名"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
                'ext_info MEDIUMTEXT DEFAULT NULL COMMENT "扩展信息存储区"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Navigation' => array('index' => '导航管理', 'add' => '添加导航', 'del' => '删除导航', 'edit' => '编辑导航'),
        ),
    ),
);
