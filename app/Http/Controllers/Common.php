<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Model;

class Common extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, \Closure $next) {
            $response = $next($request);
            $this->_initialize();
            return $response;
        });
    }

    public function _initialize()
    {
        //没有安装，跳转到安装页
        if (0 == env('INSTALL_STATUS') && !Route::is("Install::*")) {
            $message = trans('common.please') . trans('common.install') . trans('common.app_name');
            die($this->error($message, route('Install::index')));
        }
    }

    protected function success($msg = '', $backUrl = '', $timeout = 3)
    {
        return $this->dispatch_jump($msg, $backUrl, $timeout, true);
    }

    protected function error($msg = '', $backUrl = '', $timeout = 3)
    {
        return $this->dispatch_jump($msg, $backUrl, $timeout, false);
    }

    /**
     * @param string $message
     * @param string $backUrl
     * @param int    $timeout
     * @param string $template
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function dispatch_jump(
        $message = '',
        $backUrl = '',
        $timeout = 3,
        $status = true,
        $template = 'common.dispatch_jump'
    ) {
        if ('' == $message) {
            $message = trans('common.handle') . ($status) ? trans('common.success') : trans('common.error');
        }
        if ('' == $backUrl) {
            $backUrl = route(request()->route()->getName());
        }
        if (request()->ajax()) {
            $ajax_data = [
                'status' => $status,
                'info'   => $message,
            ];
            return $ajax_data;
        }
        $assign = [
            'status'   => $status,
            'message'  => $message,
            'back_url' => $backUrl,
            'timeout'  => intval($timeout),
        ];
        return view($template, $assign);
    }

    //检查验证码是否正确
    protected function verifyCheck($code, $name = '')
    {
        return VerificationCode::verify($code, $name);
    }

    //生成中文拼音首字母缩写
    protected function _zh2py($field, $str)
    {
        $MZh2py = new \App\Library\MZh2py();
        if ($field == '') {
            return $MZh2py->encode($str, false);
        } else {
            return str_replace(' ', '', $MZh2py->encode($str, true));
        }
    }

    //生成缩略图 是源文件名后加 thumb
    protected function imageThumb($file, $width = 195, $height = 120)
    {
        //TODO 暂时不实现
        return '';
        if (!is_file($file) || !$width || !$height) {
            return '';
        }

        $pathinfo = pathinfo($file);
        $newName  = $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
        $newFile  = $pathinfo['dirname'] . '/' . $newName;
        //保证只生成一个缩略图
        if (false !== strpos($file, $newName)) {
            $newFile = $file;
        }

        //如果文件不存在就生成
        if (!is_file($newFile)) {
            $Image = new \Think\Image();
            $Image->open($file);
            $Image->thumb($width, $height)->save($newFile);
        }

        //如果记录不存在 追加新生成文件的记录
        $userId   = ('Admin' == MODULE_NAME) ? session('backend_info.id') : session('frontend_info.id');
        $userType = ('Admin' == MODULE_NAME) ? 1 : 2;
        $fileInfo = Model\ManageUpload::where('path', $newFile)->first()->toArray();
        if (!$fileInfo) {
            $data = [
                'name'      => $newName,
                'user_id'   => $userId,
                'user_type' => $userType,
                'path'      => $newFile,
                'size'      => filesize($newFile),
                'mime'      => '',
                'suffix'    => $pathinfo['extension'],
            ];
            Model\ManageUpload::create($data);
        }

        return $newFile;
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allowAjaxApi = ['get_data'];
        if (!in_array(request('type'), $allowAjaxApi)) {
            return;
        }

        return $this->doAjaxApi();
    }

    //Ajax 接口
    protected function doAjaxApi()
    {
        if (!request()->ajax()) {
            return;
        }

        $currentAction = get_class_methods($this);
        switch (request('type')) {
            case 'validform':
                if (!in_array('doValidateForm', $currentAction)) {
                    return $this->error(trans('common.none') . trans('common.ajax') . 'validform API');
                }
                $result = $this->doValidateForm(request('field'), request('data'));
                break;
            case 'line_edit':
                if (!$this->_check_privilege('edit') || !in_array('_line_edit', $currentAction)) {
                    return $this->error(trans('common.none') . trans('common.ajax') . trans('common.edit'));
                }
                $result = $this->_line_edit(request('field'), request('data'));
                break;
            case 'get_data':
                if (!in_array('getData', $currentAction)) {
                    return $this->error(trans('common.none') . trans('common.ajax') . 'get_data API');
                }
                $result = $this->getData(request('field'), request('data'));
                break;
            case 'zh2py':
                $result           = [];
                $result['status'] = 1;
                $result['info'] = $this->_zh2py(request('field'), request('data'));
                break;
        }
        if (is_array($result)) {
            if ($result['status']) {
                return $this->success($result['info']);
            } else {
                return $this->error($result['info']);
            }
        } else {
            return $result;
        }
    }

    //登录功能
    protected function doLogin($userName, $password)
    {
    }

    //登出功能
    protected function doLogout()
    {
    }

    //是否登录的接口
    protected function isLogin()
    {
    }

    //获取权限数据
    protected function getPrivilege($module, $contrast = false)
    {
        if (!in_array($module, ['Admin', 'Home'])) {
            return [];
        }

        $privilegeOld = mGetArr(storage_path('app/install_privilege.php'));

        $privilege = [];
        foreach ($privilegeOld[$module] as $groupName => $controllers) {
            foreach ($controllers as $controller => $actions) {
                foreach ($actions as $action => $actionName) {
                    if (is_array($contrast) && (!in_array('all',
                                $contrast) && !in_array($action, $contrast))
                    ) {
                        continue;
                    }

                    $privilege[$groupName][$controller][$actionName] = $action;
                }
            }
        }
        return $privilege;
    }

    //检查权限默认方法
    public function _check_privilege($routeName)
    {
        return true;
    }

    //输出页面提示
    protected function showConfirm($lang)
    {
        if ('yes' == request('confirm')) {
            return true;
        }

        //防止缓存
        $currentTime       = Carbon::now();
        $backUrl           = route(request()->route()->getName(), ['confirm' => 'yes']);
        $assign['lang']    = $lang;
        $assign['backUrl'] = $backUrl;
        echo view('common.showConfirm', $assign);
        return false;
    }

}
