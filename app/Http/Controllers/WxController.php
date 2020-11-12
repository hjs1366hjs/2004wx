<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;

class WxController extends Controller
{
    //
    protected $xml_obj;

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
            echo "111";
        }
        //接收数据
//        $xml_str = file_get_contents("php://input");
//
//        //记录日志
//        file_put_contents("wx_event.log",$xml_str);
//
//        //将接收来的数据转化为对象
//        $obj = simplexml_load_string($xml_str);
//        //dd($obj);
//
//        $this->xml_obj = $obj;
//        $msg_type = $obj->MsgType;
//        switch ($msg_type)
//        {
//            case 'event';
//                if($obj->Event == "subscride")
//                {
//                    echo $this->subscride();
//                    exit;
//                }
//        }
   	}



    /**
     * @return mixed
     *
     * 回复扫码关注
     */
    public function subscride()
    {
        echo 111;
        $content = "欢迎关注";
        $ToUserName = $this->xml_obj->FromUserName;
        $FromUserName = $this->xml_obj->ToUserName;
        $xml = "";
    }

    //获取token
    public function getAccessToken()
    {
        $key = 'weixing:access_token';
        $token = Redis::get($key);
        if($token){
            //echo "有缓存";
            //echo'</br>';

        }else{

            //echo '无缓存';
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('APP_ID')."&secret=".env('APP_SECRET');
//            echo $url;die;
            $resp = file_get_contents($url);

            $data = json_decode($resp,true);
            $token = $data['access_token'];
            //dd($token);
            //token保存到redis中 设置过期时间为3600
            Redis::set($key,$token);
            Redis::expire($key,3600);
        }
        return $token;
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
