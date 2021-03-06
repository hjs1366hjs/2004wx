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
Route::any("/wx/event","WxController@wxEvent"); //关注回复
Route::get("/wx/token","WxController@getAccessToken");
Route::get("/wx/menu","WxController@createMenu");
Route::any("/wx/atten","WxController@attention");
Route::get("/wx/subscr","WxController@subscride");

//小程序
Route::get("/wx/xcxlog","XcxController@xcxlog");
Route::get("/wx/xcxgoods","XcxController@xcxgoods");
Route::get("/wx/detail","XcxController@xcxdetail");
Route::get("/wx/cart","XcxController@xcxcart");
//Route::any("/wx/userinfo","WxController@getWxUserInfo");

Route::get("/test1","WxController@test1");
Route::post("/test2","WxController@test2");

Route::prefix('/test')->group(function(){
   Route::get('/guzzle1','TestController@guzzle1');
});
