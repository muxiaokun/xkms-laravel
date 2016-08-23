<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
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
});