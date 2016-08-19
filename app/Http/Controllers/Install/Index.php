<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
// 安装 初始化 重置 系统数据库

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Common;

define('XKMS_ARTICLES', APP_PATH . 'Data/article/'); //申明系统文章在的目录
define('XKMS_TABLES', APP_PATH . 'Data/initdb/'); //申明表数组在的目录
define('XKMS_DEFAULT_CONFIG', APP_PATH . 'Data/config/'); //申明数据库默认配置的目录
class Index extends Common
{
    public function _initialize()
    {
        $loaded_ext = get_loaded_extensions();
        //没有加载mysql模块不能进行安装
        if (!in_array('pdo_mysql', $loaded_ext)) {
            die('require pdo_mysql extension!!!');
        }

        parent::_initialize();
        /* 安装流程
         * 1.链接数据库检测配置是否可用
         * 2.检测已安装数据，是否覆盖式安装
         */
        //检测安装状态和系统版本
        //install_status 0 没有安装 1安装完毕但没有显示最后一页 2安装完毕
        $status = F('sys_status');
        if (!APP_DEBUG && 1 == $status['install_status'] && 'setp4' != ACTION_NAME) {$this->error(L('install_error1'), U('Install/Index/index', array('setp' => 4)));return;}
        if (!APP_DEBUG && 2 == $status['install_status']) {$this->error(L('install_error2'), U('Home/Index/index'));return;}
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allow_ajax_api = array('get_data');
        if (!in_array(I('type'), $allow_ajax_api)) {
            return;
        }

        $this->_ajax_api();
    }

    //第一页 欢迎页
    public function index()
    {
        $this->assign('title', L('index_title'));
        $this->display();
    }

    public function setp0()
    {
        $this->assign('article', htmlspecialchars_decode(F('licenses', '', XKMS_ARTICLES)));
        $this->assign('setp', L('welcome') . L('use') . APP_NAME);
        $this->assign('title', L('setp0_title'));
        $this->display();
    }

    //第二页 检测扩展模块，设置数据库
    public function setp1()
    {
        $loaded_ext = get_loaded_extensions();
        //检测有可能会依赖的扩展模块
        $must_ext = array('iconv', 'json', 'mcrypt', 'session', 'PDO', 'bz2', 'openssl',
            'curl', 'fileinfo', 'gd', 'mbstring', 'pdo_mysql');
        $unload_ext = array();
        foreach ($must_ext as $ext) {
            if (!in_array($ext, $loaded_ext)) {
                $unload_ext[] = $ext;
            }

        }
        $this->assign('note', $unload_ext);
        //读取默认数据库配置
        $this->assign('database_list', $this->_compare_database());
        $default_config = include XKMS_DEFAULT_CONFIG . 'database.php';
        if (C('DB_HOST')) {
            $default_config['DB_HOST'] = C('DB_HOST');
        }

        if (C('DB_NAME')) {
            $default_config['DB_NAME'] = C('DB_NAME');
        }

        if (C('DB_USER')) {
            $default_config['DB_USER'] = C('DB_USER');
        }

        if (C('DB_PWD')) {
            $default_config['DB_PWD'] = C('DB_PWD');
        }

        if (C('DB_PORT')) {
            $default_config['DB_PORT'] = C('DB_PORT');
        }

        if (C('DB_PREFIX')) {
            $default_config['DB_PREFIX'] = C('DB_PREFIX');
        }

        $this->assign('default_config', $default_config); //读取默认数据库配置
        $this->assign('setp', L('pfsetp', array('setp' => L('one'), 'count' => L('four'))));
        $this->assign('title', L('setp1_title'));
        $this->display();
    }

    //第三页 安装数据库
    public function setp2()
    {
        $this->assign('compare_database_info', $this->_compare_database(true));
        $this->assign('compare_tables_info', $this->_compare_tables());
        $this->assign('setp', L('pfsetp', array('setp' => L('two'), 'count' => L('four'))));
        $this->assign('title', L('setp2_title'));
        $this->display();
    }

