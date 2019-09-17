<?php 
header("Content-type: text/html; charset=utf-8");
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');

if(!empty($_GET['customer_id'])){
	$customer_id = $_GET['customer_id'];
}
$type = $_GET['type'];
$pid = $_GET['pid'];

if($type=='edit'){
	$query = "select * from weixin_commonshop_group_products where isvalid=true and id=".$pid." and customer_id=".$customer_id;
	$row = mysql_query($query);
	$rod = mysql_fetch_object($row);
		$name = $rod->name;
		$unit_price = $rod->unit_price;
		$group_price = $rod->group_price;
		$shelf = $rod->shelf;
		$number = $rod->number;
		$status = $rod->status;
		$sales = $rod->sales;
		$stock = $rod->stock;
		$img = $rod->img;
		$title = $rod->title;
		$details = $rod->details;
		$units = $rod->units;
		$img_one = $rod->img_one;
		$img_two = $rod->img_two;
		$img_three = $rod->img_three;
		$img_for = $rod->img_for;
}

if(!empty($_GET['shop'])){
$shop = $_GET['shop'];
$shop = json_decode($shop);
	$name = $shop->name;
	$shelf = $shop->shelf;
	$status = $shop->status;
	$unit_price = $shop->unit_price;
	$group_price = $shop->group_price;
	$number = $shop->number;
	$sales = $shop->sales;
	$stock = $shop->stock;
	$units = $shop->units;
	$title = $shop->title;
	$details = $shop->details;
	$destination = $shop->destination;
	$type = $shop->type;
	$customer_id = $shop->customer_id;
	$pid = $shop->pid;
	$img = $shop->imgs;
	$img_one = $shop->imgs_one;
	$img_two = $shop->imgs_two;
	$img_three = $shop->imgs_three;
	$img_for = $shop->imgs_for;
}


?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>添加商品</title>
<style>
*{margin:0;padding:0;}
body{margin-left:30px;margin-top:20px;}
div{margin-bottom:10px;}
.zg
{
	
	font-family: "微软雅黑";
	font-size: 18px;
	font-weight: bold;
	
}
 
