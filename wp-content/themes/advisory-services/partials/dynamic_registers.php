<?php
// MTA Register hooks
add_action(  'archived_mta',  'on_mta_archived', 10, 2 );
// IHC Register hooks
add_action(  'archived_ihc',  'on_ihc_archived', 10, 2 );
// DR Maturity Register hooks
add_action(  'archived_drm',  'on_drm_archived', 10, 2 );
// Data Management Maturity Register hooks
add_action(  'archived_dmm',  'on_dmm_archived', 10, 2 );
// Status chnage for both DRM & DMM
add_action( 'transition_post_status', 'post_unarchived', 10, 3 );
// ON archive MTA Assesment
function on_mta_archived( $assessmentID, $post ) {
	$company = get_post_meta($assessmentID, 'permission', true);
	if ($company) {
		$register = get_page_by_title($post->post_title, OBJECT, 'mtar');
		if ($register) {
			updateOldRegisterFor($company, $post, $register);
		} else {
			createNewRegisterFor($company, 'mta', 'mtar');
		}
	}
}
// ON archive IHC Assesment
function on_ihc_archived( $assessmentID, $post ) {
	$company = get_post_meta($assessmentID, 'permission', true);
	if ($company) {
		$register = get_page_by_title($post->post_title, OBJECT, 'ihcr');
		if ($register) {
			updateOldRegisterFor($company, $post, $register);
		} else {
			createNewRegisterFor($company, 'ihc', 'ihcr');
		}
	}
}
// ON archive DMM Assesment
function on_dmm_archived( $assessmentID, $post ) {
	$company = get_post_meta($assessmentID, 'permission', true);
	if ($company) {
		$register = get_page_by_title($post->post_title, OBJECT, 'dmmr');
		if ($register) {
			updateOldDMMRFor($company, $post, $register);
		} else {
			createNewDMMRFor($company);
		}
	}
}
// ON archive DR Mturity Assesment
function on_drm_archived( $assessmentID, $post ) {
	$company = get_post_meta($assessmentID, 'permission', true);
	if ($company) {
		$drmrr = get_page_by_title($post->post_title, OBJECT, 'drmrr');
		if ($drmrr) {
			updateOldDRMRRFor($company, $post, $drmrr);
		} else {
			// removeCurrentDRMRRFor($company);
			createNewDRMRFor($company);
		}
	}
}
// CHANGED ONE (DYNAMIC)
function createNewRegisterFor($company, $assessmentType, $registerType) {
	$assessment = getLatestAssessmentFor($company, $assessmentType); 			// GET ASSESSMENT
	if ($assessment) {
		$registerID = createTheRegisterFor($assessment, $registerType); 		// CREATE REGISTER
		if($registerID) addRegisterMetaFields($assessment->ID, $registerID); 	// ADD POST META
	}
}
function getLatestAssessmentFor($company, $assessmentType) {
	$assessments = new WP_Query([
        'post_type' 	=> $assessmentType,
        'post_status' 	=> 'archived',
        'posts_per_page'=> 1,
        'meta_query' 	=> [[
            'key' 			=> 'permission',
            'value' 		=> serialize($company),
            'compare' 		=> '='
        ]],
    ]);
    if ($assessments->posts) return $assessments->posts[0];
    return false;
}
function createTheRegisterFor($assessment, $registerType) {
	$registerID = wp_insert_post(array (
	    'post_type' => $registerType,
	    'post_title' => $assessment->post_title,
	    'post_content' => '',
	    'post_status' => 'archived',
	    'comment_status' => 'closed',
	));
	return $registerID;
}
function updateOldRegisterFor($company, $assessment, $register) {
	$assessmentID 	= $assessment->ID;
	$registerID 	= $register->ID;
	if($registerID) addRegisterMetaFields($assessmentID, $registerID); // ADD POST META
}
// CHANGED ONE (DYNAMIC) END
// ON unarchive DR Mturity Assesment
function post_unarchived( $new_status, $old_status, $post ) {
    if ( $old_status == 'archived'  &&  $new_status != 'archived' ) {
		$company = get_post_meta($post->ID, 'permission', true);
		if ($company) {
			if ($post->post_type == 'dmm') {
				$oldRegister = get_page_by_title($post->post_title, OBJECT, 'dmmr');
				if ($oldRegister) {
					updateOldDMMRFor($company, $post, $oldRegister);
				} else {
					createNewDMMRFor($company);
				}
			} elseif ($post->post_type == 'drm') {
				$oldRegister = get_page_by_title($post->post_title, OBJECT, 'drmrr');
				if ($oldRegister) { updateOldDRMRRFor($company, $post, $oldRegister); } 
				else { createNewDRMRFor($company); }
			} elseif ($post->post_type == 'ihc') {
				$assessmentType = 'ihc';
				$registerType = 'ihcr';
				$oldRegister = get_page_by_title($post->post_title, OBJECT, $registerType);
				if ($oldRegister) updateOldRegisterFor($company, $post, $oldRegister);
				else createNewRegisterFor($company, $assessmentType, $registerType);
			}
		}
    }
}
// UPDATE REGISTERS ( alias updateOldRegisterFor)
function updateOldDMMRFor($company, $assessment, $register) {
	$assessmentID 	= $assessment->ID;
	$registerID 	= $register->ID;
	if($registerID) addRegisterMetaFields($assessmentID, $registerID); // ADD POST META
}
function updateOldDRMRRFor($company, $assessment, $register) {
	$assessmentID 	= $assessment->ID;
	$registerID 	= $register->ID;
	if($registerID) addRegisterMetaFields($assessmentID, $registerID); // ADD POST META
}
// DMM
// alias (createNewRegisterFor, getLatestAssessmentFor, createTheRegisterFor)
function createNewDMMRFor($company) {
	$ids = new WP_Query([
        'post_type' 	=> 'dmm',
        'post_status' 	=> 'archived',
        'posts_per_page'=> 1,
        'meta_query' 	=> [[
            'key' 			=> 'permission',
            'value' 		=> serialize($company),
            'compare' 		=> '='
        ]],
    ]);
	if ($ids->posts) {
		$assessment 	= $ids->posts[0];
		$assessmentID 	= $assessment->ID;
		$registerID 	= createNewDMMRForAssessment($assessment);
		if($registerID) addRegisterMetaFields($assessmentID, $registerID); // ADD POST META
	}
}
function createNewDMMRForAssessment($post) {
	$registerID = wp_insert_post(array (
	    'post_type' => 'dmmr',
	    'post_title' => $post->post_title,
	    'post_content' => '',
	    'post_status' => 'archived',
	    'comment_status' => 'closed',
	));
	return $registerID;
}
// DRM
// alias (createNewRegisterFor, getLatestAssessmentFor, createTheRegisterFor)
function createNewDRMRFor($company) {
	$ids = new WP_Query([
        'post_type' 	=> 'drm',
        'post_status' 	=> 'archived',
        'posts_per_page'=> 1,
        'meta_query' 	=> [[
            'key' 			=> 'permission',
            'value' 		=> serialize($company),
            'compare' 		=> '='
        ]],
    ]);
    if ($ids->posts) {
    	$assessment 	= $ids->posts[0];
    	$assessmentID 	= $assessment->ID;
    	$registerID 	= createNewDRMRRForAssessment($assessment);
		if($registerID) addRegisterMetaFields($assessmentID, $registerID); // ADD POST META
    }
}
function createNewDRMRRForAssessment($post) {
	$registerID = wp_insert_post(array (
	    'post_type' => 'drmrr',
	    'post_title' => $post->post_title,
	    'post_content' => '',
	    'post_status' => 'archived',
	    'comment_status' => 'closed',
	));
	return $registerID;
}
function getDynamicRegisterMenuFor($postType='risk', $title='DR Risk Register', $pageTitle='Risk Register', $icon=null, $showEmpty=false) {
	$image = '';
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
    if ($icon) {
    	$image = '<img src="'. get_template_directory_uri() .'/images/'. $icon .'" alt="'. @$title .' Icon">';
    }
    if (isset($rr_form_id[0]) && !empty($rr_form_id[0])) {
        $rr_data = advisory_get_formatted_drr_data($rr_form_id[0]);
        // echo '<br>'. $rr_form_id[0] .'<pre>'. print_r($rr_data, true) .'</pre>';
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
function advisory_get_formatted_drr_data($post_id) {
	$meta = get_post_meta($post_id, 'form_opts', true);
	$data = [];
	$index = 0;
	// help($meta);
	if (!empty($meta['areas'])) {
		foreach ($meta['areas'] as $threat_cat) {
			$data[$index]['cat'] = $threat_cat['name'];
			$data[$index]['base'] = $threat_cat['base'];
            $cat_id = advisory_id_from_string($threat_cat['name']);
			if ($threats = $meta[$cat_id . '_threats']) {
				foreach ($threats as $threatIndex => $threat) {
					if ($threat) {
						$data[$index]['areas'][$threatIndex]['name'] = $threat['name'];
						$data[$index]['areas'][$threatIndex]['avg'] = $threat['avg'];
					}
				}
			}
			$index++;
		}
	}
	return $data;
}
function addRegisterMetaFields($assessmentID, $registerID) {
	$assessment = get_post_meta($assessmentID, 'form_opts', true);
	$register['display_name'] 	= $assessment['display_name'];
	$register['desc'] 			= $assessment['desc'];
	$register['icon'] 			= $assessment['icon'];
	$register['areas'] 			= $assessment['areas'];
	// areas
	if ($register['areas']) {
		$base = 1;
		foreach ($register['areas'] as $key => $area) {
			// categories / areas
			if (isset($area['color'])) unset($register['areas'][$key]['color']);
			if (isset($area['desc'])) $register['areas'][$key]['desc'] = '';
			if (!isset($area['base'])) $register['areas'][$key]['base'] = $base;
			// Threats / sections
			$commonID = advisory_id_from_string($area['name']);
			$threatID = $commonID .'_threats';
			$sectionID = 'sections_'. $commonID;
			if (empty($assessment[$sectionID])) $register[$threatID] = [];
			else {
				foreach ($assessment[$sectionID] as $key => $threat) {
					$register[$threatID][$key]['name'] = $threat['name'];
					$register[$threatID][$key]['desc'] = $threat['desc'];
					// GETTING AVG FROM ASSESSMENT
					// META EXAMPLE : sections_organizational_readiness_tables_program_initiation
					$threat_id = $sectionID . '_tables_' . advisory_id_from_string($threat['name']);
					$default = advisory_form_default_values($assessmentID, $threat_id);
					$form_data = advisory_form_default_values($assessmentID, $threat_id);
					$register[$threatID][$key]['avg'] = $form_data['avg'] ? $form_data['avg'] : '0.0';
					// questions / tables (may be)
					// sections_customer_facing_tables_customer_relationship_management
					// customer_facing_threats_customer_relationship_management
					$registerThreadID = $threatID.'_'.advisory_id_from_string($threat['name']);
					if (!empty($assessment[$threat_id])) {
						foreach ($assessment[$threat_id] as $subthreadSI => $subthread) {
							// sections_customer_facing_tables_customer_relationship_management_questions_crm_ecrm_avg
							$subthreadID = $threat_id.'_questions_'. advisory_id_from_string($subthread['name']) .'_avg';
							$register[$registerThreadID][$subthreadSI] = $subthread;
							$register[$registerThreadID][$subthreadSI]['avg'] = @$default[$subthreadID];
						}
					}
				}
			}
			$base++;
		}
	}
	// ADD form_opts META
	if (get_post_meta($registerID, 'form_opts', true)) update_post_meta($registerID, 'form_opts', $register);
	else add_post_meta($registerID, 'form_opts', $register);
	// ADD company META
	$company = get_post_meta($assessmentID, 'permission', true);
	if ($company) {
		if (get_post_meta($assessmentID, 'permission', true)) update_post_meta($registerID, 'permission', $company);
		else add_post_meta($registerID, 'permission', $company);
		// ADD assigned_company META
		if (get_post_meta($assessmentID, 'assigned_company', true)) update_post_meta($registerID, 'assigned_company', $company['users']);
		else add_post_meta($registerID, 'assigned_company', $company['users']);
	}
}
// NOT USED
function removeCurrentDRMRRFor($company) {
	$ids = new WP_Query([
        'post_type' => 'drmrr',
        'post_status' => 'archived',
        'posts_per_page' => -1,
        'meta_query' => [[
            'key' => 'permission',
            'value' => serialize($company),
            'compare' => '='
        ]],
        'fields' => 'ids',
    ]);
    if ($ids->posts) {
    	foreach ($ids->posts as $register) {
    		$regIDS[] = $register;
    		wp_delete_post($register, true); // Permanenly delete
    	}
    }
}
function dynamicRegisterAvgCalculation($avg=0) {
	$data = [];
	if ($avg >= 0 && $avg <= 3) {
		$data['color'] = 'color-four';
		$data['level'] = 'Low';
	} else if ($avg >= 4 && $avg <= 8) {
		$data['color'] = 'color-three';
		$data['level'] = 'Medium';
	} else if ($avg >= 9 && $avg <= 12) {
		$data['color'] = 'color-two';
		$data['level'] = 'High';
	} else if ($avg >= 13 && $avg <= 16) {
		$data['color'] = 'color-one';
		$data['level'] = 'Extreme';
	}
	if ($data) {
		$button = '<div class="total-risk text-center font-120p '. $data['color'] .'"><strong>'. $data['level'] .'</strong></div>';
	}
	return $button;
}
// NOT USED END
// TEST
function addRegisterMetaFieldsTest($assessmentID=510, $registerID=592) {
	$assessment = get_post_meta($assessmentID, 'form_opts', true);
	$register['display_name'] 	= $assessment['display_name'];
	$register['desc'] 			= $assessment['desc'];
	$register['icon'] 			= $assessment['icon'];
	$register['areas'] 			= $assessment['areas'];
	// areas
	if ($register['areas']) {
		$base = 1;
		foreach ($register['areas'] as $key => $area) {
			// categories / areas
			if (isset($area['color'])) unset($register['areas'][$key]['color']);
			if (isset($area['desc'])) $register['areas'][$key]['desc'] = '';
			if (!isset($area['base'])) $register['areas'][$key]['base'] = $base;
			// Threats / sections
			$commonID = advisory_id_from_string($area['name']);
			$threatID = $commonID .'_threats';
			$sectionID = 'sections_'. $commonID;
			if ($assessment[$sectionID]) {
				foreach ($assessment[$sectionID] as $key => $threat) {
					$register[$threatID][$key]['name'] = $threat['name'];
					$register[$threatID][$key]['desc'] = $threat['desc'];
					// GETTING AVG FROM ASSESSMENT
					// META EXAMPLE : sections_organizational_readiness_tables_program_initiation
					$threat_id = $sectionID . '_tables_' . advisory_id_from_string($threat['name']);
					$default = advisory_form_default_values($assessmentID, $threat_id);
					$form_data = advisory_form_default_values($assessmentID, $threat_id);
					$register[$threatID][$key]['avg'] = $form_data['avg'] ? $form_data['avg'] : '0.0';
					// questions / tables (may be)
					// sections_customer_facing_tables_customer_relationship_management
					// customer_facing_threats_customer_relationship_management
					$registerThreadID = $threatID.'_'.advisory_id_from_string($threat['name']);
					if (!empty($assessment[$threat_id])) {
						foreach ($assessment[$threat_id] as $subthreadSI => $subthread) {
							// sections_customer_facing_tables_customer_relationship_management_questions_crm_ecrm_avg
							$subthreadID = $threat_id.'_questions_'. advisory_id_from_string($subthread['name']) .'_avg';
							$register[$registerThreadID][$subthreadSI] = $subthread;
							$register[$registerThreadID][$subthreadSI]['avg'] = @$default[$subthreadID];
						}
					}
				}
			} else {
				$register[$threatID] = [];
			}
			$base++;
		}
	}
	// sections_customer_facing_tables_customer_relationship_management
	// sections_customer_facing_tables_customer_relationship_management


	// // ADD form_opts META
	// if (get_post_meta($registerID, 'form_opts', true)) {
	// 	update_post_meta($registerID, 'form_opts', $register);
	// } else {
	// 	add_post_meta($registerID, 'form_opts', $register);
	// }
	// // ADD company META
	// $company = get_post_meta($assessmentID, 'permission', true);
	// if ($company) {
	// 	if (get_post_meta($assessmentID, 'permission', true)) {
	// 		update_post_meta($registerID, 'permission', $company);
	// 	} else{
	// 		add_post_meta($registerID, 'permission', $company);
	// 	}
	// 	// ADD assigned_company META
	// 	if (get_post_meta($assessmentID, 'assigned_company', true)) {
	// 		update_post_meta($registerID, 'assigned_company', $company['users']);
	// 	} else{
	// 		add_post_meta($registerID, 'assigned_company', $company['users']);
	// 	}
	// }
	help($register);
}
// TEST END