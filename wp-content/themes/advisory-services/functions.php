<?php
$allPostTypes = ['itcl', 'prr', 'mta', 'ihc', 'itsm', 'cra', 'bia', 'risk', 'bcp', 'drm', 'drmrr', 'dmm', 'dmmr', 'ihcr', 'mtar', 'sfia', 'sfiar']; // ['dmmr','ihcr']
define('P3_TEMPLATE_VERSION', time());
define('ALL_SCORECARDS', json_encode(['prr', 'mta', 'ihc', 'itsm', 'cra', 'bia', 'risk', 'bcp', 'drm', 'dmm', 'itcl']));
define('ALL_POST_TYPES', json_encode($allPostTypes));
define('ALL_RISKS', json_encode(['risk', 'bcp', 'dmmr']));
define('P3_TEMPLATE_URI', get_template_directory_uri());
define('P3_TEMPLATE_PATH', get_template_directory());
define('IMAGE_DIR_URL', P3_TEMPLATE_URI .'/images/');
/* Include helper functions for survey */
require_once P3_TEMPLATE_PATH . '/PHPExcel-1.8/Classes/PHPExcel.php';

require_once P3_TEMPLATE_PATH .'/includes/input_fields.php';
require_once P3_TEMPLATE_PATH .'/helper/common.php';
require_once P3_TEMPLATE_PATH .'/helper/mta.php';
require_once P3_TEMPLATE_PATH .'/helper/mtar.php';
require_once P3_TEMPLATE_PATH .'/helper/ihc.php';
require_once P3_TEMPLATE_PATH .'/helper/ihcr.php';
require_once P3_TEMPLATE_PATH .'/helper/itsm.php';
require_once P3_TEMPLATE_PATH .'/helper/cra.php';
require_once P3_TEMPLATE_PATH .'/helper/bia.php';
require_once P3_TEMPLATE_PATH .'/helper/risk.php';
require_once P3_TEMPLATE_PATH .'/helper/bcp.php';
require_once P3_TEMPLATE_PATH .'/helper/prr.php';
require_once P3_TEMPLATE_PATH .'/helper/drm.php';
require_once P3_TEMPLATE_PATH .'/helper/drmrr.php';
require_once P3_TEMPLATE_PATH .'/helper/dmm.php';
require_once P3_TEMPLATE_PATH .'/helper/csa.php';
require_once P3_TEMPLATE_PATH .'/helper/sfia.php';
require_once P3_TEMPLATE_PATH .'/helper/sfiats.php';
require_once P3_TEMPLATE_PATH .'/helper/sfiar.php';
require_once P3_TEMPLATE_PATH .'/helper/metabox.php';
require_once P3_TEMPLATE_PATH .'/partials/post_types.php';
require_once P3_TEMPLATE_PATH .'/partials/dynamic_registers.php';
// require_once P3_TEMPLATE_PATH . '/includes/pdf_html.php';

// DEFAULT EMAIL
add_filter( 'wp_mail_from_name', function( $name ) 	{ return 'WGAdvisory Portal'; });
add_filter( 'wp_mail_from', function ($email )		{ return 'encase@wgadvisory.ca'; });
/*
 * After theme setup
 */
function advisory_after_theme_setup() {
	// Add translation support.
	load_theme_textdomain('advisory', P3_TEMPLATE_PATH . '/languages');
	// Let WordPress manage the document title.
	add_theme_support('title-tag');
	// Hide admin bar
	if (!is_admin()) show_admin_bar(false);
}
add_action('after_setup_theme', 'advisory_after_theme_setup');
/*
 * Enqueue Scripts
 */
function advisory_enqueue_scripts() {
	// Styles
	wp_enqueue_style('advisory', get_stylesheet_uri());
	wp_enqueue_style('advisory-main', P3_TEMPLATE_URI. '/css/main.css', [], P3_TEMPLATE_VERSION);
	// Scripts
	wp_enqueue_script('jquery');
	// wp_register_script('advisory-tinymce', P3_TEMPLATE_URI. '/js/plugins/jquery.tinymce.min.js', array(), false, false);
	wp_enqueue_script('advisory-essential', P3_TEMPLATE_URI. '/js/essential-plugins.js', array(), false, true);
	wp_enqueue_script('advisory-bootstrap', P3_TEMPLATE_URI. '/js/bootstrap.min.js', array(), false, true);
	wp_enqueue_script('advisory-pace', P3_TEMPLATE_URI. '/js/plugins/pace.min.js', array(), false, true);
	wp_enqueue_script('advisory-rwdImageMaps', P3_TEMPLATE_URI. '/js/plugins/jquery.rwdImageMaps.min.js', array(), false, true);
	wp_enqueue_script('advisory-notify', P3_TEMPLATE_URI. '/js/plugins/bootstrap-notify.min.js', array(), false, true);
	wp_enqueue_script('advisory-sweetalert', P3_TEMPLATE_URI. '/js/plugins/sweetalert.min.js', array(), false, true);
	wp_enqueue_script('advisory-uploadPreview', P3_TEMPLATE_URI. '/js/plugins/jquery.uploadPreview.min.js', array(), false, true);
	wp_enqueue_script('advisory-validate', P3_TEMPLATE_URI. '/js/plugins/jquery.validate.min.js', array(), false, true);
	wp_enqueue_script('advisory-chart', P3_TEMPLATE_URI. '/js/plugins/Chart.bundle.js', array(), false, true);
	wp_enqueue_script('advisory-utils', P3_TEMPLATE_URI. '/js/plugins/utils.js', array(), false, true);
	wp_enqueue_script('advisory-main', P3_TEMPLATE_URI. '/js/main.js', array(), false, true);
	wp_enqueue_script('advisory', P3_TEMPLATE_URI. '/js/script.js', array(), P3_TEMPLATE_VERSION, true);
	wp_localize_script('advisory', 'object', ['ajaxurl' => admin_url('admin-ajax.php'), 'home_url' => home_url(), 'ajax_nonce' => wp_create_nonce('advisory_nonce'), 'template_dir_url' => P3_TEMPLATE_URI]);
	wp_enqueue_media();
}
add_action('wp_enqueue_scripts', 'advisory_enqueue_scripts');
/**
 * Add Codestar Framework.
 */
include get_stylesheet_directory() . '/codestar/cs-framework.php';
define('CS_ACTIVE_SHORTCODE', false);
define('CS_ACTIVE_CUSTOMIZE', false);
/***********************************************
 *
 * Ajax Callback
 * @used frontend
 *
 **********************************************/
// login
function advisory_ajax_login() {
	check_ajax_referer('advisory_nonce', 'security');
	$creds = array();
	$creds['user_login'] = $_REQUEST['email'];
	$creds['user_password'] = $_REQUEST['pass'];
	$creds['remember'] = $_REQUEST['remember'];
	$user = wp_signon($creds, false);
	if (is_wp_error($user)) echo false;
	else echo true;
	wp_die();
}
add_action('wp_ajax_nopriv_user_login', 'advisory_ajax_login');
// save form data
function advisory_ajax_save_survey() {
	check_ajax_referer('advisory_nonce', 'security');
	$data = $_REQUEST['data'];
	$meta_key = $_REQUEST['meta'];
	$post_id = $_REQUEST['post_id'];
	if (get_post_type($post_id) == 'dmm') {
		// update corresponding resgister meta also
		$company = get_post_meta($post_id, 'permission', true);
		$assessment = get_post($post_id);
		$register = get_page_by_title($assessment->post_title, OBJECT, 'dmmr');
		updateOldDMMRFor($company, $assessment, $register);
	}
	if (update_post_meta($post_id, $meta_key, $data)) { wp_send_json(true); }
	else { wp_send_json($data); }
}
add_action('wp_ajax_save_survey', 'advisory_ajax_save_survey');
// save form data
function advisory_ajax_save_rr() {
	check_ajax_referer('advisory_nonce', 'security');
	$data = $_REQUEST['data'];
	$meta_key = $_REQUEST['meta'];
	$post_id = $_REQUEST['post_id'];
	$archivedby = !empty($_REQUEST['archivedby']) ? $_REQUEST['archivedby'] : false;
	if ($archivedby) {
		if (update_term_meta($post_id, $meta_key, $data) && MTARegisterSaveArchive()) {
		// if (MTARegisterSaveArchive()) {
			wp_send_json(true);
		} else wp_send_json(false);
	} else {
		if (update_term_meta($post_id, $meta_key, $data)) wp_send_json(true);
		else wp_send_json(false);
	}
}
add_action('wp_ajax_save_rr', 'advisory_ajax_save_rr');

// update profile
function advisory_filter_upload_dir($dir) {
	return array(
		'path' => $dir['basedir'] . '/profile_avatar',
		'url' => $dir['baseurl'] . '/profile_avatar',
		'subdir' => '/profile_avatar',
	) + $dir;
}
function advisory_ajax_update_profile() {
	check_ajax_referer('advisory_nonce', 'security');
	$data = [];
	parse_str($_REQUEST['data'], $data);
	$avatar = $_FILES['avatar'];
	$user = get_current_user_id();
	$updateData = array('ID' => $user, 'first_name' => $data['f_name'], 'last_name' => $data['l_name'], 'user_email' => $data['email'], 'user_pass' => $data['password']);
	add_filter('upload_dir', 'advisory_filter_upload_dir');
	if ($avatar['name']) {
		$uploadedfile = array(
			'name' => $avatar['name'],
			'type' => $avatar['type'],
			'tmp_name' => $avatar['tmp_name'],
			'error' => $avatar['error'],
			'size' => $avatar['size'],
		);
		$movefile = wp_handle_upload($uploadedfile, ['test_form' => false]);
		if ($movefile) {
			update_user_meta($user, 'avatar', $movefile['url']);
		}
	}
	remove_filter('upload_dir', 'advisory_filter_upload_dir');
	$response = wp_update_user(array_filter($updateData));
	if (is_wp_error($response)) wp_send_json($response);
	else wp_send_json(true);
}
add_action('wp_ajax_update_profile', 'advisory_ajax_update_profile');
// publish survey
function ajax_publish_survey() {
	check_ajax_referer('advisory_nonce', 'security');
	$form_id = $_REQUEST['form_id'];
	if (get_post_type($form_id) == 'mta') {
		if (metadata_exists('mta', $form_id, 'archive_date' )) delete_post_meta( $form_id, 'archive_date');
		add_post_meta( $form_id, 'archive_date', time(), false );
	}
	$update = wp_update_post(['ID' => $form_id, 'post_status' => 'archived']);
	if (is_wp_error($update)) wp_send_json(false);
	else wp_send_json(true);
}
add_action('wp_ajax_publish_survey', 'ajax_publish_survey');
// reset survey
function advisory_ajax_reset_survey() {
	check_ajax_referer('advisory_nonce', 'security');
	if (isset($_REQUEST['form_id']) && !empty($_REQUEST['form_id'])) $form_id = $_REQUEST['form_id'];
	else {
		$form_id = advisory_get_active_forms(advisory_get_user_company_id(), array('ihc', 'mta'));
		if (empty($form_id)) wp_send_json(false);
		$form_id = $form_id[0];
	}
	if (!advisory_has_survey_delete_permission($form_id)) wp_send_json(false);
	$postType = get_post_type($form_id);
	if($postType == 'csa'){
		$opts = get_post_meta($form_id, 'form_opts', true);
		if (!empty($opts['sections'])) {
			foreach ($opts['sections'] as $section) {
				$sectionID = advisory_id_from_string($section['name']).'_section';
	            if (!empty($opts[$sectionID])) {
					foreach ($opts[$sectionID] as $category) {
						if (@$_POST['area'] != advisory_id_from_string($category['name'])) continue;
		                $categoryID = advisory_id_from_string($category['name']) . '_domains';
		                $department_id = $sectionID.'_'.$categoryID;
						if (delete_post_meta($form_id, $department_id.'_csa')) wp_send_json(true);
					}
	            }
			}
		}
	} else if($postType == 'ihc' || $postType == 'mta'){
		$sections = get_post_meta($form_id);
		if (!empty($sections)) {
			foreach ($sections as $key => $section) {
				parse_str($section[0], $section);
				if (isset($section['avg']) || isset($section['reset'])) {
					$deleted = delete_post_meta($form_id, $key);
				}
			}
			if ($deleted != false) {wp_send_json(true); }
		}
	} else if($postType == 'bcp'){
		$sections = get_post_meta($form_id);
		if (!empty($sections)) {
			foreach ($sections as $key => $section) {
				parse_str($section[0], $section);
				if (isset($section['avg']) || isset($section['reset'])) {
					$deleted[] = delete_post_meta($form_id, $key);
				}
			}
			if (!empty($deleted)) {wp_send_json(true); }
		}
	} else {
		$sections = get_post_meta($form_id);
		if (!empty($sections)) {
			foreach ($sections as $key => $section) {
				parse_str($section[0], $section);
				if (isset($section['avg']) || isset($section['reset'])) {
					$deleted = delete_post_meta($form_id, $key);
					if ($deleted != false) {
						wp_send_json($key);
					}
				}
			}
		}
	}
	wp_send_json(false);
}
add_action('wp_ajax_reset_survey', 'advisory_ajax_reset_survey');
// delete survey
function advisory_ajax_delete_survey() {
	check_ajax_referer('advisory_nonce', 'security');
	$post_id = $_REQUEST['post_id'];
	$deleted = wp_delete_post($post_id, true);
	if ($deleted) wp_send_json(true);
	wp_send_json(false);
}
add_action('wp_ajax_delete_survey', 'advisory_ajax_delete_survey');
// lock survey
function advisory_ajax_lock_survey() {
	check_ajax_referer('advisory_nonce', 'security');
	$post_id = $_REQUEST['post_id'];
	$user_id = $_REQUEST['user_id'];
	$meta = get_user_meta($user_id, 'edit_permissions', true);
	$meta = explode(',', $meta);
	if (in_array($post_id, $meta)) {
		$meta = array_diff($meta, array($post_id));
		$meta = implode(',', $meta);
		$update = update_user_meta($user_id, 'edit_permissions', $meta);
		if ($update) wp_send_json(true);
	} else {
		array_push($meta, $post_id);
		$meta = implode(',', $meta);
		$update = update_user_meta($user_id, 'edit_permissions', $meta);
		if ($update) wp_send_json(false);
	}
}
add_action('wp_ajax_lock_survey', 'advisory_ajax_lock_survey');
// valid survey category
function advisory_ajax_is_valid_form_submission() {
	check_ajax_referer('advisory_nonce', 'security');
	$form_id = $_REQUEST['form_id'];
	$valid = advisory_is_valid_form_submission($form_id);
	wp_send_json($valid);
}
add_action('wp_ajax_validate_form_submission', 'advisory_ajax_is_valid_form_submission');
// Dashboard Reportcard
function advisory_ajax_dashboard_cloud_reportcard() {
	check_ajax_referer('advisory_nonce', 'security');
	$html = '';
	$upstreams = advisory_get_cloud_reportcard_data();
	// echo '<br><pre>'. print_r($upstreams, true) .'</pre>'; wp_die();
	if ($upstreams) $html .= advisory_cloudcard_html($upstreams, 'cloud-reportcard.jpg', 'Cloud Service Catalogue');
	echo $html;
	wp_die();
}
add_action('wp_ajax_dashboard_cloud_reportcard', 'advisory_ajax_dashboard_cloud_reportcard');

function advisory_ajax_dashboard_report_card() {
	check_ajax_referer('advisory_nonce', 'security');
	$html = '';
	$dependencies = advisory_get_dashboard_report_card_data();
	// $html = '<br><pre>'. print_r($dependencies['cloud'], true) .'</pre>'; echo $html; wp_die();
	if (!empty($dependencies)) {
		$html .= '<form method="post" class="pull-right scorecardExportForm">';
		$html .= '<button type="submit" name="export_summary" value="csv" class="btn btn-xs btn-primary pdf-btn mr-10">CSV</button>';
		$html .= '<button type="submit" name="export_summary" value="excel" class="btn btn-xs btn-primary pdf-btn">Excel</button>';
		$html .= '</form>';
		$html .= '<div class="text-center"> <img src="'.P3_TEMPLATE_URI.'/images/report_card.jpg" class="img-responsive"> </div>';
        $html .= '<div class="bold legendWrapper">';
            $html .= '<div style="width: 210px;margin-bottom: 5px;float:right;">RTO = Recovery Time Objective <br> &nbsp;&nbsp; CL = Criticality Level</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '</div>';
        $html .= '<div style="max-height:672px;overflow-y:scroll;">';
			if (!empty($dependencies['it'])) {
				$pdfLink = '<a href="'. site_url('pdf') .'?pid=catelogue_summary&catalogue=it" class="pdfBtn" title="Catelogue Summary" target="_blank"><img src="'.IMAGE_DIR_URL.'pdf/power_inverse.png"></a>';
				$html .= '<div class="catalogueSummaryTitle">IT Service Catalogue '.$pdfLink.'</div>';
				$html .= '<div class="catalogueSummary accordion" id="itsc_accordion">';
					foreach ($dependencies['it'] as $serviceSI => $service) {
						if (!empty($service['items'])) {
							uasort($service['items'],"advisory_sort_name");
						    $html .= '<div class="custom-card">';
						        $html .= '<div class="card-header" id="itsc_heading_'. $serviceSI .'">';
						            $html .= '<div class="collapsed" data-toggle="collapse" data-target="#itsc_collapse_'. $serviceSI .'" aria-expanded="false" aria-controls="itsc_collapse_'. $serviceSI .'"> Service : '.$service['name'].' </div>';
						        $html .= '</div>';
						        $html .= '<div id="itsc_collapse_'. $serviceSI .'" class="collapse" aria-labelledby="itsc_heading_'. $serviceSI .'" data-parent="#itsc_accordion">';
						            $html .= '<div class="card-body">';
						                $html .= '<table class="table table-bordered table-reportCard">';
						                    $html .= '<tr>';
											    $html .= '<th class="t-heading-lightgrey strong">Department</th>';
											    $html .= '<th class="t-heading-lightgrey strong">Service/Process</th>';
											    $html .= '<th class="t-heading-lightgrey text-center strong" style="width: 98px;">RTO</th>';
											    $html .= '<th class="t-heading-lightgrey text-center strong">CL</th>';
											$html .= '</tr>';
											foreach ($service['items'] as $item) {
											    $html .= '<tr>';
											        $html .= '<th>'.$item['area'].'</th>';
											        $html .= '<th>'.$item['service'].'</th>';
											        $html .= '<th class="text-center">'.$item['rto'].'</th>';
											        $html .= '<th class="text-center '.$item['cl']['class'].'">'.$item['cl']['value'].'</th>';
											    $html .= '</tr>';
											}
						                $html .= '</table>';
						            $html .= '</div>';
						        $html .= '</div>';
						    $html .= '</div>';
						}
					}
				$html .= '</div>';
			}

			if (!empty($dependencies['cloud'])) {
				$pdfLink = '<a href="'. site_url('pdf') .'?pid=catelogue_summary&catalogue=cloud" class="pdfBtn" title="Catelogue Summary" target="_blank"><img src="'.IMAGE_DIR_URL.'pdf/power_inverse.png"></a>';
				$html .= '<div class="catalogueSummaryTitle">Cloud Service Catalogue '.$pdfLink.'</div>';
				$html .= '<div class="catalogueSummary accordion" id="csc_accordion">';
					foreach ($dependencies['cloud'] as $serviceSI => $service) {
						if (!empty($service['items'])) {
							uasort($service['items'],"advisory_sort_name");
						    $html .= '<div class="custom-card">';
						        $html .= '<div class="card-header" id="csc_heading_'. $serviceSI .'">';
						            $html .= '<div class="collapsed" data-toggle="collapse" data-target="#csc_collapse_'. $serviceSI .'" aria-expanded="false" aria-controls="csc_collapse_'. $serviceSI .'"> Service : '.$service['name'].' </div>';
						        $html .= '</div>';
						        $html .= '<div id="csc_collapse_'. $serviceSI .'" class="collapse" aria-labelledby="csc_heading_'. $serviceSI .'" data-parent="#csc_accordion">';
						            $html .= '<div class="card-body">';
						                $html .= '<table class="table table-bordered table-reportCard">';
						                    $html .= '<tr>';
											    $html .= '<th class="t-heading-lightgrey strong">Department</th>';
											    $html .= '<th class="t-heading-lightgrey strong">Service/Process</th>';
											    $html .= '<th class="t-heading-lightgrey text-center strong" style="width: 98px;">RTO</th>';
											    $html .= '<th class="t-heading-lightgrey text-center strong">CL</th>';
											$html .= '</tr>';
											foreach ($service['items'] as $item) {
											    $html .= '<tr>';
											        $html .= '<th>'.$item['area'].'</th>';
											        $html .= '<th>'.$item['service'].'</th>';
											        $html .= '<th class="text-center">'.$item['rto'].'</th>';
											        $html .= '<th class="text-center '.$item['cl']['class'].'">'.$item['cl']['value'].'</th>';
											    $html .= '</tr>';
											}
						                $html .= '</table>';
						            $html .= '</div>';
						        $html .= '</div>';
						    $html .= '</div>';
						}
					}
				$html .= '</div>';
			}

			if (!empty($dependencies['desktop'])) {
				$pdfLink = '<a href="'. site_url('pdf') .'?pid=catelogue_summary&catalogue=desktop" class="pdfBtn" title="Catelogue Summary" target="_blank"><img src="'.IMAGE_DIR_URL.'pdf/power_inverse.png"></a>';
				$html .= '<div class="catalogueSummaryTitle">Desktop Service Catalogue '.$pdfLink.'</div>';
				$html .= '<div class="catalogueSummary accordion" id="dsc_accordion">';
					foreach ($dependencies['desktop'] as $serviceSI => $service) {
						if (!empty($service['items'])) {
							uasort($service['items'],"advisory_sort_name");
						    $html .= '<div class="custom-card">';
						        $html .= '<div class="card-header" id="dsc_heading_'. $serviceSI .'">';
						            $html .= '<div class="collapsed" data-toggle="collapse" data-target="#dsc_collapse_'. $serviceSI .'" aria-expanded="false" aria-controls="dsc_collapse_'. $serviceSI .'"> Service : '.$service['name'].' </div>';
						        $html .= '</div>';
						        $html .= '<div id="dsc_collapse_'. $serviceSI .'" class="collapse" aria-labelledby="dsc_heading_'. $serviceSI .'" data-parent="#dsc_accordion">';
						            $html .= '<div class="card-body">';
						                $html .= '<table class="table table-bordered table-reportCard">';
						                    $html .= '<tr>';
											    $html .= '<th class="t-heading-lightgrey strong">Department</th>';
											    $html .= '<th class="t-heading-lightgrey strong">Service/Process</th>';
											    $html .= '<th class="t-heading-lightgrey text-center strong" style="width: 98px;">RTO</th>';
											    $html .= '<th class="t-heading-lightgrey text-center strong">CL</th>';
											$html .= '</tr>';
											foreach ($service['items'] as $item) {
											    $html .= '<tr>';
											        $html .= '<th>'.$item['area'].'</th>';
											        $html .= '<th>'.$item['service'].'</th>';
											        $html .= '<th class="text-center">'.$item['rto'].'</th>';
											        $html .= '<th class="text-center '.$item['cl']['class'].'">'.$item['cl']['value'].'</th>';
											    $html .= '</tr>';
											}
						                $html .= '</table>';
						            $html .= '</div>';
						        $html .= '</div>';
						    $html .= '</div>';
						}
					}
				$html .= '</div>';
			}
        $html .= '</div>';
	}
	echo $html; wp_die();
}
add_action('wp_ajax_dashboard_report_card', 'advisory_ajax_dashboard_report_card');
function advisory_get_dashboard_report_card_data($biaIDs=null) {
	$data = [];
	$companyID = advisory_get_user_company_id();
	$company = get_term_meta($companyID, 'company_data', true);
	if (empty($biaIDs)) {
		if ($company['bia']) $biaIDs = $company['bia']; 
		else $biaIDs = getLatestBIAID($companyID);
	}
	// $biaIDs = [703, 693, 665];
	// $biaIDs = 703;
	// return $biaIDs;
	if (is_array($biaIDs) && $biaIDs) {
		foreach ($biaIDs as $biaID) {
			// company
			$biaData = advisory_get_cloud_report_card_data_for($biaID, $company);
			if ($biaData) {
				if (empty($data)) $data = $biaData;
				else {
					foreach ($biaData as $dependenciesKey => $dependencies) {
						if (!empty($dependencies)) {
							foreach ($dependencies as $dependencySI => $dependency) {
								if (empty($data[$dependenciesKey][$dependencySI])) { $data[$dependenciesKey][$dependencySI] = (array) $dependency; }
								else { @$data[$dependenciesKey][$dependencySI]['items'] = array_merge((array)$data[$dependenciesKey][$dependencySI]['items'], (array)@$dependency['items']);}
							}
						}
					}
				}
			}
		}
	} else {
		$data = advisory_get_cloud_report_card_data_for($biaIDs, $company);
	}
	return $data;
}

