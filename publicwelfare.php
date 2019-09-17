<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');

mysql_query("SET NAMES UTF8");
require('../auth_user.php');

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

$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	}
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

$query="select count(1) as new_qr_count from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}



$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}

$search_name="";
if(!empty($_GET["search_name"])){
	$search_name = $configutil->splash_new($_GET["search_name"]);
}
$query ="select publicwelfare from weixin_commonshop_publicwelfare where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $publicwelfare = $row->publicwelfare;
	}

	 $begintime="";
	if(!empty($_GET["begintime"])){
		$begintime = $configutil->splash_new($_GET["begintime"]);
	}
	$endtime="";
	if(!empty($_GET["endtime"])){
	    $endtime = $configutil->splash_new($_GET["endtime"]);
	}

?>
<!DOCTYPE html>
<!-- saved from url=(0047)http://www.ptweixin.com/member/?m=shop&a=orders -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/icon.css" media="all">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/inside.css" media="all">
<script type="text/javascript" src="../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/global.js"></script>
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
			<link href="css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/shop.js"></script>
	<div class="r_nav">
		<ul>
			<li id="auth_page0" class=""><a href="base.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a></li>
			<li id="auth_page1" class=""><a href="fengge.php?customer_id=<?php echo $customer_id_en; ?>">风格设置</a></li>
			<li id="auth_page2" class=""><a href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&default_set=1">首页设置</a></li>
			<li id="auth_page3" class=""><a href="product.php?customer_id=<?php echo $customer_id_en; ?>">产品管理</a></li>
			<li id="auth_page4" class=""><a href="order.php?customer_id=<?php echo $customer_id_en; ?>&status=-1">订单管理</a></li>
			<?php if($is_supplierstr){?><li id="auth_page5" class=""><a href="supply.php?customer_id=<?php echo $customer_id_en; ?>">供应商</a></li><?php }?>
			<?php if($is_distribution){?><li id="auth_page6" class=""><a href="agent.php?customer_id=<?php echo $customer_id_en; ?>">代理商</a></li><?php }?>
			<li id="auth_page7" class=""><a href="qrsell.php?customer_id=<?php echo $customer_id_en; ?>">推广员</a></li>
			<li id="auth_page8"><a href="customers.php?customer_id=<?php echo $customer_id_en; ?>">顾客</a></li>
			<li id="auth_page9" class=""><a href="shops.php?customer_id=<?php echo $customer_id_en; ?>">门店</a></li>
			<?php if($isOpenPublicWelfare){?><li id="auth_page10" class="cur"><a href="publicwelfare.php?customer_id=<?php echo $customer_id_en; ?>">公益基金</a></li><?php }?>
		</ul>
	</div>
<link href="css/operamasks-ui.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/operamasks-ui.min.js"></script>
<script type="text/javascript" src="../js/tis.js"></script>
<script language="javascript">

$(document).ready(shop_obj.orders_init);
</script>
<div id="orders" class="r_con_wrap">
		<form class="search" id="search_form">
			&nbsp;公益基金总额：<span style="padding-left:10px;color:red;font-size:18px;font-weight:bold">￥<?php echo $publicwelfare;?></span>
		   &nbsp;&nbsp;&nbsp;&nbsp;捐助时间：<span class="om-calendar om-widget om-state-default">
			<input type="text" class="input" id="begintime" name="AccTime_S" value="<?php echo $begintime; ?>" maxlength="20" id="K_1389249066532">
			<span class="om-calendar-trigger"></span></span>-<span class="om-calendar om-widget om-state-default">
			<input type="text" class="input" id="endtime" name="AccTime_E" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580">
			<span class="om-calendar-trigger"></span></span>&nbsp;
		   <input type="button" class="search_btn" onclick="searchForm();" value="搜 索">
		</form>	
		<table border="0" cellpadding="5" cellspacing="0" class="r_con_table" id="order_list">
			<thead>
				<tr>
					<td width="12%" nowrap="nowrap">名称</td>
					<td width="14%" nowrap="nowrap">订单号</td>
					<td width="14%" nowrap="nowrap">捐助时间</td>
					<td width="14%" nowrap="nowrap">个人累计捐助金额</td>
					<td width="14%" nowrap="nowrap">单次捐助金额</td>
				</tr>
			</thead>
			<tbody>
			   <?php 
			   
			   $pagenum = 1;

				if(!empty($_GET["pagenum"])){
				   $pagenum = $configutil->splash_new($_GET["pagenum"]);
				}

				$start = ($pagenum-1) * 20;
				$end = 20;				
				
				$query = 'SELECT user_id,createtime,before_score,add_score,batchcode FROM weixin_commonshop_publicwelfare_log where isvalid=true and customer_id='.$customer_id;				              
			    if($begintime!=""){
				   $query = $query." and UNIX_TIMESTAMP(weixin_commonshop_publicwelfare_log.createtime)>".strtotime($begintime);
				 }
				 if($endtime!=""){
				   $query = $query." and UNIX_TIMESTAMP(weixin_commonshop_publicwelfare_log.createtime)<".strtotime($endtime);
				 } 
				 $result = mysql_query($query);
				 $rcount_q2 = mysql_num_rows($result);
				 $query1=$query.' order by id desc limit '.$start.','.$end;
				 
			   $result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());
			   
			   while ($row = mysql_fetch_object($result1)) {
				   $welfare_user_id = $row->user_id;
				   $createtime = $row->createtime;
				   $before_score = $row->before_score;
				   $add_score = $row->add_score;
				   $batchcode = $row->batchcode;
				   $total_score=$before_score+$add_score;
					$query2= "select name,weixin_name from weixin_users where isvalid=true and id=".$welfare_user_id; 
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$weixin_name="";
					$username="";
					while ($row = mysql_fetch_object($result2)) {
						$username=$row->name;
						$weixin_name = $row->weixin_name;
						$username = $username."(".$weixin_name.")";
					}
					
				     
				/* if(!empty($search_name)){
					$query=$query." and name like '%".$search_name."%'";
				}
				 */
				
			   
           				 
			
			   ?>
                <tr>
				   <td><?php echo $username; ?></td>
				   <td><a href="order.php?&search_batchcode=<?php echo $batchcode; ?>"><?php echo $batchcode; ?></a></td>
				   <td><?php echo $createtime; ?></td>
				   <td><?php echo $total_score; ?></td>
				   <td><?php echo $add_score; ?></td>
                </tr>				
				
			   <?php } ?>
			   
			   <tr>
			      <td colspan=13>
			       <!-- <div class="getmore">
					 <?php if($pagenum>1){ ?>
					 <div class="getmore_l" onclick="prePage();">
						上一页
					 </div>
					 <?php } ?>
					  
					 <?php if($rcount_q==20){?>
					 <div class="getmore_r"  onclick="nextPage();">
						下一页
					 </div>
					 <?php } ?>
				  </div> -->
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
		var begintime = document.getElementById("begintime").value;
		var endtime = document.getElementById("endtime").value;
		document.location= "publicwelfare.php?pagenum="+p+"&begintime="+begintime+"&endtime="+endtime;
	}
});
function searchForm(){
	var begintime = document.getElementById("begintime").value;
    var endtime = document.getElementById("endtime").value;
	document.location= "publicwelfare.php?pagenum=1&begintime="+begintime+"&endtime="+endtime;
}
</script>
</body></html>