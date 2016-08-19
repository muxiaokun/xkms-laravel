<?php
return array(
    'control_group' => '扩展管理',
    'control_info'  => '招聘',
    'tables'        => array(
        'recruit'     => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'title VARCHAR(64) NOT NULL COMMENT "招聘名称"',
                'explains MEDIUMTEXT DEFAULT NULL COMMENT "招聘说明"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
                'current_portion INT(10) UNSIGNED DEFAULT 0 COMMENT "当前简历数"',
                'max_portion INT(10) UNSIGNED DEFAULT 0 COMMENT "最大简历数"',
                'start_time INT(10) UNSIGNED DEFAULT 0 COMMENT "开始时间"',
                'end_time INT(10) UNSIGNED DEFAULT 0 COMMENT "结束时间"',
                'ext_info MEDIUMTEXT DEFAULT NULL COMMENT "输入项目"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
        'recruit_log' => array(
            'table_info' => '记录',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'r_id INT(10) UNSIGNED NOT NULL COMMENT "招聘编号"',
                'add_time INT(10) UNSIGNED NOT NULL COMMENT "添加时间"',
                'name VARCHAR(64) NOT NULL COMMENT "名字"',
                'birthday INT(10) UNSIGNED NOT NULL COMMENT "生日"',
                'sex TINYINT(3) UNSIGNED NOT NULL COMMENT "性别"',
                'certificate TINYINT(3) UNSIGNED NOT NULL COMMENT "结业证"',
                'ext_info MEDIUMTEXT DEFAULT NULL COMMENT "扩展信息"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Recruit'    => array('index' => '招聘管理', 'add' => '新增招聘', 'del' => '删除招聘', 'edit' => '编辑招聘'),
            'RecruitLog' => array('index' => '应聘记录', 'add' => '新增招聘', 'del' => '删除招聘', 'edit' => '编辑招聘'),
        ),
        'Home'  => array(
            'Recruit' => array('index' => '招聘列表', 'add' => '应聘职位', 'edit' => '查看招聘'),
        ),
    ),
);
