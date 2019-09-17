<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]1
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$user_id = $configutil->splash_new($_GET["user_id"]);	//提现金额
$paypassword = MD5(888888);
$id = -1;
$query = "SELECT id FROM user_paypassword WHERE isvalid=true AND user_id=$user_id LIMIT 1";
$result= mysql_query($query)or die('Query failed 12: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$id = $row->id;
}

if( $id < 0 ){
	echo '<script>alert("用户尚未设置支付密码！");history.go(-1);</script>';
}else{
	$query = "UPDATE user_paypassword SET paypassword = '$paypassword' WHERE isvalid=true AND user_id = $user_id";
	mysql_query($query)or die('Query failed 12: ' . mysql_error());
	echo '<script>alert("重置成功！");history.go(-1);</script>';
}


?>