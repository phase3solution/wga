<?php
function advisory_sfia_get_menu_items($post_id) {
	global $sfia_premission;
	if ($sfia_premission) {
		$form_meta = get_post_meta($post_id, 'form_opts', true);
		return '<li><a href="'.home_url('sfia-assessment').'"><img src="'.IMAGE_DIR_URL.'sfia/logo.png"><span>' . advisory_get_form_name($post_id) . '</span></a>';
	}
	return '';
}
function advisory_generate_sfia_form_subcategories() {
	if (is_admin() && is_edit_page()) {
		$data = [];
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create Sub-category and save. Then create Table from "Skills" tab'];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $category) {
				$data[] = [
					'id' => 'sections_' . advisory_id_from_string($category['name']),
					'type' => 'group',
					'title' => $category['name'],
					'desc' => 'Each Sub-category name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Sub-category',
					'fields' => [['id' => 'name', 'type' => 'text', 'title' => 'Name'], ['id' => 'desc', 'type' => 'textarea', 'title' => 'Description']]
				];
			}
		}
		return $data;
	}
}
function advisory_generate_sfia_form_skills() {
	if (is_admin() && is_edit_page()) {
		$data = [];
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create Table and save.'];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $category) {
				$section_id = 'sections_' . advisory_id_from_string($category['name']);
				if (!empty($meta[$section_id])) {
					$data[] = ['type' => 'heading', 'content' => $category['name']];
					foreach ($meta[$section_id] as $section) {
						$data[] = array(
							'id' => $section_id . '_tables_' . advisory_id_from_string($section['name']),
							'type' => 'group',
							'title' => $section['name'],
							'desc' => 'Each Table name should be unique',
							'button_title' => 'Add New',
							'accordion_title' => 'Add New Table',
							'fields' => [
								['id' => 'name', 'type' => 'text', 'title' => 'Name'],
								['id' => 'code', 'type' => 'text', 'title' => 'Code'],
								['id' => 'desc', 'type' => 'textarea', 'title' => 'Desc'],
								[
									'id' => 'levels',
									'type' => 'textarea',
									'title' => 'Levels',
									'desc' => 'One Level per line',
									'default' => '1:Level 1&#13;&#10;2:Level 2&#13;&#10;3:Level 3&#13;&#10;4:Level 4&#13;&#10;5:Level 5&#13;&#10;6:Level 6&#13;&#10;7:Level 7'
								]
							]
						);
					}
				}
			}
		}
		return $data;
	}
}
// SINGLE PAGE
add_action('wp_ajax_sfia_get_team_users', 'sfia_get_team_users');
function sfia_get_team_users() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$company_id = !empty($_POST['company_id']) ? $_POST['company_id'] : false;
	$team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : false;
	$load_published_users = !empty($_POST['load_published_users']) ? $_POST['load_published_users'] : false;
	$company = get_term_meta($company_id, 'company_data', true);

	$user_type = !empty($_POST['load_published_users']) ? 'published_users' : 'active_users';
	$active_users = get_post_meta( $post_id, $user_type, true );
	$company_users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
	if (!empty($active_users[$team_id]) && !empty($company_users)) {
		$data .= '<option value="">Select Name</option>';
		foreach ($active_users[$team_id] as $user_id => $user_name) {
			if (!empty($company_users[$user_id])) 
			$data .= '<option value="'.$user_id.'">'.$user_name.'</option>';
		}
	}
	echo $data; wp_die();
}
add_action('wp_ajax_sfia_get_team_user_form', 'sfia_get_team_user_form');
function sfia_get_team_user_form() {
	$data = [];
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	$archive = !empty($_POST['archive']) ? $_POST['archive'] : false;
	$select_attr = !empty($_POST['select_attr']) ? $_POST['select_attr'] : false;
	$head = advisory_form_default_values($post_id, $user_id.'_head');
	$skills = advisory_form_default_values($post_id, $user_id.'_skills');
	$skills_fit = 'N/A';
    if ( isset($skills['avg']) ) {
        $skills_fit = $skills['avg'];
        unset($skills['avg']);
    }
    $technical_score = 0;
	$data['unique_id'] 	= advisory_sfia_get_team_user_details_id($user_id);
	$data['role'] 		= advisory_sfia_get_team_user_details_role($head, $user_id, $select_attr);
	$data['level'] 		= advisory_sfia_get_team_user_details_level($head, $user_id, $select_attr);
	$data['notes'] 		= advisory_sfia_get_team_user_details_notes($head, $user_id, $select_attr);
	$data['skillAvg'] 	= advisory_sfia_get_team_user_skill_avg($skills_fit);
	$data['tsAvg'] 		= advisory_sfia_get_team_technical_score_avg($user_id, $head, $select_attr, $archive);
	$data['bottom'] 	= advisory_sfia_get_team_user_bottomContent($post_id, $user_id, $skills, $skills_fit, $select_attr);
	$data['pdfsummary'] = advisory_sfia_get_team_user_summary($post_id, $user_id, $select_attr);
	echo json_encode($data); wp_die();
}
add_action('wp_ajax_get_sfia_skill_items', 'get_sfia_skill_items');
function get_sfia_skill_items() {
	$html = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$counter = !empty($_POST['counter']) ? $_POST['counter'] : false;
	if ($post_id && $counter) {
		$opts = get_post_meta($post_id, 'form_opts', true);
		$html .= advisory_generate_skill_items($opts, $counter, [], '', true);
	}
	echo $html; wp_die();
}
add_action('wp_ajax_sfia_add_team_user', 'sfia_add_team_user');
function sfia_add_team_user() {
	$html = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	$user_name = !empty($_POST['user_name']) ? $_POST['user_name'] : false;


	if ($post_id && $team_id && $user_id && $user_name) {
		$active_users = get_post_meta( $post_id, 'active_users', true);
		if (!empty($active_users)) { $active_users[$team_id][$user_id] = $user_name; }
		else { $active_users = [$team_id =>[$user_id => $user_name]]; }
		if (update_post_meta( $post_id, 'active_users', $active_users)) wp_send_json(true);
	}
	wp_send_json(false);
}
add_action('wp_ajax_sfia_change_team_user', 'ajax_sfia_change_team_user');
function ajax_sfia_change_team_user() {
	$html = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$old_team_id = !empty($_POST['old_team_id']) ? $_POST['old_team_id'] : false;
	$new_team_id = !empty($_POST['new_team_id']) ? $_POST['new_team_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	$user_name = !empty($_POST['user_name']) ? $_POST['user_name'] : false;
	$user_status = !empty($_POST['user_status']) ? $_POST['user_status'] : false;


	if ($post_id && $old_team_id && $new_team_id && $user_id && $user_name && $user_status) {
		$users = get_post_meta( $post_id, $user_status, true);

		if (!empty($users)){
			if (isset($users[$old_team_id][$user_id])) unset($users[$old_team_id][$user_id]);
			$users[$new_team_id][$user_id] = $user_name;
			if (update_post_meta( $post_id, $user_status, $users)) wp_send_json(true);
		}
	}
	wp_send_json(false);
}
function advisory_generate_skill_items($opts, $skillCount=0, $defaults=[], $select_attr='', $ajax_call=false) {
	$html = '';
	$loop = !empty($defaults) ? count($defaults) / 6 : 0;
	// if ($skillCount>0) $loop = $skillCount;
	$deleteAttr = $loop > 1 || !empty($select_attr) || $ajax_call ? '' : 'disabled';
	// $html .= '<tr><td colspan=8>'.$loop.'<pre>'. print_r($defaults, true) .'</pre></tr></td>';
	// $html .= '<tr><td colspan=5><pre>'. print_r($opts, true) .'</pre></tr></td>';
	do {
		$level_opts = [];
		$defaultCatId = !empty($defaults['category_'.$skillCount]) ? 'sections_'.$defaults['category_'.$skillCount] : '';
		$defaultCatClass= advisory_get_sfia_category_class(@$defaults['category_'.$skillCount]);
		$html .= '<tr id="skillContainer_'.$skillCount.'" class="skillContainer '.$defaultCatClass.'" skillcount='.$skillCount.'>';
	        if (!$select_attr) $html .= '<td class="move">'. ($skillCount + 1) .'</td>';
	        $html .= '<td class="category_container">';
	        	$html .= advisory_generate_sfia_categories($opts, $skillCount, $defaults, $select_attr);
	        $html .= '</td>';
	        $defaultSubCategoryId = !empty($defaults['subcategory_'.$skillCount]) ? $defaults['subcategory_'.$skillCount] : '';
	        $subcategoryAttr = empty($defaultSubCategoryId) || !empty($select_attr) ? 'disabled' : '';
	        $html .= '<td class="sub_category_container">';
	            $html .= '<select name="subcategory_'.$skillCount.'" id="subcategory_'.$skillCount.'" class="subcategory" '.$subcategoryAttr.'>';
	                $html .= '<option value="0">Sub-Category</option>';
	                if (!empty($opts[$defaultCatId])) {
	                	foreach ($opts[$defaultCatId] as $subcategory) {
							$subcatId = advisory_id_from_string($subcategory['name']);
							$attr = $defaultSubCategoryId == $subcatId ? 'selected' : '';
	                		$html .= '<option value="'.$subcatId.'" '.$attr.'>'.$subcategory['name'].'</option>';
	                	}
	                }
	            $html .= '</select>';
	        $html .= '</td>';
	        $defaultSkillId = !empty($defaults['skill_'.$skillCount]) ? $defaults['skill_'.$skillCount] : '';
	        $skillAttr = empty($defaultSkillId) || !empty($select_attr) ? 'disabled' : '';
	        $skill_opts = !empty($opts[$defaultCatId.'_tables_'.$defaultSubCategoryId]) ? $opts[$defaultCatId.'_tables_'.$defaultSubCategoryId] : [];
	        
	        $html .= '<td class="skills_container">';
	            $html .= '<select name="skill_'.$skillCount.'" id="skill_'.$skillCount.'" class="skills" '.$skillAttr.'>';
	                $html .= '<option value="0">Skills</option>';
	                if (!empty($skill_opts)) {
	                	foreach ($skill_opts as $skill) {
							$skillId = advisory_id_from_string($skill['name']);
							if ($defaultSkillId == $skillId) { 
								$level_opts = advisory_select_array($skill['levels']);
								$attr = 'selected';
								$code = $skill['code'];
							} else { $attr = ''; }
	                		$html .= '<option value="'.$skillId.'" code="'.$skill['code'].'" '.$attr.'>'.$skill['name'].'</option>';
	                	}
	                }
	            $html .= '</select>';
	        $html .= '</td>';
	        $html .= '<td class="code_container">';
	            $html .= '<div id="code_'.$skillCount.'" class="code">'.$code.'</div>';
	        $html .= '</td>';
	        $defaultRank = !empty($defaults['rank_'.$skillCount]) ? $defaults['rank_'.$skillCount] : '';
	        $rankAttr = empty($defaultRank) || !empty($select_attr) ? 'disabled' : '';
	        $rank_opts = ['Core', 'Contributor', 'Awareness'];
	        $html .= '<td class="rank_container">';
	        	$html .= '<select name="rank_'.$skillCount.'" id="rank_'.$skillCount.'" class="ranks" '.$select_attr.'>';
	                // $html .= '<option value="0">ranks</option>';
	                if (!empty($rank_opts)) {
	                	foreach ($rank_opts as $rank) {
							$rankId = advisory_id_from_string($rank);
							if ($defaultRank == $rankId) $attr = 'selected';
							else $attr = '';
	                		$html .= '<option value="'.$rankId.'" '.$attr.'>'.$rank.'</option>';
	                	}
	                }
	            $html .= '</select>';
	        $html .= '</td>';
	        $default_assess_level = isset($defaults['assess_level_'.$skillCount]) ? $defaults['assess_level_'.$skillCount] : '';
	        $default_target_level = isset($defaults['target_level_'.$skillCount]) ? $defaults['target_level_'.$skillCount] : '';
	        $default_assess_level_class = advisory_get_sfia_access_level_container_class($default_assess_level, $default_target_level);

	        $html .= '<td class="target_level_container">';
	            $html .= '<select name="target_level_'.$skillCount.'" id="target_level_'.$skillCount.'" class="target_level" '.$skillAttr.'>';
	                $html .= '<option value="">Target</option>';
	                if (!empty($level_opts)) {
	                	foreach ($level_opts as $levelId => $level) {
	                		$selectedLevel = $default_target_level == $levelId ? 'selected' : '';
	                		$html .= '<option value="'.$levelId.'" '.$selectedLevel.'>'.$level.'</option>';
	                	}
	                }
	            $html .= '</select>';
	        $html .= '</td>';
	        
	        $html .= '<td class="access_level_container '.$default_assess_level_class.'">';
	            $html .= '<select name="assess_level_'.$skillCount.'" id="assess_level_'.$skillCount.'" class="assessed_level" '.$skillAttr.'>';
	                $html .= '<option value="">Evaluation</option>';
	                if (!empty($level_opts)) {
	                	$level_opts = [0=>'Missing'] + $level_opts;
	                	foreach ($level_opts as $alevelId => $alevel) {
	                		$selectedLevel = $default_assess_level != '' && $default_assess_level == $alevelId ? 'selected' : '';
	                		$html .= '<option value="'.$alevelId.'" '.$selectedLevel.'>'.$alevel.'</option>';
	                	}
	                }
	            $html .= '</select>';
	        $html .= '</td>';
	        
	        // $infoImgLink = IMAGE_DIR_URL .'sfia/info/'.$defaultSkillId.'.png';
	        $infoImgLink = IMAGE_DIR_URL .'sfia/info/';
	        if ($code) {
		        $infoImgLink .= $code.'.pdf';
		        $SFIARemoveBtnAttr = '';
	        } else {
	        	$infoImgLink .= '';
		        $SFIARemoveBtnAttr = 'disabled';
	        }
	        $html .= '<td class="text-center actions_container">';
	        // $html .= '<span class="SFIALevelInfoBtn"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
	        $html .= '<button class="btn btn-primary SFIAInfoBtn" type="button" title="Information" data-skill="'.$infoImgLink.'" '.$SFIARemoveBtnAttr.'><span class="fa fa-info-circle"></span></button> ';
	        if (!$select_attr) $html .= '<button class="btn btn-danger SFIARemoveBtn" type="button" title="Delete row" '.$deleteAttr.'><span class="fa fa-trash"></span></button> ';
	        $html .= '</td>';
	    $html .= '</tr>';
	    $skillCount++;
	} while ( $skillCount < $loop);
    return $html;
}
function advisory_generate_sfia_categories($opts, $skillCount, $defaults, $select_attr) {
	$html = '';
	$html .= '<select name="category_'.$skillCount.'" id="category_'.$skillCount.'" class="categoryItem" '.$select_attr.'>';
		$html .= '<option value="0" selected="">Category</option>';
	    if (!empty($opts['areas'])) {
	        foreach ($opts['areas'] as $category) {
	        	$catId = advisory_id_from_string($category['name']);
	        	$selected = !empty($defaults['category_'.$skillCount]) && $catId == $defaults['category_'.$skillCount] ? ' selected' : '';
				$html .= '<option value="'.advisory_id_from_string($category['name']).'"'. $selected .'>'.$category['name'].'</option>';
	        }
	    }
	$html .= '</select>';
	return $html;
}
add_action('wp_ajax_get_sfia_subcategory', 'get_sfia_subcategory');
add_action('wp_ajax_nopriv_get_sfia_subcategory', 'get_sfia_subcategory');
function get_sfia_subcategory() {
	$html = '';
	$category = !empty($_POST['category']) ? 'sections_'.$_POST['category'] : false;
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	if ($post_id && $category) {
		$opts = get_post_meta($post_id, 'form_opts', true);
		if (!empty($opts[$category])) {
			$html .= '<option value="0">Sub-Category</option>';
			foreach ($opts[$category] as $subcat) {
				$html .= '<option value="'. advisory_id_from_string($subcat['name']) .'">'.$subcat['name'].'</option>';
			}
		}
	}
	echo $html; wp_die();
}
add_action('wp_ajax_get_sfia_skills', 'get_sfia_skills');
add_action('wp_ajax_nopriv_get_sfia_skills', 'get_sfia_skills');
function get_sfia_skills() {
	$html = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$category = !empty($_POST['category']) ? 'sections_'.$_POST['category'] : false;
	$subcategory = !empty($_POST['subcategory']) ? '_tables_'.$_POST['subcategory'] : false;
	if ($post_id && $category && $subcategory) {
		$opts = get_post_meta($post_id, 'form_opts', true);
		if (!empty($opts[$category.$subcategory])) {
			$html .= '<option value="0">Skills</option>';
			foreach ($opts[$category.$subcategory] as $subcat) {
				$html .= '<option value="'.advisory_id_from_string($subcat['name']) .'" code="'.$subcat['code'].'">'.$subcat['name'].'</option>';
			}
		}
	}
	echo $html; wp_die();
}
add_action('wp_ajax_get_sfia_assessed_level', 'get_sfia_assessed_level');
function get_sfia_assessed_level() {
	$html = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$category = !empty($_POST['category']) ? 'sections_'.$_POST['category'] : false;
	$subcategory = !empty($_POST['subcategory']) ? '_tables_'.$_POST['subcategory'] : false;
	$skills = !empty($_POST['skills']) ? $_POST['skills'] : false;
	if ($post_id && $skills) {
		$opts = get_post_meta($post_id, 'form_opts', true);
		if (!empty($opts[$category.$subcategory])) {
			foreach ($opts[$category.$subcategory] as $subcat) {
				if ($skills == advisory_id_from_string($subcat['name']) && !empty($subcat['levels'])) {
					$levels = advisory_select_array($subcat['levels']);
					if (!empty($levels)) {
						// $levels = [0=>'Missing'] + $levels;
						foreach ($levels as $levelId => $level) {
							$html .= '<option value="'.$levelId.'">'.$level.'</option>';
						}
					}
				}
			}
		}
	}
	echo $html; wp_die();
}
function advisory_get_sfia_category_class($value) {
	$cls = '';
	switch ($value) {
		case 'strategy_and_architecture': 		$cls = 'color-one'; 	break;
		case 'change_and_transformation': 		$cls = 'color-two'; 	break;
		case 'development_and_implementation': 	$cls = 'color-three'; 	break;
		case 'delivery_and_operation': 			$cls = 'color-four'; 	break;
		case 'skills_and_quality': 				$cls = 'color-five'; 	break;
		case 'relationships_and_engagement': 	$cls = 'color-six'; 	break;
		default: 								$cls = ''; 				break;
	}
	return $cls;
}
function advisory_get_sfia_skills_fit_class($avg) {
    $cls = '';
    if (!is_numeric($avg)) { $cls = 'bg-gap'; }
    else if ($avg >= 100) 	{ $cls = 'bg-deepblue'; }
    else if ($avg >= 90) 	{ $cls = 'bg-deepgreen'; }
    else if ($avg >= 80) 	{ $cls = 'bg-yellow'; }
    else if ($avg >= 70) 	{ $cls = 'bg-orange'; }
    else                	{ $cls = 'bg-red'; }
    return $cls;
}
function advisory_get_sfia_skills_fit_color($avg) {
    $cls = '';
    if (!is_numeric($avg)){ $cls = 'color-gap'; }
    else if ($avg >= 100) 	{ $cls = 'color-deepblue'; }
    else if ($avg >= 90) 	{ $cls = 'color-deepgreen'; }
    else if ($avg >= 80) 	{ $cls = 'color-yellow'; }
    else if ($avg >= 70) 	{ $cls = 'color-orange'; }
    else                	{ $cls = 'color-red'; }
    return $cls;
}
function advisory_sfia_calculate_level_scores($assessed_level, $target_level) {
	if ($assessed_level && $target_level) {
	    if ($assessed_level >= $target_level) 			 { return 1; }
	    else if ($assessed_level == ($target_level - 1)) { return 0.5; }
	    else if ($assessed_level == ($target_level - 2)) { return 0.3; }
	    else if ($assessed_level == ($target_level - 3)) { return 0.1; }
	    else 											 { return 0; }
	}
	return 0;
}
function advisory_get_sfia_access_level_container_class($assessed_level, $target_level) {
	if ($assessed_level && $target_level) {
	    if ($assessed_level > $target_level) 			{ return 'bg-deepblue'; }
	    else if ($assessed_level == $target_level) 		{ return 'bg-deepgreen'; }
	    else if ($assessed_level == ($target_level - 1)){ return 'bg-yellow'; }
	    else if ($assessed_level == ($target_level - 2)){ return 'bg-orange'; }
	    else if ($assessed_level == ($target_level - 3)){ return 'bg-red'; }
	    else 											{ return 'bg-red'; }
	}
	return 'bg-red';
}
function advisory_sfia_get_team_user_details_role($head, $unique_id, $select_attr='') {
	$data = '';
	$company_id = advisory_get_user_company_id();
	$company = get_term_meta($company_id, 'company_data', true);
	$roles = !empty($company['sfia_roles']) ? advisory_select_array($company['sfia_roles']) : [];

	$data .= '<label for="name"><img src="'.IMAGE_DIR_URL.'sfia/assessment/role.png" class="levelImg"></label>';
	$data .= '<select name="role" id="role" class="form-control"'.$select_attr.'>';
        $data .= '<option value="">Role/Title</option>';
        if (!empty($roles)) {
        	foreach ($roles as $role_id => $role) {
        		$selected_role = $head['role'] == $role_id ? 'selected' : '';
        		$data .= '<option value="'.$role_id.'" '.$selected_role.'>'.$role.'</option>';
        	}
        }
    $data .= '</select>';
	return $data;
}
function advisory_sfia_get_team_user_details_level($head, $unique_id, $select_attr='') {
	$data = '';
	$defaultHead = !empty($head['level']) ? $head['level'] : 0;
	$data .= '<img class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" src="'.IMAGE_DIR_URL.'sfia/assessment/level_desc.png" alt="">';
    $data .= '<div style="font-size: 17px;font-weight:bold;text-align: center;">Level <span class="levelValue">'.$defaultHead.'</span></div>';
    if (!$select_attr) {
	    $data .= '<ul class="dropdown-menu">';
	        for ($level=1; $level <= 7 ; $level++) { 
	            $data .= '<li><a class="level_'.$level.'" href="javascript:;" data-level='.$level.'>Level '.$level.'</a></li>';
	        }
	    $data .= '</ul>';
	    $data .= '<input type="hidden" name="level" id="level" value="'.$head['level'].'">';
    }
	return $data;
}
function advisory_sfia_get_team_user_details_id($unique_id) {
	$data = '';
	$data .= '<span style="font-size: 17px;font-weight: bold">ID : '.$unique_id.'</span>';
	return $data;
}
function advisory_sfia_get_team_user_details_notes($head, $unique_id, $select_attr) {
	$data = '';
    $data .= '<label for="name"><img src="'.IMAGE_DIR_URL.'sfia/assessment/notes.png" class="levelImg"></label>';
    $data .= '<textarea name="notes" cols="30" rows="4" class="form-control" placeholder="Notes"'.$select_attr.'>'.$head['notes'].'</textarea>';
    // $data .= '<br><pre>'. print_r($head, true) .'</pre>';
	return $data;
}
function advisory_sfia_get_team_user_skill_avg($skills_fit) {
	$data = '';
	$skill_fit_text = $skills_fit == 'N/A' || $skills_fit == '' ? 'N/A' : number_format($skills_fit) .'%';
	// $data .= '<br><pre>'. print_r($skills_fit, true) .'</pre>';
	$data .= '<div class="skill-title"><img src="'.IMAGE_DIR_URL.'sfia/assessment/skills_fit.png" class="levelImg"></div>';
	$data .= '<div class="skill-point '.advisory_get_sfia_skills_fit_class($skills_fit).'">'.$skill_fit_text.'</div>';
	return $data;
}
function advisory_sfia_get_team_technical_score_avg($unique_id, $head, $select_attr, $archive=false) {
	$data = '';
	// $active_sfiats_id = advisory_sfiats_get_active_assessments($unique_id);
	if ($archive) {
		$sfiats_id = !empty($head['sfiats_id']) ? $head['sfiats_id'] : 0 ;
		$lastPublished = get_post_meta( $sfiats_id, 'archive_date', true );
	} else {
		$published_sfiats_id = advisory_sfiats_get_published_assessments($unique_id);
		$lastPublished = get_post_meta( $published_sfiats_id, 'archive_date', true );
		$sfiats_id = advisory_sfiats_get_last_assessments($unique_id);
	}
	
	// $data .='<br><pre>'. print_r($lastPublished, true) .'</pre>';
	$default = advisory_form_default_values($sfiats_id, 'poles');
	$sfiatsAvg = !empty($default['avg']) ? $default['avg'] : 0;
	$sfiats['cls'] = advisory_get_sfia_skills_fit_class($sfiatsAvg);
	$sfiats['text'] = number_format($sfiatsAvg).'%';

	// $data .= '<br><pre>'. print_r(['active_sfiats_id'=>$active_sfiats_id], true) .'</pre>';
	$data .= '<div class="ta-title"><img src="'.IMAGE_DIR_URL.'sfia/assessment/technica_score.png" class="levelImg"></div>';
    $data .= '<div class="buttonsContainer">';
		$data .= '<input type="hidden" name="sfiats_id" class="form-control" value="'.$sfiats_id.'">';
        $data .= '<div class="text-center point '.$sfiats['cls'].'">'. $sfiats['text'] .'</div>';
        if (!$select_attr) {
        	$sfiats_edit_link = $sfiats_id ? 'href="'.site_url('sfiats/').$sfiats_id.'/" target="_blank"' : 'href="javascript:;"';
	        $data .= '<div class="edit">';
		        if (get_post_status($sfiats_id) != 'archived') $data .= '<a '.$sfiats_edit_link.'><img src="'.IMAGE_DIR_URL.'sfia/assessment/start-green.png" class="levelImg"></a>';
		        else $data .= '<a '.$sfiats_edit_link.' ><img src="'.IMAGE_DIR_URL.'sfia/assessment/start-red.png" class="levelImg"></a>';
			$data .= '</div>';
        }
		if ($lastPublished) $data .= '<div class="text-center lastPublished"> Last Published : '. date('M, j\<\s\u\p\>S\<\/\s\u\p\> Y', $lastPublished) .'</div>';
    $data .= '</div>';
    // $data .='<br><pre>'. print_r($head, true) .'</pre>';
	return $data;
}
function advisory_sfia_get_team_user_bottomContent($post_id, $user_id, $default_skills, $skills_fit, $select_attr='') {
	$data = '';
	$opts = get_post_meta($post_id, 'form_opts', true);
    $opts['display_name'] = !empty($opts['display_name']) ? $opts['display_name'] : get_the_title($post_id);
	// $data .= '<br><pre>'. print_r($default_skills, true) .'</pre>';
	$data .= '<form class="sfia-form single sfia-wrapper-bottom" method="post" data-meta="'.$user_id.'_skills" data-id="'.$post_id.'">';
        $data .= '<div class="card"> <div class="card-body sfia-wrapper">';
			$data .= '<table class="table-bordered skillWrapper">';
		        $data .= '<thead>';
		            $data .= '<tr class="titleWrapper">';
		                if (!$select_attr) $data .= '<td class="skill_number"> <h3 class="title">##</h3> </td>';
		                $data .= '<td class="categoryTitle"> <h3 class="title">Category</h3> </td>';
		                $data .= '<td class="sub_categoryTitle"> <h3 class="title">Sub-category</h3> </td>';
		                $data .= '<td class="skillsTitle"> <h3 class="title">Skill</h3> </td>';
		                $data .= '<td class="codeTitle"> <h3 class="title">Code</h3> </td>';
		                $data .= '<td class="rankTitle"> <h3 class="title">Rank</h3> </td>';
		                $data .= '<td class="targetLevelTitle"> <h3 class="title">Target</h3> </td>';
		                $data .= '<td class="accessLevelTitle"> <h3 class="title">Evaluation</h3> </td>';
		                $data .= '<td class="actionTitle"><h3 class="title">Actions</h3></td>';
		            $data .= '</tr>';
		        $data .= '</thead>';
		        $data .= '<tbody id="sortable">';
		        $data .= advisory_generate_skill_items($opts, 0, $default_skills, $select_attr);
		        $data .= '</tbody>';
		    $data .= '</table>';
		    $data .= '<div class="clearfix"><br></div>';
		    $data .= '<input type="hidden" name="avg" id="skills_fit" value="'.$skills_fit.'">';
		    if (!$select_attr) {
		        $data .= '<div class="row">';
		            $data .= '<div class="col-sm-12 text-right buttonWrapper">';
		                $data .= '<button type="button" class="btn btn-primary addMoreSkill"><i class="fa fa-lg fa-plus"></i> add skill</button> &nbsp;';
		                $data .= '<button class="btn btn-success SFIASubmitBtn" type="submit" disabled><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
		            $data .= '</div>';
		        $data .= '</div>';
		    }
        $data .= '</div> </div>';
    $data .= '</form>';
    return $data;
}
function advisory_sfia_get_team_user_summary($post_id, $user_id, $select_attr) {
	$data = '';
	if ($select_attr == '') {
		$default = advisory_form_default_values($post_id, $user_id.'_pdf');
		$summary = !empty($default['summary']) ? $default['summary'] : '';
	    $data .= '<form class="sfia-form single" method="post" data-meta="'.$user_id.'_pdf" data-id="'.$post_id.'">';
	        $data .= '<div class="card">';
				$data .= '<div class="card-title-w-btn"> <h4 class="title">Summary</h4> </div>';
		        $data .= '<div class="card-body">';
					// $data .= '<h3 class="mb-10">Summary</h3>';
					$data .= '<textarea name="summary" class="tinymce-editor">'.$summary.'</textarea>';
		        $data .= '</div>';
				$data .= '<div class="row mt-10">';
					$data .= '<div class="col-sm-12 text-right">';
						$data .= '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
					$data .= '</div>';
				$data .= '</div>';
	        $data .= '</div>';
	    $data .= '</form>';
	}
	return $data;
}
function advisory_sfia_get_active_post_id($company_id=0) {
	$postType = 'sfia';
	if (!$company_id) $company_id = advisory_get_user_company_id();
	if (advisory_metrics_in_progress($company_id, [$postType])) {
	    $form_id = advisory_get_active_forms($company_id, [$postType]);
	} else {
	    $id = new WP_Query([
	        'post_type' => $postType,
	        'post_status' => 'archived',
	        'posts_per_page' => 1,
	        'meta_query' => [['key' => 'assigned_company', 'value' => $company_id]],
	        'fields' => 'ids',
	    ]);
	    if ($id->found_posts > 0) $form_id = $id->posts;
	}
	if (!empty($form_id[0])) return $form_id[0];
	return false;
}
function advisory_sfia_get_company_users($company_id=0) {
	if (!$company_id) $company_id = advisory_get_user_company_id();
	$sfiaUsers = get_term_meta( $company_id, 'company_data', true );
	return !empty($sfiaUsers['sfia_users']) ? advisory_select_array($sfiaUsers['sfia_users']) : [];
}
function advisory_sfia_get_team_users_string_array($str) {
	if (!empty($str)) {
		$arr = [];
		$str = explode(', ', $str);
		if (!empty($str)) {
			foreach ($str as $value) {
				$tmp = explode(':', $value);
				$arr[$tmp[0]] = $tmp[1];
			}
		}
		return $arr;
	}
	return $str;
}
add_action('wp_ajax_validate_form_submission_sfia', 'ajax_validate_form_submission_sfia');
function ajax_validate_form_submission_sfia() {
	check_ajax_referer('advisory_nonce', 'security');
	if (advisory_sfia_is_user_valid_for_publishing($_REQUEST['post_id'], $_REQUEST['company_id'], $_REQUEST['user_id'])) wp_send_json( true );
	wp_send_json(false);
}
add_action('wp_ajax_validate_form_submission_sfia_assessment', 'ajax_validate_form_submission_sfia_assessment');
function ajax_validate_form_submission_sfia_assessment() {
	check_ajax_referer('advisory_nonce', 'security');
	$post_id = !empty($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false;
	$company_id = !empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : false;
	$company = get_term_meta($company_id, 'company_data', true);
	$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];

	if (!empty($users) && $post_id && $company_id) {
		$active_users = get_post_meta( $post_id, 'active_users', true );
		if (!empty($active_users)) {
			foreach ($active_users as $teams) {
				if (!empty($teams)) {
					// CHECK THE VALIDATION
					foreach ($teams as $user_id => $user_name) {
						if ($users[$user_id] && !advisory_sfia_is_user_valid_for_publishing($post_id, $company_id, $user_id)) wp_send_json([$user_id=>$user_name]);
					}
				}
			}
		}
		wp_send_json( true );
	}
	wp_send_json(false);
}
add_action('wp_ajax_sfia_publish_user', 'ajax_sfia_publish_user');
function ajax_sfia_publish_user() {
	check_ajax_referer('advisory_nonce', 'security');
	$publish_assessment = false;
	$post_id 	= !empty($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false;
	$company_id = !empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : false;
	$team_id 	= !empty($_REQUEST['team_id']) ? $_REQUEST['team_id'] : false;
	$user_id 	= !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : false;


	if ($status = advisory_sfia_publish_user($post_id, $company_id, $team_id, $user_id)) {
		$active_users = get_post_meta( $post_id, 'active_users', true); 
		if (advisory_sfia_is_item_empty($active_users)) {
			advisory_sfia_publish_assessment($post_id);
		}
		wp_send_json( $status );
	}
	wp_send_json(false);
}
add_action('wp_ajax_sfia_publish_assessment', 'ajax_sfia_publish_assessment');
function ajax_sfia_publish_assessment() {
	$post_id = !empty($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false;
	$company_id = !empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : false;
	if ($post_id && $company_id) {
		$active_users = get_post_meta( $post_id, 'active_users', true );
		if (!empty($active_users)) {
			foreach ($active_users as $team_id => $team) {
				if (!empty($team)) {
					foreach ($team as $user_id => $user_name) {
						if (!advisory_sfia_publish_user($post_id, $company_id, $team_id, $user_id)) wp_send_json([$user_id => $user_name]);
					}
				}
			}
		}
		if (!advisory_sfia_publish_assessment($post_id)) wp_send_json($post_id);
		wp_send_json( true );
	}
	wp_send_json(false);
}
function advisory_sfia_publish_assessment($post_id) {
	advisory_sfia_add_assessment_publish_date($post_id);
	$update = wp_update_post(['ID' => $post_id, 'post_status' => 'archived']);
	if (is_wp_error($update)) return false;
	return true;
}
function advisory_sfia_add_assessment_publish_date($post_id) {
	if (metadata_exists('post', $post_id, 'archive_date' )) delete_post_meta( $post_id, 'archive_date');
	return add_post_meta( $post_id, 'archive_date', time(), false );
}
function advisory_sfia_add_user_publish_date($post_id, $user_id) {
	$meta_key = $user_id.'_archive_date';
	if (metadata_exists('post', $post_id, $meta_key )) delete_post_meta( $post_id, $meta_key);
	return add_post_meta( $post_id, $meta_key, time(), false );
}
function advisory_sfia_publish_user($post_id, $company_id, $team_id, $user_id) {
	if ($post_id && $company_id && $team_id && $user_id) {
		$head_id = $user_id.'_head';
		$active_users = get_post_meta( $post_id, 'active_users', true);
		$published_users = get_post_meta( $post_id, 'published_users', true);
		$published_users = !empty($published_users) ? $published_users : [];
		// return [$post_id, $company_id];
		if (!empty($active_users[$team_id][$user_id])) {
			$head = advisory_form_default_values( $post_id, $head_id);
			// ATTACH THE LATEST POLES ID TO ASSESSMENT
			$sfiats_id = advisory_sfiats_get_last_assessments($user_id);
			if ( $sfiats_id && @$head['sfiats_id'] != $sfiats_id ) {
				$head['sfiats_id'] = $sfiats_id;
				update_post_meta( $post_id, $head_id, http_build_query($head));
			}

			try {
				advisory_sfia_save_latest_technical_survey_id($post_id, $company_id, $team_id, $user_id);
				advisory_create_sfiar_for($post_id, $company_id); // CREATE THE REGISTER
				$published_users[$team_id][$user_id] = $active_users[$team_id][$user_id];
				unset($active_users[$team_id][$user_id]);
				update_post_meta($post_id, 'active_users', $active_users);
				if (metadata_exists('post', $post_id, 'published_users')) update_post_meta($post_id, 'published_users', $published_users);
				else add_post_meta( $post_id, 'published_users', $published_users, false );
				advisory_sfia_add_user_publish_date($post_id, $user_id);

				advisory_sfia_add_assessment_publish_date($post_id);
				// advisory_sfiats_publish_for_user($user_id, $company_id); // PUBLISH TECHNICAL SURVEY OLD
				advisory_publish_sfiats(@$head['sfiats_id']); // PUBLISH TECHNICAL SURVEY
				return true;
			} catch (Exception $e) {
				return false;
			}
		}
	}
	return false;
}
function advisory_sfia_save_latest_technical_survey_id($post_id, $company_id, $team_id, $user_id){

}
function advisory_sfia_get_company_available_users($company, $post_id) {
	$teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
	$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
	$active_users = get_post_meta( $post_id, 'active_users', true);
	$published_users = get_post_meta( $post_id, 'published_users', true);

    // REMOVE ACTIVE USERS
	if (!empty($active_users)) {
		foreach ($active_users as $team_users) {
			if (!empty($team_users)) {
				foreach ($team_users as $user_id => $user_name) {
					if (isset($users[$user_id])) unset($users[$user_id]);
				}
			}
		}
	}
    // REMOVE PUBLISHED USERS
	if (!empty($published_users)) {
		foreach ($published_users as $published_team_users) {
			if (!empty($published_team_users)) {
				foreach ($published_team_users as $user_id => $user_name) {
					if (isset($users[$user_id])) unset($users[$user_id]);
				}
			}
		}
	}
	return $users;
}
add_action('wp_ajax_sfia_get_team_users_table', 'ajax_sfia_get_team_users_table');
function ajax_sfia_get_team_users_table() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$company_id = !empty($_POST['company_id']) ? $_POST['company_id'] : false;
	$team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : false;
	$select_attr = !empty($_POST['select_attr']) ? $_POST['select_attr'] : '';
	$active_users = get_post_meta( $post_id, 'active_users', true);
	$active_users = !empty($active_users[$team_id]) ? $active_users[$team_id] : [];
	$published_users = get_post_meta( $post_id, 'published_users', true);
	$published_users = !empty($published_users[$team_id]) ? $published_users[$team_id] : [];

	$company = get_term_meta($company_id, 'company_data', true);
	$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
	// $data .= '<br><pre>'. print_r($users, true) .'</pre>';
	// $data .= '<br><pre>'. print_r(advisory_select_array($company['sfia_roles']), true) .'</pre>';

	if ( !empty($users) && (!empty($active_users) || !empty($published_users)) ) {
		$data .= '<table class="table table-bordered table-hover m-0">';
		    $data .= '<tr>';
			    $data .= '<th>Name</th>';
			    if (!$select_attr) $data .= '<th style="width: 100px;">Action</th>';
		    $data .= '</tr>';
		    if (!empty($active_users)) {
				foreach ($active_users as $user_id => $user_name) {
					if (!empty($users[$user_id])) {
				    	$data .= '<tr>';
					    	$data .= '<td>'.$users[$user_id].' <span class="badge bg-deepgreen">A</span></td>';
					    	if (!$select_attr) $data .= '<td><button type="button" class="btn btn-sm btn-warning btnEditTeamUser" post_id='.$post_id.' team_id='.$team_id.' user_id='.$user_id.' user_name="'.$users[$user_id].'" user_status="active_users"> <span class="fa fa-edit"></span> Edit Role</button></td>';
				    	$data .= '</tr>';
					}
				}
		    }
		    if (!empty($published_users)) {
				foreach ($published_users as $user_id => $user_name) {
					if (!empty($users[$user_id])) {
			    		$data .= '<tr>';
			    			$data .= '<td>'.$users[$user_id].'</td>';
			    			if (!$select_attr) $data .= '<td><button type="button" class="btn btn-sm btn-warning btnEditTeamUser" post_id='.$post_id.' team_id='.$team_id.' user_id='.$user_id.' user_name="'.$users[$user_id].'" user_status="published_users"> <span class="fa fa-edit"></span> Edit Role</button></td>';
			    		$data .= '</tr>';
					}
				}
		    }
		$data .= '</table>';
	} else {
		$data .= '<h3 class="text-center text-danger"> No user found</h3>';
	}
	wp_send_json( $data );
}
add_action('wp_ajax_sfia_get_edit_team_users_form', 'sfia_get_edit_team_users_form');
function sfia_get_edit_team_users_form() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$company_id = !empty($_POST['company_id']) ? $_POST['company_id'] : false;
	$team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	$company = get_term_meta($company_id, 'company_data', true);

	$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
	$active_users = !empty($company['sfia_active_users'][$team_id]) ? $company['sfia_active_users'][$team_id] : [];
	if (!empty($active_users) && !empty($users)) {
		$data .= '<table class="table table-bordered table-hover m-0">';
		    $data .= '<tr><th>Name</th><th style="width: 100px;">Action</th></tr>';
			foreach ($active_users as $user_id) {
				if (!empty($users[$user_id])) {
		    	$data .= '<tr><td>'.$users[$user_id].'</td><td><button type="button" class="btn btn-sm btn-warning btnEditTeamUser" team_id='.$team_id.' user_id='.$user_id.' user_name="'.$users[$user_id].'"> <span class="fa fa-edit"></span> Edit Role</button></td></tr>';
				}
			}
		$data .= '</table>';
	} else {
		$data .= '<h3 class="text-center text-danger"> No user found</h3>';
	}
	wp_send_json( $data );
}
add_action('wp_ajax_sfia_reset_assessment', 'ajax_sfia_reset_assessment');
function ajax_sfia_reset_assessment() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$active_users = get_post_meta( $post_id, 'active_users', true );
	$published_users = get_post_meta( $post_id, 'published_users', true );

	if ( metadata_exists('post', $post_id, 'active_users') ) delete_post_meta( $post_id, 'active_users');
	if ( metadata_exists('post', $post_id, 'published_users') ) delete_post_meta( $post_id, 'published_users');
	if ( metadata_exists('post', $post_id, 'archive_date') ) delete_post_meta( $post_id, 'archive_date');
	
	wp_send_json(true);
}
add_action('wp_ajax_sfia_dashboard_scorecard', 'sfia_dashboard_scorecard');
function sfia_dashboard_scorecard() {
	check_ajax_referer('advisory_nonce', 'security');
	$type = !empty($_POST['type']) ? $_POST['type'] : false;
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : false;
	$team_name = !empty($_POST['team_name']) ? $_POST['team_name'] : false;
	if ($type == 'skillgap') echo advisory_sfia_skillgap_scorecard($post_id, $team_id, $team_name);
	else echo advisory_sfia_dashboard_scorecard($post_id, $team_id);
	wp_die();
}
function advisory_sfia_dashboard_scorecard($post_id=0, $team=1) {
	$html = '';
	if ($post_id && $team) {
		$user_company_id = advisory_get_user_company_id();
		if (!$post_id) $post_id = advisory_sfia_get_active_post_id($user_company_id);
		$opts = get_post_meta($post_id, 'form_opts', true);
		$company = get_term_meta($user_company_id, 'company_data', true);
		$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
		$teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
		$roles = !empty($company['sfia_roles']) ? advisory_select_array($company['sfia_roles']) : [];
		$published_users = get_post_meta( $post_id, 'published_users', true);
		// $html .= '<br><pre>'. print_r($opts, true) .'</pre>';

		if (!empty($published_users) && !empty($teams)) {
			$html .= '<div class="text-center"><img src="'.IMAGE_DIR_URL.'sfia/dashboard/value_scorecard.jpg" class="img-responsive"> </div> <br>';
			$html .= '<div class="table-responsive">';
			foreach ($published_users as $team_id => $team_users) {
				if ($team_id != $team ) continue;
				$html .= '<table class="table table-bordered m-0" style="border-bottom: none;">';
					$html .= '<tbody>';
						$html .= '<tr><th class="t-heading-gray bold" colspan="100" style="border-bottom: none; font-size:19px">TEAM/GROUP : '.$teams[$team_id].'</th></tr>';
					$html .= '</tbody>';
				$html .= '</table>';
				$html .= '<div class="accordion" id="accordionSFIA">';
				if (!empty($team_users)) {
					foreach ($team_users as $user_id => $user_name) {
						if (empty($users[$user_id])) continue;
						// $head = get_post_meta( $post_id, $user_id.'_head', true);
						$head = advisory_form_default_values( $post_id, $user_id.'_head', true);
						$skills = advisory_form_default_values( $post_id, $user_id.'_skills');
						$avg = ['value'=>'N/A', 'text'=>'N/A', 'cls' => advisory_get_sfia_skills_fit_class('N/A')];
						if (isset($skills['avg'])) {
							$avg = ['value'=>$skills['avg'], 'text'=> number_format($skills['avg']).'%', 'cls' => advisory_get_sfia_skills_fit_class($skills['avg'])];
							unset($skills['avg']);
						}
						$loop = !empty($skills) ? count($skills) / 6 : 0;
						// TECHNICAL SCORE
						$default = advisory_form_default_values(@$head['sfiats_id'], 'poles');
						if (!empty($default['avg'])) {
							$sfiats['cls'] = advisory_get_sfia_skills_fit_class($default['avg']);
							$sfiats['text'] = number_format($default['avg']).'%';
						} else {
							$sfiats['cls'] = advisory_get_sfia_skills_fit_class('N/A');
							$sfiats['text'] = 'N/A';
						}
						// $html .= '<tr><td colspan="100"><br><pre>'. print_r($skills, true) .'</pre></td></tr>';
						$html .= '<table class="table table-bordered m-0" data-toggle="collapse" data-target="#collapse_'.$user_id.'" aria-expanded="true" aria-controls="collapse_'.$user_id.'">';
							$html .= '<tbody>';
								$html .= '<tr>';
									$html .= '<th class="t-heading-dark pr-0 h4" style="width:70px;border-bottom: none;cursor: ns-resize;">ID: '.$user_id.'</th>';
									$html .= '<th class="t-heading-dark pr-0 h4" style="border-bottom: none;cursor: ns-resize;">ROLE/TITLE: <br>'.$roles[$head['role']].'</th>';
									$html .= '<th class="t-heading-dark pl-0 h4 text-right" style="border: none;width: 102px;cursor: ns-resize;">TECHNICAL SCORE</th>';
									$html .= '<th class="h4 text-center p-0 '.$sfiats['cls'].'" style="border: none;width: 46px;cursor: ns-resize;">'.$sfiats['text'].'</th>';
									$html .= '<th class="t-heading-dark pl-0 h4 text-right" style="border: none;width: 65px;cursor: ns-resize;">SKILLS FIT %</th>';
									$html .= '<th class="h4 text-center p-0 '.$avg['cls'].'" style="border: none;width: 46px;cursor: ns-resize;">'.$avg['text'].'</th>';
									$html .= '<th class="text-center bg-black p-0" style="border: none;width:72px;cursor: ns-resize;"><a href="'. site_url('pdf') .'?pid='. $post_id .'&user_id='.$user_id.'" style="margin:0 5px;" title="Assessment Summary" target="_blank"><img src="'.IMAGE_DIR_URL.'pdf/power_inverse.png" style="width:35px;"></a></th>';
								$html .= '</tr>';
							$html .= '</tbody>';
						$html .= '</table>';
						$html .= '<div id="collapse_'.$user_id.'" class="collapse" aria-labelledby="heading_'.$user_id.'" data-parent="#accordionSFIA">';
							$html .= '<table class="table table-bordered m-1">';
								$html .= '<tbody>';
									$html .= '<tr>';
										$html .= '<th class="t-heading-sky" colspan="68" style="width:68%">REQUIRED SKILL</th>';
										$html .= '<th class="t-heading-sky" colspan="5" style="width:5%">CODE</th>';
										$html .= '<th class="t-heading-sky" colspan="12" style="width:12%">TARGET</th>';
										$html .= '<th class="t-heading-sky" colspan="12" style="width:12%">ASSESSED</th>';
									$html .= '</tr>';
									for ($counter=0; $counter < $loop; $counter++) { 
										$skill_class = advisory_get_sfiar_category_class($skills['category_'. $counter]);
										$code = advisory_sfia_dashboard_scorecard_get_skill_colde($opts, $skills, $counter);
										$class = advisory_get_sfia_access_level_container_class($skills['assess_level_'. $counter], $skills['target_level_'. $counter]);
										$level_text = advisory_sfia_get_skill_level_text($skills['assess_level_'. $counter]); 
										$html .= '<tr>';
											$html .= '<th class="'.$skill_class.'" colspan="68">'.advisory_string_from_id(@$skills['skill_'. $counter]).'</th>';
											$html .= '<th class="'.$skill_class.'" colspan="5">'.$code.'</th>';
											$html .= '<th class="bg-deepgreen" colspan="12"> Level '.@$skills['target_level_'. $counter].'</th>';
											$html .= '<th class="'.$class.'" colspan="12"> '.$level_text .'</th>';
										$html .= '</tr>';
					    			}
					    		$html .= '<tbody>';
			    			$html .= '</table>';
			    		$html .= '</div>';
					}
				}
				$html .= '</div>';
			}
			$html .= '</div>';
		}
	}
	return $html;
}
function advisory_sfia_get_skill_level_text($level) {
	if ($level == '') return 'Evaluation';
	else if ($level == 0) return 'Missing';
	else return 'Level '. $level;
}
function advisory_sfia_dashboard_scorecard_get_skill_colde($opts, $skills, $counter) {
	$table_id = 'sections_'.$skills['category_'.$counter].'_tables_'.$skills['subcategory_'.$counter].'';
	if (!empty($opts[$table_id])) {
		foreach ($opts[$table_id] as $opts_skill) {
			if (advisory_id_from_string($opts_skill['name']) == $skills['skill_'.$counter]) {
				return !empty($opts_skill['code']) ? $opts_skill['code'] : false;
			}
		}
	}
	return false;
}
function advisory_sfia_skillgap_scorecard($post_id, $team_id, $team_name) {
	$html = '';
	if ($post_id && $team_id && $team_name) {
		$user_company_id = advisory_get_user_company_id();
		$opts = get_post_meta($post_id, 'form_opts', true);
		$company = get_term_meta($user_company_id, 'company_data', true);
		$teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
		$roles = !empty($company['sfia_roles']) ? advisory_select_array($company['sfia_roles']) : [];
		$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
		$skills = advisory_sfia_skillgap_scorecard_skills($post_id, $team_id);

		// $html .= '<br><pre>'. print_r($skills, true) .'</pre>';
		$html .= '<div class="text-center"><img src="'.IMAGE_DIR_URL.'sfia/dashboard/scorecard.png" class="img-responsive" style="width: 80%; margin: 25px auto 20px auto;"> </div> <br>';
		$html .= '<div class="table-responsive">';
			$html .= '<table class="table table-bordered m-0" style="border-bottom: none;">';
				$html .= '<tbody>';
					$html .= '<tr><th class="t-heading-gray bold" colspan="100" style="border-bottom: none; font-size:19px">TEAM/GROUP : '.$team_name.'</th></tr>';
				$html .= '</tbody>';
			$html .= '</table>';
		$html .= '</div>';

		if (!empty($skills) && !empty($roles)) {
			$html .= '<div class="accordion" id="accordionSkillGap">';
				foreach ($skills as $role_id => $skill_array) {
					// $html .= '<br><pre>'. print_r($skill_array, true) .'</pre>';
					$html .= '<table class="table table-bordered m-0" style="border-bottom: none;cursor: ns-resize;" data-toggle="collapse" data-target="#collapse_'.$role_id.'" aria-expanded="true" aria-controls="collapse_'.$role_id.'">';
						$html .= '<tbody>';
							$html .= '<tr><th class="t-heading-dark h4" style="border-bottom: none;">ROLE/TITLE : <br>'.$roles[$role_id].'</th></tr>';
						$html .= '</tbody>';
					$html .= '</table>';
					$html .= '<div id="collapse_'.$role_id.'" class="collapse" aria-labelledby="heading_'.$role_id.'" data-parent="#accordionSkillGap">';
						$html .= '<table class="table table-bordered m-1">';
							$html .= '<tbody>';
								$html .= '<tr>';
									$html .= '<th class="t-heading-sky" style="">REQUIRED SKILL</th>';
									$html .= '<th class="t-heading-sky" style="width:62px;;">CODE</th>';
									$html .= '<th class="t-heading-sky" style="width:78px;;">TARGET</th>';
									$html .= '<th class="t-heading-sky" style="width:104px;;">SKILL FIT %</th>';
								$html .= '</tr>';
								if (!empty($skill_array)) {
									foreach ($skill_array as $skill_id => $skill) {
										$score = !empty($skill['values']) ? (array_sum($skill['values']) / count($skill['values'])) * 100 : 0;
										$class = advisory_get_sfia_skills_fit_class($score);

										// $html .= '<tr><td colspan="4"><pre>'. print_r($skill, true) .'</pre></td></tr>';
										$html .= '<tr>';
											$html .= '<th class="'.@$skill['class'].'" style="">'.advisory_string_from_id($skill_id).'</th>';
											$html .= '<th class="'.@$skill['class'].'" style="text-align:center;">'.@$skill['code'].'</th>';
											$html .= '<th class="bg-deepgreen" style="text-align:center;">Level '.@$skill['target'].'</th>';
											$html .= '<th class="'.$class.'" style="text-align:center;">'.$score.'%</th>';
										$html .= '</tr>';
									}
								}
							$html .= '</tbody>';
						$html .= '</table>';
					$html .= '</div>';
				}
			$html .= '</div>';
		}
	}
	return $html; 
}
function advisory_sfia_skillgap_scorecard_skills($post_id, $team_id) {
	$data = [];
	// $post_id = 831;
	// $published_users = get_post_meta( $post_id, 'active_users', true);
	$opts = get_post_meta($post_id, 'form_opts', true);
	$published_users = get_post_meta( $post_id, 'published_users', true);
	$team_users = !empty($published_users[$team_id]) ? $published_users[$team_id] : false;
	if ($team_users) {
		foreach ($team_users as $user_id => $user_name) {
			$head = advisory_form_default_values($post_id, $user_id.'_head');
			$skills = advisory_form_default_values($post_id, $user_id.'_skills');
			if (isset($skills['avg'])) unset($skills['avg']);

			$loop = !empty($skills) ? count($skills) / 6 : 0;
			if (!empty($head['role']) && !empty($skills)) {
				for ($counter=0; $counter < $loop; $counter++) { 
					$skill_value = advisory_sfia_calculate_level_scores($skills['assess_level_'. $counter], $skills['target_level_'. $counter]);
					$code = advisory_sfia_dashboard_scorecard_get_skill_colde($opts, $skills, $counter);
					$skill_class = advisory_get_sfiar_category_class($skills['category_'. $counter]);
					$data[$head['role']][$skills['skill_'.$counter]]['id'] = $skills['skill_'.$counter];
					$data[$head['role']][$skills['skill_'.$counter]]['code'] = $code;
					$data[$head['role']][$skills['skill_'.$counter]]['class'] = $skill_class;
					$data[$head['role']][$skills['skill_'.$counter]]['target'] = $skills['target_level_'. $counter];
					$data[$head['role']][$skills['skill_'.$counter]]['values'][$user_id] = $skill_value;
    			}
			}
		}
	}
	return $data;
}
function advisory_sfia_is_user_valid_for_publishing($post_id, $company_id, $user_id) {
	if ($post_id && $user_id) {
		$head = advisory_form_default_values($post_id, $user_id.'_head');
		$skills = advisory_form_default_values($post_id, $user_id.'_skills');
		$sfiats = advisory_sfiats_is_valid_publishing_for_user($user_id, $company_id);
		if ($sfiats && !empty($head['level']) && !empty($head['role']) && !empty($skills)) {
			// CHECK IF ANY SKILL LEVEL VALUES ARE MISSING
			if (isset($skills['avg'])) unset($skills['avg']);
			$loop = !empty($skills) ? count($skills) / 6 : 0;
			for ($i=0; $i < $loop; $i++) { 
				if ( $skills['rank_'.$i] == 'core' && ($skills['target_level_'.$i] == '' || $skills['assess_level_'.$i] == '') ) {
					return false;
				}
			}
			return true;
		}
	}
	return false;
}
function advisory_sfia_get_skills_history($user_id, $company_id, $default=[], $exclude=0) {
	$data = '';
	if (!$company_id) $company_id = advisory_get_user_company_id();
	$postType = 'sfia';
	$args = [
        'post_type' => $postType,
        'post_status' => ['publish','archived'],
        'posts_per_page' => -1,
        'meta_query' => [ ['key' => 'assigned_company', 'value' => $company_id] ],
    ];
    if ($exclude) $args['post__not_in'] = [$exclude];
    $posts = new WP_Query($args);
	// $data .= '<br><pre>'. print_r($args, true) .'</pre>';
	$data .= '<select class="form-control bg-gap sfia_assessment" user_id="'.$user_id.'">';
        $data .= '<option value="">SFIA Assessments</option>';
        if (!empty($posts->posts)) {
            foreach ($posts->posts as $post) {
            	$published_users = get_post_meta( $post->ID, 'published_users', true);
            	if (advisory_sfia_is_user_has_published_assessment($post->ID, $user_id)) {
	            	$selected = !empty($default['sfia']) && $default['sfia'] == $post->ID ? ' selected' : '';
	                $data .= '<option value="'.$post->ID.'"'.$selected.'>'.$post->post_title.'</option>';
            	}
            }
        }
    $data .= '</select>';
	return $data;
}
function advisory_sfia_is_user_has_published_assessment($post_id, $user_id) {
	$published_users = get_post_meta( $post_id, 'published_users', true);
	if (!empty($published_users)) {	
		foreach ($published_users as $team_id => $team_users) {
			if (array_key_exists($user_id, $team_users)) return true;
		}
	}
	return false;
}
function advisory_sfia_get_permission_level($user_id=null) {
	global $user_switching;
	if (!empty($user_switching->get_old_user())) return 'full';
	if (empty($user_id)) { $user = wp_get_current_user(); $user_id = $user->ID; }
	if (!empty($user_id) && get_the_author_meta('spuser', $user_id) ) return 'full';
	else if (!empty($user_id) && get_the_author_meta('specialsfiauser', $user_id) ) return 'partial';
	return false;
}
function advisory_sfia_get_select_attr($user_id=null) {
	$permission = advisory_sfia_get_permission_level($user_id);
	if ($permission == 'full') return true;
	else return false;
}
function advisory_get_sfia_pdf_data($post_id, $user_id) {
	$data = [];
	// $data['customer_name'] = wp_get_current_user()->display_name;
	if ($post_id && $user_id) {
		$company = advisory_get_user_company();
		if (!empty($company)) {
			$opts = get_post_meta($post_id, 'form_opts', true);
			$companyData = get_term_meta($company->term_id, 'company_data', true);
			$data['company'] = $company->name;
			$users = !empty($companyData['sfia_users']) ? advisory_select_array($companyData['sfia_users']) : [];
			$roles = !empty($companyData['sfia_roles']) ? advisory_select_array($companyData['sfia_roles']) : [];

			$head = advisory_form_default_values($post_id, $user_id.'_head');
			// return $head;
			$skills = advisory_form_default_values($post_id, $user_id.'_skills');
			
			$data = $data + $head;
			$data['id'] = $user_id; 
			$data['name'] = !empty($users[$user_id]) ? $users[$user_id] : false;
			$data['role'] = !empty($roles[$head['role']]) ? $roles[$head['role']] : false;
			if (isset($skills['avg'])) { 
				$data['sfia']['text'] = number_format($skills['avg'],0) .'%'; 
				$data['sfia']['class'] = advisory_get_sfia_skills_fit_class($skills['avg']); 
				// $data['sfia']['class'] = str_replace('bg', 'bgimg', advisory_get_sfia_skills_fit_class($skills['avg'])); 
				unset($skills['avg']); 
			}
			$default = advisory_form_default_values($post_id, $user_id.'_pdf');
			$data['sfia']['summary'] = !empty($default['summary']) ? $default['summary'] : '';

			$sfiats = advisory_form_default_values(@$head['sfiats_id'], 'poles');
			if (!empty($sfiats['avg'])) {
				$data['sfiats']['text'] = number_format($sfiats['avg']).'%';
				$data['sfiats']['class'] = advisory_get_sfia_skills_fit_class($sfiats['avg']);
				// $data['sfiats']['class'] = str_replace('bg', 'bgimg', advisory_get_sfia_skills_fit_class($sfiats['avg']));
			} else {
				$data['sfiats']['text'] = 'N/A';
				$data['sfiats']['class'] = advisory_get_sfia_skills_fit_class($sfiats['avg']);
				// $data['sfiats']['class'] = str_replace('bg', 'bgimg', advisory_get_sfia_skills_fit_class('N/A'));
			}
			$default = advisory_form_default_values(@$head['sfiats_id'], 'pdf');
			$data['sfiats']['summary'] = !empty($default['summary']) ? $default['summary'] : '';

			$loop = !empty($skills) ? count($skills) / 6 : 0;
			if (!empty($skills)) {
				for ($counter=0; $counter < $loop; $counter++) { 
					$data['skills'][$counter]['title'] = advisory_string_from_id($skills['skill_'.$counter]);
					$data['skills'][$counter]['code'] = advisory_sfia_dashboard_scorecard_get_skill_colde($opts, $skills, $counter);
					$data['skills'][$counter]['tar'] = 'Level '. $skills['target_level_'. $counter];
					$data['skills'][$counter]['eval'] = advisory_sfia_get_skill_level_text($skills['assess_level_'. $counter]);
					$data['skills'][$counter]['evalcls'] = advisory_get_sfia_access_level_container_class($skills['assess_level_'. $counter], $skills['target_level_'. $counter]);
					$data['skills'][$counter]['catcls'] = advisory_get_sfiar_category_class($skills['category_'.$counter]);
    			}
			}
		}
	}
	return $data;
}
function advisory_sfia_is_item_empty($item) {
	if (!empty($item)) {
		foreach ($item as $team) {
			if (!empty($team)) return false;
		}
	}
	return true;
}