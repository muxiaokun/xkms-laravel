<?php
return array(
    'control_group' => '系统管理',
    'control_info'  => '管理上传',
    'tables'        => array(
        'manage_upload' => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'user_id INT(10) UNSIGNED NOT NULL COMMENT "上传用户"',
                'user_type INT(10) UNSIGNED NOT NULL COMMENT "用户类型"',
                'name VARCHAR(64) NOT NULL COMMENT "文件名称"',
                'add_time INT(10) UNSIGNED NOT NULL COMMENT "上传时间"',
                'path VARCHAR(256) NOT NULL COMMENT "文件路径"',
                'mime VARCHAR(64) DEFAULT NULL COMMENT "mime类型"',
                'size INT(10) UNSIGNED DEFAULT 0 COMMENT "大小"',
                'suffix VARCHAR(32) DEFAULT NULL COMMENT "后缀"',
                'bind_info MEDIUMTEXT DEFAULT NULL COMMENT "绑定信息存储区"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'ManageUpload' => array('index' => '文件管理', 'del' => '删除文件', 'edit' => '清除未用'),
        ),
    ),
);
