<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php'); //配置
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
require('../common/jssdk.php');
$user_id = 196282;
$batchcode = -1;
$pid = -1;
$batchcode = $_GET['batchcode'];
$pid = $_GET['pid'];
$customer_id = 3243;


?>
<!DOCTYPE html>
<html>
<head>
    <title>退款</title>
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
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />  
    <link type="text/css" rel="stylesheet" href="./css/css_orange.css" />   
    
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    
</head>

<link rel="stylesheet" href="./css/order_css/style.css" type="text/css" media="all">
<link rel="stylesheet" href="./css/order_css/tuihuo.css" type="text/css" media="all">

<!-- 基本dialog-->
<link type="text/css" rel="stylesheet" href="./css/goods/dialog.css" />
<link type="text/css" rel="stylesheet" href="./css/self_dialog.css" />


<body data-ctrl=true>
	<!-- <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="history.go(-1)">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png"/><span>返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">退款</h1>
	</header>
    <div class="topDiv"></div> --> <!-- 暂时屏蔽头部 -->
	
	<!-- 基本数据地区 - 开始 -->
	<form id="tijiaoFrom" action="./aftersale_action.php" method="post" enctype="multipart/form-data">
		<div id="mainArea"> 
			<input type="hidden" name="aftersale_type" value="refund">
			<input type="hidden" name="batchcode" value="<?php echo $batchcode;?>">
			<input type="hidden" name="pid" value="<?php echo $pid;?>">
			<!-- 退款原因 -->
			<div onclick="selectTuiKuanReason();" class="white-list frame_reason">
				<div class="list-one">
					<div class="left-title" style="width:30%"><span >退款原因</span></div>
					<div style="float:right;margin-right:10px;">
						<span id="selectedReason">请选择</span>
						<input type="hidden" id="refund_reason" name="refund_reason" value="">
						<img class="btn_right_arrow" src="./images/order_image/btn_right.png">
					</div>
				</div>
			</div>
			<div class="line_gray10"></div>
			
			<!-- 退款原因描述 -->
			<div class="itemComment" style="width:100%;" goodsId="1">
				<div class="white-list frame_reason" style="height:300px;">
					<div class="list-one" style="margin-left:10px;">退款原因描述</div>					
					<div class="frame_reason_textarea">
						<textarea name="refund_describe" id="reasonContent" maxlength="125" placeholder="请填写您遇到的问题，最多125字。"></textarea>
					</div>
				</div>
			</div>
			<div class="line_gray10"></div>
			
			<!-- 退款金额 -->
			<div class="white-list frame_reason">
				<div class="list-one">
					<div class="left-title">退款金额</div>
					<div class="div-money"><input id="money" name="return_account" type="text" placeholder="请输入退款金额" style="border:none;text-align:right;"/></div>
				</div>
			</div>
			
		</div>
	</form>
	<!-- 基本数据地区 - 终结 -->
		
	<!-- 下面的【提交】按钮地区 -->
    <div class="white-list frame_button_area">
        <div class="list-one" style="background-color:#eee;">
			<div onclick="tijiao();" class="btn_bottom">提交</div>
        </div>
    </div>
	
	<!-- 弹出来的【选择退款原因】窗口 - 开始 -->
	<div id="reasonSelectArea">
		<div class="frame_list">
			<div onclick="reasonSelect(this,1);" class="item_list">质量原因</div>
			<div onclick="reasonSelect(this,2);" class="item_list">商品信息描述不好</div>
			<div onclick="reasonSelect(this,3);" class="item_list">功能/效果不好</div>
			<div onclick="reasonSelect(this,4);" class="item_list">少件/漏件</div>
			<div onclick="reasonSelect(this,5);" class="item_list">包装/商品破损</div>
			<div onclick="reasonSelect(this,6);" class="item_list">发票问题</div>
			<div onclick="reasonSelect(this,7);" class="item_list" style="border-bottom:none;">其他</div>
		</div>
	</div>
	<!-- 弹出来的【选择退款原因】窗口 - 终结 -->
      
</body>		
<script type="text/javascript">

	var imageCount = 0;
	var tuikuanReason = -1;//退款原因
	
	//选择一个【退款原因】，从【退款原因】列表
	function reasonSelect(obj,kind){
		tuikuanReason = kind;
		$("#selectedReason").html($(obj).html());
		$("#refund_reason").val($(obj).html());
		$("#reasonSelectArea").hide();
	}

	//点击上面的【请选择-退款原因】
	function selectTuiKuanReason(){
		$("#reasonSelectArea").show();
	}
	
	//点击【提交】
	function tijiao(){
		if(tuikuanReason == -1){
			alert("请选择退款原因");
			return;
		}
	
		var reasonContent = $("#reasonContent").val();
		if(reasonContent == ""){
			alert("请输入退货原因描述");
			return;
		}
		
		var inputMoney = $("#money").val();
		if(isNaN(inputMoney) || inputMoney==""){
			alert("请正确输入退款金额！");
			return;
		}
		
		$("#tijiaoFrom").submit();
	}
 	
</script>

</body>
</html>