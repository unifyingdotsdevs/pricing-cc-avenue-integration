<?php

function proteen_get_cc_avenue_checkout_form($atts) {
	
	// $response_handler = proteen_get_cc_avenue_handler_url();
	$response_handler = site_url('/').'cc-avenue-handler.php';
	ob_start(); ?>
		<div class="payment-integration-form">
			<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" id="proteen_form_handler" class="proteen_form_handler" enctype="multipart/form-data">

				<?php $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
					$order_suffix = substr(str_shuffle($str_result),0, 6);
					$order_id = "PROTEEN".$order_suffix;
				?>
				<input type="hidden" name="email_subject" value="<?php echo $legend; ?>">
				<input type="hidden" name="order_id" value="<?php echo $order_id; ?>"/>
				<input type="hidden" name="merchant_id" value="130338">
				<input type="hidden" name="language" value="EN">
				<input type="hidden" name="amount" id="proteen_amount" value="50">

				<input type="hidden" name="merchant_param1" id="selected_plan" value=""/>
				<input type="hidden" name="merchant_param2"id="coupon_id" value=""/>

				<input type="hidden" name="currency" value="INR">
				<input type="hidden" name="redirect_url" value="<?php echo $response_handler; ?>">
				<input type="hidden" name="cancel_url" value="<?php echo $response_handler; ?>">
				<input type="hidden" name="action" value="proteen_checkout_cc_avenue">

				<div class="form-group">
					<input type="text" class="form-control" id="name" placeholder="Name" tabindex="1" name="merchant_param3" required="required">
				</div>
				<div class="form-group">

					<input type="email" class="form-control" id="email" placeholder="Email" tabindex="2" name="merchant_param4" required="required">
				</div>
				<div class="form-group">

					<input type="text" class="form-control" id="phone" placeholder="Phone" tabindex="3" name="merchant_param5" required="required">
				</div>
				<div class="form-group">
					<div class="captcha_wrapper">
						<div class="g-recaptcha" data-sitekey="6Le4troZAAAAAJ9q8IxtMobKvNSZZjK25JOScHJz"></div>
					</div>
				</div>
				<div>
					<button class="btn-payment" type="submit">Pay Now</button>
				</div>
			</form>
		</div>
	<?php return ob_get_clean();
}
add_shortcode('get_cc_avenue_checkout_form', 'proteen_get_cc_avenue_checkout_form');







function send_prepurchase_notification() {

	$plan_ids = substr($_POST['merchant_param1'], 0, -1);
	$plan_ids = str_replace(' ', '', $plan_ids);
	$plan_ids = explode(",", $plan_ids);
	$plan_ids = array_filter($plan_ids, 'strlen');

	$plans = '';
	foreach ($plan_ids as $plan) {
		$plans .= get_the_title($plan).", ";
	}

	$selected_plan = substr($plans, 0, -1);
	$mail_content = '';
	$mail_content .= "Name: ".$_POST['merchant_param3']."<br/>";
	$mail_content .= "Email: ".$_POST['merchant_param4']."<br/>";
	$mail_content .= "Phone: ".$_POST['merchant_param5']."<br/>";
	$mail_content .= "Plans: ".$selected_plan."<br/>";


	$subject = 'ProTeen Pre-Order Notification';
	$headers = array('Content-Type: text/html; charset=UTF-8');

	if($_POST['merchant_param1'] != '' && $_POST['merchant_param3'] != '' && $_POST['merchant_param4'] != '' && $_POST['merchant_param5'] != '' && $plan_ids != '' && sizeof($plan_ids) > 0) {
		try {
			$file_path = get_home_path()."log/purchase_log.txt";

			$myfile = fopen($file_path, "a") or die("Unable to open file!");

			$stringData = "Time log: ".current_time("Y-m-d Y-m-d h:i:s A") ."\n";
			$stringData .= "Name: ".$_POST['merchant_param3']."\n";
			$stringData .= "Email: ".$_POST['merchant_param4']."\n";
			$stringData .= "Plans: ".$selected_plan."\n\n\n\n";

			fwrite($myfile, $stringData);
			fclose($myfile);
		} catch (Exception $e) {
			//caught here
		}
		wp_mail( "info@proteen.com", $subject, $mail_content, $headers );
	}
}



