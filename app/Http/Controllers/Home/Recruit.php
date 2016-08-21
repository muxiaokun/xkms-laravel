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
// 前台 招聘

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Recruit extends Frontend
{
    //列表
    public function index()
    {
        $RecruitModel = D('Recruit');
        $current_time = time();
        $log_count    = 0;
        $where        = array(
            'start_time' => array('lt', $current_time),
            'end_time'   => array('gt', $current_time),
            '(current_portion < max_portion OR max_portion = 0)',
        );
        $keyword = I('keyword');
        if ($keyword) {
            $keyword             = '%' . $keyword . '%';
            $complex             = array('_logic' => 'or');
            $complex['title']    = array('like', $keyword);
            $complex['explains'] = array('like', $keyword);
            $where['_complex']   = $complex;
        }

        $recruit_list = $RecruitModel->mSelect($where);
        $this->assign('recruit_list', $recruit_list);
        $this->assign('recruit_list_count', $RecruitModel->getPageCount($where));

        $this->assign('title', L('recruit'));
        $this->display();
    }

    //添加
    public function add()
    {
        //初始化参数
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('Recruit/index'));
        }

        $RecruitModel = D('Recruit');
        $recruit_info = $RecruitModel->mFind($id);
        //检测是否能够提交
        $current_time = time();
        if ($recruit_info['start_time'] < $current_time && $recruit_info['end_time'] < $current_time) {
            $this->error(L('start') . L('end') . L('time') . L('error'), U('Recruit/index'));
        }
        if (0 != $recruit_info['max_portion'] && $recruit_info['current_portion'] >= $recruit_info['max_portion']) {
            $this->error(L('re_recruit') . L('number') . L('gt') . L('recruit') . L('number'), U('Quests/index'));
        }
        //存入数据
        if (IS_POST) {
            $data                = array();
            $data['r_id']        = $id;
            $data['name']        = I('name');
            $data['birthday']    = M_mktime(I('birthday'));
            $data['sex']         = I('sex');
            $data['certificate'] = I('certificate');
            $data['ext_info']    = I('ext_info');
            $data['file_path']   = I('file_path');
            $RecruitLogModel     = D('RecruitLog');
            $result_add          = $RecruitLogModel->mAdd($data);
            if ($result_add) {
                $RecruitModel = D('Recruit');
                $RecruitModel->where(array('id' => $recruit_info['id']))->setInc('current_portion');
                $this->success(L('resume') . L('submit') . L('success'), U('Recruit/index'));
            } else {
                $this->error(L('resume') . L('submit') . L('error'), U('index'));
            }
            return;
        }

        //缓存数据 下文中的thumb可以换成招聘统一的图
        $cache_name  = MODULE_NAME . CONTROLLER_NAME . 'add' . $id;
        $cache_value = S($cache_name);
        if ($cache_value && true !== APP_DEBUG) {
            $recruit_info['explains'] = $cache_value;
        } else {
            $recruit_info['explains']                                = M_content2ckplayer($recruit_info['explains'], $recruit_info['thumb']);
            C('SYS_ARTICLE_SYNC_IMAGE') && $recruit_info['explains'] = M_sync_img($recruit_info['explains']);
            $cache_value                                             = $recruit_info['explains'];
            S($cache_name, $cache_value, C('SYS_TD_CACHE'));
        }

        //以法定成年年龄为基准减去 18 + (10 = select_rang/2)
        $start_year = date(C('SYS_DATE'), mktime(0, 0, 0, date('m'), date('d'), date('Y') - 28));
        $this->assign('start_year', $start_year);
        $this->assign('title', L('write') . L('recruit'));
        $this->display();
    }

    //查看
    public function edit()
    {
        //初始化参数
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('Recruit/index'));
        }

        $RecruitModel = D('Recruit');
        $recruit_info = $RecruitModel->mFind($id);
        $this->assign('recruit_info', $recruit_info);
        $this->assign('title', L('look') . L('recruit'));
        $this->display();
    }
}
