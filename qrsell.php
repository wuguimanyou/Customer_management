<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]1
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../common/utility_shop.php');

require('../proxy_info.php');

mysql_query("SET NAMES UTF8");
require('../auth_user.php');
$begintime="";
$endtime ="";
$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   $id = $configutil->splash_new($_GET["id"]);
   $user_id = $configutil->splash_new($_GET["user_id"]);
   if($op=="resetpwd"){
	   $sql="update promoters set pwd='888888' where user_id=".$user_id;
	   mysql_query($sql);
   }else if($op=="p_tupian"){
	   $sql='update promoters set foreverimg="",exp_map_url="",imgcreatime=NULL  where  user_id='.$user_id.' and isvalid=true and customer_id='.$customer_id;
	  mysql_query($sql);
   }else if($op=="p_tupian2"){
	   $sql='update promoters set foreverimg="",exp_map_url="",imgcreatime=NULL  where  isvalid=true and customer_id='.$customer_id;
	  mysql_query($sql);
   }
}

$query ="select isOpenPublicWelfare,is_team,is_shareholder from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	$isOpenPublicWelfare=0;
	$is_team=0;
	$is_shareholder=0;
	while ($row = mysql_fetch_object($result)) {		
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	   $is_team = $row->is_team;
	   $is_shareholder = $row->is_shareholder;
	}
//echo $query;
$exp_user_id=-1;

if(!empty($_GET["exp_user_id"])){
    $exp_user_id = $configutil->splash_new($_GET["exp_user_id"]);
}
$search_status=-1;
if(!empty($_GET["search_status"])){
    $search_status = $configutil->splash_new($_GET["search_status"]);
}
if(!empty($_POST["search_status"])){
    $search_status = $configutil->splash_new($_POST["search_status"]);
}
$search_generation=-1;
if(!empty($_GET["search_generation"])){
    $search_generation = $configutil->splash_new($_GET["search_generation"]);
}
if(!empty($_POST["search_generation"])){
    $search_generation = $configutil->splash_new($_POST["search_generation"]);
}

$search_name="";
if(!empty($_GET["search_name"])){
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
if(!empty($_POST["search_name"])){
    $search_name = $configutil->splash_new($_POST["search_name"]);
}

$search_user_id="";
if(!empty($_GET["search_user_id"])){
    $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
if(!empty($_POST["search_user_id"])){
    $search_user_id = $configutil->splash_new($_POST["search_user_id"]);
}


$search_phone="";
if(!empty($_GET["search_phone"])){
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
if(!empty($_POST["search_phone"])){
    $search_phone = $configutil->splash_new($_POST["search_phone"]);
}



//新增客户
$new_customer_count =0;
//今日销售
$today_totalprice=0;
//新增订单
$new_order_count =0;
//新增推广员
$new_qr_count =0;

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}

$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and year(paytime)=".$year." and month(paytime)=".$month." and day(paytime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);

$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}

$query="select count(1) as new_qr_count from promoters where status=1 and isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}


$exp_name="推广员";
$query="select exp_name,shop_card_id,qrsell_orderothers from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$shop_card_id=-1;
$qrsell_orderothers="";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 while ($row = mysql_fetch_object($result)) {
	$shop_card_id= $row->shop_card_id;
	$exp_name = $row->exp_name;	
	$open_qrsell_orderothers = $row->qrsell_orderothers;	
	break;
 }
 
//代理模式,分销商城的功能项是 266
$is_distribution=0;//渠道取消代理商功能
$is_disrcount=0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城代理模式' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W_is_disrcount Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
   $is_disrcount = $row->is_disrcount;
   break;
}
if($is_disrcount>0){
   $is_distribution=1;
}

//供应商模式,渠道开通与不开通
$is_supplierstr=0;//渠道取消供应商功能
$sp_count=0;//渠道取消供应商功能
$sp_query="select count(1) as sp_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城供应商模式' and c.id=cf.column_id";
$sp_result = mysql_query($sp_query) or die('W_is_supplier Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($sp_result)) {
   $sp_count = $row->sp_count;
   break;
}
if($sp_count>0){
   $is_supplierstr=1;
}

?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/icon.css" media="all">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/inside.css" media="all">
<script type="text/javascript" src="../common/js/jquery-1.7.2.min.js"></script>
<link href="css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/shop.js"></script>
<link href="css/operamasks-ui.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/operamasks-ui.min.js"></script>
<script type="text/javascript" src="../js/tis.js"></script>
<script language="javascript">
$(document).ready(shop_obj.orders_init);
</script>
</head>

<body>