function proteen_initiate_cc_avenue_checkout_process() {
	$secret="6Le4troZAAAAACDLG3qKB3uv7TkkV23jKC0I8Y6t";


	$response = $_POST["g-recaptcha-response"];

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array(
			'secret' => '6Le4troZAAAAACDLG3qKB3uv7TkkV23jKC0I8Y6t',
			'response' => $_POST["g-recaptcha-response"]
		);
		$options = array(
			'http' => array (
				'method' => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$verify = file_get_contents($url, false, $context);
		$captcha_success=json_decode($verify);

		if ($captcha_success->success==false) {
			echo "fake";
			die();
		}









	send_prepurchase_notification();
	$merchant_data='130338';
    $working_key='D9A289CF81E7D4C3FF63AE3AFD13F9A4';//Shared by CCAVENUES
    $access_code='AVDQ92HF75BA65QDAB';//Shared by CCAVENUES
	
	foreach ($_POST as $key => $value){
		$merchant_data.=$key.'='.$value.'&';
	}

	
	$encrypted_data=encrypt($merchant_data,$working_key);

?>
<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
<?php
echo "<input type=hidden name=encRequest value=$encrypted_data>";
echo "<input type=hidden name=access_code value=$access_code>";
?>
</form>
</center>
<script language='javascript'>document.redirect.submit();</script>

<?php

}
add_action( 'admin_post_nopriv_proteen_checkout_cc_avenue', 'proteen_initiate_cc_avenue_checkout_process' );
add_action( 'admin_post_proteen_checkout_cc_avenue', 'proteen_initiate_cc_avenue_checkout_process' );



function proteen_add_transaction_data($transaction_data) {

	$dataSize=sizeof($transaction_data);
	$orderId=false;
	$plan_ids = array();
	$order_id = $coupon_id = $user_name = $user_email = $phone_number = '';
	for ($i = 0; $i < $dataSize; $i++) {
		$information = explode('=', $transaction_data[$i]);
		if ($i==0) {
			$order_id=$information[1];
		}
		else if ($i==26) {
			$plan_ids=$information[1];
			$plan_ids = substr($plan_ids, 0, -1);
			$plan_ids = explode(",", $plan_ids);
		}
		else if ($i==27) {
			$coupon_id=$information[1];
		}
		else if ($i==28) {
			$user_name=$information[1];
		}
		else if ($i==29) {
			$user_email=$information[1];
		}
		if ($i==30) {
			$phone_number=$information[1];
		}
		$information = '';
	}


	$found_coin = 0;
	$add_on_arr = array();
	$coins = 0;
	$plan_name = '';
	$p_plan_type = '';
	$is_rb = 0;
	if(sizeof($plan_ids) > 0) {
		foreach ($plan_ids as $key => $plan) {
			$transaction_arr = array(
				'post_title'		=> $user_name,
				'post_status'		=> 'publish',
				'post_type'			=> 'proteen_transaction',
			);


			$transaction_id = wp_insert_post($transaction_arr);

			if($coupon_id != '') {
				update_field('coupon_id', $coupon_id, $transaction_id);
				$count = get_post_meta( $coupon_id, 'coupon_redeem_count', true );
				if ( ! $count ) {
					$count = 0;
				}
				$count++;
				update_post_meta( $coupon_id, 'coupon_redeem_count', $count );
			}
			if($user_email != '') {
				update_field('user_email', $user_email, $transaction_id);
			}

			if($plan != '') {
				update_field('plan_id', $plan, $transaction_id);
			}

			if($order_id != '') {
				update_field('order_id', $order_id, $transaction_id);
			}

			if($phone_number != '') {
				update_field('phone_number', $phone_number, $transaction_id);
			}

			if($found_coin == 0) {
				$coins = get_post_meta($plan,'procoins', true);
				$found_coin = 1;
			}

			$plan_types = get_the_terms( $plan , 'plan_type' );
			foreach ( $plan_types as $plan_type ) {
				$type = $plan_type->name;
				$selected_plan = get_the_title($plan);
				if($type == "Monthly" || $type == "Yearly") {
					$plan_name = $selected_plan;
					$p_plan_type = $type;
				} else {
					array_push($add_on_arr, $selected_plan);
				}
				break;
			}


			//is rb?
			if($is_rb == 0) {
				$rb_plan = get_field('is_rb_plan', $plan, true);
				if($rb_plan == 1) {
					$is_rb = 1;
				}
			}
		}

	
		proteen_send_user_notification($user_name, $user_email, $plan_name, $p_plan_type, $add_on_arr, $coins);
		inform_portal($user_email, $coins, $is_rb);
	}
}


function inform_portal($email, $coins, $is_rb = 0) {

	$handler = curl_init();

	$post_data = json_encode(array(
		'email' => $email,
		'coins' => $coins,
		'is_rb'	=> $is_rb
	 ));
	curl_setopt($handler, CURLOPT_URL, 'https://portal.proteen.com/api/subscribe-plan');
	curl_setopt($handler, CURLOPT_POSTFIELDS, $post_data);


	$header = array();
	$header[] = 'Accept: application/json';
	$header[] = 'Content-type: application/json';
	$header[] = 'Authorization: YWRtaW4udXNlckBwcm90ZWVuLmNvbTokMnkkMTAkWWlqUTNNR2owOEtuTEhtdS9JS3E4dU55dFFMajBwMHV4VVhBUjZ5eHNjSDlrMUxIVzZBdDI';


	curl_setopt($handler, CURLOPT_HTTPHEADER,$header);

	curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);


	$response = curl_exec($handler);

	$response = json_decode($response);

}



function proteen_send_user_notification($user_name, $user_email, $plan_name, $plan_type, $add_on_arr, $coins) {

	$mail_content = "Hi ".$user_name.",<br/><br/>";
	$mail_content .= "Thank you for subscribing to ".$plan_name." – Pack. ".number_format($coins)." Procoins will be added to your ProTeen account registered with email id – ".$user_email.".<br/><br/>";

	if(sizeof($add_on_arr) > 0) {
		$mail_content .="Your Add Ons:";
		$mail_content .= "<ul>";
		foreach ($add_on_arr as $key => $add_on) {
			$mail_content .="<li>".$add_on."</li>";
		}
		$mail_content .= "</ul>";
		$mail_content .= "<br/>";
	}


	$mail_content .= "ProTeen team will reach out to you shortly.<br/><br/>";

	$mail_content .= "Note: In case you are using a different email id on ProTeen, mail us on info@proteen.com or call/WhatsApp us on +91 8657386646.<br/><br/>";

	$mail_content .= "Please feel free to reach out to us for any further information.<br/><br/>";

	$mail_content .= "Thanks.<br/><br/>";

	$to = $user_email;
	$subject = 'ProTeen Order Notification';
	$headers = array('Content-Type: text/html; charset=UTF-8');
	wp_mail( $to, $subject, $mail_content, $headers );
	wp_mail( "info@proteen.com", $subject, $mail_content, $headers );
}