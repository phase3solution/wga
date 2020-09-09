<?php
function advisory_generate_ihcr_form_threats() {
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
					'accordion_title' => 'Add New Threat',
					'fields' => [
						['id' => 'name', 'type' => 'text', 'title' => 'Name'],
						['id' => 'desc', 'type' => 'textarea', 'title' => 'Description']
					]
				);
			}
		}
		return $data;
	}
}
function advisory_generate_ihcr_form_questions() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create Threat Cats and save. Then create Threats, and Questions'];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $threat_cat) {
				$data[] = ['type' => 'subheading', 'content' => $threat_cat['name']];
				if ($threats = $meta[advisory_id_from_string($threat_cat['name']) . '_threats']) {
					foreach ($threats as $threat) {
						$data[] = [
							'id' => advisory_id_from_string($threat_cat['name']) . '_threat_' . advisory_id_from_string($threat['name']) . '_fields',
							'type' => 'group',
							'title' => $threat['name'],
							'button_title' => 'Add New',
							'accordion_title' => 'Add New Question',
							'fields' => [
								['id' => 'name', 'type' => 'text', 'title' => 'Name'],
								['id' => 'field_1_id', 'type' => 'select', 'title' => 'Field 1', 'options' => advisory_registered_criteria('ihcr')],
								['id' => 'field_2_id', 'type' => 'select', 'title' => 'Field 2', 'options' => advisory_registered_criteria('ihcr')],
								['id' => 'field_3_id', 'type' => 'select', 'title' => 'Field 3', 'options' => advisory_registered_criteria('ihcr')],
								['id' => 'field_4_id', 'type' => 'select', 'title' => 'Field 4', 'options' => advisory_registered_criteria('ihcr')]
							]
						];
					}
				}
			}
		}
		return $data;
	}
}