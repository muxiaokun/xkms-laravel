<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Model;

class Common extends Controller
{
    public function __construct()
    {
        $this->_initialize();
    }

    public function _initialize()
    {
        //没有安装，跳转到安装页
        if (0 == env('INSTALL_STATUS') && !Route::is("Install::*")) {
            $message = trans('common.please') . trans('common.install') . trans('common.app_name');
            die($this->error($message, route('Install::index')));

        }
        //权限检测
        //保存表单验证错误之前的GET
//        if (config('TOKEN_ON') && request()->isMethod('GET') && !request()->ajax()) {
//            session('token_back_page', __SELF__);
//        }
        //POST提交必须检查表单验证
//        if (request()->isMethod('POST') && !request()->ajax() && !isset($_FILES['imgFile']) && !$this->token_check()) {
//           return $this->error(trans('common.token') . trans('common.error') . '(' . trans('common.refresh') . trans('common.later') . trans('common.submit') . ')',
//                session('token_back_page')); //后台统一检查表单令牌
//        }
    }

    protected function success($msg = '', $back_url = '', $timeout = 5)
    {
        return $this->dispatch_jump($msg, $back_url, $timeout, true);
    }

    protected function error($msg = '', $back_url = '', $timeout = 5)
    {
        return $this->dispatch_jump($msg, $back_url, $timeout, false);
    }

    /**
     * @param string $message
     * @param string $back_url
     * @param int    $timeout
     * @param string $template
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function dispatch_jump(
        $message = '',
        $back_url = '',
        $timeout = 3,
        $status = true,
        $template = 'common.dispatch_jump'
    ) {
        if ('' == $message) {
            $message = trans('common.handle') . ($status) ? trans('common.success'):trans('common.error');
        }
        if ('' == $back_url) {
            $back_url = route(request()->route()->getName());
        }
        if (request()->ajax()) {
            $ajax_data = [
                'status'  => $status,
                'info' => $message,
            ];
            return $ajax_data;
        }
        $assign = [
            'status'   => $status,
            'message'  => $message,
            'back_url' => $back_url,
            'timeout'  => intval($timeout),
        ];
        return view($template, $assign);
    }

    public function cache()
    {
        $id   = request('id');
        $type = request('type');
        if (!$id || !in_array($type, ['qrcode'])) {
            return;
        }

        $cache = S($id);
        if (!$cache) {
            return;
        }

        $echoCache = '';
        //解决文件出现Byte Order Mark  BOM
        //ob_clean();
        switch ($type) {
            case 'qrcode':
                header('Content-Type:image/png');
                $echoCache = $cache;
                break;
        }
        echo $echoCache;
        return;
    }

    //检查验证码是否正确
    protected function verifyCheck($code, $name = '')
    {
        return VerificationCode::verify($code, $name);
    }

    //生成中文拼音首字母缩写
    protected function _zh2py($str)
    {
        $MZh2py = new \App\Library\mZh2py();
        return $MZh2py->encode($str, false);
    }

    //生成缩略图 是源文件名后加 thumb
    protected function imageThumb($file, $width = 195, $height = 120)
    {
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
        $fileInfo = Model\ManageUpload::mFind($newFile, true);
        if (!$fileInfo) {
            $data = [
                'name'   => $newName,
                'path'   => $newFile,
                'size'   => filesize($newFile),
                'mime'   => '',
                'suffix' => $pathinfo['extension'],
            ];
            Model\ManageUpload::mAdd($data);
        }

        return $newFile;
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
                $result['info']   = $this->_zh2py(request('data'));
                break;
        }
        if ($result['status']) {
            return $this->success($result['info']);
        } else {
            return $this->error($result['info']);
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

        $privilegeOld = F('privilege');
        $privilege    = [];
        foreach ($privilegeOld[$module] as $controllersName => $controllers) {
            foreach ($controllers as $controllerName => $controller) {
                foreach ($controller as $actionName => $action) {
                    if (is_array($contrast) && (!in_array('all',
                                $contrast) && !in_array($controllerName . "_" . $actionName, $contrast))
                    ) {
                        continue;
                    }

                    $privilege[$controllersName][$controllerName][$actionName] = $action;
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
        $currentTime = Carbon::now();
        $htmlStr     = <<< EOF
<script type='text/javascript'>
    //{$currentTime}
   (function(){
       mConfirm('{$lang}?','{:route('',array('confirm'=>'yes'))}',true);
   })();
</script>
EOF;
        $this->show($htmlStr);
        return false;
    }

}
