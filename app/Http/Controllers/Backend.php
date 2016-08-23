<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

class Backend extends Common
{
    public function _initialize()
    {
        /*parent::_initialize();
        if ($this->isLogin()) {
            $backendInfo = session('backend_info');
            //自动登出时间
            if (time() - config('SYS_BACKEND_TIMEOUT') < $backendInfo['login_time']) {
                $backendInfo['login_time'] = time();
                session('backend_info', $backendInfo);
            } else {
                $this->doLogout();
                $this->error(trans('login') . L('timeout'), route('Admin/Index/index'));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $AdminModel            = D('Admin');
            $adminInfo            = $AdminModel->mFind($backendInfo['id']);
            $AdminGroupModel       = D('AdminGroup');
            $adminGroupPrivilege = $AdminGroupModel->mFind_privilege($adminInfo['group_id']);
            if (
                $backendInfo['privilege'] !== $adminInfo['privilege'] ||
                $backendInfo['group_privilege'] !== $adminGroupPrivilege
            ) {
                $this->doLogout();
                $this->error(trans('privilege') . L('change') . L('please') . L('login'), route('Admin/Index/index'));
            }

            //登录后 检查权限
            if (!$this->_check_privilege()) {
                $this->error(trans('you') . L('none') . L('privilege'));
            }

            //是否开启管理员日志 记录除了root 和 非POST(不记录来自ajax_api)提交数据
            if (config('SYS_ADMIN_AUTO_LOG') && 1 != session('backend_info.id') && IS_POST && 'ajax_api' != ACTION_NAME) {
                $denyLog['Index'] = array('index', 'top_nav', 'left_nav', 'main', 'logout');
                if (!in_array(ACTION_NAME, $denyLog[CONTROLLER_NAME])) {
                    $AdminLogModel = D('AdminLog');
                    $AdminLogModel->mAdd($backendInfo['id']);
                }
            }
        } else {
            //检测不登陆就可以访问的
            $allowAction['Index'] = array('index', 'login', 'verifyImg');
            if (!in_array(ACTION_NAME, $allowAction[CONTROLLER_NAME])) {
                $this->error(trans('notdoLogin') . L('backend'), route('Admin/Index/index'));
            }
        }*/
    }

    //生成验证码
    public function verifyImg()
    {
        //查看配置是否需要验证码
        if (!config('SYS_BACKEND_VERIFY')) {
            return;
        }

        return parent::verifyImg();
    }

    //检查验证码是否正确
    protected function verifyCheck($code, $t = '')
    {
        //查看配置是否需要验证码
        if (!config('SYS_BACKEND_VERIFY')) {
            return true;
        }

        return parent::verifyCheck($code, $t);
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allowAjaxApi = array('validform', 'get_data');
        if (!$this->isLogin() && !in_array(I('type'), $allowAjaxApi)) {
            return;
        }

        $this->_ajax_api();
    }

    //登录功能
    protected function doLogin($userName, $password)
    {
        if (!$this->verifyCheck(I('verify'))) {
            return 'verify_error';
        }

        $AdminModel = D('Admin');
        //检测后台尝试登陆次数
        $loginNum = config('SYS_BACKEND_LOGIN_NUM');
        $lockTime = config('SYS_BACKEND_LOCK_TIME');
        if (0 != $loginNum) {
            $loginInfo = $AdminModel->mFind($AdminModel->mFindId($userName));
            if (0 != $loginInfo['lock_time'] && $loginInfo['lock_time'] > (time() - $lockTime)) {
                $AdminModel->data(array('lock_time' => time()))->where(array('id' => $loginInfo['id']))->save();
                return 'lock_user_error';
            }
        }
        //验证用户名密码
        $adminInfo = $AdminModel->authorized($userName, $password);
        if ($adminInfo) {
            //管理员有组的 加载分组权限
            if (0 < count($adminInfo['group_id'])) {
                $AdminGroupModel               = D('AdminGroup');
                $adminInfo['group_privilege'] = $AdminGroupModel->mFind_privilege($adminInfo['group_id']);
            }
            //重置登录次数
            if (0 != $adminInfo['login_num']) {
                $loginData = array('login_num' => 0, 'lock_time' => 0);
                $AdminModel->data($loginData)->where(array('id' => $loginInfo['id']))->save();
            }
            $adminInfo['login_time'] = time();
            session('backend_info', $adminInfo);
            return 'login_success';
        } else {
            //检测后台尝试登陆次数
            if (0 != $loginNum) {
                $loginData              = array();
                $loginData['login_num'] = $loginInfo['login_num'] + 1;
                $loginData['lock_time'] = ($loginNum <= $loginData['login_num']) ? time() : 0;
                $AdminModel->data($loginData)->where(array('id' => $loginInfo['id']))->save();
            }
            return 'user_pwd_error';
        }
    }
    //登出功能
    protected function doLogout()
    {
        session('backend_info', null);
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
        $allowAction['Index']        = array('index', 'top_nav', 'left_nav', 'main', 'logout');
        $allowAction['ManageUpload'] = array('UploadFile', 'ManageFile');
        $backendInfo                 = session('backend_info');
        $adminPriv                   = $backendInfo['privilege'];
        $adminGroupPriv             = ($backendInfo['group_privilege']) ? $backendInfo['group_privilege'] : array();
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
            $saveConfig = array();
        }

        foreach ($col as $option) {
            $saveConfig[$option] = I($option);
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
        $putResult = file_put_contents($cfgFile, $putConfig);
        if ($putResult) {
            $this->success(trans('save') . L('success'), route(ACTION_NAME));
        } else {
            $this->error(trans('save') . L('error'), route(ACTION_NAME));
        }
        //此函数不做任何返回
    }
}
