<?php
// 前台 招聘

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Recruit extends Frontend
{
    //列表
    public function index()
    {
        $recruitList = Model\Recruit::where(function ($query) {
            $currentTime = Carbon::now();
            $query->where('start_time', '<', $currentTime);
            $query->where('end_time', '>', $currentTime);
            $query->where(function ($query) {
                $query->whereColumn('current_portion', '<', 'max_portion');
                $query->orWhere('max_portion', '=', 0);
            });
            $keyword = request('keyword');
            if ($keyword) {
                $keyword = '%' . $keyword . '%';
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('title', 'like', $keyword);
                    $query->orWhere('explains', 'like', $keyword);
                });
            }
        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['recruit_list'] = $recruitList;

        $assign['title']        = trans('recruit.recruit');
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
            return $this->error(trans('recruit.re_recruit') . trans('common.number') . trans('common.gt') . trans('recruit.recruit') . trans('common.number'),
                route('Home::Recruit::index'));
        }

        //存入数据
        if (request()->isMethod('POST')) {
            $data                = [];
            $data['r_id']        = $id;
            $data['name']        = request('name');
            $data['birthday'] = request('birthday');
            $data['sex']         = request('sex');
            $data['certificate'] = request('certificate');
            $data['ext_info']    = request('ext_info');
            $resultAdd           = Model\RecruitLog::create($data);
            if ($resultAdd) {
                Model\Recruit::where(['id' => $recruitInfo['id']])->increment('current_portion');
                return $this->success(trans('recruit.resume') . trans('common.submit') . trans('common.success'),
                    route('Home::Recruit::index'));
            } else {
                return $this->error(trans('recruit.resume') . trans('common.submit') . trans('common.error'),
                    route('Home::Recruit::index'));
            }
        }
        //缓存数据 下文中的thumb可以换成招聘统一的图
        $cacheName       = 'Home::Recruit::add' . $id;
        $cacheValue      = Cache::get($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            $recruitInfo['explains'] = $cacheValue;
        } else {
            $recruitInfo['explains'] = mContent2ckplayer($recruitInfo['explains']);
            config('system.sys_article_sync_image') && $recruitInfo['explains'] = mAsyncImg($recruitInfo['explains']);
            $cacheValue = $recruitInfo['explains'];
            $expiresAt               = Carbon::now()->addSecond(config('system.sys_td_cache'));
            Cache::put($cacheName, $cacheValue, $expiresAt);
        }

        //以法定成年年龄为基准减去 18 + (10 = select_rang/2)
        $startYear            = date(config('system.sys_date'), mktime(0, 0, 0, date('m'), date('d'), date('Y') - 28));
        $assign['start_year'] = $startYear;
        $assign['title'] = trans('common.write') . trans('recruit.recruit');
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

        $recruitInfo     = Model\Recruit::colWhere($id)->first();
        $assign['recruit_info'] = $recruitInfo;
        $assign['title'] = trans('common.look') . trans('recruit.recruit');
        return view('home.Recruit_edit', $assign);
    }
}
