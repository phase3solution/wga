<?php
// generate question section
function advisory_generate_dmm_form_sections() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = array(
			'type' => 'notice',
			'class' => 'danger',
			'content' => 'Create Section and save. Then create Table from "Tables" tab',
		);
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $area) {
				$data[] = array(
					'id' => 'sections_' . advisory_id_from_string($area['name']),
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
				);
			}
		}
		return $data;
	}
}
function advisory_generate_dmm_form_tables() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = array(
			'type' => 'notice',
			'class' => 'danger',
			'content' => 'Create Table and save. Then create Table Group from "Table Groups" tab',
		);
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $area) {
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($meta[$section_id])) {
					$data[] = array(
						'type' => 'heading',
						'content' => $area['name'],
					);
					foreach ($meta[$section_id] as $section) {
						$data[] = array(
							'id' => $section_id . '_tables_' . advisory_id_from_string($section['name']),
							'type' => 'group',
							'title' => $section['name'],
							'desc' => 'Each Table name should be unique',
							'button_title' => 'Add New',
							'accordion_title' => 'Add New Table',
							'fields' => array(
								array(
									'id' => 'name',
									'type' => 'text',
									'title' => 'Name',
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
function advisory_generate_dmm_form_questions() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$tables = [];
		$data = [];
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $area) {
				$section_id = 'sections_' . advisory_id_from_string($area['name']);
				if (!empty($meta[$section_id])) {
					$data[] = array(
						'type' => 'heading',
						'content' => $area['name'],
					);
					foreach ($meta[$section_id] as $section) {
						$t_id = $section_id . '_tables_' . advisory_id_from_string($section['name']);
						if (!empty($meta[$t_id])) {
							$data[] = array(
								'type' => 'subheading',
								'content' => $section['name'],
							);
							foreach ($meta[$t_id] as $table) {
								$data[] = array(
									'id' => $t_id . '_questions_' . advisory_id_from_string($table['name']),
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