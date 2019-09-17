<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../proxy_info.php');
$scene_id = $configutil->splash_new($_GET["scene_id"]);

mysql_query("SET NAMES UTF8");

$query = 'SELECT id,appid,appsecret,access_token FROM weixin_menus where isvalid=true and customer_id='.$customer_id;
$result = mysql_query($query) or die('Query failed1: ' . mysql_error());  
$access_token="";
while ($row = mysql_fetch_object($result)) {
	$keyid =  $row->id ;
	$appid =  $row->appid ;
	$appsecret = $row->appsecret;
	$access_token = $row->access_token;
	break;
}

$weixin_fromuser="";
$query2= "select name,phone from weixin_users where isvalid=true and id=".$scene_id." limit 0,1"; 
$result2 = mysql_query($query2) or die('Query failed2: ' . mysql_error());
$username="";
$userphone="";

while ($row2 = mysql_fetch_object($result2)) {
	$username=$row2->name;
	$userphone = $row2->phone;
	break;
}

$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed3: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed4: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

 $query="select id,name,weixin_name,weixin_headimgurl,phone,createtime,fromw,parent_id from weixin_users w where isvalid=true and match(gflag) against (',".$scene_id.",')";
$result_q = mysql_query($query) or die('Query failed5: ' . mysql_error());
$rcount_q = mysql_num_rows($result_q);
$query = $query." order by id desc"." limit ".$start.",".$end;
//更新总的粉丝数
$query_update="update promoters set team_fans=".$rcount_q." where isvalid=true  and customer_id=".$customer_id." and user_id=".$scene_id."";
mysql_query($query_update)or die('Query failed_up01'.mysql_error());
				
?>
<!DOCTYPE html>
<!-- saved from url=(0047)http://www.ptweixin.com/member/?m=shop&a=orders -->
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">	
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
</head>

