<?php
function get_proteen_pricing_table($atts) {
	ob_start();

	$legend = 'Monthly';
	if(isset($atts['type'])) {
		$legend = $atts['type'];
	}

	$subscription_lead = acf_get_fields('group_5ecf44ba62d90');

	$args = array(
		'post_type'        => 'proteen_plan',
		'posts_per_page'   => -1,
		'orderby' => 'title',
		'order'   => 'ASC',
		'tax_query' => array(
			array(
				'taxonomy' => 'plan_type',
				'terms' => strtolower($legend),
				'field' => 'slug',
				'operator' => 'IN'
			)
		),
	);
	$query = new WP_Query( $args );
	$pricing_legend = array_values($subscription_lead);

	if($legend == "Yearly") {
		$legend = "Year";
	}
	if($legend == "Monthly") {
		$legend = "Month";
	}

	$legendly = 'Month';

	if($legend == "Year") {
		$legendly = "Year";
	}

	if ( $query->have_posts() ) { ?>
		<div class="proteen_pricing web">
			<table>
				<thead>
					<tr>
						<th style="background:#fff"><center><img src="https://staging.proteenlife.com/wp-content/uploads/2020/04/Proteen-Logo-Black.svg" width="80%"></center></th>
						<?php
							while ( $query->have_posts() ) {
								$query->the_post();
									$is_highlted = get_post_meta(get_the_ID(), 'is_highlted', true);
									$additional_text = get_post_meta(get_the_ID(), 'additional_text', true);
									$plan_price = (int)get_post_meta(get_the_ID(), 'plan_price', true);
									$old_price = (int)get_post_meta(get_the_ID(), 'old_price', true);
									$plan_name = get_post_meta(get_the_ID(), 'plan_name', true);

								?>
								<th class="<?php echo ($is_highlted) ? "superStyle": ""; ?>">
									<span class="price_node" data-plan_id="<?php echo get_the_ID(); ?>" data-plan_pricing="<?php echo $plan_price; ?>" data-plan_name="<?php echo $plan_name; ?>" data-plan_type="<?php echo $legendly; ?>"></span>
									<center>
										<h4 class="panel-title price"><?php echo $plan_name; ?></h4>
											<?php
												if($plan_price != 0 && $plan_price != -1) {?>
													<p class="text-muted text-sm">
														<span class="strikethrough">INR <?php echo number_format($old_price); ?>/<?php echo $legend; ?></span><br><span class="originalprice">INR <?php echo number_format($plan_price); ?><sub>/<?php echo $legend; ?></sub></span>
													</p>
												<?php } else { ?>
													<p class="text-muted text-sm additional_text">
													<?php 
													echo $additional_text; ?>
													</p>
												<?php
												}
											?>
									</center>
								</th>
							<?php }
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					$counter = 0;
					echo "<tr>";
						echo "<td>Features</td>";
						while ( $query->have_posts() ) {
							$query->the_post();
							$is_highlted = get_post_meta(get_the_ID(), 'is_highlted', true);
							$button_text = get_post_meta(get_the_ID(), 'button_text', true);
							$button_action = get_post_meta(get_the_ID(), 'button_action', true); ?>
							<td class="<?php echo ($is_highlted) ? "superStyle-block": ""; ?>">
							<?php
							if($button_action == '') {
								$button_action = 'javascript:void(0)';
							}
							echo '<a href='.$button_action.' class="btn btn-info" style="margin-top:10px; margin-bottom:10px;">'.$button_text.'</a>';
							echo "</td>";
						}
					echo "</tr>";
					foreach ($pricing_legend as $key => $legend_item) {
						// $plan_id = $query->posts[$counter]->ID;
						echo "<tr>";
							echo "<td>".$legend_item['label']."</td>";
							while ( $query->have_posts() ) {
								$query->the_post();
								echo "<td>";
								$meta = get_post_meta(get_the_ID(), $legend_item['name'], true);
								if($meta == 'yes') {
									echo '<i style="color:limegreen" class="fa fa-check-circle"></i>';
								} else if ($meta == 'no') {
									echo '<i style="color:red" class="fa fa-times-circle"></i>';
								} else {
									echo $meta;
								}
								echo "</td>";
							}
						echo "</tr>";
						$counter++;
					} ?>
				</tbody>
			</table>
		</div>


		<!--mobile view-->
		<div class="proteen_pricing mobile">

				<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						$is_highlted = get_post_meta(get_the_ID(), 'is_highlted', true);
						$additional_text = get_post_meta(get_the_ID(), 'additional_text', true);
						$plan_name = get_post_meta(get_the_ID(), 'plan_name', true);
						$plan_price = (int)get_post_meta(get_the_ID(), 'plan_price', true);
						$old_price = (int)get_post_meta(get_the_ID(), 'old_price', true);
						?>
						<div class="pricing_element <?php echo ($is_highlted) ? "superStyle-block": ""; ?>">

							<div class="pricing_header">
								<span class="price_node" data-plan_id="<?php echo get_the_ID(); ?>" data-plan_pricing="<?php echo $plan_price; ?>" data-plan_name="<?php echo $plan_name; ?>" data-plan_type="<?php echo $legendly; ?>"></span>
								<center>
									<h4 class="panel-title price"><?php echo $plan_name; ?></h4>
										<?php
											if($plan_price != 0 && $plan_price != -1) {?>
												<p class="text-muted text-sm">
													<span class="strikethrough">INR <?php echo $old_price; ?>/<?php echo $legend; ?></span><br><span class="originalprice">INR <?php echo $plan_price; ?><sub>/<?php echo $legend; ?></sub></span>
												</p>
											<?php } else { ?>
												<p class="text-muted text-sm additional_text">
												<?php
													echo $additional_text;
												?>
												</p>
											<?php
											}
										?>
								</center>
							</div>
							<div class="plan-action-btn <?php echo ($is_highlted) ? "superStyle-block": ""; ?>">
							<?php
							$button_text = get_post_meta(get_the_ID(), 'button_text', true);
							$button_action = get_post_meta(get_the_ID(), 'button_action', true);
							if($button_action == '') {
								$button_action = 'javascript:void(0)';
							}
							echo '<a href='.$button_action.' class="btn btn-info" style="margin-top:10px; margin-bottom:10px;">'.$button_text.'</a>';
							echo "</div>";
							?>
							<div class="plan-element-wrapper">
							<?php
							foreach ($pricing_legend as $key => $legend_item) {

								echo "<div class='plan-element'>";
									$meta = get_post_meta(get_the_ID(), $legend_item['name'], true);
									if($meta == 'yes') {
										echo '<i style="color:limegreen" class="fa fa-check-circle"></i>' . $legend_item['label'];
									} else if ($meta == 'no') {
										echo '<i style="color:red" class="fa fa-times-circle"></i>' .$legend_item['label'];
									} else {
										if($meta == "Limited" || $meta == "Unlimited") {
											echo '<i style="color:limegreen" class="fa fa-check-circle"></i>'.$meta. ' Parent Access';
										} else {
											echo $meta;
										}
									}
								echo "</div>";


								$counter++;
							}
							echo "</div>";
						echo "</div>";
					}

				?>

		</div>

		<!--/mobile view-->
		<?php
	}



	wp_reset_query();
	return ob_get_clean();

}
add_shortcode('proteen_pricing_table','get_proteen_pricing_table');










