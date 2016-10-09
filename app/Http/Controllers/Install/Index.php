<?php
// 安装 初始化 重置 系统数据库

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Frontend;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Index extends Frontend
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
//        $status = F('sys_status');
//        if (!config('app.debug') && 1 == $status['install_status'] && 'setp4' != ACTION_NAME) {
//            $this->error(trans('install_error1'), route('Install/Index/index', ['setp' => 4]));
//            return;
//        }
//        if (!config('app.debug') && 2 == $status['install_status']) {
//            $this->error(trans('install_error2'), route('Home/Index/index'));
//            return;
//        }
    }

    //第一页 欢迎页
    public function index()
    {
        $assign = [
            'show_height' => false,
            'progress'    => 0,
            'setp'        => '',
            'title'       => trans('install.index_title'),
        ];
        return view('install.Index_index', $assign);
    }

    public function setp0()
    {
        $assign = [
            'show_height' => true,
            'progress'    => 0,
            'setp'        => trans('common.welcome') . trans('common.use'),
            'title'       => trans('install.setp0_title'),
            'article'     => Storage::get('xkms/article/licenses.php'),
        ];
        return view('install.Index_setp0', $assign);
    }

    //第二页 检测扩展模块，设置数据库
    public function setp1()
    {
        $loadedExt = get_loaded_extensions();
        //检测有可能会依赖的扩展模块
        $mustExt   = [
            //laravel
            'openssl',
            'PDO',
            'mbstring',
            'tokenizer',
            'xml',
            // xkms
            'curl',
            'fileinfo',
            'gd',
            'pdo_mysql',
        ];
        $unloadExt = [];
        foreach ($mustExt as $ext) {
            if (!in_array($ext, $loadedExt)) {
                $unloadExt[] = $ext;
            }
        }

        $defaultConfig = [
            'DB_HOST'     => env('DB_HOST'),
            'DB_PORT'     => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'DB_PASSWORD' => env('DB_PASSWORD'),
            'DB_PREFIX'   => env('DB_PREFIX'),
        ];

        $assign = [
            'show_height'    => true,
            'progress'       => 0,
            'setp'           => trans('install.pfsetp', [
                'setp'  => trans('common.one'),
                'count' => trans('common.four'),
            ]),
            'title'          => trans('install.setp1_title'),
            'note'           => $unloadExt,
            'database_list'  => $this->_getDatabases(),
            'default_config' => $defaultConfig,

        ];
        return view('install.Index_setp1', $assign);
    }

    //第三页 安装数据库
    public function setp2()
    {
        $this->assign('compare_database_info', $this->_compare_database(true));
        $this->assign('compare_tables_info', $this->_compare_tables());
        $this->assign('setp', trans('pfsetp', ['setp' => trans('two'), 'count' => trans('four')]));
        $this->assign('title', trans('setp2_title'));
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
        $this->assign('setp', trans('pfsetp', ['setp' => trans('three'), 'count' => trans('four')]));
        $this->assign('title', trans('setp3_title'));
        $this->display();
        ob_flush();
        flush();
        //初始化需要安装的数据库
        $compareDatabaseInfo = $this->_compare_database(true);
        if (1 != $compareDatabaseInfo['if_exists']) {
            try {
                $db = M('', '', [
                    'db_type' => 'mysql',
                    'db_host' => config('DB_HOST'),
                    'db_user' => config('DB_USER'),
                    'db_pwd'  => config('DB_PWD'),
                    'db_port' => config('DB_PORT'),
                ])->db()->connect();
            } catch (\Think\Exception $e) {
                $errorMsg = mb_convert_encoding($e->getMessage(), 'utf-8', ['gbk', 'utf-8']);
            }
            $result = $db->query('CREATE DATABASE `' . config('DB_NAME') . '` DEFAULT CHARACTER SET = utf8');

            $langStr = trans('install') . trans('database') . config('DB_NAME');
            if ($result) {
                $langStr .= trans('success');
                $type = 'success';
            } else {
                $langStr .= trans('error');
                $errorMsg = $db->errorInfo();
                $langStr .= trans('error') . $errorMsg[0] . $errorMsg[1] . $errorMsg[2];
                $type = 'danger';
            }
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","' . $type . '")</script>';
        } else {
            $langStr = trans('database') . config('DB_NAME') . trans('exists');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","info")</script>';
        }
        //初始化需要安装的数据表
        $compareTablesInfo = $this->_compare_tables(true);
        //必装的表有6个
        if (6 > count($compareTablesInfo)) {
            $langStr = trans('setp3_commont1');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","danger")</script>';
            echo '<script type="text/javascript">setTimeout(\'window.location.href=\"' . route('',
                    ['setp' => 2]) . '\"\',3000)</script>';
            return;
        }

        try {
            $db = M('', '', [
                'db_type' => 'mysql',
                'db_host' => config('DB_HOST'),
                'db_user' => config('DB_USER'),
                'db_pwd'  => config('DB_PWD'),
                'db_port' => config('DB_PORT'),
                'db_name' => config('DB_NAME'),
            ])->db()->connect();
            $db->query('set names utf8');
        } catch (\Think\Exception $e) {
            $errorMsg = mb_convert_encoding($e->getMessage(), 'utf-8', ['gbk', 'utf-8']);
        }

        $createTablesSuccess = 0;
        $createTablesError   = 0;
        //清空权限缓存
        F('privilege', null);
        $installControl = request('install_control');

        $tablesCount        = 0;
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

                $langStr = trans('install') . trans('controller') . '&nbsp;' . $control['control_info'] . '&nbsp;' . trans('table') . $table['table_info'] . $tableName;
                if ($result) {
                    $createTablesSuccess++;
                    $langStr .= trans('success');
                    $type = 'success';
                } else {
                    $createTablesError++;
                    $errorMsg = $db->errorInfo();
                    $langStr .= trans('error') . $errorMsg[0] . $errorMsg[1] . $errorMsg[2];
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
        $langStr = trans('success') . trans('initialize') . $createTablesSuccess . trans('table') . trans('error') . $createTablesError . trans('table');
        if (0 == $createTablesError) {
            $langStr .= trans('three_second_next_setp');
            echo '<script type="text/javascript">show_install_message("#show_box","' . $langStr . '","success")</script>';
            echo '<script type="text/javascript">setTimeout(\'window.location.href=\"' . route('setp4') . '\"\',3000)</script>';
        } else {
            $buttonStr = '<a class=\"mr30 w100 btn btn-success\" href=\"' . route('setp2') . '\">' . trans('previous') . trans('setp') . '</a>';
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
        $this->assign('setp', trans('pfsetp', ['setp' => trans('four'), 'count' => trans('four')]));
        $this->assign('title', trans('setp4_title'));
        $this->display();
    }

    //AJAX 查询接口
    protected function getData($field, $data)
    {
        switch ($field) {
            //第二部 验证数据库
            case 'check_mysql';
                $currentDate = date(config('system.sys_date_detail')) . " ";

                //检测是否能连接到数据库服务器
                $errorMsg = false;
                try {
                    config([
                        'database.connections.install_mysql' => [
                            'driver'    => 'mysql',
                            'charset'   => 'utf8',
                            'collation' => 'utf8_unicode_ci',
                            'strict'    => true,
                            'engine'    => null,
                            'host'      => $data['db_host'],
                            'port'      => $data['db_port'],
                            'database'  => '',//$data['db_database'],
                            'username'  => $data['db_username'],
                            'password'  => $data['db_password'],
                            'prefix'    => $data['db_prefix'],

                        ],
                    ]);
                    DB::connection('install_mysql')->reconnect();
                } catch (\PDOException $e) {
                    $errorMsg = $e->getMessage();
                }

                if ($errorMsg) {
                    return ['status' => false, 'info' => ['msg' => $currentDate . $errorMsg, 'type' => 1]];
                }

                //数据库名不能为空
                if (!$data['db_database']) {
                    $msg = trans('common.please') . trans('common.input') . trans('common.database') . trans('common.name');
                    return ['status' => false, 'info' => ['msg' => $msg, 'type' => 1]];
                }

                //检测指定的数据库是否存在
                $exitis_databases = $this->_getDatabases();
                if (in_array($data['db_database'], $exitis_databases)) {
                    return [
                        'status' => false,
                        'info'   => ['msg' => trans('install.exists_database_and_next'), 'type' => 2],
                    ];
                }

                //只要能链接数据库就 保存配置
                $new_config = [
                    'DB_HOST'     => $data['db_host'],
                    'DB_PORT'     => $data['db_port'],
                    'DB_DATABASE' => $data['db_database'],
                    'DB_USERNAME' => $data['db_username'],
                    'DB_PASSWORD' => $data['db_password'],
                    'DB_PREFIX'   => $data['db_prefix'],
                ];
                mPutenv($new_config);

                $result = [
                    'status' => true,
                    'info'   => [
                        'msg'  => $currentDate . trans('install.save_config_success') . trans('install.three_second_next_setp'),
                        'type' => 3,
                    ],
                ];
                return $result;
                break;
        }
    }

    private function _getDatabases()
    {
        //不显示的数据库
        $notListDatabase = ['information_schema', 'mysql', 'performance_schema'];
        $reCompareInfo   = [];
        try {
            //获取现有数据库不选择数据库
            config(['database.connections.mysql.database' => '']);
            foreach (DB::select('show databases') as $db_key => $row) {
                if (in_array($row->Database, $notListDatabase)) {
                    continue;
                }
                $reCompareInfo[] = $row->Database;
            }
        } catch (\PDOException $e) {
            //允许连接失败没有任何提示
            //die('_getDatabases error:' . $e->getMessage());
        }
        return $reCompareInfo;
    }

    //返回数据库基本信息$database = true  和 已有数据库列表$database = false
    private function _compare_database($database = false)
    {
        $databaseName = config('DB_NAME');
        $serverLink   = (config('DB_PORT')) ? C('DB_HOST') . ":" . C('DB_PORT') : C('DB_HOST');
        try {
            $db = M('', '', [
                'db_type' => 'mysql',
                'db_host' => config('DB_HOST'),
                'db_user' => config('DB_USER'),
                'db_pwd'  => config('DB_PWD'),
                'db_port' => config('DB_PORT'),
                'db_name' => config('DB_NAME'),
            ])->db()->connect();
        } catch (\Think\Exception $e) {
            //echo $e->getMessage();
            return false;
        }

        //不显示的数据库
        $notListDatabase = ['information_schema', 'mysql', 'performance_schema'];
        $databaseList    = [];
        foreach ($db->query('show databases') as $row) {
            if (!in_array($row[0], $notListDatabase)) {
                $databaseList[] = $row[0];
            }

        }

        $reCompareInfo = [];
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
        $controls      = scandir(XKMS_TABLES);
        $reCompareInfo = [];
        $tables        = [];
        //检测数据库是否存在 如果存在就初始化已有表数组
        $compareDatabaseInfo = $this->_compare_database(config('DB_NAME'));
        if ($compareDatabaseInfo['if_exists'] == 1) {
            try {
                $db = M('', '', [
                    'db_type' => 'mysql',
                    'db_host' => config('DB_HOST'),
                    'db_user' => config('DB_USER'),
                    'db_pwd'  => config('DB_PWD'),
                    'db_port' => config('DB_PORT'),
                    'db_name' => config('DB_NAME'),
                ])->db()->connect();
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
                || !is_file(XKMS_TABLES . $control)
            ) {
                continue;
            }

            $controlInfo = include XKMS_TABLES . $control;
            foreach ($controlInfo['tables'] as $table => $tableValue) {
                $newTab = config('DB_PREFIX') . $table;
                //增加表名前缀
                $controlInfo['tables'][$newTab] = $controlInfo['tables'][$table];
                unset($controlInfo['tables'][$table]);
                if ($ifInstall) {
                    $createSql = '';
                    $attribute = '';
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
            $reCompareInfo[]         = $controlInfo;
        }
        return $reCompareInfo;
    }

    public function scan($name = 'Home')
    {
        $controller_path = app_path('Http/Controllers/' . $name);
        $controllers     = scandir($controller_path);
        $route_cfg_str   = '<pre>';
        foreach ($controllers as $controller) {
            if ('.' == $controller || '..' == $controller) {
                continue;
            }
            $controller_file = $controller_path . '/' . $controller;
            $file_content    = file_get_contents($controller_file);
            preg_match_all('/public function ([^(_]+)/', $file_content, $matchs);

            $controller_name = basename($controller, '.php');
            $route_cfg_str .= "Route::group([
    'as'        => '$controller_name::',
    //'middleware'=>'auth',
    'prefix'    => '$controller_name',
], function () {
";
            foreach ($matchs[1] as $method) {
                $route_cfg_str .= "    Route::get('$method', ['as' => '$method', 'uses' => '$controller_name@$method']);" . PHP_EOL;
            }
            $route_cfg_str .= "});
";
        }
        return $route_cfg_str . '</pre>';
    }
}
