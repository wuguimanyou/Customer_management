<?php
header("Content-type: text/html; charset=utf-8");
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');

$customer_id = $_POST['customer_id'];
$home_id = $_POST['home_id'];
$home_name = $_POST['home_name'];
$home_pid = $_POST['home_pid'];
$home_imgurl = $_POST['home_imgurl'];

$query = "select name from weixin_commonshop_group_products where customer_id=".$customer_id." and id=".$home_pid;
$row = mysql_query($query);
$rod = mysql_fetch_object($row);
	$home_pname = $rod->name;


$query2 = "update weixin_commonshop_pt_home set pname='".$home_pname."',pid=".$home_pid.",imgurl='".$home_imgurl."' where id=".$home_id." and customer_id=".$customer_id;
file_put_contents("2233ss.txt","SQL===".$query2."\r\n",FILE_APPEND);
mysql_query($query2);
echo 1;


mysql_close($link);
?>

