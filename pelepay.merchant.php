<?php 
/**
  * Plugin Name: Pelepay for WP e-Commerce
  * Plugin URI: http://www.eoi.com
  * Description: A plugin that provides pelepay support for WP e-Commerce
  * Version: 1.0
  * Author: EOI
  * Author URI: http://www.eoi.co.il
  **/
  
register_activation_hook( __FILE__,'pelepay_set_default_options' );

$nzshpcrt_gateways[$num]['name'] 			= 	'Pelepay';
$nzshpcrt_gateways[$num]['internalname'] 	= 	'Pelepay';
$nzshpcrt_gateways[$num]['display_name'] 	= 	'Pelepay';
$nzshpcrt_gateways[$num]['function'] 		= 	'gateway_pelepay';
$nzshpcrt_gateways[$num]['form'] 			= 	"form_pelepay";
$nzshpcrt_gateways[$num]['submit_function'] = 	"submit_pelepay";
if(get_option('pelepay_button_url') == '')
{
	$nzshpcrt_gateways[$num]['image'] 		= 	"https://www.pelepay.co.il/images/banners/respect_pp_8C.gif";
}
else
{
	$nzshpcrt_gateways[$num]['image'] 		= 	get_option('pelepay_button_url');
}

function pelepay_set_default_options() 
{
	if(get_option('pelepay_business_method') === false) 
	{
		add_option('pelepay_business_method', "");
	}//business account information
	
	if(get_option('pelepay_cancel_url') === false) 
	{
		add_option('pelepay_cancel_url', "");
	}//cancel url
	
	if(get_option('pelepay_success_url') === false) 
	{
		add_option('pelepay_success_url', "");
	}//success url
	
	if(get_option('pelepay_failure_url') === false) 
	{
		add_option('pelepay_failure_url', "");
	}//failure url
	
	if(get_option('pelepay_button_url') === false) 
	{
		add_option('pelepay_button_url', "https://www.pelepay.co.il/images/banners/respect_pp_8C.gif");
	}//button url
	
	if(get_option('pelepay_gateway_url') === false) 
	{
		add_option('pelepay_gateway_url', "https://www.pelepay.co.il/pay/custompaypage.aspx");
	}//gateway url
	
	if(get_option('pelepay_payment_number') === false) 
	{
		add_option('pelepay_payment_number', "1");
	}//payment number
}

