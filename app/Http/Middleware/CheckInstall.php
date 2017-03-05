<?php

namespace App\Http\Middleware;

use Closure;

class CheckInstall extends Common
{
    public function handle($request, Closure $next)
    {
        //没有安装，跳转到安装页
        if (0 == env('INSTALL_STATUS')) {
            return $this->error(trans('common.please') . trans('common.install') . trans('common.app_name'),
                route('Install::index'));
        }
        return $next($request);
    }
}
