<?php
function advisory_generate_dmmr_form_threats() {
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
					),
				);
			}
		}
		return $data;
	}
}
function advisory_generate_dmmr_form_questions() {
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
				if ($threats = $meta[advisory_id_from_string($threat_cat['name']) . '_threats']) {
					foreach ($threats as $threat) {
						$data[] = array(
							'id' => advisory_id_from_string($threat_cat['name']) . '_threat_' . advisory_id_from_string($threat['name']) . '_fields',
							'type' => 'group',
							'title' => $threat['name'],
							'button_title' => 'Add New',
							'accordion_title' => 'Add New Question',
							'fields' => array(
								array(
									'id' => 'name',
									'type' => 'text',
									'title' => 'Name',
								),
								array(
									'id' => 'field_1_id',
									'type' => 'select',
									'title' => 'Field 1',
									'options' => advisory_registered_criteria('dmmr'),
								), array(
									'id' => 'field_2_id',
									'type' => 'select',
									'title' => 'Field 2',
									'options' => advisory_registered_criteria('dmmr'),
								), array(
									'id' => 'field_3_id',
									'type' => 'select',
									'title' => 'Field 3',
									'options' => advisory_registered_criteria('dmmr'),
								), array(
									'id' => 'field_4_id',
									'type' => 'select',
									'title' => 'Field 4',
									'options' => advisory_registered_criteria('dmmr'),
								),
							),
						);
					}
				}
			}
		}
		return $data;
	}
}