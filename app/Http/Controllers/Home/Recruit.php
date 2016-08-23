<?php
// 前台 招聘

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Recruit extends Frontend
{
    //列表
    public function index()
    {
        $RecruitModel = D('Recruit');
        $currentTime = time();
        $logCount    = 0;
        $where        = array(
            'start_time' => array('lt', $currentTime),
            'end_time'   => array('gt', $currentTime),
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

        $recruitList = $RecruitModel->mSelect($where);
        $this->assign('recruit_list', $recruitList);
        $this->assign('recruit_list_count', $RecruitModel->mGetPageCount($where));

        $this->assign('title', trans('recruit'));
        $this->display();
    }

    //添加
    public function add()
    {
        //初始化参数
        $id = I('id');
        if (!$id) {
            $this->error(trans('id') . L('error'), route('Recruit/index'));
        }

        $RecruitModel = D('Recruit');
        $recruitInfo = $RecruitModel->mFind($id);
        //检测是否能够提交
        $currentTime = time();
        if ($recruitInfo['start_time'] < $currentTime && $recruitInfo['end_time'] < $currentTime) {
            $this->error(trans('start') . L('end') . L('time') . L('error'), route('Recruit/index'));
        }
        if (0 != $recruitInfo['max_portion'] && $recruitInfo['current_portion'] >= $recruitInfo['max_portion']) {
            $this->error(trans('re_recruit') . L('number') . L('gt') . L('recruit') . L('number'), route('Quests/index'));
        }
        //存入数据
        if (IS_POST) {
            $data                = array();
            $data['r_id']        = $id;
            $data['name']        = I('name');
            $data['birthday']    = mMktime(I('birthday'));
            $data['sex']         = I('sex');
            $data['certificate'] = I('certificate');
            $data['ext_info']    = I('ext_info');
            $data['file_path']   = I('file_path');
            $RecruitLogModel     = D('RecruitLog');
            $resultAdd          = $RecruitLogModel->mAdd($data);
            if ($resultAdd) {
                $RecruitModel = D('Recruit');
                $RecruitModel->where(array('id' => $recruitInfo['id']))->setInc('current_portion');
                $this->success(trans('resume') . L('submit') . L('success'), route('Recruit/index'));
            } else {
                $this->error(trans('resume') . L('submit') . L('error'), route('index'));
            }
            return;
        }

        //缓存数据 下文中的thumb可以换成招聘统一的图
        $cacheName  = MODULE_NAME . CONTROLLER_NAME . 'add' . $id;
        $cacheValue = S($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            $recruitInfo['explains'] = $cacheValue;
        } else {
            $recruitInfo['explains']                                = mContent2ckplayer($recruitInfo['explains'], $recruitInfo['thumb']);
            config('SYS_ARTICLE_SYNC_IMAGE') && $recruitInfo['explains'] = mSyncImg($recruitInfo['explains']);
            $cacheValue                                             = $recruitInfo['explains'];
            S($cacheName, $cacheValue, config('SYS_TD_CACHE'));
        }

        //以法定成年年龄为基准减去 18 + (10 = select_rang/2)
        $startYear = date(config('SYS_DATE'), mktime(0, 0, 0, date('m'), date('d'), date('Y') - 28));
        $this->assign('start_year', $startYear);
        $this->assign('title', trans('write') . L('recruit'));
        $this->display();
    }

    //查看
    public function edit()
    {
        //初始化参数
        $id = I('id');
        if (!$id) {
            $this->error(trans('id') . L('error'), route('Recruit/index'));
        }

        $RecruitModel = D('Recruit');
        $recruitInfo = $RecruitModel->mFind($id);
        $this->assign('recruit_info', $recruitInfo);
        $this->assign('title', trans('look') . L('recruit'));
        $this->display();
    }
}
