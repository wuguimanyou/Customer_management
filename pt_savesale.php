<?php
header("Content-type: text/html; charset=utf-8");
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');

if(!empty($_GET['customer_id'])){
	$customer_id = $_GET['customer_id'];
}
$type = $_POST['type'];
$pid = $_POST['pid'];

$name = $_POST['name'];
$shelf = $_POST['shelf'];
$status = $_POST['status'];
$unit_price = $_POST['unit_price'];
$group_price = $_POST['group_price'];
$number = $_POST['number'];
$sales = $_POST['sales'];
$stock = $_POST['stock'];
$units = $_POST['units'];
$title = $_POST['title'];
$details = $_POST['details'];
$img = $_POST['img'];
if(!empty($_POST['img_one'])){
	$img_one = $_POST['img_one'];
}else{
	$img_one = '';
}
if(!empty($_POST['img_two'])){
	$img_two = $_POST['img_two'];
}else{
	$img_two = '';
}
if(!empty($_POST['img_three'])){
	$img_three = $_POST['img_three'];
}else{
	$img_three = '';
}
if(!empty($_POST['img_for'])){
	$img_for = $_POST['img_for'];
}else{
	$img_for = '';
}

$op = $_POST['op'];
$dp = $_POST['dp'];


if($op == 'xj'){
	$query_xj = "update weixin_commonshop_group_products set shelf=2 where id=".$pid." and customer_id=".$customer_id;
	$row_xj = mysql_query($query_xj);
	if($row_xj){
		echo 1;exit;
	}else{
		echo 0;exit;
	}
}elseif($op =='sj'){
	$query_sj = "update weixin_commonshop_group_products set shelf=1 where id=".$pid." and customer_id=".$customer_id;
	$row_sj = mysql_query($query_sj);
	if($row_sj){
		echo 1;exit;
	}else{
		echo 0;exit;
	}
}

if($dp == 'open'){
	$query_open = "update weixin_commonshop_group_products set status=1 where id=".$pid." and customer_id=".$customer_id;
	$row_open = mysql_query($query_open);
	//file_put_contents("2233ss.txt","SQL;===".$query_open."\r\n",FILE_APPEND);
	if($row_open){
		echo 1;exit;
	}else{
		echo 0;exit;
	}
}elseif($dp == 'close'){
	$query_close = "update weixin_commonshop_group_products set status=2 where id=".$pid." and customer_id=".$customer_id;
	$row_close = mysql_query($query_close);
	//file_put_contents("2233ss.txt","SQL;===".$query_close."\r\n",FILE_APPEND);
	if($row_close){
		echo 1;exit;
	}else{
		echo 0;exit;
	}
}


switch($type){
	case 'edit':
		$query = "update weixin_commonshop_group_products set name='".$name."',shelf=".$shelf.",status=".$status.",unit_price=".$unit_price.",group_price=".$group_price.",number=".$number.",sales=".$sales.",stock=".$stock.",units='".$units."',title='".$title."',details='".$details."',
		img='".$img."',img_one='".$img_one."',img_two='".$img_two."',img_three='".$img_three."',img_for='".$img_for."' where id=".$pid." and customer_id=".$customer_id;
		$row = mysql_query($query);
		file_put_contents("1122ss.txt","SQL;===".$query."\r\n",FILE_APPEND);        
		if($row){
			echo 1;exit;
		}else{
			echo 0;exit;
		}
		break;

	case 'del':
		$query = "update weixin_commonshop_group_products set isvalid=false where id=".$pid." and customer_id=".$customer_id;
		//file_put_contents("1122ss.txt","SQL_del;===".$query."\r\n",FILE_APPEND);
		$row = mysql_query($query);
		if($row){
			echo 1;exit;
		}else{
			echo 0;exit;
		}
		break;

	case 'add':
		$query = "insert into weixin_commonshop_group_products (customer_id,name,shelf,status,unit_price,group_price,number,sales,stock,units,title,details,img,isvalid,createtime,img_one,img_two,img_three,img_for) 
					value (".$customer_id.",'".$name."',".$shelf.",".$status.",".$unit_price.",".$group_price.",".$number.",".$sales.",".$stock.",'".$units."','".$title."','".$details."','".$img."',true,now(),'".$img_one."','".$img_two."','".$img_three."','".$img_for."')";
		$row = mysql_query($query);
		if($row){
			echo 1;exit;
		}else{
			echo 0;exit;
		}
		break;
}




mysql_close($link);
?>