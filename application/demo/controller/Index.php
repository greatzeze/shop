<?php 
namespace app\demo\controller;

use think\Controller;

class Index extends Controller
{
	public function txt(){
		$file = fopen('1.txt','r') or die("打开文件失败");
		$filesize =  filesize('1.txt');
		$txt = fread($file,$filesize);
		fclose($file);
	}
}