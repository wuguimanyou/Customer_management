<?php 
header("Content-type: text/html; charset=utf-8");
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');

if(!empty($_GET['customer_id'])){
	$customer_id=$configutil->splash_new($_GET['customer_id']);
	$customer_id = passport_decrypt($customer_id);

if(!empty($_GET['sou_group_id'])){
	$sou_group_id = $_GET['sou_group_id'];
}else{
	$sou_group_id = '';
}

if($sou_group_id != ''){
	$querys= "select count(1) as count from weixin_commonshop_group where isvalid=true and customer_id=".$customer_id." and id=".$sou_group_id;
}else{
	$querys = "select count(1) as count from weixin_commonshop_group where isvalid=true and customer_id=".$customer_id;
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

if($sou_group_id != ''){
	$query = "select * from weixin_commonshop_group where isvalid=true and customer_id=".$customer_id." and id=".$sou_group_id." order by id desc limit ".$start.",".$end;
}else{
	$query = "select * from weixin_commonshop_group where isvalid=true and customer_id=".$customer_id." order by id desc limit ".$start.",".$end;
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
	<span><a href="pt_order.php?customer_id=<?php echo $customer_id;?>"><input type="button" value="所有订单"></a></span>
	<span><a href="pt_team.php?customer_id=<?php echo $customer_id;?>"><input type="button" value="团队订单"></a></span>
	<input type="text" id="sou" value="<?php if($sou_group_id != ''){echo $sou_group_id;}?>"><input type="button" value="搜索团队" onclick="sou()">
</div>
<table border="1px">
<tr>
	<th>拼团编号</th>
	<th>拼团商品</th>
	<th>开团时间</th>
	<th>结束时间</th>
	<th>拼团人数</th>
	<th>现有人数</th>
	<th>团长信息</th>
	<th>团队发货状态</th>
	<th>团队订单状态</th>
	<th>操作</th>
</tr>
<?php 
while($rod = mysql_fetch_object($row)){
	$g_group_id = $rod->id;
	$g_pid = $rod->pid;
	$g_createtime = $rod->createtime;
	$g_overtime = $rod->overtime;
	$g_group_number = $rod->group_number;
	$g_existing_number = $rod->existing_number;
	$g_user_id = $rod->user_id;
	$g_status = $rod->status;
	$g_address_id = $rod->address_id;
	$g_sendstatus = $rod->sendstatus;

	$querys = "select * from weixin_commonshop_group_products where isvalid=true and id=".$g_pid." and customer_id=".$customer_id;
	$rows = mysql_query($querys);
	$rods = mysql_fetch_object($rows);
		$g_shop_name = $rods->name;					//商品名称

	$query5 = "select * from weixin_commonshop_addresses where isvalid=true and user_id=".$g_user_id;
	$row5 = mysql_query($query5);
	$rod5 = mysql_fetch_object($row5);
		$g_name = $rod5->name;								//团长 收货名称
		$g_location_p = $rod5->location_p;					//团长 收货地址 省
		$g_location_c = $rod5->location_c;					//团战 收货地址 市
		$g_phone = $rod5->phone;
		switch($g_location_p){
			case '北京市' : $g_address = $g_location_c;break;
			case '天津市' : $g_address = $g_location_c;break;
			case '重庆市' : $g_address = $g_location_c;break;
			case '上海市' : $g_address = $g_location_c;break;
			case '香港特别行政区' : $g_address = $g_location_c;break;
			case '澳门特别行政区' : $g_address = $g_location_c;break;
			case '内蒙古自治区' : $g_address = '内蒙古'.$g_location_c;break;
			case '西藏自治区' : $g_address = '西藏'.$g_location_c;break;
			case '广西壮族自治区' : $g_address = '广西'.$g_location_c;break;
			case '宁夏回族自治区' : $g_address = '宁夏'.$g_location_c;break;
			case '新疆维吾尔自治区' : $g_address = $g_location_c;break;
			default : $g_address = $g_location_p." ".$g_location_c;break;
		}

?>
<tr>
	<td><?php echo $g_group_id;?></td>
	<td><?php echo $g_shop_name;?></td>
	<td><?php echo $g_createtime;?></td>
	<td><?php echo $g_overtime;?></td>
	<td><?php echo $g_group_number;?></td>
	<td><?php echo $g_existing_number;?></td>
	<td><p><?php echo $g_name;?><p>
		<?php echo $g_address;?>
	</td>
	<td><?php 
			switch($g_sendstatus){
				case 1:echo '未发货';break;
				case 2:echo '已发货';break;
				case 3:echo '已收货';break;
				case 4:echo '已退款';break;
				case 5:echo '已退货';break;
			}
		?>	
	</td>
	<td><?php if($g_status==2){echo '已确认';}else{echo '未确认';}?></td>
	<td>
		<input type="button" id="cha" value="查看" onclick="cha(obj='<?php echo $g_group_id;?>')">
		<input type="button" id="del" value="删除订单" onclick="del(jkl='<?php echo $g_group_id;?>')">
	</td>
</tr>
<tr>
	<td>
</tr>
<?php }?>
</table>
<!--翻页开始-->
<div class="fanye">
<?php

for($pagenum=1;$pagenum<=$page;$pagenum++){
?>
<!--<span><a href="pt_order.php?num=<?php echo $pagenum;?>"><?php echo $pagenum;?></a></span>-->
<span><input type="button" id="num" value="<?php echo $pagenum;?>" onclick="show(<?php echo $pagenum;?>)"></span>
<?php } ?>
</div>
<!--翻页结束-->
<script type="text/javascript" src="jquery_tk.js"></script>
<script>
function show(jdk){
	var customer_id = "<?php echo $customer_id;?>";
	var sou_group_id = "<?php echo $sou_group_id;?>";
	if(sou_group_id != ''){
		return false;
	}
	document.location.href = 'pt_team.php?customer_id='+customer_id+'&num='+jdk;
}

function del(jkl){
	if(confirm('确定要删除吗?')){
		var op = 'del';
		var num = "<?php echo $num;?>";
		var customer_id = "<?php echo $customer_id;?>";
		$.ajax({
			type:"POST",
			url:"pt_saveteam.php",
			dataType:"html",
			data:"group_id="+jkl+"&op="+op+"&customer_id="+customer_id,
				success:function(dota){
					if(dota == 1){
						alert('删除成功');
					}else{
						alert(dota);
					}
					document.location.href='pt_team.php?customer_id=<?php echo $customer_id;?>&num='+num;
				},
		});
	}
}

function sou(){
	var customer_id = "<?php echo $customer_id;?>";
	var sou_group_id = document.getElementById('sou').value;
	document.location.href = 'pt_team.php?customer_id='+customer_id+'&sou_group_id='+sou_group_id;
}

function cha(obj){
	var customer_id = "<?php echo $customer_id;?>";
	document.location.href = 'pt_order.php?customer_id='+customer_id+'&g_group_id='+obj;
}

</script>
</body>
</html>

<?php }?>
<?php mysql_close($link);?>

