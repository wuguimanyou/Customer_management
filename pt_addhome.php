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
}
if(!empty($_GET['id'])){
	$home_id = $_GET['id'];
}
if(!empty($_GET['destination'])){
	$destination = $_GET['destination'];
}


$query = "select * from weixin_commonshop_pt_home where customer_id=".$customer_id." and id=".$home_id;
$row = mysql_query($query);
$rod = mysql_fetch_object($row);
	$home_pid = $rod->pid;
	$home_name = $rod->name;;
	$home_imgurl = $rod->imgurl;
	$customer_id = $rod->customer_id;

if(!empty($_GET['home'])){
	$home = $_GET['home'];
	$home = json_decode($home);
		$home_id = $home->home_id;
		$home_name = $home->home_name;
		$home_pid = $home->home_pid;
		$customer_id = $home->customer_id;
}

$query2 = "select id,name from weixin_commonshop_group_products where isvalid=true and customer_id=".$customer_id;
$row2 = mysql_query($query2);

?>

<html>
<head>
<meta charset="utf-8">
<title></title>
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
<body style="background-color:white;margin:3%;" >
<form action="pt_imghome.php" method="POST" enctype="multipart/form-data">
<tr>
	<input type="hidden" id="customer_id" name="customer_id" value="<?php echo $customer_id;?>">
	<input type="hidden" id="home_id" name="home_id" value="<?php echo $home_id;?>">
	<td>名称:<span><input type="text" name="home_name" value="<?php echo $home_name;?>" readonly></span></td>
	<td>链接产品:
		<span><select name="home_pid" id="home_pid">
		<?php while($rod2 = mysql_fetch_object($row2)){ 
			$shop_id = $rod2->id;
			$shop_name = $rod2->name;
		?>
		  <option value="<?php echo $shop_id;?>" <?php if($home_pid==$shop_id){echo 'selected = "selected"';}?>><?php echo $shop_name;?></option>
		<?php }?>
		</select></span>
	</td>
	<td>图片:
		<input class="zgz" type="submit" value="上传"><input type="file" name="img">
	</td>
</tr>
</form>
	<td>
		<div border="1px" width="800px" height="600px">
		<?php if($destination){ ?>
		<img src="<?php echo $destination;?>">
		<?php }elseif($home_imgurl){ ?>
		<img src="<?php echo $home_imgurl;?>">
		<?php }?>
		</div>
	</td>
</form>
<input class="zgz" type="button" onclick="show()" value="保存">
<script type="text/javascript" src="jquery_tk.js"></script>
<script>
function show(){
	<?php if($destination){ ?>
		var home_imgurl = "<?php echo $destination;?>";
	<?php }else{ ?>
		var home_imgurl = "<?php echo $home_imgurl;?>";
	<?php }?>
	var home_id = "<?php echo $home_id;?>";
	var home_name = "<?php echo $home_name;?>";
	var home_pid = document.getElementById('home_pid').value;
	var customer_id = "<?php echo $customer_id?>";
	$.ajax({
		type:"POST",
		url:"pt_savehome.php",
		dataType:"html",
		data:'customer_id='+customer_id+'&home_id='+home_id+'&home_pid='+home_pid+"&home_imgurl="+home_imgurl,
		success:function(msg){
			if(msg==1){
				alert('保存成功');
				document.location.href = 'pt_home.php?customer_id=<?php echo $customer_id;?>';
			}else{
				alert('保存失败');
			}
		}
	});
	//document.location.href = 'pt_savehome.php?customer_id='+customer_id+'&home_id='+home_id+'&home_pid='+home_pid+"&home_imgurl="+home_imgurl;
}

</script>
</body>
</html>
<?php mysql_close($link);?>