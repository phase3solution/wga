<?php 
function advisory_generate_mtar_form_threats() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create Threat Cats and save. Then create Threats, and Questions'];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $threat_cat) {
				$data[] = array(
					'id' => advisory_id_from_string($threat_cat['name']) . '_threats',
					'type' => 'group',
					'title' => $threat_cat['name'],
					'desc' => 'Each Threats name should be unique',
					'button_title' => 'Add New',
					'fields' => [
						['id' => 'name', 'type' => 'text', 'title' => 'Name'],
						['id' => 'desc', 'type' => 'textarea', 'title' => 'Description'],
						['id' => 'avg', 'type' => 'text', 'title' => 'AVG'],
						['id' => 'test', 'type' => 'text', 'title' => 'test'],
					]
				);
			}
		}
		return $data;
	}
}
function advisory_generate_mtar_form_subthreats() {
	if (is_admin() && is_edit_page()) {
		// addRegisterMetaFieldsTest(637, 682);
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create Threat Cats and save. Then create Threats, and Questions'];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $threat_cat) {
				$data[] = ['type' => 'subheading', 'content' => $threat_cat['name']];
				$threatID = advisory_id_from_string($threat_cat['name']) . '_threats';
				if ($threats = @$meta[$threatID]) {
					foreach ($threats as $threat) {
						$subheadID = $threatID .'_'. advisory_id_from_string($threat['name']);
						$data[] = [
							'id' => $subheadID,
							'type' => 'group',
							'title' => $threat['name'],
							'button_title' => 'Add New',
							'fields' => [
								['id' => 'name', 'type' => 'text', 'title' => 'Name'],
								['id' => 'desc', 'type' => 'textarea', 'title' => 'Description']
							]
						];
					}
				}
			}
		}
		return $data;
	}
}
function advisory_get_formatted_mtar_data($post_id) {
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
						// customer_facing_threats_customer_relationship_management
						$subthreatID = $cat_id . '_threats_'. advisory_id_from_string($threat['name']);
						$data[$index]['areas'][$threatIndex]['name'] = $threat['name'];
						$data[$index]['areas'][$threatIndex]['avg'] = $threat['avg'];
						$data[$index]['areas'][$threatIndex]['subthreats'] = !empty($meta[$subthreatID]) ? $meta[$subthreatID] : [];
					}
				}
			}
			$index++;
		}
	}
	return $data;
}
function category_select_options($subthreats, $selectedID='') {
	$html = $data ='';
	if (!empty($subthreats)) {
		$selected = get_template_directory_uri().'/images/mta/select/'.$selectedID.'.png';
		$html .= '<div class="container-fluid">';
		    $html .= '<div class="select-image-option">';
    		    $html .= '<div class="row">';
    		        $html .= '<div class="col-sm-4">&nbsp;</div>';
    		        $html .= '<div class="col-sm-4">';
    		            $html .= '<div class="text-center">';
    		                $html .= '<div class="dropdown">';
                    			$html .= '<button class="font-130p mtarThreatSelect t-heading-sky" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Select Categories </button>';
                    			$html .= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    				foreach ($subthreats as $subthreatSI => $subthreat) {
                    					$subthreatID = advisory_id_from_string($subthreat['name']);
                    					$bg = get_template_directory_uri().'/images/mta/select/'.$subthreatID.'.png';
                    					$selected = $selectedID == $subthreatID ? ' selected' : '';
                    					$html .= '<div class="dropdown-item mtarThreatSelectItem'.$selected.'" subthreat="'.$subthreatID.'"><img src="'.$bg.'"></div>';
                    				}
                    			$html .= '</div>';
                    		$html .= '</div>';
    		            $html .= '</div>';
    		        $html .= '</div>';
    		        $html .= '<div class="col-sm-4">&nbsp;</div>';
    		    $html .= '</div>';
    		$html .= '</div>';
	    $html .= '</div>';
	}
	return $html.$data;
}
function category_select_options2($subthreats, $selectedID='') {
	$html = '';
	if (!empty($subthreats)) {
		$html .= '<select id="" class="font-130p mtarThreatSelect t-heading-sky">';
			foreach ($subthreats as $subthreatSI => $subthreat) {
				$subthreatID = advisory_id_from_string($subthreat['name']);
				$selected = $selectedID == $subthreatID ? ' selected' : '';
				$html .= '<option value="'.$subthreatID.'"'.$selected.'>'.$subthreat['name'].'</option>';
			}
		$html .= '</select>';
	}
	return $html;
}
function subcategory_select_options($threatID, $subthreats, $selectedID='') {
	$html = '';
	if (!empty($subthreats)) {
		$html .= '<select id="" class="mtarSubThreatSelect t-heading-sky" style="padding:3px 3px 3px 5px;margin-left:-5px">';
			foreach ($subthreats as $subthreatSI => $subthreat) {
				$subthreatID = $threatID.'_'.advisory_id_from_string($subthreat['name']);
				$selected = $threatID.'_'.$selectedID == $subthreatID ? ' selected' : '';
				$html .= '<option value="'.$subthreatID.'"'.$selected.'>'.$subthreat['name'].'</option>';
			}
		$html .= '</select>';
	}
	return $html;
}
function MTAR_PDF_Data() {
	$data = [];
	if (!empty($_GET)) {
		$postID = !empty($_GET['post']) ? $_GET['post'] : false;
		if ($postID) {
		    $company = advisory_get_user_company();
            $data['customer_name'] = $company->name ? $company->name : 'Customer Name';
			$data['report_date'] = date("m/d/Y");
			$data['last_assessment'] = get_the_date("m/d/Y", $postID);
			$rr_data = advisory_get_formatted_mtar_data($postID);
			foreach ($rr_data as $rr) {
				if (@$_GET['cat'] != advisory_id_from_string($rr['cat'])) continue;
				$data['cat'] = $rr['cat'];
			    $base = @number_format($rr['base']) * 1000;
			    foreach ($rr['areas'] as $areaSI => $area) {
					$areaID = advisory_id_from_string($area['name']);
					if (@$_GET['th'] != $areaID) continue;
					$data['area'] = $area['name'];
					foreach ($area['subthreats'] as $subthreatSI => $subthreat) {
						$base++;
						$subthreatID = advisory_id_from_string($subthreat['name']);
						if (@$_GET['subth'] != $subthreatID) continue;
						$rr_id = $areaID . '_'. $subthreatID;
						$data = array_merge($data, advisory_company_default_values($_GET['company'], $rr_id));
						$data['subarea'] = $subthreat['name'];
						$data['base'] = $base;
						if ($subthreat['avg'] == 'g') {
							$data['avg'] = 'GAP';
							$data['avg_cls'] = ihcColorAVG($subthreat['avg']);
						} else {
							$data['avg'] = number_format($subthreat['avg'],1);
							$data['avg_cls'] = ihcColorAVG(number_format($subthreat['avg'], 1));
						}
						$data = array_merge($data,  MTARegisterColorByValue(@$data['status']));
					}
			    }
			}
		}
	}
	return $data;
}
function MTARegisterColorByValue($val) {
	$cl = '';
	$statusOpts = ['1'=>'Not Required/Not Started', '2'=>'Work Deferred', '3'=>'Work In Progress','4'=>'Work Completed','5'=>'Reassessment Required'];
	switch ($val) {
		case '1': $cl = 'color-one'; break;
		case '2': $cl = 'color-two'; break;
		case '3': $cl = 'color-three'; break;
		case '4': $cl = 'color-four'; break;
		case '5': $cl = 'color-five'; break;
		default: $cl = ''; break;
	}
	return ['status_cls'=>$cl, 'status_txt'=> @$statusOpts[$val]];
}
function MTARegisterSaveArchive() {
	global $wpdb;
	$table = $wpdb->prefix.'ph3_archives';
	parse_str($_REQUEST['data']);
	$data = [
		'user_id' => $_REQUEST['archivedby'], 
		'post_id' => $post_id, 
		'company_id' => $_REQUEST['post_id'], 
		'category' => $category_name, 
		'threat' => $threat_name, 
		'subthreat' => $subthreat_name, 
		'meta' => $_REQUEST['meta'], 
		'pa' => $pa,
		'ca' => $ca,
		'owner' => $owner,
		'status' => $status,
		'dc' => $dc,
	];
	if ($wpdb->insert($table, $data)) return 1;
	// return $wpdb->last_error;
	return 0;
}
function MTARegisterGetArchive($userID, $limit=0) {
	global $wpdb, $user_switching, $RM;
	$removePDF = false;
	if (current_user_can('administrator') || current_user_can('advisor') || $user_switching->get_old_user()) $removePDF = true;
	$category = !empty($_GET['cat']) ? advisory_string_from_id($_GET['cat']) : false;
	$table = $wpdb->prefix.'ph3_archives';
	$sql = '';
	$sql .= 'SELECT *  FROM `'.$table.'` WHERE `user_id` = '.$userID.' ';
	$sql .= 'AND category = "'.$category.'" ';
	$sql .= 'AND is_active = 1 ';
	$sql .= 'ORDER BY `id` DESC ';
	if ($limit) $sql .= 'LIMIT '.$limit;
	$archives = $wpdb->get_results($sql);
	$html = '';
	if ($archives) {
		// $html .=  '<br><pre>'. print_r($archives, true) .'</pre>';
		$html .= '<table class="table table-inverse table-bordered table-hover">';
			$html .= '<thead>';
				$html .= '<tr>';
					$html .= '<th class="t-heading-dark font-120p">ID</th>';
					$html .= '<th class="t-heading-dark font-120p">Category</th>';
					$html .= '<th class="t-heading-dark font-120p">Sub-Category</th>';
					$html .= '<th class="t-heading-dark font-120p">Element</th>';
					$html .= '<th class="t-heading-dark font-120p" style="width:165px;">Date</th>';
					if ($removePDF) $html .= '<th class="t-heading-dark font-120p" style="width:142px;">Action</th>';
					else $html .= '<th class="t-heading-dark font-120p" style="width:80px;">Action</th>';
				$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
				foreach ($archives as $archiveSI => $archive) {
					$date = date('M j, Y h:i A', strtotime($archive->created_at));
					$html .= '<tr>';
						$html .= '<td>'.($archiveSI+1).'</td>';
						$html .= '<td>'.$archive->category.'</td>';
						$html .= '<td>'.$archive->threat.'</td>';
						$html .= '<td>'.$archive->subthreat.'</td>';
						$html .= '<td>'. $date .'</td>';
						$pdfLink = site_url('mtar-pdf/').'?archive='.$archive->id;
						// $pdfLink = 'javascript:;';
						if ($removePDF) { 
							$html .= '<td class="text-center">';
								$html .= '<a href="'.$pdfLink.'" target="_blank" class="btn btn-primary btn-sm viewMTAArchive">View</a>&nbsp;';
								$html .= '<a href="javascript:;" class="btn btn-danger btn-sm removeMTAArchive" date="'. $date .'" mtar_archive_id='.$archive->id.'>Remove</a>';
							$html .= '</td>';
						} else $html .= '<td class="text-center"><a href="'.$pdfLink.'" target="_blank" class="btn btn-primary btn-sm">View</a></td>';
					$html .= '</tr>';
				}
			$html .= '</tbody>';
		$html .= '</table>';
	}
	return $html;
}
function MTAR_Arhcive_PDF_Data($archiveID) {
	global $wpdb, $current_user;
	$data = [];
	$table = $wpdb->prefix.'ph3_archives';
	$sql = "SELECT *  FROM `".$table."` WHERE `id` = $archiveID";
	$archive = $wpdb->get_row($sql);
	if ($archive->user_id != $current_user->ID) return false;
	if ($archive->post_id) {
	    $company = advisory_get_user_company();
        $data['customer_name'] = $company->name ? $company->name : 'Customer Name';
		$data['report_date'] = date("m/d/Y", strtotime($archive->created_at));
		$data['last_assessment'] = get_the_date("m/d/Y", $archive->post_id);
		$rr_data = advisory_get_formatted_mtar_data($archive->post_id);
		foreach ($rr_data as $rr) {
			if ($archive->category != $rr['cat']) continue;
			$data['cat'] = $rr['cat'];
		    $base = @number_format($rr['base']) * 1000;
		    foreach ($rr['areas'] as $areaSI => $area) {
				$areaID = advisory_id_from_string($area['name']);
				if ($archive->threat != $area['name']) continue;
				$data['area'] = $area['name'];
				foreach ($area['subthreats'] as $subthreatSI => $subthreat) {
					$base++;
					$subthreatID = advisory_id_from_string($subthreat['name']);
					if ($archive->subthreat != $subthreat['name']) continue;
					$rr_id = $areaID . '_'. $subthreatID;
					$data = array_merge($data, advisory_company_default_values($archive->company_id, $rr_id));
					$data['subarea'] = $subthreat['name'];
					$data['base'] = $base;
					if ($subthreat['avg'] == 'g') {
						$data['avg'] = 'GAP';
						$data['avg_cls'] = ihcColorAVG($subthreat['avg']);
					} else {
						$data['avg'] = number_format($subthreat['avg'],1);
						$data['avg_cls'] = ihcColorAVG(number_format($subthreat['avg'], 1));
					}
					$data = array_merge($data,  MTARegisterColorByValue($data['status']));
				}
		    }
		}
		$data['pa'] = $archive->pa;
		$data['ca'] = $archive->ca;
		$data['owner'] = $archive->owner;
		$data['status'] = $archive->status;
	}
	return $data;
}
add_action('wp_ajax_mta_register_remove_archive', 'MTARegisterRemoveArchive');
function MTARegisterRemoveArchive() {
	check_ajax_referer('advisory_nonce', 'security');
	$archiveID = $_REQUEST['archiveid'];
	if ($archiveID) {
		global $wpdb;
		$table = $wpdb->prefix.'ph3_archives';
		return wp_send_json($wpdb->update($table, ['is_active' => 0], ['id' => $archiveID]));
	}
	wp_send_json(false);
}