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

//Route::any("/wx","WxController@AccessToken"); //接收事件推送
Route::post("/wx/event","WxController@wxEvent"); //关注回复
Route::any("/wx/token","WxController@getAccessToken");
Route::get("/wx/menu","WxController@createMenu");
Route::get("/wx/subscr","WxController@subscride");



Route::get("/test1","WxController@test1");
Route::post("/test2","WxController@test2");

Route::prefix('/test')->group(function(){
   Route::get('/guzzle1','TestController@guzzle1');
});
