<?php 
header("Content-type: text/html; charset=utf-8");
//require('../../../config.php');
//$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
//mysql_select_db(DB_NAME) or die('Could not select database');
//mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');

$shop['customer_id'] = $_POST['customer_id'];
$shop['pid'] = $_POST['pid'];
$shop['type'] = $_POST['type'];
$shop['name'] = $_POST['name'];
$shop['shelf'] = $_POST['shelf'];
$shop['status'] = $_POST['status'];
$shop['unit_price'] = $_POST['unit_price'];
$shop['group_price'] = $_POST['group_price'];
$shop['number'] = $_POST['number'];
$shop['sales'] = $_POST['sales'];
$shop['stock'] = $_POST['stock'];
$shop['units'] = $_POST['units'];
$shop['title'] = $_POST['title'];
$shop['details'] = $_POST['details'];

$shop['imgs'] = $_POST['imgs'];
$shop['imgs_one'] = $_POST['imgs_one'];
$shop['imgs_two'] = $_POST['imgs_two'];
$shop['imgs_three'] = $_POST['imgs_three'];
$shop['imgs_for'] = $_POST['imgs_for'];

$shop_arr = json_encode($shop);
$urls = "<script> {window.alert('上传失败');location.href='pt_addsale.php?shop=".$shop_arr."'} </script>";