function advisory_get_cloud_report_card_data_for($biaID, $company) {
	$reports = $dependencies = ['it' => [], 'cloud' => [], 'desktop' => []];
	// upstream
	if (!$company['upstream']) $upstreamDependencies = [];
	else $upstreamDependencies = $company['upstream'];
	$upstreamDependencies = getDependengiesInAlphabeticOrder($upstreamDependencies);
	if ($upstreamDependencies) {
		foreach ($upstreamDependencies as $key => $upstream) {
			$dependencies['it'][$key]['id'] = $key;
			$dependencies['it'][$key]['name'] = $upstream;
		}
	}
	// externalDependency / Cloud Dependency
	if (!$company['externalDependency']) $externalDependencies = [];
	else $externalDependencies = $company['externalDependency'];
	$externalDependencies = getDependengiesInAlphabeticOrder($externalDependencies);
	if ($externalDependencies) {
		foreach ($externalDependencies as $key => $externalDependency) {
			$dependencies['cloud'][$key]['id'] = $key;
			$dependencies['cloud'][$key]['name'] = $externalDependency;
		}
	}
	// desktopDependency
	if (!$company['desktopDependency']) $desktopDependencies = [];
	else $desktopDependencies = $company['desktopDependency'];
	$desktopDependencies = getDependengiesInAlphabeticOrder($desktopDependencies);
	if ($desktopDependencies) {
		foreach ($desktopDependencies as $key => $desktopDependency) {
			$dependencies['desktop'][$key]['id'] = $key;
			$dependencies['desktop'][$key]['name'] = $desktopDependency;
		}
	}
	$reports = $dependencies;
	// return $dependencies;
	$data = [];
	$form_data = get_post_meta($biaID, 'form_opts', true);
	$transient_data = get_post_meta($biaID);
	$count = 0;
	if (!empty($form_data['areas'])) {
		foreach ($form_data['areas'] as $area) {
			$count2 = 0;
			$data[$count]['name'] = $area['name'];
			$services = advisory_id_from_string($area['name']) . '_services';
			if (!empty($form_data[$services])) {
				$Q3Default = advisory_form_default_values($biaID, $services .'_how_recreate');
				$Q4Default = advisory_form_default_values($biaID, $services .'_int_func');
				$Q5Default = advisory_form_default_values($biaID, $services .'_ext_func');
                foreach ($form_data[$services] as $service) {
                	$biaQ3Value = biaQ3Value(advisory_id_from_string($service['name']), $Q3Default);
                	$biaQ4Value = biaQ4Value(advisory_id_from_string($service['name']), $Q4Default, 1);
                	$biaQ5Value = biaQ5Value(advisory_id_from_string($service['name']), $Q5Default);
					$data[$count]['services'][$count2]['name'] = $service['name'];
					$service_data = !empty( $transient_data[$services . '_bia_' . advisory_id_from_string($service['name'])] ) ? $transient_data[$services . '_bia_' . advisory_id_from_string($service['name'])] : [];
					parse_str($service_data[0], $service_data);
					$data[$count]['services'][$count2]['cl'] = !empty($service_data['avg']) ? $service_data['avg'] : 0;
					$data[$count]['services'][$count2]['rto'] = !empty($service_data['rto']) ? str_replace(' - ', '-', $service_data['rto']) : 0;
					$data[$count]['services'][$count2]['rpo'] = !empty($biaQ3Value) ? $biaQ3Value : [];
					$data[$count]['services'][$count2]['upstream'] = !empty($biaQ4Value['upstream']) ? $biaQ4Value['upstream'] : '';
					$data[$count]['services'][$count2]['desktop'] = !empty($biaQ4Value['desktop']) ? $biaQ4Value['desktop'] : '';
					$data[$count]['services'][$count2]['cloud'] = $biaQ5Value != 'N/A' ? $biaQ5Value : '';
					$count2++;
				}
			}
			$count++;
		}
	}
	// get reports
	if (!empty($data)) {
		$itCounter = $cloudCounter = $desktopCounter = 0;
		foreach ($data as $area) {
			if (!empty($area['services'])) {
				foreach ($area['services'] as $service) {
					if (!empty($service['upstream'])) {
						$upstreams = explode(',', $service['upstream']);
						foreach ($upstreams as $it) {
							if (!empty(trim($area['name'])) && !empty(trim($service['name']))) {
								$reports['it'][$it]['items'][$itCounter]['sort'] = trim($area['name']) .' - '. trim($service['name']);
								$reports['it'][$it]['items'][$itCounter]['area'] = $area['name'];
								$reports['it'][$it]['items'][$itCounter]['service'] = $service['name'];
								$reports['it'][$it]['items'][$itCounter]['rto'] = @$service['rto'];
								$reports['it'][$it]['items'][$itCounter]['rpo'] = $service['rpo'];
								$reports['it'][$it]['items'][$itCounter]['cl'] = ['class' => coloring_elements(@$service['cl'], 'bia-score'), 'value' => @$service['cl']];
								$itCounter++;
							}
						}
					}

					if (!empty($service['cloud'])) {
						$clouds = explode(',', $service['cloud']);
						foreach ($clouds as $cloud) {
							if (!empty(trim($area['name'])) && !empty(trim($service['name']))) {
								$reports['cloud'][$cloud]['items'][$cloudCounter]['sort'] = trim($area['name']) .' - '. trim($service['name']);
								$reports['cloud'][$cloud]['items'][$cloudCounter]['area'] = $area['name'];
								$reports['cloud'][$cloud]['items'][$cloudCounter]['service'] = $service['name'];
								$reports['cloud'][$cloud]['items'][$cloudCounter]['rto'] = @$service['rto'];
								$reports['cloud'][$cloud]['items'][$cloudCounter]['rpo'] = $service['rpo'];
								$reports['cloud'][$cloud]['items'][$cloudCounter]['cl'] = ['class' => coloring_elements(@$service['cl'], 'bia-score'), 'value' => @$service['cl']];
								$cloudCounter++;
							}
						}
					}

					if (!empty($service['desktop'])) {
						$desktops = explode(',', $service['desktop']);
						foreach ($desktops as $desktop) {
							if (!empty(trim($area['name'])) && !empty(trim($service['name']))) {
								$reports['desktop'][$desktop]['items'][$desktopCounter]['sort'] = trim($area['name']) .' - '. trim($service['name']);
								$reports['desktop'][$desktop]['items'][$desktopCounter]['area'] = $area['name'];
								$reports['desktop'][$desktop]['items'][$desktopCounter]['service'] = $service['name'];
								$reports['desktop'][$desktop]['items'][$desktopCounter]['rto'] = @$service['rto'];
								$reports['desktop'][$desktop]['items'][$desktopCounter]['rpo'] = $service['rpo'];
								$reports['desktop'][$desktop]['items'][$desktopCounter]['cl'] = ['class' => coloring_elements(@$service['cl'], 'bia-score'), 'value' => @$service['cl']];
								$desktopCounter++;
							}
						}
					}
				}
			}
		}
	}
	return $reports;
}
function advisory_sort_name($a,$b) {return $a["sort"] > $b["sort"]; }

