<?php if (!defined('ABSPATH')) {die;} // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK SETTINGS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$settings = array(
	'menu_title' => 'Phase3 Options',
	'menu_type' => 'menu', // menu, submenu, options, theme, etc.
	'menu_slug' => 'p3s-options',
	'ajax_save' => false,
	'show_reset_all' => false,
	'framework_title' => 'Phase3 Options',
	'menu_capability' => 'manage_options',
);
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();
// ------------------------------
// PDF 			                -
// ------------------------------
// ----------------------------------------
// Criteria Rating and Definitions
// ----------------------------------------
// $options[] = ['name' => 'csa_pdf_section', 'title' => 'CSA PDF', 'icon' => 'fa fa-book', 'fields' => advisory_generate_csa_pdf_subsections()];
$options[] = ['name' => 'bia_pdf_section', 'title' => 'BIA PDF', 'icon' => 'fa fa-book', 'fields' => advisory_generate_bia_pdf_subsections()];
$options[] = ['name' => 'ihc_pdf_section', 'title' => 'IHC PDF', 'icon' => 'fa fa-book', 'fields' => advisory_generate_ihc_pdf_subsections()];
$options[] = ['name' => 'mta_pdf_section', 'title' => 'MTA PDF', 'icon' => 'fa fa-book', 'fields' => advisory_generate_mta_pdf_subsections()];
$options[] = ['name' => 'departments_bia', 'title' => 'Departments (BIA)', 'icon' => 'fa fa-book', 'fields' => advisory_generate_bia_companies()];
$options[] = array(
	'name' => 'forms',
	'title' => 'Forms',
	'icon' => 'fa fa-star',
	'fields' => array(
		array(
			'id' => 'criteria_itsm',
			'type' => 'group',
			'title' => 'Criteria Options (IT Management)',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Criteria',
			'fields' => array(
				['id' => 'id', 'type' => 'text', 'title' => 'ID'],
				['id' => 'name', 'type' => 'text', 'title' => 'Name'],
				['id' => 'label', 'type' => 'text', 'title' => 'label'],
				['id' => 'options', 'type' => 'textarea', 'title' => 'Options'],
			),
			'default' => [
				[
					'id' => 'it_management',
					'name' => 'IT Management',
					'label' => 'IT Management',
					'options' => '0:N\A&#13;&#10;1:Does Not Exist&#13;&#10;2:Ad-Hoc&#13;&#10;3:Defined&#13;&#10;4:Well managed&#13;&#10;5:Optimizing',
				],
			],
		),
		array(
			'id' => 'criteria_sfiats',
			'type' => 'textarea',
			'title' => 'Technical Survey',
			'default' => '0:Very Poor&#13;&#10;1:Poor&#13;&#10;2:Average&#13;&#10;3:Good&#13;&#10;4:Very Good&#13;&#10;5:Excellent',
		),

		array(
			'id' => 'criteria_drm',
			'type' => 'group',
			'title' => 'Criteria Options (DR Maturity)',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Criteria',
			'fields' => array(
				['id' => 'id', 'type' => 'text', 'title' => 'ID'],
				['id' => 'name', 'type' => 'text', 'title' => 'Name'],
				['id' => 'label', 'type' => 'text', 'title' => 'label'],
				['id' => 'options', 'type' => 'textarea', 'title' => 'Options'],
			),
			'default' => array(
				array(
					'id' => 'dr_readiness',
					'name' => 'DR Maturity',
					'label' => 'DR Maturity',
					'options' => '0:N\A&#13;&#10;1:No&#13;&#10;2:Ready to Start&#13;&#10;3:In Progress&#13;&#10;4:Ready for Review&#13;&#10;5:Complete/Production',
				),
			),
		),
		array(
			'id' => 'criteria_cra',
			'type' => 'group',
			'title' => 'Criteria Options (Cloud Readiness)',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Criteria',
			'fields' => array(
				['id' => 'id', 'type' => 'text', 'title' => 'ID'],
				['id' => 'name', 'type' => 'text', 'title' => 'Name'],
				['id' => 'label', 'type' => 'text', 'title' => 'label'],
				['id' => 'options', 'type' => 'textarea', 'title' => 'Options'],
			),
			'default' => array(
				array(
					'id' => 'cloud_readiness',
					'name' => 'Cloud Readiness',
					'label' => 'Cloud Readiness',
					'options' => '0:N\A&#13;&#10;1:No&#13;&#10;2:Ready to Start&#13;&#10;3:In Progress&#13;&#10;4:Ready for Review&#13;&#10;5:Complete/Production',
				),
			),
		),
		array(
			'id' => 'criteria_bia',
			'type' => 'group',
			'title' => 'Criteria Options (BIA)',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Criteria',
			'fields' => array(
				['id' => 'id', 'type' => 'text', 'title' => 'ID'],
				['id' => 'name', 'type' => 'text', 'title' => 'Name'],
				['id' => 'label', 'type' => 'text', 'title' => 'label'],
				['id' => 'options', 'type' => 'textarea', 'title' => 'Options'],
			),
			'default' => array(
				array(
					'id' => 'bia',
					'name' => 'BIA',
					'label' => 'BIA',
					'options' => '0:No to Low&#13;&#10;1:Low&#13;&#10;2:Moderate&#13;&#10;3:High&#13;&#10;4:High to Catastrophic',
				),
			),
		),
		array(
			'id' => 'criteria_risk',
			'type' => 'group',
			'title' => 'Criteria Options (RISK)',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Criteria',
			'fields' => array(
				['id' => 'id', 'type' => 'text', 'title' => 'ID'],
				['id' => 'name', 'type' => 'text', 'title' => 'Name'],
				['id' => 'label', 'type' => 'text', 'title' => 'label'],
				['id' => 'options', 'type' => 'textarea', 'title' => 'Options'],
			),
			'default' => array(
				[
					'id' => 'na',
					'name' => 'N\A',
					'label' => 'N\A',
					'options' => '0:None&#13;&#10;1:Low&#13;&#10;2:Medium&#13;&#10;3:High&#13;&#10;4:Extreme',
				],
				[
					'id' => 'impact',
					'name' => 'Impact',
					'label' => 'Impact',
					'options' => '1:None&#13;&#10;1:Low&#13;&#10;2:Medium&#13;&#10;3:High&#13;&#10;4:Extreme',
				],
				[
					'id' => 'probability',
					'name' => 'Probability',
					'label' => 'Probability',
					'options' => '1:None&#13;&#10;1:Low&#13;&#10;2:Medium&#13;&#10;3:Likely&#13;&#10;4:Very Likely',
				],
				[
					'id' => 'mitigation',
					'name' => 'Mitigation',
					'label' => 'Mitigation',
					'options' => '0:None&#13;&#10;1:Low&#13;&#10;2:Medium&#13;&#10;3:High',
				],
				[
					'id' => 'bool',
					'name' => 'Yes or No',
					'label' => 'Yes or No',
					'options' => '0:Yes&#13;&#10;16:No',
				],
			),
		),
		array(
			'id' => 'level_importance',
			'type' => 'textarea',
			'title' => 'Level Of Importance',
			'default' => '1:High&#13;&#10;0.5:Moderate&#13;&#10;0.25:Low',
		),
		array(
			'id' => 'criteria_rating_definitions',
			'type' => 'wysiwyg',
			'title' => 'Criteria Ratings Definitions',
			'settings' => array(
				'media_buttons' => false,
			),
			'default' => '<h5><strong>IT Management Maturity</strong></h5> <ol> <li>Ad hoc – the starting point for a new or undocumented/repeatable processes</li><li>Repeatable – the process is documented/repeatable</li><li>Defined – the process is defined as a business standard</li><li>Well Managed – the process is quantitatively managed with documented metrics</li><li>Optimized – includes process management with continued improvement though feedback and the introduction of innovative process enhancement</li></ol> <h5><strong>Meets Needs (Usability)</strong></h5> <ol> <li>Does Not Meet Needs – lowest level of readiness for a component that does not meet the business technology requirements or has not been implemented</li><li>Barely Meets Needs – basic level of readiness that addresses a small subset of requirements</li><li>Somewhat Meets Needs – most business technology requirements are met, however there are both current and future needs that may not be addressed</li><li>Meets Needs – all current business technology requirements are met</li><li>Fully Meets Needs – highest level of readiness that satisfies both existing needs and technology roadmaps</li></ol> <h5><strong>Outage Frequency (Reliability)</strong></h5> <ol> <li>Very Often – component exhibits an extreme lack of stability</li><li>Often – component experiences regular outages</li><li>Occasionally – component experiences one or more outages per year</li><li>Infrequently – component is stable and rarely experiences an outage</li><li>Never – component has never experienced an outage</li></ol> <h5><strong>Up to Date Architecture (Obsolescence)</strong></h5> <ol> <li>Unsupported – there is no vendor/resource support</li><li>Approaching Unsupported – currently supported but approaching end of life (EOL) in less than one year</li><li>Supported – currently supported but approaching end of life (EOL) in less than two years</li><li>Implemented Last 2 Years – fairly new and fully supported</li><li>New – implemented in the last six months</li></ol> <h5><strong>Cost/Effort to Maintain</strong></h5> <span>Direct Costs: Hardware, Software, Operations, Administration <br>Indirect Costs: Downtime, End User Operations</span> <ol> <li>Very High/Very Frequently – substantial cost to maintain and support – requires dedicated support team</li><li>High/Frequent – high operational costs with regular maintenance and support</li><li>Moderate/Periodically – costs are reasonable with occasional support/maintenance</li><li>Low/Frequent – both direct/indirect costs are low</li><li>Very Low/Zero Touch – direct costs are very low – no requirements for internal or external support</li></ol> <h5><strong>Level of Acceptance</strong></h5> <ol> <li>Missing – does not exist</li><li>Inadequate – exists but lacks the requirements for the organization</li><li>Needs Improvement – some of the requirements are met</li><li>Meets Needs – requirements are met</li><li>Exceeds Needs – requirements are met and future needs may be addressed in the existing form</li></ol> <h5><strong>Alignment with Business</strong></h5> <ol> <li>Not Aligned – Business objectives have not been factored into the discipline</li><li>Barely Aligned - The discipline may meet some business objectives</li><li>Somewhat Aligned –Some business objectives have been aligned with the discipline</li><li>Aligned –Business objectives are aligned</li><li>Closely Aligned - Business objectives are aligned within a formal IT Governance model</li></ol>',
		),
		array(
			'id' => 'dmm_rating_criteria',
			'type' => 'wysiwyg',
			'title' => 'DMM Rating Criteria',
			'settings' => array(
				'media_buttons' => true,
			),
			'default' => '<h5><strong>IT Management Maturity</strong></h5> ',
		),
	),
);

// ------------------------------
// backup                       -
// ------------------------------
$options[] = array(
	'name' => 'backup_section',
	'title' => 'Backup',
	'icon' => 'fa fa-shield',
	'fields' => array(
		array(
			'type' => 'notice',
			'class' => 'warning',
			'content' => 'You can save your current options. Download a Backup and Import.',
		),
		array(
			'type' => 'backup',
		),
	),
);
CSFramework::instance($settings, $options);