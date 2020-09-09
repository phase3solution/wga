<?php
function advisory_generate_bia_form_threats() {
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
function advisory_generate_bia_form_questions() {
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