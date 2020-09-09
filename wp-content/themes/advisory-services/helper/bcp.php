<?php
function advisory_generate_bcp_form_threats() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = array(
			'type' => 'notice',
			'class' => 'danger',
			'content' => 'Create Threat Cats and save. Then create Threats, and Questions',
		);
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $threat_cat) {
				$data[] = array(
					'id' => advisory_id_from_string($threat_cat['name']) . '_threats',
					'type' => 'group',
					'title' => $threat_cat['name'],
					'desc' => 'Each Threats name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Threat',
					'fields' => [['id' => 'name', 'type' => 'text', 'title' => 'Name'], ['id' => 'desc', 'type' => 'textarea', 'title' => 'Description']],
				);
			}
		}
		return $data;
	}
}
function advisory_generate_bcp_form_questions() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = array(
			'type' => 'notice',
			'class' => 'danger',
			'content' => 'Create Threat Cats and save. Then create Threats, and Questions',
		);
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $threat_cat) {
				$data[] = array(
					'type' => 'subheading',
					'content' => $threat_cat['name'],
				);
				if ($threats = @$meta[advisory_id_from_string($threat_cat['name']) . '_threats']) {
					foreach ($threats as $threat) {
						$data[] = array(
							'id' => advisory_id_from_string($threat_cat['name']) . '_threat_' . advisory_id_from_string($threat['name']) . '_fields',
							'type' => 'group',
							'title' => $threat['name'],
							'button_title' => 'Add New',
							'accordion_title' => 'Add New Question',
							'fields' => [['id' => 'name', 'type' => 'text', 'title' => 'Name']]
						);
					}
				}
			}
		}
		return $data;
	}
}
function BCPcolorByValue($val, $reverse=false) {
	$cl = 'color-five';
	switch ($val) {
		case '1': $cl = 'color-five'; break;
		case '2': $cl = 'color-four'; break;
		case '3': $cl = 'color-three'; break;
		case '4': $cl = 'color-two'; break;
		case '5': $cl = 'color-one'; break;
		default: $cl = 'color-five'; break;
	}
	return $cl;
}
function bcp_risk_calc($vulnerability, $impact, $probability) {
	if (!$vulnerability && !$impact && !$probability) return 0;
	if (!$impact) $impact = 1;
	if (!$probability) $probability = 1;
	if (!$vulnerability) $vulnerability = 1;
	$total = $impact * $vulnerability * $probability;
	if ($total < 2) return 0;
	return $total;
}
function bcp_risk_class(float $value) {
	$cls = 'color-one';
	if ($value <= 12) 						{ $cls = 'color-five'; } 
	else if ($value >= 13 && $value <= 26) 	{ $cls = 'color-four'; } 
	else if ($value >= 27 && $value <= 47) 	{ $cls = 'color-three'; } 
	else if ($value >= 48 && $value <= 99) 	{ $cls = 'color-two'; } 
	else if ($value >= 100) 				{ $cls = 'color-one'; }
	return $cls;
}
function bcp_heat_headers($char_arr) {
	$data = [];
	$steps = [1,7,13,18,23,27,32,37,43,48,58,68,77,87,199,108,117];
	foreach ($steps as $stepSI => $step) {
		 $chars = [];
		$data[$stepSI]['start'] = $step;
		$data[$stepSI]['end'] = ($step == 117) ? 125 : $steps[$stepSI + 1] - 1; 	
		$data[$stepSI]['range'] = $data[$stepSI]['start'] .' - '. $data[$stepSI]['end']; 	
		$data[$stepSI]['color'] = coloring_elements($step, 'bcp-score'); 
		for ($i=$data[$stepSI]['start']; $i <= $data[$stepSI]['end']; $i++) { 
			if (array_key_exists($i, $char_arr)) $chars[] = $char_arr[$i];
		}	
		$data[$stepSI]['char'] = $chars ? '<div class="score-list-value">(' . implode(', ', $chars) . ')</div>' : '';	
		// $data[$stepSI]['chars'] = $chars;
	}
    return $data;
}