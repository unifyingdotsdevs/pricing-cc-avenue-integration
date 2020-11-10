<?php
add_action( 'restrict_manage_posts', 'get_plan_selection' );
// restrict_manage_posts
function get_plan_selection() {
	global $wpdb, $table_prefix;
	if(isset($_GET['meta_plan_type']) && $_GET['meta_plan_type'] != '0') {
		$selected_plan_type = 	$_GET['meta_plan_type'];
	}
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'proteen_transaction') {
		$args_plan = array(
			'post_type'        => 'proteen_plan',
			'posts_per_page'   => -1,
			'orderby' => 'title',
			'order'   => 'ASC',
		);
		$query_plan = new WP_Query( $args_plan );
		if ( $query_plan->have_posts() ) { ?>
			<select name="meta_plan_type">
				<option value="0">Select Plan</option>
				<?php
				while ( $query_plan->have_posts() ) {
					$query_plan->the_post();
					$selected = '';
					if($selected_plan_type == get_the_ID()) {
						$selected = "selected='selected'";
					} ?>
					<option value="<?php echo the_ID(); ?>" <?php echo $selected; ?>><?php the_title(); ?></option>
					<?php
				} ?>
			</select>
			<?php
		}

		?>
	<?php }
}



add_action( 'pre_get_posts', 'wpse454363_posts_filter', 999 );
function wpse454363_posts_filter( $query ){
	global $pagenow;
	if ( ! is_admin() && !$query->is_main_query() )
		return;

	if( !('proteen_transaction' === $query->query['post_type']  ) ){
		return $query;
	}
	$meta_query = array();
	if ( 'proteen_transaction' == $_GET['post_type'] && is_admin() && $pagenow=='edit.php') {
		
		if (isset($_GET['meta_plan_type']) && $_GET['meta_plan_type'] != '0') {

	        $query->set( 'meta_key', 'plan_id' );
	        $query->set( 'meta_query', array(
	            array(
	                'key'     => 'plan_id',
	                'compare' => '=',
	                'value'   => $_GET['meta_plan_type'],
	            )
	        ) );
	    }
	}

	return $query;
}