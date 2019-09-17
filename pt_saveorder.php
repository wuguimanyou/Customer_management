<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../config.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');mysql("set names utf8");
mysql_query("SET NAMES UTF8");
date_default_timezone_set('prc');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../common/utility_pt.php');

$customer_id = $_POST['customer_id'];
$batchcode = $_POST['batchcode'];
$createtime = date("Y-m-d H:i:s",time());
$overtime = date("Y-m-d H:i:s",time()+24*60*60);
$time_rr = time();
$op = $_POST['op'];


$group_id = -1;
$type = 1;
$query = "select user_id,customer_id,type,group_id,pid,address_id,totalprice from weixin_commonshop_order_group where isvalid=true and batchcode='".$batchcode."'";
$row = mysql_query($query);
$rod = mysql_fetch_object($row);
	$user_id = $rod->user_id;
	$customer_id = $rod->customer_id;
	$type = $rod->type;
	$group_id = $rod->group_id;
	$pid = $rod->pid;
	$address_id = $rod->address_id;
	$totalprice = $rod->totalprice;

$querys = "select number,name from weixin_commonshop_group_products where isvalid=true and id=".$pid." and customer_id=".$customer_id;
$rows = mysql_query($querys);
$rods = mysql_fetch_object($rows);
	$number = $rods->number;
	$shop_name = $rods->name;

$query2 = "select * from weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id;
$row2 = mysql_query($query2);
$rod2 = mysql_fetch_object($row2);
	$openid = $rod2->weixin_fromuser;

$shopmessage = new shopMessage_Utlity();

switch($op){
	case 'pay':
		if($type == 1){			//拼团购买
			if($group_id<0){		//开团
				$query2 = "insert into weixin_commonshop_group (createtime,group_number,isvalid,overtime,existing_number,pid,user_id,customer_id,status,address_id) 
							value ('".$createtime."',".$number.",true,'".$overtime."',1,".$pid.",".$user_id.",".$customer_id.",1,".$address_id.")";
				//file_put_contents("zhifu.txt","SQL2===".$query2."\r\n",FILE_APPEND);
				mysql_query($query2);
				$group_id = mysql_insert_id();

				$query3 = "update weixin_commonshop_order_group set paystatus=2,group_id=".$group_id.",transaction_id='".$transaction_id."',paytime='".$createtime."' where isvalid=true and type=1 and batchcode='".$batchcode."'";
				//file_put_contents("zhifu.txt","SQL3===".$query3."\r\n",FILE_APPEND);
				mysql_query($query3);

				$content = "您成功支付一笔订单,开团成功\r\n".
							"商品：".$shop_name."\n".
							"时间：".$createtime."\n".
							"金额：".$totalprice."\n".
							"身份：团长";

				$shopmessage->SendMessage($content, $openid, $customer_id);				
				
				echo 1;exit;
			}else{						//参团					
				$query2 = "select group_number,existing_number,overtime from weixin_commonshop_group where isvalid=true and id=".$group_id." and customer_id=".$customer_id;
				$row2 = mysql_query($query2);
				$rod2 = mysql_fetch_object($row2);
					$group_number = $rod2->group_number;
					$existing_number = $rod2->existing_number;
					$g_overtime = $rod2->overtime;

				if($existing_number < $group_number && $time_rr < $g_overtime){
					$query3 = "update weixin_commonshop_order_group set paystatus=2,transaction_id='".$transaction_id."',paytime='".$createtime."' where isvalid=true and type=1 and batchcode='".$batchcode."' and customer_id=".$customer_id;
					//file_put_contents("zhifu.txt","SQL2===".$query2."\r\n",FILE_APPEND);
					mysql_query($query3);

					$existing_number = $existing_number + 1;
					$query4 = "update weixin_commonshop_group set existing_number=".$existing_number." where isvalid=true and id=".$group_id." and customer_id=".$customer_id;
					mysql_query($query4);

					$content = "您成功支付一笔订单,参团成功\r\n".
								"商品：".$shop_name."\n".
								"时间：".$createtime."\n".
								"金额：".$totalprice."\n".
								"身份：团员";

					$shopmessage->SendMessage($content, $openid, $customer_id);

					if($existing_number == $group_number){
						$query5 = "update weixin_commonshop_group set status=2 where isvalid=true and id=".$group_id." and customer_id=".$customer_id;
						mysql_query($query5);
						
						$query6 = "update weixin_commonshop_order_group set status=2 where isvalid=true and paystatus=2 and group_id=".$group_id." and customer_id=".$customer_id;
						mysql_query($query6);

						$query7 = "select user_id from weixin_commonshop_order_group where isvalid=true and status=2 and paystatus=2 and group_id=".$group_id." and customer_id=".$customer_id;
						$row7 = mysql_query($query7);
						while($rod7 = mysql_fetch_object($row7)){
							$g_user_id = $rod7->user_id;

							$query8 = "select weixin_fromuser from weixin_users where isvalid=true and id=".$g_user_id." and customer_id=".$customer_id;
							$row8 = mysql_query($query8);
							$rod8 = mysql_fetch_object($row8);
								$openid = $rod8->weixin_fromuser;

							$content = "您的团队已确认\r\n".
										"拼团商品：".$shop_name."\n".
										"拼团人数：".$group_number."\n".
										"确认时间：".$createtime;
							
							$shopmessage->SendMessage($content, $openid, $customer_id);
						}

					}
						echo 1;exit;
				}else{
					$query2 = "insert into weixin_commonshop_group (createtime,group_number,isvalid,overtime,existing_number,pid,user_id,customer_id,status,address_id) 
								value ('".$createtime."',".$number.",true,'".$overtime."',1,".$pid.",".$user_id.",".$customer_id.",1,".$address_id.")";
					//file_put_contents("zhifu.txt","SQL2===".$query2."\r\n",FILE_APPEND);
					mysql_query($query2);
					$group_id = mysql_insert_id();

					$query3 = "update weixin_commonshop_order_group set paystatus=2,group_id=".$group_id.",transaction_id='".$transaction_id."',paytime='".$createtime."' where isvalid=true and type=1 and batchcode='".$batchcode."'";
					//file_put_contents("zhifu.txt","SQL3===".$query3."\r\n",FILE_APPEND);
					mysql_query($query3);

					if($time_rr > $g_overtime){
						$gc = "已过期,已新开一个团队";
					}else{
						$gc = "人数已满,已新开一个团队";
					}

					$content = "您成功支付一笔订单\r\n".
								"该团".$gc."\n".
								"商品：".$shop_name."\n".
								"时间：".$createtime."\n".
								"金额：".$totalprice."\n".
								"身份：团长";

					$shopmessage->SendMessage($content, $openid, $customer_id);
					
					echo 1;exit;
				}
			}
		}else{				//单独购买
			$query2 = "update weixin_commonshop_order_group set status=2,paystatus=2,paytime='".$createtime."' where isvalid=true and type=2 and batchcode='".$batchcode."' and customer_id=".$customer_id;
			//file_put_contents("zhifu.txt","SQL2===".$query2."\r\n",FILE_APPEND);
			mysql_query($query2);

			$content = "您成功支付一笔订单\r\n".
						"商品：".$shop_name."\n".
						"时间：".$createtime."\n".
						"金额：".$totalprice;

			$shopmessage->SendMessage($content, $openid, $customer_id);

			echo 1;exit;
		}
	break;

	case 'del':
		$query8 = "update weixin_commonshop_order_group set isvalid=false where batchcode='".$batchcode."'";
		mysql_query($query8);
		echo 1;exit;
	break;
}

mysql_close($link);
?>