// Dashboard MTAR scorecard
function advisory_ajax_dashboard_mtar_statuscard() {
	check_ajax_referer('advisory_nonce', 'security');
	$html = '';
	$area = advisory_get_mrtar_status_data();
	if (!empty($area)) {
		// $html = '<br><pre>'. print_r($area['sections'], true) .'</pre>';
		$html .= '<div class="table-responsive">';
			$html .= '<table class="table table-bordered table-mtar_statuscard">';
				$html .= '<tbody>';
					$html .= '<tr>';
						$html .= '<th class="t-heading-dark" style="font-size: 20px;border:none" colspan="1">'.$area['name'].'</th>';
						$html .= '<th class="t-heading-dark text-center" style="font-size: 18px;border:none;width:250px;">MTA Register</th>';
					$html .= '</tr>';
					if (!empty($area['sections'])) {
						foreach ($area['sections'] as $section) {
							$html .= '<tr>';
								$html .= '<th class="t-heading-sky" style="font-size:17px;">'.$section['name'].'</th>';
								$html .= '<th class="t-heading-sky text-center" style="font-weight:400;font-size:17px;">Status</th>';
							$html .= '</tr>';
							if (!empty($section['tables'])) {
								foreach ($section['tables'] as $table) {
									$html .= '<tr>';
										$html .= '<td style="font-weight: 700;">'.$table['name'].'</td>';
										$html .= '<td class="text-center '.$table['value']['status_cls'].'">'.$table['value']['status_txt'].'</td>';
									$html .= '</tr>';
								}
							}
						}
					}
				$html .= '</tbody>';
			$html .= '</table>';
		$html .= '</div>';
	}
	echo $html;
	wp_die();
}
add_action('wp_ajax_dashboard_mtar_statuscard', 'advisory_ajax_dashboard_mtar_statuscard');
function advisory_get_mrtar_status_data(){
	$data = [];
 	if (!empty($_REQUEST['post_id'])) {
		$opts = get_post_meta($_REQUEST['post_id'], 'form_opts', true);
		if (!empty($opts['areas'])) {
			foreach ($opts['areas'] as $area) {
				$areaID = advisory_id_from_string($area['name']);
				if ($areaID != $_REQUEST['area_id']) continue;
				$data['name'] = $area['name'];
				if ($opts['sections_'. $areaID]) {
					$counter = 0;
					foreach ($opts['sections_'. $areaID] as $sectionSI => $section) {
						$sectionID = advisory_id_from_string($section['name']);
						$data['sections'][$sectionSI]['name'] = $section['name'];
						if (!empty($opts['sections_'. $areaID .'_tables_'. $sectionID])) {
							foreach ($opts['sections_'. $areaID .'_tables_'. $sectionID] as $tableSI => $table) {
								$tableID = advisory_id_from_string($table['name']);
								$default = advisory_company_default_values(advisory_get_user_company_id(), $sectionID . '_'. $tableID);
								$data['sections'][$sectionSI]['tables'][$tableSI]['name'] = $table['name'];
								$data['sections'][$sectionSI]['tables'][$tableSI]['value'] = !empty($default['status']) ? MTARegisterColorByValue($default['status']) : MTARegisterColorByValue(1);
							}
						}
					}
				}
			}
		}
	}
	return $data;
}
// Dashboard Reportcard
function advisory_ajax_dashboard_reportcard() {
	check_ajax_referer('advisory_nonce', 'security');
	$html = '';
	$upstreams = advisory_get_reportcard_data();
	// echo '<br><pre>'. print_r($upstreams, true) .'</pre>'; exit();
	if ($upstreams) $html = advisory_reportcard_html($upstreams, 'reportcard.jpg', 'IT Service Catalogue');
	echo $html;
	wp_die();
}
add_action('wp_ajax_dashboard_reportcard', 'advisory_ajax_dashboard_reportcard');
function dashboard_eva_reportcard() {
    global $current_user;
	check_ajax_referer('advisory_nonce', 'security');
	$postID = $_GET['post_id'];
	$ops = get_post_meta($postID, 'form_opts', true);
	$html = '';
	$html .= '<style>';
	$html .= '.csaForm .strong {font-weight:bold;}';
    $html .= '.csaForm .black {background: #000; color: #fff;}';
    $html .= '.csaForm .red {background: #e40613;}';
    $html .= '.csaForm .orange {background: #ea4e1b;}';
    $html .= '.csaForm .yellow {background: #fdea11;}';
    $html .= '.csaForm .green {background: #3baa34;}';
    $html .= '.csaForm .aqua {background: #36a9e0;}';
	$html .= '</style>';
	$html .= '<div class="text-center"><img src="'. IMAGE_DIR_URL .'csa/scorecard.jpg" class="img-responsive" alt="responsive"></div><br>';
	$html .= '<div class="table-responsive csaForm">
		<table class="table table-bordered table-survey table_others">
			<tbody>';
			foreach ($ops['sections'] as $section) {
				$sectionID = advisory_id_from_string($section['name']);
				if ($sectionID == 'overview') continue;
				else if ($sectionID == 'risk') {
					$html .= '<tr><th class="t-heading-dark h4" style="font-size: 20px;" colspan="2">';
					$html .= 'INHERENT RISK PROFILE ';
					$html .= '<a href="'. site_url('pdf') .'?pid='. $_REQUEST['post_id'] .'&area='.$sectionID.'" target="_blank" style="    display: inline; margin-left: 3px; vertical-align: text-bottom;"><img src="'.P3_TEMPLATE_URI.'/images/pdf/power.png" style="height:28px;"></a>';
					$html .= '</th></tr>';
					$html .= '<tr>';
						$html .= '<th class="t-heading-sky text-center">CATEGORY</th>';
						$html .= '<th class="t-heading-sky text-center" style="width:150px;">Rating Level</th>';
					$html .= '</tr>';
				} else {
					$html .= '<tr><th class="t-heading-dark h4" style="font-size: 20px;" colspan="2">';
					$html .= 'CYBERSECURITY MATURITY ';
					$html .= '<a href="'. site_url('pdf') .'?pid='. $_REQUEST['post_id'] .'&area='.$sectionID.'" target="_blank" style="    display: inline; margin-left: 3px; vertical-align: text-bottom;"><img src="'.P3_TEMPLATE_URI.'/images/pdf/power.png" style="height:28px;"></a>';
					$html .= '</th></tr>';
					$html .= '<tr>';
						$html .= '<th class="t-heading-sky text-center">DOMAIN</th>';
						$html .= '<th class="t-heading-sky text-center" style="width:150px;">Rating Level</th>';
					$html .= '</tr>';
				}
				$sectionID = $sectionID.'_section';
				if ($ops[$sectionID]) {
					foreach ($ops[$sectionID] as $domain) {
						$domainID = advisory_id_from_string($domain['name']) . '_domains';
						$defaultID = $sectionID.'_'.$domainID.'_csa';
                        $default = advisory_form_default_values($postID, $defaultID);
						$html .= '<tr>';
						$html .= '<td style="font-weight: 700;text-transform:uppercase;">'. $domain['title'] .'</td>';
						$html .= '<td class="text-center '. $default['cls'] .'" style="color:transparent">'. $default['avg'] .'</td>';
						$html .= '</tr>';
					}
				}
			}
			$html .= '</tbody>
		</table>
	</div>';
	echo $html;
	wp_die();
}
add_action('wp_ajax_dashboard_eva_reportcard', 'dashboard_eva_reportcard');
// Dashboard Scorecard
function advisory_ajax_dashboard_scorecard() {
	check_ajax_referer('advisory_nonce', 'security');
	$html = '';
	$requestedPostType = get_post_type($_REQUEST['post_id']);
	$data = advisory_get_scorecard_data($_REQUEST['post_id']);
	$tmp_fields = [];
	$allRisks =  json_decode(ALL_RISKS);
	// $html .= '<br><pre>'. print_r($data, true) .'</pre>';
	if ($requestedPostType == 'bia') {
		$html .= '<div class="text-center"> <img src="'.P3_TEMPLATE_URI.'/images/value-scorecard-bia.jpg" class="img-responsive"></div><br>';
		foreach ($data as $area) {
			// $department_id = advisory_id_from_string($department['name']) . '_services';
			$biaScoreAVG = round($area['total'] / count($area['services']));
	        $html .= '<div id="bia_'. $_REQUEST['post_id'] .'" class="table-responsive">
	            <table class="table table-bordered table-survey mb bia-core table_bia">
	                <thead>
	                    <tr>
	                        <th colspan="2"><strong class="font-120p">Department: ' . $area['name'] . '</strong></th>
	                        <th class="no-padding"><a href="'. site_url('pdf') .'?pid='. $_REQUEST['post_id'] .'&dept='. advisory_id_from_string($area['name']) .'" target="_blank" style="display:block;text-align:center;" postid="'. $_REQUEST['post_id'] .'"><img src="'.P3_TEMPLATE_URI.'/images/pdf/power.png" style="height:35px;"></a></th>
	                        <th class="no-padding"><div class="total-risk text-center ' . coloring_elements($biaScoreAVG, 'bia-score2') . '"><strong>' . bia_level2($biaScoreAVG) .'</strong></div></th>
	                    </tr>
	                    <tr>
	                        <th class="t-heading-dark strong">Service/Process</th>
	                        <th class="t-heading-dark text-center strong" style="width:100px;">Calculated RTO</th>
	                        <th class="t-heading-dark text-center strong" style="width:100px;">Criticality Level</th>
	                        <th class="t-heading-dark text-center strong" style="width:80px;">IT Services</th>
	                    </tr>
	                </thead>
	                <tbody>';
	                if ($area['services']) {
	                    foreach ($area['services'] as $service) {
	                    	if (!empty($service['req']) && $service['req'] != 'N/A') {
	                    		$serviceReqColor = ' style="background:red"';
	                    		$serviceReqClass = 'ITServiceCatalogue';
	                    		$serviceReqCount = count(explode(',', $service['req']));
	                    	} else {
	                    		$serviceReqColor = ' style="background:green"';
	                    		$serviceReqClass = '';
	                    		$serviceReqCount = 0;
	                    	}
	                        $html .= '<tr>';
	                            $html .= '<th class="t-heading-sky w-260px">' . $service['name'] . '</th>';
	                            $html .= '<th class="text-center">' . $service['rto'] . '</th>';
	                            $html .= '<th class="text-center '. coloring_elements($service['cr'], 'bia-score') .'">'. round($service['cr']) .'</th>';
	                            $html .= '<th class="text-center '.$serviceReqClass.'" services="'. $service['req'] .'" '. $serviceReqColor .'>'.$serviceReqCount.'</th>';
	                        $html .= '</tr>';
	                    }
	                }
	                $html .= '</tbody>
	            </table>
	        </div>';
        }
	} elseif (in_array($requestedPostType, $allRisks)) {
		$avg = [];
        $char_arr = [];
        $char = 'A';
        $tmp_threats = [];
        foreach ($data as $area) {
            $tmp_threats = array_merge($tmp_threats, (is_array($area['threats']) ? $area['threats'] : []));
            if ($area['threats']) {
                foreach ($area['threats'] as $threat) {
                    array_push($avg, $threat['avg']);
                }
                $avg = array_unique($avg);
                sort($avg);
            }
        }
        foreach ($avg as $key => $value) {
            $char_arr[$value] = $char;
            $char++;
        }
        $html .= '<div class="text-center">
            <img src="'.P3_TEMPLATE_URI.'/images/value-scorecard-risk.jpg" class="img-responsive">
        </div><br>';
        if ($requestedPostType == 'risk') {
        	$scoreType = $requestedPostType == 'bcp' ? 'bcp-score' : 'risk-score';
        	$heatLoop = $requestedPostType == 'bcp' ? 126 : 17;
	        $html .= '<div class="row">
		        <div class="col-sm-12">
		            <div class="score-table mb clearfix">
		                <ul>';
		                    for ($i=0; $i < $heatLoop; $i++) { 
		                        $html .= '<li class="' . coloring_elements($i, $scoreType) . '">
		                            <strong>' . $i . '</strong>
		                            ' . (array_key_exists($i, $char_arr) ? '<div class="score-list-value">(' . $char_arr[$i] . ')</div>' : '') . '
		                        </li>';
		                    }
		                    $html .= '<li class="t-heading-dark"> <strong>Risk Heat Map</strong> </li>
		                </ul>
		            </div>
		        </div>
		    </div>';
        }
        if ($requestedPostType == 'bcp') {
        	$scoreType = $requestedPostType == 'bcp' ? 'bcp-score' : 'risk-score';
        	$heatLoop = $requestedPostType == 'bcp' ? 126 : 17;
        	$heatSteps = bcp_heat_headers($char_arr);
        	// $html .= '<br><pre>'. print_r($heatSteps, true) .'</pre>';
	        $html .= '<div class="row">
		        <div class="col-sm-12">
		            <div class="score-table mb clearfix">
		                <ul class="heatIndex">';
		                	if (!empty($heatSteps)) {
		                		foreach ($heatSteps as $heatStep) {
		                			$html .= '<li class="'. $heatStep['color'] .'"> <strong>' . $heatStep['range'] . '</strong> ' . $heatStep['char'] . '</li>';
		                		}
		                	}
		                    $html .= '<li class="t-heading-dark"> <strong>Risk Heat Map</strong> </li>
		                </ul>
		            </div>
		        </div>
		    </div>';
        }
	    $html .= '<div class="score-rank-table">
	        <div class="table-responsive">
	            <table class="table table-bordered table-survey strong text-center">
	                <thead>
	                    <tr>
	                        <th class="font-110p strong text-center">Letter</th>
                            <th class="font-110p strong text-center">Threat (Category)</th>
                            <th class="font-110p strong text-center">Rank</th>
	                    </tr>
	                </thead>
	                <tbody>';
	                    if ($tmp_threats) {
	                        usort($tmp_threats, 'advisory_tmp_threat_sort');
	                        foreach ($tmp_threats as $threat) {
								$scoreType = $requestedPostType == 'bcp' ? 'bcp-score' : 'risk-score';
	                            $html .= '<tr>
	                                <td>' . $char_arr[$threat['avg']] . '</td>
	                                <td>' . $threat['name'] . ' (' . $threat['cat'] . ')</td>
	                                <td><span class="box-padding ' . coloring_elements($threat['avg'], $scoreType) . '"></span>&nbsp;&nbsp;' . $threat['avg'] . '</td>
	                            </tr>';
	                        }
	                    }
	                $html .= '</tbody>
	            </table>
	        </div>
	    </div>';
	} elseif ($requestedPostType == 'itsm') {
		$html .= '<div class="text-center"> <img src="' .P3_TEMPLATE_URI.'/images/value-scorecard.jpg" class="img-responsive" alt=""> </div><br>
		<div class="table-responsive">
			<table class="table table-bordered table-survey table_itsm">
				<tbody>';
				foreach ($data as $key => $area) {
					$loop = 0;
					foreach ($area['sections'] as $section) {
						if ($loop == 0 || !empty(array_diff_key($tmp_fields, $section['fields']))) {
							$tmp_fields = $section['fields'];
							$colspan = ($area['criteria'] == 'single' ? 3 : count($section['fields']) + 2);
							$formMeta = get_post_meta($form['section_form'], 'form_opts', true);
							$html .= '<tr><th class="t-heading-dark h4" colspan="' . $colspan . '">' . $area['name'] . '</th></tr>';
							if ($area['criteria'] == 'single') {
								$html .= '<tr><th class="t-heading-sky text-center">' . __('Section', 'advisory') . '</th><th class="t-heading-sky text-center">' . __('Description', 'advisory') . '</th>';
							} else {
								$html .= '<tr><th class="t-heading-sky">' . (empty($area['icon']) ? '' : '<img src="' . $area['icon'] . '" alt="">') . '</th>';
								/* foreach ($section['fields'] as $key => $field) {
									$html .= '<th class="t-heading-sky text-center">' . advisory_get_criteria_label($key) . '</th>';
								} */
							}
							$html .= '<th class="t-heading-sky text-center">Rating Level</th></tr>';
						}
						$loop++;
						$html .= '<tr>
			                        <td>' . $section['name'] . '</td>';
						if ($area['criteria'] == 'single') {
							$html .= '<td class="text-center">' . $section['desc'] . '</td>';
						} else {
							/* foreach ($section['fields'] as $field) {
								$html .= '<td class="text-center">' . ($field == 0 ? 'N/A' : number_format($field, 1)) . '</td>';
							} */
						}
						$html .= '<td class="text-center ' . coloring_elements($section['avg'], 'avg') . '">' . $section['avg'] . '</td>
			                    </tr>';
					}
				}
				$html .= '</tbody>
			</table>
		</div>';
	} elseif ($requestedPostType == 'drm') {
		$html .= '<div class="text-center"> <img src="' .P3_TEMPLATE_URI.'/images/drm-value-scorecard.jpg" class="img-responsive" alt=""> </div><br>
		<div class="table-responsive">
			<table class="table table-bordered table-survey table_drm">
				<tbody>';
				foreach ($data as $key => $area) {
					$loop = 0;
					foreach ($area['sections'] as $section) {
						$avg = advisory_drm_avg_class_and_text($section['avg']);
						if ($loop == 0 || !empty(array_diff_key($tmp_fields, $section['fields']))) {
							$tmp_fields = $section['fields'];
							$colspan = ($area['criteria'] == 'single' ? 3 : count($section['fields']) + 2);
							$formMeta = get_post_meta($form['section_form'], 'form_opts', true);
							$html .= '<tr><th class="t-heading-dark h4" colspan="' . $colspan . '">' . $area['name'] . '</th></tr>';
							if ($area['criteria'] == 'single') {
								$html .= '<tr><th class="t-heading-sky text-center">' . __('Section', 'advisory') . '</th><th class="t-heading-sky text-center">' . __('Description', 'advisory') . '</th>';
							} else {
								$html .= '<tr><th class="t-heading-sky">' . (empty($area['icon']) ? '' : '<img src="' . $area['icon'] . '" alt="">') . '</th>';
								/* foreach ($section['fields'] as $key => $field) {
									$html .= '<th class="t-heading-sky text-center">' . advisory_get_criteria_label($key) . '</th>';
								} */
							}
							$html .= '<th class="t-heading-sky text-center">Rating Level</th></tr>';
						}
						$loop++;
						$html .= '<tr>
			                        <td>' . $section['name'] . '</td>';
						if ($area['criteria'] == 'single') {
							$html .= '<td class="text-center">' . $section['desc'] . '</td>';
						} else {
							/* foreach ($section['fields'] as $field) {
								$html .= '<td class="text-center">' . ($field == 0 ? 'N/A' : number_format($field, 1)) . '</td>';
							} */
						}
						$html .= '<td class="text-center '.$avg['cls'].'">'.$section['avg'].'</td>
			                    </tr>';
					}
				}
				$html .= '</tbody>
			</table>
		</div>';
	} elseif ($requestedPostType == 'ihc') {
		$html .= '<a href="'. site_url('pdf') .'?pid='. $_REQUEST['post_id'] .'" target="_blank" class="btn btn-xs btn-primary pdf-btn pull-right" postid="'. $_REQUEST['post_id'] .'">Executive Summary</a>';
		// SCORECARD CONTENT 
		$html .= '<div class="text-center"> <img src="' .P3_TEMPLATE_URI.'/images/ihc-value-scorecard.jpg" class="img-responsive" alt=""> </div><br>
		<div class="table-responsive">
			<table class="table table-bordered table-survey table_others">
				<tbody>';
				foreach ($data as $key => $area) {
					$savg = $stotal = $loop = 0;
					$areaHtml = '';
					foreach ($area['sections'] as $section) {
						// $areaHtml .= '<tr><td colspan="2"><pre>'. json_encode($section) .'</pre><td></tr>';
						$avg = $section['avg'] ?? 0;
						$stotal += $avg;
						if ($loop == 0 || !empty(array_diff_key($tmp_fields, $section['fields']))) {
							$tmp_fields = $section['fields'];
							$colspan = ($area['criteria'] == 'single' ? 3 : count($section['fields']) + 1);
							$formMeta = get_post_meta($form['section_form'], 'form_opts', true);
							$areaHtml .= '<tr>';
								$areaHtml .= '<th class="t-heading-dark h4" style="font-size: 20px;" colspan="' . $colspan . '">' . $area['name'] . '</th>';
								$areaHtml .= '<th class="text-center ##SAVGC##">##SAVGV##</th>';
							$areaHtml .= '</tr>';
							$areaHtml .= '<tr>';
								$areaHtml .= '<th class="t-heading-sky text-center"> </th>';
								$areaHtml .= '<th class="t-heading-sky text-center">Rating Level</th>';
							$areaHtml .= '</tr>';
						}
						$loop++;
						$areaHtml .= '<tr>';
						$areaHtml .= '<td style="font-weight: 700;">' . $section['name'] . '</td>';
						$areaHtml .= '<td class="text-center ' . ihcColorAVG($avg) . '">' . number_format($avg,1) . '</td>';
						$areaHtml .= '</tr>';
					}
					$savg = IHCRAvgStatus($stotal / $loop);
					$html .= str_replace(['##SAVGC##','##SAVGV##'], [$savg['cls'], $savg['txt']], $areaHtml);
				}
				$html .= '</tbody>
			</table>
		</div>';
	} elseif ($requestedPostType == 'mta') {
		$registerLink = home_url('mta-register/');
		// $areaID = !empty($_REQUEST['area_id']) && false ? '&&area='.$_REQUEST['area_id'] : '';
		// $html .= '<a href="'. site_url('pdf') .'?pid='. $_REQUEST['post_id'] . $areaID .'" target="_blank" class="btn btn-xs btn-primary pdf-btn pull-right">Executive Summary</a>';
		// $html .= '<br><pre>'. print_r($data, true) .'</pre>';
		$html .= '<div class="table-responsive">
			<table class="table table-bordered table-survey table_others">
				<tbody>';
				foreach ($data as $key => $area) {
					$savg = $stotal = $loop = 0; 
					$areaHtml = '';
					foreach ($area['sections'] as $section) {
						// $areaHtml .= '<tr><td colspan="2"><pre>'. print_r($section, true) .'</pre><td></tr>';
						$link = $registerLink.'?cat='.advisory_id_from_string($area['name']).'&th='. advisory_id_from_string($section['name']);

						if ($loop == 0 || !empty(array_diff_key($tmp_fields, $section['fields']))) {
							$tmp_fields = $section['fields'];
							$colspan = ($area['criteria'] == 'single' ? 3 : count($section['fields']) + 1);
							$formMeta = get_post_meta($form['section_form'], 'form_opts', true);
							$areaHtml .= '<tr>';
								$areaHtml .= '<th class="t-heading-dark h4" style="font-size: 20px;" colspan="' . $colspan . '">' . $area['name'] . '</th>';
								$areaHtml .= '<th class="text-center ##SAVGC##">##SAVGV##</th>';
							$areaHtml .= '</tr>';
						}
						$areaHtml .= '<tr>';
							$areaHtml .= '<th class="t-heading-sky">'. $section['name'] .'</th>';
							$areaHtml .= '<th class="t-heading-sky text-center">Rating Level</th>';
						$areaHtml .= '</tr>';
						if (!empty($section['tables'])) {
							foreach ($section['tables'] as $subTables) {
								if (!empty(trim($subTables['name']))) {
									$subLink = $link .'&sub='. advisory_id_from_string($subTables['name']);
									$areaHtml .= '<tr>';
									$areaHtml .= '<td style="font-weight: 700;"><a style="color:#000" target="_blank" href="'.$subLink.'">'. $subTables['name'] .'</a></td>';
									if (!empty($subTables['rate']) && $subTables['rate'] == 'g') $areaHtml .= '<td class="text-center color-gap">GAP</td>';
									else {
										if ($avg != 'g') { $stotal += $subTables['rate']; $loop++; }
										$areaHtml .= '<td class="text-center '. mtaColorAVG(number_format($subTables['rate'], 1)) .'">' . number_format($subTables['rate'],1) . '</td>';

									}
									$areaHtml .= '</tr>';
								}
							}
						}
					}
					if ($loop) $savg = MTAAvgStatus($stotal / $loop);
					else $savg = ['cls' => 'color-gap', 'txt' => 'GAP'];
					$html .= str_replace(['##SAVGC##','##SAVGV##'], [$savg['cls'], $savg['txt']], $areaHtml);
				}
				$html .= '</tbody>
			</table>
		</div>';
	} elseif ($requestedPostType == 'dmm') {
		$html .= '<div class="text-center"> <img src="' .P3_TEMPLATE_URI.'/images/value_scorecard_heading_dmm.jpg" class="img-responsive" alt=""> </div><br>
		<div class="table-responsive">
			<table class="table table-bordered table-survey table_others">
				<tbody>';
				foreach ($data as $key => $area) {
					$loop = 0;
					foreach ($area['sections'] as $section) {
						if ($loop == 0 || !empty(array_diff_key($tmp_fields, $section['fields']))) {
							$tmp_fields = $section['fields'];
							$colspan = ($area['criteria'] == 'single' ? 3 : count($section['fields']) + 2);
							$formMeta = get_post_meta($form['section_form'], 'form_opts', true);
							$html .= '<tr><th class="t-heading-dark h4" colspan="' . $colspan . '">' . $area['name'] . '</th></tr>';
							if ($area['criteria'] == 'single') {
								$html .= '<tr><th class="t-heading-sky text-center">' . __('Section', 'advisory') . '</th><th class="t-heading-sky text-center">' . __('Description', 'advisory') . '</th>';
							} else {
								$html .= '<tr><th class="t-heading-sky">' . (empty($area['icon']) ? '' : '<img src="' . $area['icon'] . '" alt="">') . '</th>';
								foreach ($section['fields'] as $key => $field) {
									$html .= '<th class="t-heading-sky text-center">' . advisory_get_criteria_label($key) . '</th>';
								}
							}
							$html .= '<th class="t-heading-sky text-center">Rating Level</th></tr>';
						}
						$loop++;
						$html .= '<tr>
			                        <td style="font-weight: 700;">' . $section['name'] . '</td>';
						if ($area['criteria'] == 'single') {
							$html .= '<td class="text-center">' . $section['desc'] . '</td>';
						} else {
							foreach ($section['fields'] as $field) {
								$html .= '<td class="text-center">' . ($field == 0 ? 'N/A' : number_format($field, 1)) . '</td>';
							}
						}
						$html .= '<td class="text-center ' . DMMAvgColor2(round($section['avg'])) . '">' . DMMAvgTxt(round($section['avg'])) . '</td>
			                    </tr>';
					}
				}
				$html .= '</tbody>
			</table>
		</div>';
	} elseif ($requestedPostType == 'itcl') {
		$default = advisory_form_default_values($_REQUEST['post_id'], 'security');
        // $html .= help($default,false);

	    $date = !empty($default['date']) ? ' – '.date(get_option( 'date_format'), strtotime($default['date'])) : '';

		$html .= '<style>';
            $html .= '.corporateLandscape .main-heading {background: #000;color: #fff;padding: 5px 10px;font-size: 20px;font-weight: 700;}';
            $html .= '.corporateLandscape .table {margin-bottom: 0 !important;}';
            $html .= '.corporateLandscape .table-bordered>tbody>tr>td,.corporateLandscape .table-bordered>tbody>tr>th {border: 1px solid #000;padding: 4px;font-weight: 700;}';
            $html .= '.corporateLandscape .sub-heading.blue {background: #6c91d1;}';
            $html .= '.corporateLandscape .sub-heading.orange {background: #de810f;}';
            $html .= '.corporateLandscape .sub-heading.green {background: #73b048;}';
            $html .= '.corporateLandscape .sub-heading th {font-size: 18px;font-weight: 700;color: #000;}';
            $html .= '.corporateLandscape .table .gray {background: #d9d9d9;}';
            $html .= '.corporateLandscape .table .yellow {background: #ffff00;}';
            $html .= '.corporateLandscape .table .sub-section {background: #3a3838;color: #fff;font-weight: 700;}';
            $html .= '.corporateLandscape .sub-heading th,.corporateLandscape .sub-heading td,.corporateLandscape .table .sub-section th{text-align: left !important;}';
        $html .= '</style>';
        $html .= '<div class="corporateLandscape">';
            $html .= '<img src="' .P3_TEMPLATE_URI.'/images/report_card-01.jpg" class="img-responsive" alt="">';
            $html .= '<div class="main-heading"> Corporate Landscape'. $date .'</div>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-heading blue">';
                    $html .= '<th colspan="6">General</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray">Municipality</td>';
                    $html .= '<td class="italic">'. @$default['municipality'] .'</td>';
                    $html .= '<td class="gray">Population</td>';
                    $html .= '<td class="italic">'. @$default['population'] .'</td>';
                    $html .= '<td class="gray">Land Area (km<sup>2</sup>)</td>';
                    $html .= '<td class="italic">'. @$default['area'] .'</td>';
                $html .= '</tr>';
            $html .= '</table><br>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-heading orange">';
                    $html .= '<th colspan="8" class="noBorder">Staff and Facilities (Supported by IT)</th>';
                $html .= '</tr>';
                $html .= '<tr class="sub-section">';
                    $html .= '<th colspan="8">Staff</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray">Full-time</td>';
                    $html .= '<td class="italic">'. @$default['stafffulltime'] .'</td>';
                    $html .= '<td class="gray">Part-time</td>';
                    $html .= '<td class="italic">'. @$default['staffparttime'] .'</td>';
                    $html .= '<td class="gray">Contractors</td>';
                    $html .= '<td class="italic">'. @$default['staffcontractor'] .'</td>';
                    $html .= '<td class="gray">Other(e.g. students, interns)</td>';
                    $html .= '<td class="italic">'. @$default['staffother'] .'</td>';
                $html .= '</tr>';
            $html .= '</table><br>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-section">';
                    $html .= '<th colspan="11">Supported Facilities</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray">Admin</td>';
                    $html .= '<td class="italic">'. @$default['sfadmin'] .'</td>';
                    $html .= '<td class="gray">Library</td>';
                    $html .= '<td class="italic">'. @$default['sflibrary'] .'</td>';
                    $html .= '<td class="gray">Recreation</td>';
                    $html .= '<td class="italic">'. @$default['sfrecreation'] .'</td>';
                    $html .= '<td class="gray">Fire</td>';
                    $html .= '<td class="italic">'. @$default['sffire'] .'</td>';
                    $html .= '<td class="gray">Other (e.g. Long-Term Care)</td>';
                    $html .= '<td class="italic">'. @$default['sfother'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="yellow">Notes:</td>';
                    $html .= '<td class="italic" style="width: 90%;">'. @$default['sftnotes'] .'</td>';
                $html .= '</tr>';
            $html .= '</table><br>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-heading green">';
                    $html .= '<th colspan="12" class="noBorder">Information Technology</th>';
                $html .= '</tr>';
                $html .= '<tr class="sub-section">';
                    $html .= '<th colspan="12">IT Staff (Breakdown of Roles)</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray">Network</td>';
                    $html .= '<td>'. @$default['itnetwork'] .'</td>';
                    $html .= '<td class="gray">Security</td>';
                    $html .= '<td>'. @$default['itsecurity'] .'</td>';
                    $html .= '<td class="gray">Service Desk</td>';
                    $html .= '<td>'. @$default['itsd'] .'</td>';
                    $html .= '<td class="gray">Database</td>';
                    $html .= '<td>'. @$default['itdb'] .'</td>';
                    $html .= '<td class="gray">GIS</td>';
                    $html .= '<td>'. @$default['itgis'] .'</td>';
                    $html .= '<td class="gray">General</td>';
                    $html .= '<td>'. @$default['itgs'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="gray">Application</td>';
                    $html .= '<td>'. @$default['itas'] .'</td>';
                    $html .= '<td class="gray">CIO</td>';
                    $html .= '<td>'. @$default['itcio'] .'</td>';
                    $html .= '<td class="gray">CTO</td>';
                    $html .= '<td>'. @$default['itcto'] .'</td>';
                    $html .= '<td class="gray">Director</td>';
                    $html .= '<td>'. @$default['itderector'] .'</td>';
                    $html .= '<td class="gray">Manager</td>';
                    $html .= '<td>'. @$default['itmanage'] .'</td>';
                    $html .= '<td class="gray">Supervisor</td>';
                    $html .= '<td>'. @$default['itsupervisor'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="yellow">Notes:</td>';
                    $html .= '<td style="width: 90%;">'. @$default['ittnotes'] .'</td>';
                $html .= '</tr>';
            $html .= '</table><br>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-section">';
                    $html .= '<th colspan="6">IT Budget (Annual)</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray">IT Budget (Capital)</td>';
                    $html .= '<td>'. @$default['itbadget'] .'</td>';
                    $html .= '<td class="gray">IT Budget (Operating)</td>';
                    $html .= '<td>'. @$default['itboperating'] .'</td>';
                    $html .= '<td class="gray">Percentage of Corporate</td>';
                    $html .= '<td>'. @$default['itpcb'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="yellow">Notes:</td>';
                    $html .= '<td style="width: 90%;">'. @$default['itbtnotes'] .'</td>';
                $html .= '</tr>';
            $html .= '</table><br>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-section">';
                    $html .= '<th colspan="10">Technology (In-House)</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray">Desktop</td>';
                    $html .= '<td>'. @$default['tihdesktop'] .'</td>';
                    $html .= '<td class="gray">Laptop</td>';
                    $html .= '<td>'. @$default['tihlaptop'] .'</td>';
                    $html .= '<td class="gray">Tablet</td>';
                    $html .= '<td>'. @$default['tihtablet'] .'</td>';
                    $html .= '<td class="gray">Smartphone</td>';
                    $html .= '<td>'. @$default['tihsmartphone'] .'</td>';
                    $html .= '<td class="gray">Desk Phone</td>';
                    $html .= '<td>'. @$default['tihdeskphone'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="gray" style="width: 12%;">Telephony</td>';
                    $html .= '<td>'. @$default['tihtp'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="gray">Physical Host</td>';
                    $html .= '<td>'. @$default['tihph'] .'</td>';
                    $html .= '<td class="gray">Virtual Host</td>';
                    $html .= '<td>'. @$default['tihtvh'] .'</td>';
                    $html .= '<td class="gray">Server OS</td>';
                    $html .= '<td>'. @$default['tihsos'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="gray">Total Storage</td>';
                    $html .= '<td>'. @$default['tihts'] .'</td>';
                    $html .= '<td class="gray">Used Storage</td>';
                    $html .= '<td>'. @$default['tihus'] .'</td>';
                    $html .= '<td class="gray" colspan="2">Corporate Applications</td>';
                    $html .= '<td>'. @$default['tihcp'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
	            $html .= '<tr>';
	                $html .= '<td class="yellow">Notes:</td>';
	                $html .= '<td style="width: 90%;">'. @$default['tinotes'] .'</td>';
	            $html .= '</tr>';
	        $html .= '</table><br>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-section">';
                    $html .= '<th colspan="8">Technology (Cloud)</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray">Desktop</td>';
                    $html .= '<td>'. @$default['tcdesktop'] .'</td>';
                    $html .= '<td class="gray">Server</td>';
                    $html .= '<td>'. @$default['tcservice'] .'</td>';
                    $html .= '<td class="gray">Storage</td>';
                    $html .= '<td>'. @$default['tcstroage'] .'</td>';
                    $html .= '<td class="gray">Applications (SaaS/laaS/PaaS)</td>';
                    $html .= '<td>'. @$default['tcca'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="gray">Telephony</td>';
                    $html .= '<td style="width: 88%;">'. @$default['tctel'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="yellow">Notes:</td>';
                    $html .= '<td style="width: 90%;">'. @$default['tcnotes'] .'</td>';
                $html .= '</tr>';
            $html .= '</table><br>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr class="sub-section">';
                    $html .= '<th colspan="2">Network</th>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="gray" style="width: 10%;">Internet</td>';
                    $html .= '<td>'. @$default['netinternet'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="gray" style="width: 8%;">WAN</td>';
                    $html .= '<td>'. @$default['netwan'] .'</td>';
                    $html .= '<td class="gray" style="width: 8%;">LAN</td>';
                    $html .= '<td>'. @$default['netlan'] .'</td>';
                    $html .= '<td class="gray"  style="width: 10%;">Wireless</td>';
                    $html .= '<td>'. @$default['netwireless'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="yellow">Notes:</td>';
                    $html .= '<td style="width: 90%;">'. @$default['netnotes'] .'</td>';
                $html .= '</tr>';
            $html .= '</table>';
        $html .= '</div>';
	} else {
		$html .= '<div class="text-center">';
       if($requestedPostType == 'cra') $html .= '<img src="' .P3_TEMPLATE_URI.'/images/value_scorecard_cloud_readiness.jpg" class="img-responsive" alt="">';
       else $html .= '<img src="' .P3_TEMPLATE_URI.'/images/value-scorecard.jpg" class="img-responsive" alt="">';
		$html .= '</div><br>
		<div class="table-responsive">
			<table class="table table-bordered table-survey table_others">
				<tbody>';
				foreach ($data as $key => $area) {
					$loop = 0;
					foreach ($area['sections'] as $section) {
						if ($loop == 0 || !empty(array_diff_key($tmp_fields, $section['fields']))) {
							$tmp_fields = $section['fields'];
							$colspan = ($area['criteria'] == 'single' ? 3 : count($section['fields']) + 2);
							$formMeta = get_post_meta($form['section_form'], 'form_opts', true);
							$html .= '<tr><th class="t-heading-dark h4" colspan="' . $colspan . '">' . $area['name'] . '</th></tr>';
							if ($area['criteria'] == 'single') {
								$html .= '<tr><th class="t-heading-sky text-center">' . __('Section', 'advisory') . '</th><th class="t-heading-sky text-center">' . __('Description', 'advisory') . '</th>';
							} else {
								$html .= '<tr><th class="t-heading-sky">' . (empty($area['icon']) ? '' : '<img src="' . $area['icon'] . '" alt="">') . '</th>';
								foreach ($section['fields'] as $key => $field) {
									$html .= '<th class="t-heading-sky text-center">' . advisory_get_criteria_label($key) . '</th>';
								}
							}
							$html .= '<th class="t-heading-sky text-center">Rating Level</th></tr>';
						}
						$loop++;
						$html .= '<tr>
			                        <td>' . $section['name'] . '</td>';
						if ($area['criteria'] == 'single') {
							$html .= '<td class="text-center">' . $section['desc'] . '</td>';
						} else {
							foreach ($section['fields'] as $field) {
								$html .= '<td class="text-center">' . ($field == 0 ? 'N/A' : number_format($field, 1)) . '</td>';
							}
						}
						$html .= '<td class="text-center ' . coloring_elements($section['avg'], 'avg') . '">' . $section['avg'] . '</td>
			                    </tr>';
					}
				}
				$html .= '</tbody>
			</table>
		</div>';
	}
	echo $html; wp_die();
}
add_action('wp_ajax_dashboard_scorecard', 'advisory_ajax_dashboard_scorecard');
function getPDFSectionsData($tableID, $section, $prefix, $pdfData=false) {
	$total = 0;
	$data = ['name'=>'','assessmentTitle'=>'','assessmentDesc'=>'','summaryTitle'=>'','summaryDesc'=>'', 'avg'=>0];
	// return $prefix.$tableID.'_aassessment_title <br> ihc_pdf_sections_operations_tables_government_for_enterprise_it_aassessment_title';
	$name = '';
	if ($pdfData) {
		$data['assessmentTitle']= $pdfData[$prefix.$tableID.'_assessment_title'];
		$data['assessmentDesc'] = $pdfData[$prefix.$tableID.'_assessment_desc'];
		$data['summaryTitle'] 	= $pdfData[$prefix.$tableID.'_summary_title'];
		$data['summaryDesc'] 	= $pdfData[$prefix.$tableID.'_summary_desc'];
	} else {
		$data['assessmentTitle']= cs_get_option($prefix.$tableID.'_assessment_title');
		$data['assessmentDesc'] = cs_get_option($prefix.$tableID.'_assessment_desc');
		$data['summaryTitle'] 	= cs_get_option($prefix.$tableID.'_summary_title');
		$data['summaryDesc'] 	= cs_get_option($prefix.$tableID.'_summary_desc');
	}
	if (!empty($section['tables'])) {
		foreach ($section['tables'] as $table) { $data['name'] .= $table['name'] .' - '; }
		$data['name'] = rtrim($data['name'], ' - ');
	}
	$data['avg']  = !empty($section['avg']) ? $section['avg'] : 0;
	return $data;
}
/***********************************************
 *
 * Helper Functions
 * @used backend - option & metabox
 *
 **********************************************/
// registered criteria
function advisory_registered_criteria($name = 'ihc'): array{
	$data = [];
	if ($name == 'bia') $criteria = cs_get_option('criteria_bia');
	elseif($name == 'itsm') $criteria = cs_get_option('criteria_itsm');
	elseif($name == 'risk') $criteria = cs_get_option('criteria_risk');
	elseif($name == 'bcp') $criteria = cs_get_option('criteria_risk');
	elseif($name == 'prr') $criteria = cs_get_option('criteria_risk');
	elseif($name == 'cra') $criteria = cs_get_option('criteria_cra');
	elseif($name == 'drm') $criteria = cs_get_option('criteria_drm');
	elseif($name == 'drmrr') $criteria = cs_get_option('criteria_risk');
	elseif($name == 'dmm') $criteria = cs_get_option('criteria_drm');
	elseif($name == 'dmmr') $criteria = cs_get_option('criteria_risk');
	else $criteria = cs_get_option('criteria_ihc');
	foreach ($criteria as $c) { $data[$c['id']] = $c['name']; }
	return $data;
}
// criteria label by id
function advisory_get_criteria_label($id) {
	$criteria = array_merge(cs_get_option('criteria_itsm'), cs_get_option('criteria_cra'), cs_get_option('criteria_bia'), cs_get_option('criteria_risk'), cs_get_option('criteria_drm'));
	if (!empty($criteria)) {
		foreach ($criteria as $c) {
			if ($id == $c['id']) {
				return (!empty($c['label']) ? $c['label'] : $c['name']);
			}
		}
	}
	return false;
}
// generate id from string
function advisory_id_from_string($string): string{
	$string = str_replace(['#', '[', '(', ')', '-', '+', '/', ']', ' ', '?', ';'], '_', strtolower(trim($string)));
	$string = str_replace(['&'], 'sand', $string);
	$string = str_replace(',', 'comaoperator', $string);
	$string = str_replace('.', 'dotoperator', $string);
	return $string;
}
function advisory_string_from_id($string): string{
	$string = ucwords(str_replace('_', ' ', trim($string)));
	$string = str_replace('sand', '&', $string);
	$string = str_replace('comaoperator', ',', $string);
	$string = str_replace('dotoperator', '.', $string);
	return $string;
}
// check edit screen
function is_edit_page($new_edit = null) {
	global $pagenow;
	if (!is_admin()) { return false; }
	if ($new_edit == "edit") { return in_array($pagenow, array('post.php')); } 
	elseif ($new_edit == "new") { return in_array($pagenow, array('post-new.php')); } 
	else { return in_array($pagenow, array('post.php', 'post-new.php')); }
}
// generate array from text box multiline data
function advisory_select_array($strOptions=null): array{
	$opts = [];
	$options = explode(PHP_EOL, $strOptions);
	if ($options) {
		foreach ($options as $key => $val) {
	        $tmp = explode(':', $val);
	        $opts[$tmp[0]] = $tmp[1];
	    }
	}
	return $opts;
}
// generate level importance array
function advisory_level_importance(): array{
	$criteria = cs_get_option('level_importance');
	$opts = array();
	if (!empty($criteria)) {
		$arr = explode(PHP_EOL, $criteria);
		foreach ($arr as $ar) {
			$val = substr($ar, 0, strpos($ar, ":"));
			$title = substr($ar, strpos($ar, ":") + 1);
			$opts[$val] = $title;
		}
	}
	return $opts;
}
// generate criteria weights
function advisory_criteria_weights() {
	if (is_admin() && is_edit_page()) {
		$criteria = cs_get_option('criteria_ihc');
		$data = [];
		foreach ($criteria as $c) {
			$data[] = array(
				'id' => $c['id'],
				'type' => 'select',
				'title' => $c['name'],
				'options' => advisory_level_importance(),
			);
		}
		return $data;
	}
}
// get registered user data
function advisory_registered_users(): array{
	$users = get_users(array('role' => 'Viewer'));
	$data = [];
	foreach ($users as $user) {
		$data[$user->ID] = $user->display_name;
	}
	return $data;
}
// get registered company data
function advisory_registered_companies(): array{
	$companies = get_terms(array('taxonomy' => 'company'));
	$data = [];
	foreach ($companies as $company) {
		$data[$company->term_id] = $company->name;
	}
	return $data;
}
/***********************************************
 *
 * Helper Functions
 * @used frontend
 *
 **********************************************/
function advisory_get_user_avatar($user_id): string {
	if ($avatar = get_user_meta($user_id, 'avatar', true)) { return $avatar; }
	return P3_TEMPLATE_URI. '/images/avatar.png';
}
function advisory_get_user_company($user_id = null) {
	if ($user_id) { $user_data = get_userdata($user_id); } 
	else { $user_data = wp_get_current_user(); }
	if (in_array('viewer', $user_data->roles)) {
		$terms = wp_get_object_terms($user_data->ID, 'company');
		return $terms[0];
	}
	return null;
}
function advisory_get_user_company_id($user_id = null) {
	if ($user_id) { $user_data = get_userdata($user_id); } 
	else { $user_data = wp_get_current_user(); }
	if ($user_data) {
		if (in_array('viewer', $user_data->roles)) {
			$terms = wp_get_object_terms($user_data->ID, 'company');
			return $terms[0]->term_id;
		}
	}
	return 0;
}
function advisory_get_active_forms($company_id, $post_types = array()): array{
	$ids = array();
	if (empty($post_types)) {
		$post_types = array_merge(['csa'], json_decode(ALL_POST_TYPES));
		if (isUserHasDashboardB()) {
			if (($key = array_search('ihc', $post_types)) !== false) {
			    unset($post_types[$key]);
			}
		} else {
			if (($key = array_search('mta', $post_types)) !== false) {
			    unset($post_types[$key]);
			}
		}
	}
	foreach ($post_types as $post_type) {
		$query = [
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'fields' => 'ids',
			'meta_query' => [['key' => 'assigned_company', 'value' => $company_id, 'compare' => '=']]
		];
		$query = new WP_Query($query);
		if ($query->found_posts > 0) {$ids[] = $query->posts[0]; }
	}
	return $ids;
}
function advisory_has_form_view_permission($post_id): bool {
	global $user_switching;
	if ($user_switching->get_old_user() && (in_array(get_post_status($post_id), ['publish']))) {
		return true;
	}
	return false;
}
function advisory_has_survey_view_permission($post_id = null): bool {
	if (!empty($post_id)) {
		if (in_array(get_post_status($post_id), ['publish', 'archived'])) {
			if (current_user_can('administrator') || current_user_can('advisor')) {
				return true;
			}
			if (get_post_field('assigned_company', $post_id) == advisory_get_user_company_id()) {
				return true;
			}
		}
	}
	return false;
}
function advisory_has_dmm_view_permission($post_id = null): bool {
	if (!empty($post_id)) {
		if (in_array(get_post_status($post_id), ['publish', 'archived'])) {
			if (current_user_can('administrator') || current_user_can('advisor') || current_user_can('viwer')) {
				return true;
			}
			if (get_post_field('assigned_company', $post_id) == advisory_get_user_company_id()) {
				return true;
			}
		}
	}
	return false;
}
function advisory_has_survey_edit_permission($post_id = null): bool {
	if (!empty($post_id)) {
		global $user_switching;
		if (get_post_status($post_id) == 'archived') {
			if (current_user_can('administrator') || current_user_can('advisor') || advisory_current_user_capabilities($post_id)) {
				return true;
			}
			if ($user_switching->get_old_user()) {
				return true;
			}
			// viewer
			$ids = get_user_meta(get_current_user_id(), 'edit_permissions', true);
			$ids = explode(',', $ids);
			if (in_array($post_id, $ids)) {
				return true;
			}
		}
	}
	return false;
}
function advisory_current_user_capabilities($post_id, $user_id=null) {
	if (!$user_id) $user_id = get_current_user_id();
	if (get_user_meta( $user_id, 'spuser', true)) return true;
	else {
		$post_type = get_post_type($post_id);
		$meta_keys = ['specialbiauser' => 'bia', 'specialriskuser'  => 'bcp'];
		foreach ($meta_keys as $meta_key => $meta_value) {
			if (get_user_meta( $user_id, $meta_key, true) && $meta_value == $post_type) return true;
		}

	}
	return false;
}
function advisory_has_survey_delete_permission($post_id = null): bool {
	global $user_switching;
	if ($user_switching->get_old_user()) return true;
	if (!current_user_can('viewer')) return true;
	return false;
}
function advisory_has_scorecard_view_permission($post_id = null): bool {
	if (!empty($post_id)) {
		if (get_post_status($post_id) == 'archived') {
			if (current_user_can('administrator') || current_user_can('advisor')) return true;
			if (get_post_field('assigned_company', $post_id) == advisory_get_user_company_id()) return true;
		}
	}
	return false;
}
function advisory_is_valid_form_submission($form_id): bool{
	$form_meta = get_post_meta($form_id, 'form_opts', true);
	$valid = true;
	$postType = get_post_type($form_id); 
	if ($postType == 'bia') {
		if (isset($form_meta['departments']) && !empty($form_meta['departments'])) {
            foreach ($form_meta['departments'] as $department) {
                $department_id = advisory_id_from_string($department['name']) . '_services';
                if ($services = $form_meta[$department_id]) {
                	foreach ($services as $service) {
						$base = $department_id . '_bia_' . advisory_id_from_string($service['name']);
		            	$data = advisory_form_default_values($form_id, $base);
		            	if (empty($data)) {
							$valid = false;
							break;
		            	} else {
		            		if ($data['avg'] == 0) {
								$valid = false;
								break;
							}
		            	}
					}
                }
            }
        }
        if (isset($form_meta['threat_cats']) && !empty($form_meta['threat_cats'])) {
        	foreach ($form_meta['threat_cats'] as $threat_cat) {
                $cat_id = advisory_id_from_string($threat_cat['name']);
                if ($threats = $form_meta[$cat_id . '_threats']) {
                    foreach ($threats as $threat) {
                        $base = $cat_id . '_' . advisory_id_from_string($threat['name']);
                        $data = advisory_form_default_values($form_id, $base);
                        if (empty($data)) {
							$valid = false;
							break 2;
		            	} else {
		            		if ($data['avg'] == 0) {
								$valid = false;
								break 2;
							}
		            	}
                    }
                }
        	}
        }
	} else if($postType == 'mta'){
		if (!empty($form_meta['areas'])) {
			foreach ($form_meta['areas'] as $area) {
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($form_meta[$section_id])) {
					foreach ($form_meta[$section_id] as $section) {
						$base = $section_id . '_tables_' . advisory_id_from_string($section['name']);
						$data = advisory_form_default_values($form_id, $base);
						if (empty($data)) {
							$valid = false;
							break 2;
						}
					}
				}
			}
		}
	} else if($postType == 'sfia'){
		if (!empty($form_meta['head'])) {
			foreach ($form_meta['head'] as $area) {
				if (empty($area)) {$valid = false; break 1; }
			}
		}
	} else {
		if (!empty($form_meta['areas'])) {
			foreach ($form_meta['areas'] as $area) {
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($form_meta[$section_id])) {
					foreach ($form_meta[$section_id] as $section) {
						$base = $section_id . '_tables_' . advisory_id_from_string($section['name']);
						$data = advisory_form_default_values($form_id, $base);
						if (empty($data)) {
							$valid = false;
							break 2;
						} else {
							if ($data['avg'] == 0) {
								$valid = false;
								break 2;
							}
						}
					}
				}
			}
		}
	}
	return $valid;
}
// get risk registry formatted data
function advisory_get_formatted_rr_data($post_id) {
	$meta = get_post_meta($post_id, 'form_opts', true);
	$data = [];
	$index = 0;
	if (!empty($meta['areas'])) {
		foreach ($meta['areas'] as $threat_cat) {
			$index2 = 0;
            $cat_id = advisory_id_from_string($threat_cat['name']);
			if ($threats = $meta[$cat_id . '_threats']) {
				foreach ($threats as $threat) {
					$index2++;
					$threat_id = $cat_id . '_' . advisory_id_from_string($threat['name']);
					$form_data = advisory_form_default_values($post_id, $threat_id);
					if ($form_data) {
						$data[$index]['cat'] = $threat_cat['name'];
						$data[$index]['base'] = $threat_cat['base'];
						$data[$index]['areas'][$index2]['name'] = $threat['name'];
						$data[$index]['areas'][$index2]['avg'] = $form_data['avg'];
						$data[$index]['areas'][$index2]['impact'] = [];
						$data[$index]['areas'][$index2]['probability'] = [];
						$data[$index]['areas'][$index2]['mitigation'] = [];
						$data[$index]['areas'][$index2]['bool'] = [];
						$data[$index]['areas'][$index2]['nq'] = $form_data['nq'];
						foreach ($form_data as $key => $value) {
							if (strpos($key, '_impact')) {$data[$index]['areas'][$index2]['impact'][] = $value; }
							if (strpos($key, '_probability')) {$data[$index]['areas'][$index2]['probability'][] = $value; }
							if (strpos($key, '_mitigation')) {$data[$index]['areas'][$index2]['mitigation'][] = $value; }
							if (strpos($key, '_bool')) {$data[$index]['areas'][$index2]['bool'][] = $value; }
						}
					}
				}
			}
			$index++;
		}
	}
	return $data;
}
function advisory_get_formatted_bcpr_data($post_id) {
	$meta = get_post_meta($post_id, 'form_opts', true);
	$data = [];
	$index = 0;
	if (!empty($meta['areas'])) {
		foreach ($meta['areas'] as $threat_cat) {
			$index2 = 0;
            $cat_id = advisory_id_from_string($threat_cat['name']);
			if ($threats = $meta[$cat_id . '_threats']) {
				foreach ($threats as $threat) {
					$index2++;
					$threat_id = $cat_id . '_' . advisory_id_from_string($threat['name']);
					$form_data = advisory_form_default_values($post_id, $threat_id);
					if ($form_data) {
						$data[$index]['cat'] = $threat_cat['name'];
						$data[$index]['base'] = $threat_cat['base'];
						$data[$index]['areas'][$index2]['name'] = $threat['name'];
						$data[$index]['areas'][$index2]['c_summary'] = @$form_data['summary'];
						$data[$index]['areas'][$index2]['c_historical_evidence'] = @$form_data['historical_evidence'];
						$data[$index]['areas'][$index2]['c_impact'] = @$form_data['impact'];
						$data[$index]['areas'][$index2]['c_probablity'] = @$form_data['probablity'];
						$data[$index]['areas'][$index2]['rd'] = $threat['desc'];
						$data[$index]['areas'][$index2]['lad'] = get_the_time(get_option( 'date_format'), $post_id);
						$data[$index]['areas'][$index2]['avg'] = $form_data['avg'];
						$data[$index]['areas'][$index2]['rw'] = @$form_data['ac'];
						$data[$index]['areas'][$index2]['ai'] = @$form_data['vc'];
						$data[$index]['areas'][$index2]['vulnerability'] = [];
						$data[$index]['areas'][$index2]['impact'] = [];
						$data[$index]['areas'][$index2]['probability'] = [];
						$data[$index]['areas'][$index2]['bool'] = [];
						$data[$index]['areas'][$index2]['nq'] = $form_data['nq'];
						// $data[$index]['areas'][$index2]['test'] = $form_data;
						foreach ($form_data as $key => $value) {
							if (strpos($key, '_vulnerability')) {$data[$index]['areas'][$index2]['vulnerability'][] = $value; }
							if (strpos($key, '_impact')) { $data[$index]['areas'][$index2]['impact'][] = $value; }
							if (strpos($key, '_probability')) {$data[$index]['areas'][$index2]['probability'][] = $value; }
							if (strpos($key, '_bool')) {$data[$index]['areas'][$index2]['bool'][] = $value; }
						}
					}
				}
			}
			$index++;
		}
	}
	return $data;
}
function biaQuestionsList($services):array {
// 	$arr[''] = 'Select One';
	if ($services) {
		foreach ($services as $service) {
			$arr[advisory_id_from_string($service['name'])] = $service['name'];
		}
	}
	return $arr;
}

function biaQ3Value($serviceID, $default) {
	$rpos = ['0-4 hours', '1-day', '3-days', '1-week'];
	if ($key = array_search($serviceID, $default)) {
		$key = str_replace('num_req_', '', $key);
		if ($default['cross_trained_'. $key]) return $rpos[ $default['cross_trained_'. $key] ]; 
	}
	return $rpos[0];
}
function biaQ4Value($serviceID, $default, $include_deskotp=false) {
	if ($key = array_search($serviceID, $default)) {
		if ($include_deskotp) {
			$data = ['upstream'=>'', 'desktop'=>''];
			$key = str_replace('function_', '', $key);
			if ($default['upstream_'. $key]) $data['upstream'] = $default['upstream_'. $key];
			if ($default['desktop_'. $key]) $data['desktop'] = $default['desktop_'. $key];
			return $data;
		} else {
			$key = str_replace('function_', '', $key);
			if ($default['upstream_'. $key]) return $default['upstream_'. $key];
		}
	}
	return 'N/A';
}
function biaQ4bValue($serviceID, $default) {
	if ($secondaryServices = array_search($serviceID, $default)) {
		$tmp = [];
		if ($secondaryServices .'_tier_1') $tmp['tier_1'] = $default[$secondaryServices .'_tier_1'];
		if ($secondaryServices .'_tier_2') $tmp['tier_2'] = $default[$secondaryServices .'_tier_2'];
		if ($secondaryServices .'_tier_3') $tmp['tier_3'] = $default[$secondaryServices .'_tier_3'];
		if ($secondaryServices .'_tier_4') $tmp['tier_4'] = $default[$secondaryServices .'_tier_4'];
		return $tmp;
	}
	return 'N/A';
}
function biaQ5Value($serviceID, $default) {
	if ($key = array_search($serviceID, $default)) {
		$key = str_replace('eds_', '', $key);
		if ($default['dependency_'. $key]) return $default['dependency_'. $key]; 
	}
	return 'N/A';
}
/***********************************************
 *
 * Helper Functions
* @used frontend - cloud reportcard
 *
 **********************************************/
function advisory_get_cloud_reportcard_data($biaIDs=null) {
	$data = [];
	$companyID = advisory_get_user_company_id();
	$company = get_term_meta($companyID, 'company_data', true);
	if (empty($biaIDs)) {
		if ($company['bia']) $biaIDs = $company['bia']; 
		else $biaIDs = getLatestBIAID($companyID);
	}
	// return $biaIDs; // 665
	if (is_array($biaIDs) && $biaIDs) {
		foreach ($biaIDs as $biaID) {
			$biaData = advisory_get_cloud_reportcard_data_for($biaID, $company);
			if ($biaData) {
				if (!$data) $data = $biaData;
				else {
					foreach ($biaData as $serviceID => $tiers) {
						$data[$serviceID]['04hours'] = array_merge($data[$serviceID]['04hours'], $biaData[$serviceID]['04hours']);
						$data[$serviceID]['24hours'] = array_merge($data[$serviceID]['24hours'], $biaData[$serviceID]['24hours']);
						$data[$serviceID]['3days'] = array_merge($data[$serviceID]['3days'], $biaData[$serviceID]['3days']);
						$data[$serviceID]['7days'] = array_merge($data[$serviceID]['7days'], $biaData[$serviceID]['7days']);
						$data[$serviceID]['24weeks'] = array_merge($data[$serviceID]['24weeks'], $biaData[$serviceID]['24weeks']);
					}
				}
			}
		}
	} else {
		$data = advisory_get_cloud_reportcard_data_for($biaIDs, $company);
	}
	return $data;
}

function advisory_get_cloud_reportcard_data_for($biaID, $company) {
	$reports = [];
	$tiers = ['04hours' => [], '24hours' => [], '3days' => [], '7days' => [], '24weeks' => []];
	if (!$company['externalDependency']) $externalDependencies = [];
	else $externalDependencies = $company['externalDependency'];
	// $externalDependencies = explode(PHP_EOL, $externalDependencies);
	$externalDependencies = getDependengiesInAlphabeticOrder($externalDependencies);
	if ($externalDependencies) {
		foreach ($externalDependencies as $key => $externalDependency) {
			// $tmp = explode(':', $externalDependency);
			$reports[$key] = $tiers;
			$reports[$key]['id'] = $key;
			$reports[$key]['name'] = $externalDependency;
		}
	}
	$data = advisory_get_scorecard_data($biaID);
	if ($data) {
		foreach ($data as $area) {
			if ($area['services']) {
				foreach ($area['services'] as $service) {
					if ($service) {
						if (@$service['cloud']) {
							$clouds = explode(',', $service['cloud']);
							if ($clouds) {
								foreach ($clouds as $cloud) {
									if ($cloud == 'N/A') continue;
									switch ($service['rto']) {
										case '0 - 4 Hours': $reports[$cloud]['04hours'][] = trim($area['name']) .'&&&'. trim($service['name']); break;
										case '24 Hours': 	$reports[$cloud]['24hours'][] = trim($area['name']) .'&&&'. trim($service['name']); break;
										case '3 Days': 		$reports[$cloud]['3days'][]   = trim($area['name']) .'&&&'. trim($service['name']); break;
										case '7 Days': 		$reports[$cloud]['7days'][]   = trim($area['name']) .'&&&'. trim($service['name']); break;
										case '2 - 4 Weeks': $reports[$cloud]['24weeks'][] = trim($area['name']) .'&&&'. trim($service['name']); break;
										default: break;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	return $reports;
}
/***********************************************
 *
 * Helper Functions
 * @used frontend - reportcard
 *
 **********************************************/
function advisory_cloudcard_html($upstreams='', $img='reportcard.jpg', $title='Cloud Service Catalogue') {
	$html = '';
	// $html .= '<form method="post" class="pull-right scorecardExportForm">';
	// $html .= '<button type="submit" name="export_cloud_service_catalogue" value="csv" class="btn btn-xs btn-primary pdf-btn mr-10">CSV</button>';
	// $html .= '<button type="submit" name="export_cloud_service_catalogue" value="excel" class="btn btn-xs btn-primary pdf-btn">Excel</button>';
	// $html .= '</form>';
	$html .= '<div class="text-center"> <img src="'.P3_TEMPLATE_URI.'/images/'. $img .'" class="img-responsive"> </div><br>';
	$html .= '<div class="table-responsive">';
            $html .= '<table class="table table-bordered table-reportCard">';
                $html .= '<thead>';
                    $html .= '<tr>';
                        $html .= '<th style="font-size: 20px;" class="t-heading-dark strong">'. $title .'</th>';
                        $html .= '<th style="width:100px;" class="t-heading-dark text-center strong">Tier 1 <br> (0-4 hours)</th>';
                        $html .= '<th style="width:100px;" class="t-heading-dark text-center strong">Tier 2 <br> (24-hours)</th>';
                        $html .= '<th style="width:100px;" class="t-heading-dark text-center strong">Tier 3 <br> (3-days)</th>';
                        $html .= '<th style="width:100px;" class="t-heading-dark text-center strong">Tier 4 <br> (7-days)</th>';
                        $html .= '<th style="width:104px;" class="t-heading-dark text-center strong">Tier 5 <br> (2-4 weeks)</th>';
                    $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                if ($upstreams) {
                    foreach ($upstreams as $key => $upstream) {
                    	if (isset($upstream['name'])) {
                    		if (!empty($upstream['04hours'])) {
	                    		natcasesort($upstream['04hours']);
	                    		$hours04 		= addslashes(implode('###', $upstream['04hours']));
	                    		$hours04Class 	= 'color-one reportCardItems';
                    		} else { $hours04 	= ''; $hours04Class = 'color-black'; }

                    		if (!empty($upstream['24hours'])) {
	                    		natcasesort($upstream['24hours']);
	                    		$hours24 		= addslashes(implode('###', $upstream['24hours']));
	                    		$hours24Class 	= 'color-two reportCardItems';
                    		} else { $hours24 	= ''; $hours24Class = 'color-black'; }

                    		if (!empty($upstream['3days'])) {
	                    		natcasesort($upstream['3days']);
	                    		$days3 			= addslashes(implode('###', $upstream['3days']));
	                    		$days3Class 	= 'color-three reportCardItems';
                    		} else { $days3 	= ''; $days3Class = 'color-black'; }

                    		if (!empty($upstream['7days'])) {
	                    		natcasesort($upstream['7days']);
	                    		$days7 			= addslashes(implode('###', $upstream['7days']));
	                    		$days7Class 	= 'color-four reportCardItems';
                    		} else { $days7 	= ''; $days7Class = 'color-black'; }

                    		if (!empty($upstream['24weeks'])) {
	                    		natcasesort($upstream['24weeks']);
	                    		$weeks24 		= addslashes(implode('###', $upstream['24weeks']));
	                    		$weeks24Class 	= 'color-five reportCardItems';
                    		} else { $weeks24 	= ''; $weeks24Class = 'color-black'; }

	                        $html .= '<tr>';
	                            $html .= '<th class="t-heading-sky">' . $upstream['name'] . '</th>';
	                            $html .= '<th class="text-center '. $hours04Class .'" title="'. addslashes($upstream['name']) .' (RTO 0-4 hours)" services="'. $hours04 .'"></th>';
	                            $html .= '<th class="text-center '. $hours24Class .'" title="'. addslashes($upstream['name']) .' (RTO 24 hours)" services="'. $hours24 .'"></th>';
	                            $html .= '<th class="text-center '. $days3Class .'" title="'. addslashes($upstream['name']) .' (RTO 3 days)" services="'. $days3 .'"></th>';
	                            $html .= '<th class="text-center '. $days7Class .'" title="'. addslashes($upstream['name']) .' (RTO 7 days)" services="'. $days7 .'"></th>';
	                            $html .= '<th class="text-center '. $weeks24Class .'" title="'. addslashes($upstream['name']) .' (RTO 24 weeks)" services="'. $weeks24 .'"></th>';
	                        $html .= '</tr>';
                    	}
                    }
                }
                $html .= '</tbody>
            </table>
        </div>';
    return $html;
}
function advisory_reportcard_html($upstreams='', $img='reportcard.jpg', $title='IT Service Catalogue') {
	$html = '';
	// $html .= '<form method="post" class="pull-right scorecardExportForm">';
	// $html .= '<a href="'.home_url('pdf/').'?pid=service_criticality_report_card" target="_blank" class="btn btn-xs btn-primary pdf-btn mr-10">PDF</a>';
	// $html .= '<button type="submit" name="export_service_criticality_reportcard" value="csv" class="btn btn-xs btn-primary pdf-btn mr-10">CSV</button>';
	// $html .= '<button type="submit" name="export_service_criticality_reportcard" value="excel" class="btn btn-xs btn-primary pdf-btn">Excel</button>';
	// $html .= '</form>';
	$html .= '<div class="text-center"> <img src="'.P3_TEMPLATE_URI.'/images/'. $img .'" class="img-responsive"> </div><br>';
	$html .= '<div class="table-responsive">';
            $html .= '<table class="table table-bordered table-reportCard">';
                $html .= '<thead>';
                    $html .= '<tr>';
                        $html .= '<th style="width: 40%;font-size: 20px;" class="t-heading-dark strong">'. $title .'</th>';
                        $html .= '<th style="width: 15%;" class="t-heading-dark text-center strong">Tier 1 <br> (0-4 hours)</th>';
                        $html .= '<th style="width: 15%;" class="t-heading-dark text-center strong">Tier 2 <br> (24-hours)</th>';
                        $html .= '<th style="width: 15%;" class="t-heading-dark text-center strong">Tier 3 <br> (3-days)</th>';
                        $html .= '<th style="width: 15%;" class="t-heading-dark text-center strong">Tier 4 <br> (7-days)</th>';
                    $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                if ($upstreams) {
                    foreach ($upstreams as $key => $upstream) {
                    	if (isset($upstream['name'])) {
                    		if (!empty($upstream['04hours'])) {
	                    		natcasesort($upstream['04hours']);
	                    		$hours04 		= addslashes(implode('###', $upstream['04hours']));
	                    		$hours04Class 	= 'color-one reportCardItems';
                    		} else { $hours04 	= ''; $hours04Class = 'color-black'; }

                    		if (!empty($upstream['24hours'])) {
	                    		natcasesort($upstream['24hours']);
	                    		$hours24 		= addslashes(implode('###', $upstream['24hours']));
	                    		$hours24Class 	= 'color-two reportCardItems';
                    		} else { $hours24 	= ''; $hours24Class = 'color-black'; }

                    		if (!empty($upstream['3days'])) {
	                    		natcasesort($upstream['3days']);
	                    		$days3 			= addslashes(implode('###', $upstream['3days']));
	                    		$days3Class 	= 'color-three reportCardItems';
                    		} else { $days3 	= ''; $days3Class = 'color-black'; }

                    		if (!empty($upstream['7days'])) {
	                    		natcasesort($upstream['7days']);
	                    		$days7 			= addslashes(implode('###', $upstream['7days']));
	                    		$days7Class 	= 'color-four reportCardItems';
                    		} else { $days7 	= ''; $days7Class = 'color-black'; }

                    		if (!empty($upstream['24weeks'])) {
	                    		natcasesort($upstream['24weeks']);
	                    		$weeks24 		= addslashes(implode('###', $upstream['24weeks']));
	                    		$weeks24Class 	= 'color-five reportCardItems';
                    		} else { $weeks24 	= ''; $weeks24Class = 'color-black'; }

	                        $html .= '<tr>';
	                            $html .= '<th class="t-heading-sky">' . $upstream['name'] . '</th>';
	                            $html .= '<th class="text-center '. $hours04Class .'" title="'. addslashes($upstream['name']) .' (RTO 0-4 hours)" services="'. $hours04 .'"></th>';
	                            $html .= '<th class="text-center '. $hours24Class .'" title="'. addslashes($upstream['name']) .' (RTO 24 hours)" services="'. $hours24 .'"></th>';
	                            $html .= '<th class="text-center '. $days3Class .'" title="'. addslashes($upstream['name']) .' (RTO 3 days)" services="'. $days3 .'"></th>';
	                            $html .= '<th class="text-center '. $days7Class .'" title="'. addslashes($upstream['name']) .' (RTO 7 days)" services="'. $days7 .'"></th>';
	                        $html .= '</tr>';
                    	}
                    }
                }
                $html .= '</tbody>
            </table>
        </div>';
    return $html;
}
function advisory_get_reportcard_data($biaIDs=null) {
	$data = [];
	$companyID = advisory_get_user_company_id();
	$company = get_term_meta($companyID, 'company_data', true);
	if (empty($biaIDs)) {
		if ($company['bia']) $biaIDs = $company['bia']; 
		else $biaIDs = getLatestBIAID($companyID);
	}
	if (is_array($biaIDs) && $biaIDs) {
		foreach ($biaIDs as $biaID) {
			$biaData = advisory_get_reportcard_data_for($biaID, $company);
			if ($biaData) {
				if (!$data) $data = $biaData;
				else {
					foreach ($biaData as $serviceID => $tiers) {
						if (!empty($biaData[$serviceID]['04hours'])) $data[$serviceID]['04hours'] = array_merge($data[$serviceID]['04hours'], $biaData[$serviceID]['04hours']);
						if (!empty($biaData[$serviceID]['24hours'])) $data[$serviceID]['24hours'] = array_merge($data[$serviceID]['24hours'], $biaData[$serviceID]['24hours']);
						if (!empty($biaData[$serviceID]['3days'])) 	 $data[$serviceID]['3days']   = array_merge($data[$serviceID]['3days'], $biaData[$serviceID]['3days']);
						if (!empty($biaData[$serviceID]['7days'])) 	 $data[$serviceID]['7days']   = array_merge($data[$serviceID]['7days'], $biaData[$serviceID]['7days']);
						if (!empty($biaData[$serviceID]['24weeks'])) $data[$serviceID]['24weeks'] = array_merge($data[$serviceID]['24weeks'], $biaData[$serviceID]['24weeks']);
					}
				}
			}
			$Q4BData = advisory_get_secondary_reportcard_data($biaID, $company);
			if ($Q4BData) {
				if (!$data) $data = $Q4BData;
				else {
					foreach ($Q4BData as $serviceID => $tiers) {
						if (!empty($Q4BData[$serviceID]['04hours'])) $data[$serviceID]['04hours'] = array_merge($data[$serviceID]['04hours'], $Q4BData[$serviceID]['04hours']);
						if (!empty($Q4BData[$serviceID]['24hours'])) $data[$serviceID]['24hours'] = array_merge($data[$serviceID]['24hours'], $Q4BData[$serviceID]['24hours']);
						if (!empty($Q4BData[$serviceID]['3days']))   $data[$serviceID]['3days']   = array_merge($data[$serviceID]['3days'], $Q4BData[$serviceID]['3days']);
						if (!empty($Q4BData[$serviceID]['7days']))   $data[$serviceID]['7days']   = array_merge($data[$serviceID]['7days'], $Q4BData[$serviceID]['7days']);
						if (!empty($Q4BData[$serviceID]['24weeks'])) $data[$serviceID]['24weeks'] = array_merge($data[$serviceID]['24weeks'], $Q4BData[$serviceID]['24weeks']);
					}
				}
			}
		}
	} else {
		$data = advisory_get_reportcard_data_for($biaIDs, $company);
		$Q4BData = advisory_get_secondary_reportcard_data($biaIDs, $company);
		// $data = $biaIDs;
		if ($Q4BData) {
			if (!$data) $data = $Q4BData;
			else {
				foreach ($Q4BData as $serviceID => $tiers) {
					if (!empty($Q4BData[$serviceID]['04hours'])) $data[$serviceID]['04hours'] = array_merge($data[$serviceID]['04hours'], $Q4BData[$serviceID]['04hours']);
					if (!empty($Q4BData[$serviceID]['24hours'])) $data[$serviceID]['24hours'] = array_merge($data[$serviceID]['24hours'], $Q4BData[$serviceID]['24hours']);
					if (!empty($Q4BData[$serviceID]['3days']))   $data[$serviceID]['3days']   = array_merge($data[$serviceID]['3days'], $Q4BData[$serviceID]['3days']);
					if (!empty($Q4BData[$serviceID]['7days']))   $data[$serviceID]['7days']   = array_merge($data[$serviceID]['7days'], $Q4BData[$serviceID]['7days']);
					if (!empty($Q4BData[$serviceID]['24weeks'])) $data[$serviceID]['24weeks'] = array_merge($data[$serviceID]['24weeks'], $Q4BData[$serviceID]['24weeks']);
				}
			}
		}
	}
	return $data;
}
function advisory_get_reportcard_data_for($biaID, $company) {
	$reports = [];
	$tiers = ['04hours' => [], '24hours' => [], '3days' => [], '7days' => [], '24weeks' => []];
	if (!$company['upstream']) $upstreams = [];
	else $upstreams = $company['upstream'];
	// $upstreams = explode(PHP_EOL, $upstreams);
	$upstreams = getDependengiesInAlphabeticOrder($upstreams);
	if ($upstreams) {
		foreach ($upstreams as $key => $upstream) {
			// $tmp = explode(':', $upstream);
			$reports[$key] = $tiers;
			$reports[$key]['id'] = $key;
			$reports[$key]['name'] = $upstream;
		}
	}
	$data = advisory_get_scorecard_data($biaID);
	foreach ($data as $area) {
		if ($area['services']) {
			foreach ($area['services'] as $service) {
				if ($service) {
					if (@$service['req']) {
						$reqs = explode(',', $service['req']);
						if ($reqs) {
							foreach ($reqs as $req) {
								if ($req == 'N/A') continue;
								switch ($service['rto']) {
									case '0 - 4 Hours': $reports[$req]['04hours'][] = trim($area['name']) .'&&&'. trim($service['name']); break;
									case '24 Hours': 	$reports[$req]['24hours'][] = trim($area['name']) .'&&&'. trim($service['name']); break;
									case '3 Days': 		$reports[$req]['3days'][] 	= trim($area['name']) .'&&&'. trim($service['name']); break;
									case '7 Days': 		$reports[$req]['7days'][] 	= trim($area['name']) .'&&&'. trim($service['name']); break;
									case '2 - 4 Weeks': $reports[$req]['24weeks'][] = trim($area['name']) .'&&&'. trim($service['name']); break;
									default: break;
								}
							}
						}
					}
				}
			}
		}
	}
	return $reports;
}
function advisory_get_secondary_reportcard_data($id=447, $company='') {
	if (!$company) {
		$companyID = advisory_get_user_company_id();
		$company = get_term_meta($companyID, 'company_data', true);
	}
	$upstreams = [];
	$reports = [];
	$tiers = ['04hours' => [], '24hours' => [], '3days' => [], '7days' => [], '24weeks' => []];
	if (!$company['upstream']) $upstream = [];
	else $upstream = $company['upstream'];
	$upstream = explode(PHP_EOL, $upstream);
	if ($upstream) {
		foreach ($upstream as $key => $upstream) {
			$tmp = explode(':', $upstream);
			$reports[$tmp[0]]['id'] = $tmp[0];
			$reports[$tmp[0]]['name'] = $tmp[1];
			$reports[$tmp[0]]  = $tiers;
		}
	}
	$data = [];
	$form_data = get_post_meta($id, 'form_opts', true);
	$transient_data = get_post_meta($id);
	$requestedPostType = get_post_type($id);
	$allRisks =  json_decode(ALL_RISKS);
	$count = 0;
	if (!empty($form_data['areas'])) {
		foreach ($form_data['areas'] as $area) {
			$count2 = 0;
			$data[$count]['name'] = $area['name'];
			if ($requestedPostType == 'bia') {
				$services = advisory_id_from_string($area['name']) . '_services';
				if (!empty($form_data[$services])) {
                    foreach ($form_data[$services] as $service) {
						$data[$count]['services'][$count2]['name'] = $service['name'];
						$service_data = $transient_data[$services .'_bia_'. advisory_id_from_string($service['name'])];
						$data[$count]['services'][$count2]['req'] = biaQ4bValue(advisory_id_from_string($service['name']), advisory_form_default_values($id, $services .'_int_func_b'));
						// $data[$count]['services'][$count2]['reqb'] = advisory_form_default_values($id, $services .'_int_func_b');
						$count2++;
					}
				}
				$count2++;
			}
			$count++;
		}
	}
	if (!empty($data)) {
		foreach ($data as $department) {
			if (!empty($department['services'])) {
				foreach ($department['services'] as $service) {
					if ( isset($service['req']['tier_1']) || isset($service['req']['tier_2']) || isset($service['req']['tier_3']) || isset($service['req']['tier_4'])) {
						foreach ($service['req'] as $tierKey => $tier) {
							if (!empty($tier)) {
								$tiers = explode(',', $tier);
								if ($tiers) {
									foreach ($tiers as $theService) {
										$reports[$theService][advisory_get_tier_name_for_q4b($tierKey)][] = $department['name'] .' (secondary)&&&'. $service['name'];
									}
								}
							}
						}
					}
				}
			}
		}
	}
	return $reports;
}
function advisory_get_tier_name_for_q4b($tierKey) {
	$name = '';
	if ($tierKey == 'tier_1') $name = '04hours';
	else if ($tierKey == 'tier_2') $name = '24hours';
	else if ($tierKey == 'tier_3') $name = '3days';
	else if ($tierKey == 'tier_4') $name = '7days';
	else $name = false;
	return $name;
}
function advisory_rto_to_tier($rto=null) {
	$tier = '';
	if ($rto) {
		$rto = str_replace(' ', '', strtolower($rto));
		// return $rto;
		switch ($rto) {
			case '04hours': 
			case '0-4hours': $tier = 'Tier 1'; break;
			case '24hours':  $tier = 'Tier 2'; break;
			case '3days': 	 $tier = 'Tier 3'; break;
			case '7days': 	 $tier = 'Tier 4'; break;
			case '24weeks': 
			case '2-4weeks': $tier = 'Tier 5'; break;
			default: break;
		}
	}
	return $tier;
}
function advisory_rto_to_tier_class($rto=null) {
	$tier = '';
	if ($rto) {
		$rto = str_replace(' ', '', strtolower($rto));
		// return $rto;
		switch ($rto) {
			case '04hours':
			case '0-4hours': $tier = 'bg-red'; break;
			case '24hours':  $tier = 'bg-orange'; break;
			case '3days': 	 $tier = 'bg-yellow'; break;
			case '7days': 	 $tier = 'bg-deepgreen'; break;
			case '24weeks':
			case '2-4weeks': $tier = 'bg-deepblue'; break;
			default: break;
		}
	}
	return $tier;
}
function advisory_rto_id_to_name($rto=null) {
	if ($rto) {
		switch (trim($rto)) {
			case '04hours': return '0-4 hours'; break;
			case '24hours': return '24 hours'; break;
			case '3days': return '3 days'; break;
			case '7days': return '7 days'; break;
			case '24weeks': return '2-4 weeks'; break;
			default: return ''; break;
		}
	}
	return false;
}
/***********************************************
 *
 * Helper Functions
 * @used frontend - scorecard
 *
 **********************************************/
function advisory_get_scorecard_data($id) {
	$data = array();
	$form_data = get_post_meta($id, 'form_opts', true);
	$transient_data = get_post_meta($id);
	$requestedPostType = get_post_type($id);
	$allRisks =  json_decode(ALL_RISKS);
	$count = 0;
	if (!empty($form_data['areas'])) {
		foreach ($form_data['areas'] as $area) {
			$count2 = 0;
			if ($requestedPostType == 'bia') {
				$data[$count]['name'] = $area['name'];
				$data[$count]['icon'] = $area['icon_menu'];
				$data[$count]['total'] = 0;
				$services = advisory_id_from_string($area['name']) . '_services';
				if (!empty($form_data[$services])) {
                    foreach ($form_data[$services] as $service) {
						$data[$count]['services'][$count2]['name'] = $service['name'];
						$service_data = $transient_data[$services . '_bia_' . advisory_id_from_string($service['name'])];
						parse_str($service_data[0], $service_data);
						$data[$count]['total'] += !empty($service_data['avg']) ? $service_data['avg'] : 0;
						$data[$count]['services'][$count2]['cr'] = $service_data['avg'];
						$data[$count]['services'][$count2]['rto'] = $service_data['rto'];
						$data[$count]['services'][$count2]['req'] = biaQ4Value(advisory_id_from_string($service['name']), advisory_form_default_values($id, $services .'_int_func'));
						$data[$count]['services'][$count2]['cloud'] = biaQ5Value(advisory_id_from_string($service['name']), advisory_form_default_values($id, $services .'_ext_func'));
						$count2++;
					}
					$data[$count]['opts']['gq_nosp'] = !empty($area['gq_nosp']) ? $area['gq_nosp'] : 3;
					$data[$count]['opts']['gq_nosp'] = !empty($area['gq_nosp']) ? $area['gq_nosp'] : 3;
					$data[$count]['opts']['gq_nobl'] = !empty($area['gq_nobl']) ? $area['gq_nobl'] : 3;
					$data[$count]['opts']['se_nodosp'] = !empty($area['se_nodosp']) ? $area['se_nodosp'] : 3;
					$data[$count]['opts']['se_nodosp2'] = !empty($area['se_nodosp2']) ? $area['se_nodosp2'] : 3;
					$data[$count]['opts']['se_q7_epct'] = !empty($area['se_q7_epct']) ? $area['se_q7_epct'] : 3;
					$data[$count]['opts']['se_q7_mnac'] = !empty($area['se_q7_mnac']) ? $area['se_q7_mnac'] : 3;
					$data[$count]['opts']['se_q7_dcl'] = !empty($area['se_q7_dcl']) ? $area['se_q7_dcl'] : 3;
				}
				$count2++;
			} elseif (in_array($requestedPostType, $allRisks)) {
				$data[$count]['name'] = $area['name'];
				$data[$count]['icon'] = $area['icon_menu'];
				$cat_id = advisory_id_from_string($area['name']);
				if ($threats = $form_data[$cat_id . '_threats']) {
                    foreach ($threats as $threat) {
                        $threat_id = $cat_id . '_' . advisory_id_from_string($threat['name']);
						if ($threat_data = $transient_data[$threat_id]) {
							parse_str($threat_data[0], $threat_data);
							$data[$count]['threats'][$count2]['name'] = $threat['name'];
							$data[$count]['threats'][$count2]['cat'] = $area['name'];
							$data[$count]['threats'][$count2]['avg'] = round($threat_data['avg']);
						}
						$count2++;
                    }
                }
			} else if ($requestedPostType == 'mta') {
				if (!empty($_REQUEST['area_id']) && $_REQUEST['area_id'] != advisory_id_from_string($area['name']) ) continue;
				$data[$count]['name'] = $area['name'];
				$data[$count]['icon'] = $area['icon_menu'];
				$sections = 'sections_' . advisory_id_from_string($area['name']);

				if (!empty($form_data[$sections])) {
					foreach ($form_data[$sections] as $section) {
						$data[$count]['sections'][$count2]['name'] = $section['name'];
						$data[$count]['sections'][$count2]['desc'] = $section['desc'];
						$data[$count]['sections'][$count2]['fields'] = [];
						$data[$count]['sections'][$count2]['tables'] = [];
						$tables = $sections . '_tables_' . advisory_id_from_string($section['name']);
						$subTables = $form_data[$tables];
						$subDefault = advisory_form_default_values($id, $tables);

						if ($subTables) {
							foreach ($subTables as $subTable) {
								$table_id = $tables . '_questions_' . advisory_id_from_string($subTable['name']);
								$subRating = !empty($subDefault[$table_id .'_avg']) ? $subDefault[$table_id .'_avg'] : 0;
								if ($subTable) {
									foreach ($subTable as $subTableNameSI => $subTableName) {
										if ($subTableName) {
											$data[$count]['sections'][$count2]['tables'][] = @['name'=>$subTableName, 'rate'=>$subRating[$subTableNameSI]];
										}
									}
								}
							}
						}

						$fields = !empty($section['fields']) ? $section['fields'] : false;
						if (!empty($transient_data[$tables])) {
							foreach ($transient_data[$tables] as $table) {
								parse_str($table, $table);
								if ($fields) {
									foreach ($fields as $f_key => $field) {
										$sum_arr = array();
										foreach ($table as $t_key => $value) {
											if (strpos($t_key, '_' . $field)) { $sum_arr[] = $value; }
										}
										$arr_count = count(array_filter($sum_arr));
										$avg = ($arr_count == 0 ? array_sum($sum_arr) : array_sum($sum_arr) / $arr_count);
										$data[$count]['sections'][$count2]['fields'][$field] = $avg;
									}
								}
								$data[$count]['sections'][$count2]['avg'] = $table['avg'];
							}
						}
						$count2++;
					}
				}
			} else {
				$data[$count]['name'] = $area['name'];
				$data[$count]['icon'] = $area['icon_menu'];
				$sections = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($form_data[$sections])) {
					foreach ($form_data[$sections] as $section) {
						$data[$count]['sections'][$count2]['name'] = $section['name'];
						$data[$count]['sections'][$count2]['desc'] = $section['desc'];
						$data[$count]['sections'][$count2]['fields'] = [];
						$data[$count]['sections'][$count2]['tables'] = [];
						$tables = $sections . '_tables_' . advisory_id_from_string($section['name']);
						if ($requestedPostType == 'mta' || $requestedPostType == 'ihc') {
							$subTables = $form_data[$tables];
							$subDefault = advisory_form_default_values($id, $tables);
							if ($subTables) {
								foreach ($subTables as $subTable) {
									$table_id = $tables . '_questions_' . advisory_id_from_string($subTable['name']);
									$subRating = !empty($subDefault[$table_id .'_avg']) ? $subDefault[$table_id .'_avg'] : 0;
									if ($subTable) {
										foreach ($subTable as $subTableNameSI => $subTableName) {
											if ($subTableName) {
												$data[$count]['sections'][$count2]['tables'][] = @['name'=>$subTableName, 'rate'=>$subRating[$subTableNameSI]];
											}
										}
									}
								}
							}
						}
						$fields = !empty($section['fields']) ? $section['fields'] : false;
						if (!empty($transient_data[$tables])) {
							foreach ($transient_data[$tables] as $table) {
								parse_str($table, $table);
								if ($fields) {
									foreach ($fields as $f_key => $field) {
										$sum_arr = array();
										foreach ($table as $t_key => $value) {
											if (strpos($t_key, '_' . $field)) {
												$sum_arr[] = $value;
											}
										}
										$arr_count = count(array_filter($sum_arr));
										$avg = ($arr_count == 0 ? array_sum($sum_arr) : array_sum($sum_arr) / $arr_count);
										$data[$count]['sections'][$count2]['fields'][$field] = $avg;
									}
								}
								$data[$count]['sections'][$count2]['avg'] = $table['avg'];
							}
						}
						$count2++;
					}
				}
			}
			$count++;
		}
	}
	return $data;
}
/***********************************************
 *
 * Helper Functions
 * @used frontend - forms
 *
 **********************************************/
// select box
function advisory_opt_select($name = '', $id = '', $class = '', $attr = '', $opts, $default = null): string{
	if (!is_array($opts)) {
		$criteria = array_merge(cs_get_option('criteria_itsm'), cs_get_option('criteria_cra'), cs_get_option('criteria_bia'), cs_get_option('criteria_risk'), cs_get_option('criteria_drm'));
		foreach ($criteria as $c) {
			if ($c['id'] == $opts) {
				$opts = [];
				$arr = explode(PHP_EOL, $c['options']);
				foreach ($arr as $ar) {
					$val = substr($ar, 0, strpos($ar, ":"));
					$title = substr($ar, strpos($ar, ":") + 1);
					$opts[$val] = $title;
				}
			}
		}
	}
	if (strrpos($name, 'mitigation')) {
		$class .= ' reverse';
	}
	if (strrpos($name, 'probability') || strpos($name, 'impact')) {
		$class .= ' increment';
	}
	$html = '<select name="' . $name . '" id="' . $id . '" class="' . $class . '" ' . $attr . '>';
	foreach ($opts as $val => $title) {
		$html .= '<option value="' . $val . '" ' . ($default == $val ? 'selected' : '') . '>' . $title . '</option>';
	}
	$html .= '</select>';
	return $html;
}
// coloring elements
function coloring_elements($val = null, $type = 'select', $reverse = false) {
	$color = '';
	if ($type == 'select') {
		switch ($val) {
			case '0':
			default:  $color = ($reverse ? 'color-five' : 'color-zero'	); 	break;
			case '1': $color = ($reverse ? 'color-four' : 'color-one'	); 	break;
			case '2': $color = ($reverse ? 'color-three': 'color-two'	); 	break;
			case '3': $color = ($reverse ? 'color-two' 	: 'color-three'	); 	break;
			case '4': $color = ($reverse ? 'color-one' 	: 'color-four'	); 	break;
			case '5': $color = ($reverse ? 'color-zero' : 'color-five'	); 	break;
		}
	} elseif ($type == 'mta-metrics') {
		if($val < 1.5) 						{ $color = 'metrics-red'; 	}
	    else if($val >= 1.5 && $val < 2.5) 	{ $color = 'metrics-orange';}
	    else if($val >= 2.5 && $val < 3.5) 	{ $color = 'metrics-yellow';}
	    else if($val >= 3.5 && $val < 4.5) 	{ $color = 'metrics-green'; }
	    else if($val >= 4.5) 				{ $color = 'metrics-blue'; 	}
	    else 								{ $color = 'metrics-red'; 	}
	} elseif ($type == 'mta-panel') {
		if($val < 3) 					{ $color = 'metrics-red'; 	}
	    else if($val >= 3 && $val < 5) 	{ $color = 'metrics-orange';}
	    else if($val >= 5 && $val < 7) 	{ $color = 'metrics-yellow';}
	    else if($val >= 7 && $val < 9) 	{ $color = 'metrics-green'; }
	    else if($val >= 9) 				{ $color = 'metrics-blue'; 	}
	    else 							{ $color = 'metrics-red'; 	}
	} elseif ($type == 'ihc-metrics') {
		if($val < 3) 					{ $color = 'metrics-red'; 	}
	    else if($val >= 3 && $val < 5) 	{ $color = 'metrics-orange';}
	    else if($val >= 5 && $val < 7) 	{ $color = 'metrics-yellow';}
	    else if($val >= 7 && $val < 9) 	{ $color = 'metrics-green'; }
	    else if($val >= 9 && $val < 11) { $color = 'metrics-blue'; 	}
	    else 							{ $color = 'metrics-red'; 	}
	} elseif ($type == 'metrics') {
		if ($val <= 2.5) {$color = 'metrics-red'; } 
		else if ($val > 2.5 && $val <= 3.5) {$color = 'metrics-yellow'; } 
		else if ($val > 3.5 && $val <= 5) 	{$color = 'metrics-green'; }
		else if ($val > 5) 					{$color = 'metrics-blue'; }
		else  								{$color = 'metrics-red'; }
	} elseif ($type == 'avg') {
		if ($val <= 2.5) {
			$color = 'color-one';
		} else if ($val > 2.5 && $val <= 3.5) {
			$color = 'color-three';
		} else if ($val > 3.5 && $val <= 5) {
			$color = 'color-five';
		}
	} elseif ($type == 'bia-score') {
		if ($val >= 0 && $val <= 20) {
			$color = 'color-four';
		} else if ($val >= 21 && $val <= 40) {
			$color = 'color-five';
		} else if ($val >= 41 && $val <= 60) {
			$color = 'color-three';
		} else if ($val >= 61 && $val <= 80) {
			$color = 'color-two';
		} else if ($val >= 80) {
			$color = 'color-one';
		}
	} elseif ($type == 'bia-score2') {
		if ($val >= 0 && $val <= 20) {
			$color = 'color-four';
		} else if ($val >= 21 && $val <= 40) {
			$color = 'color-five';
		} else if ($val >= 41 && $val <= 60) {
			$color = 'color-three';
		} else if ($val >= 61 && $val <= 80) {
			$color = 'color-two';
		} else if ($val >= 80) {
			$color = 'color-one';
		}
	} elseif ($type == 'risk-score') {
		if ($val >= 0 && $val <= 3) {
			$color = 'color-four';
		} else if ($val >= 4 && $val <= 8) {
			$color = 'color-three';
		} else if ($val >= 9 && $val <= 12) {
			$color = 'color-two';
		} else if ($val >= 13 && $val <= 16) {
			$color = 'color-one';
		}
	} elseif ($type == 'bcp-score') {
		$color = bcp_risk_class($val);
	} elseif ($type == 'prr-score') {
		if ($val >= 0 && $val <= 3) {
			$color = 'color-four';
		} else if ($val >= 4 && $val <= 8) {
			$color = 'color-three';
		} else if ($val >= 9 && $val <= 12) {
			$color = 'color-two';
		} else if ($val >= 13 && $val <= 16) {
			$color = 'color-one';
		}
	} elseif ($type == 'drmrr-score') {
		if ($val >= 0 && $val <= 1)  $color = 'color-five';
		else if ($val >1 && $val <= 2) $color = 'color-four';
		else if ($val >2 && $val <= 3) $color = 'color-three';
		else if ($val >3 && $val <= 4) $color = 'color-two';
		else if ($val > 4 && $val <= 5) $color = 'color-one';
	} elseif ($type == 'dmmr-score') {
		if ($val >= 0 && $val <= 3) {
			$color = 'color-four';
		} else if ($val >= 4 && $val <= 8) {
			$color = 'color-three';
		} else if ($val >= 9 && $val <= 12) {
			$color = 'color-two';
		} else if ($val >= 13 && $val <= 16) {
			$color = 'color-one';
		}
	}
	return $color;
}
function bia_level($score) {
	if ($score >= 0 && $score <= 15) {
		return 'Non-essential';
		// '30 Days'
	} else if ($score >= 16 && $score <= 45) {
		return 'Normal';
		// '7 Days'
	} else if ($score >= 46 && $score <= 49) {
		return 'Important';
		// '72 Hours'
	} else if ($score >= 50 && $score <= 69) {
		return 'Urgent';
		// '24 Hours'
	} else if ($score >= 70) {
		return 'Critical';
		// '1 Hour'
	}
}
function bia_level2($score) {
	if ($score >= 0 && $score <= 20) {
		return 'Non-essential';
		// '30 Days'
	} else if ($score >= 21 && $score <= 40) {
		return 'Normal';
		// '7 Days'
	} else if ($score >= 41 && $score <= 60) {
		return 'Important';
		// '72 Hours'
	} else if ($score >= 61 && $score <= 80) {
		return 'Urgent';
		// '24 Hours'
	} else if ($score >= 81) {
		return 'Critical';
		// '1 Hour'
	}
}
function advisory_tmp_threat_sort($a, $b) {
    return $a['avg'] - $b['avg'];
}
// default values
function advisory_form_default_values($post_id, $meta_key): array{
	$data = get_post_meta($post_id, $meta_key, true);
	parse_str($data, $data);
	return $data;
}
function advisory_company_default_values($company_id, $meta_key): array{
	$data = get_term_meta($company_id, $meta_key, true);
	parse_str($data, $data);
	return $data;
}
// form area exists
function advisory_area_exists($form_id, $searching_for) {
	$opts = get_post_meta(get_the_ID(), 'form_opts', true);
	if (is_array($opts['areas'])) {
		foreach ($opts['areas'] as $area) {
			if (advisory_id_from_string($area['name']) == $searching_for) {
				return $area;
			}
		}
	}
	return false;
}
/*************************************************************************
 *
 * Dashboard Avg
 *
 ************************************************************************/
function advisory_metrics_in_progress($company_id, $post_type): bool{
	$posts = advisory_get_active_forms($company_id, $post_type);
	if (!empty($posts)) {
		return true;
	}
	return false;
}
function advisory_graphic_link($post_type, $area) {
	global $user_switching;
	$company_id = advisory_get_user_company_id();
	if (advisory_metrics_in_progress($company_id, $post_type)) {
		$form_id = advisory_get_active_forms($company_id, $post_type);
		if ($user_switching->get_old_user()) {
			return esc_url(get_the_permalink($form_id[0]) . "?area={$area}");
		}
		return esc_url(get_the_permalink($form_id[0]) . "?view=true&area={$area}");
	} else {
		$id = new WP_Query([
			'post_type' => $post_type,
			'post_status' => 'archived',
			'posts_per_page' => 1,
			'meta_query' => [['key' => 'assigned_company', 'value' => $company_id, ]],
			'fields' => 'ids',
		]);
		if ($id->found_posts > 0) {
			$form_id = $id->posts;
			return esc_url(get_the_permalink($form_id[0]) . "?view=true&area={$area}");
		} else {
			return esc_url('#');
		}
	}
}
function advisory_dashboard_avg_mta($company_id, $post_type) {
	$data = array();
	$id = new WP_Query([
		'post_type' => $post_type,
		'post_status' => 'archived',
		'posts_per_page' => 1,
		'meta_query' => [['key' => 'assigned_company', 'value' => $company_id, 'compare' => '=', ]],
		'fields' => 'ids',
	]);
	if ($id->found_posts > 0) {
		$id = $id->posts;
		$opts = get_post_meta($id[0], 'form_opts', true);
		$transient_data = get_post_meta($id[0]);
		$key = 0;
		if (!empty($opts['areas'])) {
			foreach ($opts['areas'] as $area) {
				$data[$key]['name'] = $area['name'];
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($opts[$section_id])) {
					$table_groups_values = [];
					foreach ($opts[$section_id] as $section) {
						$table_id = $section_id . '_tables_' . advisory_id_from_string($section['name']);
						if (!empty($opts[$section_id]) && !empty($transient_data[$table_id])) {
							parse_str($transient_data[$table_id][0], $table_groups_values);
							foreach ($opts[$table_id] as $tableId => $table) {
								$question_id = $table_id . '_questions_' . advisory_id_from_string($table['name']);
								$itemAvg = $table_groups_values[$question_id.'_avg'];
								if ($itemAvg != 'g') $data[$key]['values'][] = $itemAvg;
							}
						}
					}
				}
				$key++;
			}
		}
	}
	return $data;
}
function advisory_dashboard_avg($company_id, $post_type) {
	$data = array();
	$id = new WP_Query([
		'post_type' => $post_type,
		'post_status' => 'archived',
		'posts_per_page' => 1,
		'meta_query' => [['key' => 'assigned_company', 'value' => $company_id, 'compare' => '=', ]],
		'fields' => 'ids',
	]);
	if ($id->found_posts > 0) {
		$id = $id->posts;
		$form_data = get_post_meta($id[0], 'form_opts', true);
		$transient_data = get_post_meta($id[0]);
		$key = 0;
		if (!empty($form_data['areas'])) {
			foreach ($form_data['areas'] as $area) {
				$data[$key]['name'] = $area['name'];
				$sections = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($form_data[$sections])) {
					foreach ($form_data[$sections] as $section) {
						$table_id = $sections . '_tables_' . advisory_id_from_string($section['name']);
						if (!empty($transient_data[$table_id])) {
							foreach ($transient_data[$table_id] as $table_groups) {
								parse_str($table_groups, $table_groups);
								if ($post_type == 'mta' || $table_groups['avg'] != 'g') $data[$key]['values'][] = $table_groups['avg'];
								else if ($table_groups['avg'] != 0) $data[$key]['values'][] = $table_groups['avg'];
							}
						}
					}
				}
				$key++;
			}
		}
	}
	return $data;
}
function advisory_transient_avg_mta($company_id, $post_type): array{
	$data = [];
	$form = advisory_get_active_forms($company_id, $post_type);
	if (!empty($form[0])) {
		$transient_data = get_post_meta($form[0]);
		$opts = get_post_meta($form[0], 'form_opts', true);
		$key = 0;
		if (!empty($opts['areas'])) {
			foreach ($opts['areas'] as $area) {
				$data[$key]['name'] = $area['name'];
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($opts[$section_id])) {
					$table_groups_values = [];
					foreach ($opts[$section_id] as $section) {
						$table_id = $section_id . '_tables_' . advisory_id_from_string($section['name']);
						if (!empty($opts[$section_id]) && !empty($transient_data[$table_id])) {
							parse_str($transient_data[$table_id][0], $table_groups_values);
							foreach ($opts[$table_id] as $tableId => $table) {
								$question_id = $table_id . '_questions_' . advisory_id_from_string($table['name']);
								$itemAvg = $table_groups_values[$question_id.'_avg'];
								if ($itemAvg != 'g') $data[$key]['values'][] = $itemAvg;
							}
						}
					}
				}
				$key++;
			}
		}
	}
	return $data;
}
function advisory_transient_avg($company_id, $post_type): array{
	$data = array();
	$form = advisory_get_active_forms($company_id, $post_type);
	$id = $form[0];
	$transient_data = get_post_meta($id);
	$form_data = get_post_meta($id, 'form_opts', true);
	$key = 0;
	if (!empty($form_data['areas'])) {
		foreach ($form_data['areas'] as $area) {
			$data[$key]['name'] = $area['name'];
			$sections = 'sections_' . advisory_id_from_string($area['name']);
			if (!empty($form_data[$sections])) {
				foreach ($form_data[$sections] as $section) {
					$table_id = $sections . '_tables_' . advisory_id_from_string($section['name']);
					if (!empty($transient_data[$table_id])) {
						foreach ($transient_data[$table_id] as $table_groups) {
							parse_str($table_groups, $table_groups);
							if ($post_type == 'mta' || $table_groups['avg'] != 'g') $data[$key]['values'][] = $table_groups['avg'];
							else if ($table_groups['avg'] != 0) $data[$key]['values'][] = $table_groups['avg'];
						}
					}
				}
			}
			$key++;
		}
	}
	return $data;
}
// has reset survey permission on dashboard
function advisory_has_dashboard_reset_permission() {
	$form_id = advisory_get_active_forms(advisory_get_user_company_id(), array('ihc'));
	if (empty($form_id)) {
		return false;
	}
	$form_id = $form_id[0];
	if (!advisory_has_survey_delete_permission($form_id)) {
		return false;
	}
	return true;
}
/***********************************************
 *
 * Helper Functions
 * @used frontend - forms calculation
 *
 **********************************************/
// section avg
function advisory_section_avg($post_id, $section) {
	$default = number_format(0, 1);
	$data = get_post_meta($post_id, $section, true);
	parse_str($data, $data);
	$data = (!empty($data['avg']) ? number_format($data['avg'], 1) : $default);
	return $data;
}
/***********************************************
 *
 * Helper Functions
 * @used frontend - forms view/edit
 *
 **********************************************/
function advisory_template_areas($template_id): array{
	$form_meta = get_post_meta($template_id, 'form_opts', true);
	$areas = [];
	if (!empty($form_meta['areas'])) {
		foreach ($form_meta['areas'] as $area) {
			$areas[] = $area['name'];
		}
	}
	return $areas;
}
function advisory_get_form_name($form_id): string{
	$opts = get_post_meta($form_id, 'form_opts', true);
	return @($opts['display_name'] ? $opts['display_name'] : get_the_title($form_id));
}
function advisory_is_survey_locked($post_id, $user_id) {
	$meta = get_user_meta($user_id, 'edit_permissions', true);
	$meta = explode(',', $meta);
	if (in_array($post_id, $meta)) {
		return false;
	}
	return true;
}
/***********************************************
 *
 * Hooks
 * @used backend
 *
 **********************************************/
function advisory_save_form_hook($post_id) {
	if (in_array(get_post_type($post_id), array_merge(json_decode(ALL_POST_TYPES), ['csa', 'sfiats']))) {
		$permission = get_post_meta($post_id, 'permission', true);
		if (!empty($permission) && is_array($permission)) {
			if (array_key_exists('users', $permission)) {
				update_post_meta($post_id, 'assigned_company', $permission['users']);
			}
		}
	}
}
add_action('save_post', 'advisory_save_form_hook', 13, 2);
/***********************************************
 *
 * Customize WP
 * @used backend
 *
 **********************************************/
remove_role('subscriber');
remove_role('contributor');
remove_role('author');
remove_role('editor');
remove_role('advisor');
remove_role('viewer');
add_role('advisor', __('Advisor'), array(
	'read' => true,
	'upload_files' => true,
	'publish_posts' => true,
	'edit_posts' => true,
	'edit_published_posts' => true,
	'edit_others_posts' => true,
	'delete_posts' => true,
	'delete_published_posts' => true,
	'delete_others_posts' => true,
	'manage_categories' => true,
	'manage_options' => true,
	'create_users' => true,
	'edit_users' => true,
	'delete_users' => true,
	'list_users' => true,
));
add_role('viewer', __('Viewer'), array(
	'read' => true,
));
function advisory_wp_admin_init() {
	if (!defined('DOING_AJAX')) {
		if (is_admin() && current_user_can('viewer')) {
			exit(wp_redirect(home_url()));
		}
	}
}
add_action('admin_init', 'advisory_wp_admin_init', 100);
function advisory_remove_admin_role_dropdown($all_roles) {
	global $pagenow;
	if (current_user_can('advisor')) {
		if ($pagenow == 'user-edit.php' || $pagenow == 'user-new.php') {
			unset($all_roles['administrator']);
		}
	}
	return $all_roles;
}
add_action('editable_roles', 'advisory_remove_admin_role_dropdown');
function advisory_login_redirect($redirect_to, $request, $user) {
	if (isset($user->roles) && is_array($user->roles)) {
		if (in_array('administrator', $user->roles)) {
			return $redirect_to;
		} else {
			return home_url();
		}
	} else {
		return home_url();
	}
}
add_filter('login_redirect', 'advisory_login_redirect', 10, 3);
function advisory_hide_admin_listing($u_query) {
	global $pagenow;
	if ($pagenow == 'users.php') {
		$current_user = wp_get_current_user();
		if ($current_user->roles[0] != 'administrator') {
			global $wpdb;
			$u_query->query_where = str_replace(
				'WHERE 1=1',
				"WHERE 1=1 AND {$wpdb->users}.ID IN (
				SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
					WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
					AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')",
				$u_query->query_where
			);
		}
	}
}
add_action('pre_user_query', 'advisory_hide_admin_listing');
function advisory_remove_admin_node($wp_admin_bar) {
	$wp_admin_bar->remove_node('wp-logo');
	$wp_admin_bar->remove_node('new-post');
	$wp_admin_bar->remove_node('new-link');
	$wp_admin_bar->remove_node('view');
}
add_action('admin_bar_menu', 'advisory_remove_admin_node', 999);
function advisory_remove_admin_menu() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'advisory_remove_admin_menu');
function advisory_remove_help($old_help, $screen_id, $screen) {
	$screen->remove_help_tabs();
	return $old_help;
}
add_filter('contextual_help', 'advisory_remove_help', 999, 3);
add_filter('additional_capabilities_display', function () {
	return false;
});
function advisory_admin_page_remove() {
	if (current_user_can('advisor')) {
		remove_menu_page('index.php');
		remove_menu_page('edit.php');
		remove_menu_page('edit-comments.php');
		remove_menu_page('tools.php');
		remove_menu_page('options-general.php');
	}
}
add_action('admin_menu', 'advisory_admin_page_remove');
function advisory_remove_dashboard_widgets() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
}
add_action('wp_dashboard_setup', 'advisory_remove_dashboard_widgets');
function advisory_login_logo() {
	echo '<style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(' . get_stylesheet_directory_uri() . '/images/login-logo-dark.png);
			height:175px;
			width:150px;
			background-size: 150px 175px;
			background-repeat: no-repeat;
        	padding-bottom: 30px;
        }
    </style>';
}
add_action('login_enqueue_scripts', 'advisory_login_logo');
function advisory_login_logo_url() {
	return home_url();
}
add_filter('login_headerurl', 'advisory_login_logo_url');
function advisory_login_logo_url_title() {
	return 'Advisory Services';
}
add_filter('login_headertitle', 'advisory_login_logo_url_title');
function advisory_remove_row_actions($actions) {
	if (in_array(get_post_type(), json_decode(ALL_POST_TYPES))) {
		unset($actions['view']);
	}
	return $actions;
}
add_filter('post_row_actions', 'advisory_remove_row_actions', 10, 1);
function advisory_disable_sample_permalink($return) {
	global $post;
	global $post_type;
	if (in_array($post_type, json_decode(ALL_POST_TYPES))) {
		return '';
	}
}
add_filter('get_sample_permalink_html', 'advisory_disable_sample_permalink');
function advisory_hide_post_preview() {
	global $post_type;
	if (in_array($post_type, json_decode(ALL_POST_TYPES))) {
		echo '<style type="text/css">#post-preview, #view-post-btn, .updated a{display: none;}</style>';
	}
}
add_action('admin_head-post-new.php', 'advisory_hide_post_preview');
add_action('admin_head-post.php', 'advisory_hide_post_preview');
function advisory_new_dashboard() {
	if (current_user_can('advisor')) {
		add_menu_page(__('Dashboard', 'advisory'), 'Dashboard', 'manage_options', 'dashboard', '', 'dashicons-dashboard', 2);
	}
}
add_action('admin_menu', 'advisory_new_dashboard');
function advisory_remove_profile_fields($hook) {
	echo '<style type="text/css">form#your-profile > h2:nth-of-type(1), form#your-profile > table:nth-of-type(1), form#your-profile > h2:nth-of-type(4), form#your-profile > table:nth-of-type(4) { display:none!important;visibility:hidden!important; }</style>';
}
add_action('admin_print_styles-profile.php', 'advisory_remove_profile_fields');
add_action('admin_print_styles-user-edit.php', 'advisory_remove_profile_fields');
// WP PageNavi Bootstrap
function advisory_pagenavi_filter($html) {
	$out = '';
	$out = str_replace("<div", "", $html);
	$out = str_replace("class='wp-pagenavi'>", "", $out);
	$out = str_replace("<a", "<li><a", $out);
	$out = str_replace("</a>", "</a></li>", $out);
	$out = str_replace("<span", "<li><span", $out);
	$out = str_replace("</span>", "</span></li>", $out);
	$out = str_replace("</div>", "", $out);
	$out = str_replace("<li><span class='current'", "<li class='active'><span", $out);
	return '<nav><ul class="pagination">' . $out . '</ul></nav>';
}
add_filter('wp_pagenavi', 'advisory_pagenavi_filter', 10, 2);
// Display archived label
function advisory_display_archived_label($statuses) {
	global $post;
	if (get_query_var('post_status') != 'archived') {
		if ($post->post_status == 'archived') {
			return array('Archived');
		}
	}
	return $statuses;
}
add_filter('display_post_states', 'advisory_display_archived_label');
// Add archived label to quick edit
function advisory_archived_status_to_quick_edit() {
	echo "<script>
		jQuery(document).ready( function() {
			jQuery('select[name=\"_status\"]').append('<option value=\"archived\">Archived</option>');
		});
	</script>";
}
add_action('admin_footer-edit.php', 'advisory_archived_status_to_quick_edit');
include 'includes/user_profile_field.php';
// add upload capabilities
if ( current_user_can('viewer') && !current_user_can('upload_files') ){
	$user = wp_get_current_user();
	if (get_user_meta($user->ID, 'mediaPage', true )) {
		$user->add_cap( 'upload_files');
	} else {
		$user->remove_cap( 'upload_files');
	}
}
function get_terms_for_risk_registers(){
	$terms = [];
	$terms = get_terms('risk_cat', ['hide_empty' => 0]);
	if ($terms) {
		foreach ($terms as $key => $term) {
			$ids = new WP_Query([
		        'post_type' => 'risk',
		        'post_status' => 'archived',
		        'posts_per_page' => 1,
		        'meta_query' => [[
		            'key' => 'assigned_company',
		            'value' => advisory_get_user_company_id(),
		        ]],
		        'tax_query' => [[
		            'taxonomy' => 'risk_cat',
		            'field' => 'slug',
		            'terms' => [$term->slug],
		            'operator' => 'IN',
		        ]],
		        'fields' => 'ids',
		    ]);
		    if ($ids->posts[0]) $term->post_ID = $ids->posts[0];
		    else unset($terms[$key]);
		}
	}
	return $terms;
    $ids = new WP_Query([
        'post_type' => 'risk',
        'post_status' => 'archived',
        'posts_per_page' => -1,
        'meta_query' => [[
            'key' => 'assigned_company',
            'value' => advisory_get_user_company_id(),
        ]],
        // 'fields' => 'ids',
    ]);
    if ($ids->posts) {
        foreach ($ids->posts as $post) {
            $term_list = wp_get_post_terms($post->ID, 'risk_cat', array("fields" => 'all'));
            if ($term_list) {
                foreach ($term_list as $term) {
                    if (!array_key_exists($term->term_id, $terms)) {
                    	// add new array item
                		$term->post_ID = $post->ID;
                    	$term->icon = getRiskIconFor($term);
                    	$terms[$term->term_id] = $term;
                    } else {
                    	// add post at corresponding array
                    	if (!empty($terms[$term->term_id]->post_ID)) {
                    		$terms[$term->term_id]->post_ID .= ','. $post->ID;
                    	}
                    }
                }
            }
        }
    }
    return $terms;
}
function getRiskIconFor($term) {
	$termMeta = get_term_meta($term->term_id, 'risk_type_data');
	return @$termMeta[0]['risk_type_icon'];
}
function isUserHasDashboardB($user_company_id=null) {
	if (!$user_company_id) $user_company_id = advisory_get_user_company_id();
	$data = get_term_meta($user_company_id, 'company_data', true);
    return @$data['userDashboard'] == 'Dashboard B' ? true : false;
}
function companyLogo($user_company_id=null) {
	if (!$user_company_id) $user_company_id = advisory_get_user_company_id();
	$data = get_term_meta($user_company_id, 'company_data', true);
	$logo = $data['logo'] ? $data['logo'] : P3_TEMPLATE_URI.'/images/logo.png';
	return $logo;
}
function companyUpstream($user_company_id=null) {
	if (!$user_company_id) $user_company_id = advisory_get_user_company_id();
	$data = get_term_meta($user_company_id, 'company_data', true);
	if ($data['upstream']) {
		$upstreams =  explode(PHP_EOL, $data['upstream']);
		return $upstreams;
	}
	return false;
}
function companyUpstreamArray($user_company_id=null) : array {
	$arr = [];
	$upstreams =  companyUpstream($user_company_id);
	if ($upstreams) {
		$arr[0] = 'Select one';
		foreach ($upstreams as $upstream) {
			$tmp = explode(':', $upstream);
			if ($tmp) {
				$arr[$tmp[0]] = $tmp[1];
			}
		}
	}
	return $arr;
}
function getArchivedMenuFor($postType='risk', $title='DR Risk Register', $pageTitle='Risk Register', $icon=null, $showEmpty=false) {
	if (advisory_metrics_in_progress(advisory_get_user_company_id(), [$postType])) {
        $rr_form_id = advisory_get_active_forms(advisory_get_user_company_id(), [$postType]);
    } else {
        $id = new WP_Query([
            'post_type' => $postType,
            'post_status' => 'archived',
            'posts_per_page' => 1,
            'meta_query' => [[
                'key' => 'assigned_company',
                'value' => advisory_get_user_company_id(),
            ]],
            'fields' => 'ids',
        ]);
        if ($id->found_posts > 0) {
            $rr_form_id = $id->posts;
        }
    }
    $image = $icon ? '<img src="'.P3_TEMPLATE_URI.'/images/'. $icon .'" alt="'. @$title .' Icon">' : '';
    if (!empty($rr_form_id[0])) {
        $rr_data = advisory_get_formatted_rr_data($rr_form_id[0]);
        echo '<li id="riskMenu_'. $rr_form_id[0] .'""><a href="javascript:;">'. $image .' '. $title .'</a>
            <ul class="treeview-menu">';
            foreach ($rr_data as $area) {
                echo '<li><a href="' . get_permalink(get_page_by_title($pageTitle)) . '?cat=' . advisory_id_from_string($area['cat']) . '"><span>' . $area['cat'] . '</span></a></li>';
            }
            echo '</ul>
        </li>';
    } else {
    	if ($showEmpty) {
	        echo '<li><a href="javascript:;">'. $image .' '. $title .'</a>
	            <ul class="treeview-menu">
	                <li><a>N/A</a></li>
	            </ul>
	        </li>';
    	}
    }
}
function getArchivedSingleMenuFor($postType='itcl', $title='ITCL', $icon=null, $showEmpty=false) {
	if (advisory_metrics_in_progress(advisory_get_user_company_id(), [$postType])) {
        $rr_form_id = advisory_get_active_forms(advisory_get_user_company_id(), [$postType]);
    } else {
        $id = new WP_Query([
            'post_type' => $postType,
            'post_status' => 'archived',
            'posts_per_page' => 1,
            'meta_query' => [[
                'key' => 'assigned_company',
                'value' => advisory_get_user_company_id(),
            ]],
            'fields' => 'ids',
        ]);
        if ($id->found_posts > 0) {
            $rr_form_id = $id->posts;
        }
    }
    $image = $icon ? '<img src="'.P3_TEMPLATE_URI.'/images/'. $icon .'" alt="'. $title .' Icon">' : '';
    if (!empty($rr_form_id[0])) {
    	echo '<li><a href="'. esc_url( site_url($postType.'/'. $rr_form_id[0])) .'/">'. $image .'<span>'. $title .'</span></a></li>';
    }
}
function hexToRGB($color = "#ff9900", $opacity=1) {;
	list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
	return "rgba($r, $g, $b, .{$opacity})";
}
function DMMAvgColor($avg) {
	if ($avg == 5) return 'color-one';
	elseif ($avg == 4) return 'color-two';
	elseif ($avg == 3) return 'color-three';
	elseif ($avg == 2) return 'color-four';
	elseif ($avg == 1) return 'color-five';
	else return '';
}
function DMMAvgColor2($avg) {
	if ($avg == 5) return 'color-one';
	elseif ($avg == 4) return 'color-two';
	elseif ($avg == 3) return 'color-three';
	elseif ($avg == 2) return 'color-five';
	elseif ($avg == 1) return 'color-four';
	else return '';
}
function DMMAvgTxt($avg) {
	if ($avg == 5) return 'Initial';
	elseif ($avg == 4) return 'Managed';
	elseif ($avg == 3) return 'Defined';
	elseif ($avg == 2) return 'Measured';
	elseif ($avg == 1) return 'Optimizing';
	else return '';
}
function help($array=[], $key='', $echo=true) {
	if ($key) $data = '<br><pre style="margin-left: 250px; margin-top: 50px;">'. $key .' -> '. print_r($array, true) .'</pre>';
	else $data = '<br><pre style="margin-left: 250px; margin-top: 50px;">'. print_r($array, true) .'</pre>';
	if ($echo) echo $data;
	else return $data;
}
function pageTitleAndBreadcrumb($title='Example', $icon='', $breadcrumb='') {
	$html = '';
	$img = '';
	if ($icon) $img = '<img class="dashboardIcon" src="'.P3_TEMPLATE_URI.'/images/'. $icon .'" alt="'. $title .' icon">';
	if (!$breadcrumb) $breadcrumb = $title;
	$html .= '<div class="page-title">';
        $html .= '<div>';
            $html .= '<h1>'. $img .' '. $title .'</h1>';
        $html .= '</div>';
        $html .= '<div>';
            $html .= '<ul class="breadcrumb">';
                $html .= '<li><i class="fa fa-home fa-lg"></i></li>';
                $html .= '<li><a href="#">'. $breadcrumb .'</a></li>';
            $html .= '</ul>';
        $html .= '</div>';
    $html .= '</div>';
    echo $html;
}
add_action('wp_ajax_upstream_options', 'upstreamOptions');
function upstreamOptions() {
	check_ajax_referer('advisory_nonce', 'security');
	$upstream = $_POST['upstream'];
	$input = $_POST['input'];
	$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	if (!$data['upstream']) $upstreams = [];
	else $upstreams = $data['upstream'];
	$data = advisory_checkbox_array($upstreams, $upstream, $input);
	echo $data;
	wp_die();
}
add_action('wp_ajax_upstream_options_Q4b', 'upstreamOptionsQ4b');
function upstreamOptionsQ4b() {
	check_ajax_referer('advisory_nonce', 'security');
	$upstream = $_POST['upstream'];
	$allSelected = $_POST['selected'];
	$input = $_POST['input'];
	$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	$upstreams = $data['upstream'] ?? [];
	$data = advisory_checkbox_array($upstreams, $upstream, $input, $allSelected);
	// echo 'allSelected : '. $allSelected;
	echo $data;
	wp_die();
}
add_action('wp_ajax_external_dependency_options', 'externalDependencyOptions');
function externalDependencyOptions() {
	check_ajax_referer('advisory_nonce', 'security');
	$dependency = $_POST['dependency'];
	$input = $_POST['input'];
	$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	if (!$data['externalDependency']) $dependencies = [];
	else $dependencies = $data['externalDependency'];
	$data = advisory_checkbox_array($dependencies, $dependency, $input);
	echo $data;
	wp_die();
}
add_action('wp_ajax_mobile_apps_options', 'ajax_mobile_apps_options');
function ajax_mobile_apps_options() {
	check_ajax_referer('advisory_nonce', 'security');
	$dependency = $_POST['dependency'];
	$input = $_POST['input'];
	$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	if (!$data['mobile_apps']) $dependencies = [];
	else $dependencies = $data['mobile_apps'];
	$data = advisory_checkbox_array($dependencies, $dependency, $input);
	echo $data;
	wp_die();
}
add_action('wp_ajax_desktop_application_options', 'desktopApplicationOptions');
function desktopApplicationOptions() {
	check_ajax_referer('advisory_nonce', 'security');
	$dependency = $_POST['dependency'];
	$input = !empty($_POST['input']) ? $_POST['input'] : false;
	$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	if (!$data['desktopDependency']) $dependencies = [];
	else $dependencies = $data['desktopDependency'];
	// return '<br><pre>'. print_r($dependencies, true) .'</pre>';
	$data = advisory_checkbox_array($dependencies, $dependency, $input);
	echo $data;
	wp_die();
}
add_action('wp_ajax_assets_impacted_options', 'bcpassetsImpactedOptions');
function bcpassetsImpactedOptions() {
	check_ajax_referer('advisory_nonce', 'security');
	$ai = $_POST['ai'];
	$input = $_POST['input'];
	$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	if (!$data['upstreamDependencies']) $aiOpts = [];
	else $aiOpts = $data['upstreamDependencies'];
	$data = advisory_checkbox_array($aiOpts, $ai, $input);
	echo $data;
	wp_die();
}
add_action('wp_ajax_vulnerability_options', 'bcpvulnerabilityOptions');
function bcpvulnerabilityOptions() {
	check_ajax_referer('advisory_nonce', 'security');
	$ai = $_POST['ai'];
	$input = $_POST['input'];
	$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	if (!$data['bcpvulnerabilities']) $aiOpts = [];
	else $aiOpts = $data['bcpvulnerabilities'];
	$data = advisory_checkbox_array($aiOpts, $ai, $input);
	echo $data;
	wp_die();
}
function getDependengiesInAlphabeticOrder($dependencies) {
	$data = [];
	if (!empty($dependencies)) {
		$dependencies = explode(PHP_EOL, $dependencies);
		if ($dependencies) {
			foreach ($dependencies as $dependency) {
				$tmp = explode(':', $dependency);
				if (!empty($tmp[0]) && !empty($tmp[1])) $data[$tmp[0]] = $tmp[1];
			}
		}
	}
	// asort($data);
	natcasesort($data);
	return $data;
}
// generate array from check box multiline data
function advisory_checkbox_array($strOptions, $defaults=null, $input=true, $q4b=false){
	$html = '';
	$disables = [];
	$defaults = explode(',', $defaults);
	// $options = explode(PHP_EOL, $strOptions);
	$options = getDependengiesInAlphabeticOrder($strOptions);
	if ($q4b) {
		$disables = explode(',', $q4b);
		if ($defaults) $disables = array_diff($disables, $defaults);
	}
	if ($options) {
		list($part1, $part2) = array_chunk($options, ceil(count($options) / 2), true);
		if (!empty($part1)) {
			$html .= '<div class="firstHalf" style="width:50%;float:left;">';
			$html .= getCheckboxHTML($part1, $defaults, $disables, $input);
			$html .= '</div>';
		}
		if (!empty($part2)) {
			$html .= '<div class="secondHalf" style="width:50%;float:left;">';
			$html .= getCheckboxHTML($part2, $defaults, $disables, $input);
			$html .= '</div>';
		}
	}
	return $html;
}
function getCheckboxHTML($options, $defaults, $disables, $input) {
	// return json_encode($options);
	$html = '';
	$end = $start + $loop;
	if (!empty($options)) {
		foreach ($options as $optionSI => $option) {
			$checked = in_array($optionSI, $defaults) ? ' checked' : '';
			$active = in_array($optionSI, $defaults) ? ' activeLabel' : '';
			$isDisabled = in_array($optionSI, $disables) ? 'disabled' : '';
	        
	        $html .= '<label class="'. $active .' '. $isDisabled .'">';
	        	if ($input) $html .= '<input type="checkbox" class="upstream" name="upstream[]" value="'. $optionSI .'"'. $checked .' '. $isDisabled .'> ';
	        	// else $html .= $optionSI .'. ';
	        	$html .= $option;
	        $html .= '</label>';
		}
	}
	return $html;
}
function getCheckboxHTML2($options, $defaults, $disables, $input, $loop, $start=0) {
	$html = '';
	$end = $start + $loop;
	for ($i=$start; $i < $end; $i++) { 
        $tmp = explode(':', $options[$i]);
		$checked = in_array($tmp[0], $defaults) ? ' checked' : '';
		$active = in_array($tmp[0], $defaults) ? ' activeLabel' : '';
		$isDisabled = in_array($tmp[0], $disables) ? 'disabled' : '';
	        $html .= '<label class="'. $active .' '. $isDisabled .'">';
	        	if ($input) $html .= '<input type="checkbox" class="upstream" name="upstream[]" value="'. $tmp[0] .'"'. $checked .' '. $isDisabled .'> ';
	        	else $html .= $tmp[0] .'. ';
	        	$html .= $tmp[1];
	        $html .= '</label>';
	}
	return $html;
}
// REPORT CARD
function getLatestBIAID($companyID) {
	$latestBiaID = 0;
	$query = array(
		'post_type' => 'bia',
		'post_status' => 'archived',
		'posts_per_page' => 1,
		'fields' => 'ids',
		'meta_query' => array(
			array(
				'key' => 'assigned_company',
				'value' => $companyID,
				'compare' => '=',
			),
		),
	);
	$query = new WP_Query($query);
	if ($query->found_posts > 0) {$latestBiaID = $query->posts[0]; }
	return $latestBiaID;
}
function getAllBIAIDFor($companyID) {
	$bias = [];
	$query = array(
		'post_type' => 'bia',
		'post_status' => ['publish','archived'],
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'assigned_company',
				'value' => $companyID,
				'compare' => '=',
			),
		),
	);
	$query = new WP_Query($query);
	if ($query->found_posts > 0) {
		foreach ($query->posts as $bia) {
			$bias[$bia->ID] = $bia->post_title;
		}
	}
	return $bias;
}
function upstreamDependencies($biaID) {
	$opts = get_post_meta($biaID, 'form_opts', true);
	if ($opts['areas']) {
		foreach ($opts['areas'] as $department) {
			$department_id = advisory_id_from_string($department['name']) . '_services';
			if ($services = $opts[$department_id]) {
				return $questions = biaQuestionsList($services);
			}
		}
	}
}
function mtaColorAVG($value=0) {
    $cls = '';
    if ($value == 'g') $cls = 'color-gap';
    else {
	    $value = intval($value*10);
	    switch($value){
	        case 1:     { $cls = 'color-1'; 	break; }
	        case 2:     { $cls = 'color-2'; 	break; }
	        case 3:     { $cls = 'color-3'; 	break; }
	        case 4:     { $cls = 'color-4'; 	break; }
	        case 5:     { $cls = 'color-5'; 	break; }
	        case 6:     { $cls = 'color-6'; 	break; }
	        case 7:     { $cls = 'color-7'; 	break; }
	        case 8:     { $cls = 'color-8'; 	break; }
	        case 9:     { $cls = 'color-9'; 	break; }
	        case 10:     { $cls = 'color-10'; 	break; }
	        case 11:     { $cls = 'color-11'; 	break; }
	        case 12:     { $cls = 'color-12'; 	break; }
	        case 13:     { $cls = 'color-13'; 	break; }
	        case 14:     { $cls = 'color-14'; 	break; }
	        case 15:     { $cls = 'color-15'; 	break; }
	        case 16:     { $cls = 'color-16'; 	break; }
	        case 17:     { $cls = 'color-17'; 	break; }
	        case 18:     { $cls = 'color-18'; 	break; }
	        case 19:     { $cls = 'color-19'; 	break; }
	        case 20:     { $cls = 'color-20'; 	break; }
	        case 21:     { $cls = 'color-21'; 	break; }
	        case 22:     { $cls = 'color-22'; 	break; }
	        case 23:     { $cls = 'color-23'; 	break; }
	        case 24:     { $cls = 'color-24'; 	break; }
	        case 25:     { $cls = 'color-25'; 	break; }
	        case 26:     { $cls = 'color-26'; 	break; }
	        case 27:     { $cls = 'color-27'; 	break; }
	        case 28:     { $cls = 'color-28'; 	break; }
	        case 29:     { $cls = 'color-29'; 	break; }
	        case 30:     { $cls = 'color-30';	break; }
	        case 31:     { $cls = 'color-31';	break; }
	        case 32:     { $cls = 'color-32';	break; }
	        case 33:     { $cls = 'color-33';	break; }
	        case 34:     { $cls = 'color-34';	break; }
	        case 35:     { $cls = 'color-35';	break; }
	        case 36:     { $cls = 'color-36';	break; }
	        case 37:     { $cls = 'color-37';	break; }
	        case 38:     { $cls = 'color-38';	break; }
	        case 39:     { $cls = 'color-39';	break; }
	        case 40:     { $cls = 'color-40'; 	break; }
	        case 41:     { $cls = 'color-41'; 	break; }
	        case 42:     { $cls = 'color-42'; 	break; }
	        case 43:     { $cls = 'color-43'; 	break; }
	        case 44:     { $cls = 'color-44'; 	break; }
	        case 45:     { $cls = 'color-45'; 	break; }
	        case 46:     { $cls = 'color-46'; 	break; }
	        case 47:     { $cls = 'color-47'; 	break; }
	        case 48:     { $cls = 'color-48'; 	break; }
	        case 49:     { $cls = 'color-49'; 	break; }
	        case 50:     { $cls = 'color-50'; 	break; }
	        case 51:     { $cls = 'color-51'; 	break; }
	        case 52:     { $cls = 'color-52'; 	break; }
	        case 53:     { $cls = 'color-53'; 	break; }
	        case 54:     { $cls = 'color-54'; 	break; }
	        case 55:     { $cls = 'color-55'; 	break; }
	        case 56:     { $cls = 'color-56'; 	break; }
	        case 57:     { $cls = 'color-57'; 	break; }
	        case 58:     { $cls = 'color-58'; 	break; }
	        case 59:     { $cls = 'color-59'; 	break; }
	        case 60:     { $cls = 'color-60'; 	break; }
	        case 61:     { $cls = 'color-61'; 	break; }
	        case 62:     { $cls = 'color-62'; 	break; }
	        case 63:     { $cls = 'color-63'; 	break; }
	        case 64:     { $cls = 'color-64'; 	break; }
	        case 65:     { $cls = 'color-65'; 	break; }
	        case 66:     { $cls = 'color-66'; 	break; }
	        case 67:     { $cls = 'color-67'; 	break; }
	        case 68:     { $cls = 'color-68'; 	break; }
	        case 69:     { $cls = 'color-69'; 	break; }
	        case 70:     { $cls = 'color-70'; 	break; }
	        case 71:     { $cls = 'color-71'; 	break; }
	        case 72:     { $cls = 'color-72'; 	break; }
	        case 73:     { $cls = 'color-73'; 	break; }
	        case 74:     { $cls = 'color-74'; 	break; }
	        case 75:     { $cls = 'color-75'; 	break; }
	        case 76:     { $cls = 'color-76'; 	break; }
	        case 77:     { $cls = 'color-77'; 	break; }
	        case 78:     { $cls = 'color-78'; 	break; }
	        case 79:     { $cls = 'color-79'; 	break; }
	        case 80:     { $cls = 'color-80'; 	break; }
	        case 81:     { $cls = 'color-81'; 	break; }
	        case 82:     { $cls = 'color-82'; 	break; }
	        case 83:     { $cls = 'color-83'; 	break; }
	        case 84:     { $cls = 'color-84'; 	break; }
	        case 85:     { $cls = 'color-85'; 	break; }
	        case 86:     { $cls = 'color-86'; 	break; }
	        case 87:     { $cls = 'color-87'; 	break; }
	        case 88:     { $cls = 'color-88'; 	break; }
	        case 89:     { $cls = 'color-89'; 	break; }
	        case 90:     { $cls = 'color-90'; 	break; }
	        case 91:     { $cls = 'color-91'; 	break; }
	        case 92:     { $cls = 'color-92'; 	break; }
	        case 93:     { $cls = 'color-93'; 	break; }
	        case 94:     { $cls = 'color-94'; 	break; }
	        case 95:     { $cls = 'color-95'; 	break; }
	        case 96:     { $cls = 'color-96'; 	break; }
	        case 97:     { $cls = 'color-97'; 	break; }
	        case 98:     { $cls = 'color-98'; 	break; }
	        case 99:     { $cls = 'color-99'; 	break; }
	        case 100:    { $cls = 'color-100'; 	break; }
	        default:    { $cls = 'color-0'; 	break; }
	    }
	}
    return $cls;
}
function ihcColorAVG($value=0) {
    $cls = '';
    if ($value == 'g') $cls = 'color-gap';
    else {
	    $value = intval($value*10);
	    switch($value){
	        case 1:     { $cls = 'color-1'; 	break; }
	        case 2:     { $cls = 'color-2'; 	break; }
	        case 3:     { $cls = 'color-3'; 	break; }
	        case 4:     { $cls = 'color-4'; 	break; }
	        case 5:     { $cls = 'color-5'; 	break; }
	        case 6:     { $cls = 'color-6'; 	break; }
	        case 7:     { $cls = 'color-7'; 	break; }
	        case 8:     { $cls = 'color-8'; 	break; }
	        case 9:     { $cls = 'color-9'; 	break; }
	        case 10:     { $cls = 'color-10'; 	break; }
	        case 11:     { $cls = 'color-11'; 	break; }
	        case 12:     { $cls = 'color-12'; 	break; }
	        case 13:     { $cls = 'color-13'; 	break; }
	        case 14:     { $cls = 'color-14'; 	break; }
	        case 15:     { $cls = 'color-15'; 	break; }
	        case 16:     { $cls = 'color-16'; 	break; }
	        case 17:     { $cls = 'color-17'; 	break; }
	        case 18:     { $cls = 'color-18'; 	break; }
	        case 19:     { $cls = 'color-19'; 	break; }
	        case 20:     { $cls = 'color-20'; 	break; }
	        case 21:     { $cls = 'color-21'; 	break; }
	        case 22:     { $cls = 'color-22'; 	break; }
	        case 23:     { $cls = 'color-23'; 	break; }
	        case 24:     { $cls = 'color-24'; 	break; }
	        case 25:     { $cls = 'color-25'; 	break; }
	        case 26:     { $cls = 'color-26'; 	break; }
	        case 27:     { $cls = 'color-27'; 	break; }
	        case 28:     { $cls = 'color-28'; 	break; }
	        case 29:     { $cls = 'color-29'; 	break; }
	        case 30:     { $cls = 'color-30';	break; }
	        case 31:     { $cls = 'color-31';	break; }
	        case 32:     { $cls = 'color-32';	break; }
	        case 33:     { $cls = 'color-33';	break; }
	        case 34:     { $cls = 'color-34';	break; }
	        case 35:     { $cls = 'color-35';	break; }
	        case 36:     { $cls = 'color-36';	break; }
	        case 37:     { $cls = 'color-37';	break; }
	        case 38:     { $cls = 'color-38';	break; }
	        case 39:     { $cls = 'color-39';	break; }
	        case 40:     { $cls = 'color-40'; 	break; }
	        case 41:     { $cls = 'color-41'; 	break; }
	        case 42:     { $cls = 'color-42'; 	break; }
	        case 43:     { $cls = 'color-43'; 	break; }
	        case 44:     { $cls = 'color-44'; 	break; }
	        case 45:     { $cls = 'color-45'; 	break; }
	        case 46:     { $cls = 'color-46'; 	break; }
	        case 47:     { $cls = 'color-47'; 	break; }
	        case 48:     { $cls = 'color-48'; 	break; }
	        case 49:     { $cls = 'color-49'; 	break; }
	        case 50:     { $cls = 'color-50'; 	break; }
	        case 51:     { $cls = 'color-51'; 	break; }
	        case 52:     { $cls = 'color-52'; 	break; }
	        case 53:     { $cls = 'color-53'; 	break; }
	        case 54:     { $cls = 'color-54'; 	break; }
	        case 55:     { $cls = 'color-55'; 	break; }
	        case 56:     { $cls = 'color-56'; 	break; }
	        case 57:     { $cls = 'color-57'; 	break; }
	        case 58:     { $cls = 'color-58'; 	break; }
	        case 59:     { $cls = 'color-59'; 	break; }
	        case 60:     { $cls = 'color-60'; 	break; }
	        case 61:     { $cls = 'color-61'; 	break; }
	        case 62:     { $cls = 'color-62'; 	break; }
	        case 63:     { $cls = 'color-63'; 	break; }
	        case 64:     { $cls = 'color-64'; 	break; }
	        case 65:     { $cls = 'color-65'; 	break; }
	        case 66:     { $cls = 'color-66'; 	break; }
	        case 67:     { $cls = 'color-67'; 	break; }
	        case 68:     { $cls = 'color-68'; 	break; }
	        case 69:     { $cls = 'color-69'; 	break; }
	        case 70:     { $cls = 'color-70'; 	break; }
	        case 71:     { $cls = 'color-71'; 	break; }
	        case 72:     { $cls = 'color-72'; 	break; }
	        case 73:     { $cls = 'color-73'; 	break; }
	        case 74:     { $cls = 'color-74'; 	break; }
	        case 75:     { $cls = 'color-75'; 	break; }
	        case 76:     { $cls = 'color-76'; 	break; }
	        case 77:     { $cls = 'color-77'; 	break; }
	        case 78:     { $cls = 'color-78'; 	break; }
	        case 79:     { $cls = 'color-79'; 	break; }
	        case 80:     { $cls = 'color-80'; 	break; }
	        case 81:     { $cls = 'color-81'; 	break; }
	        case 82:     { $cls = 'color-82'; 	break; }
	        case 83:     { $cls = 'color-83'; 	break; }
	        case 84:     { $cls = 'color-84'; 	break; }
	        case 85:     { $cls = 'color-85'; 	break; }
	        case 86:     { $cls = 'color-86'; 	break; }
	        case 87:     { $cls = 'color-87'; 	break; }
	        case 88:     { $cls = 'color-88'; 	break; }
	        case 89:     { $cls = 'color-89'; 	break; }
	        case 90:     { $cls = 'color-90'; 	break; }
	        case 91:     { $cls = 'color-91'; 	break; }
	        case 92:     { $cls = 'color-92'; 	break; }
	        case 93:     { $cls = 'color-93'; 	break; }
	        case 94:     { $cls = 'color-94'; 	break; }
	        case 95:     { $cls = 'color-95'; 	break; }
	        case 96:     { $cls = 'color-96'; 	break; }
	        case 97:     { $cls = 'color-97'; 	break; }
	        case 98:     { $cls = 'color-98'; 	break; }
	        case 99:     { $cls = 'color-99'; 	break; }
	        case 100:    { $cls = 'color-100'; 	break; }
	        default:    { $cls = 'color-0'; 	break; }
	    }
	}
    return $cls;
}
function IHCRAvgStatus($avg) {
	$data = [];
    if($avg < 3) 		{ $data['cls'] = 'color-one'; 	$data['txt'] = 'Initial'; 	}
    else if($avg < 5) 	{ $data['cls'] = 'color-two'; 	$data['txt'] = 'Managed'; 	}
    else if($avg < 7) 	{ $data['cls'] = 'color-three'; $data['txt'] = 'Defined'; 	}
    else if($avg < 9) 	{ $data['cls'] = 'color-four'; 	$data['txt'] = 'Measured'; 	}
    else if($avg < 11)  { $data['cls'] = 'color-five'; 	$data['txt'] = 'Optimizing';}
    else 				{ $data['cls'] = 'color-one'; 	$data['txt'] = 'Initial'; 	}
    return $data;
}
function MTAAvgStatus($avg) {
	$data = [];
    if($avg == 'g')		{ $data['cls'] = 'bg-gap'; 	$data['txt'] = 'GAP'; 	}
    else if($avg < 3) 	{ $data['cls'] = 'bg-red'; 	$data['txt'] = 'Initial'; 	}
    else if($avg < 5) 	{ $data['cls'] = 'bg-orange'; 	$data['txt'] = 'Managed'; 	}
    else if($avg < 7) 	{ $data['cls'] = 'bg-yellow'; $data['txt'] = 'Defined'; 	}
    else if($avg < 9) 	{ $data['cls'] = 'bg-green'; 	$data['txt'] = 'Measured'; 	}
    else if($avg < 11)  { $data['cls'] = 'bg-blue'; 	$data['txt'] = 'Optimizing';}
    else 				{ $data['cls'] = 'bg-red'; 	$data['txt'] = 'Initial'; 	}
    // $data['txt'] .= ' => '.number_format($avg,1);
    return $data;
}
function updateCompanyMeta($companyID, $metaName, $value) {
	if (get_term_meta($companyID, $metaName, true)) return update_term_meta($companyID, $metaName, $value);
	else return add_term_meta($companyID, $metaName, $value);
	return false;
}
function advisoryGetFormatedDefaulValuesForPDF($postID, $department, $question) {
	$items = [];
	if ($question == 'Q3') {
		$itemCountPerRow = 4;
		$keyName = 'step_';
		$valueName = 'activity_';
	} else {
		$itemCountPerRow = 2;
		$keyName = 'sp_';
		$valueName = 'desc_';
	}
	$default = advisory_form_default_values($postID, $department);
	// return $department;
	if ($default) {
		if (isset($default['reset'])) unset($default['reset']);
		$loop = round(count($default) / $itemCountPerRow);
		for ($i=0; $i < $loop; $i++) { 
			$key = !empty($default[$keyName. $i]) ? $default[$keyName. $i] : '';
			$value = !empty($default[$valueName. $i]) ? $default[$valueName. $i] : '';
			$items[$key] = $value;
		}
	}
	return $items;
}
function advisoryGetFormatedDefaulValuesForPDFQ9($postID, $department, $services) {
	$items = $depts		= [];
	$itemCountPerRow 	= 4;
	if (!empty($services)) {
		foreach ($services as $service) {
			$depts = $depts + [advisory_id_from_string($service['name']) => $service['name']];
		}
	}
	$default 			= advisory_form_default_values($postID, $department);
	if ($default) {
		if (isset($default['reset'])) unset($default['reset']);
		$loop = round(count($default) / $itemCountPerRow);
		for ($i=0; $i < $loop; $i++) { 
			$sop = !empty($default['sop_'. $i]) ? $default['sop_'. $i] : '';
			if ($sop) {
				$items[$i]['service'] 	= $depts[$sop];
				$items[$i]['ud'] 		= !empty($default['ud_'. $i]) ? $default['ud_'. $i] : '';
				$items[$i]['dd']		= !empty($default['dd_'. $i]) ? $default['dd_'. $i] : '';
				$items[$i]['comments'] 	= !empty($default['comments_'. $i]) ? $default['comments_'. $i] : '';
			}
		}
	}
	return $items;
}
function advisoryGetFormatedDefaulValuesForPDFEvalQ3($postID, $department, $services) {
	$items 				= [];
	$itemCountPerRow 	= 3;
	$service 			= 'num_req_';
	$rpo 				= 'cross_trained_';
	$process 			= 'skillset_';
	$default 			= advisory_form_default_values($postID, $department);
	// return $services;
	if ($default) {
		$rpos = ['0-4 hours', '1-day', '3-days', '1-week'];

		if (isset($default['reset'])) unset($default['reset']);
		$loop = round(count($default) / $itemCountPerRow);
		if ($loop) {
			for ($i=0; $i < $loop; $i++) {
				$serviceID 		= !empty($default[$service. $i]) ? $default[$service. $i] : '';
				$serviceName 	= !empty($services[$i]['name']) ? $services[$i]['name'] : '';
				$rpoVal 		= isset($default[$rpo. $i]) ? $rpos[$default[$rpo. $i]] : '';
				$rpoColor 		= isset($default[$rpo. $i]) ? advisoryRPOColor($default[$rpo. $i]) : '';
				$processVal 	= !empty($default[$process. $i]) ? $default[$process. $i] : '';
				$items[] 		= ['SID'=> $serviceID, 'SName'=> $serviceName, 'rpo'=>$rpoVal, 'rpoColor' => $rpoColor, 'process'=> $processVal];
			}
		}
	}
	return $items;
}
function advisoryGetFormatedDefaulValuesForPDFUpstream($postID) {
	$data = [];
	$companyData = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
	$upstreams = !empty($companyData['upstream']) ? explode(PHP_EOL, $companyData['upstream']) : false;
	$cloudes = !empty($companyData['externalDependency']) ? explode(PHP_EOL, $companyData['externalDependency']) : false;
	$desktops = !empty($companyData['desktopDependency']) ? explode(PHP_EOL, $companyData['desktopDependency']) : false;

	$opts = get_post_meta($postID, 'form_opts', true);
	if ($opts['areas']) {
        foreach ($opts['areas'] as $department) {
            if (@$_GET['dept'] != advisory_id_from_string($department['name'])) continue;
            $department_id = advisory_id_from_string($department['name']) . '_services';
            $defaultQ4 = advisory_form_default_values($postID, $department_id . '_int_func');
            $defaultQ5 = advisory_form_default_values($postID, $department_id . '_ext_func');
            if ($services = $opts[$department_id]) {
            	$serviceSI = 0;
            	foreach ($services as $i => $service) {
            		$serviceName = $service['name'] ?? '';
                    $serviceID = advisory_id_from_string($serviceName);
            		$data[$serviceSI]['title'] = $serviceName;
            		$defaultUpstream = !empty($defaultQ4['upstream_' . $serviceSI]) ? $defaultQ4['upstream_' . $serviceSI] : false;
            		$defaultDesktop = !empty($defaultQ4['desktop_' . $serviceSI]) ? $defaultQ4['desktop_' . $serviceSI] : false;
            		$defaultCloue = !empty($defaultQ5['dependency_' . $serviceSI]) ? $defaultQ5['dependency_' . $serviceSI] : false;
            		$defaultOthers = !empty($defaultQ5['when_' . $serviceSI]) ? $defaultQ5['when_' . $serviceSI] : false;
            		$data[$serviceSI]['upstream'] = implode(', ', advisoryGetFormatedValuesForPDF($upstreams, $defaultUpstream));
            		$data[$serviceSI]['desktop'] = implode(', ', advisoryGetFormatedValuesForPDF($desktops, $defaultDesktop));
            		$data[$serviceSI]['cloud'] = implode(', ', advisoryGetFormatedValuesForPDF($cloudes, $defaultCloue));
            		$data[$serviceSI]['other'] = $defaultOthers;
            		$serviceSI++;
            	}
            }
        }
    }
	return $data;
}
function advisoryGetFormatedValuesForPDF($items, $selected) {
	$data = [];
	if (!empty($items) && !empty($selected)) {
		$selected = explode(',', $selected);
		foreach ($items as $itemSI => $item) {
			$tmp =  explode(':', $item);
			if ( !empty($tmp[0]) && !empty($tmp[1]) && in_array($tmp[0], $selected)) {
				$data[] = $tmp[1];
			}
		}
	}
	return $data;
}
function advisoryRPOColor($value) {
	$name = '';
	switch ($value) {
		case '0': $name = 'color-one'; 		break;
		case '1': $name = 'color-two'; 		break;
		case '2': $name = 'color-three'; 	break;
		case '3': $name = 'color-four'; 	break;
		default: $name  = 'color-one'; 		break;
	}
	return $name;
}
function advisoryGetFormatedDefaulValuesForPDFQ2($postID, $departmentID, $defaultID) {
	$items 				= [];
	$itemCountPerRow 	= 2;
	$keyName 			= 'skillset_';
	$valueName 			= 'comments_';
	$default 			= advisory_form_default_values($postID, $defaultID);
	// return $default;
	if ($default) {
		// $opts = get_post_meta($postID, 'form_opts', true);
		// if (isset($default['reset'])) unset($default['reset']);
		// $loop = round(count($default) / $itemCountPerRow);
		// if ($opts[$departmentID]) {
		// 	foreach ($opts[$departmentID] as $i => $depts) {
		// 		$value = !empty($default[$valueName. $i]) ? $default[$valueName. $i] : '';
		// 		$key = !empty($default[$keyName. $i]) ? $default[$keyName. $i] : '';
		// 		// if ($key) $items[$key] = $departmentID;
		// 		$items[$key] = $value;
		// 	}
		// }
		if (isset($default['reset'])) unset($default['reset']);
		$loop = round(count($default) / $itemCountPerRow);
		if ($loop) {
			for ($i=0; $i < $loop; $i++) {
				$value 			= !empty($default[$valueName. $i]) ? $default[$valueName. $i] : '';
				$key 			= !empty($default[$keyName. $i]) ? $default[$keyName. $i] : '';
				$items[$key] 	= $value;
			}
		}
	}
	return $items;
}
function advisoryGetFormatedDefaulValuesForPDFQ6($postID, $department, $dept) {
	$items 		= ['A'=>[], 'B'=>[], 'C'=>[]];
	$default 	= advisory_form_default_values($postID, $department);
	// return $default;
	if ($default) {
		$loop = $dept['se_nodosp'] ? $dept['se_nodosp'] : 3;
		for ($i=0; $i < $loop; $i++) {
			$items['A'][$i]['vr'] 		= !empty($default['inf_req_adp_'. $i]) ? $default['inf_req_adp_'. $i] : '';
			$items['A'][$i]['desc'] 	= !empty($default['inf_req_desc_'. $i]) ? $default['inf_req_desc_'. $i] : '';
			$items['A'][$i]['sl'] 		= !empty($default['inf_req_req_'. $i]) ? $default['inf_req_req_'. $i] : '';
			$items['A'][$i]['format'] 	= !empty($default['inf_req_format_'. $i]) ? $default['inf_req_format_'. $i] : '';
			$items['A'][$i]['updated']	= !empty($default['inf_req_updated_'. $i]) ? $default['inf_req_updated_'. $i] : '';
		}
		$loop2 = $dept['se_nodosp2'] ? $dept['se_nodosp2'] : 3;
		for ($i=0; $i < $loop2; $i++) {
			$items['B'][$i]['type'] 	= !empty($default['tech_req_adp_'. $i]) ? $default['tech_req_adp_'. $i] : '';
			$items['B'][$i]['normal'] 	= !empty($default['tech_req_normal_'. $i]) ? $default['tech_req_normal_'. $i] : '';
			$items['B'][$i]['msl'] 		= !empty($default['tech_req_req_'. $i]) ? $default['tech_req_req_'. $i] : '';
			$items['B'][$i]['comments'] = !empty($default['tech_req_comments_'. $i]) ? $default['tech_req_comments_'. $i] : '';
			$items['C'][$i]['ca'] 		= !empty($default['tech_req_ca_'. $i]) ? $default['tech_req_ca_'. $i] : '';
			$items['C'][$i]['func'] 	= !empty($default['tech_req_func_'. $i]) ? $default['tech_req_func_'. $i] : '';
			$items['C'][$i]['sc'] 		= !empty($default['tech_req_sc_'. $i]) ? $default['tech_req_sc_'. $i] : '';
		}
	}
	return $items;
}
function advisoryGetFormatedDefaulValuesForPDFQ7($postID, $department, $dept, $services) {
	$items 		= ['epct'=>[], 'mnac'=>[], 'dcl'=>[]];
	$default 	= advisory_form_default_values($postID, $department);
	$epctLoop 	= count($services);
	$mnacLoop 	= !empty($dept['se_q7_mnac']) ? $dept['se_q7_mnac'] : 3;
	$iclLoop 	= !empty($dept['se_q7_dcl'])  ? $dept['se_q7_dcl']  : 3;
	$eclLoop 	= !empty($dept['se_q7_ecl'])  ? $dept['se_q7_ecl']  : 3;
	// return $default;
	if ($default) {
		for ($i=0; $i < $epctLoop; $i++) { 
			$items['epct'][$i]['sop'] 		= !empty($default['sop_'. $i]) ? $default['sop_'. $i] : '';
			$items['epct'][$i]['psop'] 		= !empty($default['psop_'. $i]) ? $default['psop_'. $i] : '';
			$items['epct'][$i]['cct'] 		= !empty($default['cct_'. $i]) ? $default['cct_'. $i] : '';
			$items['epct'][$i]['comments'] 	= !empty($default['comments_'. $i]) ? $default['comments_'. $i] : '';
		}
			
		for ($i=0; $i < $mnacLoop; $i++) { 
			$items['mnac'][$i]['system'] 	= !empty($default['system_'. $i]) ? $default['system_'. $i] : '';
			$items['mnac'][$i]['pu'] 		= !empty($default['pu_'. $i]) ? $default['pu_'. $i] : '';
			$items['mnac'][$i]['hu'] 		= !empty($default['hu_'. $i]) ? $default['hu_'. $i] : '';
			$items['mnac'][$i]['si'] 		= !empty($default['si_'. $i]) ? $default['si_'. $i] : '';
			$items['mnac'][$i]['al'] 		= !empty($default['al_'. $i]) ? $default['al_'. $i] : '';
		}

		for ($i=0; $i < $iclLoop; $i++) { 
			$items['icl'][$i]['position'] 	= !empty($default['position_'. $i]) ? $default['position_'. $i] : '';
			$items['icl'][$i]['name'] 		= !empty($default['name_'. $i]) ? $default['name_'. $i] : '';
			$items['icl'][$i]['op'] 		= !empty($default['op_'. $i]) ? $default['op_'. $i] : '';
			$items['icl'][$i]['cp'] 		= !empty($default['cp_'. $i]) ? $default['cp_'. $i] : '';
			$items['icl'][$i]['email'] 		= !empty($default['email_'. $i]) ? $default['email_'. $i] : '';
		}

		for ($i=0; $i < $eclLoop; $i++) { 
			$items['ecl'][$i]['vendor'] 	= !empty($default['e_vendor_'. $i]) ? $default['e_vendor_'. $i] : '';
			$items['ecl'][$i]['contact'] 	= !empty($default['e_contact_'. $i]) ? $default['e_contact_'. $i] : '';
			$items['ecl'][$i]['phone'] 		= !empty($default['e_phone_'. $i]) ? $default['e_phone_'. $i] : '';
			$items['ecl'][$i]['email'] 		= !empty($default['e_email_'. $i]) ? $default['e_email_'. $i] : '';
			$items['ecl'][$i]['comment'] 	= !empty($default['e_comment_'. $i]) ? $default['e_comment_'. $i] : '';
		}
	}
	return $items;
}
function get_excerpt( $content, $length=7, $more_text='...') {
	$content = str_replace('&nbsp;', ' ', preg_replace('/(<([^>]+)>)/i', '', $content));
	if ($content && str_word_count($content) > $length) {
		$excerpt = [];
		$content = explode(' ', $content);
		for ($i=0; $i < $length; $i++) { 
		    $excerpt[] = trim($content[$i]);
		}
		$excerpt = implode(' ', $excerpt);
		return $excerpt.$more_text;
	}
	return $content;
}
function advisory_add_or_update_post_meta($post_type, $post_id, $meta_key, $meta_value) {
	if (metadata_exists($post_type, $post_id, $meta_key )) delete_post_meta( $post_id, $meta_key);
	if (add_post_meta( $post_id, $meta_key, $meta_value, false )) return true;
	return false;
}
// add_action( 'admin_menu', 'register_my_custom_menu_page' );
function register_my_custom_menu_page() {
  // add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position = null )
  add_submenu_page( 'users.php', 'Job Description', 'Job Description', 'manage_options', 'job-description', 'advisory_company_job_description',10 );
}
function advisory_company_job_description() {
	echo "string";
}