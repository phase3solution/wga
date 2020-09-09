<?php
// generate question section
function advisory_generate_sfiats_form_users() {
	if (is_admin() && is_edit_page()) {
		$data = '';
		$post_id = @$_GET['post'];
		// advisory_sfiats_add_default_post_meta($post_id);
		$company_id = get_post_field('assigned_company', $post_id);
		$company = get_term_meta($company_id, 'company_data', true);
		$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
		return [''=>'Select User'] + $users;
	}
}
function advisory_get_sfiats_total($default, $questions) {
	$total = 0;
	if (!empty($default['avg'])) unset($default['avg']);
	$loop = count($questions);
	for ($i=1; $i <= $loop; $i++) { 
		$rating = !empty($default['rating_'. $i]) ? intval($default['rating_'. $i]) : 0;
		$total += $rating;
	}
	return $total;
}
add_action('wp_ajax_sfiats_save', 'ajax_sfiats_save');
function ajax_sfiats_save() {
	check_ajax_referer('advisory_nonce', 'security');
	$data = $_REQUEST['data'];
	$meta_key = $_REQUEST['meta'];
	$post_id = $_REQUEST['post_id'];
	if (update_post_meta($post_id, $meta_key, $data)) { wp_send_json(true); }
	wp_send_json($data);
}

add_action('wp_ajax_reset_afiats', 'advisory_ajax_reset_afiats');
function advisory_ajax_reset_afiats() {
	check_ajax_referer('advisory_nonce', 'security');
	$form_id = isset($_REQUEST['form_id']) && !empty($_REQUEST['form_id']) ? $_REQUEST['form_id'] : 0;
	if ($form_id) {
		if (!advisory_has_survey_delete_permission($form_id)) wp_send_json(false);
		$deleted = delete_post_meta($form_id, 'poles');
		if ($deleted != false) wp_send_json(true);
	}
	wp_send_json(false);
}

add_action('wp_ajax_validate_form_submission_sfiats', 'ajax_validate_form_submission_sfiats');
function ajax_validate_form_submission_sfiats() {
	check_ajax_referer('advisory_nonce', 'security');
	$form_id = $_REQUEST['form_id'];
	if (advisory_validate_form_submission_sfiats($form_id)) wp_send_json(true);
	wp_send_json(false);
}
add_action('wp_ajax_publish_sfiats', 'advisory_ajax_publish_sfiats');
function advisory_ajax_publish_sfiats() {
	check_ajax_referer('advisory_nonce', 'security');
	$post_id = !empty($_REQUEST['form_id']) ? $_REQUEST['form_id'] : false;
	if (advisory_publish_sfiats($post_id)) wp_send_json(true);
	wp_send_json(false);
}
function advisory_publish_sfiats($post_id) {
	if (!$post_id) return false;
	if (metadata_exists('post', $post_id, 'archive_date' )) delete_post_meta( $post_id, 'archive_date');
	add_post_meta( $post_id, 'archive_date', time(), false );

	$update = wp_update_post(['ID' => $post_id, 'post_status' => 'archived']);
	if (is_wp_error($update)) return false;
	return true;
}
function advisory_sfiats_add_default_post_meta($post_id, $user_id='GC001') {
	$opts = Array
	(
	    'display_name' => 'Azucena Boehman - Grey County',
	    'desc' => 'IT Infrastructure Health Check',
	    'icon' => 'https://wgadvisory.ca/IHC-Portal/wp-content/uploads/2017/07/mechanical-gears-.png',
	    'user' => $user_id,
	    'questions' => Array
	        (
	            '1' => Array ('name' => 'Data Security Vision, Security Mission', 'desc' => '', ),
	            '2' => Array ('name' => 'Security Objectives', 'desc' => '', ),
	            '3' => Array ('name' => 'Security Responsibilities / Organizational Responsibilities', 'desc' => '', ),
	            '4' => Array ('name' => 'Governance Principles', 'desc' => '', ),
	            '5' => Array ('name' => 'Passwords Policy', 'desc' => '', ),
	        ),
	);
	update_post_meta( $post_id, 'form_opts', $opts);
}

