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
// 全局 View Filter 行为控制器
// PS:为了给rewrite模式下所有的外部资源和连接加绝对路径前缀
namespace App\Library;

use Think\Behavior;

class ViewFilterBehavior extends Behavior
{
    public function run(&$return)
    {
        $this->_preg_system_js($return);
        //替换链接前缀一定要后置
        $this->_preg_src($return);
    }

    //给所有的页面添加系统变量js 不放业务逻辑和引入文件 支持system_js
    private function _preg_system_js(&$return)
    {
        // 抄不使用布局
        if (false !== strpos($return, '{__NOSYSTEMJS__}')) { // 可以单独定义不使用系统js引入
            $return = str_replace('{__NOSYSTEMJS__}', '', $return);
        } else { // 引入系统js
            $common_var = array();
            if (C('TOKEN_ON')) {
                $name = C('TOKEN_NAME', null, '__hash__');
                if (isset($_SESSION[$name])) { // 令牌数据无效
                    // 令牌验证码获取
                    $tokenKey            = md5($_SERVER['REQUEST_URI']);
                    $common_var['token'] = array(
                        'name'  => $name,
                        'value' => $tokenKey . '_' . $_SESSION[$name][$tokenKey],
                    );
                }
            } else {
                $common_var['token'] = false;
            }
            $common_var['root']                                  = __ROOT__ . '/';
            C('SYS_SYNC_IMAGE') && $common_var['sys_sync_image'] = __ROOT__ . '/' . C('SYS_SYNC_IMAGE');
            $common_var['lang']                                  = array_change_key_case(L());
            $replace                                             = '<script type="text/javascript" charset="utf-8" >var $Think = ' . json_encode($common_var) . '</script>\1 ';
            $return                                              = preg_replace('/(<script)/i', $replace, $return, 1);
        }
    }

    //处理src添加__ROOT__/前缀
    private function _preg_src(&$return)
    {
        if (!in_array(C('URL_MODEL'), array(URL_REWRITE, URL_PATHINFO))) {
            return;
        }

        //断言已经处理过的和不需要处理的链接前缀
        $urlpreg = M_get_urlpreg(__ROOT__ . '/');
        $return  = preg_replace($urlpreg['pattern'], $urlpreg['replacement'], $return);
    }
}
