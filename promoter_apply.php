<?php
header("Content-type: text/html; charset=utf-8");    
session_cache_limiter( "private, must-revalidate" ); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
//头文件----start
require('../common/common_from.php');
//头文件----end
require('select_skin.php');
$level = 1;
if(!empty($_POST['level'])){
	$level = $configutil->splash_new($_POST['level']);
}

$qrsell_orderothers = '';	//推广员申请自定义
$sell_detail 		= '';	//推广员协议
$query = "select qrsell_orderothers,sell_detail from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('query failed'.mysql_error());
while($row = mysql_fetch_object($result)){
	$qrsell_orderothers = $row->qrsell_orderothers;
	$sell_detail 		= $row->sell_detail;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>推广员申请</title>
    <!-- 模板 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="no" name="apple-touch-fullscreen">
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no">
    <meta name=apple-mobile-web-app-capable content=yes>
    <meta name=apple-mobile-web-app-status-bar-style content=black>
    <meta http-equiv="pragma" content="nocache">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <!-- 模板 --> 
     
    <!-- 页联系style-->
    
    <!-- calendar --->
    <!-- jQuery Include -->
    <link href="./css/goods/mobiscroll/bootstrap.min.css" rel="stylesheet" type="text/css"> 
    <!-- Mobiscroll JS and CSS Includes -->
    <link href="./css/goods/mobiscroll/mobiscroll.custom-2.17.1.min.css" rel="stylesheet" type="text/css">
    <!-- calendar --->
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/goods/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/tuiguangyuanshenqing1-1.css" />
    <link type="text/css" rel="stylesheet" href="./css/self_dialog.css" />
	<link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />
	<link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" /> 
<style>
.redxing{color:red;vertical-align: middle;margin-right: 7px;}
</style>
    
</head>



<body data-ctrl=true class = "my-body">
	<!-- header部门-->
	<!-- <header data-am-widget="header" class="am-header am-header-default header" >
		<div class="am-header-left am-header-nav">
			<img class="am-header-icon-custom header-btn"  src="./images/center/nav_bar_back.png" /><span class = "header-btn" >返回</span>
		</div>
	    <h1 class="header-title" >推广员申请</h1>
	    <div class="am-header-right am-header-nav">
		</div>
	</header> --> <!-- 暂时隐藏 -->
	<!-- header部门-->
	
	<!-- content --->
	<input type="hidden" id="level" name="level" value="<?php echo $level;?>">
    <div id="containerDiv" class = "content" style="margin-top:0;">
    	<div class = "content-row" >
    		<div class = "content-row-remark">
    			<span>
    				<font class = "content-row-remark-font1"></font>
    				<font><span class="redxing">*</span>请输入您的姓名</font>
    			</span>
    		</div>
    		<div class = "content-row-input">
		    	<input class = "content-row-input-text"  id = "name" name="name" type="text" placeholder="请输入您的姓名">
		    </div>
		 </div>
		 
		 <div class = "content-row">
    		<div class = "content-row-remark" >
    			<span>
    				<font class = "content-row-remark-font1"></font>
    				<font><span class="redxing">*</span>请输入您的手机号码</font>
    			</span>
    		</div>
    		<div class = "content-row-input">
		    	<input class = "content-row-input-text" id = "phone" type="text"  name="phone" placeholder="请输入您的手机号码">
		    </div>
		 </div>
					<?php 
					   $oarr = explode(",",$qrsell_orderothers);
					   
					   $len = count($oarr);
					   $num = 1;
					   for($i=0;$i<$len;$i++){
					   
						  $item = $oarr[$i];
						  if(empty($item)){
							 continue;
						  }
						  $iarr = explode("_",$item);
						  
						  $type = $iarr[0];
						  if(empty($type)){
							 continue;
						  }
						  
						  $name = $iarr[1];
						  if(empty($name)){
							 continue;
						  }
						  $value = $iarr[2];
						  switch($type){
							 case 1:
					  ?>
						<div class = "content-row">
							<div class = "content-row-remark" >
									<span class="redxing">*</span>
									<font name="orderothers_font1" class = "content-row-remark-font2">
									<?php echo $name; ?>
									</font>
							</div>
							<div class = "content-row-input">
								<input type=hidden name="qrsell_orderothers_type" value="<?php echo $num;?>">
								<input class = "content-row-input-text" id = "diy_<?php echo $i; ?>" type="text"  name="orderothers" placeholder="<?php echo $value; ?>">
							</div>
						</div>
					  <?php
							break;
							 case 2:
					  ?>
						<div class = "content-row" >
							<div class = "content-row-remark">
									<span class="redxing">*</span>
									<font name="orderothers_font1" class = "content-row-remark-font2">
										<?php echo $name; ?>
									</font>
							</div>
							<div class = "content-row-input">
								<input type=hidden name="qrsell_orderothers_type" value="<?php echo $num;?>">
								<input class = "m_calendar content-row-input-text" id="diy_<?php echo $i; ?>" name="orderothers" type="text" placeholder="<?php echo $value; ?>" readonly >
							</div>
						 </div>
					  <?php 
						break;
					   case 3:
					  ?>
						<div class = "content-row">
							<div class = "content-row-remark">
								<span>
									<span class="redxing">*</span>
									<font  class = "content-row-remark-font2" name="orderothers_font1"><?php echo $name; ?></font>
									<input type=hidden name="qrsell_orderothers_type" value="<?php echo $num;?>">
								</span>
							</div>
							<div class = "content-row-input1">
								 <select  class = "content-select-box" name="orderothers" id="diy_<?php echo $i; ?>" style = "height:46px; line-height:46px; width:100%;padding:0px 10px;">
									<option value="-1">－－请选择－－</option>
									<?php 
									$opv = explode("|",$value);
									$len2 = count($opv);
									for($j=0;$j<$len2;$j++){
									$v = $opv[$j];
									?>
									<option value="<?php echo $v; ?>" ><?php echo $v; ?></option>
									<?php } ?>			        			        			        			        
								  </select>
							</div>
						</div>
					   <?php  break;
							}
							$num++;
						} 
						?>
					<input type=hidden id="qrsell_orderothers" value="<?php echo $qrsell_orderothers;?>" name="qrsell_orderothers" >
		 <div class = "content-row">
		    <div class = "content-row1">
			 	<span>*请务必填写正确，姓名与手机号将作为您以后颁奖的依据</span>
			 </div>
		 </div>
		 
		 <div class = "content-row2">
		 	<img class = "check-button" id = "check-btn"  src = "./images/goods_image/20160050304.png">
		    <span class = "content-row2-span" onclick="showTuiguangyuanMsg();"> 阅读推广员协议</span>
	     </div>
	     
	     <div class =  "shenqing-btn command-button" >
	     	<span>申请</span>
	     </div>
	     <div class =  "quxiao-btn command-button">
	     	<span>取消</span>
	     </div>
	</div>
	<!-- content --->
	
	<!--dialog-->
    <div class="am-share shangpin-dialog dlg">
   	  <!--dialog rect-->
   	  <div class = "close_button">
   	  	<img src = "./images/info_image/btn_close.png"  width = "30" height = "50">
   	  </div>	
   	  <!--<div class = "dlg-main">
   	  	<div class = "dlg-main-row1">
   	  		<img src = "./images/goods_image/20160050413.png" width = "100" height = "100">
   	  	</div>
   	  	<div class = "dlg-main-row2">
   			<span>您的申请成功</span>
   		</div>
   		<div class = "dlg-main-row3">
   			<span><font>理由:</font>您不符合推广员条件，请联系客服</span>
   		</div>
   		<div class =  "zhongxin-btn">
	     	<span>返回到推广员</span>
	    </div>
   	  </div>-->
   </div>
   
   <!--  推广员协议  -->
    <div id="promoter_agreement" style="display:none;"><?php echo $sell_detail;?></div>
	
    
</body>		
<script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
<script type="text/javascript" src="./assets/js/amazeui.js"></script>
<script type="text/javascript" src="./js/global.js"></script>
<script type="text/javascript" src="./js/loading.js"></script>
<script src="./js/jquery.ellipsis.js"></script>
<script src="./js/jquery.ellipsis.unobtrusive.js"></script>
<!-- jQuery Include -->
<script src="./js/goods/mobiscroll/jquery-1.11.1.min.js"></script>
<!-- Mobiscroll JS and CSS Includes -->
<script src="./js/goods/mobiscroll/mobiscroll.custom-2.17.1.min.js" type="text/javascript"></script>
<!-- 页联系js -->
<script src="./js/goods/global.js"></script>
<script src="./js/goods/promoter_apply.js"></script>
<script>
var customer_id    = <?php echo $customer_id;?>;
var customer_id_en = '<?php echo $customer_id_en;?>';
var user_id		   = <?php echo $user_id;?>;
	function viewRule(){
		var title = '推广员协议';
		var content = $('#promoter_agreement').text();
		$(".check-button").attr("src", "./images/goods_image/20160050305.png");
		$(".check-button").addClass("check-on");
		showDialogMsg(title,content);
	}
</script>
<!--引入微信分享文件----start-->
<script>
<!--微信分享页面参数----start-->
debug=false;
share_url=''; //分享链接
title=""; //标题
desc=""; //分享内容
imgUrl="";//分享LOGO
share_type=3;//自定义类型
<!--微信分享页面参数----end-->
</script>
<?php require('../common/share.php');?>
<!--引入微信分享文件----end-->
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</body>
</html>