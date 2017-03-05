<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Model;
use Intervention\Image\Facades\Image;

class Common extends Controller
{
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
    protected function imageThumb($oldFile, $width = 195, $height = 120)
    {
        $file    = storage_path('app/public/' . $oldFile);
        if (!is_file($file) || !$width || !$height) {
            return '';
        }

        $pathinfo = pathinfo($file);
        $newName  = $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
        $newFile  = $pathinfo['dirname'] . '/' . $newName;
        $newPath = str_replace(storage_path('app/public/'), '', $newFile);
        //保证只生成一个缩略图
        if (is_file($newFile)) {
            return $newPath;
        }

        //如果文件不存在就生成
        $img = Image::make($file);
        $img->resize($width, $height);
        $img->save($newFile);

        //如果记录不存在 追加新生成文件的记录
        $manageUploadInfo = Model\ManageUpload::where(['path' => $oldFile])->first();
        $userType         = session('backend_info.id') ? session('backend_info.id') : 0;
        $userId           = $userType ? session('backend_info.id') : session('frontend_info.id');
        $data             = [
            'name'      => $manageUploadInfo['name'],
            'user_id'   => $manageUploadInfo['user_id'],
            'user_type' => $manageUploadInfo['user_type'],
            'path'      => $newPath,
            'size'      => filesize($newFile),
            'mime'      => $manageUploadInfo['mime'],
            'suffix'    => $manageUploadInfo['suffix'],
        ];
        Model\ManageUpload::updateOrCreate(['path' => $newPath], $data);

        return $newPath;
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
