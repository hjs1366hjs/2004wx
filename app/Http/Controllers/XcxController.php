<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\GoodsModel;
use App\Model\UserModel;

class XcxController extends Controller
{
    //
    public function xcxgoods(Request $request)
    {
        //echo '111';die;
        $page_size = $request->get('ps');
        $g_name = GoodsModel::select('goods_id','goods_name','shop_price','goods_img')->paginate($page_size);
        $response = [
            'error' => 0,
            'msg'   => 'ok',
            'data'  => [
                'list' => $g_name->items()
            ]
        ];
        return $response;
    }

    public function xcxlog(Request $request)
    {
        //echo '<pre>';print_r($_GET);echo '<pre>';
        $code = $request->get('code');
        //echo $code;
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.env('XCX_APPID').'&secret='.env('XCX_APPSECRET').'&js_code='.$code.'&grant_type=authorization_code';
        $data = json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '<pre>';

        if(isset($data['errcode']))
        {
            $response = [
                'error' => 110001,
                'msg'   => "登录失败",
            ];
        }else{
            $dataopen = $data['openid'];
            if(empty(UserModel::where(['openid'=> $data['openid']])->first())) {
                UserModel::insert(['openid'=>$data['openid']]);
            }
            $token = sha1($data['openid'].$data['session_key'].mt_rand(0,999999));
            $redis_key = 'xcx_token:' .$token;
            Redis::set($redis_key,time());
            Redis::expire($redis_key,3600);
            $response = [
              'error' => 0,
              'msg'   => 'ok',
              'data'  => [
                  'token' => $token
              ]
            ];
        }
        return $response;
    }
}