function advisory_sfiats_get_skills_history($user_id, $company_id, $default=[]) {
	$data = '';
	if (!$company_id) $company_id = advisory_get_user_company_id();
	$postType = 'sfiats';
    $posts = new WP_Query([
        'post_type' => $postType,
        'post_status' => 'archived',
        'posts_per_page' => -1,
        'meta_query' => [
        	'relation' => 'AND',
        	['key' => 'wga-user', 'value' => $user_id],
        	['key' => 'assigned_company', 'value' => $company_id]
        ],
        // 'fields' => 'ids',
    ]);
    $data .= '<select class="form-control bg-gap technical_assessments">';
        $data .= '<option value="">Technical Assessments</option>';
        if (!empty($posts->posts)) {
            foreach ($posts->posts as $post) {
	            $data .= '<option value="'.$post->ID.'"'.$selected.'>'.$post->post_title.'</option>';
            }
        }
    $data .= '</select>';

    // $data .= '<div class="btn-group" data-toggle="tooltip" title="Technical Survey" style="width:100%">
    //     <button class="sfiarArchiveBtn dropdown-toggle" href="#" data-toggle="dropdown"><span class="fa fa-down"></span> Technical Assessments</button>
    //     <ul class="dropdown-menu">';
    //     	if (!empty($posts->posts)) {
	   //          foreach ($posts->posts as $post) {
    //             	$data .= '<li><a class="technical_assessments" href="javascript:;" post_id='.$post->ID.'>'.$post->post_title.'</a></li>';
	   //          }
	   //      }
    //     $data .= '</ul>
    // </div>';
	return $data;
}
function advisory_sfiats_get_active_assessments($user_id, $company_id=0) {
	if (!$company_id) $company_id = advisory_get_user_company_id();
	$args = [
		'post_type' => 'sfiats',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'AND',
			['key' => 'wga-user', 'value' => $user_id, 'compare' => '='],
			['key' => 'assigned_company', 'value' => $company_id, 'compare' => '=']
		],
		'order' => 'DESC',
		'fields' => 'ids',
	];
	$query = new WP_Query($args);
	if (!empty($query->posts[0])) return $query->posts[0];
	return false;
}
function advisory_sfiats_get_last_assessments($user_id, $company_id=0) {
	if (!$company_id) $company_id = advisory_get_user_company_id();
	$args = [
		'post_type' => 'sfiats',
		'post_status' => ['publish', 'archived'],
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'AND',
			['key' => 'wga-user', 'value' => $user_id, 'compare' => '='],
			['key' => 'assigned_company', 'value' => $company_id, 'compare' => '=']
		],
		'order' => 'DESC',
		'fields' => 'ids',
	];
	$query = new WP_Query($args);
	if (!empty($query->posts[0])) return $query->posts[0];
	return false;
}
function advisory_sfiats_get_published_assessments($user_id, $company_id=0) {
	if (!$company_id) $company_id = advisory_get_user_company_id();
	$args = [
		'post_type' => 'sfiats',
		'post_status' => 'archived',
		'posts_per_page' => 1,
		'meta_query' => [
			'relation' => 'AND',
			['key' => 'wga-user', 'value' => $user_id, 'compare' => '='],
			['key' => 'assigned_company', 'value' => $company_id, 'compare' => '=']
		],
		'order' => 'DESC',
		'fields' => 'ids',
	];
	$query = new WP_Query($args);
	if (!empty($query->posts[0])) return $query->posts[0];
	return false;
}
function advisory_sfiats_is_valid_publishing_for_user($user_id, $company_id=0) {
	if ($user_id && $company_id) {
		$post_id = advisory_sfiats_get_active_assessments($user_id, $company_id);
		if (!$post_id) return true; // NO ACTIVE ASSESSMENT
		return advisory_validate_form_submission_sfiats($post_id);
	}
	return false;
}
function advisory_sfiats_publish_for_user($user_id, $company_id=0) {
	if ($user_id && $company_id) {
		$post_id = advisory_sfiats_get_active_assessments($user_id, $company_id);
		if (!$post_id) return true; // NO ACTIVE ASSESSMENT
		return advisory_publish_sfiats($post_id);
	}
	return false;
}
function advisory_validate_form_submission_sfiats($post_id) {
	return true;
	$default = advisory_form_default_values($post_id, 'poles');
	if (!empty($default) && !empty($default['avg'])) return true;
	return false;
}



