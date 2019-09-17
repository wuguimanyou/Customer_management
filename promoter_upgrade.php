<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
require('../common/utility.php');
/*require('../common/jssdk.php');
$jssdk = new JSSDK($customer_id);
$signPackage = $jssdk->GetSignPackage();*/
//头文件----start
require('../common/common_from.php');
//头文件----end
require('select_skin.php');
$level_arry = array("","一级","二级","三级","四级","五级","六级","七级","八级");

$sell_detail = '';	//推广员协议
$query = "select sell_detail from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('query failed'.mysql_error());
while($row = mysql_fetch_object($result)){
	$sell_detail = $row->sell_detail;
}

$query2 = "select id,status,commision_level from promoters where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id;
$result2 = mysql_query($query2) or die('query failed2'.mysql_error());
$status		 	 = -1;	//推广员状态 1:审核通过 0:审核中
$promoter_id 	 = -1;	//推广员ID
$commision_level =  1;	//推广员等级
while($row2 = mysql_fetch_object($result2)){
   $promoter_id 	= $row2->id;
   $status 			= $row2->status;
   $commision_level = $row2->commision_level;
   break;
}

$exp_name = '推广员';
$query3 = "select exp_name from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result3 = mysql_query($query3) or die('query failed3'.mysql_error());
while($row3 = mysql_fetch_object($result3)){
	$exp_name = $row3->exp_name;
	$exp_name_1 =$exp_name;
}

$is_ncomission 		 = 1; //是否开启3*3
$is_ncomission_apply = 1; //手机前端申请按钮 1:开启 0:关闭
$query4 = "select is_ncomission,is_ncomission_apply from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result4 = mysql_query($query4) or die('query failed4'.mysql_error());
while($row4 = mysql_fetch_object($result4)){
	$is_ncomission 		 = $row4->is_ncomission;
	$is_ncomission_apply = $row4->is_ncomission_apply;
}

if(1==$is_ncomission && 1==$status){
	$query5 = "select exp_name from weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." and level=".$commision_level." limit 0,1";
	$result5 = mysql_query($query5) or die('query failed5'.mysql_error());
	while($row5 = mysql_fetch_object($result5)){
		$exp_name = $row5->exp_name;	//推广员自定义名称
		
	}
}

$is_autoupgrade		= 0;	//推广员生成模式
$auto_upgrade_money = 0;	//消费累积成为推广员
$query6 = "select is_autoupgrade,auto_upgrade_money from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result6 = mysql_query($query6) or die('query failed6'.mysql_error());  
while($row6 = mysql_fetch_object($result6)) {
	$is_autoupgrade 	= $row6->is_autoupgrade;
	$auto_upgrade_money = $row6->auto_upgrade_money;
}

// $query7 = "select sum(totalprice) as total_money from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id =".$user_id." and paystatus=1  and sendstatus<3 and return_status in(0,3,9)";
$query7 = "select total_money from my_total_money where isvalid=true and user_id=".$user_id." limit 1";
$total_pay_money = 0;	//累积消费
$result7 = mysql_query($query7) or die('query failed7' . mysql_error());
while($row7 = mysql_fetch_object($result7)) {
	$total_pay_money = $row7->total_money;		//个人消费金额
}
$total_pay_money = round($total_pay_money,2);
if($total_pay_money<0){
	$total_pay_money = 0;
}

//判断渠道是否开启大礼包功能---start
$is_disrcount 	 = 0;
$is_distribution = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='升级大礼包' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 W_is_disrcount Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_distribution = 1;
}
//判断渠道是否开启大礼包功能---end
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $exp_name_1;?></title>
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
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/goods/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/tuiguangyuan1-2chengweituiguangyuan-leijijifen.css" />
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" /> 
    
    <style type="text/css">
    	//ld 点击效果
        .button{ 
        	-webkit-transition-duration: 0.4s; /* Safari */
        	transition-duration: 0.4s;
        }

        .buttonclick:hover{
        	box-shadow:  0 0 5px 0 rgba(0,0,0,0.24);
        }
    </style>  
