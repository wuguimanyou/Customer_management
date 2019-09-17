<?php
header("Content-type: text/html; charset=utf-8");
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');

$customer_id = $_POST['customer_id'];
$group_id = $_POST['group_id'];
$op = $_POST['op'];


if($op == 'del'){
$query = "update weixin_commonshop_group set isvalid=false where id=".$group_id." and customer_id=".$customer_id;
file_put_contents("123.txt","SQL===".$query."\r\n",fILE_APPEND);
mysql_query($query);


$query2 = "update weixin_commonshop_order_group set isvalid=false where group_id=".$group_id." and customer_id=".$customer_id;
file_put_contents("123.txt","SQL2===".$query2."\r\n",fILE_APPEND);
mysql_query($query2);

echo 1;exit;
}

mysql_close($link);
?>