<body>
<div id="WSY_content">
	<div class="WSY_columnbox" style="min-height: 300px;">
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">粉丝</a>
			</div>
		</div>
		<div  class="WSY_data">
			<div id="WSY_list" class="WSY_list">
				<div class="WSY_left" style="background: none;">
					<div class="search">
						姓名：<span style="font-weight:bold"><?php echo $username; ?></span>&nbsp;&nbsp;&nbsp; 手机号：<span style="font-weight:bold"><?php echo $userphone; ?></span>&nbsp;&nbsp;&nbsp;
						
						推广总数：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $rcount_q; ?></span>
						
						
					</div>
				</div>
				<li style="margin: 10px 40px 0 0;float:right;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>

			</div>
		<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<tr>
					<th width="13%" nowrap="nowrap">姓名</th>  
					<th width="13%" nowrap="nowrap">手机号</th>
					<th width="13%" nowrap="nowrap">推广时间</th>
					<th width="10%" nowrap="nowrap">是否成为推广员</th>
					<th width="10%" nowrap="nowrap">来源</th>
					
				</tr>
			</thead>
			<tbody>
			   <?php 
			   
				// echo $query;
				 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
	             while ($row = mysql_fetch_object($result)){				   
					$user_id = $row->id;
					$fromwstr="主动关注";
					$fromw = $row->fromw;
					//fromw来源.1:主动关注；2：朋友圈；3.二维码；4:后台手动调整；5网页注册的用户;6:微推广链接; 7:APP注册的用户 ;8:微信注册
					switch($fromw){
					   case 1:
					      //主动关注
						  $fromwstr="主动关注";
					      break;
					    case 2:
					      //朋友圈
						  $fromwstr="朋友圈";
					      break;
					    case 3:
					      //扫码
						  $fromwstr="二维码";
						  break;
					    case 4:
					      //手动调整
						  $fromwstr="手动调整";
						  break;
					    case 5:
					      //网页注册
						  $fromwstr="网页注册";
						  break;
					    case 6:
					      //微推广链接
						  $fromwstr="微推广链接";
						  break;
					    case 7:
					      //APP注册
						  $fromwstr="APP注册";
						  break;
						case 8:
					      //微信注册
						  $fromwstr="微信注册";
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
						
						 if(!empty($obj->errcode)){
						     $errcode =$obj->errcode ;
						    //echo $errorcode;
						    if($errcode==42001||$errcode==40014 ||$errcode==40001){
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
					$result2 = mysql_query($query2) or die('Query failed6: ' . mysql_error());
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
				   <td align="center">
				   <?php if($p_status==1){ ?>
				     <a style="color: #06A7E1;" href="promoter.php?exp_user_id=<?php echo $user_id; ?>&customer_id=<?php echo $customer_id_en; ?>">
					 <?php echo $username; ?>
					 </a>
				   <?php }else{ ?>
				      <?php echo $username; ?>
				   <?php } ?>
				   </td>
				   <td align="center"><?php echo $userphone; ?></td>
				   <td align="center"><?php echo $createtime; ?></td>
				   <td align="center"><?php echo $p_status_str; ?></td>
				   <td align="center"><?php echo $fromwstr; ?></td>
                </tr>		
				
				 <?php } ?>
			   <!-- <tr>
					<td colspan=5>
						 <div class="tcdPageCode"></div>
					</td>
				</tr> -->
			</tbody>
		</table>
		<div class="blank20"></div>
		<div id="turn_page"></div>
		
		</div>	
		<!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
	</div>
	   
</div>
	
<div style="top: 101px; position: absolute; background-color: white; z-index: 2000; left: 398px; visibility: hidden; background-position: initial initial; background-repeat: initial initial;" class="om-calendar-list-wrapper om-widget om-clearfix om-widget-content multi-1"><div class="om-cal-box" id="om-cal-4381460996810347"><div class="om-cal-hd om-widget-header"><a href="javascript:void(0);" class="om-prev "><span class="om-icon om-icon-seek-prev">Prev</span></a><a href="javascript:void(0);" class="om-title">2014年1月</a><a href="javascript:void(0);" class="om-next "><span class="om-icon om-icon-seek-next">Next</span></a></div><div class="om-cal-bd"><div class="om-whd"><span>日</span><span>一</span><span>二</span><span>三</span><span>四</span><span>五</span><span>六</span></div><div class="om-dbd om-clearfix"><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);">1</a><a href="javascript:void(0);">2</a><a href="javascript:void(0);">3</a><a href="javascript:void(0);">4</a><a href="javascript:void(0);">5</a><a href="javascript:void(0);">6</a><a href="javascript:void(0);">7</a><a href="javascript:void(0);">8</a><a href="javascript:void(0);" class="om-state-highlight om-state-nobd">9</a><a href="javascript:void(0);" class="om-state-disabled">10</a><a href="javascript:void(0);" class="om-state-disabled">11</a><a href="javascript:void(0);" class="om-state-disabled">12</a><a href="javascript:void(0);" class="om-state-disabled">13</a><a href="javascript:void(0);" class="om-state-disabled">14</a><a href="javascript:void(0);" class="om-state-disabled">15</a><a href="javascript:void(0);" class="om-state-disabled">16</a><a href="javascript:void(0);" class="om-state-disabled">17</a><a href="javascript:void(0);" class="om-state-disabled">18</a><a href="javascript:void(0);" class="om-state-disabled">19</a><a href="javascript:void(0);" class="om-state-disabled">20</a><a href="javascript:void(0);" class="om-state-disabled">21</a><a href="javascript:void(0);" class="om-state-disabled">22</a><a href="javascript:void(0);" class="om-state-disabled">23</a><a href="javascript:void(0);" class="om-state-disabled">24</a><a href="javascript:void(0);" class="om-state-disabled">25</a><a href="javascript:void(0);" class="om-state-disabled">26</a><a href="javascript:void(0);" class="om-state-disabled">27</a><a href="javascript:void(0);" class="om-state-disabled">28</a><a href="javascript:void(0);" class="om-state-disabled">29</a><a href="javascript:void(0);" class="om-state-disabled">30</a><a href="javascript:void(0);" class="om-state-disabled">31</a><a href="javascript:void(0);" class="om-null">0</a></div></div><div class="om-setime om-state-default hidden"></div><div class="om-cal-ft"><div class="om-cal-time om-state-default">时间：<span class="h">0</span>:<span class="m">0</span>:<span class="s">0</span><div class="cta"><button class="u om-icon om-icon-triangle-1-n"></button><button class="d om-icon om-icon-triangle-1-s"></button></div></div><button class="ct-ok om-state-default">确定</button></div><div class="om-selectime om-state-default hidden"></div></div></div><div style="top: 101px; position: absolute; background-color: white; z-index: 2000; left: 564px; visibility: hidden; background-position: initial initial; background-repeat: initial initial;" class="om-calendar-list-wrapper om-widget om-clearfix om-widget-content multi-1"><div class="om-cal-box" id="om-cal-8113757355604321"><div class="om-cal-hd om-widget-header"><a href="javascript:void(0);" class="om-prev "><span class="om-icon om-icon-seek-prev">Prev</span></a><a href="javascript:void(0);" class="om-title">2014年1月</a><a href="javascript:void(0);" class="om-next "><span class="om-icon om-icon-seek-next">Next</span></a></div><div class="om-cal-bd"><div class="om-whd"><span>日</span><span>一</span><span>二</span><span>三</span><span>四</span><span>五</span><span>六</span></div><div class="om-dbd om-clearfix"><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);" class="om-null">0</a><a href="javascript:void(0);">1</a><a href="javascript:void(0);">2</a><a href="javascript:void(0);">3</a><a href="javascript:void(0);">4</a><a href="javascript:void(0);">5</a><a href="javascript:void(0);">6</a><a href="javascript:void(0);">7</a><a href="javascript:void(0);">8</a><a href="javascript:void(0);" class="om-state-highlight om-state-nobd">9</a><a href="javascript:void(0);" class="om-state-disabled">10</a><a href="javascript:void(0);" class="om-state-disabled">11</a><a href="javascript:void(0);" class="om-state-disabled">12</a><a href="javascript:void(0);" class="om-state-disabled">13</a><a href="javascript:void(0);" class="om-state-disabled">14</a><a href="javascript:void(0);" class="om-state-disabled">15</a><a href="javascript:void(0);" class="om-state-disabled">16</a><a href="javascript:void(0);" class="om-state-disabled">17</a><a href="javascript:void(0);" class="om-state-disabled">18</a><a href="javascript:void(0);" class="om-state-disabled">19</a><a href="javascript:void(0);" class="om-state-disabled">20</a><a href="javascript:void(0);" class="om-state-disabled">21</a><a href="javascript:void(0);" class="om-state-disabled">22</a><a href="javascript:void(0);" class="om-state-disabled">23</a><a href="javascript:void(0);" class="om-state-disabled">24</a><a href="javascript:void(0);" class="om-state-disabled">25</a><a href="javascript:void(0);" class="om-state-disabled">26</a><a href="javascript:void(0);" class="om-state-disabled">27</a><a href="javascript:void(0);" class="om-state-disabled">28</a><a href="javascript:void(0);" class="om-state-disabled">29</a><a href="javascript:void(0);" class="om-state-disabled">30</a><a href="javascript:void(0);" class="om-state-disabled">31</a><a href="javascript:void(0);" class="om-null">0</a></div></div><div class="om-setime om-state-default hidden"></div><div class="om-cal-ft"><div class="om-cal-time om-state-default">时间：<span class="h">0</span>:<span class="m">0</span>:<span class="s">0</span><div class="cta"><button class="u om-icon om-icon-triangle-1-n"></button><button class="d om-icon om-icon-triangle-1-s"></button></div></div><button class="ct-ok om-state-default">确定</button></div><div class="om-selectime om-state-default hidden"></div></div></div>

<?php 
mysql_close($link);

?>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
var customer_id = '<?php echo $customer_id_en ?>';

var scene_id = <?php echo $scene_id ?>;
var pagenum = <?php echo $pagenum ?>;
var rcount_q2 = <?php echo $rcount_q ?>;
var end = <?php echo $end ?>;
var count = Math.ceil(rcount_q2/end);//总页数
console.log(count);
var page = count;
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			
		document.location= "qrsell_detail_member.php?pagenum="+p+"&customer_id="+customer_id+"&scene_id="+scene_id;
	   }
    });

  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val()); 
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "qrsell_detail_member.php?pagenum="+a+"&customer_id="+customer_id+"&scene_id="+scene_id;
		
	}
  }
</script>
</body></html>