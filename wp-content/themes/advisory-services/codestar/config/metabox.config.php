<?php if (!defined('ABSPATH')) {die;} // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// METABOX OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();
// -----------------------------------------
// MTA                                     -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'mta',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
				array(
					'id' => 'criteria_definition',
					'type' => 'switcher',
					'title' => 'Criteria Definition',
					'label' => 'Do you want to display criteria definiiton?',
					'default' => true,
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Area and save. Then create Section from "Sections" tab',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Areas',
					'desc' => 'Each Area name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						['id' => 'name', 'type' => 'text', 'title' => 'Name',], 
						['id' => 'icon_menu', 'type' => 'upload', 'title' => 'Icon (Menu)',],
						['id' => 'icon_title', 'type' => 'upload', 'title' => 'Icon (Title)',],
					),
				),
			),
		),
		['name' => 'sections', 'title' => 'Sections', 'fields' => advisory_generate_mta_form_sections(),],
		['name' => 'tables', 'title' => 'Tables', 'fields' => advisory_generate_mta_form_tables(),],
		['name' => 'questions', 'title' => 'Questions', 'fields' => advisory_generate_mta_form_questions(),],
	),
);
// -----------------------------------------
// MTA Register                            -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'mtar',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => [
		[
			'name' => 'general',
			'title' => 'General',
			'fields' => [
				['id' => 'display_name', 'type' => 'text', 'title' => 'Display Name'],
				['id' => 'desc', 'type' => 'text', 'title' => 'Description'],
				['id' => 'icon', 'type' => 'upload', 'title' => 'Icon']
			],
		],
		[
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => [
				['type' => 'notice', 'class' => 'danger', 'content' => 'Create Areas and save. Then create Threats, and Questions'],
				[
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Threat Cats',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => [
						['id' => 'name', 'type' => 'text', 'title' => 'Name'],
						['id' => 'icon_menu', 'type' => 'upload', 'title' => 'Icon (Menu)'],
						['id' => 'icon_title', 'type' => 'upload', 'title' => 'Icon (Title)'],
						['id' => 'desc', 'type' => 'textarea', 'title' => 'Description', 'sanitize' => false],
						['id' => 'base', 'type' => 'number', 'title' => 'Base']
					]
				]
			]
		],
		['name' => 'threats', 'title' => 'Threats', 'fields' => advisory_generate_mtar_form_threats()],
		['name' => 'subthreats', 'title' => 'Subthreats', 'fields' => advisory_generate_mtar_form_subthreats()]
	],
);
// -----------------------------------------
// IHC                                     -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'ihc',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
				array(
					'id' => 'criteria_definition',
					'type' => 'switcher',
					'title' => 'Criteria Definition',
					'label' => 'Do you want to display criteria definiiton?',
					'default' => true,
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Area and save. Then create Section from "Sections" tab',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Areas',
					'desc' => 'Each Area name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						['id' => 'name', 'type' => 'text', 'title' => 'Name'],
						['id' => 'icon_menu', 'type' => 'upload', 'title' => 'Icon (Menu)'],
						['id' => 'icon_title', 'type' => 'upload', 'title' => 'Icon (Title)'],
						['id' => 'desc', 'type' => 'textarea', 'title' => 'Description', 'sanitize' => false],
					),
				),
			),
		),
		array(
			'name' => 'sections',
			'title' => 'Sections',
			'fields' => advisory_generate_ihc_form_sections(),
		),
		array(
			'name' => 'tables',
			'title' => 'Tables',
			'fields' => advisory_generate_ihc_form_tables(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_ihc_form_questions(),
		),
	),
);
// -----------------------------------------
// IHC Register                            -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'ihcr',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Areas and save. Then create Threats, and Questions',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Threat Cats',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'base',
							'type' => 'number',
							'title' => 'Base',
						),
					),
				),
			),
		),
		array(
			'name' => 'threats',
			'title' => 'Threats',
			'fields' => advisory_generate_ihcr_form_threats(),
		),
	),
);
// -----------------------------------------
// ITSM                                    -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'itsm',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
				array(
					'id' => 'criteria_definition',
					'type' => 'switcher',
					'title' => 'Criteria Definition',
					'label' => 'Do you want to display criteria definiiton?',
					'default' => true,
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Area and save. Then create Section from "Sections" tab',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Areas',
					'desc' => 'Each Area name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
					),
				),
			),
		),
		array(
			'name' => 'sections',
			'title' => 'Sections',
			'fields' => advisory_generate_itsm_form_sections(),
		),
		array(
			'name' => 'tables',
			'title' => 'Tables',
			'fields' => advisory_generate_itsm_form_tables(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_itsm_form_questions(),
		),
	),
);
// -----------------------------------------
// drm                                    -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'drm',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
				array(
					'id' => 'criteria_definition',
					'type' => 'switcher',
					'title' => 'Criteria Definition',
					'label' => 'Do you want to display criteria definiiton?',
					'default' => true,
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Area and save. Then create Section from "Sections" tab',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Areas',
					'desc' => 'Each Area name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'color',
							'type' => 'color_picker',
							'title' => 'Accent Color',
						),
					),
				),
			),
		),
		array(
			'name' => 'sections',
			'title' => 'Sections',
			'fields' => advisory_generate_drm_form_sections(),
		),
		array(
			'name' => 'tables',
			'title' => 'Tables',
			'fields' => advisory_generate_drm_form_tables(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_drm_form_questions(),
		),
	),
);
// -----------------------------------------
// DOCL                                    -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'docl',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'documents',
			'title' => 'Documents',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create documents and save.',
				),
				array(
					'id' => 'documents',
					'type' => 'group',
					'title' => 'Documents',
					'desc' => 'Each document name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Document',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Document Name',
						),
						array(
							'id' => 'document_upload',
							'type' => 'upload',
							'title' => 'Document',
						),
						array( 
							'id' => 'date_picker', 
							'type' => 'text', 
							'title' => __( 'Upload Date', 'date' ), 
							'attributes' => array( 'type' => 'date', ), 
						),
						array(
							'id' => 'comments',
							'type' => 'textarea',
							'title' => 'Comments',
							'sanitize' => false,
						),
						array(
							  'id'             => 'company',
							  'type'           => 'select',
							  'title'          => 'Select Company',
							  'options'        => advisory_registered_companies(),
							  'default_option' => 'Select a company',
							),
					),
				),
			),
		),
	),
);
// -----------------------------------------
// CRA                                     -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'cra',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
				array(
					'id' => 'area_image',
					'type' => 'upload',
					'title' => 'Area Image',
					'desc' => 'Upload <strong style="color:red">700X700 JPG</strong> image',
				),
				array(
					'id' => 'criteria_definition',
					'type' => 'switcher',
					'title' => 'Criteria Definition',
					'label' => 'Do you want to display criteria definiiton?',
					'default' => true,
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Area and save. Then create Section from "Sections" tab',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Areas',
					'desc' => 'Each Area name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'upload',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'color',
							'type' => 'color_picker',
							'title' => 'Accent Color',
						),
					),
				),
			),
		),
		array(
			'name' => 'sections',
			'title' => 'Sections',
			'fields' => advisory_generate_cra_form_sections(),
		),
		array(
			'name' => 'tables',
			'title' => 'Tables',
			'fields' => advisory_generate_cra_form_tables(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_cra_form_questions(),
		),
	),
);
// -----------------------------------------
// BIA                                     -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'bia',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Departments',
			'fields' => array(
				['type' => 'notice', 'class' => 'danger', 'content' => 'Create Departments and save. Then create Services'],
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Departments',
					'desc' => 'Each Department name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => [
						// ['type' => 'notice', 'class' => 'danger', 'content' => advisory_get_bia_departments()],
						['id' => 'name', 'type' => 'select', 'title' => 'Name', 'options' => advisory_get_bia_departments()],
						// ['id' => 'name', 'type' => 'text', 'title' => 'Name'],
						// ['id' => 'test124', 'type' => 'text', 'title' => 'Name', 'default' => 'advisory_get_bia_departments'],
						['id' => 'icon_menu', 'type' => 'upload', 'title' => 'Icon (Menu)'],
						['id' => 'icon_title', 'type' => 'upload', 'title' => 'Icon (Title)'],
						['id' => 'gq_nosp', 'type' => 'number', 'title' => 'GQ: Num Row for SP', 'default' => 4],
						['id' => 'gq_nobl', 'type' => 'number', 'title' => 'GQ: Num Row for BL', 'default' => 4],
						// ['id' => 'se_nomsl', 'type' => 'number', 'title' => 'SE: Num Row for 2', 'default' => 4],
						// ['id' => 'se_nolif', 'type' => 'number', 'title' => 'SE: Num Row for 4', 'default' => 4],
						// ['id' => 'se_nolef', 'type' => 'number', 'title' => 'SE: Num Row for 5', 'default' => 4],
						['id' => 'se_nodosp', 'type' => 'number', 'title' => 'SE: Num Row for 6 (Vital Records)', 'default' => 4],
						['id' => 'se_nodosp2', 'type' => 'number', 'title' => 'SE: Num Row for 6 (Technology Required)', 'default' => 4],
						['id' => 'se_q7_mnac', 'type' => 'number', 'title' => 'SE: Num Row for 7 (MODES OF NITIFICATION AND COMMUNICATION)', 'default' => 4],
						['id' => 'se_q7_dcl', 'type' => 'number', 'title' => 'SE: Num Row for 7 (DEPARTMENT CONTACT LIST)', 'default' => 4],
						['id' => 'se_q7_ecl', 'type' => 'number', 'title' => 'SE: Num Row for 7 (EXTERNAL CONTACT LIST)', 'default' => 4],
						['id' => 'se_q9_oid', 'type' => 'number', 'title' => 'SE: Num Row for 9 (OTHER INTERNAL DEPENDENCIES)', 'default' => 0],
						['id' => 'cp_q1_epct', 'type' => 'number', 'title' => 'CP: Num Row for 1 (ESSENTIAL PERSONNEL AND CROSS-TRAINING)', 'default' => 0],
					],
				),
			),
		),
		['name' => 'services', 'title' => 'Services', 'fields' => advisory_generate_bia_form_services()],
	),
);
// -----------------------------------------
// Risk Register                           -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'risk',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Threat Cats',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Threat Cats and save. Then create Threats, and Questions',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Threat Cats',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'base',
							'type' => 'number',
							'title' => 'Base',
						),
					),
				),
			),
		),
		array(
			'name' => 'threats',
			'title' => 'Threats',
			'fields' => advisory_generate_bia_form_threats(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_bia_form_questions(),
		),
	),
);
// -----------------------------------------
// BCP Register                           -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'bcp',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Threat Cats',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Threat Cats and save. Then create Threats, and Questions',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Threat Cats',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'base',
							'type' => 'number',
							'title' => 'Base',
						),
					),
				),
			),
		),
		array(
			'name' => 'threats',
			'title' => 'Threats',
			'fields' => advisory_generate_bcp_form_threats(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_bcp_form_questions(),
		),
	),
);
// -----------------------------------------
// Project Risk Register                   -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'prr',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Threat Cats',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Threat Cats and save. Then create Threats, and Questions',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Threat Cats',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'base',
							'type' => 'number',
							'title' => 'Base',
						),
					),
				),
			),
		),
		array(
			'name' => 'threats',
			'title' => 'Threats',
			'fields' => advisory_generate_prr_form_threats(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_prr_form_questions(),
		),
	),
);
// -----------------------------------------
// DR Maturity Register               -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'drmrr',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Areas and save. Then create Threats, and Questions',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Threat Cats',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'base',
							'type' => 'number',
							'title' => 'Base',
						),
					),
				),
			),
		),
		array(
			'name' => 'threats',
			'title' => 'Threats',
			'fields' => advisory_generate_prr_form_threats(),
		),
	),
);
// -----------------------------------------
// Data Management Maturity Assessment     -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'dmm',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
				array(
					'id' => 'criteria_definition',
					'type' => 'switcher',
					'title' => 'Criteria Definition',
					'label' => 'Do you want to display criteria definiiton?',
					'default' => true,
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Area and save. Then create Section from "Sections" tab',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Areas',
					'desc' => 'Each Area name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
					),
				),
			),
		),
		array(
			'name' => 'sections',
			'title' => 'Sections',
			'fields' => advisory_generate_dmm_form_sections(),
		),
		array(
			'name' => 'tables',
			'title' => 'Tables',
			'fields' => advisory_generate_dmm_form_tables(),
		),
		array(
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => advisory_generate_dmm_form_questions(),
		),
	),
);
// -----------------------------------------
// Data Management Maturity Register       -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'dmmr',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Areas and save. Then create Threats, and Questions',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Threat Cats',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'icon_menu',
							'type' => 'upload',
							'title' => 'Icon (Menu)',
						),
						array(
							'id' => 'icon_title',
							'type' => 'upload',
							'title' => 'Icon (Title)',
						),
						array(
							'id' => 'desc',
							'type' => 'textarea',
							'title' => 'Description',
							'sanitize' => false,
						),
						array(
							'id' => 'base',
							'type' => 'number',
							'title' => 'Base',
						),
					),
				),
			),
		),
		array(
			'name' => 'threats',
			'title' => 'Threats',
			'fields' => advisory_generate_prr_form_threats(),
		),
	),
);
// -----------------------------------------
// IT corporate Landscape 			       -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'itcl',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		array(
			'name' => 'general',
			'title' => 'General',
			'fields' => array(
				array(
					'id' => 'display_name',
					'type' => 'text',
					'title' => 'Display Name',
				),
				array(
					'id' => 'desc',
					'type' => 'text',
					'title' => 'Description',
				),
				array(
					'id' => 'icon',
					'type' => 'upload',
					'title' => 'Icon',
				),
			),
		),
		array(
			'name' => 'areas',
			'title' => 'Areas',
			'fields' => array(
				array(
					'type' => 'notice',
					'class' => 'danger',
					'content' => 'Create Areas and save.',
				),
				array(
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Area',
					'desc' => 'Each Threat Cat name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
					),
					'default' => [['name' => 'General']],
				),
			),
		),
	),
);
// -----------------------------------------
// CSA 								       -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'csa',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => [
		[
			'name' => 'general',
			'title' => 'General',
			'fields' => [
				['id' => 'display_name', 'type' => 'text', 'title' => 'Display Name'],
				['id' => 'desc', 'type' => 'text', 'title' => 'Description'],
				['id' => 'icon', 'type' => 'upload', 'title' => 'Icon']
			]
		],
		[
			'name' => 'sections',
			'title' => 'Sections',
			'fields' => [
				['type' => 'notice', 'class' => 'danger', 'content' => 'Create Sections and save. Then creat Domains.'],
				[
					'id' => 'sections',
					'type' => 'group',
					'title' => 'Section',
					'desc' => 'Each Domain name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Domain',
					'fields' => [['id' => 'name', 'type' => 'text', 'title' => 'Name'], ['id' => 'title', 'type' => 'text', 'title' => 'Title'] ],
					'default' => [['name' => 'OVERVIEW']]
				]
			]
		],
		['name' => 'domains', 'title' => 'Domains', 'fields' => advisory_generate_csa_form_domains()],
		['name' => 'services', 'title' => 'Areas', 'fields' => advisory_generate_csa_form_services()],
	],
);
// -----------------------------------------
// SFIA                                     -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'sfia',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		[
			'name' => 'general',
			'title' => 'General',
			'fields' => [
				['id' => 'display_name', 'type' => 'text', 'title' => 'Display Name', ],
				['id' => 'desc', 'type' => 'text', 'title' => 'Description', ],
				// ['id' => 'icon', 'type' => 'upload', 'title' => 'Icon', ],
			] 
		],
		[
			'name' => 'areas',
			'title' => 'Categories',
			'fields' => [
				['type' => 'notice', 'class' => 'danger', 'content' => 'Create Category and save. Then create Sub-category from "Sub-areas" tab'],
				[
					'id' => 'areas',
					'type' => 'group',
					'title' => 'Categories',
					'desc' => 'Each Category name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Category',
					'fields' => [
						['id' => 'name', 'type' => 'text', 'title' => 'Name',], 
						['id' => 'icon_menu', 'type' => 'upload', 'title' => 'Icon (Menu)',],
						['id' => 'icon_title', 'type' => 'upload', 'title' => 'Icon (Title)',]
					],
				]
			]
		],
		['name' => 'sections', 'title' => 'Sub-categories', 'fields' => advisory_generate_sfia_form_subcategories()],
		['name' => 'tables', 'title' => 'Skills', 'fields' => advisory_generate_sfia_form_skills()]
	),
);
// -----------------------------------------
// SFIA REGISTER                           -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'sfiar',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => array(
		[
			'name' => 'general',
			'title' => 'General',
			'fields' => [
				['id' => 'display_name', 'type' => 'text', 'title' => 'Display Name', ],
				['id' => 'desc', 'type' => 'text', 'title' => 'Description', ],
				// ['id' => 'icon', 'type' => 'upload', 'title' => 'Icon', ],
			] 
		],
	),
);
// -----------------------------------------
// SFIA Technical Survey                                    -
// -----------------------------------------
$options[] = array(
	'id' => 'form_opts',
	'title' => 'Form Options',
	'post_type' => 'sfiats',
	'context' => 'normal',
	'priority' => 'default',
	'sections' => [
		[
			'name' => 'general',
			'title' => 'General',
			'fields' => [
				['id' => 'display_name', 'type' => 'text', 'title' => 'Display Name'],
				['id' => 'desc', 'type' => 'text', 'title' => 'Description'],
				['id' => 'icon', 'type' => 'upload', 'title' => 'Icon'],
				// ['id' => 'user', 'type' => 'select', 'title' => 'User', 'options'=> advisory_generate_sfiats_form_users()],
			],
		],
		[
			'name' => 'questions',
			'title' => 'Questions',
			'fields' => [
				[
					'id' => 'questions',
					'type' => 'group',
					'title' => 'Questions',
					'desc' => 'Add Questions here',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Area',
					'fields' => [
						['id' => 'name', 'type' => 'text', 'title' => 'Name'], 
						['id' => 'desc', 'type' => 'text', 'title' => 'Description']
					]
				]
			]
		]
	],
);

// -----------------------------------------
// Permissions                             -
// -----------------------------------------
$options[] = array(
	'id' => 'permission',
	'title' => 'Enabled Company',
	'post_type' => array_merge(json_decode(ALL_POST_TYPES), ['csa', 'sfiats', 'sfiar']),
	// 'post_type' => json_decode(ALL_POST_TYPES),
	'context' => 'side',
	'priority' => 'default',
	'sections' => [['name' => 'users', 'fields' => [['id' => 'users', 'type' => 'radio', 'options' => advisory_registered_companies()]]]],
);
CSFramework_Metabox::instance($options);