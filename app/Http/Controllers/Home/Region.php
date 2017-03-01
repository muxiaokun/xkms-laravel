<?php
// 前台 地域

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;

class Region extends Frontend
{
    //异步获取数据接口
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'parent_id':
                $where[]        = ['parent_id', '=', ($data['id']) ? $data['id'] : 0];
                $regionUserList = Model\Region::select(['id', 'region_name as name'])->where($where)->get();
                $result['info'] = $regionUserList;
                break;
        }
        return $result;
    }
}