function form_pelepay()
{
	$pelepay_business_method	=	get_option('pelepay_business_method');
	$pelepay_cancel_url			=	get_option('pelepay_cancel_url');
	$pelepay_success_url		=	get_option('pelepay_success_url');
	$pelepay_failure_url		=	get_option('pelepay_failure_url');
	/*$pelepay_gateway_url		=	get_option('pelepay_gateway_url');*/
	$pelepay_button_url			=	get_option('pelepay_button_url');	
	$pelepay_payment_number		=	get_option('pelepay_payment_number');
	

	$output ='<tr>';	
	$output.='<td>Email:</td>';	
	$output.='<td><input name="pelepay_business_method" type="text" value="'.esc_html($pelepay_business_method).'"/>
				<br><small>Email address of the account holder used to identify the account beneficiary on pelepay</small></td>';
	$output.='</tr>';
	
	$output.='<tr>';	
	$output.='<td>Payment No.(1-12):</td>';	
	$output.='<td><input name="pelepay_payment_number" type="text" value="'.esc_html($pelepay_payment_number).'"/></td>';
	$output.='</tr>';
	
	$output.='<tr>';	
	$output.='<td>Cancel URL:</td>';	
	$output.='<td><input name="pelepay_cancel_url" type="text" value="'.esc_html($pelepay_cancel_url).'"/>
				<br><small>Please enter landing url for when customers click "cancel" during payment</small></td>';
	$output.='</tr>';
	
	$output.='<tr>';	
	$output.='<td>Success URL:</td>';	
	$output.='<td><input name="pelepay_success_url" type="text" value="'.esc_html($pelepay_success_url).'"/>
				<br><small>Please enter landing URL for approved transactions</small></td>';
	$output.='</tr>';
	
	$output.='<tr>';	
	$output.='<td>Failure URL:</td>';	
	$output.='<td><input name="pelepay_failure_url" type="text" value="'.esc_html($pelepay_failure_url).'"/>
				<br><small>Please enter landing URL for declined transactions</small></td>';
	$output.='</tr>';
	
	$output.='<tr>';	
	$output.='<td>Button URL:</td>';	
	$output.='<td><input name="pelepay_button_url" type="text" value="'.esc_html($pelepay_button_url).'"/>
				<br><small>Please enter image URL for the payment button</small></td>';
	$output.='</tr>';
	
		
		
	return $output;
}
function submit_pelepay()
{
	$pelepay_business_method=	sanitize_text_field($_POST[pelepay_business_method]);
	$pelepay_cancel_url		=	sanitize_text_field($_POST[pelepay_cancel_url]);
	$pelepay_success_url	=	sanitize_text_field($_POST[pelepay_success_url]);
	$pelepay_failure_url	=	sanitize_text_field($_POST[pelepay_failure_url]);
	$pelepay_button_url		=	sanitize_text_field($_POST[pelepay_button_url]);	
	$pelepay_payment_number	=	sanitize_text_field($_POST[pelepay_payment_number]);
	
	// Store updated options array to database
	update_option('pelepay_business_method', $pelepay_business_method);
	update_option('pelepay_cancel_url', $pelepay_cancel_url);
	update_option('pelepay_success_url', $pelepay_success_url);
	update_option('pelepay_failure_url', $pelepay_failure_url);
	update_option('pelepay_button_url', $pelepay_button_url);	
	update_option('pelepay_payment_number', $pelepay_payment_number);
	return true;
}
function nzshpcrt_pelepay_callback()
{	
	if(isset($_GET['Response']))
	{
		$pmt_sts	=	$_GET['Response'];
		switch($pmt_sts)
		{
			case 'cancel':
						$_SESSION['WpscGatewayErrorMessage'] = __('Your order is cancel');
						break;
			case '000': 
						$sessionid	=	$_GET['orderid'];
						$transact_id	=	$_GET['index'];
						unset($_SESSION['WpscGatewayErrorMessage']);
						$data = array(
						'processed'  => 2,
						'transactid' => $transact_id,
						'date'       => time(),
						);
						wpsc_update_purchase_log_details( $sessionid, $data, 'sessionid' );
						transaction_results($sessionid, false, $transaction_id);						
						break;					
			case '003': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1492;&#1514;&#1511;&#1513;&#1512; &#1500;&#1495;&#1489;&#1512;&#1514; &#1492;&#1488;&#1513;&#1512;&#1488;&#1497;');
						break;
			case '004': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1505;&#1497;&#1512;&#1493;&#1489; &#1513;&#1500; &#1495;&#1489;&#1512;&#1514; &#1492;&#1488;&#1513;&#1512;&#1488;&#1497;');
						break;
			case '033': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1492;&#1499;&#1512;&#1496;&#1497;&#1505; &#1488;&#1497;&#1504;&#1493; &#1514;&#1511;&#1497;&#1503;');
						break;
			case '001': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1499;&#1512;&#1496;&#1497;&#1505; &#1488;&#1513;&#1512;&#1488;&#1497; &#1495;&#1505;&#1493;&#1501;');
						break;
			case '002': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1499;&#1512;&#1496;&#1497;&#1505; &#1488;&#1513;&#1512;&#1488;&#1497; &#1490;&#1504;&#1493;&#1489;');
						break;
			case '039': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1505;&#1508;&#1512;&#1514; &#1492;&#1489;&#1497;&#1511;&#1493;&#1512;&#1514; &#1513;&#1500; &#1492;&#1499;&#1512;&#1496;&#1497;&#1505; &#1488;&#1497;&#1504;&#1492; &#1514;&#1511;&#1497;&#1504;&#1492;');
						break;
			case '101': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1500;&#1488; &#1502;&#1499;&#1489;&#1491;&#1497;&#1501; &#1491;&#1497;&#1497;&#1504;&#1512;&#1505;');
						break;
			case '061': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1500;&#1488; &#1492;&#1493;&#1494;&#1503; &#1502;&#1505;&#1508;&#1512; &#1499;&#1512;&#1496;&#1497;&#1505; &#1488;&#1513;&#1512;&#1488;&#1497;');
						break;
			case '157': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1499;&#1512;&#1496;&#1497;&#1505; &#1488;&#1513;&#1512;&#1488;&#1497; &#1514;&#1497;&#1497;&#1512;');
						break;
			case '133': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1499;&#1512;&#1496;&#1497;&#1505; &#1488;&#1513;&#1512;&#1488;&#1497; &#1514;&#1497;&#1497;&#1512;');
						break;
			case '036': 
						$_SESSION['WpscGatewayErrorMessage'] = __('&#1508;&#1490; &#1514;&#1493;&#1511;&#1507; &#1492;&#1499;&#1512;&#1496;&#1497;&#1505;');
						break;	
		}	
	}	
}	

