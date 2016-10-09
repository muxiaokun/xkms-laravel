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
        $currentTime  = time();
        $logCount     = 0;
        $where        = [
            'start_time' => ['lt', $currentTime],
            'end_time'   => ['gt', $currentTime],
            '(current_portion < max_portion OR max_portion = 0)',
        ];
        $keyword      = request('keyword');
        if ($keyword) {
            $keyword             = '%' . $keyword . '%';
            $complex             = ['_logic' => 'or'];
            $complex['title']    = ['like', $keyword];
            $complex['explains'] = ['like', $keyword];
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
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('Recruit/index'));
        }

        $RecruitModel = D('Recruit');
        $recruitInfo  = $RecruitModel->mFind($id);
        //检测是否能够提交
        $currentTime = time();
        if ($recruitInfo['start_time'] < $currentTime && $recruitInfo['end_time'] < $currentTime) {
            $this->error(trans('start') . trans('end') . trans('time') . trans('error'), route('Recruit/index'));
        }
        if (0 != $recruitInfo['max_portion'] && $recruitInfo['current_portion'] >= $recruitInfo['max_portion']) {
            $this->error(trans('re_recruit') . trans('number') . trans('gt') . trans('recruit') . trans('number'),
                route('Quests/index'));
        }
        //存入数据
        if (IS_POST) {
            $data                = [];
            $data['r_id']        = $id;
            $data['name']        = request('name');
            $data['birthday']    = mMktime(request('birthday'));
            $data['sex']         = request('sex');
            $data['certificate'] = request('certificate');
            $data['ext_info']    = request('ext_info');
            $data['file_path']   = request('file_path');
            $RecruitLogModel     = D('RecruitLog');
            $resultAdd           = $RecruitLogModel->mAdd($data);
            if ($resultAdd) {
                $RecruitModel = D('Recruit');
                $RecruitModel->where(['id' => $recruitInfo['id']])->setInc('current_portion');
                $this->success(trans('resume') . trans('submit') . trans('success'), route('Recruit/index'));
            } else {
                $this->error(trans('resume') . trans('submit') . trans('error'), route('index'));
            }
            return;
        }

        //缓存数据 下文中的thumb可以换成招聘统一的图
        $cacheName  = MODULE_NAME . CONTROLLER_NAME . 'add' . $id;
        $cacheValue = S($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            $recruitInfo['explains'] = $cacheValue;
        } else {
            $recruitInfo['explains'] = mContent2ckplayer($recruitInfo['explains'], $recruitInfo['thumb']);
            config('system.sys_article_sync_image') && $recruitInfo['explains'] = mSyncImg($recruitInfo['explains']);
            $cacheValue = $recruitInfo['explains'];
            S($cacheName, $cacheValue, config('system.sys_td_cache'));
        }

        //以法定成年年龄为基准减去 18 + (10 = select_rang/2)
        $startYear = date(config('system.sys_date'), mktime(0, 0, 0, date('m'), date('d'), date('Y') - 28));
        $this->assign('start_year', $startYear);
        $this->assign('title', trans('write') . trans('recruit'));
        $this->display();
    }

    //查看
    public function edit()
    {
        //初始化参数
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('Recruit/index'));
        }

        $RecruitModel = D('Recruit');
        $recruitInfo  = $RecruitModel->mFind($id);
        $this->assign('recruit_info', $recruitInfo);
        $this->assign('title', trans('look') . trans('recruit'));
        $this->display();
    }
}