input{border-radius: 4px;outline:none;padding:3px 6px;margin:3px}
.zgz:hover{color:white;background-color:#565454;border:none;}
</style>

</head>
<body style="background-color:white">
<div class="zg">
<form action="pt_imgsale.php" method="POST" enctype="multipart/form-data">
<input type="hidden" id="customer_id" name="customer_id" value="<?php echo $customer_id;?>">
<input type="hidden" id="type" name="type" value="<?php echo $type;?>">

<div><p>商品名称　<input type="text" autocomplete="off" id="name" name="name" value="<?php echo $name;?>"></p></div>
<div><p>上架/下架 <span>上架</span><input type="radio" id="shelf" name="shelf" value="1" <?php if($shelf==1){echo 'checked';}?>><span>下架</span><input type="radio" name="shelf" id="shelf" value="2" <?php if($shelf==2){echo 'checked';}?>></p></div>
<div><p>拼团模式　<span>开启</span><input type="radio" id="status" name="status" value="1" <?php if($status==1){echo 'checked';}?>><span>关闭</span><input type="radio" name="status" id="status" value="2" <?php if($status==2){echo 'checked';}?>></p></div>
<div><p>单买价格　<input type="text" autocomplete="off" id="unit_price" name="unit_price" value="<?php echo $unit_price;?>"></p></div>
<div><p>拼团价格　<input type="text" autocomplete="off" id="group_price" name="group_price" value="<?php echo $group_price;?>"><span>　(若关闭拼团模式,则请输入0)</span></p></div>
<div><p>拼团人数　<input type="text" autocomplete="off" id="number" name="number" value="<?php echo $number;?>"><span>　(若关闭拼团模式,则请输入0)</span></p></div>
<div><p>销　　量　<input type="text" autocomplete="off" id="sales" name="sales" value="<?php echo $sales;?>"></p></div>
<div><p>库　　存　<input type="text" autocomplete="off" id="stock" name="stock" value="<?php echo $stock;?>"></p></div>
<div><p>单　　位　<input type="text" autocomplete="off" id="units" name="units" value="<?php echo $units;?>"></p></div>
<div><p>商品标题　<input type="text" autocomplete="off" id="title" name="title" value="<?php echo $title;?>"></p></div>
<div><p>商品详情　<input type="text" autocomplete="off" id="details" name="details" value="<?php echo $details;?>"></p></div>
<input type="hidden" id="pid" name="pid" value="<?php echo $pid;?>">
<div><p>商品图片　<input type="file" name="img"></p></div>
<div><p>详情图片一	<input type="file" name="img_one"></p></div>
<div><p>详情图片二	<input type="file" name="img_two"></p></div>
<div><p>详情图片三	<input type="file" name="img_three"></p></div>
<div><p>详情图片四	<input type="file" name="img_for"></p></div>
<input type="hidden" name="imgs" value="<?php echo $img;?>">
<input type="hidden" name="imgs_one" value="<?php echo $img_one;?>">
<input type="hidden" name="imgs_two" value="<?php echo $img_two;?>">
<input type="hidden" name="imgs_three" value="<?php echo $img_three;?>">
<input type="hidden" name="imgs_for" value="<?php echo $img_for;?>">
<input class="zgz" type="submit" value="上传">
</form>
<div border="1px" width="800px" height="600px">
<img src="<?php echo $img;?>">
</div>
<div border="1px">
<span><img src="<?php echo $img_one;?>" width="300px" height="200px"></span>
<span><img src="<?php echo $img_two;?>" width="300px" height="200px"></span>
<span><img src="<?php echo $img_three;?>" width="300px" height="200px"></span>
<span><img src="<?php echo $img_for;?>" width="300px" height="200px"></span>
</div>
<input class="zgz" type="button" value="保存" onclick="show()">
</div>
<script type="text/javascript" src="jquery_tk.js"></script>
<script type="text/javascript">
function show(){
var customer_id = "<?php echo $customer_id;?>";
var pid = "<?php echo $pid;?>";
var name = document.getElementById('name').value;
//var shelf = document.getElementById('shelf').value;
//var status = document.getElementById('status').value;
var shelf = $("input[name='shelf']:checked").val(); 
var status = $("input[name='status']:checked").val();
var unit_price = document.getElementById('unit_price').value;
var group_price = document.getElementById('group_price').value;
var number = document.getElementById('number').value;
var sales = document.getElementById('sales').value;
var stock = document.getElementById('stock').value;
var units = document.getElementById('units').value;
var title = document.getElementById('title').value;
var details = document.getElementById('details').value;
var img = "<?php echo $img;?>";
var type = "<?php echo $type;?>";
var img_one = "<?php echo $img_one;?>";
var img_two = "<?php echo $img_two;?>";
var img_three = "<?php echo $img_three;?>";
var img_for = "<?php echo $img_for;?>";

if(name==''){
	alert('请输入名称');
	return false;
}
if(shelf==''){
	alert('请选择上架或者下架');
	return false;
}
if(status==''){
	alert('请选择开启或者关闭拼团模式');
	return false;
}
if(unit_price==''){
	alert('请输入单买价格');
	return false;
}
if(group_price==''){
	alert('请输入拼团价格');
	return false;
}
if(number==''){
	alert('请输入拼团人数');
	return false;
}
if(sales==''){
	alert('请输入销量');
	return false;
}
if(stock==''){
	alert('请输入库存');
	return false;
}
if(title==''){
	alert('请输入标题');
	return false;
}
if(details==''){
	alert('请输入详情');
	return false;
}
if(img==''){
	alert('请上传图片');
	return false;
}
if(type == 'edit'){
	$.ajax({
	type:"POST",
	url:"pt_savesale.php",
	dataType:"html",
	data: "customer_id="+customer_id+"&pid="+pid+"&name="+name+"&shelf="+shelf+"&status="+status+"&unit_price="+unit_price+"&group_price="+group_price
	+"&number="+number+"&sales="+sales+"&stock="+stock+"&units="+units+"&title="+title+"&details="+details+"&img="+img+"&type="+type+"&img_one="+img_one+"&img_two="+img_two+"&img_three="+img_three+"&img_for="+img_for,

		success:function(msg){   
			if(msg==1){
				alert('修改成功');
				document.location.href='pt_sale.php?customer_id=<?php echo $customer_id;?>';
			}else{
				alert(msg);
			}
		},
	});
}else if(type == 'add'){
	$.ajax({
	type:"POST",
	url:"pt_savesale.php",
	dataType:"html",
	data: "customer_id="+customer_id+"&name="+name+"&shelf="+shelf+"&status="+status+"&unit_price="+unit_price+"&group_price="+group_price
	+"&number="+number+"&sales="+sales+"&stock="+stock+"&units="+units+"&title="+title+"&details="+details+"&img="+img+"&type="+type+"&img_one="+img_one+"&img_two="+img_two+"&img_three="+img_three+"&img_for="+img_for,

		success:function(msg){
			if(msg==1){
				alert('添加成功');
				document.location.href='pt_sale.php?customer_id=<?php echo $customer_id;?>';
			}else{
				alert('添加失败');
			}
		},
	});
}

}
</script>
</body>
</html>






<?php
mysql_close($link);
?>

