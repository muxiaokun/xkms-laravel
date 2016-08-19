<?php
return array(
    'control_group' => '扩展管理',
    'control_info'  => '问卷',
    'tables'        => array(
        'quests'        => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'title VARCHAR(64) NOT NULL COMMENT "标题"',
                'current_portion INT(10) UNSIGNED DEFAULT 0 COMMENT "当前份数"',
                'max_portion INT(10) UNSIGNED DEFAULT 0 COMMENT "最大份数"',
                'start_content MEDIUMTEXT DEFAULT NULL COMMENT "欢迎词"',
                'end_content MEDIUMTEXT DEFAULT NULL COMMENT "结束词"',
                'start_time INT(10) UNSIGNED NOT NULL COMMENT "开始时间"',
                'end_time INT(10) UNSIGNED NOT NULL COMMENT "结束时间"',
                'access_info MEDIUMTEXT DEFAULT NULL COMMENT "访问配置"',
                'ext_info MEDIUMTEXT DEFAULT NULL COMMENT "后面说明"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
        'quests_answer' => array(
            'table_info' => '答案表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'quests_id INT(10) UNSIGNED NOT NULL COMMENT "问卷编号"',
                'member_id INT(10) UNSIGNED DEFAULT 0 COMMENT "会员编号"',
                'add_time INT(10) UNSIGNED DEFAULT 0 COMMENT "提交时间"',
                'answer MEDIUMTEXT NOT NULL COMMENT "答案编号或内容"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Quests'       => array('index' => '问卷管理', 'add' => '新增问卷', 'del' => '删除问卷', 'edit' => '编辑问卷'),
            'QuestsAnswer' => array('index' => '答案管理', 'del' => '清空答案', 'edit' => '统计问卷'),
        ),
        'Home'  => array(
            'Quests' => array('index' => '问卷管理', 'add' => '填写问卷'),
        ),
    ),
);
