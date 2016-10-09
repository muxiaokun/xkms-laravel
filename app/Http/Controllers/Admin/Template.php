<?php
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Template extends Backend
{
    //$tplInfoFile 与 Common/Common/function.php M_scan_template一致
    private $tplInfoFile  = 'theme_info';
    private $configFile   = '';
    private $defaultTheme = '';
    private $viewPath     = '';
    private $viewFiles    = [];

    public function _initialize()
    {
        parent::_initialize();
        //初始化模板目录
        $this->view_path = APP_PATH . config('DEFAULT_MODULE') . '/' . C('DEFAULT_V_LAYER') . '/';
        config('DEFAULT_THEME') && $this->view_path .= C('DEFAULT_THEME') . '/';
        //初始化模本文件列表
        $this->view_files = F($this->tpl_info_file, '', $this->view_path);
        if (!$this->view_files) {
            $this->_refresh_view_files();
        }

        //初始化配置文件所在位置
        $this->config_file   = APP_PATH . config('DEFAULT_MODULE') . '/Conf/' . 'config.php';
        $config              = include $this->config_file;
        $this->default_theme = $config['DEFAULT_THEME'];
    }

    //列表
    public function index()
    {
        //刷新模本文件列表
        if (1 == request('refresh')) {
            $this->_refresh_view_files();
            $this->success(trans('theme') . trans('template') . trans('refresh') . trans('success'), route('index'));
            return;
        }

        //修改默认主题配置
        $defaultTheme = request('default_theme');
        if (null !== $defaultTheme) {
            if ('empty' == $defaultTheme) {
                $defaultTheme = '';
            }

            $config['DEFAULT_THEME'] = $defaultTheme;
            $configStr               = var_export($config, true);
            $CoreCopyright           = config('CORE_COPYRIGHT');
            $putConfig               = <<<EOF
<?php
{$CoreCopyright}
// Home 配置
return {$configStr};
?>
EOF;
            file_put_contents($this->config_file, $putConfig);
            $this->success(trans('theme') . trans('selection') . trans('success'), route('index'));
            return;
        }

        //修改模板文件信息
        if (IS_POST) {
            foreach ($this->view_files as $fileMd5 => $info) {
                $postInfo                           = request($fileMd5);
                $this->view_files[$fileMd5]['name'] = $postInfo['name'];
                $this->view_files[$fileMd5]['info'] = $postInfo['info'];
            }

            //保存缓存
            F($this->tpl_info_file, $this->view_files, $this->view_path);
            $this->success(trans('theme') . trans('info') . trans('save') . trans('success'), route('index'));
            return;
        }

        $this->assign('default_theme', $this->default_theme);
        $this->assign('theme_list', $this->_get_theme_list());
        $this->assign('theme_info_list', $this->view_files);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('theme') . trans('template') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            //添加时不可以创建新的目录 必须使用系统设置的后缀
            $fileName = trim(request('file_name'));
            //处理2级目录
            $filePath = explode('/', $fileName);
            if (preg_match('/\.\.\//', $fileName)
                || !preg_match('/' . str_ireplace('.', '\.', config('TMPL_TEMPLATE_SUFFIX')) . '$/', $fileName)
            ) {
                $this->error(trans('file') . trans('name') . trans('error'), route('index'));
            }
            if (!$fileName) {
                $this->error(trans('file') . trans('name') . trans('not') . trans('empty'), route('index'));
            }

            $filePath = $this->view_path . $fileName;
            $fileMd5  = ($filePath);
            if (is_array($this->view_files[$fileMd5])) {
                $this->error(trans('file') . trans('name') . trans('repeat'), route('index'));
            }

            $content    = request('content', '', false);
            $resultEdit = file_put_contents($filePath, $content);
            if (false !== $resultEdit) {
                $this->_refresh_view_files();
                $this->success(trans('theme') . trans('template') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('theme') . trans('template') . trans('add') . trans('error'), route('index'));
            }
        }

        $this->assign('title', trans('add') . trans('theme') . trans('template') . trans('template'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!is_array($this->view_files[$id])) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $fileName = $this->view_files[$id]['file_name'];
        $filePath = $this->view_path . $fileName;
        if (IS_POST) {
            $content    = request('content', '', false);
            $resultEdit = file_put_contents($filePath, $content);
            if ($resultEdit) {
                $this->success(trans('theme') . trans('template') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('theme') . trans('template') . trans('edit') . trans('error'), route('index'));
            }
        }

        $editInfo            = $this->view_files[$id];
        $editInfo['content'] = htmlspecialchars(file_get_contents($filePath));
        $this->assign('id', $id);
        $this->assign('edit_info', $editInfo);

        $this->assign('title', trans('edit') . trans('theme') . trans('template') . trans('template'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!is_array($this->view_files[$id])) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $fileName  = $this->view_files[$id]['file_name'];
        $filePath  = $this->view_path . $fileName;
        $resultDel = unlink($filePath);
        if ($resultDel) {
            $this->_refresh_view_files();
            $this->success(trans('theme') . trans('template') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('theme') . trans('template') . trans('del') . trans('error'), route('index'));
        }
    }

    //获取模板主题
    private function _get_theme_list()
    {
        $themeList = [];
        if (config('DEFAULT_THEME')) {
            $themePath = str_ireplace(config('DEFAULT_THEME') . '/', '', $this->view_path);
        }

        $scanInfo   = scandir($themePath);
        $denyChange = ['.', '..', 'index.html'];
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
    private function _refresh_view_files()
    {
        //清空缓存
        $this->view_files = [];
        //获取最新的文件信息
        $this->_get_view_files($this->view_path);
        //保存缓存
        F($this->tpl_info_file, $this->view_files, $this->view_path);
    }

    //刷新模板文件列表
    private function _get_view_files($path, $level = 2)
    {
        if (!is_dir($path) || 1 > $level) {
            return false;
        }

        //把文件中的缓存取出
        $themeInfo = F($this->tpl_info_file, '', $path);

        //构造模板文件
        //只扫描两级目录不进行递归 跳过 'index.html'
        $scanInfo = scandir($path);

        $denyChange = ['.', '..', 'theme_info.php'];
        if (2 == $level) {
            array_push($denyChange, 'index.html');
        }

        foreach ($scanInfo as $info) {
            if (in_array($info, $denyChange)) {
                continue;
            }

            $newPath = $path . $info;
            if (is_dir($newPath)) {
                $this->_get_view_files($newPath . '/', $level - 1);
            } elseif (is_file($newPath)) {
                $relativeViewPath           = str_ireplace($this->view_path, '', $newPath);
                $fileMd5                    = md5($relativeViewPath);
                $this->view_files[$fileMd5] = [
                    'file_name' => $relativeViewPath,
                ];
                if ($themeInfo && $themeInfo[$fileMd5]) {
                    $this->view_files[$fileMd5]['name'] = $themeInfo[$fileMd5]['name'];
                    $this->view_files[$fileMd5]['info'] = $themeInfo[$fileMd5]['info'];
                }
            }
        }
    }
}
