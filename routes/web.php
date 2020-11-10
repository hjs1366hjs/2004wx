<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/info', function () {
//    phpinfo();
//});

Route::get("/wx","WxController@AccessToken"); //接收事件推送
Route::get("/wx/event","WxController@AccessToken"); //关注回复

Route::get("/wx/token","WxController@getAccessToken");

Route::get("/test1","WxController@test1");
Route::post("/test2","WxController@test2");

Route::prefix('/test')->group(function(){
   Route::get('/guzzle1','TestController@guzzle1');
});
