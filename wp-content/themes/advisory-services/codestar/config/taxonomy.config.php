<?php if (!defined('ABSPATH')) {die;} // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// TAXONOMY OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$companyID 	= $_GET['tag_ID'] ?? 0;
$options 	= array();
$options[] 	= array(
	'id' => 'company_data',
	'taxonomy' => 'company',
	'fields' => array(
		[
			'id' => 'static',
			'type' => 'group',
			'title' => 'IHC Graph',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Static',
			'fields' => [
				['id' => 'date', 'type' => 'text', 'title' => 'Date'],
				['id' => 'color', 'type' => 'color_picker', 'title' => 'Color', 'default' => '#cd2122'],
				['id' => 'operations', 'type' => 'text', 'title' => 'Operations Rating'],
				['id' => 'operations_link', 'type' => 'upload', 'title' => 'Operations Link'],
				['id' => 'hardware', 'type' => 'text', 'title' => 'Hardware Rating'],
				['id' => 'hardware_link', 'type' => 'upload', 'title' => 'Hardware Link'],
				['id' => 'software', 'type' => 'text', 'title' => 'Software Rating'],
				['id' => 'software_link', 'type' => 'upload', 'title' => 'Software Link'],
				['id' => 'network', 'type' => 'text', 'title' => 'Network Rating'],
				['id' => 'network_link', 'type' => 'upload', 'title' => 'Network Link'],
				['id' => 'data_management', 'type' => 'text', 'title' => 'Data Management Rating'],
				['id' => 'data_management_link', 'type' => 'upload', 'title' => 'Data Management Link'],
			]
		],
		[
			'id' => 'dr_static',
			'type' => 'group',
			'title' => 'DR Maturity Graph',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New Static',
			'fields' => [
				['id' => 'dr_date', 'type' => 'text', 'title' => 'Date'], 
				['id' => 'dr_color', 'type' => 'color_picker', 'title' => 'Color', 'default' => '#cd2122'],
				['id' => 'organizational_readiness', 'type' => 'text', 'title' => 'Organizational Readiness Rating'],
				['id' => 'organizational_readiness_link', 'type' => 'upload', 'title' => 'Organizational Readiness Link'],
				['id' => 'technology_readiness', 'type' => 'text', 'title' => 'Technology Readiness Rating'],
				['id' => 'technology_readiness_link', 'type' => 'upload', 'title' => 'Technology Readiness Link'],
				['id' => 'recovery_planning', 'type' => 'text', 'title' => 'Recovery Planning Rating'],
				['id' => 'recovery_planning_link', 'type' => 'upload', 'title' => 'Recovery Planning Link'],
				['id' => 'maintenance_sand_improvement', 'type' => 'text', 'title' => 'Maintenance and Improvement Rating'],
				['id' => 'maintenance_sand_improvement_link', 'type' => 'upload', 'title' => 'Maintenance and Improvement Link']
			]
		],
		array(
			'id' => 'mta_static',
			'type' => 'group',
			'title' => 'MTA Trend Analysis',
			'button_title' => 'Add New',
			'accordion_title' => 'Add New MTA',
			'fields' => array(
				array('id' => 'date', 'type' => 'text', 'title' => 'Date', ),
				array('id' => 'color', 'type' => 'color_picker', 'title' => 'Color', 'default' => '#cd2122', ),
				array('id' => 'customer_facing', 'type' => 'text', 'title' => 'Customer Facing Rating', ),
				array('id' => 'customer_facing_link', 'type' => 'upload', 'title' => 'Customer Facing Link', ),
				array('id' => 'integration', 'type' => 'text', 'title' => 'Integration Rating', ),
				array('id' => 'integration_link', 'type' => 'upload', 'title' => 'Integration Link', ),
				array('id' => 'business_solutions', 'type' => 'text', 'title' => 'Business Solutions Rating', ),
				array('id' => 'business_solutions_link', 'type' => 'upload', 'title' => 'Business Solutions Link', ),
				array('id' => 'technology_infrastructure', 'type' => 'text', 'title' => 'Technology Infrastructure Rating', ),
				array('id' => 'technology_infrastructure_link', 'type' => 'upload', 'title' => 'Technology Infrastructure Link', ),
			),
		),
		array(
			'id'             => 'userDashboard',
			'type'           => 'select',
			'title'          => 'Dashboard Type',
			'options'        => ['Dashboard B' => 'Dashboard B'],
			'default_option' => 'Dashboard A',
		),
		array(
			'id'             => 'logo',
			'type'           => 'upload',
			'title'          => 'Company Logo ( 230 X 50 )',
		),
		array(
			'id'             => 'upstream',
			'type'           => 'textarea',
			'title'          => 'Upstream Dependencies',
			'default'        => '1:Network Access (private Circuit)&#13;&#10;2:Telephony (Phone System)&#13;&#10;3:Telephony (Cellular)&#13;&#10;4:Internet/Intranet&#13;&#10;5:File/Print Service&#13;&#10;6:Email&#13;&#10;7:Remote Access',
		),
		array(
			'id'             => 'externalDependency',
			'type'           => 'textarea',
			'title'          => 'External Dependencies',
			'default'        => '1:External Website: Provincial Programs Database “Grants”&#13;&#10;2:External Website: SAMS (Homelessness Programs)&#13;&#10;3:External Website: Yardi -  Property Management and Waitlist Activity&#13;&#10;4:Extend Communications - After hour emergency services through paging system',
		),
		array(
			'id'             => 'desktopDependency',
			'type'           => 'textarea',
			'title'          => 'Desktop Catalogue',
			'default'        => '1:Option 1&#13;&#10;2:Option 2&#13;&#10;3:Option 3&#13;&#10;4:Option 4&#13;&#10;5:Option 5&#13;&#10;6:Option 6&#13;&#10;7:Option 7',
		),
		array(
			'id'             => 'mobile_apps',
			'type'           => 'textarea',
			'title'          => 'Mobile Apps',
			'default'        => '1:Mobile 1&#13;&#10;2:Mobile 2&#13;&#10;3:Mobile 3&#13;&#10;4:Mobile 4&#13;&#10;5:Mobile 5&#13;&#10;6:Mobile 6&#13;&#10;7:Mobile 7',
		),
		array(
			'id'             => 'upstreamDependencies',
			'type'           => 'textarea',
			'title'          => 'Risk Assets',
			'default'        => '1:External Website: Provincial Programs Database “Grants”&#13;&#10;2:External Website: SAMS (Homelessness Programs)&#13;&#10;3:External Website: Yardi -  Property Management and Waitlist Activity&#13;&#10;4:Extend Communications - After hour emergency services through paging system',
		),
		array(
			'id'             => 'bcpvulnerabilities',
			'type'           => 'textarea',
			'title'          => 'Risk Vulnerabilities',
			'default'        => '1:Network Access (private Circuit)&#13;&#10;2:Telephony (Phone System)&#13;&#10;3:Telephony (Cellular)&#13;&#10;4:Internet/Intranet&#13;&#10;5:File/Print Service&#13;&#10;6:Email&#13;&#10;7:Remote Access',
		),
		array(
			'id'             => 'bia',
			'type'           => 'checkbox',
			'title'          => 'BIA For Scorecard',
			'options'        => getAllBIAIDFor($companyID),
		),
		array(
			'id'             => 'sfia_teams',
			'type'           => 'textarea',
			'title'          => 'SFIA Teams',
			'default'        => '1:Network Group&#13;&#10;2:Development Group&#13;&#10;3:Helpdek Group',
		),
		array(
			'id'             => 'sfia_users',
			'type'           => 'textarea',
			'title'          => 'SFIA Users',
			'default'        => 'U001:John Doe&#13;&#10;U002:Micle Sumaka&#13;&#10;U003:Hussain Bolt',
		),
		array(
			'id'             => 'sfia_roles',
			'type'           => 'textarea',
			'title'          => 'SFIA Roles',
			'default'        => '1:Network Admin&#13;&#10;2:Editor&#13;&#10;3:Supervisor',
		),
	),
);
$options[] = [
	'id' => 'risk_type_data',
	'taxonomy' => 'risk_cat',
	'fields' => [
		[
			'id' => 'risk_type_icon',
			'type' => 'upload',
			'title' => 'Risk Icon',
		]
	],
];
CSFramework_Taxonomy::instance($options);