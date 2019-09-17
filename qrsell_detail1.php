<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../back_init.php');
$link = mysql_connect("localhost",DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');
$scene_id = $_GET["scene_id"];
echo $scene_id.'=';
$rcount = $_GET["rcount"];
mysql_query("SET NAMES UTF8");

$query = 'SELECT id,appid,appsecret,access_token FROM weixin_menus where isvalid=true and customer_id='.$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
$access_token="";
while ($row = mysql_fetch_object($result)) {
	$keyid =  $row->id ;
	$appid =  $row->appid ;
	$appsecret = $row->appsecret;
	$access_token = $row->access_token;
	break;
}


$query2= "select name,phone from weixin_users where isvalid=true and id=".$scene_id; 
$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
$username="";
$userphone="";

while ($row2 = mysql_fetch_object($result2)) {
	$username=$row2->name;
	$userphone = $row2->phone;
	break;
}


?>
<!DOCTYPE html>
<!-- saved from url=(0047)http://www.ptweixin.com/member/?m=shop&a=orders -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/global.js"></script>
</head>

<body>

<style type="text/css">body, html{background:url(images/main-bg.jpg) left top fixed no-repeat;}</style>
<div id="iframe_page">
	<div class="iframe_content">
			<link href="css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/shop.js"></script>
	<div class="r_nav">
		<ul>
			<li class=""><a href="base.php?customer_id=<?php echo $customer_id; ?>">基本设置</a></li>
			<li class=""><a href="fengge.php?customer_id=<?php echo $customer_id; ?>">风格设置</a></li>
			<li class=""><a href="defaultset.php?customer_id=<?php echo $customer_id; ?>">首页设置</a></li>
			<li class=""><a href="product.php?customer_id=<?php echo $customer_id; ?>">产品管理</a></li>
			<li class=""><a href="order.php?customer_id=<?php echo $customer_id; ?>">订单管理</a></li>
			<li class="cur"><a href="qrsell.php?customer_id=<?php echo $customer_id; ?>">推广员</a></li>
			<li class=""><a href="customers.php?customer_id=<?php echo $customer_id; ?>">顾客</a></li>
		</ul>
	</div>
<link href="css/operamasks-ui.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/operamasks-ui.min.js"></script>
<script type="text/javascript" src="../js/tis.js"></script>
<script language="javascript">

$(document).ready(shop_obj.orders_init);
</script>
<div id="orders" class="r_con_wrap">
        <div class="search">
	    姓名：<span style="font-weight:bold"><?php echo $username; ?></span>&nbsp;&nbsp;&nbsp; 手机号：<span style="font-weight:bold"><?php echo $userphone; ?></span>&nbsp;&nbsp;&nbsp;
		推广总数：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $rcount; ?></span>
		</div>
		<table border="0" cellpadding="5" cellspacing="0" class="r_con_table" id="order_list">
			<thead>
				<tr>
					<td width="13%" nowrap="nowrap">姓名</td>
					<td width="13%" nowrap="nowrap">手机号</td>
					<td width="30%" nowrap="nowrap">推广时间</td>
					<td width="30%" nowrap="nowrap">是否成为推广员</td>
					<td width="10%" nowrap="nowrap">来源</td>
				</tr>
			</thead>
			<tbody>
			   <?php 
			     // $query="select wqs.id,user_id,wqs.createtime,wqs.fromw,wu.name as name,wu.phone as phone,wu.weixin_name as weixin_name,wu.weixin_fromuser from weixin_qr_scans wqs inner join weixin_users wu on wu.id=wqs.user_id  and wqs.isvalid=true and wu.isvalid=true  and scene_id=".$scene_id." and wqs.customer_id=".$customer_id;
				 $query="select wqs.id,user_id,wqs.createtime,wqs.fromw,wu.name as name,wu.phone as phone,wu.weixin_name as weixin_name,wu.weixin_fromuser from weixin_qr_scans wqs inner join weixin_users wu on wu.id=wqs.user_id  and wqs.isvalid=true and wu.isvalid=true  and scene_id=".$scene_id." and wqs.customer_id=".$customer_id;
				 $query = $query." order by wqs.id desc";
				 echo $query;
				 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
	             while ($row = mysql_fetch_object($result)) {
				    $user_id = $row->user_id;
					$id = $row->id;
					$fromwstr="主动关注";
					$fromw = $row->fromw;
					
					switch($fromw){
					   case 2:
					      //朋友圈
						  $fromwstr="朋友圈";
					      break;
					   case 3:
					      //扫码
						  $fromwstr="扫二维码";
						  break;
					   case 4:
					      //手动调整
						  $fromwstr="手动调整";
						  break;
					}
					
					
					    $username=$row->name;
						$userphone = $row->phone;
						$weixin_name = $row->weixin_name;
						$weixin_fromuser= $row->weixin_fromuser;
					
					
					$createtime=$row->createtime;
					
					if(empty($weixin_name)){
					
					    $url="https://api.weixin.qq.com/cgi-bin/user/info";
                        $data = array('access_token'=>$access_token,'openid'=>$weixin_fromuser); 

						$ch = curl_init(); 
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POST, 1); 
						// 这一句是最主要的
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
						curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
						$html = curl_exec($ch);  
						
						curl_close($ch) ;

						$obj=json_decode($html);
						
						 if(!empty($obj->errmsg)){
						     $errmsg =$obj->errmsg ;
						    //echo $errorcode;
						    if($errmsg=="access_token expired"){
							 //高级接口超时，重新绑定
							//echo "<script>win_alert('发生未知错误！请联系商家');</script>";
							    $data = array('grant_type'=>'client_credential','appid'=>$appid,'secret'=>$appsecret);  
							     $url = "https://api.weixin.qq.com/cgi-bin/token";

								$ch = curl_init(); 
								curl_setopt($ch, CURLOPT_URL, $url);
								curl_setopt($ch, CURLOPT_POST, 1); 
								// 这一句是最主要的
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
								curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
								$html = curl_exec($ch);  
								$obj=json_decode($html);
								
								$access_token = "";
								curl_close($ch) ;
								if(!empty($obj->access_token)){
								   $access_token = $obj->access_token;
								   $query4="update weixin_menus set appid='".$appid."',appsecret='".$appsecret."', access_token = '".$access_token."' where customer_id=".$customer_id;
								   mysql_query($query4);
								   
								    $url="https://api.weixin.qq.com/cgi-bin/user/info";
                                   $data = array('access_token'=>$access_token,'openid'=>$weixin_fromuser); 


									$ch = curl_init(); 
									curl_setopt($ch, CURLOPT_URL, $url);
									curl_setopt($ch, CURLOPT_POST, 1); 
									// 这一句是最主要的
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
									curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
									$html = curl_exec($ch);  
									$obj=json_decode($html);
									$weixin_name =  $obj->nickname;
									$sex = $obj->sex;
									$headimgurl= $obj->headimgurl;
									$subscribe_time = $obj->subscribe_time;
									$query4 = "update weixin_users set weixin_headimgurl='".$headimgurl."',weixin_name='".$weixin_name."',sex=".$sex." where id=".$user_id;
									//echo $query;	
									mysql_query($query4);
								}else{
								   echo "<script>win_alert('发生未知错误！请联系商家');</script>";
								   return;
								}
						 }
					  }else{
						        $weixin_name =  $obj->nickname;
								$sex = $obj->sex;
								$headimgurl= $obj->headimgurl;
								$subscribe_time = $obj->subscribe_time;
								$query4 = "update weixin_users set weixin_headimgurl='".$headimgurl."',weixin_name='".$weixin_name."',sex=".$sex." where id=".$user_id;
							    mysql_query($query4);
						 }
					}

					$username = $username."(".$weixin_name.")";
					
					$query2="select status from promoters where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id;
					
					$p_status=-2;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
	                while ($row2 = mysql_fetch_object($result2)) {
					    $p_status = $row2->status;
                        break;						
					}
					$p_status_str="未申请";
					switch($p_status){
					    case -1:
						   $p_status_str="已驳回";
						   break;
						case 0:
						   $p_status_str="已申请,正在审核";
						   break;
						case 1:
						   $p_status_str="是";
						   break;
						default:
						
						   break;
					}
					
					
			   ?>
                <tr>
				   <td>
				   <?php if($p_status==1){ ?>
				     <a href="qrsell.php?exp_user_id=<?php echo $user_id; ?>&customer_id=<?php echo $customer_id; ?>">
					 <?php echo $username; ?>
					 </a>
				   <?php }else{ ?>
				      <?php echo $username; ?>
				   <?php } ?>
				   </td>
				   <td><?php echo $userphone; ?></td>
				   <td><?php echo $createtime; ?></td>
				   <td><?php echo $p_status_str; ?></td>
				   <td><?php echo $fromwstr; ?></td>
                </tr>				
			   <?php } ?>
			</tbody>
		</table>
		<div class="blank20"></div>
		<div id="turn_page"></div>
	</div>	</div>