    //第三页 安装数据库 提示
    public function setp3()
    {
        //非法提交 返回上一步
        if (!IS_POST) {
            $this->redirect('setp2');
        }

        set_time_limit(0);
        $this->assign('setp', L('pfsetp', array('setp' => L('three'), 'count' => L('four'))));
        $this->assign('title', L('setp3_title'));
        $this->display();
        ob_flush();
        flush();
        //初始化需要安装的数据库
        $compare_database_info = $this->_compare_database(true);
        if (1 != $compare_database_info['if_exists']) {
            try
            {
                $db = M('', '', array(
                    'db_type' => 'mysql',
                    'db_host' => C('DB_HOST'),
                    'db_user' => C('DB_USER'),
                    'db_pwd'  => C('DB_PWD'),
                    'db_port' => C('DB_PORT'),
                ))->db()->connect();
            } catch (\Think\Exception $e) {
                $error_msg = mb_convert_encoding($e->getMessage(), 'utf-8', array('gbk', 'utf-8'));
            }
            $result = $db->query('CREATE DATABASE `' . C('DB_NAME') . '` DEFAULT CHARACTER SET = utf8');

            $lang_str = L('install') . L('database') . C('DB_NAME');
            if ($result) {
                $lang_str .= L('success');
                $type = 'success';
            } else {
                $lang_str .= L('error');
                $error_msg = $db->errorInfo();
                $lang_str .= L('error') . $error_msg[0] . $error_msg[1] . $error_msg[2];
                $type = 'danger';
            }
            echo '<script type="text/javascript">show_install_message("#show_box","' . $lang_str . '","' . $type . '")</script>';
        } else {
            $lang_str = L('database') . C('DB_NAME') . L('exists');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $lang_str . '","info")</script>';
        }
        //初始化需要安装的数据表
        $compare_tables_info = $this->_compare_tables(true);
        //必装的表有6个
        if (6 > count($compare_tables_info)) {
            $lang_str = L('setp3_commont1');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $lang_str . '","danger")</script>';
            echo '<script type="text/javascript">setTimeout(\'window.location.href=\"' . U('', array('setp' => 2)) . '\"\',3000)</script>';
            return;
        }

        try
        {
            $db = M('', '', array(
                'db_type' => 'mysql',
                'db_host' => C('DB_HOST'),
                'db_user' => C('DB_USER'),
                'db_pwd'  => C('DB_PWD'),
                'db_port' => C('DB_PORT'),
                'db_name' => C('DB_NAME'),
            ))->db()->connect();
            $db->query('set names utf8');
        } catch (\Think\Exception $e) {
            $error_msg = mb_convert_encoding($e->getMessage(), 'utf-8', array('gbk', 'utf-8'));
        }

        $create_tables_success = 0;
        $create_tables_error   = 0;
        //清空权限缓存
        F('privilege', null);
        $install_control = I('install_control');

        $tables_count         = 0;
        $install_tables_count = 0;
        foreach ($compare_tables_info as $control_index => $control) {
            //跳过未选中的控制器数据表 不能跳过必装的控制器0-1
            if (!in_array($control_index, $install_control['install']) && 1 < $control['category']) {
                continue;
            }
            $tables_count += count($control['tables']);
        }

        foreach ($compare_tables_info as $control_index => $control) {
            //跳过未选中的控制器数据表 不能跳过必装的控制器0-1
            if (!in_array($control_index, $install_control['install']) && 1 < $control['category']) {
                continue;
            }

            //安装表
            foreach ($control['tables'] as $table_name => $table) {
                $reset = in_array($table_name, $install_control['reset']);
                //由于有默认数据的自动写入重置必须执行
                if ($reset) {
                    $db->query("DROP TABLE IF EXISTS " . $table_name);
                }

                $result = $db->query($table['create_sql']);

                $lang_str = L('install') . L('controller') . '&nbsp;' . $control['control_info'] . '&nbsp;' . L('table') . $table['table_info'] . $table_name;
                if ($result) {
                    $create_tables_success++;
                    $lang_str .= L('success');
                    $type = 'success';
                } else {
                    $create_tables_error++;
                    $error_msg = $db->errorInfo();
                    $lang_str .= L('error') . $error_msg[0] . $error_msg[1] . $error_msg[2];
                    $type = 'danger';
                }
                echo '<script type="text/javascript">show_install_progress(' . ($install_tables_count++ / $tables_count) . ');</script>';
                echo '<script type="text/javascript">show_install_message("#show_box","' . $lang_str . '","' . $type . '")</script>';
                ob_flush();
                flush();
                //表不存在或者选择重置将初始化数据
                if ((!$table['if_exists'] || $reset) && 0 < count($table['insert_row'])) {
                    foreach ($table['insert_row'] as $insert_row) {
                        $db->exec('INSERT INTO ' . $table_name . $insert_row);
                    }
                }
            }
            //安装权限
            $privilege = F('privilege');
            foreach ($control['privilege'] as $group => $control_priv) {
                foreach ($control_priv as $control_name => $action_priv) {
                    $privilege[$group][$control['control_group']][$control_name] = $action_priv;
                }
            }
            F('privilege', $privilege);
        }

        //返回安装报告
        $lang_str = L('success') . L('initialize') . $create_tables_success . L('table') . L('error') . $create_tables_error . L('table');
        if (0 == $create_tables_error) {
            $lang_str .= L('three_second_next_setp');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $lang_str . '","success")</script>';
            echo '<script type="text/javascript">setTimeout(\'window.location.href=\"' . U('setp4') . '\"\',3000)</script>';
        } else {
            $button_str = '<a class=\"mr30 w100 btn btn-success\" href=\"' . U('setp2') . '\">' . L('previous') . L('setp') . '</a>';
            echo '<script type="text/javascript">show_install_message("#show_box","' . $button_str . $lang_str . '","danger")</script>';
        }
        ob_flush();
        flush();
        //安装完成之后 对安装功能上锁 2
        $status['install_status'] = 1;
        F('sys_status', $status);
    }

