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
// 前台 地域

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Region extends Frontend
{
    //异步获取数据接口
    protected function getData($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'parent_id':
                $where['parent_id'] = ($data['id']) ? $data['id'] : 0;
                $RegionModel        = D('Region');
                $count              = $RegionModel->where($where)->count();
                $regionUserList   = $RegionModel->field('id,region_name')->limit($count)->mSelect($where);
                $result['info']     = $regionUserList;
                break;
        }
        return $result;
    }
}
