<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility.php');
$customer_id = $_SESSION['customer_id'];
$user_id = $_SESSION['user_id_'.$customer_id];

//echo $customer_id."==".$user_id;

$type 		 = $configutil->splash_new($_POST['type']);

$id 		 = $configutil->splash_new($_POST['id']);
if( $id == '' && $type == 'edit'){
	echo '<script type="text/javascript">history.go(-1);</script>';
	return;
}

$name        = 	'';//收货人名字
$phone       = 	'';//联系电话
$address     = 	'';//自定义街道等信息
$identity    = 	'';//身份证号码
$location_p  = 	'';//省
$location_c  = 	'';//市
$location_a  = 	'';//镇区
$is_default  = 	 0;//是否默认

$name        = 	$configutil->splash_new($_POST["name"]);	
$phone       = 	$configutil->splash_new($_POST["phone"]);
$identity    = 	$configutil->splash_new($_POST["identity"]);
$address     = 	$configutil->splash_new($_POST["address"]);
$location_p  = 	$configutil->splash_new($_POST["location_p"]);//国家
$location_c  = 	$configutil->splash_new($_POST["location_c"]);//城市
$location_a  = 	$configutil->splash_new($_POST["location_a"]);//镇区
$is_default  = 	$configutil->splash_new($_POST["default"]);//是否默认
$identityimg =  $configutil->splash_new($_POST["Filedata_"]);//
// $identityimgt=  $configutil->splash_new($_POST["img1"]);//
// $identityimgf=  $configutil->splash_new($_POST["img2"]);//


//echo $name."==".$phone."==".$identity."==".$address."==".$location_p."==".$location_c."==".$location_a."==".$is_default;die;

$img_url	= '../up/'.$customer_id.'/'.$user_id;
if(!is_dir($img_url))
	{
		mkdir($img_url, 0755, true);
	}

if(!empty($_FILES['Filedata_']['name'][0]) && !empty($_FILES['Filedata_']['name'][1])){
	//echo '11';die;
	foreach ($_FILES["Filedata_"]["name"] as $key => $val) {
	        
	        	$exten = strtolower( pathinfo($_FILES['Filedata_']['name'][$key], PATHINFO_EXTENSION) ); //后缀
	        	//echo $exten;die;
	            $tmp_name = $_FILES["Filedata_"]["tmp_name"][$key];//旧文件名
	            $newname = $user_id.time().$key.'.'.$exten;
	            $new_url = $img_url.'/'.$newname;
	            move_uploaded_file($tmp_name, $new_url);

	            if($key == 0 && !empty($_FILES['Filedata_']['name'][0])){
	            	$identityimgt = $new_url;//身份证正面路径
	            }
	            if($key == 1 && !empty($_FILES['Filedata_']['name'][1])){
	            	$identityimgf = $new_url;//身份证背面路径
	            }

	        }
}


switch ($type) {
	case 'edit':	//修改
		if($is_default==1){
			$query = "UPDATE weixin_commonshop_addresses set is_default=0 WHERE user_id=".$user_id; 	//为了保证只有一个默认地址，先把所有该user的地址默认值全部设为0 
			mysql_query($query)or die('Query failed39: ' . mysql_error());
		}
		if(!empty($_FILES['Filedata_']['name'][0]) && !empty($_FILES['Filedata_']['name'][1])){
			$query = "UPDATE weixin_commonshop_addresses SET name='$name',phone='$phone',identity='$identity',address='$address',location_p='$location_p',location_c='$location_c',location_a='$location_a',is_default='$is_default',identityimgt='$identityimgt',identityimgf='$identityimgf' WHERE id=".$id;
		}else{
			$query = "UPDATE weixin_commonshop_addresses SET name='$name',phone='$phone',identity='$identity',address='$address',location_p='$location_p',location_c='$location_c',location_a='$location_a',is_default='$is_default' WHERE id=".$id;
		}
		
		//echo $query;die;
		mysql_query($query) or die('Query failed39: ' . mysql_error());  

		echo "<script>window.location.href='my_address.php?customer_id=".$customer_id_en."&a_type=".$_SESSION['a_type_'.$user_id]."'</script>";

	break;
	
	case 'insert':	//新增
		if($is_default==1){
			$query = "UPDATE weixin_commonshop_addresses set is_default=0 WHERE user_id=".$user_id;	//为了保证只有一个默认地址，先把所有该user的地址默认值全部设为0 
			mysql_query($query)or die('Query failed39: ' . mysql_error());
		}
		$query = "INSERT INTO weixin_commonshop_addresses(user_id,address,isvalid,name,phone,identity,location_p,location_c,location_a,is_default,identityimgt,identityimgf) VALUES('$user_id','$address',true,'$name','$phone','$identity','$location_p','$location_c','$location_a','$is_default','$identityimgt','$identityimgf')";
		// file_put_contents ( "log1106.txt", "isnew====".var_export ( $query, true ) . "\r\n", FILE_APPEND );
		// echo $query;die;
		mysql_query($query)or die('Query failed39: ' . mysql_error()); 
		echo '<script>history.go(-2);</script>';
		

	break;

	case 'savedefault'://设为默认
		$query = "UPDATE weixin_commonshop_addresses set is_default=0 WHERE user_id=".$user_id;//为了保证只有一个默认地址，先把所有该user的地址默认值全部设为0 
		mysql_query($query)or die('Query failed39: ' . mysql_error());
		$query2 = "UPDATE weixin_commonshop_addresses set is_default=1 WHERE id=".$id;
		mysql_query($query2)or die('Query failed39: ' . mysql_error());
		echo json_encode('ok');

	break;

	case 'wxAdress':	//获取微信收货地址
		$query = "UPDATE weixin_commonshop_addresses set is_default=0 WHERE user_id=".$user_id;//为了保证只有一个默认地址，先把所有该user的地址默认值全部设为0
		mysql_query($query)or die('Query failed67: ' . mysql_error());
		$query = "INSERT INTO weixin_commonshop_addresses(user_id,address,isvalid,name,phone,identity,location_p,location_c,location_a,is_default) VALUES('$user_id','$address',true,'$name','$phone','$identity','$location_p','$location_c','$location_a',1)";
		mysql_query($query)or die('Query failed69: ' . mysql_error());
		//echo '<script>history.go(-1);</script>';
		echo 'ok';


	break;


	
}






?>