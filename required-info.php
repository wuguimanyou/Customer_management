<?php
header("Content-type: text/html; charset=utf-8");
session_cache_limiter( "private, must-revalidate" ); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('order_Form Could not select database');
require('../common/utility.php');
require('../proxy_info.php');
//头文件----start
require('../common/common_from.php');
//头文件----end


$pid = '';
$pros = '';
$supply_id = 0;
$supply_id = $_POST['supply_id'];	//品牌供应商ID或者平台ID
$pid = $_POST['pid'];				//一个订单的所有PID和属性
$pros = $_POST['pros'];				//一个订单所有的产品属性
$ii = $_POST['ii'];					//定位第几个订单
$pid_arr = explode(',',$pid);
$pros_arr = explode('|*|',$pros);		
//var_dump($pid_arr);

//------------查找品牌供应商或者平台名称和logo
	$shop_name = '';												//商店名称
	$brand_logo = 'images/goods_image/iconfont-jiantou.png';		//默认logo
	if($supply_id>0){
			$isbrand_supply = 0;	//普通供应商标识
			$supply_apply_id = 0;
			
			$query_is_supply = "select id,shopName from weixin_commonshop_applysupplys where isvalid=true and user_id=".$supply_id." and isbrand_supply=1 ";
			$result_is_supply=mysql_query($query_is_supply)or die('Query failed'.mysql_error());
			
			while($row_is_supply=mysql_fetch_object($result_is_supply)){
				$supply_apply_id = $row_is_supply->id;
				$shop_name = $row_is_supply->shopName;
			}
			if($supply_apply_id>0){		//品牌供应商
				
				
				$query_supply = "select brand_name from weixin_commonshop_brand_supplys where isvalid=true and customer_id=".$customer_id." and id=".$supply_id."";
				$result_supply=mysql_query($query_supply)or die('Query failed'.mysql_error());
				
				while($row_supply=mysql_fetch_object($result_supply)){
					$shop_name = $row_supply->brand_name;
					$brand_logo = $row_supply->brand_logo;
				}
				$isbrand_supply = 1;	//普通供应商标识
			}
		}else{							//平台
			$query="select name from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
			$result = mysql_query($query) or die('Query failed2: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
				$shop_name = $row->name;
			}
		}
//------------查找品牌供应商或者平台名称和logo		
?>
<!DOCTYPE html>
<html>
<head>
    <title>必填信息</title>
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
    
    <link type="text/css" rel="stylesheet" href="assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="css/goods/global.css" />
    <link rel="stylesheet" href="css/css_orange.css" />
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="assets/js/amazeui.js"></script>
    <script type="text/javascript" src="js/global.js"></script>
    <script type="text/javascript" src="js/loading.js"></script>
    <script src="js/jquery.ellipsis.js"></script>
    <script src="js/jquery.ellipsis.unobtrusive.js"></script>
