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
// 前台 默认主页

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Index extends Frontend
{
    public function index()
    {
        /*
         * 检测移动端
         *
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        $uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
        if( ($ua == '' || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']),'wap') )
        {
        redirect(M_U('article_category',1));
        }
         */
        $this->display();
    }

    public function test()
    {
        $this->display();
    }
}
