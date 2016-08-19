<?php
return array(
    'control_group' => '扩展管理',
    'control_info'  => '考核',
    'tables'        => array(
        'assess'     => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'title VARCHAR(64) NOT NULL COMMENT "考核表名称"',
                'explains VARCHAR(256) DEFAULT NULL COMMENT "考核说明"',
                'group_level TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "定义考核分组级别"',
                'start_time INT(10) UNSIGNED DEFAULT 0 COMMENT "开始时间"',
                'end_time INT(10) UNSIGNED DEFAULT 0 COMMENT "结束时间"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
                'target VARCHAR(64) DEFAULT NULL COMMENT "考核目标"',
                'ext_info MEDIUMTEXT DEFAULT NULL COMMENT "条目数组"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
        'assess_log' => array(
            'table_info' => '记录',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'assess_id INT(10) UNSIGNED NOT NULL COMMENT "考核表编号"',
                'grade_id INT(10) UNSIGNED NOT NULL COMMENT "评分人"',
                're_grade_id INT(10) UNSIGNED NOT NULL COMMENT "被评分人"',
                'add_time INT(10) UNSIGNED DEFAULT 0 COMMENT "添加时间"',
                'score text NOT NULL COMMENT "数组"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Assess'    => array('index' => '考核管理', 'add' => '新增考核', 'del' => '删除考核', 'edit' => '编辑考核'),
            'AssessLog' => array('edit' => '统计记录', 'del' => '删除记录'),
        ),
        'Home'  => array(
            'Assess' => array('index' => '考核管理', 'add' => '考核评分'),
        ),
    ),
);
