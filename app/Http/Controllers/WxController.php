<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use App\Model\WxUserModel;

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
//        $signature = $_GET["signature"];
//        $timestamp = $_GET["timestamp"];
//        $nonce = $_GET["nonce"];
//
//        $token = env('WX_TOKEN');
//        $tmpArr = array($token, $timestamp, $nonce);
//        sort($tmpArr, SORT_STRING);
//        $tmpStr = implode( $tmpArr );
//        $tmpStr = sha1( $tmpStr );
//
//        if( $tmpStr == $signature ){
//            echo $_GET['echostr'];
//        }else{
//            echo "111";
//        }
        //接收数据
        $xml_str = file_get_contents("php://input");

        //记录日志
        $log_str = date('Y-m-d H:i:s') . $xml_str ." \n\n";
        file_put_contents("wx_event.log",$log_str);

        //将接收来的数据转化为对象
        $obj = simplexml_load_string($xml_str);
        //dd($obj);
        //echo $this->xml_obj;die;
        $this->xml_obj = $obj;

        $msg_type = $obj->MsgType;
        //dd($msg_type);
        switch ($msg_type)
        {
            case 'event';
                if($obj->Event == "subscride")
                {
                    //扫码关注
                    echo $this->subscride();
                    exit;
                }
                break;
        }
   	}



    /**
     * @return mixed
     *
     * 回复扫码关注
     */
    public function subscride()
    {
       //接收值
        //$userinfo = $this->getWxUserInfo();
        $ToUserName = $this->xml_obj->FromUserName;
        $FromUserName = $this->xml_obj->ToUserName;
        $wxUser = WxUserModel::where(['openid'=>$ToUserName])->first();
        if($wxUser)
        {
            $content = "欢迎回来 :".data("Y-m-d H:i:s");
        }else{
            //获取用户信息
            $user_info = $this->getWxUserInfo();

            //入库
            unset($user_info['subscribe']);
            unset($user_info['remark']);
            unset($user_info['groupid']);
            unset($user_info['substagid_listcribe']);
            unset($user_info['qr_scene']);
            unset($user_info['qr_scene_str']);
            unset($user_info['tagid_list']);

            WxUserModel::insertGetId($user_info);
            $content = "欢迎关注";
        }

        $xml="<xml>
              <ToUserName><![CDATA[".$ToUserName."]]></ToUserName>
              <FromUserName><![CDATA[".$FromUserName."]]></FromUserName>
              <CreateTime>time()</CreateTime>
              <MsgType><![CDATA[text]]></MsgType>
              <Content><![CDATA[".$content."]]></Content>
              </xml>";
        return $xml;
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
        //dd($this->xml_obj);die;
        $openid = $this->xml_obj->FromUserName;
        //dd($openid);
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'openid='.$openid.'&lang=zh_CN";

        //请求接口
        $client = new Client();
        $response = $client->request('GET',$url,[
           'verify' => false
        ]);
        return json_decode($response->getBody(),true);
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
