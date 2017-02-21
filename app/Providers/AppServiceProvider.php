<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extendImplicit('user_name', 'App\Http\Validators\Common@user_name');
        Validator::extendImplicit('password', 'App\Http\Validators\Common@password');
        Validator::extend('privilege', 'App\Http\Validators\Common@privilege');
        Validator::extend('phone', 'App\Http\Validators\Common@phone');
        Validator::extend('short_name', 'App\Http\Validators\Common@short_name');
        Validator::extend('admin_exist', 'App\Http\Validators\Admin@admin_exist');
        Validator::extend('admin_group_exist', 'App\Http\Validators\AdminGroup@admin_group_exist');
        Validator::extend('member_exist', 'App\Http\Validators\Member@member_exist');
        Validator::extend('member_group_exist', 'App\Http\Validators\MemberGroup@member_group_exist');
        Validator::extend('itlink_name_exist', 'App\Http\Validators\Itlink@itlink_name_exist');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
