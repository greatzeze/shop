<?php

use Ramsey\Uuid\Uuid;
use think\Db;

// 应用公共文件

//获取uuid
function getUUID(){
    $uuid4 = Uuid::uuid4();
    return $uuid4->toString();
}

//提示后转移
function alertHref($msg, $url){
    return "<script>alert('$msg');window.location.href='$url';</script>";
}

//提示后返回
function alertBack($msg){
    return "<script>alert('$msg');window.history.back();</script>";
}

//写log
function productLog($module, $message, $level){
    trace($message,$module . ' ] [ ' . $level);
}

//协调json方法
function productJson($flag, $content, $message){
    return json_encode(['flag' => $flag, 'content' => $content, 'message' => $message]);
}


//获取频道下拉列表
function channel_select_list($t0,$t1,$t2,$t3){
	$tmp = '';
	$s = '';
	$level = $t1;
	$main = '';
	for ( $i=0; $i < $level; $i++ ){
		$s =$s . '　' ;
	}
	$main = ( $t0 == 0 ) ? '' : '├' ;
	$level = $level +1;
	$sql = 'select * from veg_channel where c_parent = '.$t0.' and id <> '.$t3.' order by c_order asc , id asc';
	$result = Db::query($sql);
	while( $row = mysql_fetch_array($result)){
		$select = ($row['id'] == $t2 ? 'selected="selected"' : '');
		$tmp .='<option value="'.$row['id'].'" '.$select.'>'.$s.$main.$row['c_name'].'</option>'.channel_select_list($row['id'],$level,$t2,$t3);
	}
	return $tmp;
}
//获取频道层级
function get_channel_level($t0,$t1=1){
	$level = $t1;
	$sql = 'select * from veg_channel where id ='.$t0.'';
	$result = Db::query($sql);
	$row = mysql_fetch_array($result);
	if($row['c_parent'] == 0){
		return $level;
	} else {
		return get_channel_level($row['c_parent'],$level + 1);
	}
}
//获取所有频道的ID
function get_channel_sub($t0,$t1){
	$tmp = '';
	$s = ',';
	$sql = 'select * from veg_channel where c_parent = '.$t0.' order by c_order asc , id asc ';
	$result = Db::query($sql);
	while(!!$row = mysql_fetch_array($result)){
		$tmp .= $s.$row['id'].get_channel_sub($row['id'],'');
	};
	return $t1.$tmp;
}

//获取指定频道的最上级频道
function get_channel_main($parent){
	$sql = 'select * from veg_channel where id ='.$parent.'';
	$result = Db::query($sql);
	$row = mysql_fetch_array($result);
	if($row['c_parent'] == 0){
		return $row['id'];
	}else{
		return get_channel_main($row['c_parent']);
	}
}

//获取指定频道是否有子频道
function get_channel_ifsub($id){
	$result = Db::query('select * from veg_channel where c_parent = '.$id.' ');
	if (mysql_fetch_array($result)){
		return 1;
	}else{
		return 0;
	}
}
//更新所有频道
function update_channel(){
	$sql = 'select * from veg_channel order by id asc';
	$result = Db::query($sql);
	while (!!$row = mysql_fetch_array($result)){
		$sql2 = 'update veg_channel set c_sub="'.get_channel_sub($row['id'],$row['id']).'",c_ifsub='.get_channel_ifsub($row['id']).',c_main='.get_channel_main($row['id']).',c_level='.get_channel_level($row['id']).' where id = '.$row['id'].' ';
		Db::query($sql2);
	};
}

