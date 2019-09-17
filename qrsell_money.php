<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../proxy_info.php');
$scene_id = $configutil->splash_new($_GET["scene_id"]);
$sum_totalprice = $configutil->splash_new($_GET["sum_totalprice"]);
mysql_query("SET NAMES UTF8");
$query2= "select name,phone from weixin_users where isvalid=true and id=".$scene_id." limit 0,1"; 
$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
$username="";
$userphone="";
while ($row2 = mysql_fetch_object($result2)) {
	$username=$row2->name;
	$userphone = $row2->phone;
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
?>
<!DOCTYPE html>
<!-- saved from url=(0047)http://www.ptweixin.com/member/?m=shop&a=orders -->
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">	
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
</head>

<body>
<div id="WSY_content">
	<div class="WSY_columnbox" style="min-height: 300px;">
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">推广员</a>
			</div>
		</div>
		<div  class="WSY_data">
			<div id="WSY_list" class="WSY_list">
				<div class="WSY_left" style="background: none;">
					姓名：<span style="font-weight:bold"><?php echo $username; ?></span>&nbsp;&nbsp;&nbsp; 手机号：<span style="font-weight:bold"><?php echo $userphone; ?></span>&nbsp;&nbsp;&nbsp;
					推广金额：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $sum_totalprice; ?></span>
				</div>
				<li style="margin-right: 60px;float:right;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>

			</div>
		<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<tr>
					<th width="17%" nowrap="nowrap">编号</th>
					<th width="13%" nowrap="nowrap">姓名</th>
					<th width="13%" nowrap="nowrap">手机号</th>
					<th width="10%" nowrap="nowrap">订单号</th>
					<th width="10%" nowrap="nowrap">订单金额</th>
					<th width="10%" nowrap="nowrap">返佣总金额</th>
					<th width="10%" nowrap="nowrap">时间</th>
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
				$query="select id,sum(totalprice) as totalprice,batchcode,createtime,user_id from weixin_commonshop_orders where status=1 and paystatus=1 and isvalid=true and customer_id=".$customer_id." and  (exp_user_id>0 and exp_user_id=".$scene_id.") and aftersale_state!=4  group by batchcode order by batchcode desc ";				 
				$result_q = mysql_query($query) or die('Query failed5: ' . mysql_error());
				$rcount_q = mysql_num_rows($result_q);
				$query = $query." limit ".$start.",".$end;
				//echo $query;
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
				    $user_id    = $row -> user_id;
					$id         = $row -> id;
					$totalprice = $row -> totalprice;
					$batchcode  = $row -> batchcode;
					$createtime = $row -> createtime;
					$query2  = "select name,phone,weixin_name from weixin_users where isvalid=true and id=".$user_id." limit 0,1"; 
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$username    = "";
					$userphone   = "";
					$weixin_name = "";
	                while ($row2 = mysql_fetch_object($result2)) {
					    $username    = $row2 -> name;
						$userphone   = $row2 -> phone;
						$weixin_name = $row2 -> weixin_name;
						break;
					}
					$username     = $username."(".$weixin_name.")";
					$reward_money = 0;
					$sql = "select reward_money from weixin_commonshop_order_prices where isvalid=true and batchcode=".$batchcode; 
					//echo $sql;
					$result2 = mysql_query($sql) or die('Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {
					    $reward_money = $row2 -> reward_money;
						break;
					}					
					$reward_money = round($reward_money,2);
					
					
					
			   ?>
                <tr>
				   <td align="center"><?php echo $id; ?></td>
				   <td align="center"><?php echo $username; ?></td>
				   <td align="center"><?php echo $userphone; ?></td>
				   <td align="center"><a href="../../Order/order/order.php?customer_id=<?php echo $customer_id_en; ?>&search_batchcode=<?php echo $batchcode; ?>&status=-1"  style="color:#2eade8;"><?php echo $batchcode; ?></a></td>
				   <td align="center"><?php echo $totalprice; ?></td>
				   <td align="center"><?php echo $reward_money; ?></td>
				   <td align="center"><?php echo $createtime; ?></td>
                </tr>				
			   <?php } ?>
			</tbody>
		</table>
		<div class="blank20"></div>
		<div id="turn_page"></div>
		</div>	
			<!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
	</div>
</div>
	


<?php 

mysql_close($link);
?>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
var customer_id = '<?php echo $customer_id_en ?>';
var sum_totalprice = <?php echo $sum_totalprice ?>;
var scene_id = <?php echo $scene_id ?>;
var pagenum = <?php echo $pagenum ?>;
var rcount_q2 = <?php echo $rcount_q ?>;
var end = <?php echo $end ?>;
var count = Math.ceil(rcount_q2/end);//总页数
console.log(count);

var page = count;
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			
		document.location= "qrsell_money.php?pagenum="+p+"&customer_id="+customer_id+"&sum_totalprice="+sum_totalprice+"&scene_id="+scene_id;
	   }
    });

  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val()); 
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "qrsell_money.php?pagenum="+a+"&customer_id="+customer_id+"&sum_totalprice="+sum_totalprice+"&scene_id="+scene_id;
		
	}
  }
</script>

</body></html>