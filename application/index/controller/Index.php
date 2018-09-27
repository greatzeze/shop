<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends Controller
{
    //表前缀
    private $prefix = 'veg_';

    public function _initialize(){        
        $system = db('veg_system')->where('id',1)->find();
        $channelList = db('veg_channel')->where('c_parent',0)->select();
        $this->assign('channelList',$channelList);
        $this->assign('system',$system);
    }

    //首页
    public function index(){
        $slideshowList = db('veg_slideshow')->select();
        $productList = db('veg_product')->order('id desc')->limit(16)->select();
        $linkList = db('veg_link')->select();
        $this->assign('linkList',$linkList);
        $this->assign('productList',$productList);
        $this->assign('slideshowList',$slideshowList);
        return $this->fetch('index');
    }

    //
    public function channel($id){

        $channel = db('veg_channel')->where('id',$id)->find();
        $productList = db('veg_product')->where('d_parent',$id)->select();
        $model = $channel['c_cmodel'];

        $this->assign('productList',$productList);
        return $this->fetch($model);
    }

    //产品详情页
    public function product($id){
        $product = db('veg_product')->where('id',$id)->find();
        $this->assign('product',$product);
        return $this->fetch('product');
    }

    //登陆
    public function loginPage(){
        return $this->fetch('login');
    }

    public function loginMethod(){
        $account = input('post.name');
        $password = input('post.password');

        $ret = db($this->prefix.'user')->where('name',$name)->where('password',$password)->find();
        if(!empty($ret)){
            echo productJson(true,"","登陆成功");
            session('name',$name);
        }else{
            echo productJson(false,"","账号或密码错误");
        }
    }

    //验证码
    public function verifycode(){
        $image = imagecreate(50, 34);
        $bcolor = imagecolorallocate($image, 0, 0, 0);
        $fcolor = imagecolorallocate($image, 255, 255, 255);
        $str = '0123456789';
        $rand_str = '';
        for ($i = 0; $i < 4; $i++){
            $k = mt_rand(1, strlen($str));
            $rand_str .= $str[$k - 1];
        }
        session('verifycode',$rand_str);
        imagefill($image, 0, 0, $bcolor);
        imagestring($image, 7, 7, 10, $rand_str, $fcolor);
        header('content-type:image/png');
        imagepng($image);
    }
}
