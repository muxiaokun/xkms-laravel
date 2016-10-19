<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group([
    'as'        => 'Home::',
    //'middleware'=>'auth',
    'namespace' => 'Home',
    'prefix'    => 'Home',
], function () {
    Route::group([
        'as'     => 'Article::',
        //'middleware'=>'auth',
        'prefix' => 'Article',
    ], function () {
        Route::get('article', ['as' => 'article', 'uses' => 'Article@article']);
        Route::get('category', ['as' => 'category', 'uses' => 'Article@category']);
        Route::get('channel', ['as' => 'channel', 'uses' => 'Article@channel']);
        Route::get('search', ['as' => 'search', 'uses' => 'Article@search']);
    });
    Route::group([
        'as'     => 'Assess::',
        //'middleware'=>'auth',
        'prefix' => 'Assess',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Assess@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Assess@add']);
    });
    Route::group([
        'as'     => 'Comment::',
        //'middleware'=>'auth',
        'prefix' => 'Comment',
    ], function () {
    });
    Route::group([
        'as'     => 'Index::',
        //'middleware'=>'auth',
        'prefix' => 'Index',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Index@index']);
        Route::get('test', ['as' => 'test', 'uses' => 'Index@test']);
    });
    Route::group([
        'as'     => 'Itlink::',
        //'middleware'=>'auth',
        'prefix' => 'Itlink',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Itlink@index']);
    });
    Route::group([
        'as'     => 'Member::',
        //'middleware'=>'auth',
        'prefix' => 'Member',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Member@index']);
        Route::get('login', ['as' => 'login', 'uses' => 'Member@login']);
        Route::get('register', ['as' => 'register', 'uses' => 'Member@register']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'Member@logout']);
    });
    Route::group([
        'as'     => 'Message::',
        //'middleware'=>'auth',
        'prefix' => 'Message',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Message@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Message@add']);
        Route::get('del', ['as' => 'del', 'uses' => 'Message@del']);
    });
    Route::group([
        'as'     => 'MessageBoard::',
        //'middleware'=>'auth',
        'prefix' => 'MessageBoard',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'MessageBoard@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'MessageBoard@add']);
    });
    Route::group([
        'as'     => 'Quests::',
        //'middleware'=>'auth',
        'prefix' => 'Quests',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Quests@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Quests@add']);
    });
    Route::group([
        'as'     => 'Recruit::',
        //'middleware'=>'auth',
        'prefix' => 'Recruit',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Recruit@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Recruit@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Recruit@edit']);
    });
    Route::group([
        'as'     => 'Region::',
        //'middleware'=>'auth',
        'prefix' => 'Region',
    ], function () {
    });
    Route::group([
        'as'     => 'Wechat::',
        //'middleware'=>'auth',
        'prefix' => 'Wechat',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Wechat@index']);
        Route::get('member', ['as' => 'member', 'uses' => 'Wechat@member']);
    });

});

