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


$querys = "select count(1) as count from weixin_commonshop_group_products where customer_id=".$customer_id;
$rows = mysql_query($querys);
$rods = mysql_fetch_object($rows);
	$count = $rods->count;
	$page = ceil($count/20);
	$num = 1;
	if(@$_GET['num']){
		$num = $_GET['num'];
	}
	$start = ($num-1)*20;
	$end = 20;

?>
<html>
<head>
<meta charset="utf-8">
<title>商品管理</title>
<link rel="stylesheet" href="table.css">
</head>
<body style="background-color:white">
	<div class="zg">
	<a href="pt_addsale.php?type=add&customer_id=<?php echo $customer_id;?>"><input type="button" value="添加" style="background-color:rgb(38,78,128);border: rgb(38,78,128);color:white;margin:10px;width:50px;height:30px;font-size:14px;font-weight:bold;"></a>
	<table  cellspacing="1" class="FindAreaTable1" ID="DataGrid1">
		<tr style="word-break: keep-all;">
			<th>序号</th>
			<th>名称</th>
			<th>上架/下架</th>
			<th>单买价格</th>
			<th>拼团模式</th>
			<th>拼团(价格/人数)</th>
			<th>销量</th>
			<th>库存</th>
			<th>图片</th>
			<th>时间</th>
			<th>操作</th>
		</tr>		  
		<?php
			$query = "select * from weixin_commonshop_group_products where isvalid=true and customer_id=".$customer_id." order by id desc limit ".$start.",".$end;
			$row = mysql_query($query);
			while($rod = mysql_fetch_object($row)){
				$pid = $rod->id;
				$name = $rod->name;
				$shelf = $rod->shelf;
				$status = $rod->status;
				$number = $rod->number;
				$unit_price = $rod->unit_price;
				$group_price = $rod->group_price;
				$sales = $rod->sales;
				$stock = $rod->stock;
				$img = $rod->img;
				$createtime = $rod->createtime;
				?>  
		<tr>
			<td><?php echo $pid;?><input type="hidden" name="pid" id="pid" value="<?php echo $pid;?>"></td>
			<td><?php echo $name;?></td>
			<td><?php if($shelf==1){echo '已上架';}else{echo '已下架';}?><span><input type="button" value="<?php if($shelf==1){echo '下架';}else{echo '上架';}?>" onclick="show(pid='<?php echo $pid;?>',shelf='<?php echo $shelf;?>')"></span></td>
			<td><?php echo '￥'.$unit_price;?></td>
			<td><?php if($status==1){echo '已开启';}else{echo '已关闭';}?><span><input type="button" value="<?php if($status==1){echo '关闭';}else{echo '开启';}?>" onclick="shod(pid='<?php echo $pid;?>',status='<?php echo $status;?>')"></span></td>
			<td><?php echo '￥'.$group_price;?>/<span><?php echo $number;?></span></td>
			<td><?php echo $sales;?></td>
			<td><?php echo $stock;?></td>
			<td><img src="<?php echo $img;?>" width="80px" height="70px"></td>
			<td><?php echo $createtime;?></td>
			<td class="tu">
				<a title="修改" href="pt_addsale.php?pid=<?php echo $pid;?>&customer_id=<?php echo $customer_id;?>&type=edit"><img src="icon52.png"></a>&nbsp;
				<a title="删除" href="#" onclick="del(pid='<?php echo $pid;?>')"><img src="icon04.png"></a>
			</td>
	   </tr>
	   <?php } ?>
		
	</table>

<!--翻页开始-->
<div>
<?php

for($pagenum=1;$pagenum<=$page;$pagenum++){

?>
<!--<span><a href="page.php?num=<?php echo $pagenum;?>"><?php echo $pagenum;?></a></span>-->
<span><input type="button" value="<?php echo $pagenum;?>" onclick="shof(<?php echo $pagenum;?>)" style="background-color:rgb(38,78,128);color:white;outline:none;border: none;margin:10px;width:50px;height:30px;font-size:14px;font-weight:bold;border-radius:100%;"></span>
<?php } ?>
</div>
<!--翻页结束-->
</div>
<!--内容框架结束-->
<script type="text/javascript" src="jquery_tk.js"></script>
<script type="text/javascript">
function show(obj,shelf){
if(shelf==1){
	if(confirm('确定要下架吗？')){
		$.ajax({
			type:"POST",
			url:"pt_savesale.php",
			dataType:"html",
			data:"pid="+pid+"&op=xj",
				success:function(dota){
					if(dota==1){
						alert('下架成功');
						document.location.href='pt_sale.php?customer_id=<?php echo $customer_id;?>';
					}else{
						alert('下架失败');
					}
				},
		});
	}
}else{
	if(confirm('确定要上架吗？')){
		$.ajax({
			type:"POST",
			url:"pt_savesale.php",
			dataType:"html",
			data:"pid="+pid+"&op=sj",
				success:function(dota){
					if(dota==1){
						alert('上架成功');
						document.location.href='pt_sale.php?customer_id=<?php echo $customer_id;?>';
					}else{
						alert('上架失败');
					}	
				},
		});
	}
}

}

function shod(pid,status){
if(status==1){
	if(confirm('确定关闭拼团模式吗？')){
		$.ajax({
		type:"POST",
		url:"pt_savesale.php",
		dataType:"html",
		data:"pid="+pid+"&dp=close",
			success:function(dota){
				if(dota==1){
					alert('已关闭');
					document.location.href='pt_sale.php?customer_id=<?php echo $customer_id;?>';
					style.color = red;
				}else{
					alert('关闭失败');
				}
			},
		});
	}

}else{
	if(confirm('确定开启拼团模式吗？')){
		$.ajax({
		type:"POST",
		url:"pt_savesale.php",
		dataType:"html",
		data:"pid="+pid+"&dp=open",
			success:function(dota){
				if(dota==1){
					alert('已开启');
					document.location.href='pt_sale.php?customer_id=<?php echo $customer_id;?>';
				}else{
					alert('开启失败');
				}	
			},
		});
	}
}
}

function del(pid){
if(confirm('确定要删除吗？')){
	$.ajax({
		type:"POST",
		url:"pt_savesale.php",
		dataType:"html",
		data:"pid="+pid+"&type=del",
			success:function(jdk){
				if(jdk==1){
					alert('删除成功');
					document.location.href='pt_sale.php?customer_id=<?php echo $customer_id;?>';
				}else{
					alert('删除失败');
				}
			},
	});
}
}

function shof(jdk){
	var customer_id = "<?php echo $customer_id;?>";
	var cha_batchcode = "<?php echo $cha_batchcode;?>";
	document.location.href = 'pt_sale.php?customer_id='+customer_id+'&num='+jdk;
}

</script>
</body>
</html>
<?php }?>
<?php mysql_close($link);?>