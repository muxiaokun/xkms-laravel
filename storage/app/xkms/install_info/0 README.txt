XK Management System Initialize Directory
Note:文件以数字开头排序
0 代表必须安装
1 本系统主功能
2 本系统插件
3 第三方

done是 已完成 的功能数据库和权限文件
done是 未完成 的功能数据库和权限文件

功能权限数据库模板和正则
(.+)\t(.+)\t(.*)\t(.*)
'\1 \2 \3 COMMENT "\4"',
return array(
    'control_info'=>'管理员',
    'tables'=>array(
		'admin_manage'=>array(
            'table_info'=>'表名称',
            'column'=>array(
				'数据列信息',
            ),
            'insert_row'=>array(
                '安装时默认插入的数据() VALUES()',
            ),
        ),
	),
	'privilege'=>array(
		'分组名称'=>array(
			'控制器名称'=>array('index'=>'方法名称'),
	),
),
