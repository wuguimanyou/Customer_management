<?php 
header("Content-type: text/html; charset=utf-8");
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');

if(!empty($_GET['customer_id'])){
	$customer_id = $configutil->splash_new($_GET['customer_id']);
	$customer_id = passport_decrypt($customer_id);

if(!empty($_GET['cha_batchcode'])){
	$cha_batchcode = $_GET['cha_batchcode'];
}else{
	$cha_batchcode = '';
}

if(!empty($_GET['g_group_id'])){
	$g_group_id = $_GET['g_group_id'];
}else{
	$g_group_id = '';
}


$group_id = -1;
$time = date("Y-m-d H:i:s",time());

if($cha_batchcode != ''){
	$querys = "select count(1) as count from weixin_commonshop_order_group where isvalid=true and customer_id=".$customer_id." and batchcode='".$cha_batchcode."'";
}else{
	$querys = "select count(1) as count from weixin_commonshop_order_group where isvalid=true and customer_id=".$customer_id;
	if($g_group_id != ''){
		$querys = $querys." and group_id=".$g_group_id;
	}
}

$rows = mysql_query($querys);
$rods = mysql_fetch_object($rows);
	$count = $rods->count;
	$page = ceil($count/20);
	$num = 1;
	if(!empty($_GET['num'])){
		$num = $_GET['num'];
	}
	$start = ($num-1)*20;
	$end = 20;

if($cha_batchcode != ''){
	$query = "select * from weixin_commonshop_order_group where isvalid=true and customer_id=".$customer_id." and batchcode='".$cha_batchcode."' order by id desc limit ".$start.",".$end;
}else{
	$query = "select * from weixin_commonshop_order_group where isvalid=true and customer_id=".$customer_id;
	if($g_group_id != ''){
		$query = $query." and group_id=".$g_group_id;
	}
	
	$query = $query." order by id desc limit ".$start.",".$end;
}

$row = mysql_query($query);

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>订单管理</title>
<script type="text/javascript" src="../../common/js/jquery-2.1.0.min.js"></script>
<script charset="utf-8" src="../../common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript" src="../../js/WdatePicker.js"></script> 
<link rel="stylesheet" href="table.css">

</head>
<body style="background-color:white">
<div class="juzhong">
	<span class="input"><a href="pt_order.php?customer_id=<?php echo $customer_id;?>">
	<input type="button" value="所有订单"></a></span>
	<span class="input"><a href="pt_team.php?customer_id=<?php echo $customer_id;?>">
	<input type="button" value="团队订单"></a></span>
	<input type="text" id="cha" value="<?php if($cha_batchcode != ''){echo $cha_batchcode;}?>"><input type="button" value="搜索订单" onclick="cha()">
</div>
<table border="1px">
<tr style="word-break: keep-all;">
	<th>订单编号</th>
	<th>单买/拼团</th>
	<th>商品名称</th>
	<th>商品价格</th>
	<th>商品数量</th>
	<th>实付价格</th>
	<th>买家信息</th>
	<th>收货信息</th>
	<th>拼团信息</th>
	<th>支付状态</th>
	<th>发货状态</th>
	<th>订单状态</th>
	<th>下单时间</th>
	<th>操作</th>
</tr>
<?php 
while($rod = mysql_fetch_object($row)){
	$batchcode = $rod->batchcode;					//订单编号
	$createtime = $rod->createtime;					//下单时间
	$user_id = $rod->user_id;						//买家id
	$totalprice = $rod->totalprice;					//实付价格（包括邮费）
	$status = $rod->status;							//拼团状态 1进行中 2成功 3失败
	$pid = $rod->pid;								//商品编号
	$group_id = $rod->group_id;						//团编号
	$paystatus = $rod->paystatus;					//支付状态
	$sendstatus = $rod->sendstatus;					//发货状态
	$type = $rod->type;								//是否拼团   1拼团  2单买
	$price = $rod->price;							//商品价格(拼团或单买)
	$express_price = $rod->express_price;			//运费
	$count = $rod->count;							//数量
	$address_id = $rod->address_id;					//收货地址id
	$orderstatus = $rod->orderstatus;				//订单完成状态

	$phone = '';
	$name = '';
	$weixin_name = '';
	$user_name = '';
	$querys = "select phone,name,weixin_name from weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id;
	$rows = mysql_query($querys);
	$rods = mysql_fetch_object($rows);
		$phone = $rods->phone;
		$name = $rods->name;						//买家名称
		$weixin_name = $rods->weixin_name;			//微信名称
		$user_name = $name."(".$weixin_name.")";	

	$shop_name = '';
	$query2 = "select name from weixin_commonshop_group_products where isvalid=true and id=".$pid." and customer_id=".$customer_id;
	$row2 = mysql_query($query2);
	$rod2 = mysql_fetch_object($row2);
		$shop_name = $rod2->name;					//商品名称
	
	$g_createtime = '';
	$g_number = '';
	$g_overtime = '';
	$g_existing_number = '';
	$g_pid = '';
	$g_user_id = '';
	$g_status = '';
	$g_address_id = '';
	if($group_id>0){
	$query3 = "select * from weixin_commonshop_group where isvalid=true and id=".$group_id." and customer_id=".$customer_id;
	$row3 = mysql_query($query3);
	$rod3 = mysql_fetch_object($row3);
		$g_createtime = $rod3->createtime;					//开团时间
		$g_number = $rod3->number;							//所需人数量
		$g_overtime = $rod3->overtime;						//结束时间
		$g_existing_number = $rod3->existing_number;		//现有人数
		$g_pid = $rod3->pid;								//团队 商品编号
		$g_user_id = $rod3->user_id;						//团长 编号
		$g_status = $rod3->status;							//拼团状态 1进行中 2成功 3失败
		$g_address_id = $rod3->address_id;					//团长 地址编号
	}

	$u_name = '';
	$u_location_p = '';
	$u_location_c = '';
	$u_location_a = '';
	$u_address = '';
	$u_phone = '';
	$query4 = "select * from weixin_commonshop_addresses where isvalid=true and id=".$address_id;
	$row4 = mysql_query($query4);
	$rod4 = mysql_fetch_object($row4);
		$u_name = $rod4->name;								//买家 收货人名称
		$u_location_p = $rod4->location_p;					//省
		$u_location_c = $rod4->location_c;					//市
		$u_location_a = $rod4->location_a;					//区
		$u_address = $rod4->address;						//详细地址
		$u_phone = $rod4->phone;							//联系 手机号码
		switch($u_location_p){
			case '香港特别行政区' : $u_address = '香港';break;
			case '澳门特别行政区' : $u_address = '澳门';break;
			case '内蒙古自治区' : $u_address = '内蒙古';break;
			case '西藏自治区' : $u_address = '西藏';break;
			case '广西壮族自治区' : $u_address = '广西';break;
			case '宁夏回族自治区' : $u_address = '宁夏';break;
			case '新疆维吾尔自治区' : $u_address = '新疆';break;
			default : $u_address = $u_location_p;break;
		}

	$g_name = '';
	$g_location_p = '';
	$g_location_c = '';
	$g_address = '';
	$query5 = "select * from weixin_commonshop_addresses where isvalid=true and id=".$g_address_id;
	$row5 = mysql_query($query5);
	$rod5 = mysql_fetch_object($row5);
		$g_name = $rod5->name;								//团长 收货名称
		$g_location_p = $rod5->location_p;					//团长 收货地址 省
		$g_location_c = $rod5->location_c;					//团战 收货地址 市
		$g_phone = $rod5->phone;
		switch($g_location_p){
			case '香港特别行政区' : $g_address = '香港';break;
			case '澳门特别行政区' : $g_address = '澳门';break;
			case '内蒙古自治区' : $g_address = '内蒙古';break;
			case '西藏自治区' : $g_address = '西藏';break;
			case '广西壮族自治区' : $g_address = '广西';break;
			case '宁夏回族自治区' : $g_address = '宁夏';break;
			case '新疆维吾尔自治区' : $g_address = '新疆';break;
			default : $g_address = $g_location_p;break;
		}

?>
<tr>
	<td><?php echo $batchcode;?></td>
	<td><?php if($type==1){echo '拼团';}else{echo '单买';}?></td>
	<td><?php echo $shop_name;?></td>
	<td><?php echo '￥'.$price.'元';?></td>
	<td><?php echo $count;?></td>
	<td><p><?php echo '￥'.$totalprice.'元';?></p><?php if($express_price>0){echo '邮费：'.$express_price;}else{echo '免邮';}?></td>
	<td>
		<p><?php echo $user_name;?></p>
		<?php echo $phone;?>
	</td>
	<td>
		<p><?php echo $u_phone;?></p>
		<p><?php echo $u_name;?><p>
		<?php echo $u_address;?>
	</td>
	<td><?php if($type==1){ ?>
		<p><?php echo $group_id;?></p>
		<p>团长：<?php echo $g_name;?></p>
				<?php echo $g_address;?>
		<?php }else{
			echo '无';
		 }?>
	</td>
	<td><?php if($paystatus==2){echo '已支付';}else{echo '未支付';}?></td>
	<td><?php switch($sendstatus){
				case 1: echo '未发货';break;
				case 2: echo '已发货';break;
				case 3: echo '已收货';break;
				case 4: echo '申请退款';break;
				case 5: echo '已退款';break;
				case 6: echo '申请退货';break;
				case 7: echo '已退货';break;
		}?>
	</td>
	<td><?php if($stauts==2){echo '已确认';}else{echo '未确认';}?></td>
	<td><?php echo $createtime;?></td>
	<td>
		<p><?php if($paystatus == 1){ ?>
		<input style="word-break: break-all;" type="button" id="zhifu" value="后台支付" onclick="zhifu(obj='<?php echo $batchcode;?>')" >
		</p>
		<?php }?>
		<input type="button" id="del" value="删除订单" onclick="del(jkl='<?php echo $batchcode;?>')">
	</td>
</tr>
<?php }?>
</table>
<!--翻页开始-->
<div class="fanye">
<?php

for($pagenum=1;$pagenum<=$page;$pagenum++){
?>
<!--<span><a href="pt_order.php?num=<?php echo $pagenum;?>"><?php echo $pagenum;?></a></span>-->
<span><input type="button" id="num" value="<?php echo $pagenum;?>" style="<?php if($num==$pagenum){echo 'border-color: red;color: red;';}?>" onclick="show(<?php echo $pagenum;?>)"></span>
<?php } ?>
</div>
<!--翻页结束-->

<script type="text/javascript" src="jquery_tk.js"></script>
<script>
function zhifu(obj){
	if(confirm('确定要后台支付吗？')){
		var op = 'pay';
		var num = "<?php echo $num;?>";
		var customer_id = "<?php echo $customer_id;?>";
		$.ajax({
			type:"POST",
			url:"pt_saveorder.php",
			dataType:"html",
			data:"batchcode="+obj+"&op="+op+"&customer_id="+customer_id,
				success:function(dota){
					if(dota==1){
						alert('后台支付成功');
					}else{
						alert('后台支付失败');
					}
					document.location.href='pt_order.php?customer_id=<?php echo $customer_id;?>&num='+num;
				},
		});
	}
}

function del(jkl){
	if(confirm('确定要删除这笔订单吗?')){
		var op = 'del';
		var num = "<?php echo $num;?>";
		var customer_id = "<?php echo $customer_id;?>";
		$.ajax({
			type:"POST",
			url:"pt_saveorder.php",
			dataType:"html",
			data:"batchcode="+jkl+"&op="+op+"&customer_id="+customer_id,
				success:function(dota){
					if(dota == 1){
						alert('删除成功');
					}else{
						alert('删除失败');
					}
					document.location.href='pt_order.php?customer_id=<?php echo $customer_id;?>&num='+num;
				},
		});
	}
}

function show(jdk){
	var customer_id = "<?php echo $customer_id;?>";
	var cha_batchcode = "<?php echo $cha_batchcode;?>";
	var g_group_id = "<?php echo $g_group_id;?>";
	if(cha_batchcode != ''){
		return false;
	}
	if(g_group_id != ''){
		return false;
	}
	document.location.href = 'pt_order.php?customer_id='+customer_id+'&num='+jdk;
}

function cha(){
	var customer_id = "<?php echo $customer_id;?>";
	var cha_batchcode = document.getElementById('cha').value;
	document.location.href = 'pt_order.php?customer_id='+customer_id+'&cha_batchcode='+cha_batchcode;
}

</script>
</body>
</html>
<?php }?>
<?php mysql_close($link);?>