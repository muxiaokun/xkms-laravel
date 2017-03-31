<?php
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Template extends Backend
{
    //$tplInfoFile 与 Common/Common/function.php M_scan_template一致
    private $tplInfoFile  = 'theme_info.php';
    private $defaultTheme = '';
    private $viewPath     = '';
    private $viewFiles    = [];

    public function commonInitialize()
    {
        //初始化模板目录
        $this->viewPath = resource_path('views/home/');
        config('system.default_theme') && $this->viewPath .= config('system.default_theme') . '/';
        //初始化模本文件列表
        $this->viewFiles = mGetArr($this->viewPath . $this->tplInfoFile);
        if (!$this->viewFiles) {
            $this->_refresh_viewFiles();
        }
        $this->defaultTheme = config('system.default_theme');
    }

    //列表
    public function index()
    {
        $this->commonInitialize();
        //刷新模本文件列表
        if (1 == request('refresh')) {
            $this->_refresh_viewFiles();
            return $this->success(trans('common.theme') . trans('common.template') . trans('common.refresh') . trans('common.success'));
        }

        //修改默认主题配置
        $defaultTheme = request('default_theme');
        $themeList    = $this->_get_theme_list();
        $themeList[]  = 'empty';
        if (null !== $defaultTheme && in_array($defaultTheme, $themeList)) {
            $config['default_theme'] = ('empty' == $defaultTheme) ? '' : $defaultTheme;
            mPutArr(config_path('system.php'), $config);
            return $this->success(trans('common.theme') . trans('common.selection') . trans('common.success'));
        }

        //修改模板文件信息
        if (request()->isMethod('POST')) {
            foreach ($this->viewFiles as $fileMd5 => $info) {
                $postInfo                          = request($fileMd5);
                $this->viewFiles[$fileMd5]['name'] = $postInfo['name'];
                $this->viewFiles[$fileMd5]['info'] = $postInfo['info'];
            }

            //保存缓存
            mPutArr($this->viewPath . $this->tplInfoFile, $this->viewFiles);
            return $this->success(trans('common.theme') . trans('common.info') . trans('common.save') . trans('common.success'));
        }

        $assign['default_theme']   = $this->defaultTheme;
        $assign['theme_list']      = $this->_get_theme_list();
        $assign['theme_info_list'] = $this->viewFiles;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('Admin::Template::add');
        $batchHandle['edit']    = $this->_check_privilege('Admin::Template::edit');
        $batchHandle['del']     = $this->_check_privilege('Admin::Template::del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.theme') . trans('common.template') . trans('common.management');
        return view('admin.Template_index', $assign);
    }

    //新增
    public function add()
    {
        $this->commonInitialize();
        if (request()->isMethod('POST')) {
            //添加时不可以创建新的目录 必须使用系统设置的后缀
            $fileName = trim(request('file_name'));
            //处理2级目录
            $filePath = explode('/', $fileName);
            if (preg_match('/\.\.\//', $fileName)
                || !preg_match('/' . str_ireplace('.', '\.', config('TMPL_TEMPLATE_SUFFIX')) . '$/', $fileName)
            ) {
                return $this->error(trans('common.file') . trans('common.name') . trans('common.error'),
                    route('Admin::Template::index'));
            }
            if (!$fileName) {
                return $this->error(trans('common.file') . trans('common.name') . trans('common.not') . trans('common.empty'),
                    route('Admin::Template::index'));
            }

            $filePath = $this->viewPath . $fileName;
            $fileMd5  = md5($filePath);
            if (isset($this->viewFiles[$fileMd5])) {
                return $this->error(trans('common.file') . trans('common.name') . trans('common.repeat'),
                    route('Admin::Template::index'));
            }

            $content    = request('content', '');
            $resultEdit = file_put_contents($filePath, $content);
            if (false !== $resultEdit) {
                $this->_refresh_viewFiles();
                return $this->success(trans('common.theme') . trans('common.template') . trans('common.add') . trans('common.success'),
                    route('Admin::Template::index'));
            } else {
                return $this->error(trans('common.theme') . trans('common.template') . trans('common.add') . trans('common.error'),
                    route('Admin::Template::index'));
            }
        }
        $assign['id']        = '';
        $assign['edit_info'] = ['file_name' => '', 'content' => '',];
        $assign['title']     = trans('common.add') . trans('common.theme') . trans('common.template') . trans('common.template');
        return view('admin.Template_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $this->commonInitialize();
        $id = request('id');
        if (!is_array($this->viewFiles[$id])) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Template::index'));
        }

        $fileName = $this->viewFiles[$id]['file_name'];
        $filePath = $this->viewPath . $fileName;
        if (request()->isMethod('POST')) {
            $content    = request('content', '', false);
            $resultEdit = file_put_contents($filePath, $content);
            if ($resultEdit) {
                return $this->success(trans('common.theme') . trans('common.template') . trans('common.edit') . trans('common.success'),
                    route('Admin::Template::index'));
            } else {
                return $this->error(trans('common.theme') . trans('common.template') . trans('common.edit') . trans('common.error'),
                    route('Admin::Template::index'));
            }
        }

        $editInfo            = $this->viewFiles[$id];
        $editInfo['content'] = htmlspecialchars(file_get_contents($filePath));
        $assign['id']        = $id;
        $assign['edit_info'] = $editInfo;

        $assign['title'] = trans('common.edit') . trans('common.theme') . trans('common.template') . trans('common.template');
        return view('admin.Template_addedit', $assign);
    }

    //删除
    public function del()
    {
        $this->commonInitialize();
        $id = request('id');
        if (!is_array($this->viewFiles[$id])) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Template::index'));
        }

        $fileName  = $this->viewFiles[$id]['file_name'];
        $filePath  = $this->viewPath . $fileName;
        $resultDel = unlink($filePath);
        if ($resultDel) {
            $this->_refresh_viewFiles();
            return $this->success(trans('common.theme') . trans('common.template') . trans('common.del') . trans('common.success'),
                route('Admin::Template::index'));
        } else {
            return $this->error(trans('common.theme') . trans('common.template') . trans('common.del') . trans('common.error'),
                route('Admin::Template::index'));
        }
    }

    //获取模板主题
    private function _get_theme_list()
    {
        $themePath  = resource_path('views/home/');
        $scanInfo   = scandir($themePath);
        $denyChange = ['.', '..', 'index.html'];
        $themeList  = [];
        foreach ($scanInfo as $info) {
            if (in_array($info, $denyChange)) {
                continue;
            }
            if (is_dir($themePath . $info)) {
                $themeList[] = $info;
            }
        }
        return $themeList;
    }

    //刷新模板文件列表
    private function _refresh_viewFiles()
    {
        //清空缓存
        $this->viewFiles = [];
        //获取最新的文件信息
        $this->_get_viewFiles();
        //保存缓存
        mPutArr($this->viewPath . $this->tplInfoFile, $this->viewFiles, false);
    }

    //刷新模板文件列表
    private function _get_viewFiles()
    {
        if (!is_dir($this->viewPath)) {
            return false;
        }

        //把文件中的缓存取出
        $themeInfo = mGetArr($this->viewPath . $this->tplInfoFile);

        //构造模板文件
        //只扫描两级目录不进行递归 跳过 'index.html'
        $scanInfo = scandir($this->viewPath);

        $denyChange = ['.', '..', 'theme_info.php'];
        foreach ($scanInfo as $info) {
            if (in_array($info, $denyChange)) {
                continue;
            }
            $newPath = $this->viewPath . $info;
            if (is_file($newPath)) {
                $fileMd5                   = md5($info);
                $this->viewFiles[$fileMd5] = [
                    'file_name' => $info,
                    'name'      => '',
                    'info'      => '',
                ];
                if ($themeInfo && isset($themeInfo[$fileMd5])) {
                    $this->viewFiles[$fileMd5]['name'] = $themeInfo[$fileMd5]['name'];
                    $this->viewFiles[$fileMd5]['info'] = $themeInfo[$fileMd5]['info'];
                }
            }
        }
    }
}
