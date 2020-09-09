<?php 
function renderTabNav($sections=[] ) {
	$html = '';
	if ($sections) {
		$html .= '<div class="col-sm-3">';
	        $html .= '<ul class="nav nav-tabs tabs-left card" role="tablist">';
	        	foreach ($sections as $sectionSI => $section) {
	        		$active = $sectionSI == 0 ? 'active' : '';
	            	$html .= '<li role="presentation" class="'. $active .'"><a href="#'. $section['name'] .'" aria-controls="'. $section['name'] .'" role="tab" data-toggle="tab">'. $section['title'] .'</a></li>';
	        	}
	        $html .= '</ul>';
	    $html .= '</div>';
	}
    return $html;
}
function renderTabContentFor($fields=[] ) {
	$html = '';
	if ($fields) {
		foreach ($fields as $field) {
			if (!empty($field['id'])) $field['name'] = $field['id'];
			$field['std'] = !empty($field['std']) ? $field['std'] : '';
			// $html .= json_encode($field).'<br>';
			$html .= getInputFieldFor($field);
		}
	}
    return $html;
}
// $field [type, id, title, value, std, options(for select options only)]
function getInputFieldFor($field) {
	$html = '';
	switch ($field['type']) {
        case 'text':
			$value = !empty($field['value']) ? $field['value'] : @$field['std'];
			$desc  = !empty($field['desc']) ? '<br />(<small>'. $field['desc'] .'</small>)' : '';
			$html .= '<div class="form-group">';
			  	$html .= '<label for="'. $field['id'] .'">'. $field['title'] .'</label>'.$desc;
			  	$html .= '<input id="'. $field['id'] .'" class="form-control" type="text" name="'. $field['name'] .'" value="'. $value .'">';
			$html .= '</div>';
            break;
        case 'date':
        	$value = !empty($field['value']) ? $field['value'] : @$field['std'];
			$desc  = !empty($field['desc']) ? '<br />(<small>'. $field['desc'] .'</small>)' : '';
			$html .= '<div class="form-group">';
			  	$html .= '<label for="'. $field['id'] .'">'. $field['title'] .'</label>'.$desc;
			  	$html .= '<input id="'. $field['id'] .'" class="form-control" type="date" name="'. $field['name'] .'" value="'. $value .'">';
			$html .= '</div>';
            break;
        case 'textarea':
        	$rows = !empty($field['row']) ? $field['row'] : 3;
            $value = !empty($field['value']) ? $field['value'] : @$field['std'];
            $desc  = !empty($field['desc']) ? '<br />(<small>'. $field['desc'] .'</small>)' : '';
            $html .= '<div class="form-group">';
			  	$html .= '<label for="'. $field['id'] .'">'. $field['title'] .'</label>'.$desc;
            	$html .= '<textarea id="'. $field['id'] .'" class="form-control" rows="'. $rows .'" name="'. $field['name'] .'">'. $value .'</textarea>';
			$html .= '</div>';
            break;
        case 'wysiwyg':
			$editor_id = !empty($field['name']) ? $field['name'] : false;
			if ($editor_id) {
				$rows = !empty($field['row']) ? $field['row'] : 3;
				$desc  = !empty($field['desc']) ? '<br />(<small>'. $field['desc'] .'</small>)' : '';
				$content = !empty($field['value']) ? $field['value'] : @$field['std'];
	            $settings = array( 'media_buttons' => true, 'tinymce' => true, 'textarea_rows'=>$rows);
	            $html .= '<div class="form-group">';
				$html .= '<label for="'. $field['id'] .'">'. $field['title'] .'</label>'.$desc;
	            wp_editor( $content, $editor_id, $settings );
				$html .= '</div>';
			}
            break;
        case 'select':
            $html .= '<select name="'. $field['id']. '" id="'. $field['id']. '">';
            foreach ($field['options'] as $option) {
                $html .= '<option'. $field == $option ? ' selected="selected"' : ''. '>'. $option. '</option>';
            }
            $html .= '</select>';
            break;
        case 'radio':
            foreach ($field['options'] as $option) {
                $html .= '<input type="radio" name="'. $field['id']. '" value="'. $option['value']. '"'. $field == $option['value'] ? ' checked="checked"' : ''. ' />'. $option['name'];
            }
            break;
        case 'checkbox':
            $html .= '<label for="'. $field['id'] .'"><input type="checkbox" name="'. $field['id']. '" id="'. $field['id']. '"'. $field ? ' checked="checked"' : ''. ' /> '. $field['name'] .'</label>';
            break;
        case 'switcher':
        	$checked = !empty($field['value']) ? ' checked="checked"' : '';
        	$label = !empty($field['label']) ? ' (<small>'. $field['label'] .'</small>)' : '';
        	$html .= '<div class="custom-control custom-switch">';
        		$html .= '<input type="checkbox" class="custom-control-input" id="'. $field['id'] .'" name="'. $field['name'] .'"'. $checked .'>';
        		$html .= '<label class="custom-control-label" for="'. $field['id'] .'">'. $field['title'].$label .'</label>';
        	$html .= '</div>';
            break;
        case 'notice':
        	$btn = !empty($field['submitBtn']) ? '<button type="submit" name="updateBtn" class="btn btn-primary btn-xs pull-right">Update</button>' : '';
        	$html .= '<div class="alert alert-'. $field['class'] .'" role="alert">'. $field['content'] .$btn.'</div>';
            break;
        case 'upload':
			$value = !empty($field['value']) ? $field['value'] : @$field['std'];
			$desc  = !empty($field['desc']) ? '<br />(<small>'. $field['desc'] .'</small>)' : '';
			$html .= '<div class="form-group">';
			  	$html .= '<label for="'. $field['id'] .'">'. $field['title'] .'</label>'.$desc;
			  	$html .= '<div class="uploadWrapper">';
			  		$html .= '<input id="'. $field['id'] .'" class="form-control pull-left uploadUrl" type="text" name="'. $field['name'] .'" value="'. $value .'">';
			  		$html .= '<button type="button" class="btn btn-success btn-sm pull-right uploadBtn">Upload</button>';
			  	$html .= '<div class="clearfix"></div>';
			  	$html .= '</div>';
			$html .= '</div>';
            break;
        case 'group':
        	$asi = 1;
        	$desc = !empty($field['desc']) ? '<p><small>'. $field['desc'] .'</small></p>' : '';
        	$html .= '<div class="panel-group panelGroup" id="accordion_'.$field['id'].'" role="tablist" aria-multiselectable="true">';
        		$html .= '<div class="accordionTitle">';
        		$html .= '<button type="submit" name="updateBtn" class="btn btn-primary btn-xs pull-right">Update</button>';
        		$html .= '<h4>'. $field['title'].'</h4>'.$desc;
        		$html .= '</div>';
        		$html .= '<div class="accordionWrapper">';
        			if ($field['accordions']) {
        				
        				foreach ($field['accordions'] as $accordionSI => $accordion) {
        					$html .= getAccordionItem($field['fields'], $field['id'], $accordion, $accordionSI);
        				}
        			}
        			$asi_next = !empty($accordionSI) ? $accordionSI + 1 : 1;
        		$html .= '</div>';
        		$html .= '<button type="button" class="btn btn-primary btn-xs addNewAccordion" items=\''. json_encode($field['fields']) .'\' id="'. $field['id'] .'" btntitle="'. $field['accordion_title'] .'" asi='.$asi_next.'>'. $field['button_title'] .'</button>';
        	$html .= '</div>';
            break;
    }
    return $html;
}
function getAccordionItem($items, $id, $accordion, $accordionSI, $btnTitle='Add new', $values=[]) {
	$html = '';
	$ariaExpanded = false;
	$collapse = '';
	if (!empty($accordion)) {
		$accordionNameTitle = !empty($accordion['title']) ? $accordion['title'] : 'Item - '. $accordionSI;
		$accordionName = !empty($accordion['name']) ? $accordion['name'] : $accordionNameTitle;
	}
	else { $accordionName = $btnTitle; $ariaExpanded = true; $collapse = ' in';}
	$accordionID = preg_replace('/[^A-Za-z0-9\-]/', '', $id.'_'.advisory_id_from_string($accordionName));
	$html .= '<div class="panel panel-default panelWrapper" si="'. $accordionSI .'">';
        $html .= '<div class="panel-heading panelHeadingSmall" role="tab" id="heading'. $accordionID .'">';
            $html .= '<h4 class="panel-title">';
                $html .= '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'. $accordionID .'" aria-expanded="'.$ariaExpanded.'" aria-controls="collapse'. $accordionID .'">';
                $html .= $accordionName;
                $html .= '</a>';
            $html .= '</h4>';
        $html .= '</div>';
        $html .= '<div id="collapse'. $accordionID .'" class="panel-collapse collapse'.$collapse.'" role="tabpanel" aria-labelledby="heading'. $accordionID .'">';
            $html .= '<div class="panel-body">';
            	// $html .= json_encode($id).'<br><br>'.json_encode($items).'<br><br>'. json_encode($accordion).'<br><br>';
            	if ($items) {
            		foreach ($items as $itemSI => $item) {
            			$item['name'] = $id.'['.$accordionSI.']['.$item['id'].']'; 
            			$item['value'] = !empty($accordion[$item['id']]) ? $accordion[$item['id']] : '';
            			$html .= getInputFieldFor($item);
            		}
            	}
        		$html .= '<button type="button" class="btn btn-danger btn-xs pull-right tabpanelRemoveBtn">Remove</button>';
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</div>';
	return $html;
}
// =========================================================== //
// EDIT
// =========================================================== //

