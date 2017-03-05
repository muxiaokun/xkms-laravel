<?php

namespace App\Http\Middleware;

use Closure;

class CheckInstalled extends Common
{
    public function handle($request, Closure $next)
    {
        if (0 != env('INSTALL_STATUS')) {
            return redirect(route('root'));
        }
        return $next($request);
    }
}
