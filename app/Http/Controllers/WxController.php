<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WxController extends Controller
{
    //
    public function AccessToken()
    {
        //echo __LINE__;
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = env('WX_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            echo $_GET('echostr');
        }else{
            echo "";
        }
    }

    /**
     *
     * 关注回复
     *
     */
    public function wxEvent()
   	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = env('WX_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            //接收消息
            $xml_str = file_get_contents("php://input");
            file_put_contents('wx_event.log',$xml_str);
            echo '';
        }else{
            echo "";
        }
   	}

    //获取token
    public function getAccessToken()
    {
        $key = 'weixing:access_token';
        $token = Redis::get($key);
        if($token){

            echo "有缓存";
            echo'</br>';

        }else{

            echo '无缓存';
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('APP_ID')."&secret=".env('APP_SECRET')."";
            $resp = file_get_contents($url);

            $data = json_decode($resp,true);
            $token = $data['access_token'];
            //dd($token);
            //token保存到redis中 设置过期时间为3600
            Redis::set($key,$token);
            Redis::expire($key,3600);
        }
        echo "access_token:".$token;
    }

    //测试1
    public function test1()
    {
        print_r($_GET);
    }

    //测试2
    public function test2()
    {
        print_r($_POST);
    }

    /**
     *
     *测试guzzle 发送get请求
     *
     */
    public function guzzle1()
    {
        //echo __METHOD__;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('APP_ID').'&secret='..env('APP_SECRET')'";
    }
}