Route::group([
    'as'        => 'Admin::',
    //'middleware'=>'auth',
    'namespace' => 'Admin',
    'prefix'    => 'Admin',
], function () {
    Route::group([
        'as'     => 'Admin::',
        //'middleware'=>'auth',
        'prefix' => 'Admin',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Admins@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Admins@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Admins@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Admins@del']);
        Route::get('setting', ['as' => 'setting', 'uses' => 'Admins@setting']);
    });
    Route::group([
        'as'     => 'AdminGroup::',
        //'middleware'=>'auth',
        'prefix' => 'AdminGroup',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'AdminGroup@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'AdminGroup@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'AdminGroup@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'AdminGroup@del']);
    });
    Route::group([
        'as'     => 'AdminLog::',
        //'middleware'=>'auth',
        'prefix' => 'AdminLog',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'AdminLog@index']);
        Route::get('del', ['as' => 'del', 'uses' => 'AdminLog@del']);
        Route::get('del', ['as' => 'del', 'uses' => 'AdminLog@del']);
    });
    Route::group([
        'as'     => 'Article::',
        //'middleware'=>'auth',
        'prefix' => 'Article',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Article@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Article@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Article@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Article@del']);
        Route::get('setting', ['as' => 'setting', 'uses' => 'Article@setting']);
    });
    Route::group([
        'as'     => 'ArticleCategory::',
        //'middleware'=>'auth',
        'prefix' => 'ArticleCategory',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'ArticleCategory@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'ArticleCategory@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'ArticleCategory@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'ArticleCategory@del']);
    });
    Route::group([
        'as'     => 'ArticleChannel::',
        //'middleware'=>'auth',
        'prefix' => 'ArticleChannel',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'ArticleChannel@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'ArticleChannel@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'ArticleChannel@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'ArticleChannel@del']);
    });
    Route::group([
        'as'     => 'Assess::',
        //'middleware'=>'auth',
        'prefix' => 'Assess',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Assess@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Assess@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Assess@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Assess@del']);
    });
    Route::group([
        'as'     => 'AssessLog::',
        //'middleware'=>'auth',
        'prefix' => 'AssessLog',
    ], function () {
        Route::get('edit', ['as' => 'edit', 'uses' => 'AssessLog@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'AssessLog@del']);
    });
    Route::group([
        'as'     => 'Comment::',
        //'middleware'=>'auth',
        'prefix' => 'Comment',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Comment@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Comment@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Comment@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Comment@del']);
    });
    Route::group([
        'as'     => 'Index::',
        //'middleware'=>'auth',
        'prefix' => 'Index',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Index@index']);
        Route::get('cleanCache', ['as' => 'cleanCache', 'uses' => 'Index@cleanCache']);
        Route::get('cleanLog', ['as' => 'cleanLog', 'uses' => 'Index@cleanLog']);
        Route::get('topNav', ['as' => 'topNav', 'uses' => 'Index@topNav']);
        Route::get('leftNav', ['as' => 'leftNav', 'uses' => 'Index@leftNav']);
        Route::get('main', ['as' => 'main', 'uses' => 'Index@main']);
        Route::post('login', ['as' => 'login', 'uses' => 'Index@login']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'Index@logout']);
        Route::post('ajax_api', ['as' => 'ajax_api', 'uses' => 'Index@ajax_api']);
        Route::match(['get', 'post'], 'websiteSet', ['as' => 'websiteSet', 'uses' => 'Index@websiteSet']);
        Route::match(['get', 'post'], 'systemSet', ['as' => 'systemSet', 'uses' => 'Index@systemSet']);
        Route::match(['get', 'post'], 'databaseSet', ['as' => 'databaseSet', 'uses' => 'Index@databaseSet']);
        Route::match(['get', 'post'], 'editMyPass', ['as' => 'editMyPass', 'uses' => 'Index@editMyPass']);
    });
    Route::group([
        'as'     => 'Itlink::',
        //'middleware'=>'auth',
        'prefix' => 'Itlink',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Itlink@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Itlink@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Itlink@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Itlink@del']);
    });
    Route::group([
        'as'     => 'ManageUpload::',
        //'middleware'=>'auth',
        'prefix' => 'ManageUpload',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'ManageUpload@index']);
        Route::get('del', ['as' => 'del', 'uses' => 'ManageUpload@del']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'ManageUpload@edit']);
        Route::get('UploadFile', ['as' => 'UploadFile', 'uses' => 'ManageUpload@UploadFile']);
        Route::get('ManageFile', ['as' => 'ManageFile', 'uses' => 'ManageUpload@ManageFile']);
    });
    Route::group([
        'as'     => 'Member::',
        //'middleware'=>'auth',
        'prefix' => 'Member',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Member@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Member@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Member@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Member@del']);
        Route::get('setting', ['as' => 'setting', 'uses' => 'Member@setting']);
    });
    Route::group([
        'as'     => 'MemberGroup::',
        //'middleware'=>'auth',
        'prefix' => 'MemberGroup',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'MemberGroup@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'MemberGroup@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'MemberGroup@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'MemberGroup@del']);
    });
    Route::group([
        'as'     => 'Message::',
        //'middleware'=>'auth',
        'prefix' => 'Message',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Message@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Message@add']);
        Route::get('del', ['as' => 'del', 'uses' => 'Message@del']);
    });
    Route::group([
        'as'     => 'MessageBoard::',
        //'middleware'=>'auth',
        'prefix' => 'MessageBoard',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'MessageBoard@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'MessageBoard@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'MessageBoard@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'MessageBoard@del']);
    });
    Route::group([
        'as'     => 'MessageBoardLog::',
        //'middleware'=>'auth',
        'prefix' => 'MessageBoardLog',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'MessageBoardLog@index']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'MessageBoardLog@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'MessageBoardLog@del']);
    });
    Route::group([
        'as'     => 'Navigation::',
        //'middleware'=>'auth',
        'prefix' => 'Navigation',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Navigation@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Navigation@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Navigation@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Navigation@del']);
    });
    Route::group([
        'as'     => 'Quests::',
        //'middleware'=>'auth',
        'prefix' => 'Quests',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Quests@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Quests@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Quests@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Quests@del']);
    });
    Route::group([
        'as'     => 'QuestsAnswer::',
        //'middleware'=>'auth',
        'prefix' => 'QuestsAnswer',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'QuestsAnswer@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'QuestsAnswer@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'QuestsAnswer@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'QuestsAnswer@del']);
    });
    Route::group([
        'as'     => 'Recruit::',
        //'middleware'=>'auth',
        'prefix' => 'Recruit',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Recruit@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Recruit@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Recruit@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Recruit@del']);
    });
    Route::group([
        'as'     => 'RecruitLog::',
        //'middleware'=>'auth',
        'prefix' => 'RecruitLog',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'RecruitLog@index']);
        Route::get('del', ['as' => 'del', 'uses' => 'RecruitLog@del']);
    });
    Route::group([
        'as'     => 'Region::',
        //'middleware'=>'auth',
        'prefix' => 'Region',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Region@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Region@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Region@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Region@del']);
    });
    Route::group([
        'as'     => 'Template::',
        //'middleware'=>'auth',
        'prefix' => 'Template',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Template@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Template@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Template@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Template@del']);
    });
    Route::group([
        'as'     => 'Wechat::',
        //'middleware'=>'auth',
        'prefix' => 'Wechat',
    ], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Wechat@index']);
        Route::get('add', ['as' => 'add', 'uses' => 'Wechat@add']);
        Route::get('edit', ['as' => 'edit', 'uses' => 'Wechat@edit']);
        Route::get('del', ['as' => 'del', 'uses' => 'Wechat@del']);
    });
});
Route::group([
    'as'        => 'Install::',
    //'middleware'=>'auth',
    'namespace' => 'Install',
    'prefix'    => 'Install',
], function () {
    Route::get('index', ['as' => 'index', 'uses' => 'Index@index']);
    Route::get('scan/{name?}', ['as' => 'scan', 'uses' => 'Index@scan']);
    Route::get('setp0', ['as' => 'setp0', 'uses' => 'Index@setp0']);
    Route::get('setp1', ['as' => 'setp1', 'uses' => 'Index@setp1']);
    Route::get('setp2', ['as' => 'setp2', 'uses' => 'Index@setp2']);
    Route::post('setp3', ['as' => 'setp3', 'uses' => 'Index@setp3']);
    Route::get('setp4', ['as' => 'setp4', 'uses' => 'Index@setp4']);
    Route::post('ajax_api', ['as' => 'ajax_api', 'uses' => 'Index@ajax_api']);
});

Route::get('Minify/{type}', 'Minify@run')->name('Minify');
Route::get('VerificationCode/{name?}', 'VerificationCode@run')->name('VerificationCode');

//test error
Route::get('t', function () {

});
Route::get('tc', ['as' => 'index', 'uses' => 'Index@getInstallInfo']);