<?php
/* Techwatt Testimony Manager */

/* ---------------- CPT Registration ---------------- */
function techwatt_register_testimony_cpt() {
	$labels = array(
		'name' => 'Testimonies',
		'singular_name' => 'Testimony',
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'supports' => array('title', 'editor', 'author', 'thumbnail'),
		'menu_icon' => 'dashicons-testimonial',
	);
	register_post_type('testimony', $args);
}
add_action('init', 'techwatt_register_testimony_cpt');

/* ---------------- Frontend Add/Edit Form ---------------- */
function techwatt_testimony_form_shortcode($atts) {
	$atts = shortcode_atts(array('id' => 0), $atts);
	$post_id = intval($atts['id']);
	$current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $children = null;

    if(!empty($userID)){
        $children = get_user_meta($userID, 'tw_userdata',true)['children'] ?? [];
    }
    
	$title_val = $post_id ? get_the_title($post_id) : '';
	$content_val = $post_id ? get_post_field('post_content', $post_id) : '';

	ob_start();
	?>
	<form class="tm-form" id="tm-form" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>">
		<?php wp_nonce_field('tm_form_action', 'tm_form_nonce'); ?>
		<input type="hidden" name="postid" value="<?php _e($post_id,'tw'); ?>">
		<input type="hidden" name="action" value="tm_form">

		<p><label>Title<span class="break"></span><input type="text" name="tm_title" value="<?php echo esc_attr($title_val); ?>" required></label></p>
		<p><label>Testimony<span class="break"></span><textarea name="tm_content" rows="3" required><?php echo esc_textarea($content_val); ?></textarea></label></p>
        <p><label>Name<span class="break"></span>
            <?php
            if(isset($children) && !empty($children)){
                        echo '<select name="kid_name">';
                        echo '<option value="">Choose a name</option>';
                        foreach($children as $childKey=>$child){
                            $sel = (strtolower($data['kid_name']) === strtolower($child["name"])) ? 'selected' : '';
                            echo '<option value="'.esc_attr($child["name"]).'" '.$sel.'>'.esc_html($child["name"]).'</option>';
                        }
                        echo '</select>';
            }else{
                echo '<input type="text" name="kid_name" value="'.esc_attr($data['kid_name']).'" style="width:100%"></label>';
            }
            ?>            
        </p>
		<p><button type="button" class="canceltmForm btn btn-secondary">Cancel</button> <button type="submit" class="btn btn-primary">Submit</button></p>
	</form>
<?php
	return (isset($msg) ? $msg : '') . ob_get_clean();
}
add_shortcode('testimony_form', 'techwatt_testimony_form_shortcode');
/////////AJAX ///////////////////////////////


/* ---------------- Manage Shortcode ---------------- */
function techwatt_testimony_manage_shortcode() {
	if (!is_user_logged_in()) return '<div class="tm-error">Login required to manage testimonies.</div>';

	$user = wp_get_current_user();
	$q = new WP_Query(array(
		'post_type' => 'testimony',
		'author' => $user->ID,
		'posts_per_page' => -1,
	));

	ob_start();
	?>
	<div class="tm-manage">
	<?php if ($q->have_posts()) : ?>
		<ul>
			<?php while ($q->have_posts()) : $q->the_post(); ?>
				<li><?php the_title(); ?> —
					<a href="<?php echo esc_url(add_query_arg('tm_edit_id', get_the_ID(), get_permalink())); ?>">Edit</a>
					| <a href="#" class="tm-delete" data-id="<?php the_ID(); ?>">Delete</a>
				</li>
			<?php endwhile; ?>
		</ul>
	<?php else : ?>
		<p>No testimonies yet.</p>
	<?php endif; wp_reset_postdata(); ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('testimony_manage', 'techwatt_testimony_manage_shortcode');

/* ---------------- Display Shortcode ---------------- */
function techwatt_testimonies_view_shortcode($atts) {
	$atts = shortcode_atts(array('view' => 'grid', 'posts_per_page' => 6), $atts);
	$q = new WP_Query(array(
		'post_type' => 'testimony',
		'posts_per_page' => intval($atts['posts_per_page']),
		'post_status' => 'publish',
	));

	ob_start();
	
	if ($q->have_posts()) :
		//echo '<div class="tm-container tm-' . esc_attr($atts['view']) . '">';
		echo '<div class="tm-container ' . ($atts['view'] === 'list' ? 'tm-list' : 'tm-grid') . '">';
		while ($q->have_posts()) : $q->the_post(); 
		$author = get_post_meta(get_the_ID(), 'author_name', true);
		?>
			<article class="tm-item">
				<?php if (has_post_thumbnail()) the_post_thumbnail('medium'); ?>
				<h4><?php the_title(); ?></h4>
				<div><?php the_excerpt(); ?></div>
				<?php if ($author) : ?>
					<div class="tm-author">— <?php echo esc_html($author); ?></div>
				<?php endif; ?>
			</article>
		<?php endwhile;
		echo '</div>';
	else :
		echo '<div class="alert alert-info">No testimonies found.</divp>';
	endif;
	
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode('testimonies_view', 'techwatt_testimonies_view_shortcode');

/* ---------------- AJAX Delete ---------------- */
function techwatt_ajax_delete_testimony() {
	check_ajax_referer('tm_nonce', 'security');
	$id = intval($_POST['id']);
	$post = get_post($id);
	if (!$post || $post->post_type !== 'testimony') wp_send_json_error('Invalid post');
	if (get_current_user_id() !== (int)$post->post_author && !current_user_can('delete_others_posts')) {
		wp_send_json_error('Permission denied');
	}
	wp_delete_post($id, true);
	wp_send_json_success();
}
add_action('wp_ajax_tm_delete_testimony', 'techwatt_ajax_delete_testimony');
?>