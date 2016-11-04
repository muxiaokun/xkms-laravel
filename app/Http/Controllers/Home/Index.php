<?php
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
            redirect(route('Home::Article::category',['id'=>1]));
        }
         */
        return view('home.Index_index');
    }

    public function test()
    {
        return view('home.Index_test');
    }
}
