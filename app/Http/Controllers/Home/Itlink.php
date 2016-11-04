<?php
// 前台 图文链接

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;

class Itlink extends Frontend
{
    public function index()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.itlink') . trans('common.id') . trans('common.error'),
                route('Home::ndex::index'));
        }

        $link       = request('link');
        $itlinkInfo = Model\Itlink::where(['id' => $id])->find();
        $itlinkInfo['max_hit_num'] > 0 && Model\Itlink::where(['id' => $id])->setInc('hit_num');
        ob_end_clean();
        header('location:' . base64_decode($link));
    }
}
