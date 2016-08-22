<?php
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
        $loadedExt = get_loaded_extensions();
        //没有加载mysql模块不能进行安装
        if (!in_array('pdo_mysql', $loadedExt)) {
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
        $allowAjaxApi = array('get_data');
        if (!in_array(I('type'), $allowAjaxApi)) {
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
        $loadedExt = get_loaded_extensions();
        //检测有可能会依赖的扩展模块
        $mustExt = array('iconv', 'json', 'mcrypt', 'session', 'PDO', 'bz2', 'openssl',
            'curl', 'fileinfo', 'gd', 'mbstring', 'pdo_mysql');
        $unloadExt = array();
        foreach ($mustExt as $ext) {
            if (!in_array($ext, $loadedExt)) {
                $unloadExt[] = $ext;
            }

        }
        $this->assign('note', $unloadExt);
        //读取默认数据库配置
        $this->assign('database_list', $this->_compare_database());
        $defaultConfig = include XKMS_DEFAULT_CONFIG . 'database.php';
        if (C('DB_HOST')) {
            $defaultConfig['DB_HOST'] = C('DB_HOST');
        }

        if (C('DB_NAME')) {
            $defaultConfig['DB_NAME'] = C('DB_NAME');
        }

        if (C('DB_USER')) {
            $defaultConfig['DB_USER'] = C('DB_USER');
        }

        if (C('DB_PWD')) {
            $defaultConfig['DB_PWD'] = C('DB_PWD');
        }

        if (C('DB_PORT')) {
            $defaultConfig['DB_PORT'] = C('DB_PORT');
        }

        if (C('DB_PREFIX')) {
            $defaultConfig['DB_PREFIX'] = C('DB_PREFIX');
        }

        $this->assign('default_config', $defaultConfig); //读取默认数据库配置
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
        $compareDatabaseInfo = $this->_compare_database(true);
        if (1 != $compareDatabaseInfo['if_exists']) {
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
                $errorMsg = mb_convert_encoding($e->getMessage(), 'utf-8', array('gbk', 'utf-8'));
            }
            $result = $db->query('CREATE DATABASE `' . C('DB_NAME') . '` DEFAULT CHARACTER SET = utf8');

            $langStr = L('install') . L('database') . C('DB_NAME');
            if ($result) {
                $langStr .= L('success');
                $type = 'success';
            } else {
                $langStr .= L('error');
                $errorMsg = $db->errorInfo();
                $langStr .= L('error') . $errorMsg[0] . $errorMsg[1] . $errorMsg[2];
                $type = 'danger';
            }
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","' . $type . '")</script>';
        } else {
            $langStr = L('database') . C('DB_NAME') . L('exists');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","info")</script>';
        }
        //初始化需要安装的数据表
        $compareTablesInfo = $this->_compare_tables(true);
        //必装的表有6个
        if (6 > count($compareTablesInfo)) {
            $langStr = L('setp3_commont1');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","danger")</script>';
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
            $errorMsg = mb_convert_encoding($e->getMessage(), 'utf-8', array('gbk', 'utf-8'));
        }

        $createTablesSuccess = 0;
        $createTablesError   = 0;
        //清空权限缓存
        F('privilege', null);
        $installControl = I('install_control');

        $tablesCount         = 0;
        $installTablesCount = 0;
        foreach ($compareTablesInfo as $controlIndex => $control) {
            //跳过未选中的控制器数据表 不能跳过必装的控制器0-1
            if (!in_array($controlIndex, $installControl['install']) && 1 < $control['category']) {
                continue;
            }
            $tablesCount += count($control['tables']);
        }

        foreach ($compareTablesInfo as $controlIndex => $control) {
            //跳过未选中的控制器数据表 不能跳过必装的控制器0-1
            if (!in_array($controlIndex, $installControl['install']) && 1 < $control['category']) {
                continue;
            }

            //安装表
            foreach ($control['tables'] as $tableName => $table) {
                $reset = in_array($tableName, $installControl['reset']);
                //由于有默认数据的自动写入重置必须执行
                if ($reset) {
                    $db->query("DROP TABLE IF EXISTS " . $tableName);
                }

                $result = $db->query($table['create_sql']);

                $langStr = L('install') . L('controller') . '&nbsp;' . $control['control_info'] . '&nbsp;' . L('table') . $table['table_info'] . $tableName;
                if ($result) {
                    $createTablesSuccess++;
                    $langStr .= L('success');
                    $type = 'success';
                } else {
                    $createTablesError++;
                    $errorMsg = $db->errorInfo();
                    $langStr .= L('error') . $errorMsg[0] . $errorMsg[1] . $errorMsg[2];
                    $type = 'danger';
                }
                echo '<script type="text/javascript">show_install_progress(' . ($installTablesCount++ / $tablesCount) . ');</script>';
                echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","' . $type . '")</script>';
                ob_flush();
                flush();
                //表不存在或者选择重置将初始化数据
                if ((!$table['if_exists'] || $reset) && 0 < count($table['insert_row'])) {
                    foreach ($table['insert_row'] as $insertRow) {
                        $db->exec('INSERT INTO ' . $tableName . $insertRow);
                    }
                }
            }
            //安装权限
            $privilege = F('privilege');
            foreach ($control['privilege'] as $group => $controlPriv) {
                foreach ($controlPriv as $controlName => $actionPriv) {
                    $privilege[$group][$control['control_group']][$controlName] = $actionPriv;
                }
            }
            F('privilege', $privilege);
        }

        //返回安装报告
        $langStr = L('success') . L('initialize') . $createTablesSuccess . L('table') . L('error') . $createTablesError . L('table');
        if (0 == $createTablesError) {
            $langStr .= L('three_second_next_setp');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","success")</script>';
            echo '<script type="text/javascript">setTimeout(\'window.location.href=\"' . U('setp4') . '\"\',3000)</script>';
        } else {
            $buttonStr = '<a class=\"mr30 w100 btn btn-success\" href=\"' . U('setp2') . '\">' . L('previous') . L('setp') . '</a>';
            echo '<script type="text/javascript">show_install_message("#show_box","' . $buttonStr . $langStr . '","danger")</script>';
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
        $article = str_replace('{$appName}', APP_NAME, $article);
        $this->assign('article', $article); //读取安装完毕的文字
        $this->assign('setp', L('pfsetp', array('setp' => L('four'), 'count' => L('four'))));
        $this->assign('title', L('setp4_title'));
        $this->display();
    }

    //AJAX 查询接口
    protected function getData($field, $data)
    {
        switch ($field) {
            //第二部 验证数据库
            case 'check_mysql';
                $currentDate = date(C('SYS_DATE_DETAIL')) . " ";
                $errorMsg    = false;
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
                    $errorMsg = mb_convert_encoding($e->getMessage(), 'utf-8', array('gbk', 'utf-8'));
                }
                if ($errorMsg) {
                    return array('status' => false, 'info' => array('msg' => $currentDate . $errorMsg, 'type' => 1));
                }

                //只要能链接数据库就 保存配置
                $saveConfig = array(
                    'DB_HOST'   => $data['host'],
                    'DB_NAME'   => $data['name'],
                    'DB_USER'   => $data['user'],
                    'DB_PWD'    => $data['pass'],
                    'DB_PORT'   => $data['port'],
                    'DB_PREFIX' => $data['prefix'],
                );
                $configStr     = var_export($saveConfig, true);
                $CoreCopyright = C('CORE_COPYRIGHT');
                $putConfig     = <<<EOF
<?php
{$CoreCopyright}
// database config file
return {$configStr};
?>
EOF;
                file_put_contents(CONF_PATH . 'database.php', $putConfig);
                $result = array('status' => true, 'info' => array('msg' => $currentDate . L('save_config_success') . L('three_second_next_setp'), 'type' => 3));
                return $result;
                break;
        }
    }

    //返回数据库基本信息$database = true  和 已有数据库列表$database = false
    private function _compare_database($database = false)
    {
        $databaseName = C('DB_NAME');
        $serverLink   = (C('DB_PORT')) ? C('DB_HOST') . ":" . C('DB_PORT') : C('DB_HOST');
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
        $notListDatabase = array('information_schema', 'mysql', 'performance_schema');
        $databaseList     = array();
        foreach ($db->query('show databases') as $row) {
            if (!in_array($row[0], $notListDatabase)) {
                $databaseList[] = $row[0];
            }

        }

        $reCompareInfo = array();
        if ($database) {
            $reCompareInfo['name']      = $databaseName;
            $reCompareInfo['if_exists'] = (in_array($databaseName, $databaseList)) ? 1 : 0;
        } else {
            $reCompareInfo = $databaseList;
        }
        return $reCompareInfo;
    }

    //返回数据表基本信息
    private function _compare_tables($ifInstall = false)
    {
        //初始化参数
        $controls        = scandir(XKMS_TABLES);
        $reCompareInfo = array();
        $tables          = array();
        //检测数据库是否存在 如果存在就初始化已有表数组
        $compareDatabaseInfo = $this->_compare_database(C('DB_NAME'));
        if ($compareDatabaseInfo['if_exists'] == 1) {
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
            if (!preg_match('/(\d)([^\.]*)\.php$/i', $control, $pStr)
                || !is_file(XKMS_TABLES . $control)) {
                continue;
            }

            $controlInfo = include XKMS_TABLES . $control;
            foreach ($controlInfo['tables'] as $table => $tableValue) {
                $newTab = C('DB_PREFIX') . $table;
                //增加表名前缀
                $controlInfo['tables'][$newTab] = $controlInfo['tables'][$table];
                unset($controlInfo['tables'][$table]);
                if ($ifInstall) {
                    $createSql = '';
                    $attribute  = '';
                    foreach ($controlInfo['tables'][$newTab]['column'] as $column) {
                        if ($createSql) {
                            $createSql .= ',';
                        }

                        $createSql .= $column;
                    }
                    foreach ($controlInfo['tables'][$newTab]['attribute'] as $aName => $aValue) {
                        if ($attribute) {
                            $attribute .= ' ';
                        }

                        if ($aName && $aValue) {
                            $attribute .= $aName . ' = ' . $aValue;
                        }

                    }
                    $controlInfo['tables'][$newTab]['create_sql'] = 'CREATE TABLE IF NOT EXISTS ' . $newTab . '(' . $createSql . ')' . $attribute;
                }
                $controlInfo['tables'][$newTab]['if_exists'] = (in_array($newTab, $tables)) ? 1 : 0;
            }
            //用于菜单归类
            $controlInfo['category'] = $pStr[1];
            $reCompareInfo[]        = $controlInfo;
        }
        return $reCompareInfo;
    }
}
