<?php
// generate question section
function advisory_generate_bia_form_services() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = array(
			'type' => 'notice',
			'class' => 'danger',
			'content' => 'Create "Departments" and save. Then create Services',
			// 'content' => help($meta, false)
		);
		if (!empty($meta['areas'])) {
			foreach ($meta['areas'] as $department) {
				$data[] = array(
					'id' => advisory_id_from_string($department['name']) . '_services',
					'type' => 'group',
					'title' => $department['name'],
					'desc' => 'Each Services name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Service',
					'fields' => array(
						array(
							'id' => 'name',
							'type' => 'text',
							'title' => 'Name',
						),
						array(
							'id' => 'financial',
							'type' => 'textarea',
							'title' => 'Financial (per day)',
						),
						array(
							'id' => 'impact',
							'type' => 'textarea',
							'title' => 'Impact',
							'desc' => 'One per line',
						),
						array(
							'id' => 'weight',
							'type' => 'textarea',
							'title' => 'Criteria Weights',
							'desc' => 'One per line',
							'default' => '1:Normal&#13;&#10;2:Important&#13;&#10;3:Vital',
						),
						array(
							'id' => 'field',
							'type' => 'select',
							'title' => 'Impact Criteria Field',
							'options' => advisory_registered_criteria('bia'),
						),
					),
				);
			}
		}
		return $data;
	}
}
function advisory_get_bia_departments() {
	$id = @$_GET['post'];
	$depts = [];
	if ($id) {
		$company = @get_post_meta($id, 'permission', true)['users'];
		if ($company) {
			$arr = @array_column(cs_get_option('departments_'. $company), 'name');
			if ($arr) {
				$depts = array_merge([''=>'Select Department'], array_combine($arr, $arr));
				return $depts;
			}
		}
	}
	return [''=>'Select Department'];
}
function advisory_generate_bia_pdf_subsections($postType='bia') {
	if (is_admin()) {
		$data = [];
		$data[] = ['type' => 'subheading', 'content' => 'Heading'];
		$data[] = ['id' => 'tec_title', 'type' => 'text', 'title' => 'Technoloty Stuff Title', 'default' => 'Quia distinctio non perfere.'];
		$data[] = ['id' => 'tec_desc', 'type' => 'textarea', 'title' => 'Technoloty Stuff Desc', 'default' => demoContent('',1)];
		$data[] = ['id' => 'people_title', 'type' => 'text', 'title' => 'People Stuff Title', 'default' => 'Quia distinctio non perferendis Lorem ipsum'];
		$data[] = ['id' => 'people_desc', 'type' => 'textarea', 'title' => 'People Stuff Desc', 'default' => demoContent('',1)];
		return $data;
	}
}
function advisory_generate_bia_companies() {
	if (is_admin()) {
		$data = [];
		$companies = get_terms(['taxonomy' => 'company', 'hide_empty' => false]);
		// $data[] = ['type' => 'heading', 'content' => '<br><pre>'. print_r($companies, true) .'</pre>'];
		if ($companies) {
			foreach ($companies as $company) {
				$data[] = ['type' => 'subheading', 'content' => $company->name];
				$data[] = [
					'id' => 'departments_'.$company->term_id,
					'type' => 'group',
					'title' => 'Departments',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New Department',
					'fields' => [['id' => 'name', 'type' => 'text', 'title' => 'Name']],
				];
			}
		}
		return $data;
	}
}
function advisory_prepare_catelogue_summary_pdf_data2($services) {
	$data = [];
	if (!empty($services)) {
		foreach ($services as $service) {
			$name = !empty($service['name']) ? $service['name'] : '';
			$tmp = explode('(', str_replace(')', '', $name));
			$ser = !empty($tmp[0]) ? trim($tmp[0]) : '';
			$tecDep = !empty($tmp[1]) ? trim($tmp[1]) : $ser;

			$serviceID = advisory_id_from_string($ser);
			$tecDepID = advisory_id_from_string($tecDep);
			// unset($service['items']);
			if (!empty($service['items']))  {
				$data[$serviceID][] = [
					's_name'=> $ser, 
					'tec_dependency'=> $tecDep, 
					'items' => $service['items']
				];
			}
		}
	}
	return $data;
}
function advisory_prepare_catelogue_summary_pdf_data($services) {
	$data = [];
	if (!empty($services)) {
		foreach ($services as $service) {
			$name = !empty($service['name']) ? $service['name'] : '';
			$tmp = explode('(', str_replace(')', '', $name));
			$ser = !empty($tmp[0]) ? trim($tmp[0]) : '';
			$tecDep = !empty($tmp[1]) ? trim($tmp[1]) : $ser;

			$serviceID = advisory_id_from_string($ser);
			$tecDepID = advisory_id_from_string($tecDep);
			// unset($service['items']);
			if (!empty($service['items']))  {
				foreach($service['items'] as $item) {
					$data[$serviceID][] = ['name' => $ser, 'dependency' => $tecDep] + $item;
				}
			}
		}
	}
	return $data;
}
function advisory_prepare_export_catalogue_summary_data($dependencies) {
	$data = [];
	if (!empty($dependencies['it'])) {
		foreach ($dependencies['it'] as $service) {
			$name = !empty($service['name']) ? $service['name'] : '';
			$tmp = explode('(', str_replace(')', '', $name));
			$ser = !empty($tmp[0]) ? $tmp[0] : '';
			$tecDep = !empty($tmp[1]) ? $tmp[1] : $ser;
			if (!empty($service['items'])) {
				foreach ($service['items'] as $item) {
					$data[] = ['Catalogue' => 'IT Service Catalogue', 'Full Service' => @$service['name'], 'Service' => $ser, 'Technology Dependency' => $tecDep, 'Service/Process' => @$item['service'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'RPO' => $item['rpo'], 'CL' => @$item['cl']['value'], 'Tier' => advisory_rto_to_tier($item['rto'])];
				}
			}
		}
	}
	if (!empty($dependencies['cloud'])) {
		foreach ($dependencies['cloud'] as $service) {
			$name = !empty($service['name']) ? $service['name'] : '';
			$tmp = explode('(', str_replace(')', '', $name));
			$ser = !empty($tmp[0]) ? $tmp[0] : '';
			$tecDep = !empty($tmp[1]) ? $tmp[1] : $ser;
			if (!empty($service['items'])) {
				foreach ($service['items'] as $item) {
					$data[] = ['Catalogue' => 'Cloud Service Catalogue', 'Full Service' => @$service['name'], 'Service' => $ser, 'Technology Dependency' => $tecDep, 'Service/Process' => @$item['service'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'RPO' => $item['rpo'], 'CL' => @$item['cl']['value'], 'Tier' => advisory_rto_to_tier($item['rto'])];
				}
			}
		}
	}
	if (!empty($dependencies['desktop'])) {
		foreach ($dependencies['desktop'] as $service) {
			$name = !empty($service['name']) ? $service['name'] : '';
			$tmp = explode('(', str_replace(')', '', $name));
			$ser = !empty($tmp[0]) ? $tmp[0] : '';
			$tecDep = !empty($tmp[1]) ? $tmp[1] : $ser;
			if (!empty($service['items'])) {
				foreach ($service['items'] as $item) {
					$data[] = ['Catalogue' => 'Desktop Service Catalogue', 'Full Service' => @$service['name'], 'Service' => $ser, 'Technology Dependency' => $tecDep, 'Service/Process' => @$item['service'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'RPO' => $item['rpo'], 'CL' => @$item['cl']['value'], 'Tier' => advisory_rto_to_tier($item['rto'])];
				}
			}
		}
	}
	return $data;
}
function advisory_prepare_export_cloud_service_catalogue_data_for_excel_and_csv($dependencies) {
	return advisory_prepare_export_service_criticality_reportcard_data_for_excel_and_csv($dependencies);
}
function advisory_prepare_export_service_criticality_reportcard_data_for_excel_and_csv($dependencies) {
	$data = [];
	if ( !empty($dependencies) ) {
		foreach ($dependencies as $dependency) {
			$name = false;
			if ( !empty($dependency['name']) ) {
				$full_name = trim($dependency['name']);
				$name = explode( '(', str_replace(')', '', $dependency['name']) );
				unset($dependency['id']);
				unset($dependency['name']);
			}
			// $data[] = $name + $dependency; continue;
			if ( !empty($name) && !empty($dependency) ) {
				$ser = !empty($name[0]) ? $name[0] : '';
				$serID = advisory_id_from_string($ser);
				$tecDep = !empty($name[1]) ? $name[1] : $ser;
				foreach ($dependency as $tier_id => $tiers) {
					if ( !empty($tiers) ) {
						foreach ($tiers as $tier) {
							if ( !empty($tier) ) {
								$tier = explode('&&&', $tier);
								$data[] = [
									'Full Service' => $full_name,
									'Service' => $ser,
									'Technology Dependency' => $tecDep,
									'Department' => @trim($tier[0]),
									'Service/Process' => @trim($tier[1]),
									'RTO' => advisory_rto_id_to_name($tier_id),
									'Tier' => advisory_rto_to_tier($tier_id),
								];
							}
						}
					}
				}
				
			}
		}
	}
	return $data;
}
function advisory_prepare_export_service_criticality_reportcard_data_for_pdf($dependencies) {
	$data = [];
	if ( !empty($dependencies) ) {
		foreach ($dependencies as $dependency) {
			$name = false;
			if ( !empty($dependency['name']) ) {
				$name = explode( '(', str_replace(')', '', $dependency['name']) );
				unset($dependency['id']);
				unset($dependency['name']);
			}
			// $data[] = $name + $dependency; continue;
			if ( !empty($name) && !empty($dependency) ) {
				$ser = !empty($name[0]) ? $name[0] : '';
				$serID = advisory_id_from_string($ser);
				$tecDep = !empty($name[1]) ? $name[1] : $ser;
				foreach ($dependency as $tier_id => $tiers) {
					if ( !empty($tiers) ) {
						foreach ($tiers as $tier) {
							if ( !empty($tier) ) {
								$tier = explode('&&&', $tier);
								$data[$serID][] = [
									'catalogue' => $ser,
									'dependency' => $tecDep,
									'rto' => advisory_rto_id_to_name($tier_id),
									'tier' => advisory_rto_to_tier($tier_id),
									'tier_class' => advisory_rto_to_tier_class($tier_id),
									'department' => @trim($tier[0]),
									'service' => @trim($tier[1]),
								];
							}
						}
					}
				}
				
			}
		}
	}
	return $data;
}