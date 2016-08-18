<?php
namespace Common\Behavior;
use Think\Behavior;
class ViewFilterBehavior extends Behavior
{
    public function run(&$return)
    {
        $this->_preg_system_js($return);
    }
    
    //集合css和js
    public function _preg_system_import(&$return)
    {
        //整合文件阀值
        $allow_number = 1;
        //1.删除被注释包裹的标签
        $html = preg_replace('/<!--.*?-->/is','', $return);
        //2.查询可以缓存的css js文件
        $preg_find_css_js = '/(';
        $preg_find_css_js .= '(<link[^>]*?\stype=(\'|\")text\/css\3.*?\/>)';
        $preg_find_css_js .= '|(<script[^>]*?\stype=(\'|\")text\/javascript\5.*?>)';
        $preg_find_css_js .= ')/is';
        $preg_result = preg_match_all($preg_find_css_js,$html,$matches);
        if(0 == $preg_result)return '';
        //3.缓存可以缓存的文件 要求本地
        $tag_replace = array('/','.');
        $tag_replace_re = array('\/','\.');
        $css_cache_change = $js_cache_change = false;
        $css_files = $js_files = $css_files_cache = $js_files_cache = array();
        $css_files_index = $js_files_index = array();
        $preg_remove_css_js = array();
        $pass_root = str_replace($tag_replace,$tag_replace_re,__ROOT__.'/');
        
        foreach($matches[2] as $css_tag)
        {
            preg_match('/href=(\'|\")'.$pass_root.'(.*?)\1/i',$css_tag,$re_href);
            $file = $re_href[2];
            if(!$file || !is_file($file) || in_array($file,$css_files))continue;
            $css_files[] = $file;
        }
        if($allow_number < count($css_files))
        {
            //5.1缓存css
            $css_files_cache = F('SYSTEM_IMPORT_CSS');
            $css_file_name = md5('SYSTEM_IMPORT_'.implode('',$css_files));
            foreach($css_files as $file)
            {
                $preg_file = $pass_root.str_replace($tag_replace,$tag_replace_re,$file);
                $preg_remove_css_js[] = '/[\r\n\s]*<link.*?\shref=(\'|\")'.$preg_file.'\1.*?\/?>/i';
                
                $md5_file = md5($file);
                $css_files_index[] = $md5_file;
                if(($css_files_cache[$md5_file]['expire'] == 0 || $css_files_cache[$md5_file]['expire'] > time()) && !APP_DEBUG)continue;
                $file_content = file_get_contents($file);
                $urlpreg = M_get_urlpreg(__ROOT__ . '/' . dirname($file) . '/');
                $file_content = preg_replace($urlpreg['pattern'],$urlpreg['replacement'],$file_content);
                $css_files_cache[$md5_file]['expire'] = (0 < C('TMPL_CACHE_TIME'))?time() + C('TMPL_CACHE_TIME'):false;
                $css_files_cache[$md5_file]['content'] = $file_content;
                $css_cache_change = true;
            }
            $css_cache_change && F('SYSTEM_IMPORT_CSS',$css_files_cache);
            F($css_file_name,$css_files_index);
            //6.插入调用已经缓存的DOM代码
            $parseStr = "\r\n".'<link rel="stylesheet" type="text/css" href="' . U('Home/Index/cache',array('type'=>'css','id'=>$css_file_name),'css') . '" />\1';
            $return = preg_replace('/(<\/head>)/i',$parseStr,$return);
        }
        
        foreach($matches[4] as $js_tag)
        {
            preg_match('/src=(\'|\")'.$pass_root.'(.*?)\1/i',$js_tag,$re_src);
            $file = $re_src[2];
            if(!$file || !is_file($file) || in_array($file,$js_files))continue;
            $js_files[] = $file;
        }
        if($allow_number < count($js_files))
        {
            //5.2缓存js
            $js_files_cache = F('SYSTEM_IMPORT_JS');
            $js_file_name = md5('SYSTEM_IMPORT_'.implode('',$js_files));
            foreach($js_files as &$file)
            {
                $preg_file = $pass_root.str_replace($tag_replace,$tag_replace_re,$file);
                $preg_remove_css_js[] = '/[\r\n\s]*<script.*?\ssrc=(\'|\")'.$preg_file.'\1.*?<\/script>/i';
                
                $md5_file = md5($file);
                $js_files_index[] = $md5_file;
                if(($css_files_cache[$md5_file]['expire'] == 0 || $css_files_cache[$md5_file]['expire'] > time()) && !APP_DEBUG)continue;
                $file_content = file_get_contents($file);
                $js_files_cache[$md5_file]['expire'] = (0 < C('TMPL_CACHE_TIME'))?time() + C('TMPL_CACHE_TIME'):0;
                $js_files_cache[$md5_file]['content'] = $file_content;
                $js_cache_change = true;
            }
            $js_cache_change && F('SYSTEM_IMPORT_JS',$js_files_cache);
            F($js_file_name,$js_files_index);
            //6.插入调用已经缓存的DOM代码
            $parseStr = "\r\n".'<script type="text/javascript" src="' . U('Home/Index/cache',array('type'=>'js','id'=>$js_file_name),'js') . '" ></script>\1';
            $return = preg_replace('/(<\/head>)/i',$parseStr,$return);
        }
        //4.从DOM中删除可以被系统缓存的文件
        $return = preg_replace($preg_remove_css_js,'', $return);
    }
}

namespace Common\Controller;
use Think\Controller;
class CommonendController extends Controller
{
    public function cache()
    {
        switch($type)
        {
            case 'css':
                header_remove();
                header('Expires:'.gmdate('D, d M Y H:i:s',time()+60*60).' GMT');
                header('Content-Type:text/css;charset=utf-8');
                $sys_import = F('SYSTEM_IMPORT_CSS');
                foreach($cache as $index)
                {
                    $echo_cache .= "\n".$sys_import[$index]['content'];
                }
                flush();
                break;
            case 'js':
                header_remove();
                header('Expires:'.gmdate('D, d M Y H:i:s',time()+60*60).' GMT');
                header('Content-Type:text/javascript;charset=utf-8');
                $sys_import = F('SYSTEM_IMPORT_JS');
                foreach($cache as $index)
                {
                    $echo_cache .= "\n".$sys_import[$index]['content'];
                }
                ob_flush();
                flush();
                break;
        }
    }
    
}


?>