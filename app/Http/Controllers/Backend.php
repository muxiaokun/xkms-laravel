<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

use App\Model;
use Carbon\Carbon;

class Backend extends Common
{
    public function _initialize()
    {
        /*parent::_initialize();
        if ($this->isLogin()) {
            $backendInfo = session('backend_info');
            //自动登出时间
            if (Carbon::now() - config('system.sys_backend_timeout') < $backendInfo['login_time']) {
                $backendInfo['login_time'] = Carbon::now();
                session('backend_info', $backendInfo);
            } else {
                $this->doLogout();
                $this->error(trans('common.login') . trans('common.timeout'), route('Admin/Index/index'));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $AdminModel            = D('Admin');
            $adminInfo            = Model\Admins::mFind($backendInfo['id']);
            $adminGroupPrivilege = Model\AdminGroup::mFind_privilege($adminInfo['group_id']);
            if (
                $backendInfo['privilege'] !== $adminInfo['privilege'] ||
                $backendInfo['group_privilege'] !== $adminGroupPrivilege
            ) {
                $this->doLogout();
                $this->error(trans('common.privilege') . trans('common.change') . trans('common.please') . trans('common.login'), route('Admin/Index/index'));
            }

            //登录后 检查权限
            if (!$this->_check_privilege()) {
                $this->error(trans('common.you') . trans('common.none') . trans('common.privilege'));
            }

            //是否开启管理员日志 记录除了root 和 非POST(不记录来自ajax_api)提交数据
            if (config('system.sys_admin_auto_log') && 1 != session('backend_info.id') && IS_POST && 'ajax_api' != ACTION_NAME) {
                $denyLog['Index'] = array('index', 'top_nav', 'left_nav', 'main', 'logout');
                if (!in_array(ACTION_NAME, $denyLog[CONTROLLER_NAME])) {
                    Model\AdminLog::mAdd($backendInfo['id']);
                }
            }
        } else {
            //检测不登陆就可以访问的
            $allowAction['Index'] = array('index', 'login', 'verifyImg');
            if (!in_array(ACTION_NAME, $allowAction[CONTROLLER_NAME])) {
                $this->error(trans('common.notdoLogin') . trans('common.backend'), route('Admin/Index/index'));
            }
        }*/
    }

    //生成验证码
    public function verifyImg()
    {
        //查看配置是否需要验证码
        if (!config('system.sys_backend_verify')) {
            return;
        }

        return parent::verifyImg();
    }

    //检查验证码是否正确
    protected function verifyCheck($code, $t = '')
    {
        //查看配置是否需要验证码
        if (!config('system.sys_backend_verify')) {
            return true;
        }

        return parent::verifyCheck($code, $t);
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allowAjaxApi = ['validform', 'get_data'];
        if (!$this->isLogin() && !in_array(request('type'), $allowAjaxApi)) {
            return;
        }

        return $this->doAjaxApi();
    }

    //登录功能
    protected function doLogin($userName, $password)
    {
        if (!$this->verifyCheck(request('verify'))) {
            return 'verify_error';
        }

        //检测后台尝试登陆次数
        $loginNum  = config('system.sys_backend_login_num');
        $lockTime  = config('system.sys_backend_lock_time');
        $loginInfo = Model\Admins::mFind(Model\Admins::mFindId($userName, 'admin_name'));
        if ($loginNum && $loginInfo->lock_time && strtotime($loginInfo->lock_time) > Carbon::now()->getTimestamp() - $lockTime) {
            $loginInfo->lock_time = Carbon::now();
            $loginInfo->save();
            return 'lock_user_error';
        }
        //验证用户名密码
        $adminInfo = Model\Admins::authorized($userName, $password);
        if ($adminInfo) {
            //管理员有组的 加载分组权限
            if (0 < count($adminInfo['group_id'])) {
                $adminInfo['group_privilege'] = Model\AdminGroups::mFind_privilege($adminInfo['group_id']);
            }
            //重置登录次数
            if (0 != $adminInfo['login_num']) {
                $loginData              = [];
                $loginData['login_num'] = 0;
                $loginData['lock_time'] = null;
                Model\Admins::where('id', '=', $loginInfo->id)->update($loginData);
            }
            $adminInfo['login_time'] = Carbon::now();
            session(['backend_info' => $adminInfo]);
            return 'login_success';
        } else {
            //检测后台尝试登陆次数
            if ($loginNum) {
                $loginData              = [];
                $loginData['login_num'] = $loginInfo->login_num + 1;
                $loginData['lock_time'] = ($loginNum <= $loginData['login_num']) ? Carbon::now() : null;
                Model\Admins::where('id', '=', $loginInfo->id)->update($loginData);
            }
            return 'user_pwd_error';
        }
    }

    //登出功能
    protected function doLogout()
    {
        session(['backend_info' => null]);
    }

    //子类调用的是否登录的接口
    protected function isLogin()
    {
        if (session('backend_info')) {
            return true;
        } else {
            return false;
        }
    }

    //调用 404 的默认控制器和默认方法
    public function _empty()
    {
        $EmptyController = A('Common/CommonEmpty');
        $EmptyController->_empty();
    }

    //检测权限
    public function _check_privilege($actionName = ACTION_NAME, $controllerName = CONTROLLER_NAME)
    {
        //登录后 检查是否有权限可以操作 1.不是默认框架控制器 2.拥有管理员管理组全部权限 3.拥有权限 4.ajax_api接口 4.不需要权限就能访问的
        $allowAction['Index']        = ['index', 'top_nav', 'left_nav', 'main', 'logout'];
        $allowAction['ManageUpload'] = ['UploadFile', 'ManageFile'];
        $backendInfo                 = session('backend_info');
        $adminPriv                   = $backendInfo['privilege'];
        $adminGroupPriv              = ($backendInfo['group_privilege']) ? $backendInfo['group_privilege'] : [];
        if (
            'ajax_api' != $actionName &&
            !in_array($actionName, $allowAction[$controllerName]) &&
            !in_array('all', $adminPriv) &&
            !in_array($controllerName . '_' . $actionName, $adminPriv) &&
            !in_array('all', $adminGroupPriv) &&
            !in_array($controllerName . '_' . $actionName, $adminGroupPriv)
        ) {
            return false;
        }

        return true;
    }

    //保存系统配置
    protected function _put_config($col, $file)
    {
        if (!is_array($col)) {
            return false;
        }

        //流程 1.读取旧的配置 2.存入新的配置 3.输出到文件
        if (!in_array($file, explode(',', config('LOAD_EXT_CONFIG')))) {
            return false;
        }

        $cfgFile    = CONF_PATH . $file . '.php';
        $saveConfig = include $cfgFile;
        if (!is_array($saveConfig)) {
            $saveConfig = [];
        }

        foreach ($col as $option) {
            $saveConfig[$option] = request($option);
        }
        $configStr     = var_export($saveConfig, true);
        $CoreCopyright = config('CORE_COPYRIGHT');
        $putConfig     = <<<EOF
<?php
{$CoreCopyright}
// {$file} config file
return {$configStr};
?>
EOF;
        $putResult     = file_put_contents($cfgFile, $putConfig);
        if ($putResult) {
            $this->success(trans('common.save') . trans('common.success'), route(ACTION_NAME));
        } else {
            $this->error(trans('common.save') . trans('common.error'), route(ACTION_NAME));
        }
        //此函数不做任何返回
    }
}
