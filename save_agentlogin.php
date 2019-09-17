<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility_shop.php');
$shopMessage= new shopMessage_Utlity(); 
//头文件----start
// require('../common/common_from.php');
//头文件----end

$username ="";
if(!empty($_POST["username"])){
	$username = $_POST["username"];	//暂时没用
}
$phone = "";
if(!empty($_POST["phone"])){
	$phone = $_POST["phone"];		//暂时没用
}
if(!empty($_GET["user_id"])){
    $user_id=$configutil->splash_new($_GET["user_id"]);
    $user_id = passport_decrypt($user_id);
}else{
    if(!empty($_SESSION["user_id_".$customer_id])){
        $user_id=$_SESSION["user_id_".$customer_id];
    }
}
$agent_select = $_POST["agent_select"];
$vlst = explode("_",$agent_select);
$name = "";
$value = 0;
$data = array();
$discount = 1;
$agent_select = $vlst[1];
$agent_price = $vlst[2];
$agent_discount = $vlst[3];

$query="select id,status from weixin_commonshop_applyagents where isvalid=true and user_id=".$user_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$is_apply=-1;
$status=0;
while ($row = mysql_fetch_object($result)) {
   $is_apply = $row->id;
   $status = $row->status;
   break;
}

if($is_apply<0){
	$sql="insert into weixin_commonshop_applyagents(user_id,agent_name,agent_price,agent_discount,status,isvalid,createtime) values(".$user_id.",'".$agent_select."',".$agent_price.",'".$agent_discount."',0,true,now())";
}else{
	if($status!=1){
		$sql="update weixin_commonshop_applyagents set agent_name='".$agent_select."',agent_price='".$agent_price."',agent_discount='".$agent_discount."',createtime=now(),status=0 where user_id=".$user_id." and isvalid=true and id=".$is_apply;
	}
}
if(!empty($agent_price) and !empty($agent_discount)){ 
	 mysql_query($sql);
	 $data['status'] = 1;
}
$parent_id = -1;
$query = "select parent_id from weixin_users where isvalid=true and id=".$user_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$parent_id = $row->parent_id;
	break;
}
//生命周期
$shopMessage->ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,2,2,-1);



mysql_close($link);

echo json_encode($data);
 
?>