    //第四页 安装完成
    public function setp4()
    {
        //安装完成之后 对安装功能上锁 2
        $status['install_status'] = 2;
        F('sys_status', $status);
        $article = htmlspecialchars_decode(F('install_end', '', XKMS_ARTICLES));
        $article = str_replace('{$app_name}', APP_NAME, $article);
        $this->assign('article', $article); //读取安装完毕的文字
        $this->assign('setp', L('pfsetp', array('setp' => L('four'), 'count' => L('four'))));
        $this->assign('title', L('setp4_title'));
        $this->display();
    }

    //AJAX 查询接口
    protected function _get_data($field, $data)
    {
        switch ($field) {
            //第二部 验证数据库
            case 'check_mysql';
                $current_date = date(C('SYS_DATE_DETAIL')) . " ";
                $error_msg    = false;
                //检测是否能连接到数据库服务器
                try
                {
                    M('', '', array(
                        'db_type' => 'mysql',
                        'db_host' => $data['host'],
                        'db_user' => $data['user'],
                        'db_pwd'  => $data['pass'],
                        'db_port' => $data['port'],
                    ))->db()->connect();
                } catch (\Think\Exception $e) {
                    $error_msg = mb_convert_encoding($e->getMessage(), 'utf-8', array('gbk', 'utf-8'));
                }
                if ($error_msg) {
                    return array('status' => false, 'info' => array('msg' => $current_date . $error_msg, 'type' => 1));
                }

                //只要能链接数据库就 保存配置
                $save_config = array(
                    'DB_HOST'   => $data['host'],
                    'DB_NAME'   => $data['name'],
                    'DB_USER'   => $data['user'],
                    'DB_PWD'    => $data['pass'],
                    'DB_PORT'   => $data['port'],
                    'DB_PREFIX' => $data['prefix'],
                );
                $config_str     = var_export($save_config, true);
                $Core_Copyright = C('CORE_COPYRIGHT');
                $put_config     = <<<EOF
<?php
{$Core_Copyright}
// database config file
return {$config_str};
?>
EOF;
                file_put_contents(CONF_PATH . 'database.php', $put_config);
                $result = array('status' => true, 'info' => array('msg' => $current_date . L('save_config_success') . L('three_second_next_setp'), 'type' => 3));
                return $result;
                break;
        }
    }