<style type="text/css">body, html{background:url(images/main-bg.jpg) left top fixed no-repeat;}</style>
<div class="div_line">

		   <div class="div_line_item" onclick="show_newOrder('<?php echo $customer_id_en; ?>');">
		      今日订单: <span style="padding-left:10px;font-size:18px;font-weight:bold"><?php echo $new_order_count; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>

		   <div class="div_line_item"  onclick="show_todayMoney('<?php echo $customer_id_en; ?>');">
		      今日销售: <span style="padding-left:10px;color:red;font-size:18px;font-weight:bold">￥<?php echo $today_totalprice; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>

		   <div class="div_line_item"  onclick="show_newCustomer('<?php echo $customer_id_en; ?>');">
		       新增客户: <span style="padding-left:10px;font-size:18px;font-weight:bold"><?php echo $new_customer_count; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>

		   <div class="div_line_item"  onclick="show_newQrsell('<?php echo $customer_id_en; ?>');">
		      新增推广员: <span style="padding-left:10px;font-size:18px;font-weight:bold"><?php echo $new_qr_count; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>
		   <?php
		   $sql_stock = "select stock_remind from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
		   $res_stock = mysql_query($sql_stock) or die('Query failed: ' . mysql_error());
		   while ($row_sql_stock = mysql_fetch_object($res_stock)) {
				$stock_remind = $row_sql_stock->stock_remind;
			}
  		    $stock_mun=0;
			$stock_pidarr="";
			$query_stock1="select id from weixin_commonshop_products where isvalid=true and storenum<".$stock_remind." and isout=0 and customer_id=".$customer_id;
			//echo $query_stock1;
			$result_stock1 = mysql_query($query_stock1) or die('Query failed: ' . mysql_error());
			$stock_mun1 = mysql_num_rows($result_stock1);
			while ($row_stock1 = mysql_fetch_object($result_stock1)) {
				$stock_pid1 = $row_stock1->id;
				if(!empty($stock_pidarr)){
					$stock_pidarr=$stock_pidarr."_".$stock_pid1;
				}else{
					$stock_pidarr=$stock_pid1;
				}
				
			}
			
			$query_stock2="select id,propertyids,storenum from weixin_commonshop_products where isvalid=true and isout=0 and storenum>".$stock_remind." and customer_id=".$customer_id;
			$result_stock2 = mysql_query($query_stock2) or die('Query failed: ' . mysql_error());
			$stock_mun2=0;
			while ($row_stock2 = mysql_fetch_object($result_stock2)) {
				$stock_pid = $row_stock2->id;			
				$stock_storenum = $row_stock2->storenum;			
				$stock_propertyids = $row_stock2->propertyids;			
				if(!empty($stock_propertyids)){
				   $query_stock3="SELECT * FROM weixin_commonshop_product_prices WHERE storenum<".$stock_remind." and product_id='".$stock_pid."' limit 0,1";
				   //echo  $query_stock3;
				   $result_stock3 = mysql_query($query_stock3) or die('Query failed: ' . mysql_error());
				   $result_stock3_mun1 = mysql_num_rows($result_stock3);
				   while ($row_stock3 = mysql_fetch_object($result_stock3)) {
						$stock_pid2 = $row_stock3->product_id;
					}
				   if($result_stock3_mun1 !=0){
					   $stock_mun2=$stock_mun2 + 1;
					   if(!empty($stock_pidarr)){
							$stock_pidarr=$stock_pidarr."_".$stock_pid2;
						}else{
							$stock_pidarr=$stock_pid2;
						}
				   }				   
				}
			}
			$stock_mun=$stock_mun1+$stock_mun2; 
			
		   ?>

		   <div class="div_line_item"  onclick="show_stock('<?php echo $customer_id_en; ?>','<?php echo $stock_pidarr; ?>');">
		      库存提醒: 已有<span style="padding-left:10px;color:red;font-size:18px;font-weight:bold"><?php echo $stock_mun; ?></span>个商品库存不足了
		   </div>
		</div>
<div id="iframe_page">
	<div class="iframe_content">

	<div class="r_nav">
		<ul>
			<?php if($isOpenPublicWelfare){?><li id="auth_page10"><a href="publicwelfare.php?customer_id=<?php echo $customer_id; ?>">公益基金</a></li><?php }?>
			<li id="auth_page0" class=""><a href="base.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a></li>
			<li id="auth_page1" class=""><a href="fengge.php?customer_id=<?php echo $customer_id_en; ?>">风格设置</a></li>
			<li id="auth_page2" class=""><a href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&default_set=1">首页设置</a></li>
			<li id="auth_page3" class=""><a href="product.php?customer_id=<?php echo $customer_id_en; ?>">产品管理</a></li>
			<li id="auth_page4" class=""><a href="order.php?customer_id=<?php echo $customer_id_en; ?>&status=-1">订单管理</a></li>
			<?php if($is_supplierstr){?><li id="auth_page5" class=""><a href="supply.php?customer_id=<?php echo $customer_id_en; ?>">供应商</a></li><?php }?>
			<?php if($is_distribution){?><li id="auth_page6" class=""><a href="agent.php?customer_id=<?php echo $customer_id_en; ?>">代理商</a></li><?php }?>
			<li id="auth_page7" class="cur"><a href="qrsell.php?customer_id=<?php echo $customer_id_en; ?>">推广员</a></li>
			<li id="auth_page8" class=""><a href="customers.php?customer_id=<?php echo $customer_id_en; ?>">顾客</a></li>
			<li id="auth_page9"><a href="shops.php?customer_id=<?php echo $customer_id_en; ?>">门店</a></li>
			<?php if($isOpenPublicWelfare){?><li id="auth_page10"><a href="publicwelfare.php?customer_id=<?php echo $customer_id_en; ?>">公益基金</a></li><?php }?>
		
		</ul>
	</div>

