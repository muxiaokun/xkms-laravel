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
                route('Home::index::index'));
        }

        $itlinkInfo = Model\Itlink::where(['id' => $id])->first();
        $itlinkInfo['max_hit_num'] > 0 && Model\Itlink::colWhere($id)->increment('hit_num');
        $link = base64_decode(request('link'));
        return redirect($link);
    }
}
