<?php

namespace App\Http\Middleware;

class Common
{
    protected function error($message = '', $backUrl = '')
    {
        if ('' == $message) {
            $message = trans('common.handle') . trans('common.error');
        }
        if ('' == $backUrl) {
            $backUrl = route(request()->route()->getName());
        }
        if (request()->ajax()) {
            $ajax_data = [
                'status' => false,
                'info'   => $message,
            ];
            return response($ajax_data);
        }
        $assign = [
            'status'   => false,
            'message'  => $message,
            'back_url' => $backUrl,
            'timeout'  => 3,
        ];
        return response(view('common.dispatch_jump', $assign));
    }
}