function get_proteen_additional_package($atts) {
	ob_start();
	$legend = '';
	if(isset($atts['type'])) {
		$legend = $atts['type'];
	}

	$subscription_lead = acf_get_fields('group_5ecf44ba62d90');

	$args = array(
		'post_type'        => 'proteen_plan',
		'posts_per_page'   => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'plan_type',
				'terms' => strtolower($legend),
				'field' => 'slug',
				'operator' => 'IN'
			)
		),
	);
	$query = new WP_Query( $args );

	$pricing_legend = array_values($subscription_lead);


	if ( $query->have_posts() ) { ?>
		<div class="vc_row wpb_row vc_row-fluid pricing_addon vc_row-o-equal-height vc_row-flex">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post(); 
					$additional_text = get_post_meta(get_the_ID(), 'additional_text',true);
					$button_text = get_post_meta(get_the_ID(), 'button_text',true);
					$plan_price = get_post_meta(get_the_ID(), 'plan_price',true);
					$old_price = get_post_meta(get_the_ID(), 'old_price',true);
					$button_action = get_post_meta(get_the_ID(), 'button_action', true);
					if($button_action == '') {
						$button_action = 'javascript:void(0)';
					}

				?>
				<div class="wpb_column vc_column_container vc_col-sm-4">
					<div class="vc_column-inner">
						<div class="princing_addon_package" id="addon_plan_<?php echo the_ID(); ?>">
							<h4><?php the_title(); ?></h4>
							<div class="package_info">
								<div class="old_price">
									<span class="strikethrough">INR <?php echo number_format($old_price); ?></span>
								</div>
								<div class="pricing_original_price">
									<span>INR <?php echo number_format($plan_price); ?> <span>(One-Time)</span></span>
								</div>
								<div class="package_selection">
									<a href="<?php echo $button_action; ?>" class="btn btn-info addon-selection"><?php echo $button_text; ?></a>
									<span class="add_on_price_node" data-plan_id="<?php echo get_the_ID(); ?>" data-plan_pricing="<?php echo $plan_price; ?>" data-plan_name="<?php echo the_title(); ?>"></span>
								</div>
								<div class="addon_text">
									Available with Essential and Premium Pack
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php
			} ?>
		</div>
	<?php
	}

	wp_reset_query();
	return ob_get_clean();
}
add_shortcode('proteen_additional_package','get_proteen_additional_package');



