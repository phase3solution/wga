<?php
function advisory_sfiar_get_menu_items($post_id, $company_id=0) {
	global $sfia_premission;
	$data = '';
	if ($sfia_premission) {
		// $form_meta = get_post_meta($post_id, 'form_opts', true);
	    $company = get_term_meta($company_id, 'company_data', true);
	    $teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
	    if (!empty($teams)) {
	        $data .= '<li><a href="javascript:;"><img src="'.IMAGE_DIR_URL.'sfia/register.png"><span>' . advisory_get_form_name($post_id) . '</span></a>';
	            if (!empty($teams)) {
	                $data .= '<ul class="treeview-menu">';
	                foreach ($teams as $team_id => $team_name) {
	                    $data .= '<li><a href="'.home_url('sfia-register/').'?team='.$team_id.'"><img src="'.IMAGE_DIR_URL.'sfia/sub_register.png"><span>'.$team_name.'</span></a></li>';
	                }
	                $data .= '</ul>';
	            }
	        $data .= '</li>';
	    }
	}
    return $data;
}
function advisory_sfiar_get_active_post_id($company_id=0) {
	$postType = 'sfiar';
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
function advisory_create_sfiar_for($post_id, $company_id=null) {
	if ($company_id) {
		$company = get_term($company_id, 'company');
		if (!empty($company)) {
			$post_title = $company->name;
			$display_name = 'SFIA Register';
			$desc = $company->description;
			if (!post_exists( $post_title,'','','sfiar')) {
				$opts = get_post_meta( $post_id, 'form_opts', true );
				$post_id = wp_insert_post(array (
				    'post_type' => 'sfiar',
				    'post_title' => $post_title,
				    'post_content' => '',
				    'post_status' => 'publish',
				    'comment_status' => 'closed',
				));
				if ($post_id) {
					$opts = ['display_name' => $display_name, 'desc' => $desc];
					add_post_meta( $post_id, 'form_opts', $opts, false );
					add_post_meta( $post_id, 'permission', ['users' => $company_id], false );
					add_post_meta( $post_id, 'assigned_company', $company_id, false );
					return $post_id;
				}
			}
		}
	}
	return false;
}
add_action('wp_ajax_sfiar_get_user_data', 'sfiar_get_user_data');
function sfiar_get_user_data() {
	$data = ['user_info'=>'', 'skills_form'=>''];
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$sfia_id = !empty($_POST['sfia_id']) ? $_POST['sfia_id'] : false;
	$company_id = !empty($_POST['company_id']) ? $_POST['company_id'] : false;
	$team_id = !empty($_POST['team_id']) ? $_POST['team_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	$user_name = !empty($_POST['user_name']) ? $_POST['user_name'] : false;

	if ($post_id && $sfia_id && $company_id && $team_id && $user_id && $user_name) {
		$permission = advisory_sfia_get_permission_level();
		$select_attr = $permission && $permission == 'full' ? '' : 'disabled';
		$disabled = $permission && ($permission == 'full' || $permission == 'partial') ? '' : 'disabled';

		$default = advisory_form_default_values($post_id, $user_id.'_register');
		// $default = get_post_meta( $post_id, '808_register', true);
		$head = advisory_form_default_values( $sfia_id, $user_id.'_head');
		$skills = advisory_form_default_values( $sfia_id, $user_id.'_skills');

		$data['user_info'] = advisory_sfiar_get_user_info($sfia_id, $company_id, $team_id, $user_id, $user_name);
		$data['skills_form'] = advisory_sfiar_get_user_details($post_id, $sfia_id, $company_id, $user_id, $user_name, $default, $select_attr, $disabled);
		$data['archives'] = advisory_sfiar_get_user_archives($post_id, $sfia_id, $company_id, $user_id, $user_name, $select_attr);
		wp_send_json( $data );
	}
	wp_send_json( false );
}
add_action('wp_ajax_sfiar_save', 'advisory_ajax_sfiar_save');
function advisory_ajax_sfiar_save() {
	check_ajax_referer('advisory_nonce', 'security');
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : '';
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : '';
	$meta_key = !empty($_POST['meta']) ? $_POST['meta'] : '';
	$data = !empty($_POST['data']) ? $_POST['data'] : '';
	if ($post_id && $user_id && $meta_key && $data) {
		$archives_meta = $user_id.'_archives';
		if (!metadata_exists('post', $post_id, $archives_meta )) add_post_meta( $post_id, $archives_meta, [], false );
		$archives = get_post_meta( $post_id, $archives_meta, true );
		$archives[time()] = $data;

		if (update_post_meta($post_id, $meta_key, $data) && update_post_meta($post_id, $archives_meta, $archives)) { wp_send_json(true); }
	}
	wp_send_json(false);
}
function advisory_sfiar_get_user_info($sfia_id, $company_id, $team_id, $user_id, $user_name) {
	$company = get_term_meta($company_id, 'company_data', true);
	$head = advisory_form_default_values( $sfia_id, $user_id.'_head');
	$teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
	$roles = !empty($company['sfia_roles']) ? advisory_select_array($company['sfia_roles']) : [];

	$data = '';
	// $data .= '<br><pre>'. print_r($_POST, true) .'</pre>';
	$data .= '<div class="col-sm-3 userIntro name_id">';
		$data .= '<div class="name"><span class="bold">Name:</span> <span>'.$user_name.'</span></div>';
	    $data .= '<div class="userId"><span class="bold">ID:</span> <span>'.$user_id.'</span></div>';
	$data .= '</div>';
		$data .= '<div class="col-sm-8 userIntro team_role">';
		$data .= '<div class="team"><span class="bold">Team/Group:</span> <span>'.$teams[$team_id].'</span></div>';
		$data .= '<div class="role"><span class="bold">Role/Title:</span> <span>'.$roles[$head['role']].'</span></div>';
	$data .= '</div>';
	return $data;
}
function advisory_sfiar_get_user_details($post_id, $sfia_id, $company_id, $user_id, $user_name, $default, $select_attr, $disabled) {
	$additional_data = [];
	$opts = get_post_meta($sfia_id, 'form_opts', true);
	$head = advisory_form_default_values( $sfia_id, $user_id.'_head');
	$skills = advisory_form_default_values( $sfia_id, $user_id.'_skills');
	$published_at = get_post_meta( $sfia_id, $user_id.'_archive_date', true);
	$avg = ['value'=>'N/A', 'text'=>'N/A', 'cls' => advisory_get_sfia_skills_fit_class('N/A')];
	if (isset($skills['avg'])) {
		$avg = ['value'=>$skills['avg'], 'text'=> number_format($skills['avg']).'%', 'cls' => advisory_get_sfia_skills_fit_class($skills['avg'])];
		unset($skills['avg']);
	}

	$sfiats = advisory_form_default_values(@$head['sfiats_id'], 'poles');
	if (!empty($sfiats['avg'])) {
		$sfiats['cls'] = advisory_get_sfia_skills_fit_class($sfiats['avg']);
		$sfiats['text'] = number_format($sfiats['avg']).'%';
	} else {
		$sfiats['cls'] = advisory_get_sfia_skills_fit_class('N/A');
		$sfiats['text'] = 'N/A';
	}
	$published_sfia_select_option = advisory_sfia_get_skills_history($user_id, $company_id, $default_option, $sfia_id);
	$published_sfiats_select_option = advisory_sfiats_get_skills_history($user_id, $company_id, $default);

	$summaryLength = 4;
	$ar_length = 10;
	$ar_id = 'assessments_results'; $ar_summary = !empty($default[$ar_id]) ? ['text'=>$default[$ar_id], 'cls' => 'bg-red'] : ['text' => '', 'cls' => 'bg-green'];
	$ca_id = 'currnt_activities'; 	$ca_summary = !empty($default[$ca_id]) ? ['text'=>$default[$ca_id], 'cls' => 'bg-red'] : ['text' => '', 'cls' => 'bg-green'];
	$pa_id = 'planned_activities'; 	$pa_summary = !empty($default[$pa_id]) ? ['text'=>$default[$pa_id], 'cls' => 'bg-red'] : ['text' => '', 'cls' => 'bg-green'];

	$additional_data['sfia'] = ['id'=>$sfia_id, 'avg' => $avg['value'], 'published_at' => $published_at];
	$additional_data['sfiats']['id'] = !empty($head['sfiats_id']) ? $head['sfiats_id'] : 0;
	$additional_data['sfiats']['avg'] = !empty($sfiats['avg']) ? $sfiats['avg'] : 0;
	$additional_data['skills'] = $skills;

	$data = '';
	// $data .= '<br><pre>'. print_r($additional_data, true) .'</pre>';
    $data .= '<form class="sfiar-form" method="post" data-meta="'.$user_id.'_register" data-id="'.$post_id.'" user_id="'.$user_id.'">';
        $data .= '<input type="hidden" name="extra" value="'.http_build_query($additional_data).'">';
        $data .= '<div class="card">';
            $data .= '<div class="card-body">';
                $data .= '<table class="table table-bordered m-0" style="width:530px;">';
                    $data .= '<thead> <tr> <th colspan="4" class="t-heading-sky">Skills Analysis</th></tr> </thead>';
                    $data .= '<tbody>';
                        $data .= '<tr>';
                            $data .= '<td class="t-heading-dark">Assessment Results</td>';
                            $data .= '<td class="t-heading-dark" style="width: 100px;">Skill Fit %</td>';
                            $data .= '<td class="t-heading-dark" style="width: 100px;">Technical</td>';
                            $data .= '<td class="t-heading-dark" style="width: 152px;">Last Assessment</td>';
                        $data .= '</tr>';
                        $data .= '<tr>';
                        	$data .= '<td class="bigComment pointer '.$ar_summary['cls'].'" isactive="'.$select_attr.'"><textarea name="'. $ar_id .'" class="hidden bigComment_text" excerpt_length='.$ar_length.'>'.htmlentities($ar_summary['text']).'</textarea></td>';
                            $data .= '<td class="text-center '.$avg['cls'].'" style="font-size: 21px;font-weight: 700;">'.$avg['text'].'</td>';
                            $data .= '<td class="text-center '.$sfiats['cls'].'" style="font-size: 21px;font-weight: 700;">'.$sfiats['text'].'</td>';
                            $data .= '<td>'. date('M, j\<\s\u\p\>S\<\/\s\u\p\> Y', $published_at).'</td>';
                            // $data .= '<td class="no-padding"><input class="form-control" style="border:none;font-size:17px;font-weight:500;" name="last_assessment" value="'.@$default['last_assessment'].'"></td>';
                        $data .= '</tr>';
                    $data .= '</tbody>';
                $data .= '</table>';
                // $data .= '<br><pre>'. print_r($skills, true) .'</pre>';
                $data .= advisory_sfiar_get_user_skills($opts, $skills);
                $data .= '<table class="table table-bordered m-0" style="width:1000px;">';
                    $data .= '<thead>';
                        $data .= '<tr> <th class="t-heading-sky" colspan="4">SFIA Roadmap</th></tr>';
                    $data .= '</thead>';
                    $data .= '<tbody>';
                        $data .= '<tr>';
                            $data .= '<td class="t-heading-dark">Current/Completed Activities</td>';
                            $data .= '<td class="t-heading-dark">Planned Activities</td>';
                            $data .= '<td class="t-heading-dark" colspan="2">Skills History</td>';
                        $data .= '</tr>';
                        $data .= '<tr>';
                            $data .= '<td class="bigComment pointer '.$ca_summary['cls'].'" isactive="'.$disabled.'"><textarea name="'. $ca_id .'" class="hidden bigComment_text" excerpt_length='.$summaryLength.'>'. htmlentities($ca_summary['text']).'</textarea></td>';
                            $data .= '<td class="bigComment pointer '.$pa_summary['cls'].'" isactive="'.$disabled.'"><textarea name="'. $pa_id .'" class="hidden bigComment_text" excerpt_length='.$summaryLength.'>'. htmlentities($pa_summary['text']).'</textarea></td>';
                            $data .= '<td class="bg-gap p-0" style="width:300px;">'.$published_sfia_select_option.'</td>';
                            $data .= '<td class="bg-gap p-0" style="width:300px;">'.$published_sfiats_select_option.'</td>';
                        $data .= '</tr>';
                    $data .= '</tbody>';
                $data .= '</table>';
                $data .= '<br>';
                if (!$disabled) $data .= '<div class="clearfix"><button class="btn btn-success pull-right" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button></div>';
            $data .= '</div>';
        $data .= '</div>';
    $data .= '</form>';
	// return '';
	return $data;
}
function advisory_sfiar_get_user_archives($post_id, $sfia_id, $company_id, $user_id, $user_name, $select_attr) {
	$data = '';
	$archives_meta = $user_id.'_archives';
    $archives = get_post_meta( $post_id, $archives_meta, true );
    krsort($archives);
    $data .= '<h2 style="margin-bottom:10px">Archives</h2>';
    if (empty($archives)) $data .= '<p class="text-danger">No archive found!</p>';
    else {
    	$data .= '<table class="table table-bordered table-hover userArchivesTable" style="width: 350px">';
		    $data .= '<thead>';
		        $data .= '<tr>';
		            $data .= '<th class="bold">Date</th>';
		            $data .= '<th class="bold" style="width: 86px">Actions</th>';
		        $data .= '</tr>';
		    $data .= '</thead>';
		    $data .= '<tbody>';
		    	foreach ($archives as $archive_time => $archive) {
		    		// parse_str($archive, $archive);
		    		// parse_str($archive['extra'], $archive['extra']);
			        // $data .= '<tr><td colspan="2"><pre>'. print_r($archive, true) .'</pre></td></tr>';
			        $data .= '<tr id="archive_'.$archive_time.'">';
			            $data .= '<td>'.date('M, j\<\s\u\p\>S\<\/\s\u\p\> Y H:m:s A', $archive_time).'</td>';
			            $data .= '<td class="text-center">';
			            	$data .= '<button class="btn btn-sm btn-primary showArchiveItem" post_id='.$post_id.' sfia_id='.$sfia_id.' company_id='.$company_id.' user_id='.$user_id.' user_name='.$user_name.' archive_time='.$archive_time.'><span class="fa fa-eye"></span></button> ';
			            	if (!$select_attr) $data .= '<button class="btn btn-sm btn-danger deleteArchiveItem" post_id='.$post_id.' user_id='.$user_id.' archive_time='.$archive_time.'><span class="fa fa-trash"></span></button>';
			            $data .= '</td>';
			        $data .= '</tr>';
		    	}
		    $data .= '</tbody>';
		$data .= '</table>';
    }
	return $data;
}
function advisory_sfiar_get_user_skills($opts, $skills) {
	$data = '';
	$loop = !empty($skills) ? count($skills) / 6 : 0;
	$data .= '<table class="table table-bordered m-0">';
        $data .= '<tbody>';
            $data .= '<tr>';
                $data .= '<td class="t-heading-dark">CATEGORY</td>';
                $data .= '<td class="t-heading-dark">SUB-CATEGORY</td>';
                $data .= '<td class="t-heading-dark">SKILL</td>';
                $data .= '<td class="t-heading-dark">CODE</td>';
                $data .= '<td class="t-heading-dark">RANK</td>';
                $data .= '<td class="t-heading-dark">TARGET</td>';
                $data .= '<td class="t-heading-dark">EVALUATION</td>';
            $data .= '</tr>';
			// $data .= '<tr><td colspan="100"><br><pre>'. print_r($skills, true) .'</pre></td></tr>';
            for ($i=0; $i < $loop; $i++) { 
            	$catClass = advisory_get_sfiar_category_class($skills['category_'.$i]);
				$code = advisory_sfia_dashboard_scorecard_get_skill_colde($opts, $skills, $i);
				$class = advisory_get_sfiar_access_level_container_class($skills['assess_level_'. $i], $skills['target_level_'. $i]);
				$rank = !empty($skills['rank_'.$i]) ? advisory_string_from_id($skills['rank_'.$i]) : 'Core';

				// $table_id = 'sections_'.$skills['category_'.$i].'_tables_'.$skills['subcategory_'.$i].'';
				// $data .= '<tr><td colspan="100"><br><pre>'. print_r($skills[$i], true) .'</pre></td></tr>';

                $data .= '<tr>';
                    $data .= '<td class="'.$catClass.'">'. advisory_string_from_id($skills['category_'.$i]) .'</td>';
                    $data .= '<td class="'.$catClass.'">'. advisory_string_from_id($skills['subcategory_'.$i]) .'</td>';
                    $data .= '<td class="'.$catClass.'">'. advisory_string_from_id($skills['skill_'.$i]) .'</td>';
                    $data .= '<td class="'.$catClass.'" style="width: 62px;">'.$code.'</td>';
                    $data .= '<td style="width: 64px;">'. $rank.'</td>';
                    $data .= '<td class="bg-green" style="width: 84px; ">Level '.$skills['target_level_'.$i].'</td>';
                    $data .= '<td class="'.$class.'" style="width: 110px;">Level '.$skills['assess_level_'.$i].'</td>';
                $data .= '</tr>';
			}
        $data .= '</tbody>';
    $data .= '</table>';
	return $data;
}
function advisory_get_sfiar_category_class($value) {
	$cls = '';
	switch ($value) {
		case 'strategy_and_architecture': 		$cls = 'bg-light-red'; 		break;
		case 'change_and_transformation': 		$cls = 'bg-light-pink'; 	break;
		case 'development_and_implementation': 	$cls = 'bg-light-orange';	break;
		case 'delivery_and_operation': 			$cls = 'bg-light-yellow'; 	break;
		case 'skills_and_quality': 				$cls = 'bg-light-blue'; 	break;
		case 'relationships_and_engagement': 	$cls = 'bg-light-green'; 	break;
		default: 								$cls = ''; 					break;
	}
	return $cls;
}
function advisory_get_sfiar_access_level_container_class($assessed_level, $target_level) {
    if ($assessed_level > $target_level) { return 'bg-blue'; }
    else if ($assessed_level == $target_level) { return 'bg-green'; }
    else if ($assessed_level == ($target_level - 1)) { return 'bg-yellow'; }
    else if ($assessed_level == ($target_level - 2)) { return 'bg-orange'; }
    else if ($assessed_level == ($target_level - 3)) { return 'bg-red'; }
    else return 'bg-red';
}
add_action('wp_ajax_sfiar_sfia_assessments_archives', 'ajax_sfiar_sfia_assessments_archives');
function ajax_sfiar_sfia_assessments_archives() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	if ( $post_id && $user_id ) {
		$opts = get_post_meta($post_id, 'form_opts', true);
		$company_id = advisory_get_user_company_id();
		$company = get_term_meta($company_id, 'company_data', true);
		$roles = !empty($company['sfia_roles']) ? advisory_select_array($company['sfia_roles']) : [];
		$head = advisory_form_default_values( $post_id, $user_id.'_head', true );
		$role = !empty($roles[$head['role']]) ? $roles[$head['role']] : '';
		$skills = advisory_form_default_values( $post_id, $user_id.'_skills', true );
		$avg = ['value'=>'N/A', 'text'=>'N/A', 'class' => advisory_get_sfia_skills_fit_class('N/A')];
		if (isset($skills['avg'])) {
			$avg = ['value'=>$skills['avg'], 'text'=> number_format($skills['avg']).'%', 'class' => advisory_get_sfia_skills_fit_class($skills['avg'])];
			unset($skills['avg']);
		}
		$sfiats = advisory_form_default_values(@$head['sfiats_id'], 'poles');
		if (!empty($sfiats['avg'])) {
			$sfiats['class'] = advisory_get_sfia_skills_fit_class($sfiats['avg']);
			$sfiats['text'] = number_format($sfiats['avg']).'%';
		} else {
			$sfiats['class'] = advisory_get_sfia_skills_fit_class('N/A');
			$sfiats['text'] = 'N/A';
		}
		// $data .= '<br><pre>'. print_r($roles, true) .'</pre>';
		// $data .= '<br><pre>'. print_r($head, true) .'</pre>';
		$data .= '<div class="card">';
			$data .= '<div class="card-body">';
				$data .= '<div class="headContainer">';
                    $data .= '<div class="ratingContainer">';
                        $data .= '<div class="skills_fit"><span class="title">Skills Fit %</span> <div class="score '.$avg['class'].'">'.$avg['text'].'</div></div>';
                        $data .= '<div class="technical_score"><span class="title">Technical</span> <div class="score '.$sfiats['class'].'">'.$sfiats['text'].'</div></div>';
                    $data .= '</div>';
                    $data .= '<div class="roleLeveContainer">';
                        $data .= '<div class="role"><span class="title">Role:</span> '.$role.'</div>';
                        $data .= '<div class="level"><span class="title">Level:</span> Level '.$head['level'].'</div>';
                    $data .= '</div>';
                    $data .= '<div class="notesContainer"><span class="title">Notes:</span> '.$head['notes'].'</div>';
                    $data .= '<div class="clearfix"></div>';
                $data .= '</div>';
                $data .= '<div class="clearfix"><br></div>';
				$data .= advisory_sfiar_get_user_skills($opts, $skills);
			$data .= '</div>';
		$data .= '</div>';
	}
	echo $data; wp_die();
}
add_action('wp_ajax_sfiar_technical_assessments_archives', 'ajax_sfiar_technical_assessments_archives');
function ajax_sfiar_technical_assessments_archives() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	if ( $post_id ) {
		$poles = advisory_form_default_values( $post_id, 'poles', true );
		$opts = get_post_meta( $post_id, 'form_opts', true );
		$questions = !empty($opts['questions']) ? $opts['questions'] : [];

		$rating_options = cs_get_option('criteria_sfiats');
		$rating_options = !empty($rating_options) ? advisory_select_array($rating_options) : [];

		$avg = !empty($poles['avg']) ? number_format($poles['avg']).'%'  : 0;


		if (!empty($questions)) {
			// $data .= '<br><pre>'. print_r($poles, true) .'</pre>';
			$data .= '<div class="card">';
				$data .= '<div class="card-body">';
					$data .= '<h4 class="mb-10">Technical Assessment : '.$avg.'</h4>';
					$data .= '<table class="table table-bordered">';
				        $data .= '<thead>';
				            $data .= '<tr>';
				                $data .= '<th class="t-heading-dark">Topic Area</th>';
				                $data .= '<th class="t-heading-dark text-center p-0" style="width:100px;">Rating <span class="'.advisory_get_sfia_skills_fit_color($poles['avg']).'"></span></th> ';
				                $data .= '<th class="t-heading-dark"style="min-width:400px;">Response</th>';
				            $data .= '</tr>';
				        $data .= '</thead>';
				        $data .= '<tbody>';
				        	foreach ($questions as $question_id => $question) {
				        		if (!empty($question['name'])) {
				        			$comment = !empty($poles['comment_'.$question_id]) ? $poles['comment_'.$question_id] : '';
				        			$rating = !empty($poles['rating_'.$question_id]) ? $poles['rating_'.$question_id] : 0;
						            $data .= '<tr>';
						                $data .= '<td>'.$question_id.'. '.$question['name'].'</td>';
						                $data .= '<td class="no-padding text-center '.advisory_sfiats_get_class_by_value($rating).'">'.$rating_options[$rating].'</td>';
						                $data .= '<td class="bigComment pointer" isactive="no">'.get_excerpt($comment, 5).'<input class="bigComment_text" type="hidden" value="'.htmlentities($comment).'"></td>';
						            $data .= '</tr>';
				        		}
				        	}
				        $data .= '</tbody>';
				    $data .= '</table>';
				$data .= '</div>';
			$data .= '</div>';
		}
	}
	echo $data; wp_die();
}
add_action('wp_ajax_sfiar_get_archive_item', 'ajax_sfiar_get_archive_item');
function ajax_sfiar_get_archive_item() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	$archive_time = !empty($_POST['archive_time']) ? $_POST['archive_time'] : false;
	// echo '<br><pre>'. print_r([$post_id, $user_id, $archive_time], true) .'</pre>'; wp_die();
	if ( $post_id && $user_id && $archive_time ) {
		$archives_meta = $user_id.'_archives';
    	$archives = get_post_meta( $post_id, $archives_meta, true );
    	$archive = [];
    	if (!empty($archives[$archive_time])) {
    		$archive = $archives[$archive_time];
    		parse_str($archive, $archive);
		    parse_str($archive['extra'], $archive['extra']);
    	}
    	$opts = get_post_meta( $archive['extra']['sfia']['id'], 'form_opts', true );

    	$published_at = !empty($archive['extra']['sfia']['published_at']) ? date('M, j\<\s\u\p\>S\<\/\s\u\p\> Y', $archive['extra']['sfia']['published_at']) : '';
    	if ( !empty($archive['extra']['sfia']['avg']) ) {
    		$sfiaAvg = $archive['extra']['sfia']['avg'];
    		$sfia = ['value'=>$sfiaAvg, 'text'=>number_format($sfiaAvg).'%', 'class' => advisory_get_sfia_skills_fit_class($sfiaAvg)];
    	} else {
    		$sfia = ['value'=>'N/A', 'text'=>'N/A', 'class' => advisory_get_sfia_skills_fit_class('N/A')];
    	}
    	if ( !empty($archive['extra']['sfiats']['avg']) ) {
    		$sfiatsAvg = $archive['extra']['sfia']['avg'];
    		$sfiats = ['value'=>$sfiatsAvg, 'text'=>number_format($sfiatsAvg).'%', 'class' => advisory_get_sfia_skills_fit_class($sfiatsAvg)];
    	} else {
    		$sfiats = ['value'=>'N/A', 'text'=>'N/A', 'class' => advisory_get_sfia_skills_fit_class('N/A')];
    	}

    	$skills = !empty($archive['extra']['skills']) ? $archive['extra']['skills'] : [];
    	$loop = intval(count($skills) / 6);
    	$ar_bg = !empty($archive['assessments_results']) ? 'bg-red showArchivedComment' : 'bg-green';
    	$ca_bg = !empty($archive['currnt_activities']) 	 ? 'bg-red showArchivedComment' : 'bg-green';
    	$pa_bg = !empty($archive['planned_activities'])  ? 'bg-red showArchivedComment' : 'bg-green';
    	
    	// $data .= '<br><pre>'. print_r($archive, true) .'</pre>';
		$data .= '<table class="table table-bordered m-0" style="width:520px;" post_id='.$post_id.' user_id='.$user_id.' archive_time='.$archive_time.'>';
			$data .= '<thead>';
				$data .= '<tr>';
					$data .= '<th colspan="4" class="t-heading-sky">Skills Analysis</th>';
				$data .= '</tr>';
			$data .= '</thead>';
			$data .= '<tbody>';
				$data .= '<tr>';
					$data .= '<td class="t-heading-dark">Assessment Results</td>';
					$data .= '<td class="t-heading-dark" style="width: 100px;">Skill Fit %</td>';
					$data .= '<td class="t-heading-dark" style="width: 100px;">Technical</td>';
					$data .= '<td class="t-heading-dark" style="width: 152px;">Last Assessment</td>';
				$data .= '</tr>';
				$data .= '<tr>';
					$data .= '<td class="pointer '. $ar_bg .'" title="Assessment Results">&nbsp;<div class="hidden">'.$archive['assessments_results'].'</div></td>';
					$data .= '<td class="text-center '.$sfia['class'].'" style="font-size: 21px;font-weight: 700;">'.$sfia['text'].'</td>';
					$data .= '<td class="text-center '.$sfiats['class'].'" style="font-size: 21px;font-weight: 700;">'.$sfiats['text'].'</td>';
					$data .= '<td>'.$published_at.'</td>';
				$data .= '</tr>';
			$data .= '</tbody>';
		$data .= '</table>';
		$data .= '<table class="table table-bordered m-0">';
			$data .= '<tbody>';
				$data .= '<tr>';
					$data .= '<td class="t-heading-dark">CATEGORY</td>';
					$data .= '<td class="t-heading-dark">SUB-CATEGORY</td>';
					$data .= '<td class="t-heading-dark">SKILL</td>';
					$data .= '<td class="t-heading-dark">CODE</td>';
					$data .= '<td class="t-heading-dark">RANK</td>';
					$data .= '<td class="t-heading-dark">EVALUATION</td>';
					$data .= '<td class="t-heading-dark">TARGET</td>';
				$data .= '</tr>';
				for ($i=0; $i < $loop; $i++) { 
					$catClass = advisory_get_sfiar_category_class($skills['category_'.$i]);
					$code = advisory_sfia_dashboard_scorecard_get_skill_colde($opts, $skills, $i);
					$class = advisory_get_sfiar_access_level_container_class($skills['assess_level_'. $i], $skills['target_level_'. $i]);
					$data .= '<tr>';
						$data .= '<td class="'.$catClass.'">'. advisory_string_from_id($skills['category_'.$i]).'</td>';
						$data .= '<td class="'.$catClass.'">'. advisory_string_from_id($skills['subcategory_'.$i]).'</td>';
						$data .= '<td class="'.$catClass.'">'. advisory_string_from_id($skills['skill_'.$i]).'</td>';
						$data .= '<td class="'.$catClass.'" style="width: 62px;">'.$code.'</td>';
						$data .= '<td style="width: 64px;">'. ucfirst($skills['rank_'.$i]) .'</td>';
						$data .= '<td class="'.$class.'" style="width: 110px;">Level '.$skills['target_level_'.$i].'</td>';
						$data .= '<td class="bg-green" style="width: 84px; ">Level '.$skills['target_level_'.$i].'</td>';
					$data .= '</tr>';
				}
			$data .= '</tbody>';
		$data .= '</table>';
		$data .= '<table class="table table-bordered m-0" style="width:380px;" post_id='.$post_id.' user_id='.$user_id.' archive_time='.$archive_time.'>';
			$data .= '<thead>';
				$data .= '<tr>';
					$data .= '<th colspan="4" class="t-heading-sky">SFIA Roadmap</th>';
				$data .= '</tr>';
			$data .= '</thead>';
			$data .= '<tbody>';
				$data .= '<tr>';
					$data .= '<td class="t-heading-dark">Current/Completed Activities</td>';
					$data .= '<td class="t-heading-dark">Planned Activities</td>';
				$data .= '</tr>';
				$data .= '<tr>';
					$data .= '<td class="pointer '. $ca_bg .'" title="Current/Completed Activities">&nbsp;<div class="hidden">'.$archive['currnt_activities'].'</div></td>';
					$data .= '<td class="pointer '. $pa_bg .'" title="Planned Activities">&nbsp;<div class="hidden">'.$archive['planned_activities'].'</div></td>';
				$data .= '</tr>';
			$data .= '</tbody>';
		$data .= '</table>';
	}
	echo $data; wp_die();
}
add_action('wp_ajax_sfiar_delete_archive_item', 'ajax_sfiar_delete_archive_item');
function ajax_sfiar_delete_archive_item() {
	$data = '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : false;
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : false;
	$archive_time = !empty($_POST['archive_time']) ? $_POST['archive_time'] : false;
	$archives_meta = $user_id.'_archives';
    $archives = get_post_meta( $post_id, $archives_meta, true );
    if (isset($archives[$archive_time])) unset($archives[$archive_time]);
    if ( update_post_meta( $post_id, $archives_meta, $archives) ) wp_send_json( true );
    wp_send_json( false );
}
function advisory_get_sfiar_for_company($company_id) {
	return $company_id;
	return false;
}