<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../proxy_info.php');

mysql_query("SET NAMES UTF8");


$user_id=-1;

if(!empty($_GET["user_id"])){
    $user_id = $configutil->splash_new($_GET["user_id"]);
}
$pagenum = 1;
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

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
				<a class="white1">关系详情</a>
			</div>
		</div>
		<li style="margin: 20px 40px 20px 0;float:right;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>

		<div  class="WSY_data">
			<!-- <div id="WSY_list" class="WSY_list">
				<div class="WSY_left" style="background: none;">
					姓名：<span style="font-weight:bold"><?php echo $username; ?></span>&nbsp;&nbsp;&nbsp; 手机号：<span style="font-weight:bold"><?php echo $userphone; ?></span>&nbsp;&nbsp;&nbsp;
					推广金额：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $sum_totalprice; ?></span>
				</div>
			</div> -->
		<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<tr>
					<th width="25%" nowrap="nowrap">更改前的上级</th>
					<th width="25%" nowrap="nowrap">更改后的上级</th>
					<th width="25%" nowrap="nowrap">更改时间</th>
					<th width="25%" nowrap="nowrap">说明</th>
					
				</tr>
			</thead>
			<tbody>
			    <?php 
				
				$query = "select orgin_user_id,change_user_id,createtime,remark from weixin_commonshop_promoter_changes where isvalid=true and user_id=".$user_id." and customer_id=".$customer_id;
				
				 /* 输出数量开始 */
				 $rcount_q2=1;
				 $result2 = mysql_query($query) or die('Query failed: ' . mysql_error());
				 $rcount_q2 = mysql_num_rows($result2);
				 /* 输出数量结束 */
				 
				 $query = $query." order by id desc"." limit ".$start.",".$end;
				 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
	             while ($row = mysql_fetch_object($result)) {
				 
				    $orgin_user_id =$row->orgin_user_id;
					$change_user_id = $row->change_user_id;
					
					$createtime=$row->createtime;
					$remark = $row->remark;
					if($orgin_user_id!=-1){
						$query2= "select name,weixin_name from weixin_users where isvalid=true and id=".$orgin_user_id." limit 0,1"; 
						$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
						$orgin_name="";
						$orgin_weixin_name="";
						while ($row2 = mysql_fetch_object($result2)) {
							$orgin_name=$row2->name;
							$orgin_weixin_name = $row2->weixin_name;
							$orgin_name = $orgin_name."(".$orgin_weixin_name.")";
							break;
						}
					}else{
						$orgin_name = "无上级";
					}	
					if($change_user_id!=-1){
						$query2= "select name,weixin_name from weixin_users where isvalid=true and id=".$change_user_id." limit 0,1"; 
						$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
						$change_name="";
						$change_weixin_name="";
						while ($row2 = mysql_fetch_object($result2)) {
							$change_name=$row2->name;
							$change_weixin_name = $row2->weixin_name;
							$change_name = $change_name."(".$change_weixin_name.")";
							break;
						}
					}else{
						$change_name = "无上级";
					}	
			   ?>
                <tr>
				   <td align="center"><?php echo $orgin_name; ?></td>
				   <td align="center"><?php echo $change_name; ?></td>
				   <td align="center"><?php echo $createtime; ?></td>
				   <td align="center"><?php echo $remark; ?></td>
				  
                </tr>				
			   <?php } ?>
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
var user_id = <?php echo $user_id ?>;

var pagenum = <?php echo $pagenum ?>;
var rcount_q2 = <?php echo $rcount_q2 ?>;
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
			
		document.location= "qrsell_account_detail.php?customer_id="+customer_id+"&user_id="+user_id+"&pagenum="+p;
	   }
    });

  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val()); 
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "qrsell_account_detail.php?customer_id="+customer_id+"&user_id="+user_id+"&pagenum="+a;
		
	}
  }
</script>

</body></html>