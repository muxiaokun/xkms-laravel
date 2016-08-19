<?php
return array(
    'control_group' => '内容管理',
    'control_info'  => '图文链接',
    'tables'        => array(
        'itlink' => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'name VARCHAR(128) DEFAULT NULL COMMENT "名称"',
                'short_name VARCHAR(32) NOT NULL UNIQUE COMMENT "调用短名"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
                'start_time INT(10) UNSIGNED DEFAULT 0 COMMENT "开始时间"',
                'end_time INT(10) UNSIGNED DEFAULT 0 COMMENT "结束时间"',
                'max_show_num INT(10) UNSIGNED DEFAULT 0 COMMENT "最大显示数量"',
                'show_num INT(10) UNSIGNED DEFAULT 0 COMMENT "显示数量"',
                'max_hit_num INT(10) UNSIGNED DEFAULT 0 COMMENT "最大点击数"',
                'hit_num INT(10) UNSIGNED DEFAULT 0 COMMENT "点击数"',
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
            'Itlink' => array('index' => '图文管理', 'add' => '添加图文', 'del' => '删除图文', 'edit' => '编辑图文'),
        ),
    ),
);