function get_proteen_checkout_table() {
	ob_start(); ?>
	<form>
		<table class="checkout-table">
			<tr class="primary-pack pack-title">
				<td colspan="2">
					<span class="pack-name">ProTeen Pack</span>
				</td>
			</tr>
			<tr class="plan primary_plan">
				<td class="package">
					<span class="plan-name">Essential</span> (<span class="plan-type">Annually</span>)
				</td>
				<td>
					INR <span class="primary-price">1999</span>
				</td>
			</tr>
			<tr class="add-on-titles pack-title">
				<td colspan="2">
					<span class="pack-name">Add-ons</span>
				</td>
			</tr>
			<tr class="pack-title book">
				<td colspan="2">
					<span class="pack-name">Book</span>
				</td>
			</tr>
			<tr class="addon-item book">
				<td>
					<span class="plan-name">13 Steps to Bloody Good Marks</span>
				</td>
				<td>
					FREE
				</td>
			</tr>
			<tr class="promocode-row">
				<td id="promocode">
					<span>Enter Promo Code</span>
					<input type="hidden" id="selected_plan" name="selected_plan">
					<input type="hidden" id="coupon_id" name="coupon_id">
					<input type="text" id="ccode" name="ccode" placeholder="Enter Promo Code">
					<button type="button" class="coupon-btn" id="coupon-btn">Apply</button>
					<span id="coupon-status"></span>
				</td>
				<td id="promo-amount">
					<span id="discount-amount">- INR <span>0</span></span>
				</td>
			</tr>
			<tr class="total">
				<td>Total Amount</td>
				<td>INR <span id="checkout-price"></span></td>
			</tr>
		</table>
		<button type="button" class="btn-payment pull-right wow-modal-id-11">Proceed to payment</button>
	</form>
	<?php
	return ob_get_clean();
}


add_shortcode('proteen_checkout_table', 'get_proteen_checkout_table');