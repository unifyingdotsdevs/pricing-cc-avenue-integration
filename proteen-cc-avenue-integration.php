<?php

/**
 * Plugin Name: ProTeen CC Avenue Integration
 * Plugin URI: http://proteenlife.com
 * Description: ProTeen CC Avenue Integration
 * Version: 1.0
 * Author: Inexture
 * Author URI: https://inexture.com
 */




add_action('wp_enqueue_scripts', 'proteen_cc_avenue_scripts');
function proteen_cc_avenue_scripts() {

	wp_enqueue_style('proteen_avenue_style', plugin_dir_url(__FILE__) . 'asset/css/custom.css');

	wp_enqueue_script('jquery_validation', 'https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js', array('jquery'), 1.0);

	wp_enqueue_script('jquery_validation_additional_method', 'https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.js', array('jquery'), 1.0);
	wp_enqueue_script('captcha_js', 'https://www.google.com/recaptcha/api.js', array('jquery'), 1.0);
    wp_enqueue_script('proteen_scripts', plugin_dir_url(__FILE__) . 'asset/js/custom.js', array('jquery'), 1.0, true);

    wp_localize_script( 'proteen_scripts', 'proteen_ajax_scripts', array(
    	'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ) );

}





function proteen_get_cc_avenue_form($atts) {
	$legend = '';
	if(isset($atts['title'])) {
		$legend = $atts['title'];
	}
	$price = 0;
	if(isset($atts['price'])) {
		$price = $atts['price'];
	}
	$prefix = "PROTEEN";
	if(isset($atts['order_prefix'])) {
		$prefix = $atts['order_prefix'];
	}
	// $response_handler = proteen_get_cc_avenue_handler_url();
	$response_handler = site_url('/').'cc-avenue-handler.php';
	ob_start(); ?>
		<div class="payment-integration-form">
			<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" id="proteen_form_handler">

				<?php $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
					$order_suffix = substr(str_shuffle($str_result),0, 6);
					$order_id = "SKILL".$order_suffix;
				?>
				<input type="hidden" name="email_subject" value="<?php echo $legend; ?>">
				<input type="hidden" name="order_id" value="<?php echo $order_id; ?>"/>
				<input type="hidden" name="merchant_id" value="130338">
				<input type="hidden" name="language" value="EN">
				<input type="hidden" name="amount" value="<?php echo $price; ?>">
				<input type="hidden" name="currency" value="INR">
				<input type="hidden" name="redirect_url" value="<?php echo $response_handler; ?>">
				<input type="hidden" name="cancel_url" value="<?php echo $response_handler; ?>">
				<input type="hidden" name="action" value="proteen_cc_avenue">

				<div class="form-group">
					<!-- <label for="customer_name">Name:</label> -->
					<input type="text" class="form-control" id="name" placeholder="Name" tabindex="1" name="name" required="required">
				</div>
				<div class="form-group">
					<!-- <label for="customer_email">Email:</label> -->
					<input type="email" class="form-control" id="email" placeholder="Email" tabindex="2" name="email" required="required">
				</div>
				<div class="form-group">
					<!-- <label for="phone">Phone:</label> -->
					<input type="text" class="form-control" id="phone" placeholder="Phone" tabindex="3" name="phone" required="required">
				</div>
				<div>
					<button class="btn-payment" type="submit">Pay Now</button>
				</div>
			</form>
		</div>
	<?php return ob_get_clean();
}
add_shortcode('get_cc_avenue_form', 'proteen_get_cc_avenue_form');



function proteen_get_cc_avenue_resonse() {


	if(isset($_POST['encResp'])) {
		$workingKey='D9A289CF81E7D4C3FF63AE3AFD13F9A4';
		$encResponse=$_POST['encResp'];

		$rcvdString=decrypt($encResponse, $workingKey);
		$order_status="";
		$decryptValues=explode('&', $rcvdString);
		$dataSize=sizeof($decryptValues);
		$orderId=false;
		for ($i = 0; $i < $dataSize; $i++) {
			$information = explode('=', $decryptValues[$i]);
			if ($i==3) {
				$order_status=$information[1];
			}
		}

		ob_start(); ?>

		<div class="bg-wrapper" style="padding-bottom: 20px">
			<?php if($order_status==="Success") { proteen_add_transaction_data($decryptValues);  ?>
			<div class="thank-text">

			<p>Thank you for subscribing to ProTeen. Your transaction is successful and we have received your payment.</p> 

			<p>ProTeen Team will reach out to you shortly.</p>

			<p>In case of any queries, mail us on info@proteen.com or call/WhatsApp us on +91 8657386646.</p>


			</div>
			<?php } elseif ($order_status==="Aborted") { ?>
			<div class="thank-text">			
				<p>Thank you for showing interest in ProTeen. We will keep you posted regarding the status of your payment through e-mail.</p>

				<p>ProTeen Team will reach out to you shortly.</p>

				<p>In case of any queries, mail us on info@proteen.com or call/WhatsApp us on +91 8657386646.</p>
			</div>
			<?php } elseif ($order_status==="Failure") { ?>
			<div class="thank-text">
				<p>Thank you for showing interest in ProTeen. However, the transaction has been declined.</p>

				<p>ProTeen Team will reach out to you shortly.</p>

				<p>In case of any queries, mail us on info@proteen.com or call/WhatsApp us on +91 8657386646.</p>

			</div>
			<?php } else { ?>
			<div class="thank-text">
				<p>Thank you for showing interest in ProTeen. However, we see some security error.</p> 

				<p>In case of any queries, mail us on info@proteen.com or call/WhatsApp us on +91 8657386646.</p>
			</div>
			<?php } ?>
			<div class="table-wrapper" style="margin-bottom: 10px">
				<table class="table table-bordered table-responsive">
					<tbody>
						<?php
							for ($i = 0; $i < $dataSize; $i++) {
								$information = explode('=', $decryptValues[$i]);
								if($information[0] == 'order_id' || $information[0] == 'tracking_id' || $information[0] == 'bank_ref_no' || $information[0] == 'order_status' || $information[0] == 'payment_mode' || $information[0] == 'trans_date' || $information[0] == 'amount' || $information[0] == 'delivery_tel'){
									if($information[0] == 'order_id'){
									  echo '<tr><td>Order Id</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
									if($information[0] == 'tracking_id'){
									  echo '<tr><td>Tracking Id</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
									if($information[0] == 'bank_ref_no'){
									  echo '<tr><td>Bank Ref No</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
									if($information[0] == 'order_status'){
									  echo '<tr><td>Order Status</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
									if($information[0] == 'payment_mode'){
									  echo '<tr><td>Payment Mode</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
									if($information[0] == 'trans_date'){
									  echo '<tr><td>Transaction Date</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
									if($information[0] == 'amount'){
									  echo '<tr><td>Amount</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
									if($information[0] == 'delivery_tel'){
									  echo '<tr><td>Contact</td>';
									  echo'<td>'.$information[1].'</td></tr>';
									}
							
								}
							}
							?>
					</tbody>
				</table>
			</div>
		</div>

		<?php return ob_get_clean();

	}

}
add_shortcode('get_cc_avenue_response', 'proteen_get_cc_avenue_resonse');



function decrypt($encryptedText,$key)
{
	$secretKey         = hextobin(md5($key));
	$initVector         =  pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	$encryptedText      = hextobin($encryptedText);
	$decryptedText         =  openssl_decrypt($encryptedText,"AES-128-CBC", $secretKey, OPENSSL_RAW_DATA, $initVector);
	return $decryptedText;
}










function encrypt($plainText,$key)
{
    $secretKey = hextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $encryptedText = openssl_encrypt($plainText, "AES-128-CBC", $secretKey, OPENSSL_RAW_DATA, $initVector);
    $encryptedText = bin2hex($encryptedText);
    return $encryptedText;
}



// *********** Padding Function *********************
function pkcs5_pad($plainText, $blockSize)
{
    $pad = $blockSize - (strlen($plainText) % $blockSize);
    return $plainText . str_repeat(chr($pad), $pad);
}

// ********** Hexadecimal to Binary function for php 4.0 version ********
function hextobin($hexString)
{
    $length = strlen($hexString);
    $binString = "";
    $count = 0;
    while ($count < $length) {
        $subString = substr($hexString, $count, 2);
        $packedString = pack("H*", $subString);
        if ($count == 0) {
            $binString = $packedString;
        } 
        else {
            $binString .= $packedString;
        }
        
        $count += 2;
    }
    return $binString;
}


function proteen_initiate_cc_avenue_process() {


	
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
add_action( 'admin_post_nopriv_proteen_cc_avenue', 'proteen_initiate_cc_avenue_process' );
add_action( 'admin_post_proteen_cc_avenue', 'proteen_initiate_cc_avenue_process' );



add_action( 'wp_ajax_proteen_validate_coupon', 'proteen_validate_coupon_handler' );
add_action( 'wp_ajax_nopriv_proteen_validate_coupon', 'proteen_validate_coupon_handler' );
function proteen_validate_coupon_handler() {

	global $woocommerce;

	$coupon_code = $_POST['code'];
	$selected_plan = $_POST['plan_id'];
	$plan_ids = explode(",", $selected_plan);
	$args = array(
		'post_type'        => 'proteen_coupon',
		'posts_per_page'   => 1,
		'orderby' => 'title',
		'order'   => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'coupon_code',
				'value' => $coupon_code,
			),
			array (
				'key' => 'expiry_date',
				'value' => date('Y/m/d',time()),
				'compare' => '>=',
				'type'    => 'DATE'
			),
		),
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$coupn_id = $query->posts[0]->ID;

		$plan_id = get_post_meta($coupn_id, 'select_plan', true);


		if(in_array($plan_id, $plan_ids)) {

			$plan_price = get_post_meta($plan_id, 'plan_price', true);
			$total_redeem = get_post_meta($coupn_id, 'coupon_redeem_count', true);
			$coupon_redeemed = get_post_meta($coupn_id, 'coupon_redeemed', true);


			if($total_redeem > $coupon_redeemed) {

				$coupon_amount = get_post_meta($coupn_id, 'coupon_amount', true);
				$coupon_type = get_post_meta($coupn_id, 'coupon_type', true);
				$discount_amount = 0;

				if($coupon_type == 'flat') {
					$discount_amount = ($plan_price * $coupon_amount)/100;
				} else {
					$discount_amount = $coupon_amount;
				}

				echo json_encode(array('success' => true, 'discount' => round($discount_amount), 'coupon_id'=>$coupn_id));
				wp_die();
			}

		}

	}
	echo json_encode(array('success' => false));
	wp_die();

}



require_once( plugin_dir_path( __FILE__ ).'/inc/proteen-pricing-table.php');
require_once( plugin_dir_path( __FILE__ ).'/inc/proteen-checkout-handler.php');
require_once( plugin_dir_path( __FILE__ ).'/inc/proteen-transaction-filter.php');