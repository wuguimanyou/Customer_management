<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$data		= array();
$product_id	= $configutil->splash_new($_POST["product_id"]);
$pid		= $configutil->splash_new($_POST["pid"]);
$chenk_id	= -1;
$query = "select id from products_relation_t where isvalid = true and pid=".$pid." and parent_pid=".$product_id; 
$result = mysql_query($query) or die("query3 error : ".mysql_error());
while ($row = mysql_fetch_object($result)) {
	$chenk_id			=  $row->id ;
}
if( $chenk_id > 0){
	$data["status"]			= 2;
	$data=json_encode($data);
	die($data);
}
  
$query = "insert into products_relation_t(pid,parent_pid,customer_id,isvalid,createtime)values(".$pid.",".$product_id.",".$customer_id.",true,now())";
//echo $query;
mysql_query($query)or die('Query failed'.mysql_error());

$query = "select id,name,default_imgurl from weixin_commonshop_products where isvalid = true and id=".$pid;   
$result = mysql_query($query) or die("query3 error : ".mysql_error());
while ($row = mysql_fetch_object($result)) {
	$pid			=  $row->id ;
	$name			=  $row->name ;
	$default_imgurl	=  $row->default_imgurl ;
}
$data["status"]			= 1;
$data["pid"] 			= $pid;
$data["name"] 			= $name;
$data["default_imgurl"]	= $default_imgurl;

$error = mysql_error();
//echo $error;
mysql_close($link);
if( !empty( $num ) ){
	$data["status"]			= 0;
}
$data=json_encode($data);
echo $data;
?>