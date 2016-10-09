<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

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
            //echo redirect()->route('Install::index');
            $message = trans('common.please') . trans('common.install') . trans('common.app_name');
            die($this->error($message, 'Install::index'));

        }
        //权限检测
        //保存表单验证错误之前的GET
//        if (config('TOKEN_ON') && IS_GET && !IS_AJAX) {
//            session('token_back_page', __SELF__);
//        }
        //POST提交必须检查表单验证
//        if (IS_POST && !IS_AJAX && !isset($_FILES['imgFile']) && !$this->token_check()) {
//            $this->error(trans('token') . trans('error') . '(' . trans('refresh') . trans('later') . trans('submit') . ')',
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
        $timeout = 5,
        $status = true,
        $template = 'common.dispatch_jump'
    ) {
        if ('' == $message) {
            $message = trans('common.handle') . ($status) ? trans('common.success'):trans('common.error');
        }
        if (Request::ajax()) {
            $ajax_data = [
                'status'  => $status,
                'info' => $message,
            ];
            return $ajax_data;
        }
        $assign = [
            'status'   => false,
            'message'  => $message,
            'back_url' => route($back_url),
            'timeout'  => intval($timeout),
        ];
        return view($template, $assign);
    }

    //检查表单令牌
    private function token_check()
    {
        if (!config('TOKEN_ON')) {
            return true;
        }

        $name = config('TOKEN_NAME', null, '__hash__');
        $hash = request($name);
        if (!isset($hash) || !isset($_SESSION[$name])) { // 令牌数据无效
            return false;
        }
        // 令牌验证
        list($key, $value) = explode('_', $hash);
        if ($value && $_SESSION[$name][$key] === $value) { // 防止重复提交
            unset($_SESSION[$name][$key]); // 验证完成销毁session
            return true;
        }
        // 开启TOKEN重置
        unset($_SESSION[$name][$key]);
        return false;
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

    //生成验证码
    public function verifyImg()
    {
        $config = [
            'expire'   => 300, // 验证码的有效期（秒）
            //            'useImgBg' => '', // 是否使用背景图片 默认为false
            //            'fontSize' => '', // 验证码字体大小（像素） 默认为25
            'useCurve' => false, // 是否使用混淆曲线 默认为true
            //            'useNoise' => true, // 是否添加杂点 默认为true
            //            'imageW' => 0, // 验证码宽度 设置为0为自动计算
            //            'imageH' => 0, // 验证码高度 设置为0为自动计算
            'length'   => 4, // 验证码位数
            'fontttf'  => '5.ttf', // 指定验证码字体 默认为随机获取
            //            'useZh' => '', // 是否使用中文验证码
            //            'bg' => '', // 验证码背景颜色 rgb数组设置，例如 array(243, 251, 254)
            //            'seKey' => '', // 验证码的加密密钥
            //            'codeSet' => '', // 验证码字符集合 3.2.1 新增
            //            'zhSet' => '', // 验证码字符集合（中文） 3.2.1 新增
            //            'reset'     =>  true,           // 验证成功后是否重置 不能重置 方式重复利用一个验证码
        ];
        $Verify = new \Think\Verify($config);
        $id     = MODULE_NAME . CONTROLLER_NAME;
        if (IS_GET) {
            $id .= request('t');
        }

        //解决文件出现Byte Order Mark  BOM
        //ob_clean();
        $Verify->entry($id);
    }

    //检查验证码是否正确
    protected function verifyCheck($code, $t = '')
    {
        $Verify = new \Think\Verify();
        $id     = MODULE_NAME . CONTROLLER_NAME;
        if ($t) {
            $id .= $t;
        }

        return $Verify->check($code, $id);
    }

    //生成中文拼音首字母缩写
    protected function _zh2py($str)
    {
        $MZh2py = new \Common\Lib\mZh2py();
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
        $ManageUploadModel = D('ManageUpload');
        $fileInfo          = $ManageUploadModel->mFind($newFile, true);
        if (!$fileInfo) {
            $data = [
                'name'   => $newName,
                'path'   => $newFile,
                'size'   => filesize($newFile),
                'mime'   => '',
                'suffix' => $pathinfo['extension'],
            ];
            $ManageUploadModel->mAdd($data);
        }

        return $newFile;
    }

    //Ajax 接口
    protected function doAjaxApi()
    {
        if (!Request::ajax()) {
            return;
        }

        $currentAction = get_class_methods($this);
        switch (request('type')) {
            case 'validform':
                if (!in_array('doValidateForm', $currentAction)) {
                    $this->error(trans('none') . trans('ajax') . 'validform API');
                }
                $result = $this->doValidateForm(request('field'), request('data'));
                break;
            case 'line_edit':
                if (!$this->_check_privilege('edit') || !in_array('_line_edit', $currentAction)) {
                    $this->error(trans('none') . trans('ajax') . trans('edit'));
                }
                $result = $this->_line_edit(request('field'), request('data'));
                break;
            case 'get_data':
                if (!in_array('getData', $currentAction)) {
                    $this->error(trans('none') . trans('ajax') . 'get_data API');
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

    //调用 404 的默认控制器和默认方法
    public function _empty()
    {
        $EmptyController = A('Common/CommonEmpty');
        $EmptyController->_empty();
    }

    //检查权限默认方法
    public function _check_privilege()
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
        $currentTime = time();
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