function gateway_pelepay($seperator, $sessionid)
{		
	global $wpdb, $wpsc_cart;
		
	$data['business'] 		=	get_option('pelepay_business_method');
	$data['amount'] 		=	number_format($wpsc_cart->total_price, 2);
	$data['orderid'] 		=	$sessionid;
	//$data['description'] 	=	'Order '.$sessionid; //amendment made after client's request
	
	$data['firstname']	=	$_POST['collected_data'][2];
	$data['lastname']	=	$_POST['collected_data'][3];
	$data['address']	=	$_POST['collected_data'][4];
	$data['postcode']	=	$_POST['collected_data'][8];
	$data['country']	=	$_POST['collected_data'][7][0];
	$data['email']		=	$_POST['collected_data'][9];
	$data['phone']		=	$_POST['collected_data'][18];
	
	foreach($wpsc_cart->cart_items as $i => $Item) 
	{
		$arr_cart_items[]	=	$Item->product_name;	
	}
	$data['description'] 	= 	implode("|", $arr_cart_items);
	
	if(get_option('pelepay_cancel_url') == '')
	{
		$data['cancel_return'] 	=	get_option('shopping_cart_url')."&Response=cancel";
	}
	else
	{
		$data['cancel_return'] 	=	get_option('pelepay_cancel_url');
	}
	
	if(get_option('pelepay_failure_url') == '')
	{
		$data['fail_return'] 	=	get_option('shopping_cart_url');
	}
	else
	{
		$data['fail_return'] 	=	get_option('pelepay_failure_url');
	}
	
	if(get_option('pelepay_success_url') == '')
	{
		$data['success_return'] 	=	get_option('transact_url').'&sessionid='.$sessionid.'&';
	}
	else
	{
		$data['success_return'] 	=	get_option('pelepay_success_url');
	}
	
	if(get_option('pelepay_button_url') == '')
	{
		$pelepay_button_url =	'https://www.pelepay.co.il/images/banners/respect_pp_8C.gif';
	}
	else
	{
		$pelepay_button_url =	get_option('pelepay_button_url');
	}
	// Create Form to post to ChronoPay
	$output = "
		<form id=\"pelepayform\" name=\"pelepayform\" method=\"post\" action=\"".get_option('pelepay_gateway_url')."\">\n";

	foreach($data as $n=>$v) {
			$output .= "			<input type=\"hidden\" name=\"$n\" value=\"$v\" />\n";
	}

	$output .= "			<input type=\"image\" src=\"$pelepay_button_url\" name=\"submit\" alt=\"Make payments with pelepay\" />
		</form>
	";
	
	// echo form..
	echo($output);
	echo "<script language=\"javascript\" type=\"text/javascript\">document.getElementById('pelepayform').submit();</script>";
  	exit();
}
function gateway_pelepay1($seperator, $sessionid)
{

	// $wpdb is the database handle,
	// $wpsc_cart is the shopping cart object

	global $wpdb, $wpsc_cart;

	// This grabs the purchase log id from the database
	// that refers to the $sessionid

	$purchase_log = $wpdb->get_row("SELECT * FROM `" . WPSC_TABLE_PURCHASE_LOGS . "` WHERE `sessionid`= " . $sessionid . " LIMIT 1", ARRAY_A);

	// This grabs the users info using the $purchase_log
	// from the previous SQL query

	$usersql = "SELECT `" . WPSC_TABLE_SUBMITED_FORM_DATA . "`.value,
				`" . WPSC_TABLE_CHECKOUT_FORMS . "`.`name`,
				`" . WPSC_TABLE_CHECKOUT_FORMS . "`.`unique_name` FROM
				`" . WPSC_TABLE_CHECKOUT_FORMS . "` LEFT JOIN
				`" . WPSC_TABLE_SUBMITED_FORM_DATA . "` ON
				`" . WPSC_TABLE_CHECKOUT_FORMS . "`.id =
				`" . WPSC_TABLE_SUBMITED_FORM_DATA . "`.`form_id` WHERE
				`" . WPSC_TABLE_SUBMITED_FORM_DATA . "`.`log_id`=" . $purchase_log['id'] . "
				ORDER BY `" . WPSC_TABLE_CHECKOUT_FORMS . "`.`order`";
	$userinfo = $wpdb->get_results($usersql, ARRAY_A);

	// Now we will store all the information into an associative array
	// called $data to prepare it for sending via cURL
	// please note that the key in the array may need to be changed
	// to work with your gateway (refer to your gateways documentation).

	$data = array();
	$data['USER'] = get_option('my_new_gateway_username');
	$data['PWD'] = get_option('my_new_gateway_password');
	$data['AMT'] = number_format($wpsc_cart->total_price, 2);
	$data['ITEMAMT'] = number_format($wpsc_cart->subtotal, 2);
	$data['SHIPPINGAMT'] = number_format($wpsc_cart->base_shipping, 2);
	$data['TAXAMT'] = number_format($wpsc_cart->total_tax);
	foreach((array)$userinfo as $key => $value)
	{
		if (($value['unique_name'] == 'billingfirstname') && $value['value'] != '')
		{
			$data['BILLFIRSTNAME'] = $value['value'];
		}

		if (($value['unique_name'] == 'billinglastname') && $value['value'] != '')
		{
			$data['BILLLASTNAME'] = $value['value'];
		}

		if (($value['unique_name'] == 'billingaddress') && $value['value'] != '')
		{
			$data['BILLADDRESS'] = $value['value'];
		}

		if (($value['unique_name'] == 'billingemail') && $value['value'] != '')
		{
			$data['BILLEMAIL'] = $value['value'];
		}

		if (($value['unique_name'] == 'billingphone') && $value['value'] != '')
		{
			$data['BILLPHONE'] = $value['value'];
		}

		if (($value['unique_name'] == 'shippingfirstname') && $value['value'] != '')
		{
			$data['SHIPFIRSTNAME'] = $value['value'];
		}

		if (($value['unique_name'] == 'shippinglastname') && $value['value'] != '')
		{
			$data['SHIPLASTNAME'] = $value['value'];
		}

		if (($value['unique_name'] == 'shippingaddress') && $value['value'] != '')
		{
			$data['SHIPADDRESS'] = $value['value'];
		}

		if (($value['unique_name'] == 'shippingemail') && $value['value'] != '')
		{
			$data['SHIPEMAIL'] = $value['value'];
		}

		if (($value['unique_name'] == 'shippingphone') && $value['value'] != '')
		{
			$data['SHIPPHONE'] = $value['value'];
		}
	}

	// Ordered Products

	foreach($wpsc_cart->cart_items as $i => $Item)
	{
		$data['PROD_NAME' . $i] = $Item->product_name;
		$data['PROD_AMT' . $i] = number_format($Item->unit_price, 2);
		$data['PROD_NUMBER' . $i] = $i;
		$data['PROD_QTY' . $i] = $Item->quantity;
		$data['PROD_TAXAMT' . $i] = number_format($Item->tax, 2);
	}

	// now we add all the information in the array into a long string

	$transaction = "";
	foreach($data as $key => $value)
	{
		if (is_array($value))
		{
			foreach($value as $item)
			{
				if (strlen($transaction) > 0) $transaction.= $seperator;
				$transaction.= "$key=" . urlencode($item);
			}
		}
		else
		{
			if (strlen($transaction) > 0) $transaction.= $seperator;
			$transaction.= "$key=" . urlencode($value);
		}
	}

	// Now we have the information we want to send to the gateway in a nicely formatted string we can setup the cURL

	curl_setopt($connection, CURLOPT_URL, "http://this.is.the.gateways.address");
	$useragent = 'WP e-Commerce plugin';
	curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($connection, CURLOPT_NOPROGRESS, 1);
	curl_setopt($connection, CURLOPT_VERBOSE, 1);
	curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($connection, CURLOPT_POST, 1);
	curl_setopt($connection, CURLOPT_POSTFIELDS, $transaction);
	curl_setopt($connection, CURLOPT_TIMEOUT, 30);
	curl_setopt($connection, CURLOPT_USERAGENT, $useragent);
	curl_setopt($connection, CURLOPT_REFERER, "https://" . $_SERVER['SERVER_NAME']);
	curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
	$buffer = curl_exec($connection);
	curl_close($connection);
	/*
	* This is the trickiest part, gateways send back their data in many different ways, please refer to your gateways documentation.
	* So now we have passed the information to the gateway and have received information back from the gateway (stored in $buffer),
	All we have left is to find out whether the transaction was accepted by the gateway or whether the transaction failed.
	* This next bit of code was borrowed from the people at http://shopplugin.net/tour/ and their paypal pro module
	*/
	$_ = new stdClass();
	$r = array();
	$pairs = split("&", $buffer);
	foreach($pairs as $pair)
	{
		list($key, $value) = split("=", $pair);
		if (preg_match("/(w*?)(d+)/", $key, $matches))
		{
			if (!isset($r[$matches[1]])) $r[$matches[1]] = array();
			$r[$matches[1]][$matches[2]] = urldecode($value);
		}
		else $r[$key] = urldecode($value);
	}

	$response->ack = $r['ACK'];

	// with paypal Pro, ACK holds the status of the payment either
	// 'Success' 'SuccessWithWarning' and other error messages as well.
	// All we need at this time is 'Success and SuccessWithWarning

	$response->errorcodes = $r['L_ERRORCODE'];
	$response->shorterror = $r['L_SHORTMESSAGE'];
	$response->longerror = $r['L_LONGMESSAGE'];
	$response->severity = $r['L_SEVERITYCODE'];
	$response->timestamp = $r['TIMESTAMP'];
	$response->correlationid = $r['CORRELATIONID'];
	$response->version = $r['VERSION'];
	$response->build = $r['BUILD'];
	$response->transactionid = $r['TRANSACTIONID'];
	$response->amt = $r['AMT'];
	$response->avscode = $r['AVSCODE'];
	$response->cvv2match = $r['CVV2MATCH'];
	if ($response->ack == 'Success' || $response->ack == 'SuccessWithWarning')
	{

		// redirect to  transaction page and store in DB as a order with
		// accepted payment

		$sql = "UPDATE `" . WPSC_TABLE_PURCHASE_LOGS . "` SET `processed`= '2' WHERE `sessionid`=" . $sessionid;
		$wpdb->query($sql);
		$transact_url = get_option('transact_url');
		unset($_SESSION['WpscGatewayErrorMessage']);
		header("Location: " . $transact_url . $seperator . "sessionid=" . $sessionid);
	}
	else
	{

		// redirect back to checkout page with errors

		$sql = "UPDATE `" . WPSC_TABLE_PURCHASE_LOGS . "` SET `processed`= '5' WHERE `sessionid`=" . $sessionid;
		$wpdb->query($sql);
		$transact_url = get_option('checkout_url');
		$_SESSION['WpscGatewayErrorMessage'] = __('Sorry your transaction did not go through successfully, please try again.');
		header("Location: " . $transact_url);
	}
}
add_action('init', 'nzshpcrt_pelepay_callback');

?>