    //返回数据库基本信息$database = true  和 已有数据库列表$database = false
    private function _compare_database($database = false)
    {
        $database_name = C('DB_NAME');
        $server_link   = (C('DB_PORT')) ? C('DB_HOST') . ":" . C('DB_PORT') : C('DB_HOST');
        try
        {
            $db = M('', '', array(
                'db_type' => 'mysql',
                'db_host' => C('DB_HOST'),
                'db_user' => C('DB_USER'),
                'db_pwd'  => C('DB_PWD'),
                'db_port' => C('DB_PORT'),
                'db_name' => C('DB_NAME'),
            ))->db()->connect();
        } catch (\Think\Exception $e) {
            //echo $e->getMessage();
            return false;
        }

        //不显示的数据库
        $not_list_database = array('information_schema', 'mysql', 'performance_schema');
        $database_list     = array();
        foreach ($db->query('show databases') as $row) {
            if (!in_array($row[0], $not_list_database)) {
                $database_list[] = $row[0];
            }

        }

        $re_compare_info = array();
        if ($database) {
            $re_compare_info['name']      = $database_name;
            $re_compare_info['if_exists'] = (in_array($database_name, $database_list)) ? 1 : 0;
        } else {
            $re_compare_info = $database_list;
        }
        return $re_compare_info;
    }

    //返回数据表基本信息
    private function _compare_tables($if_install = false)
    {
        //初始化参数
        $controls        = scandir(XKMS_TABLES);
        $re_compare_info = array();
        $tables          = array();
        //检测数据库是否存在 如果存在就初始化已有表数组
        $compare_database_info = $this->_compare_database(C('DB_NAME'));
        if ($compare_database_info['if_exists'] == 1) {
            try
            {
                $db = M('', '', array(
                    'db_type' => 'mysql',
                    'db_host' => C('DB_HOST'),
                    'db_user' => C('DB_USER'),
                    'db_pwd'  => C('DB_PWD'),
                    'db_port' => C('DB_PORT'),
                    'db_name' => C('DB_NAME'),
                ))->db()->connect();
            } catch (\Think\Exception $e) {
                //echo $e->getMessage();
                return false;
            }
            foreach ($db->query('SHOW TABLES') as $row) {
                $tables[] = $row[0];
            }
        }
        foreach ($controls as $control) {
            //跳过不是文件和不是.php 结尾的文件
            if (!preg_match('/(\d)([^\.]*)\.php$/i', $control, $p_str)
                || !is_file(XKMS_TABLES . $control)) {
                continue;
            }

            $control_info = include XKMS_TABLES . $control;
            foreach ($control_info['tables'] as $table => $table_value) {
                $new_tab = C('DB_PREFIX') . $table;
                //增加表名前缀
                $control_info['tables'][$new_tab] = $control_info['tables'][$table];
                unset($control_info['tables'][$table]);
                if ($if_install) {
                    $create_sql = '';
                    $attribute  = '';
                    foreach ($control_info['tables'][$new_tab]['column'] as $column) {
                        if ($create_sql) {
                            $create_sql .= ',';
                        }

                        $create_sql .= $column;
                    }
                    foreach ($control_info['tables'][$new_tab]['attribute'] as $a_name => $a_value) {
                        if ($attribute) {
                            $attribute .= ' ';
                        }

                        if ($a_name && $a_value) {
                            $attribute .= $a_name . ' = ' . $a_value;
                        }

                    }
                    $control_info['tables'][$new_tab]['create_sql'] = 'CREATE TABLE IF NOT EXISTS ' . $new_tab . '(' . $create_sql . ')' . $attribute;
                }
                $control_info['tables'][$new_tab]['if_exists'] = (in_array($new_tab, $tables)) ? 1 : 0;
            }
            //用于菜单归类
            $control_info['category'] = $p_str[1];
            $re_compare_info[]        = $control_info;
        }
        return $re_compare_info;
    }
}