<style>
.title{width: 92%;height: 52px;line-height: 52px;margin:0 auto;}
.title img{width: 23px;display: inline-block;vertical-align: middle;}
.title span{display: inline-block;vertical-align: middle;font-size: 16px;color:#707070;overflow: hidden;text-overflow:ellipsis;width: 80%;margin-left: 3%;}
.content{width: 100%;font-size: 0;padding: 10px 0;border-top: 1px solid #d1d1d1;background: #e9e9e9;}
.content:last-child{border-bottom: 1px solid #d1d1d1;}
.c-img{width: 26%;display: inline-block;border:1px solid #d1d1d1;margin-left: 3%;vertical-align: top;}
.right-side{width: 70%;display: inline-block;}
.right-side input{width: 92%;height: 25px;margin-bottom: 8px;margin-left: 4%;border:1px solid #d1d1d1;font-size: 13px;color: #222;line-height: 25px;text-indent: 5px;}
.save{background: #1c1f20;text-align: center;font-size: 15px;color: #fff;width: 92%;padding: 8px 0;border:none;margin:10px auto;display: block;}
.cancle{border:1px solid #1c1f20;color:#1c1f20;width: 92%;padding: 8px 0;background: #fff;margin:0 auto;display: block;}
</style>
</head>
<body>
<?php 
	
  ?>
<div class="b-box">
    <div class="title">
        <img src="<?php echo $brand_logo; ;?>">
        <span><?php echo $shop_name;?></span>
    </div>
	<?php 
	/*-------查出这个订单需要填写必填信息的产品------*/
	$i = 0;	
	foreach ($pid_arr as $product_id){
	
	$pro_id = 0;
	$pro_name = '';
	$default_imgurl = '';
	$query = "select id,name,default_imgurl from weixin_commonshop_products  where isvalid=true and is_Pinformation=1   and customer_id=".$customer_id." and id =".$product_id."";
	//echo $query;
	$result=mysql_query($query)or die('Query failed'.mysql_error());
	while($row=mysql_fetch_object($result)){
		$pro_id = $row->id;
		$pro_name = $row->name;
		$default_imgurl = $row->default_imgurl;
	}		
	
	?>
    <div class="content" pid="<?php echo $product_id ;?>" pros="<?php echo $pros_arr[$i];?>" pid="<?php echo $pro_id ;?>">	
        <img src="<?php echo $default_imgurl ;?>" class="c-img">
        <div class="right-side" >
        <form>
			<?php
			$info_id = 0;			//必填信息ID
			$information_name = '';//必填信息内容
			$query2  = "select id,name from weixin_commonshop_product_information_t where isvalid=true and customer_id=".$customer_id." and p_id=".$pro_id;
			$result2=mysql_query($query2)or die('Query failed2'.mysql_error());
			while($row2=mysql_fetch_object($result2)){
				$info_id = $row2->id;
				$information_name = $row2->name;
			
			
			?>
            <input type="text" info_id="<?php echo $info_id;?>" information_name="<?php echo $information_name;?>" name="fname"  placeholder="<?php echo $information_name;?>" required="required" pros="<?php echo $pros_arr[$i];?>" pid="<?php echo $pro_id ;?>" >
           
		<?php } ?>
        </form>
        </div>
    </div>
	<?php $i++; }?>
 </div>   
  
    <button class="save" onclick="save();" style="background:#ff8430;border:1px solid  #ff8430;">保存</button>
    <button class="cancle" onclick="history.go(-1);" style="border:1px solid  #ff8430;color:#ff8430;" >返回</button>
</body>
<script>
var supply_id = '<?php echo $supply_id ;?>';
var user_id = '<?php echo $user_id ;?>';
var ii = '<?php echo $ii ;?>';						//定位第几个订单的位置 1~~+	
var info_object = localStorage.getItem('info_'+user_id); 	//读取localStorage的数据
var info_object_arr = new Array();
if(info_object != null){
	info_object_arr = JSON.parse(info_object);			//json转数组
	var self_info_object_arr = info_object_arr[ii];		//自己对应的数组
	console.log(info_object_arr);
	//console.log(info_object_arr);
	//自动填充内容
	var j = 0;
		$('.content').each(function(){
				thiss = $(this);
				var input = thiss.find('input');
				var k = 0;
				input.each(function(){
					this_input = $(this);
					this_input.val(self_info_object_arr[j][2][k][1]);
					k++;	
				});
			j++;	
		});
}




	//保存到localStorage函数
	
	function save(){
		var isReturn = false;		
		var rtn_array_temp = new Array();			//临时数组
		var rtn_array_cart = new Array();
		var error_i = 0;
		$.each($('.content'),function(){			//把每个表单的值和info_ID组合成字符串，存入一个数组当中
			
			thiss = $(this);						
			var input = thiss.find('input');			
			var pro_array = new Array();			//填写的数据	
			
				var pid = thiss.attr('pid');
				var pros = thiss.attr('pros');
				var pro_info_arr = new Array();				 				
				var temp_parent = new Array();				
								
				pro_info_arr[0] = pid;
				pro_info_arr[1] = pros;
				
				var temp3 = new Array();
			$.each(input,function(){							//遍历表单
				
				var temp = new Array();
				var input_this = $(this);					
				var info_id = input_this.attr('info_id');		//获取每个表单的信息ID
				var information_name = input_this.attr('information_name');		//获取每个表单的信息名称
				var input_val = input_this.val();				//获取每个表单的内容
			
				//弹出错误
				if(input_val==''){
					error_i++;				
					isReturn = true;
				}
				/*生成一个数据供下次修改使用*/
				temp[0] = information_name;
				temp[1] = input_val;						
				temp_parent.push(temp);							//存入数组
				/*生成一个数据供下次修改使用*/
				
				/*生成一个数组供提交订单使用*/
				temp3[0] = pid;
				temp3[1] = pros;
				temp3[2] = information_name;
				temp3[3] = input_val;
				
				rtn_array_cart.push(temp3);
				/*生成一个数组供提交订单使用*/
			});
			pro_info_arr[2] = temp_parent;						//存入数组
			
			rtn_array_temp.push(pro_info_arr);						//全部存入到一个数组中
		
		});
		if(error_i >0){			//提示错误
			showAlertMsg("提示",'必填信息不能为空',"知道了");
		}
		if(isReturn == true ){return false ;} 
		console.log(rtn_array_temp);
		console.log(rtn_array_cart);
		
		//保存到localStorage,方便下次读取
		if(info_object_arr==null){			//创建
			var _A = new Array();					
			_A[ii] = rtn_array_temp ;					
			var rtn_array_json = JSON.stringify(_A);				//转JSON					
			localStorage.setItem('info_'+user_id,rtn_array_json);	//存入localStorage
			
		}else{					 			//修改自己的内容
			info_object_arr[ii] = rtn_array_temp ; 					 					
			var rtn_array_json = JSON.stringify(info_object_arr);	//转JSON				
			localStorage.setItem('info_'+user_id,rtn_array_json);	//存入localStorage
		}

		history.replaceState({},'','order_form.php?customer_id<?php echo $customer_id_en ;?>');	
		location.href = "order_form.php?customer_id=<?php echo $customer_id_en ;?>";
	}

</script>
<?php require('../common/share.php');?>
</html>