<div id="orders" class="r_con_wrap">
		<!-- <form class="search" id="search_form" method="post" action="qrsell.php?customer_id=<?php echo $customer_id; ?>"> -->
		<form class="search" id="search_form">
			推广员状态：<select name="search_status" id="search_status"  style="width:100px;" >
				<option value="-1">--请选择--</option>
				<option value="2" <?php if($search_status==2){ ?>selected <?php } ?>>待审核</option>
				<option value="1" <?php if($search_status==1){ ?>selected <?php } ?>>已确认</option>
				<option value="-2" <?php if($search_status==-2){ ?>selected <?php } ?>>已驳回/暂停</option>
				</select>
				
			推广员代数：<select name="search_generation" id="search_generation"  style="width:100px;" >
				<option value="-1">--请选择--</option>
				<?php
					$query = "SELECT max(generation) as max_generation FROM promoters WHERE isvalid=true and customer_id=".$customer_id;		
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$max_generation = $row->max_generation;
					}
					for ($i = 1; $i <= $max_generation; $i++){
				?>
				<option value="<?php echo $i; ?>" <?php if($search_generation==$i){ ?>selected <?php } ?>><?php echo $i;?>代推广员</option>
				<?php
					}
				?>
				</select>
				&nbsp;推广员编号:<input type=text name="search_user_id" id="search_user_id" value="<?php echo $search_user_id; ?>" style="width:80px;" />
				&nbsp;姓名:<input type=text name="search_name" id="search_name" value="<?php echo $search_name; ?>" style="width:80px;" />
				&nbsp;电话:<input type=text name="search_phone" id="search_phone" value="<?php echo $search_phone; ?>"  style="width:80px;" />
				返佣时间：
				<span class="om-calendar om-widget om-state-default">
					<input type="text" class="input" id="begintime" name="AccTime_S" value="<?php echo $begintime; ?>" maxlength="20" id="K_1389249066532">
					<span class="om-calendar-trigger"></span></span>-<span class="om-calendar om-widget om-state-default">
					<input type="text" class="input" id="endtime" name="AccTime_E" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580">
					<span class="om-calendar-trigger"></span>
				</span>&nbsp;   
			
			<input type="button" class="search_btn"  onclick="searchForm();" value="搜 索" >
			<input type="button" class="search_btn" value="导出推广员+" onClick="exportRecord();" class="button" style="cursor:hand">
			<input type="button" class="search_btn" value="导出金额详情+" onClick="exportRecord1();" class="button" style="cursor:hand">
			<input type="button" class="search_btn" value="一键删除推广图片" onClick="del_tupian();" class="button" style="cursor:hand; width:100px;">
		</form>	 
		<table border="0" cellpadding="5" cellspacing="0" class="r_con_table" id="order_list">
			<thead>
				<tr style="background: #fff;">
					<td colspan="12">
					推广员已发展到:<span style="color:red;font-size:18px;"><?php echo $max_generation;?>代推广员</span>
					</td>
				</tr>
				<tr>
					<td width="8%" nowrap="nowrap">推广员编号</td>
					<td width="8%" nowrap="nowrap">姓名</td>
					<td width="8%" nowrap="nowrap">推广二维码</td>
					<td width="8%" nowrap="nowrap">直接推广人数</td>
					<td width="8%" nowrap="nowrap">直接推广金额</td>
					<td width="8%" nowrap="nowrap">总获奖积分</td>				
					<td width="8%" nowrap="nowrap">总获奖金额</td>				
					<td width="8%" nowrap="nowrap">状态</td>
					<td width="8%" nowrap="nowrap">上线</td>
					<td width="8%" nowrap="nowrap">总消费金额</td>
					<td width="8%" nowrap="nowrap">申请时间</td>
					<?php
					if(!empty($open_qrsell_orderothers)){
					?>
					<td width="8%" nowrap="nowrap">自定义</td>
					<?php } ?>
					<td width="10%" nowrap="nowrap">操作</td>
				</tr>
			</thead>
			<tbody>
			   <?php 
			   
			   $pagenum = 1;

				if(!empty($_GET["pagenum"])){
				   $pagenum = $_GET["pagenum"];
				}

				$start = ($pagenum-1) * 20;
				$end = 20;
				$weixin_fromuser="";
			   //  $query="select distinct(wq.id) as id,qr_info_id,wq.reason as reason,wu.id as user_id,wu.name as name,wu.weixin_name as weixin_name,wu.phone as phone,wu.parent_id as parent_id ,imgurl_qr,wq.status,reward_score,reward_money,wq.createtime,promoter.fans_count,promoter_count,weixin_fromuser from weixin_qrs wq inner join weixin_qr_infos wqi inner join weixin_users wu inner join promoters promoter  on wq.qr_info_id=wqi.id and promoter.user_id=wu.id and promoter.isvalid=true and wq.isvalid=true and wqi.isvalid=true and  wqi.foreign_id = wu.id and wu.isvalid=true and  wq.isvalid=true and wq.type=1 and wqi.user_type=1 and wq.customer_id=".$customer_id;
			   $query="";
			   $query_count=0;

			      if(!empty($search_name) or !empty($search_phone) or !empty($search_user_id) or $search_generation != -1){
				   

					 $query="select distinct(wq.id) as id,qr_info_id,wq.reason as reason,wqi.foreign_id as user_id,imgurl_qr,wq.status,reward_score,reward_money,wq.createtime from weixin_qrs wq inner join weixin_qr_infos wqi inner join weixin_users wu inner join promoters ps on ps.isvalid=true and ps.user_id=wqi.foreign_id and wq.qr_info_id=wqi.id and  wu.isvalid=true and wu.id=wqi.foreign_id and wq.isvalid=true and wqi.isvalid=true  and  wq.isvalid=true and wq.type=1 and wqi.user_type=1 and wq.customer_id=".$customer_id;
					 

					 $query_count="select count(1) as tcount from weixin_qrs wq inner join weixin_qr_infos wqi inner join weixin_users wu inner join promoters ps on ps.isvalid=true and ps.user_id=wqi.foreign_id and wq.qr_info_id=wqi.id and  wu.isvalid=true and wu.id=wqi.foreign_id and wq.isvalid=true and wqi.isvalid=true  and  wq.isvalid=true and wq.type=1 and wqi.user_type=1 and wq.customer_id=".$customer_id;
				 }else{
					 $query="select distinct(wq.id) as id,qr_info_id,wq.reason as reason,wqi.foreign_id as user_id,imgurl_qr,wq.status,reward_score,reward_money,wq.createtime from weixin_qrs wq inner join weixin_qr_infos wqi  on wq.qr_info_id=wqi.id and   wq.isvalid=true and wqi.isvalid=true  and  wq.isvalid=true and wq.type=1 and wqi.user_type=1 and wq.customer_id=".$customer_id;
					 
					  $query_count="select count(1) as tcount from weixin_qrs wq inner join weixin_qr_infos wqi  on wq.qr_info_id=wqi.id and   wq.isvalid=true and wqi.isvalid=true  and  wq.isvalid=true and wq.type=1 and wqi.user_type=1 and wq.customer_id=".$customer_id;
				 }
				 $query3="";
				 if($exp_user_id>0){
				     $query3 = $query3." and wqi.foreign_id=".$exp_user_id;
				 }
				 switch($search_status){
				    case 2:
					   $query3 = $query3." and wq.status=0";
					   break;
					case 1:
					   $query3 = $query3." and wq.status=1";
					   break;
					case -2:
					   $query3 = $query3." and wq.status=-1";
					   break;
					
				     
				 }
				 switch($search_generation){
					case -1:
						break;
					default:
						$query3 = $query3." and ps.generation=".$search_generation;
				 }
				 
				 if(!empty($search_name)){
				   
					$query3 = $query3." and (wu.name like '%".$search_name."%' or wu.weixin_name like '%".$search_name."%')";
				 }
				 
				 if(!empty($search_phone)){
				   
					$query3 = $query3." and wu.phone like '%".$search_phone."'";
				 }
				 
				 if(!empty($search_user_id)){
				   
					$query3 = $query3." and wu.id like '%".$search_user_id."%'";
				 }
				 $query = $query.$query3;
				 $query_count = $query_count.$query3;
				 /* 输出数量开始 */
				 //$query2 = $query.' group by wq.id order by id';
				 $rcount_q2 = 0;
				 $result2 = mysql_query($query_count) or die('Query failed28: ' . mysql_error());
				 while ($row2 = mysql_fetch_object($result2)) {
					$rcount_q2=$row2->tcount;
				 }
				 //$rcount_q2 = mysql_num_rows($result2);
				 /* 输出数量结束 */
				 $query = $query." order by wq.id desc"." limit ".$start.",".$end;
				 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
				// $rcount_q = mysql_num_rows($result);
				//echo $query;
	             while ($row = mysql_fetch_object($result)) {
				 
				    //$weixin_fromuser = $row->weixin_fromuser;
					$weixin_fromuser = "";
					$qr_info_id = $row->qr_info_id;
					$user_id =$row->user_id;
					
					$username="";
					$weixin_name="";
					$userphone="";
					$user_parent_id = -1;
					$query2="select weixin_fromuser,name,weixin_name,phone,parent_id from weixin_users where isvalid=true and id=".$user_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
	                while ($row2 = mysql_fetch_object($result2)) {
					    $weixin_fromuser =$row2->weixin_fromuser;
						
						$username=$row2->name;
						$weixin_name = $row2->weixin_name;
						$username = $username."(".$weixin_name.")";
						$userphone = $row2->phone;
						$user_parent_id = $row2->parent_id;
					}
					$id = $row->id;
					$reward_score = $row->reward_score;
					$reward_money = $row->reward_money;
					
					$reward_money = round($reward_money, 2);
					$reason = $row->reason;
					
					
					
					$imgurl_qr=$row->imgurl_qr;
					
					$fans_count = 0;
					$promoter_count = 0;
					$parent_id = -1;
					$query2="select fans_count,promoter_count,parent_id from promoters where user_id=".$user_id." and isvalid=true";
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {
					   $fans_count = $row2->fans_count;
					   $promoter_count = $row2->promoter_count;
					   $parent_id = $row2->parent_id;
					   break;
					}
					
					
					$sum_totalprice=0;
					//总消费金额
					$query2="select sum(totalprice) as sum_totalprice from weixin_commonshop_orders where isvalid=true and status =1 and paystatus=1 and sendstatus!=4 and exp_user_id>0 and   exp_user_id=".$user_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					//echo $query2;
					while ($row2 = mysql_fetch_object($result2)) {
					   $sum_totalprice = $row2->sum_totalprice;
					   break;
					}
					if(empty($sum_totalprice)){
					   $sum_totalprice = 0;
					}
					
				    $sum_totalprice = round($sum_totalprice, 2);
						
					$status = $row->status;
					$statusstr="待审核";
					switch($status){
					   case 1:
					   
					     $statusstr="已确认";
						 break;
					   case -1:
					     $statusstr="已驳回/暂停";
						 break;
					}
					
					/*$query2= "select parent_id,weixin_name from weixin_users where isvalid=true and id=".$user_id; 
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {
						$parent_id=$row2->parent_id;
						break;
					}*/
					$parent_name = "";
					/*$query2="select parent_id from promoters where  status=1 and isvalid=true and user_id=".$user_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {
					    $parent_id = $row2->parent_id;
						break;
					}*/
					if($parent_id<0){
						$parent_id = $user_parent_id;
					}

					$query2="select createtime,isAgent,is_consume,generation,qrsell_orderothers from promoters where isvalid=true and user_id=".$user_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$isAgent = 0;
					$is_consume = 0;
					$generation = 1;
					$qrsell_orderothers = "";
					while ($row2 = mysql_fetch_object($result2)) {
					    $createtime = $row2->createtime;
						$isAgent = $row2->isAgent;	//判断 0为推广员 1为代理商 2为顶级推广员
						$is_consume = $row2->is_consume;	//判断 0:不是无限级奖励 1:无限级奖励
						$generation = $row2->generation;	//推广员代数
						$qrsell_orderothers = $row2->qrsell_orderothers;	//推广员申请自定义自动
						break;
					}
					$generation=$generation."代推广员";
					$query2="select all_areaname from weixin_commonshop_team_area where isvalid=true and area_user=".$user_id." and customer_id=".$customer_id;//团队奖励 此人分配的区域
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$all_areaname = "";//团队奖励 分配区域的全称
					while ($row2 = mysql_fetch_object($result2)) {
					    $all_areaname = $row2->all_areaname;
						break;
					}
					$query4="select a_name,b_name,c_name,d_name from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id." limit 0,1";
					$result4 = mysql_query($query4);
					while($row4 = mysql_fetch_object($result4)){
						$a_name=$row4->a_name;
						$b_name=$row4->b_name;
						$c_name=$row4->c_name;
						$d_name=$row4->d_name;
					}
					$consume_name ="";
					if($is_team==1 && $is_shareholder==0){
						if($is_consume>0){
							$consume_name = "(无限级奖励)";
						}
					}else if($is_shareholder==1){ 
						switch($is_consume){
							case 1: $consume_name = "(股东分红-".$d_name.")"; break;
							case 2: $consume_name = "(股东分红-".$c_name.")"; break;
							case 3: $consume_name = "(股东分红-".$b_name.")"; break;
							case 4: $consume_name = "(股东分红-".$a_name.")"; break;
						}
					}
					$agentname="";
					switch($isAgent){
						case 1:
							$agentname = "(代理商)";
							break;
						case 2:
							$agentname = "(顶级推广员)";
							break;
						case 3:
							$agentname = "(供应商)";
							break;
						case 5:
							$agentname = "(区代)";
							break;
						case 6:
							$agentname = "(市代)";
							break;
						case 7:
							$agentname = "(省代)";
							break;
						
					}
					//推广员数量
					/*$promoter_count=0;
					$query2="select count(distinct user_id) as promoter_count from promoters where status=1 and isvalid=true and parent_id=".$user_id." and customer_id=".$customer_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {    
					    $promoter_count = $row2->promoter_count;
						break;
					}*/
					$parent_weixin_fromuser="";
					if($parent_id>0 and $parent_id!=$user_id){
					   
						$query2="select id from promoters where  status=1 and isvalid=true and user_id=".$parent_id;
						$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
						$promoter_id = -1;
						while ($row2 = mysql_fetch_object($result2)) {    
						    $promoter_id = $row2->id;
							break;
						}
                       						
						if($promoter_id>0){
							$query2= "select name,phone,parent_id,weixin_name,weixin_fromuser from weixin_users where isvalid=true and id=".$parent_id; 
							$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
							while ($row2 = mysql_fetch_object($result2)) {
								$parent_name=$row2->name;
								$weixin_name = $row2->weixin_name;
								$parent_weixin_fromuser = $row2->weixin_fromuser;
								$parent_name = $parent_name."(".$weixin_name.")";
								break;
							}
						}
					}
					//查找账户和支付宝
					
					$query2="select account,account_type,bank_open,bank_name from weixin_card_members where isvalid=true and user_id=".$user_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$account = "";
					$account_type="";
					$bank_open="";
					$bank_name="";
					while ($row2 = mysql_fetch_object($result2)) {
					    $account= $row2->account;
						$account_type =$row2->account_type;
						$bank_open = $row2->bank_open;
						$bank_name = $row2->bank_name;
					}
					$account_type_str="";
					switch($account_type){
					    case 1:
						   $account_type_str="支付宝";
						   break;
					    case 2:
						   $account_type_str="财付通";
						   break;
						case 3:
						   $account_type_str="银行账户";
						   break;
					}
			
					//查找推广员的会员卡号
					$Membership_Card=-1;
					$query_m="SELECT id from weixin_card_members where isvalid=true and card_id=".$shop_card_id." and user_id=".$user_id;
					$result_m = mysql_query($query_m) or die('Query failed: ' . mysql_error());
					while ($row_m = mysql_fetch_object($result_m)) {
					   $Membership_Card = $row_m->id;
					   break;
					}					
			
					//显示该推广员已经购买的商品总金额(已经付款的)
					$query2="select sum(totalprice) as s_totalprice from weixin_commonshop_orders where isvalid=true and paystatus=1 and  user_id=".$user_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$s_totalprice=0;;
					while ($row2 = mysql_fetch_object($result2)) {
					    $s_totalprice = $row2->s_totalprice;
					}
					
					$s_totalprice = round($s_totalprice,2);
					
					$query2="select title,online_qq from weixin_commonshop_owners where isvalid=true and user_id=".$user_id;
					$mystore_title="";
					$mystore_qq="";
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					
					while ($row2 = mysql_fetch_object($result2)) {
					    $mystore_title=$row2->title;
						$mystore_qq = $row2->online_qq;
						break;
					}
					
					$tmp = $id.'_'.$user_id.'_'.$parent_id.'_'.$isAgent.'_'.$customer_id_en.'_'.$pagenum.'_'.$qr_info_id;  // 数据是用于操作 type 1:成为顶级推广员,2:推广员通过 3:驳回推广员 4:删除推广员 5:取消上下级关系
			   ?>
                <tr>
				   <td>
						<span style="display:block"><?php echo $user_id;?></span>
						<span style="display:block"><?php echo $generation;?></span>
						<span style="display:block"><?php echo $agentname;?></span>
						<span style="display:block"><?php echo $all_areaname;?></span>
						<span style="display:block"><?php echo $consume_name;?></span>
					</td>
				   <td style="text-align:left;"><a title="会员卡号:<?php echo $Membership_Card; ?>" href="../card_member.php?card_id=<?php echo $shop_card_id; ?>&card_member_id=<?php echo $Membership_Card; ?>&customer_id=<?php echo passport_encrypt((string)$customer_id);?>"><?php echo $username; ?></a>
				   <?php if(!empty($weixin_fromuser)){
							 ?>  
							   <a  class="btn"  href="../weixin_inter/send_to_msg.php?fromuserid=<?php echo $weixin_fromuser; ?>&customer_id=<?php echo passport_encrypt($customer_id)?>"  title="对话"><i  class="icon-comment"></i></a>
							<?php   
						   }  ?>
				   
				   <br/>
				       <?php echo $userphone; ?><br/>
					   收款类型:<?php echo $account_type_str; ?><br/>
					   收款账户:<?php echo $account; ?>
					   <?php if($account_type==3){ ?>
					   <br/>开户银行：<?php echo $bank_open; ?>
					   <br/>开户姓名：<?php echo $bank_name; ?>
					   <?php } ?>
					   <?php if(!empty($mystore_title)){ ?>
					     <br/>微店名称:<?php echo $mystore_title; ?><br/>
						 在线QQ:<?php echo $mystore_qq; ?> 
					   <?php } ?>
				   </td>
				   
				   <td><a href="<?php echo $imgurl_qr; ?>" target="_blank"><img src="<?php echo $imgurl_qr; ?>" style="width:40px;height:40px;" /></a></td>
				   <td>
				   一级会员数:&nbsp;<a href="qrsell_detail_member.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&rcount=<?php echo $fans_count; ?>"><?php echo $fans_count; ?></a><br/>
				   一级推广员数:&nbsp;<a href="qrsell_detail.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&rcount=<?php echo $promoter_count; ?>"><?php echo $promoter_count; ?></a>
				   </td>

 				   <td><a href="qrsell_money.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&sum_totalprice=<?php echo $sum_totalprice; ?>"><?php echo $sum_totalprice; ?>元</a></td>
				   <td>

				     <a href="qrsell_rewardmoney.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&type=1&sum_totalscore=<?php echo $reward_score; ?>"><?php echo $reward_score; ?></a>
				   </td>
				   <td>

				     <a href="qrsell_rewardmoney.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&type=2&sum_totalprice=<?php echo $reward_money; ?>"><?php echo $reward_money; ?></a>
				   </td>
				   <td>
				     <?php echo $statusstr; ?><br/>
					 <?php if(!empty($reason)){ ?>
					 (<span style="font-size:12px;"><?php echo $reason; ?></span>)
					 <?php } ?>
				   </td>
				   <td>

				     <a href="qrsell.php?exp_user_id=<?php echo $parent_id; ?>&customer_id=<?php echo $customer_id_en; ?>"><?php echo $parent_name; ?></a>
					  <?php if(!empty($parent_weixin_fromuser)){
							 ?>  
							   <a  class="btn"  href="../weixin_inter/send_to_msg.php?fromuserid=<?php echo $parent_weixin_fromuser; ?>&customer_id=<?php echo passport_encrypt($customer_id)?>"  title="对话"><i  class="icon-comment"></i></a>
							<?php   
						   }  ?>
				   </td>
				   <td><a href="customers.php?search_user_id=<?php echo $user_id; ?>"><?php echo $s_totalprice; ?></a></td>
				   <td><?php echo $createtime; ?></td>
				   <?php
					$qrsell_orderothers=str_replace(",","</br>",$qrsell_orderothers);
				   if(!empty($open_qrsell_orderothers)){?>
				   <td style="text-align: left;"><?php echo $qrsell_orderothers; ?></td>
				   <?php } ?>
				   <td>
				      

						<a href="add_qrsell_account.php?customer_id=<?php echo $customer_id_en; ?>&isAgent=<?php echo $isAgent; ?>&user_id=<?php echo $user_id; ?>&parent_id=<?php echo $parent_id; ?>&status=<?php echo $status; ?>&pagenum=<?php echo $pagenum; ?>"><img src="images/mod.gif" align="absmiddle" alt="编辑推广员" title="编辑推广员"></a>
						<?php if($is_shareholder==1){?>
						<a href="change_shareholder.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&parent_id=<?php echo $parent_id; ?>&status=<?php echo $status; ?>&pagenum=<?php echo $pagenum; ?>"><img src="images/mod.gif" align="absmiddle" alt="修改股东等级" title="修改股东等级"></a>
						<?php }?>         
							
					 <a href="qrsell.php?customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>&op=resetpwd&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>" onclick="if(!confirm(&#39;重置后密码为：888888。继续？&#39;)){return false};"><img src="images/m-ico-9.png" align="absmiddle" alt="重置密码" title="重置密码"></a> 
					 
				    <?php if($status==0){?>	
					    <?php if($parent_id>0){ ?>
							<a  class="btn"  onclick="qrsell_confirm(1,'<?php echo $tmp;?>')"  title="成为顶级推广员">
							  <img src="images/m-ico-8.png" align="absmiddle" alt="成为顶级推广员" title="成为顶级推广员">
							</a> 
						<?php }?>
						<a  class="btn"  onclick="qrsell_confirm(2,'<?php echo $tmp;?>')"  title="通过">
						  <i  class="icon-ok"></i>
						</a>
						<?php if($isAgent!=1){ ?>
						 <a  class="btn"  onclick="qrsell_confirm(3,'<?php echo $tmp;?>')"  title="驳回/暂停">
						  <i  class="icon-minus"></i>
						</a>
						<?php }?>
					<?php }else if($status==1){ ?>
						<?php if($parent_id>0){ ?>
							<a  class="btn"  onclick="qrsell_confirm(1,'<?php echo $tmp;?>')"  title="成为顶级推广员">
							  <img src="images/m-ico-8.png" align="absmiddle" alt="成为顶级推广员" title="成为顶级推广员">
							</a> 
						<?php }?>
						<?php if($isAgent!=1){ ?>
					    <a  class="btn"  onclick="qrsell_confirm(3,'<?php echo $tmp;?>')"  title="驳回/暂停">
						  <i  class="icon-minus"></i>
						</a>
						<?php }?>
						 <a  class="btn" onclick="qrsell_confirm(5,'<?php echo $tmp;?>')" onclick="if(!confirm(&#39;确认取消上下级关系后不可恢复，继续吗？&#39;)){return false};"  title="取消上下级关系">
						  <i  class="icon-minus"></i>
						</a>
						
					<?php }else if($status==-1){ ?>
					   <?php if($parent_id>0){ ?>
							<a  class="btn"  onclick="qrsell_confirm(1,'<?php echo $tmp;?>')"  title="成为顶级推广员">
							  <img src="images/m-ico-8.png" align="absmiddle" alt="成为顶级推广员" title="成为顶级推广员">
							</a> 
						<?php }?>
						<a  class="btn"  onclick="qrsell_confirm(2,'<?php echo $tmp;?>')"  title="通过">
						  <i  class="icon-ok"></i>
						</a>
					<?php } ?>
					<a onclick="qrsell_confirm(4,'<?php echo $tmp;?>')"><img src="images/del.gif" align="absmiddle" alt="删除" title="删除"></a>

					<a href="qrsell.php?customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>&op=p_tupian&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>" onclick="if(!confirm(&#39;删除推广员二维码图片可重新获取&#39;)){return false};"><img src="images/del.gif" align="absmiddle" alt="删除推广图片" title="删除推广图片"></a>					
				   </td>
				   
                </tr>				
				
			   <?php } ?>
			   
			   <tr>
			      <td colspan=12>
				  <div class="tcdPageCode"></div>
				 </td>
			   </tr>
			</tbody>
		</table>
		<div class="blank20"></div>
		<div id="turn_page"></div>
	</div>	</div>
