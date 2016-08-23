<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

class Common extends Controller
{
    public function _initialize()
    {
        //没有安装，跳转到安装页
        $privilege    = F('privilege');
        $allowModule = array('Install');
        if (!$privilege && !in_array(MODULE_NAME, $allowModule)) {
            $this->error(trans('please') . L('install') . APP_NAME, route('Install/Index/index'));
        }
        //保存表单验证错误之前的GET
        if (config('TOKEN_ON') && IS_GET && !IS_AJAX) {
            session('token_back_page', __SELF__);
        }
        //POST提交必须检查表单验证
        if (IS_POST && !IS_AJAX && !isset($_FILES['imgFile']) && !$this->token_check()) {
            $this->error(trans('token') . L('error') . '(' . L('refresh') . L('later') . L('submit') . ')', session('token_back_page')); //后台统一检查表单令牌
        }
    }

    //检查表单令牌
    private function token_check()
    {
        if (!config('TOKEN_ON')) {
            return true;
        }

        $name = config('TOKEN_NAME', null, '__hash__');
        $hash = I($name);
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
        $id   = I('id');
        $type = I('type');
        if (!$id || !in_array($type, array('qrcode'))) {
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
        $config = array(
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
        );
        $Verify = new \Think\Verify($config);
        $id     = MODULE_NAME . CONTROLLER_NAME;
        if (IS_GET) {
            $id .= I('t');
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
        $newName = $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
        $newFile = $pathinfo['dirname'] . '/' . $newName;
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
        $fileInfo         = $ManageUploadModel->mFind($newFile, true);
        if (!$fileInfo) {
            $data = array(
                'name'   => $newName,
                'path'   => $newFile,
                'size'   => filesize($newFile),
                'mime'   => '',
                'suffix' => $pathinfo['extension'],
            );
            $ManageUploadModel->mAdd($data);
        }

        return $newFile;
    }

    //Ajax 接口
    protected function _ajax_api()
    {
        if (!IS_AJAX) {
            return;
        }

        $currentAction = get_class_methods($this);
        switch (I('type')) {
            case 'validform':
                if (!in_array('doValidateForm', $currentAction)) {
                    $this->error(trans('none') . L('ajax') . 'validform API');
                }
                $result = $this->doValidateForm(I('field'), I('data'));
                break;
            case 'line_edit':
                if (!$this->_check_privilege('edit') || !in_array('_line_edit', $currentAction)) {
                    $this->error(trans('none') . L('ajax') . L('edit'));
                }
                $result = $this->_line_edit(I('field'), I('data'));
                break;
            case 'get_data':
                if (!in_array('getData', $currentAction)) {
                    $this->error(trans('none') . L('ajax') . 'get_data API');
                }
                $result = $this->getData(I('field'), I('data'));
                break;
            case 'zh2py':
                $result           = array();
                $result['status'] = 1;
                $result['info']   = $this->_zh2py(I('data'));
                break;
        }
        if ($result['status']) {
            $this->success($result['info']);
        } else {
            $this->error($result['info']);
        }
    }

    //登录功能
    protected function doLogin($userName, $password)
    {}
    //登出功能
    protected function doLogout()
    {}
    //是否登录的接口
    protected function isLogin()
    {}

    //获取权限数据
    protected function getPrivilege($module, $contrast = false)
    {
        if (!in_array($module, array('Admin', 'Home'))) {
            return array();
        }

        $privilegeOld = F('privilege');
        $privilege     = array();
        foreach ($privilegeOld[$module] as $controllersName => $controllers) {
            foreach ($controllers as $controllerName => $controller) {
                foreach ($controller as $actionName => $action) {
                    if (is_array($contrast) && (!in_array('all', $contrast) && !in_array($controllerName . "_" . $actionName, $contrast))) {
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
        if ('yes' == I('confirm')) {
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
