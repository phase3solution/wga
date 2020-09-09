<?php 
function advisory_init() {
	// MTA
	// @since 3.0
	register_post_type('mta', array(
		'labels' => array(
			'name' => _x('MTA', 'post type general name', 'advisory'),
			'singular_name' => _x('MTA', 'post type singular name', 'advisory'),
			'menu_name' => _x('MTA', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('MTA', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'mta', 'advisory'),
			'add_new_item' => __('Add New MTA', 'advisory'),
			'new_item' => __('New MTA', 'advisory'),
			'edit_item' => __('Edit MTA', 'advisory'),
			'view_item' => __('View MTA', 'advisory'),
			'all_items' => __('All MTA', 'advisory'),
			'search_items' => __('Search MTA', 'advisory'),
			'parent_item_colon' => __('Parent MTA:', 'advisory'),
			'not_found' => __('No MTA found.', 'advisory'),
			'not_found_in_trash' => __('No MTA found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// MTA Risk
	// @since 5.0
	register_post_type('mtar', array(
		'labels' => array(
			'name' => _x('MTA Register', 'post type general name', 'advisory'),
			'singular_name' => _x('MTA Register', 'post type singular name', 'advisory'),
			'menu_name' => _x('MTA Register', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('MTA Register', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'mta Register', 'advisory'),
			'add_new_item' => __('Add New MTA Register', 'advisory'),
			'new_item' => __('New MTA Register', 'advisory'),
			'edit_item' => __('Edit MTA Register', 'advisory'),
			'view_item' => __('View MTA Register', 'advisory'),
			'all_items' => __('All MTA Registers', 'advisory'),
			'search_items' => __('Search MTA Register', 'advisory'),
			'parent_item_colon' => __('Parent MTA Register:', 'advisory'),
			'not_found' => __('No MTA Register found.', 'advisory'),
			'not_found_in_trash' => __('No MTA Register found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// IHC
	// @since 2.0
	register_post_type('ihc', array(
		'labels' => array(
			'name' => _x('IHC', 'post type general name', 'advisory'),
			'singular_name' => _x('IHC', 'post type singular name', 'advisory'),
			'menu_name' => _x('IHC', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('IHC', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'ihc', 'advisory'),
			'add_new_item' => __('Add New IHC', 'advisory'),
			'new_item' => __('New IHC', 'advisory'),
			'edit_item' => __('Edit IHC', 'advisory'),
			'view_item' => __('View IHC', 'advisory'),
			'all_items' => __('All IHC', 'advisory'),
			'search_items' => __('Search IHC', 'advisory'),
			'parent_item_colon' => __('Parent IHC:', 'advisory'),
			'not_found' => __('No IHC found.', 'advisory'),
			'not_found_in_trash' => __('No IHC found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// IHC Risk
	// @since 5.0
	register_post_type('ihcr', array(
		'labels' => array(
			'name' => _x('IHC Register', 'post type general name', 'advisory'),
			'singular_name' => _x('IHC Register', 'post type singular name', 'advisory'),
			'menu_name' => _x('IHC Register', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('IHC Register', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'ihcr', 'advisory'),
			'add_new_item' => __('Add New Risk', 'advisory'),
			'new_item' => __('New Risk', 'advisory'),
			'edit_item' => __('Edit Risk', 'advisory'),
			'view_item' => __('View Risk', 'advisory'),
			'all_items' => __('All Risk', 'advisory'),
			'search_items' => __('Search Risk', 'advisory'),
			'parent_item_colon' => __('Parent Risk:', 'advisory'),
			'not_found' => __('No Risk found.', 'advisory'),
			'not_found_in_trash' => __('No Risk found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// ITSM
	// @since 2.0
	register_post_type('itsm', array(
		'labels' => array(
			'name' => _x('ITSM', 'post type general name', 'advisory'),
			'singular_name' => _x('ITSM', 'post type singular name', 'advisory'),
			'menu_name' => _x('IT Management', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('ITSM', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'itsm', 'advisory'),
			'add_new_item' => __('Add New ITSM', 'advisory'),
			'new_item' => __('New ITSM', 'advisory'),
			'edit_item' => __('Edit ITSM', 'advisory'),
			'view_item' => __('View ITSM', 'advisory'),
			'all_items' => __('All ITSM', 'advisory'),
			'search_items' => __('Search ITSM', 'advisory'),
			'parent_item_colon' => __('Parent ITSM:', 'advisory'),
			'not_found' => __('No ITSM found.', 'advisory'),
			'not_found_in_trash' => __('No ITSM found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// CRA
	// @since 2.0
	register_post_type('cra', array(
		'labels' => array(
			'name' => _x('CRA', 'post type general name', 'advisory'),
			'singular_name' => _x('CRA', 'post type singular name', 'advisory'),
			'menu_name' => _x('Cloud Readiness', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('CRA', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'cra', 'advisory'),
			'add_new_item' => __('Add New CRA', 'advisory'),
			'new_item' => __('New CRA', 'advisory'),
			'edit_item' => __('Edit CRA', 'advisory'),
			'view_item' => __('View CRA', 'advisory'),
			'all_items' => __('All CRA', 'advisory'),
			'search_items' => __('Search CRA', 'advisory'),
			'parent_item_colon' => __('Parent CRA:', 'advisory'),
			'not_found' => __('No CRA found.', 'advisory'),
			'not_found_in_trash' => __('No CRA found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// BIA
	// @since 2.0
	register_post_type('bia', array(
		'labels' => array(
			'name' => _x('BIA', 'post type general name', 'advisory'),
			'singular_name' => _x('BIA', 'post type singular name', 'advisory'),
			'menu_name' => _x('BIA', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('BIA', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'bia', 'advisory'),
			'add_new_item' => __('Add New BIA', 'advisory'),
			'new_item' => __('New BIA', 'advisory'),
			'edit_item' => __('Edit BIA', 'advisory'),
			'view_item' => __('View BIA', 'advisory'),
			'all_items' => __('All BIA', 'advisory'),
			'search_items' => __('Search BIA', 'advisory'),
			'parent_item_colon' => __('Parent BIA:', 'advisory'),
			'not_found' => __('No BIA found.', 'advisory'),
			'not_found_in_trash' => __('No BIA found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// Risk
	// @since 2.0
	register_post_type('risk', array(
		'labels' => array(
			'name' => _x('Risk', 'post type general name', 'advisory'),
			'singular_name' => _x('Risk', 'post type singular name', 'advisory'),
			'menu_name' => _x('Risk', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('Risk', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'risk', 'advisory'),
			'add_new_item' => __('Add New Risk', 'advisory'),
			'new_item' => __('New Risk', 'advisory'),
			'edit_item' => __('Edit Risk', 'advisory'),
			'view_item' => __('View Risk', 'advisory'),
			'all_items' => __('All Risk', 'advisory'),
			'search_items' => __('Search Risk', 'advisory'),
			'parent_item_colon' => __('Parent Risk:', 'advisory'),
			'not_found' => __('No Risk found.', 'advisory'),
			'not_found_in_trash' => __('No Risk found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// @since 8.0
	register_post_type('bcp', [
		'labels' => [
			'name' => _x('BCP', 'post type general name', 'advisory'),
			'singular_name' => _x('BCP', 'post type singular name', 'advisory'),
			'menu_name' => _x('BCP', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('BCP', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'BCP', 'advisory'),
			'add_new_item' => __('Add New BCP', 'advisory'),
			'new_item' => __('New BCP', 'advisory'),
			'edit_item' => __('Edit BCP', 'advisory'),
			'view_item' => __('View BCP', 'advisory'),
			'all_items' => __('All BCP', 'advisory'),
			'search_items' => __('Search BCP', 'advisory'),
			'parent_item_colon' => __('Parent BCP:', 'advisory'),
			'not_found' => __('No BCP found.', 'advisory'),
			'not_found_in_trash' => __('No BCP found in Trash.', 'advisory'),
		],
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	]);
	// Project Risk Register
	// @since 3.0
	register_post_type('prr', array(
		'labels' => array(
			'name' => _x('Project Risk', 'post type general name', 'advisory'),
			'singular_name' => _x('Project Risk', 'post type singular name', 'advisory'),
			'menu_name' => _x('Project Risk', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('Risk', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'prr', 'advisory'),
			'add_new_item' => __('Add New Risk', 'advisory'),
			'new_item' => __('New Risk', 'advisory'),
			'edit_item' => __('Edit Risk', 'advisory'),
			'view_item' => __('View Risk', 'advisory'),
			'all_items' => __('All Risk', 'advisory'),
			'search_items' => __('Search Risk', 'advisory'),
			'parent_item_colon' => __('Parent Risk:', 'advisory'),
			'not_found' => __('No Risk found.', 'advisory'),
			'not_found_in_trash' => __('No Risk found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// DR Maturity
	// @since 4.0
	register_post_type('drm', array(
		'labels' => array(
			'name' => _x('DRM', 'post type general name', 'advisory'),
			'singular_name' => _x('DRM', 'post type singular name', 'advisory'),
			'menu_name' => _x('DRM', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('DRM', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'DRM', 'advisory'),
			'add_new_item' => __('Add New DRM', 'advisory'),
			'new_item' => __('New DRM', 'advisory'),
			'edit_item' => __('Edit DRM', 'advisory'),
			'view_item' => __('View DRM', 'advisory'),
			'all_items' => __('All DRM', 'advisory'),
			'search_items' => __('Search DRM', 'advisory'),
			'parent_item_colon' => __('Parent DRM:', 'advisory'),
			'not_found' => __('No DRM found.', 'advisory'),
			'not_found_in_trash' => __('No DRM found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// DR Maturity Risk Register
	// @since 3.0
	register_post_type('drmrr', array(
		'labels' => array(
			'name' => _x('DRM Register', 'post type general name', 'advisory'),
			'singular_name' => _x('DRM Register', 'post type singular name', 'advisory'),
			'menu_name' => _x('DRM Register', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('DRM Register', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'drmrr', 'advisory'),
			'add_new_item' => __('Add New Risk', 'advisory'),
			'new_item' => __('New Risk', 'advisory'),
			'edit_item' => __('Edit Risk', 'advisory'),
			'view_item' => __('View Risk', 'advisory'),
			'all_items' => __('All Risk', 'advisory'),
			'search_items' => __('Search Risk', 'advisory'),
			'parent_item_colon' => __('Parent Risk:', 'advisory'),
			'not_found' => __('No Risk found.', 'advisory'),
			'not_found_in_trash' => __('No Risk found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));	
	// Document Library
	// @since 4.0
	register_post_type('docl', array(
		'labels' => array(
			'name' => _x('docl', 'post type general name', 'advisory'),
			'singular_name' => _x('DOCL', 'post type singular name', 'advisory'),
			'menu_name' => _x('Document Library', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('DOCL', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'docl', 'advisory'),
			'add_new_item' => __('Add New Document', 'advisory'),
			'new_item' => __('New Document', 'advisory'),
			'edit_item' => __('Edit Document', 'advisory'),
			'view_item' => __('View Document', 'advisory'),
			'all_items' => __('All Document', 'advisory'),
			'search_items' => __('Search Document', 'advisory'),
			'parent_item_colon' => __('Parent Document:', 'advisory'),
			'not_found' => __('No Document found.', 'advisory'),
			'not_found_in_trash' => __('No Document found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// Data Management Maturity 
	// @since 4.0
	register_post_type('dmm', array(
		'labels' => array(
			'name' => _x('DMM', 'post type general name', 'advisory'),
			'singular_name' => _x('DMM', 'post type singular name', 'advisory'),
			'menu_name' => _x('DMM', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('DMM', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'DMM', 'advisory'),
			'add_new_item' => __('Add New DMM', 'advisory'),
			'new_item' => __('New DMM', 'advisory'),
			'edit_item' => __('Edit DMM', 'advisory'),
			'view_item' => __('View DMM', 'advisory'),
			'all_items' => __('All DRM', 'advisory'),
			'search_items' => __('Search DMM', 'advisory'),
			'parent_item_colon' => __('Parent DMM:', 'advisory'),
			'not_found' => __('No DMM found.', 'advisory'),
			'not_found_in_trash' => __('No DMM found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// Data Management Maturity Risk
	// @since 4.0
	register_post_type('dmmr', array(
		'labels' => array(
			'name' => _x('DMM Register', 'post type general name', 'advisory'),
			'singular_name' => _x('DMM Register', 'post type singular name', 'advisory'),
			'menu_name' => _x('DMM Register', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('DMM Register', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'dmmr', 'advisory'),
			'add_new_item' => __('Add New Risk', 'advisory'),
			'new_item' => __('New Risk', 'advisory'),
			'edit_item' => __('Edit Risk', 'advisory'),
			'view_item' => __('View Risk', 'advisory'),
			'all_items' => __('All Risk', 'advisory'),
			'search_items' => __('Search Risk', 'advisory'),
			'parent_item_colon' => __('Parent Risk:', 'advisory'),
			'not_found' => __('No Risk found.', 'advisory'),
			'not_found_in_trash' => __('No Risk found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// IT corporate Landscape
	// @since 6.0
	register_post_type('itcl', array(
		'labels' => array(
			'name' => _x('ITCL', 'post type general name', 'advisory'),
			'singular_name' => _x('ITCL', 'post type singular name', 'advisory'),
			'menu_name' => _x('ITCL MTA', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('ITCL', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'ITCL', 'advisory'),
			'add_new_item' => __('Add New ITCL', 'advisory'),
			'new_item' => __('New ITCL', 'advisory'),
			'edit_item' => __('Edit ITCL', 'advisory'),
			'view_item' => __('View ITCL', 'advisory'),
			'all_items' => __('All ITCL', 'advisory'),
			'search_items' => __('Search ITCL', 'advisory'),
			'parent_item_colon' => __('Parent ITCL:', 'advisory'),
			'not_found' => __('No ITCL found.', 'advisory'),
			'not_found_in_trash' => __('No ITCL found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// Cybersecurity Self-Assessment
	// @since 7.0
	register_post_type('csa', array(
		'labels' => array(
			'name' => _x('CSA', 'post type general name', 'advisory'),
			'singular_name' => _x('CSA', 'post type singular name', 'advisory'),
			'menu_name' => _x('CSA', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('CSA', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'CSA', 'advisory'),
			'add_new_item' => __('Add New CSA', 'advisory'),
			'new_item' => __('New CSA', 'advisory'),
			'edit_item' => __('Edit CSA', 'advisory'),
			'view_item' => __('View CSA', 'advisory'),
			'all_items' => __('All CSA', 'advisory'),
			'search_items' => __('Search CSA', 'advisory'),
			'parent_item_colon' => __('Parent CSA:', 'advisory'),
			'not_found' => __('No CSA found.', 'advisory'),
			'not_found_in_trash' => __('No CSA found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// sfia
	// @since 8.0
	register_post_type('sfia', array(
		'labels' => array(
			'name' => _x('SFIA', 'post type general name', 'advisory'),
			'singular_name' => _x('SFIA', 'post type singular name', 'advisory'),
			'menu_name' => _x('SFIA', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('SFIA', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'sfia', 'advisory'),
			'add_new_item' => __('Add New SFIA', 'advisory'),
			'new_item' => __('New SFIA', 'advisory'),
			'edit_item' => __('Edit SFIA', 'advisory'),
			'view_item' => __('View SFIA', 'advisory'),
			'all_items' => __('All SFIA', 'advisory'),
			'search_items' => __('Search SFIA', 'advisory'),
			'parent_item_colon' => __('Parent SFIA:', 'advisory'),
			'not_found' => __('No SFIA found.', 'advisory'),
			'not_found_in_trash' => __('No SFIA found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));
	// SFIA TECHNICAL SURVEY
	// @since 8.0
	register_post_type('sfiats', array(
		'labels' => array(
			'name' => _x('Technical Surveys', 'post type general name', 'advisory'),
			'singular_name' => _x('Technical Survey', 'post type singular name', 'advisory'),
			'menu_name' => _x('Technical Survey', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('Technical Survey', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'Technical Survey', 'advisory'),
			'add_new_item' => __('Add New Technical Survey', 'advisory'),
			'new_item' => __('New Technical Survey', 'advisory'),
			'edit_item' => __('Edit Technical Survey', 'advisory'),
			'view_item' => __('View Technical Survey', 'advisory'),
			'all_items' => __('All Technical surveys', 'advisory'),
			'search_items' => __('Search Technical Survey', 'advisory'),
			'parent_item_colon' => __('Parent Technical Survey:', 'advisory'),
			'not_found' => __('No Technical Survey found.', 'advisory'),
			'not_found_in_trash' => __('No Technical Survey found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => ['title']
	));
	// SFIA REGISTER
	// @since 8.0
	register_post_type('sfiar', array(
		'labels' => array(
			'name' => _x('SFIA Register', 'post type general name', 'advisory'),
			'singular_name' => _x('SFIA Register', 'post type singular name', 'advisory'),
			'menu_name' => _x('SFIA Register', 'admin menu', 'advisory'),
			'name_admin_bar' => _x('SFIA Register', 'add new on admin bar', 'advisory'),
			'add_new' => _x('Add New', 'sfiar', 'advisory'),
			'add_new_item' => __('Add New SFIA Register', 'advisory'),
			'new_item' => __('New SFIA Register', 'advisory'),
			'edit_item' => __('Edit SFIA Register', 'advisory'),
			'view_item' => __('View SFIA Register', 'advisory'),
			'all_items' => __('All SFIA Register', 'advisory'),
			'search_items' => __('Search SFIA Register', 'advisory'),
			'parent_item_colon' => __('Parent SFIA Register:', 'advisory'),
			'not_found' => __('No SFIA Register found.', 'advisory'),
			'not_found_in_trash' => __('No SFIA Register found in Trash.', 'advisory'),
		),
		'public' => true,
		'exclude_from_search' => true,
		'supports' => array('title'),
	));

	// TAXONOMIES
	register_taxonomy(
		'company',
		'user',
		array(
			'public' => true,
			'single_value' => true,
			'show_admin_column' => true,
			'labels' => array(
				'name' => 'Company',
				'singular_name' => 'Company',
				'menu_name' => 'Companies',
				'search_items' => 'Search Companies',
				'popular_items' => 'Popular Companies',
				'all_items' => 'All Companies',
				'edit_item' => 'Edit Company',
				'update_item' => 'Update Company',
				'add_new_item' => 'Add New Company',
				'new_item_name' => 'New Company Name',
				'separate_items_with_commas' => 'Separate companies with commas',
				'add_or_remove_items' => 'Add or remove companies',
				'choose_from_most_used' => 'Choose from the most popular companies',
			),
			'rewrite' => false,
			'capabilities' => array(
				'manage_terms' => 'edit_users',
				'edit_terms' => 'edit_users',
				'delete_terms' => 'edit_users',
				'assign_terms' => 'edit_users',
			),
		)
	);
	// Register Custom Status
	$args = array(
		'label' => _x('Archived', 'Status General Name', 'advisory'),
		'label_count' => _n_noop('Archived (%s)', 'Archived (%s)', 'advisory'),
		'public' => true,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'exclude_from_search' => false,
	);
	register_post_status('archived', $args);
	// Disable wp-login.php
	global $pagenow;
	$requestGetAction = !empty($_GET['action']) ? $_GET['action'] : '';
	if ('wp-login.php' == $pagenow && $requestGetAction != 'logout') {
		wp_redirect(home_url());
		exit();
	}
}
add_action('init', 'advisory_init');