function advisoryGenerateIHCFormGeneral($meta) {
	$data = [];
	$data = [
		array(
				'id' => 'display_name',
				'type' => 'text',
				'title' => 'Display Name',
				'value' => !empty($meta['display_name']) ? $meta['display_name'] : '',
			),
			array(
				'id' => 'desc',
				'type' => 'text',
				'title' => 'Description',
				'value' => !empty($meta['desc']) ? $meta['desc'] : '',
			),
			array(
				'id' => 'icon',
				'type' => 'upload',
				'title' => 'Icon',
				'value' => !empty($meta['icon']) ? $meta['icon'] : '',
			),
			array(
				'id' => 'criteria_definition',
				'type' => 'switcher',
				'title' => 'Criteria Definition',
				'label' => 'Do you want to display criteria definiiton?',
				'value' => isset($meta['criteria_definition']) ? $meta['criteria_definition'] : true,
			),
	];
	return $data;
}
function advisoryGenerateIHCFormAreas($meta) {
	$data = [];
	$data = [
		['type' => 'notice', 'class' => 'danger', 'content' => 'Create Area and save. Then create Section from "Sections" tab'],
		[
			'id' => 'areas',
			'type' => 'group',
			'title' => 'Areas',
			'desc' => 'Each Area name should be unique',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Area',
			'fields' => [
				['id' => 'name', 'type' => 'text', 'title' => 'Name'],
				['id' => 'icon_menu', 'type' => 'upload', 'title' => 'Icon (Menu)'],
				['id' => 'icon_title', 'type' => 'upload', 'title' => 'Icon (Title)'],
				['id' => 'desc', 'type' => 'textarea', 'title' => 'Description', 'sanitize' => false]
			],
			'accordions' => !empty($meta['areas']) ? $meta['areas'] : []
		]
	];
	return $data;
}
function advisoryGenerateIHCFormSections($meta) {
	$data = [];
	$data[] = array(
		'type' => 'notice',
		'class' => 'danger',
		'content' => 'Create Section and save. Then create Table from "Tables" tab',
	);
	if (!empty($meta['areas'])) {
		foreach ($meta['areas'] as $area) {
			$id = 'sections_' . advisory_id_from_string($area['name']);
			$data[] = array(
				'id' => $id,
				'type' => 'group',
				'title' => $area['name'],
				'desc' => 'Each Section name should be unique',
				'button_title' => 'Add New',
				'accordion_title' => 'Add New Section',
				'fields' => array(
					array(
						'id' => 'name',
						'type' => 'text',
						'title' => 'Name',
					),
					array(
						'id' => 'desc',
						'type' => 'textarea',
						'title' => 'Description',
					),
					array(
						'id' => 'docs',
						'type' => 'upload',
						'title' => 'Documentation',
					),
				),
				'accordions' => !empty($meta[$id]) ? $meta[$id] : []
			);
		}
	}
	return $data;
}
function advisoryGenerateIHCFormTables($meta) {
	if (true) {
		$data = [];
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create Table and save. Then create Table Group from "Table Groups" tab'];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $area) {
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($meta[$section_id])) {
					$data[] = ['type' => 'heading', 'content' => $area['name']];
					foreach ($meta[$section_id] as $section) {
						$id = $section_id . '_tables_' . advisory_id_from_string($section['name']);
						$data[] = array(
							'id' => $id,
							'type' => 'group',
							'title' => $section['name'],
							'desc' => 'Each Table name should be unique',
							'button_title' => 'Add New',
							'accordion_title' => 'Add New Table',
							'fields' => [
								['id' => 'name', 'type' => 'text', 'title' => 'Name'],
								['id' => 'desc', 'type' => 'textarea', 'title' => 'Desc']
							],
							'accordions' => !empty($meta[$id]) ? $meta[$id] : []
						);
					}
				}
			}
		}
		return $data;
	}
}
function advisoryGenerateIHCFormQuestions($meta) {
	if (true) {
		$tables = [];
		$data = [];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $area) {
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($meta[$section_id])) {
					$data[] = ['type' => 'heading', 'content' => $area['name']];
					foreach ($meta[$section_id] as $section) {
						$t_id = $section_id . '_tables_' . advisory_id_from_string($section['name']);
						if (!empty($meta[$t_id])) {
							$data[] = array(
								'type' => 'subheading',
								'content' => $section['name'],
							);
							foreach ($meta[$t_id] as $table) {
								$id = $t_id . '_questions_' . advisory_id_from_string($table['name']);
								$data[] = array(
									'id' => $id,
									'type' => 'group',
									'title' => $table['name'],
									'desc' => 'Each Question title should be unique',
									'button_title' => 'Add New',
									'accordion_title' => 'Add New Question',
									'fields' => array(
										array(
											'id' => 'title',
											'type' => 'text',
											'title' => 'Title',
										),
										array(
											'id' => 'desc',
											'type' => 'textarea',
											'title' => 'Description',
										),
									),
									'accordions' => !empty($meta[$id]) ? $meta[$id] : []
								);
							}
						}
					}
				}
			}
		}
		return $data;
	}
}
// save form data
add_action('wp_ajax_new_accordion', 'advisory_add_new_accordion');
function advisory_add_new_accordion() {
	check_ajax_referer('advisory_nonce', 'security');
	$data 		= [];
	$id 		= $_POST['id'];
	$title 		= !empty($_POST['title']) ? $_POST['title'] : 'Add new';
	$items 		= !empty($_POST['items']) ? json_decode(stripslashes($_POST['items']), true) : [];
	$asi 		= !empty($_POST['asi']) ? (int) $_POST['asi'] : 1;
	$accordion 	= [];
	if ($id && $title && !empty($items) && $asi) {
		$html = getAccordionItem($items, $id, $accordion, $asi, $title);
		$data = ['html' => $html, 'asi' => $asi+1, 'status' => 200];
	} else {
		$data = ['html' => '', 'asi' => $asi, 'status' => 202];
	}
	echo json_encode($data);
	// echo $html;
	wp_die();
}
// =========================================================== //
// PDF
// =========================================================== //
function renderPDFTabNav($areas=[], $postType ) {
	$html = '';
	$areaSI = 0;
	if ($areas) {
		$html .= '<div class="col-sm-3">';
	        $html .= '<ul class="nav nav-tabs tabs-left card" role="tablist">';
	            if ($postType != 'bia') $html .= '<li role="presentation" class="active"><a href="#pdfgeneral" aria-controls="pdfgeneral" role="tab" data-toggle="tab">General</a></li>';
	        	foreach ($areas as $area) {
	        		$activeClass = $postType == 'bia' && $areaSI == 0 ? 'active' : '';
	            	$html .= '<li role="presentation" class="'. $activeClass .'"><a href="#'. advisory_id_from_string($area['name']) .'" aria-controls="'. advisory_id_from_string($area['name']) .'" role="tab" data-toggle="tab">'. $area['name'] .'</a></li>';
	            	$areaSI++;
	        	}
	        $html .= '</ul>';
	    $html .= '</div>';
	}
    return $html;
}
function advisoryGenerateBIAPDF($area, $meta, $pdfData, $prefix = 'ihc_pdf_') {
	$fields = [];
	$sectionSI = 0;
	
	// $fields[] = ['type' => 'notice', 'class' => 'info', 'content' => '<br><pre>'. print_r($pdfData, true) .'</pre>'];
	$serivceID = $prefix.advisory_id_from_string($area['name']).'_services';
	$fields[] = ['type' => 'subheading', 'content' => $area['name']];
	
	// ITEM 1
	$fields[] = [
		'id' => $serivceID . '_sascsop',
		'type' => 'group',
		'title' => 'SOFTWARE APPLICATIONS SUPPORTING CRITICAL SERVICES/PROCESSES',
		'desc' => 'Each name should be unique',
		'button_title' => 'Add New',
		'accordion_title' => 'Add New Item',
		'fields' => [
			['id' => 'application', 'type' => 'text', 'title' => 'Application'],
			['id' => 'function', 'type' => 'text', 'title' => 'Function'],
			['id' => 'location', 'type' => 'text', 'title' => 'Location'],
			['id' => 'desc', 'type' => 'text', 'title' => 'Description'],
			['id' => 'sc', 'type' => 'text', 'title' => 'Support Contact']
		],
		'accordions' => !empty($pdfData[$serivceID.'_sascsop']) ? $pdfData[$serivceID.'_sascsop'] : false
	];
	
	return renderTabContentFor($fields);
}
function advisoryGenerateIHCPDF($area, $meta, $pdfData, $prefix = 'ihc_pdf_') {
	$fields = [];
	$sectionSI = 0;
	$sectionID = 'sections_' . advisory_id_from_string($area['name']);
	$fields[] = ['id' => $prefix.$sectionID.'_introduction', 'type' => 'wysiwyg', 'title' => 'Introduction', 'value' => !empty($pdfData[$prefix.$sectionID.'_introduction']) ? $pdfData[$prefix.$sectionID.'_introduction'] : ''];
	if (!empty($meta[$sectionID])) {
		foreach ($meta[$sectionID] as $section) {

			$tableID = $sectionID. '_tables_' . advisory_id_from_string($section['name']);
			$assessmentTitleID 	= $prefix.$tableID.'_assessment_title';
			$assessmentDescID 	= $prefix.$tableID.'_assessment_desc';
			$summaryTitleID 	= $prefix.$tableID.'_summary_title';
			$summaryDescID 		= $prefix.$tableID.'_summary_desc';

			$fields[] = ['type' => 'notice', 'submitBtn'=>true, 'class' => 'info', 'content' => $section['name'].' ('. $area['name'] .' / '. $section['name'] .')'];
			$fields[] = ['id' => $assessmentTitleID, 'type' => 'wysiwyg', 'title' => 'Assessment Title', 'value' => !empty($pdfData[$assessmentTitleID]) ? $pdfData[$assessmentTitleID] : '' ];
			$fields[] = ['id' => $assessmentDescID, 'type' => 'wysiwyg', 'row'=>5, 'title' => 'Assessment Desc', 'value' => !empty($pdfData[$assessmentDescID]) ? $pdfData[$assessmentDescID] : '' ];
			$fields[] = ['id' => $summaryTitleID, 'type' => 'wysiwyg', 'title' => 'Summary Title', 'value' => !empty($pdfData[$summaryTitleID]) ? $pdfData[$summaryTitleID] : '' ];
			$fields[] = ['id' => $summaryDescID, 'type' => 'wysiwyg', 'row'=>5, 'title' => 'Summary Desc', 'value' => !empty($pdfData[$summaryDescID]) ? $pdfData[$summaryDescID] : '' ];
		}
	}
	return renderTabContentFor($fields);
}