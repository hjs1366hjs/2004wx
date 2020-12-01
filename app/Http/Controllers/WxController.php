<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use App\Model\WxUserModel;

class WxController extends Controller
{
    //
    public $xml_obj;

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
            echo "111";
        }
    }


    /**
     *
     * 处理事件推送
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
            echo $_GET['echostr'];
        }else{
            echo '11111';
        }
   	}



    /**
     * @return mixed
     *
     * 回复扫码关注
     */
    public function subscribe()
    {
       //接收值

//        //$userinfo = $this->getWxUserInfo();
//        $ToUserName = $this->xml_obj->FromUserName;
//        //print_r($ToUserName);
//        $FromUserName = $this->xml_obj->ToUserName;
//        //print_r($FromUserName);
//        $wxUser = WxUserModel::where(['openid'=>$ToUserName])->first();
//        //dd($wxUser);
//        if($wxUser)
//        {
//            $content = "欢迎回来 :".data("Y-m-d H:i:s");
//        }else{
//            //获取用户信息
//            $user_info = $this->getWxUserInfo();
//            //dd($user_info);
//            //入库
//            unset($user_info['subscribe']);
//            unset($user_info['remark']);
//            unset($user_info['groupid']);
//            unset($user_info['substagid_listcribe']);
//            unset($user_info['qr_scene']);
//            unset($user_info['qr_scene_str']);
//            unset($user_info['tagid_list']);
//            unset($user_info['errcode']);
//            unset($user_info['errmsg']);
//
//            WxUserModel::insertGetId($user_info);
//            $content = "欢迎关注 : ".date("Y-m-d H:i:s");
//        }
//
//        $xml="<xml>
//              <ToUserName><![CDATA[".$ToUserName."]]></ToUserName>
//              <FromUserName><![CDATA[".$FromUserName."]]></FromUserName>
//              <CreateTime>time()</CreateTime>
//              <MsgType><![CDATA[text]]></MsgType>
//              <Content><![CDATA[".$content."]]></Content>
//              </xml>";
//        return $xml;
    }

    /**
     * @return mixed
     *
     * 获取用户基本信息
     *
     */
    public function getWxUserInfo()
    {
        $token = $this->getAccessToken();
        //dd($token);
        $openid = $this->xml_obj->FromUserName;
        //print_r($openid);
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'openid='.$openid.'&lang=zh_CN";

        //请求接口
        $resp = file_get_contents($url);
        dd($resp);
//        $client = new Client();
//        $response = $client->request('GET',$url,[
//           'verify' => false
//        ]);
        //dd($token);
        //dd(json_decode($response->getBody(),true)) ;
    }

    //获取token
    public function getAccessToken()
    {
        $key = 'weixin:access_token';
        $token = Redis::get($key);
        //print_r($token);
        if($token){
            //echo "有缓存";
            return $token;
        }else{
            //echo "无缓存";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('APP_ID')."&secret=".env('APP_SECRET');
//            echo $url;die;
            $client = new Client();
            $response = $client->request('GET',$url,['verify'=>false]);
            $json_str = $response->getBody();
            $data = json_decode($json_str,true);
            $token = $data['access_token'];
            //dd($token);
            //token保存到redis中 设置过期时间为3600
            Redis::set($key,$token);
            Redis::expire($key,3600);
            return $token;
        }
    }

    /**
     *
     * 创建自定义菜单
     *
     */
    public function createMenu()
    {
        $accesstoken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accesstoken;

        $menu = [
            'button' => [
                "name" => "2004wx",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "天气",
                        "key" => "rselfmenu_0_0"
                    ],

//                    [
//                        "type" => "click",
//                        "name" => "商城",
//                        "url"  => "",
//                    ]
                ]
            ]
        ];

        $client = new Client();
        $request = $client->request('POST', $url, [
            'verify'=>false,
            'body' => json_encode($menu,JSON_UNESCAPED_UNICODE)
        ]);
        $data = $request->getBody();
        echo $data;
    }


    /**
     *
     *
     *
     */

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
