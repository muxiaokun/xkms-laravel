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
    'as'=>'Install::',
    //'middleware'=>'auth',
    'namespace'=>'Install',
    'prefix' => 'Install',
], function () {
    Route::get('t',function(){
        dump(config());
    });
    Route::get('',['as'=>'index','uses'=>'Index@index']);
    Route::get('setp0',['as'=>'setp0','uses'=>'Index@setp0']);
    Route::get('setp1',['as'=>'setp1','uses'=>'Index@setp1']);
    Route::get('setp2',['as'=>'setp2','uses'=>'Index@setp2']);
    Route::get('setp3',['as'=>'setp3','uses'=>'Index@setp3']);
    Route::get('setp4',['as'=>'setp4','uses'=>'Index@setp4']);
    Route::get('ajax_api',['as'=>'ajax_api','uses'=>'Index@ajax_api']);
});