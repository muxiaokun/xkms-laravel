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
// 前台 图文链接

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Itlink extends Frontend
{
    public function index()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('itlink') . L('id') . L('error'), U('Index/index'));
        }

        $link        = I('link');
        $ItlinkModel = D('Itlink');
        $itlink_info = $ItlinkModel->where(array('id' => $id))->find();
        $itlink_info['max_hit_num'] > 0 && $ItlinkModel->where(array('id' => $id))->setInc('hit_num');
        ob_end_clean();
        header('location:' . base64_decode($link));
    }
}
