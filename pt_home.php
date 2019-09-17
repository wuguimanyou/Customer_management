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

$query = "select * from weixin_commonshop_pt_home where customer_id=".$customer_id;
$row = mysql_query($query);

?>

<html>
<head>
<meta charset="utf-8">
<title>拼团首页</title>
<link rel="stylesheet" href="table.css">
</head>
<body style="background-color:white">
<table style="margin-top:30px;width:90%;" border="1px">

<tr style="width:20%;">
	<th>名称</th>
	<th>链接产品</th>
	<th>图片</th>
	<th>操作</th>
</tr>

<?php
	while($rod = mysql_fetch_object($row)){
		$id = $rod->id;
		$pid = $rod->pid;
		$name = $rod->name;
		$pname = $rod->pname;
		$imgurl = $rod->imgurl;
?>

<tr>
	<td><?php echo $name;?></td>
	<td><?php echo $pname;?></td>
	<td><img src="<?php echo $imgurl;?>" width="80px" height="70px"></td>
	<td><a href="pt_addhome.php?id=<?php echo $id;?>"><input type="button" value="修改"></a></td>
</tr>
<?php }?>
</table>
</body>
</html>
<?php }?>
<?php mysql_close($link);?>