</head>

<body data-ctrl=true>
	<!-- header部门-->
	<!--<header data-am-widget="header" class="am-header am-header-default header">
		<div class="am-header-left am-header-nav">
			<img class="am-header-icon-custom header-btn"   src="./images/center/nav_bar_back.png"/><span class = "header-btn">返回</span>
		</div>
	    <h1 class="am-header-title">推广员</h1>
	</header> -->   <!-- 暂时屏蔽头部 -->
	<!-- header部门-->
	
   <!-- content ---->
   <div  class = "content" id="containerDiv" style="margin-top:0;">
		<!-- content rect --> 
		<div class = "content-row1">
			<div class="leftLine"></div>
			<div class = "content-row1-main">
				<div class = "content-row1-main-button"><span class="yuedu_btn button buttonclick" data-title="<?php if($is_ncomission){echo $level_arry[$commision_level].$exp_name;}else{echo $exp_name;}?>">阅读<?php if($is_ncomission){echo $level_arry[$commision_level].$exp_name;}else{echo $exp_name;}?>协议</span></div>
				<div class="m-chatting-body">
					<div class="m-chatting-content"  style="box-shadow:-4px 6px 10px #e0e0e0;">
						<div class =  "m-chatting-content-left1">
							<img src = "./images/goods_image/20160050409.png" width = "40" height = "40">
						</div>
						<?php
							if(1==$status){
						?>
						<div class = "m-chatting-content-left2">
							<div class = "m-chatting-content-left2-top1">
								<span><?php if($is_ncomission){echo $level_arry[$commision_level].$exp_name;}else{echo $exp_name;}?></span>
							</div>
							<div class = "m-chatting-content-left2-top2">
								<span>您已经成为<?php if($is_ncomission){echo $level_arry[$commision_level].$exp_name;}else{echo $exp_name;}?></span>
							</div>
						</div>
						<div class = "m-chatting-content-right">
							<img src = "./images/goods_image/20160050410.png" width = "20" height = "15">
						</div>
						<?php
							}else{
						?>
						<div class = "m-chatting-content-left2">
							<div class = "m-chatting-content-left2-top1">
								<span><?php echo $exp_name;?></span>
							</div>
							<div class = "m-chatting-content-left2-top2">
								<span>
								<?php 
									if(0==$status){
										echo "等待商家审核通过...";
									}else{
										if(0==$is_autoupgrade or 1==$is_autoupgrade){
											//echo "申请";  //2016-10-29 by chen 开启有问题，隐藏
										}else{
											echo "购买";
										}
								?>成为<?php echo $exp_name;}?>
								</span>
							</div>
						</div>
						<div class = "m-chatting-content-right1" style="margin-right:-5px;">
						<?php 
								$query8 = "select id from package_list_t where isvalid=true and package_type=1 and stock>=1 and isout=0 and customer_id=".$customer_id." limit 0,1";
								$result8 = mysql_query($query8) or die('query failed8'.mysql_error());
								$p_id = -1;		//礼包数量
								while($row8 = mysql_fetch_object($result8)){
									$p_id = $row8->id;
								}
								if($p_id>0 and $is_distribution>0){
									$url = "package_list.php?ptype=1&level=0&customer_id=".$customer_id_en;
								}else{
									$url = "../common_shop/jiushop/index.php?customer_id=".$customer_id_en;
								}
							?>
							<a href="<?php echo $url;?>">
								<span style="border:1px solid red;color:red;">购买</span>
							</a>
						</div>
						<div class = "m-chatting-content-right1">
						<?php
							if(0==$status){
						?>
							<span style="border:1px solid grey;color:grey;">审核中</span>
						<?php
							}else if(0==$is_autoupgrade || (1==$is_autoupgrade && $total_pay_money>=$auto_upgrade_money)){
							?>
							<span class = "shenqing-btn" style="border:1px solid red;color:red;" data-level="1">申请</span>
						<?php 
							}else if(0==$is_autoupgrade || 1==$is_autoupgrade){
						?>
							<span style="border:1px solid grey;color:grey;">申请</span>
						<?php
							}
						?>
						</div>
						<?php
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
		if($is_ncomission){
			$query9 = "select id,level,level_price,exp_name from weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." and level>".$commision_level." order by level asc limit 0,10";
			$result9 = mysql_query($query9) or die('Query failed9'.mysql_error());
			$exp_name     = "推广员"; //推广员自定义名称
			$level        = 0;		  //级别 0:默认级别
			$level_price  = 0;	      //成为级别推广员条件金额
			$commision_id = -1;
			while ($row9 = mysql_fetch_object($result9)) {
				$commision_id = $row9->id;	
				$exp_name     = $row9->exp_name;
				$level_price  = $row9->level_price;
				$level        = $row9->level;
				
		?>
		<div class = "content-row1">
			<div class="leftLine"></div>
			<div class = "content-row1-main">
				<div class = "content-row1-main-button"></div>
				<div class="m-chatting-body1">
					<div class="m-chatting-content">
						<div class = "m-chatting-content-left21" >
							<div class = "m-chatting-content-left21-top1">
								<img src="./images/goods_image/20160050411.png" width="14" height="17">
								<span><?php echo $level_arry[$level].$exp_name;?></span>
							</div>
							<div class = "m-chatting-content-left21-top2">
								<div class = "m-chatting-content-left21-top2-main">
									<div class="m-progressbar-body">
										<div class="m-progressbar-content" style="width:<?php if(0==$level_price){echo '100';}else{echo round(100*($total_pay_money/$level_price),2);}?>%;max-width:100%;"></div>
									</div>
									<div class = "m-progressbar-remark">
										<span>累积消费</span>
										<div class = "m-progressbar-remark-right">
											<span><font><?php echo $total_pay_money;?></font>/<font><?php echo $level_price;?></font></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class = "m-chatting-content-right2">
						<?php 
								$query10 = "select id from package_list_t where isvalid=true and package_type=3 and stock>=1 and three_level=".$level." and isout=0 and customer_id=".$customer_id." limit 0,1";
								$result10 = mysql_query($query10) or die('query failed10'.mysql_error());
								$p_id = -1;		//礼包id
								while($row10 = mysql_fetch_object($result10)){
									$p_id = $row10->id;
								}
								if($p_id>0 and $is_distribution>0){
									$url = "package_list.php?ptype=3&level=".$level."&customer_id=".$customer_id_en;
								}else{
									$url = "../common_shop/jiushop/index.php?customer_id=".$customer_id_en;
								}
							?>
							<a href="<?php echo $url;?>">
								<span>购买</span>
							</a>
						<?php
							if($total_pay_money<$level_price || 0==$is_ncomission_apply){
						?>
							<span style="border:1px solid grey;color:grey;display:none">申请</span><!--2016-10-29 by chen 隐藏开启有问题-->
						<?php
							}else if(1==$status){
							?>
							<span class = "shenqing-btn" data-level="<?php echo $level;?>" style="display:none;">申请</span><!--2016-10-29 by chen 隐藏开启有问题-->
						<?php 
							}
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php }
		}
		?>
   </div>
   <!-- content ---->
   
   <!--dialog-->
   <div class="am-share dlg">
   	  <!--dialog rect-->
   </div>
   <div class="sell_detail" style="display:none;"><?php echo $sell_detail;?></div>
   <!--dialog-->
</body>		
<!-- 页联系js -->
<script src="./js/goods/global.js"></script>
<script src="./js/goods/promoter_upgrade.js"></script>
<script>
var customer_id    = '<?php echo $customer_id;?>';
var customer_id_en = '<?php echo $customer_id_en;?>';
var post_data      = new Array();
var post_object    = new Array();
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