<div>
</div></div>


<?php 

mysql_close($link);
?>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/fenye/fenye.css" media="all">

<script src="../js/fenye/jquery.page.js"></script>
<script>
var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
  var count =Math.ceil(rcount_q2/end);//总页数
	//pageCount：总页数
	//current：当前页
	$(".tcdPageCode").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			 var search_status = document.getElementById("search_status").value; 
			 var search_user_id = document.getElementById("search_user_id").value; 
			 var search_name = document.getElementById("search_name").value; 
			 var search_phone = document.getElementById("search_phone").value; 

			 document.location= "qrsell.php?pagenum="+p+"&search_user_id="+search_user_id+"&search_status="+search_status+"&search_name="+search_name+"&search_phone="+search_phone+"&customer_id=<?php echo $customer_id_en;?>";
	   }
    });
function del_tupian(){
	if(confirm("清除所有推广图片。确认？")){
document.location="qrsell.php?customer_id=<?php echo $customer_id_en; ?>&op=p_tupian2&pagenum=<?php echo $pagenum; ?>";
	}
}

  function searchForm(){
		var search_user_id = document.getElementById("search_user_id").value; 
		var search_status = document.getElementById("search_status").value; 
		var search_generation = document.getElementById("search_generation").value; 
		var search_name = document.getElementById("search_name").value; 
		var search_phone = document.getElementById("search_phone").value; 
		document.location= "qrsell.php?pagenum=1&search_user_id="+search_user_id+"&search_status="+search_status+"&search_generation="+search_generation+"&search_name="+search_name+"&search_phone="+search_phone+"&customer_id=<?php echo $customer_id_en;?>";
  }
  
  function exportRecord(){
     var search_status = document.getElementById("search_status").value;
     var search_user_id =document.getElementById("search_user_id").value;
	 var search_name =document.getElementById("search_name").value;
	 var search_phone =document.getElementById("search_phone").value;
	 
	 if(search_user_id==""){
	    search_user_id="0";
	 }
	 if(search_name==""){
	    search_name="0";
		// alert('name=====');
	 }
	 if(search_phone==""){
	    search_phone="0";
	 }
     var url='/weixin/plat/app/index.php/Excel/commonshop_excel_qrsell/customer_id/<?php echo $customer_id; ?>/status/'+search_status+'/search_user_id/'+search_user_id+'/search_name/'+search_name+'/search_phone/'+search_phone+'/exp_user_id/<?php echo $exp_user_id; ?>/';
	 console.log(url);
	 goExcel(url,1,'http://<?php echo $http_host;?>/weixinpl/');
  }
   function exportRecord1(){
		var search_status = document.getElementById("search_status").value;
		var search_user_id =document.getElementById("search_user_id").value;
		var search_name =document.getElementById("search_name").value;
		var search_phone =document.getElementById("search_phone").value;
		var begintime = document.getElementById("begintime").value;
		var endtime = document.getElementById("endtime").value;		
		 if(search_user_id==""){
			search_user_id="0";
		 }
		 if(search_name==""){
			search_name="0";
			// alert('name=====');
		 }
		 if(search_phone==""){
			search_phone="0";
		 }
     var url='/weixin/plat/app/index.php/Excel/commonshop_excel_qrsell_detail/customer_id/<?php echo $customer_id; ?>/scene_id/<?php echo $user_id; ?>/type/2/status/'+search_status+'/search_user_id/'+search_user_id+'/search_name/'+search_name+'/search_phone/'+search_phone;
	 if(begintime !=""){
		url=url+'/begintime/'+begintime;
	}
	if(endtime !=""){
		url=url+'/endtime/'+endtime;
	}
	 console.log(url);
	 goExcel(url,1,'http://<?php echo $http_host;?>/weixinpl/');
  }
  
  function qrsell_confirm(status,tmp){
	 
	  switch(status){
		  case 1:
			 var reason = "";
			 var i = window.confirm("确认成为顶级推广员，不会再建立上级，继续吗");
		  break;
		  case 2:
			 var reason = "";
			 var i = window.confirm("确认成为推广员，继续吗");
		  break; 
		  case 3:
			 var reason = prompt("请输入驳回/暂停理由","您不符合<?php echo $exp_name; ?>条件，请联系客服");
			 if(reason!=null){
					var i = true;
			 }else{
					var i = false;
			 }
		  break;
		  case 4:
			 var reason = "";
			 var i = window.confirm("删除后不可恢复，继续吗？");
		  break;
		  case 5:
			 var reason = "";
			 var i = window.confirm("确认取消上下级关系后不可恢复，继续吗？");
		  break;
	  }
	  //console.log(status+'='+reason+'='+i);
	  var strs= new Array();  
	  strs=tmp.split("_"); 
	  if(strs[3]==1){
		  alert("您还是代理商,请先删除代理商身份");
		  return;
	  }
	  if(strs[3]==3){
		  alert("您还是供应商,请先删除供应商身份");
		  return;
	  }
	  if(strs[3]==5){
		  alert("您还是区级代理,请先删除区级代理身份");
		  return;
	  }
	  if(strs[3]==6){
		  alert("您还是市级代理,请先删除市级代理身份");
		  return;
	  }
	  if(strs[3]==7){
		  alert("您还是省级代理,请先删除省级代理身份");
		  return;
	  }
	  if(i===true){
		 $.ajax({
				type: 'POST',
				url: "qrsell_status.php",
				data: {
					type:status, 
					id:strs[0], 
					user_id:strs[1], 
					parent_id:strs[2], 
					isAgent:strs[3], 
					customer_id:strs[4], 
					pagenum:strs[5],
					qr_info_id:strs[6],
					reason:reason
				},
				dataType: "json",
				success:function(data){
					url="qrsell.php?customer_id=<?php echo $customer_id_en;?>&pagenum="+pagenum;
					location.replace(url);
				} 

			}); 
	  }else{ 
		return false;
	  }
			
  }

</script>
</body></html>