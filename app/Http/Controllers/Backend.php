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
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

class Backend extends Common
{
    public function _initialize()
    {
        /*parent::_initialize();
        if ($this->_is_login()) {
            $backend_info = session('backend_info');
            //自动登出时间
            if (time() - C('SYS_BACKEND_TIMEOUT') < $backend_info['login_time']) {
                $backend_info['login_time'] = time();
                session('backend_info', $backend_info);
            } else {
                $this->_logout();
                $this->error(L('login') . L('timeout'), U('Admin/Index/index'));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $AdminModel            = D('Admin');
            $admin_info            = $AdminModel->m_find($backend_info['id']);
            $AdminGroupModel       = D('AdminGroup');
            $admin_group_privilege = $AdminGroupModel->m_find_privilege($admin_info['group_id']);
            if (
                $backend_info['privilege'] !== $admin_info['privilege'] ||
                $backend_info['group_privilege'] !== $admin_group_privilege
            ) {
                $this->_logout();
                $this->error(L('privilege') . L('change') . L('please') . L('login'), U('Admin/Index/index'));
            }

            //登录后 检查权限
            if (!$this->_check_privilege()) {
                $this->error(L('you') . L('none') . L('privilege'));
            }

            //是否开启管理员日志 记录除了root 和 非POST(不记录来自ajax_api)提交数据
            if (C('SYS_ADMIN_AUTO_LOG') && 1 != session('backend_info.id') && IS_POST && 'ajax_api' != ACTION_NAME) {
                $deny_log['Index'] = array('index', 'top_nav', 'left_nav', 'main', 'logout');
                if (!in_array(ACTION_NAME, $deny_log[CONTROLLER_NAME])) {
                    $AdminLogModel = D('AdminLog');
                    $AdminLogModel->m_add($backend_info['id']);
                }
            }
        } else {
            //检测不登陆就可以访问的
            $allow_action['Index'] = array('index', 'login', 'verify_img');
            if (!in_array(ACTION_NAME, $allow_action[CONTROLLER_NAME])) {
                $this->error(L('not_login') . L('backend'), U('Admin/Index/index'));
            }
        }*/
    }

    //生成验证码
    public function verify_img()
    {
        //查看配置是否需要验证码
        if (!C('SYS_BACKEND_VERIFY')) {
            return;
        }

        return parent::verify_img();
    }

    //检查验证码是否正确
    protected function _verify_check($code, $t = '')
    {
        //查看配置是否需要验证码
        if (!C('SYS_BACKEND_VERIFY')) {
            return true;
        }

        return parent::_verify_check($code, $t);
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allow_ajax_api = array('validform', 'get_data');
        if (!$this->_is_login() && !in_array(I('type'), $allow_ajax_api)) {
            return;
        }

        $this->_ajax_api();
    }

    //登录功能
    protected function _login($user_name, $password)
    {
        if (!$this->_verify_check(I('verify'))) {
            return 'verify_error';
        }

        $AdminModel = D('Admin');
        //检测后台尝试登陆次数
        $login_num = C('SYS_BACKEND_LOGIN_NUM');
        $lock_time = C('SYS_BACKEND_LOCK_TIME');
        if (0 != $login_num) {
            $login_info = $AdminModel->m_find($AdminModel->m_find_id($user_name));
            if (0 != $login_info['lock_time'] && $login_info['lock_time'] > (time() - $lock_time)) {
                $AdminModel->data(array('lock_time' => time()))->where(array('id' => $login_info['id']))->save();
                return 'lock_user_error';
            }
        }
        //验证用户名密码
        $admin_info = $AdminModel->authorized($user_name, $password);
        if ($admin_info) {
            //管理员有组的 加载分组权限
            if (0 < count($admin_info['group_id'])) {
                $AdminGroupModel               = D('AdminGroup');
                $admin_info['group_privilege'] = $AdminGroupModel->m_find_privilege($admin_info['group_id']);
            }
            //重置登录次数
            if (0 != $admin_info['login_num']) {
                $login_data = array('login_num' => 0, 'lock_time' => 0);
                $AdminModel->data($login_data)->where(array('id' => $login_info['id']))->save();
            }
            $admin_info['login_time'] = time();
            session('backend_info', $admin_info);
            return 'login_success';
        } else {
            //检测后台尝试登陆次数
            if (0 != $login_num) {
                $login_data              = array();
                $login_data['login_num'] = $login_info['login_num'] + 1;
                $login_data['lock_time'] = ($login_num <= $login_data['login_num']) ? time() : 0;
                $AdminModel->data($login_data)->where(array('id' => $login_info['id']))->save();
            }
            return 'user_pwd_error';
        }
    }
    //登出功能
    protected function _logout()
    {
        session('backend_info', null);
    }
    //子类调用的是否登录的接口
    protected function _is_login()
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
        $Empty_Controller = A('Common/CommonEmpty');
        $Empty_Controller->_empty();
    }

    //检测权限
    public function _check_privilege($action_name = ACTION_NAME, $controller_name = CONTROLLER_NAME)
    {
        //登录后 检查是否有权限可以操作 1.不是默认框架控制器 2.拥有管理员管理组全部权限 3.拥有权限 4.ajax_api接口 4.不需要权限就能访问的
        $allow_action['Index']        = array('index', 'top_nav', 'left_nav', 'main', 'logout');
        $allow_action['ManageUpload'] = array('UploadFile', 'ManageFile');
        $backend_info                 = session('backend_info');
        $admin_priv                   = $backend_info['privilege'];
        $admin_group_priv             = ($backend_info['group_privilege']) ? $backend_info['group_privilege'] : array();
        if (
            'ajax_api' != $action_name &&
            !in_array($action_name, $allow_action[$controller_name]) &&
            !in_array('all', $admin_priv) &&
            !in_array($controller_name . '_' . $action_name, $admin_priv) &&
            !in_array('all', $admin_group_priv) &&
            !in_array($controller_name . '_' . $action_name, $admin_group_priv)
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
        if (!in_array($file, explode(',', C('LOAD_EXT_CONFIG')))) {
            return false;
        }

        $cfg_file    = CONF_PATH . $file . '.php';
        $save_config = include $cfg_file;
        if (!is_array($save_config)) {
            $save_config = array();
        }

        foreach ($col as $option) {
            $save_config[$option] = I($option);
        }
        $config_str     = var_export($save_config, true);
        $Core_Copyright = C('CORE_COPYRIGHT');
        $put_config     = <<<EOF
<?php
{$Core_Copyright}
// {$file} config file
return {$config_str};
?>
EOF;
        $put_result = file_put_contents($cfg_file, $put_config);
        if ($put_result) {
            $this->success(L('save') . L('success'), U(ACTION_NAME));
        } else {
            $this->error(L('save') . L('error'), U(ACTION_NAME));
        }
        //此函数不做任何返回
    }
}