//频道管理列表
function channel_manage($t0,$t1){
	$tmp = '';
	$level = $t1;
	$s = '';
	$result = Db::query('select * from veg_channel where c_parent = '.$t0.' order by c_order asc , id asc ');
	for ($i = 0; $i < $level; $i++){
		$s = $s . '　';
	};
	$main = ( $t0 == 0 ) ? '' : '├' ;
	$level = $level + 1;
	while (!!$row = mysql_fetch_array($result)){
		$tmp .='<tr>
					<td>'.$row['id'].'</td>
					<td>'.$row['c_order'].'</td>
					<td>'.$s.$main.'<a href="../'.c_url($row['id']).'" target="_blank">'.$row['c_name'].'</a></td>
					<td><span class="badge">'.get_model_name($row['c_cmodel']).'</span></td>
					<td><span class="badge">'.get_model_name($row['c_dmodel']).'</span></td>
					<td><a class="btn bg-sub" href="veg_detail_add.php?cid='.$row['id'].'"><span class="icon-plus-square"> 添加</span></a>&nbsp<a class="btn bg-main" href="veg_detail.php?cid='.$row['id'].'"><span class="icon-bars"> 管理</span></a></td>
					<td><a class="btn bg-gray" href="veg_channel_edit.php?id='.$row['id'].'"><span class="icon-edit"> 修改</span></a>&nbsp<a class="btn bg-dot" href="veg_channel.php?del='.$row['id'].'" onclick="return confirm(\'确定要删除吗？\')"><span class="icon-times"> 删除</span></a></td>
				</tr>'.channel_manage($row['id'],$level);
	}
	return $tmp;
};
//通过id获取频道名称
function get_channel_name($t0){
	$result = Db::query('select * from veg_channel where id='.$t0.'');
	if (!!$row = mysql_fetch_array($result)){
		return $row['c_name'];
	}else{
		return '';
	};
}
//获取模型名称
function get_model_name($t0){
	$result = Db::query('select * from veg_model where m_value="'.$t0.'"');
	if (!!$row = mysql_fetch_array($result)){
		return $row['m_name'];
	}else{
		return '自定义';
	};
}

function model_select_list($t0,$t1){
	$tmp = '';
	$result = Db::query('select * from veg_model where m_type = '.$t0.' order by id asc');
	while($row = mysql_fetch_array($result)){
		$select =($row['m_value'] == $t1 ? 'selected="selected"' :'');
		$tmp .= '<option value="'.$row['m_value'].'" '.$select.'>'.$row['m_name'].'</option>';
	}
	return $tmp;
}


//频道链接地址
function c_url($t0){
	return 'channel.php?id='.$t0.'';
}
//详情链接地址
function d_url($t0){
	return 'detail.php?id='.$t0.'';
}


//频道列表
function channel_list($t0,$t1){
	$tmp = '';
	$result = Db::query('select * from veg_channel where c_parent = '.$t0.' order by c_order asc , id asc ');
	foreach($result as $row){
		$tmp .= '<li><a '.($row['id'] == $t1 ? ' class="current"' : '').' href="'.c_url($row['id']).'" target="'.$row['c_target'].'">'.$row['c_name'].'</a></li>';
	}
	return $tmp;
}



//频道和内容页的当前位置
function current_location($t0,$t1){
	$tmp = '';
	$result = Db::query('select * from veg_channel where id = '.$t0.'');
	foreach($result as $row){
		$current = ($row['id'] == $t1 ? 'class ="current"' :'');
		$tmp = '<a '.$current.' href="'.c_url($row['id']).'">'.$row['c_name'].'</a>';
		if($row['c_parent'] <> 0){
			$tmp = current_location($row['c_parent'],$t1).' > '.$tmp;
		}
	}
	return $tmp;
}

//获取频道字段
function get_channel($t0,$t1){
	$result = Db::query('select * from veg_channel where id='.$t0.'');
	if (!!$result){
		return $result[0][$t1];
	}else{
		return '不存在';
	};
}

//截断字符串
function cut_str($str, $len = 10, $etc = '...') {
	$restr = '';
	$i = 0;
	$n = 0.0;
	$strlen = strlen($str);
	while (($n < $len) and ($i < $strlen)) {
		$temp_str = substr($str, $i, 1);
		$ascnum = ord($temp_str);
		if ($ascnum >= 252) {
			$restr = $restr . substr($str, $i, 6);
			$i = $i + 6;
			$n++;
		} else if ($ascnum >= 248) {
			$restr = $restr . substr($str, $i, 5);
			$i = $i + 5;
			$n++;
		} else if ($ascnum >= 240) {
			$restr = $restr . substr($str, $i, 4);
			$i = $i + 4;
			$n++;
		} else if ($ascnum >= 224) {
			$restr = $restr . substr($str, $i, 3);
			$i = $i + 3;
			$n++;
		} else if ($ascnum >= 192) {
			$restr = $restr . substr($str, $i, 2);
			$i = $i + 2;
			$n++;
		}
		else if ($ascnum >= 65 and $ascnum <= 90 and $ascnum != 73) {
			$restr = $restr . substr($str, $i, 1);
			$i = $i + 1;
			$n++;
		}
		else if (!(array_search($ascnum, array(37,38,64,109,119)) === FALSE)) {
			$restr = $restr . substr($str, $i, 1);
			$i = $i + 1;
			$n++;
		}
		else {
			$restr = $restr . substr($str, $i, 1);
			$i = $i + 1;
			$n = $n + 0.5;
		}
	}
	if ($i < $strlen) {
		$restr = $restr . $etc;
	}
	return $restr;
}