<div>
</div></div><div style="top: 101px; position: absolute; background-color: white; z-index: 2000; left: 398px; visibility: hidden; background-position: initial initial; background-repeat: initial initial;" class="om-calendar-list-wrapper om-widget om-clearfix om-widget-content multi-1"><div class="om-cal-box" id="om-cal-4381460996810347"><div class="om-cal-hd om-widget-header"><a href="javascript:void(0);" class="om-prev "><span class="om-icon om-icon-seek-prev">Prev</span></a><a href="javascript:void(0);" class="om-title">2014年1月</a><a href="javascript:void(0);" class="om-next "><span class="om-icon om-icon-seek-next">Next</span></a></div><div class="om-cal-bd"><div class="om-whd"><span>日</span><span>一</span><span>二</span><span>三</span><span>四</span><span>五</span><span>六</span></div><div class="om-dbd om-clearfix"><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);">1</a><a href="javascript:void(0);">2</a><a href="javascript:void(0);">3</a><a href="javascript:void(0);">4</a><a href="javascript:void(0);">5</a><a href="javascript:void(0);">6</a><a href="javascript:void(0);">7</a><a href="javascript:void(0);">8</a><a href="javascript:void(0);" class="om-state-highlight om-state-nobd">9</a><a href="javascript:void(0);" class="om-state-disabled">10</a><a href="javascript:void(0);" class="om-state-disabled">11</a><a href="javascript:void(0);" class="om-state-disabled">12</a><a href="javascript:void(0);" class="om-state-disabled">13</a><a href="javascript:void(0);" class="om-state-disabled">14</a><a href="javascript:void(0);" class="om-state-disabled">15</a><a href="javascript:void(0);" class="om-state-disabled">16</a><a href="javascript:void(0);" class="om-state-disabled">17</a><a href="javascript:void(0);" class="om-state-disabled">18</a><a href="javascript:void(0);" class="om-state-disabled">19</a><a href="javascript:void(0);" class="om-state-disabled">20</a><a href="javascript:void(0);" class="om-state-disabled">21</a><a href="javascript:void(0);" class="om-state-disabled">22</a><a href="javascript:void(0);" class="om-state-disabled">23</a><a href="javascript:void(0);" class="om-state-disabled">24</a><a href="javascript:void(0);" class="om-state-disabled">25</a><a href="javascript:void(0);" class="om-state-disabled">26</a><a href="javascript:void(0);" class="om-state-disabled">27</a><a href="javascript:void(0);" class="om-state-disabled">28</a><a href="javascript:void(0);" class="om-state-disabled">29</a><a href="javascript:void(0);" class="om-state-disabled">30</a><a href="javascript:void(0);" class="om-state-disabled">31</a><a href="javascript:void(0);" class="om-null">0</a></div></div><div class="om-setime om-state-default hidden"></div><div class="om-cal-ft"><div class="om-cal-time om-state-default">时间：<span class="h">0</span>:<span class="m">0</span>:<span class="s">0</span><div class="cta"><button class="u om-icon om-icon-triangle-1-n"></button><button class="d om-icon om-icon-triangle-1-s"></button></div></div><button class="ct-ok om-state-default">确定</button></div><div class="om-selectime om-state-default hidden"></div></div></div><div style="top: 101px; position: absolute; background-color: white; z-index: 2000; left: 564px; visibility: hidden; background-position: initial initial; background-repeat: initial initial;" class="om-calendar-list-wrapper om-widget om-clearfix om-widget-content multi-1"><div class="om-cal-box" id="om-cal-8113757355604321"><div class="om-cal-hd om-widget-header"><a href="javascript:void(0);" class="om-prev "><span class="om-icon om-icon-seek-prev">Prev</span></a><a href="javascript:void(0);" class="om-title">2014年1月</a><a href="javascript:void(0);" class="om-next "><span class="om-icon om-icon-seek-next">Next</span></a></div><div class="om-cal-bd"><div class="om-whd"><span>日</span><span>一</span><span>二</span><span>三</span><span>四</span><span>五</span><span>六</span></div><div class="om-dbd om-clearfix"><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);">1</a><a href="javascript:void(0);">2</a><a href="javascript:void(0);">3</a><a href="javascript:void(0);">4</a><a href="javascript:void(0);">5</a><a href="javascript:void(0);">6</a><a href="javascript:void(0);">7</a><a href="javascript:void(0);">8</a><a href="javascript:void(0);" class="om-state-highlight om-state-nobd">9</a><a href="javascript:void(0);" class="om-state-disabled">10</a><a href="javascript:void(0);" class="om-state-disabled">11</a><a href="javascript:void(0);" class="om-state-disabled">12</a><a href="javascript:void(0);" class="om-state-disabled">13</a><a href="javascript:void(0);" class="om-state-disabled">14</a><a href="javascript:void(0);" class="om-state-disabled">15</a><a href="javascript:void(0);" class="om-state-disabled">16</a><a href="javascript:void(0);" class="om-state-disabled">17</a><a href="javascript:void(0);" class="om-state-disabled">18</a><a href="javascript:void(0);" class="om-state-disabled">19</a><a href="javascript:void(0);" class="om-state-disabled">20</a><a href="javascript:void(0);" class="om-state-disabled">21</a><a href="javascript:void(0);" class="om-state-disabled">22</a><a href="javascript:void(0);" class="om-state-disabled">23</a><a href="javascript:void(0);" class="om-state-disabled">24</a><a href="javascript:void(0);" class="om-state-disabled">25</a><a href="javascript:void(0);" class="om-state-disabled">26</a><a href="javascript:void(0);" class="om-state-disabled">27</a><a href="javascript:void(0);" class="om-state-disabled">28</a><a href="javascript:void(0);" class="om-state-disabled">29</a><a href="javascript:void(0);" class="om-state-disabled">30</a><a href="javascript:void(0);" class="om-state-disabled">31</a><a href="javascript:void(0);" class="om-null">0</a></div></div><div class="om-setime om-state-default hidden"></div><div class="om-cal-ft"><div class="om-cal-time om-state-default">时间：<span class="h">0</span>:<span class="m">0</span>:<span class="s">0</span><div class="cta"><button class="u om-icon om-icon-triangle-1-n"></button><button class="d om-icon om-icon-triangle-1-s"></button></div></div><button class="ct-ok om-state-default">确定</button></div><div class="om-selectime om-state-default hidden"></div></div></div>

<?php 

mysql_close($link);
?>
</body></html>