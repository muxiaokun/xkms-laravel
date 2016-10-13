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
                $where['parent_id'] = ($data['id']) ? $data['id'] : 0;
                $count              = Model\Region::where($where)->count();
                $regionUserList     = Model\Region::field('id,region_name')->limit($count)->mSelect($where);
                $result['info']     = $regionUserList;
                break;
        }
        return $result;
    }
}
