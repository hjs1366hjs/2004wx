<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\GoodsModel;
use App\Model\UserModel;
use App\Model\cartModel;

class XcxController extends Controller
{
    //查询商品的所有详细信息
    public function xcxdetail(request $request)
    {
        $goods_id=$request->get("goods_id");
        $detail=GoodsModel::select('goods_id','goods_img','goods_name','shop_price','goods_thumb')
            ->where('goods_id',$goods_id)
            ->first()
            ->toArray();
        $array = [
            'goods_id'=>$detail['goods_id'],
            'goods_name'=>$detail['goods_name'],
            'shop_price'=>$detail['shop_price'],
            'goods_img'=>explode(",",$detail['goods_thumb'])
        ];
        return $array;
    }

    //首页列表查询
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

    //    小程序购物车列表
    public function xcxcart(Request $request){
        //接收uid
        $uid=$_SERVER['uid'];
        //查询购物车表中 是否有这个用户
        $goods = cartModel::where('uid',$uid)->get();
        if($goods)      //购物车有商品
        {
            $goods = $goods->toArray();

            foreach($goods as $k=>&$v)
            {
                $g = userModel::select("goods_img","goods_name")->find($v['goods_id']);
                $v['goods_name'] = $g->goods_name;
                $v['goods_img']=explode(",",$g['goods_img']);
            }
        }else{          //购物车无商品
            $goods = [];
        }

        //echo '<pre>';print_r($goods);echo '</pre>';die;
        $response = [
            'errno' => 0,
            'msg'   => 'ok',
            'data'  => [
                'list'  => $goods
            ]
        ];

        return $response;
    }

}
