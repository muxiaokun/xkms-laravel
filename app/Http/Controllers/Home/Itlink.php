<?php
// 前台 图文链接

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Itlink extends Frontend
{
    public function index()
    {
        $id = I('id');
        if (!$id) {
            $this->error(trans('itlink') . L('id') . L('error'), route('Index/index'));
        }

        $link        = I('link');
        $ItlinkModel = D('Itlink');
        $itlinkInfo = $ItlinkModel->where(array('id' => $id))->find();
        $itlinkInfo['max_hit_num'] > 0 && $ItlinkModel->where(array('id' => $id))->setInc('hit_num');
        ob_end_clean();
        header('location:' . base64_decode($link));
    }
}
