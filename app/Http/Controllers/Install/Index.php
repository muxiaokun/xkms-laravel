<?php
// 安装 初始化 重置 系统数据库

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Frontend;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class Index extends Frontend
{
    public function _initialize()
    {
        parent::_initialize();
        if (1 == env('INSTALL_STATUS')) {
            $message = trans('common.app_name') . trans('common.install') . trans('common.success');
            die($this->success($message, 'Home::Index::index'));
        }
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
        $assign = [
            'show_height'  => true,
            'database'     => config('database.connections.mysql.database'),
            'databases'    => $this->_getDatabases(),
            'install_info' => $this->_getInstallInfo(),
            'setp'         => trans('pfsetp', ['setp' => trans('two'), 'count' => trans('four')]),
            'title'        => trans('install.setp2_title'),
        ];
        return view('install.Index_setp2', $assign);
    }

    //第三页 安装数据库 提示
    public function setp3()
    {
        //初始化需要安装的数据库
        $database  = config('database.connections.mysql.database');
        $databases = $this->_getDatabases();
        if (!in_array($database, $databases)) {
            DB::select('CREATE DATABASE ' . $database);
        }
        config(['database.connections.mysql.database' => $database]);
        try {
            $exitCode = Artisan::call('migrate:refresh', ['--seed' => true, '--force' => true]);
        } catch (\Exception $e) {
            $exitCode = $e->getMessage();
        }
        if (0 === $exitCode) {
            return $this->success(trans('install.setp3_commont1') . trans('common.success') . trans('install.three_second_next_setp'));
        } else {
            return $this->error(trans('install.setp3_commont1') . trans('common.error') . ':' . $exitCode);
        }
    }

    //第四页 安装完成
    public function setp4()
    {
        $new_config = [
            'INSTALL_STATUS' => 1,
        ];
        mPutenv($new_config);
        $assign = [
            'show_height' => true,
            'progress'    => 0,
            'setp'        => trans('pfsetp', ['setp' => trans('four'), 'count' => trans('four')]),
            'title'       => trans('setp4_title'),
        ];
        return view('install.Index_setp4', $assign);
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

                //检测指定的数据库是否存在
                $exitis_databases = $this->_getDatabases();
                if (in_array($data['db_database'], $exitis_databases)) {
                    return [
                        'status' => false,
                        'info'   => ['msg' => trans('install.exists_database_and_next'), 'type' => 2],
                    ];
                }

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
            DB::setDatabaseName('');
            DB::reconnect();
            //config(['database.connections.mysql.database' => '']);
            foreach (DB::select('show databases') as $row) {
                if (in_array($row->Database, $notListDatabase)) {
                    continue;
                }
                $reCompareInfo[] = $row->Database;
            }
            DB::setDatabaseName(config('database.connections.mysql.database'));
            DB::reconnect();

        } catch (\PDOException $e) {
            //允许连接失败没有任何提示
            //die('_getDatabases error:' . $e->getMessage());
        }
        return $reCompareInfo;
    }

    private function _getTables()
    {
        //不显示的数据库
        $reCompareInfo = [];
        try {
            //config(['database.connections.mysql.database' => '']);
            foreach (DB::select('show tables') as $row) {
                $key_name        = 'Tables_in_' . DB::getDatabaseName();
                $reCompareInfo[] = $row->$key_name;
            }

        } catch (\PDOException $e) {
            //允许连接失败没有任何提示
            //die('_getDatabases error:' . $e->getMessage());
        }
        return $reCompareInfo;
    }


    private function _getInstallInfo()
    {
        $filesystem    = new Filesystem();
        $files         = $filesystem->files(storage_path('app/xkms/install_info'));
        $installInfo   = [];
        $exists_tables = $this->_getTables();
        $prefix        = config('database.connections.mysql.prefix');
        foreach ($files as $file) {
            //preg $1=sort $2=category
            if (!preg_match('/(\d)([^\.]*)\.php$/i', $file, $pStr)) {
                continue;
            }
            $controlInfo = $filesystem->getRequire($file);
            //用于菜单归类
            $controlInfo['category'] = $pStr[1];

            foreach ($controlInfo['tables'] as $table_name => &$table) {
                $table['if_exists'] = in_array($prefix . $table_name, $exists_tables) ? true : false;
            }

            $installInfo[] = $controlInfo;
        }
        return $installInfo;
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
