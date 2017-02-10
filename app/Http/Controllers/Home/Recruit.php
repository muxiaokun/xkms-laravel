<?php
// 前台 招聘

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;

class Recruit extends Frontend
{
    //列表
    public function index()
    {
        $currentTime                  = Carbon::now();
        $logCount                     = 0;
        $where                        = [
            'start_time' => ['lt', $currentTime],
            'end_time'   => ['gt', $currentTime],
            '(current_portion < max_portion OR max_portion = 0)',
        ];
        $keyword                      = request('keyword');
        if ($keyword) {
            $keyword             = '%' . $keyword . '%';
            $complex             = ['_logic' => 'or'];
            $complex['title']    = ['like', $keyword];
            $complex['explains'] = ['like', $keyword];
            $where['_complex']   = $complex;
        }

        $recruitList            = Model\Recruit::where($where)->get();
        $assign['recruit_list'] = $recruitList;

        $assign['title'] = trans('common.recruit');
        return view('home.Recruit_index', $assign);
    }

    //添加
    public function add()
    {
        //初始化参数
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Home::Recruit::index'));
        }

        $recruitInfo = Model\Recruit::colWhere($id)->first()->toArray();
        //检测是否能够提交
        $currentTime = Carbon::now();
        if ($recruitInfo['start_time'] < $currentTime && $recruitInfo['end_time'] < $currentTime) {
            return $this->error(trans('common.start') . trans('common.end') . trans('common.time') . trans('common.error'),
                route('Home::Recruit::index'));
        }
        if (0 != $recruitInfo['max_portion'] && $recruitInfo['current_portion'] >= $recruitInfo['max_portion']) {
            return $this->error(trans('common.re_recruit') . trans('common.number') . trans('common.gt') . trans('common.recruit') . trans('common.number'),
                route('Home::Recruit::index'));
        }
        //存入数据
        if (request()->isMethod('POST')) {
            $data                = [];
            $data['r_id']        = $id;
            $data['name']        = request('name');
            $data['birthday']    = mMktime(request('birthday'));
            $data['sex']         = request('sex');
            $data['certificate'] = request('certificate');
            $data['ext_info']    = request('ext_info');
            $data['file_path']   = request('file_path');
            $resultAdd           = Model\RecruitLog::create($data);
            if ($resultAdd) {
                Model\Recruit::where(['id' => $recruitInfo['id']])->setInc('current_portion');
                return $this->success(trans('common.resume') . trans('common.submit') . trans('common.success'),
                    route('Home::Recruit::index'));
            } else {
                return $this->error(trans('common.resume') . trans('common.submit') . trans('common.error'),
                    route('Home::Recruit::index'));
            }
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
        $startYear            = date(config('system.sys_date'), mktime(0, 0, 0, date('m'), date('d'), date('Y') - 28));
        $assign['start_year'] = $startYear;
        $assign['title']      = trans('common.write') . trans('common.recruit');
        return view('home.Recruit_add', $assign);
    }

    //查看
    public function edit()
    {
        //初始化参数
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Home::Recruit::index'));
        }

        $recruitInfo = Model\Recruit::colWhere($id)->first()->toArray();
        $assign['recruit_info'] = $recruitInfo;
        $assign['title']        = trans('common.look') . trans('common.recruit');
        return view('home.Recruit_edit', $assign);
    }
}