add_action( 'admin_init', 'my_admin_init' );
function my_admin_init() {
	add_filter( 'manage_sfiats_posts_columns', 'advisory_sfiats_manage_posts_columns' );
	add_action( 'manage_sfiats_posts_custom_column' , 'advisory_sfiats_manage_custom_columns', 10, 2 );
	add_action('restrict_manage_posts', 'advisory_sfiats_filter_columns');
	add_filter('parse_query', 'advisory_sfiats_filter_query');
}

// Add the custom columns to the book post type:
function advisory_sfiats_manage_posts_columns($columns) {
    unset( $columns['date'] );
    $columns['user'] = __( 'User', 'advisory' );
    $columns['company'] = __( 'Company', 'advisory' );
    $columns['date'] = __( 'Date', 'advisory' );
    return $columns;
}

// Add the data to the custom columns for the book post type:
function advisory_sfiats_manage_custom_columns( $column, $post_id ) {
	if ($column == 'company') {
		$company_id = (int) get_post_meta( $post_id, 'assigned_company', true );
        $term = get_term($company_id, 'company');
        if (!empty($term->name)) echo $term->name;
        else echo 'Undefined';
	} else if ($column == 'user') {
		$company_id = get_post_meta( $post_id, 'assigned_company', true );
		$company = get_term_meta($company_id, 'company_data', true);
    	$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
		$user_id = get_post_meta($post_id, 'wga-user', true);
		if ( !empty($users[$user_id]) ) echo $users[$user_id].' ('.$user_id.')';
		else echo 'Undefined';
	}
}

function advisory_sfiats_filter_columns() {
	global $typenow;
	$post_type = 'sfiats'; // change to your post type
	if ($typenow == $post_type) {
		$terms = get_terms(['taxonomy'=>'company']);
		if (!empty($terms)) {
			echo '<select name="company">';
				echo '<option value="">Select Company</option>';
				foreach ($terms as $term) {
					$company = get_term_meta($term->term_id, 'company_data', true);
					if (!empty($company['sfia_users'])) {
						$selected = !empty($_GET['company']) && $_GET['company'] == $term->term_id ? ' selected' : '';
						echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
					}
				}
			echo '</select>';
		}
	};
}

function advisory_sfiats_filter_query($query) {
	global $pagenow, $typenow;
	$post_type = 'sfiats';
	$meta_query = [];
	if ( $pagenow == 'edit.php' && $typenow = $post_type ) {
		$query->tax_query =[];
		if (isset($query->query_vars['company'])) unset($query->query_vars['company']);
		if (!empty($query->query['company'])) $meta_query['meta_query'] = [['key' => 'assigned_company', 'value' => $query->query['company'], 'compare' => '=']];
		if (!empty($meta_query)) $query->set('meta_query',$meta_query);
	}
	return $query;
}
function advisory_sfiats_get_class_by_value($val) {
    $class = '';
    switch ($val) {
        case '0': $class = 'bg-darkred'; break;
        case '1': $class = 'bg-red'; break;
        case '2': $class = 'bg-orange'; break;
        case '3': $class = 'bg-yellow'; break;
        case '4': $class = 'bg-green'; break;
        case '5': $class = 'bg-blue'; break;
        default : $class = 'bg-darkred'; break;
    }
    return $class;
}