if(is_uploaded_file(@$_FILES['img']['tmp_name'])){
$upfile=$_FILES["img"];//获取数组里面的值
$name=$upfile["name"];//上传文件的文件名
$type=$upfile["type"];//上传文件的类型
$size=$upfile["size"];//上传文件的大小
$tmp_name=$upfile["tmp_name"];//上传文件的临时存放路径

	switch ($type){//判断是否为图片
		case 'image/pjpeg':$okType=true;
		break;
		case 'image/jpeg':$okType=true;
		break;
		case 'image/gif':$okType=true;
		break;
		case 'image/png':$okType=true;
		break;
	}

	if($okType){
		/**
		* 0:文件上传成功<br/>
		* 1：超过了文件大小，在php.ini文件中设置<br/>
		* 2：超过了文件的大小MAX_FILE_SIZE选项指定的值<br/>
		* 3：文件只有部分被上传<br/>
		* 4：没有文件被上传<br/>
		* 5：上传文件大小为0
		*/
		$error=$upfile["error"];//上传后系统返回的值
		move_uploaded_file($tmp_name,"image/".$name);//把上传的临时文件移动到image目录下面
		$imgs="image/".$name;
		if($imgs!=''){
			$shop['imgs'] = $imgs;
		}
		$shop_arr = json_encode($shop);
		$url = "<script> {window.alert('上传成功');location.href='pt_addsale.php?shop=".$shop_arr."'} </script>";
		if($error==0){
			echo $url;
		}elseif ($error==1){
			echo $urls;
		}elseif ($error==2){
			echo $urls;
		}elseif ($error==3){
			echo $urls;
		}elseif ($error==4){
			echo $urls;
		}else{
			echo $urls;
		}
	}else{
		echo $urls;
	}

}elseif(is_uploaded_file(@$_FILES['img_one']['tmp_name'])){			//详情图片一

$upfile=$_FILES["img_one"];//获取数组里面的值
$name=$upfile["name"];//上传文件的文件名
$type=$upfile["type"];//上传文件的类型
$size=$upfile["size"];//上传文件的大小
$tmp_name=$upfile["tmp_name"];//上传文件的临时存放路径

//var_dump($shop);exit;
	switch ($type){//判断是否为图片
		case 'image/pjpeg':$okType=true;
		break;
		case 'image/jpeg':$okType=true;
		break;
		case 'image/gif':$okType=true;
		break;
		case 'image/png':$okType=true;
		break;
	}

	if($okType){
		/**
		* 0:文件上传成功<br/>
		* 1：超过了文件大小，在php.ini文件中设置<br/>
		* 2：超过了文件的大小MAX_FILE_SIZE选项指定的值<br/>
		* 3：文件只有部分被上传<br/>
		* 4：没有文件被上传<br/>
		* 5：上传文件大小为0
		*/
		$error=$upfile["error"];//上传后系统返回的值
		move_uploaded_file($tmp_name,"image/".$name);//把上传的临时文件移动到image目录下面
		$img_one="image/".$name;
		if($img_one!=''){
			$shop['imgs_one'] = $img_one;
		}
		$shop_arr = json_encode($shop);
		$url = "<script> {window.alert('上传成功');location.href='pt_addsale.php?shop=".$shop_arr."'} </script>";
		if($error==0){
			echo $url;
		}elseif ($error==1){
			echo $urls;
		}elseif ($error==2){
			echo $urls;
		}elseif ($error==3){
			echo $urls;
		}elseif ($error==4){
			echo $urls;
		}else{
			echo $urls;
		}
	}else{
		echo $urls;
	}
}elseif(is_uploaded_file(@$_FILES['img_two']['tmp_name'])){			//详情图片二

$upfile=$_FILES["img_two"];//获取数组里面的值
$name=$upfile["name"];//上传文件的文件名
$type=$upfile["type"];//上传文件的类型
$size=$upfile["size"];//上传文件的大小
$tmp_name=$upfile["tmp_name"];//上传文件的临时存放路径

	switch ($type){//判断是否为图片
		case 'image/pjpeg':$okType=true;
		break;
		case 'image/jpeg':$okType=true;
		break;
		case 'image/gif':$okType=true;
		break;
		case 'image/png':$okType=true;
		break;
	}

	if($okType){
		/**
		* 0:文件上传成功<br/>
		* 1：超过了文件大小，在php.ini文件中设置<br/>
		* 2：超过了文件的大小MAX_FILE_SIZE选项指定的值<br/>
		* 3：文件只有部分被上传<br/>
		* 4：没有文件被上传<br/>
		* 5：上传文件大小为0
		*/
		$error=$upfile["error"];//上传后系统返回的值
		move_uploaded_file($tmp_name,"image/".$name);//把上传的临时文件移动到image目录下面
		$img_two="image/".$name;
		if($img_two!=''){
			$shop['imgs_two'] = $img_two;
		}
		$shop_arr = json_encode($shop);
		$url = "<script> {window.alert('上传成功');location.href='pt_addsale.php?shop=".$shop_arr."'} </script>";
		if($error==0){
			echo $url;
		}elseif ($error==1){
			echo $urls;
		}elseif ($error==2){
			echo $urls;
		}elseif ($error==3){
			echo $urls;
		}elseif ($error==4){
			echo $urls;
		}else{
			echo $urls;
		}
	}else{
		echo $urls;
	}
}elseif(is_uploaded_file(@$_FILES['img_three']['tmp_name'])){			//详情图片三

$upfile=$_FILES["img_three"];//获取数组里面的值
$name=$upfile["name"];//上传文件的文件名
$type=$upfile["type"];//上传文件的类型
$size=$upfile["size"];//上传文件的大小
$tmp_name=$upfile["tmp_name"];//上传文件的临时存放路径

	switch ($type){//判断是否为图片
		case 'image/pjpeg':$okType=true;
		break;
		case 'image/jpeg':$okType=true;
		break;
		case 'image/gif':$okType=true;
		break;
		case 'image/png':$okType=true;
		break;
	}

	if($okType){
		/**
		* 0:文件上传成功<br/>
		* 1：超过了文件大小，在php.ini文件中设置<br/>
		* 2：超过了文件的大小MAX_FILE_SIZE选项指定的值<br/>
		* 3：文件只有部分被上传<br/>
		* 4：没有文件被上传<br/>
		* 5：上传文件大小为0
		*/
		$error=$upfile["error"];//上传后系统返回的值
		move_uploaded_file($tmp_name,"image/".$name);//把上传的临时文件移动到image目录下面
		$img_three="image/".$name;
		if($img_three!=''){
			$shop['imgs_three'] = $img_three;
		}
		$shop_arr = json_encode($shop);
		$url = "<script> {window.alert('上传成功');location.href='pt_addsale.php?shop=".$shop_arr."'} </script>";
		if($error==0){
			echo $url;
		}elseif ($error==1){
			echo $urls;
		}elseif ($error==2){
			echo $urls;
		}elseif ($error==3){
			echo $urls;
		}elseif ($error==4){
			echo $urls;
		}else{
			echo $urls;
		}
	}else{
		echo $urls;
	}
}elseif(is_uploaded_file(@$_FILES['img_for']['tmp_name'])){			//详情图片四

$upfile=$_FILES["img_for"];//获取数组里面的值
$name=$upfile["name"];//上传文件的文件名
$type=$upfile["type"];//上传文件的类型
$size=$upfile["size"];//上传文件的大小
$tmp_name=$upfile["tmp_name"];//上传文件的临时存放路径

	switch ($type){//判断是否为图片
		case 'image/pjpeg':$okType=true;
		break;
		case 'image/jpeg':$okType=true;
		break;
		case 'image/gif':$okType=true;
		break;
		case 'image/png':$okType=true;
		break;
	}

	if($okType){
		/**
		* 0:文件上传成功<br/>
		* 1：超过了文件大小，在php.ini文件中设置<br/>
		* 2：超过了文件的大小MAX_FILE_SIZE选项指定的值<br/>
		* 3：文件只有部分被上传<br/>
		* 4：没有文件被上传<br/>
		* 5：上传文件大小为0
		*/
		$error=$upfile["error"];//上传后系统返回的值
		move_uploaded_file($tmp_name,"image/".$name);//把上传的临时文件移动到image目录下面
		$img_for="image/".$name;
		if($img_for!=''){
			$shop['imgs_for'] = $img_for;
		}
		$shop_arr = json_encode($shop);
		$url = "<script> {window.alert('上传成功');location.href='pt_addsale.php?shop=".$shop_arr."'} </script>";
		if($error==0){
			echo $url;
		}elseif ($error==1){
			echo $urls;
		}elseif ($error==2){
			echo $urls;
		}elseif ($error==3){
			echo $urls;
		}elseif ($error==4){
			echo $urls;
		}else{
			echo $urls;
		}
	}else{
		echo $urls;
	}
}else{
	echo $urls;
}






?>


