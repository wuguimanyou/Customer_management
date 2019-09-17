<?php 
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('prc');

$arr['customer_id'] = $_POST['customer_id'];
$arr['home_pid'] = $_POST['home_pid'];
$arr['home_id'] = $_POST['home_id'];
$arr['home_name'] = $_POST['home_name'];


$array = json_encode($arr);
$urls = "<script> {window.alert('上传失败');location.href='pt_addhome.php?home=".$array."'} </script>";

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

		$error=$upfile["error"];

		move_uploaded_file($tmp_name,"image/".$name);
		$destination="image/".$name;

		if($error==0){ 
			$url = "<script> {window.alert('上传成功');location.href='pt_addhome.php?home=".$array."&destination=".$destination."'} </script>";
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


