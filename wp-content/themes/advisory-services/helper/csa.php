<?php
function advisory_transient_csa_avg($post) {
	$data = [
		1 => ['value'=>0, 'name'=>'Recover'],
		2 => ['value'=>0, 'name'=>'Identify'],
		3 => ['value'=>0, 'name'=>'Protect'],
		4 => ['value'=>0, 'name'=>'Detect'],
		5 => ['value'=>0, 'name'=>'Respond'],
	];
	$ops = get_post_meta($post->ID, 'form_opts', true);
	foreach ($ops['sections'] as $section) {
		$sectionID = advisory_id_from_string($section['name']);
		if (($sectionID == 'overview') || ($sectionID == 'risk')) continue;
		$sectionID = $sectionID .'_section';
		if (!empty($ops[$sectionID])) {
			foreach ($ops[$sectionID] as $domainSI => $domain) {
				$domainID = advisory_id_from_string($domain['name']) . '_domains';
				$defaultID = $sectionID.'_'.$domainID.'_csa';
                $default = advisory_form_default_values($post->ID, $defaultID);
                // $data[$domainSI]['value'] = !empty($default['avg']) ? $default['avg'] : 0;
            }
        }
	}
	return $data;
}
function advisory_template_csa_areas($template_id): array{
	$form_meta = get_post_meta($template_id, 'form_opts', true);
	$areas = [];
	if (!empty($form_meta['sections'])) {
		foreach ($form_meta['sections'] as $section) {
			$sectionID = advisory_id_from_string($section['name']) .'_section';
			if ($form_meta[$sectionID]) {
				foreach ($form_meta[$sectionID] as $domain) {
					$areas[] = $domain['name'];
				}
			}
		}
	}
	return $areas;
}
// generate services section
function advisory_generate_csa_form_domains() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create Domains and save. Then creat Areas.'];
		if (!empty($meta['sections'])) {
			foreach ($meta['sections'] as $section) {
				$sectionID = advisory_id_from_string(@$section['name']);
				if ($sectionID == 'overview') continue;
				$data[] = array(
					'id' => $sectionID .'_section',
					'type' => 'group',
					'title' => @$section['name'],
					'desc' => 'Each Domain name should be unique',
					'button_title' => 'Add New',
					'accordion_title' => 'Add New',
					'fields' => [
						['id' => 'name', 'type' => 'text', 'title' => 'Name'],
						['id' => 'title', 'type' => 'text', 'title' => 'Title']
					]
				);
			}
		}
		return $data;
	}
}
function advisory_generate_csa_form_services() {
	if (is_admin() && is_edit_page()) {
		$id = @$_GET['post'];
		$meta = get_post_meta($id, 'form_opts', true);
		$data = [];
		$data[] = ['type' => 'notice', 'class' => 'danger', 'content' => 'Create "Areas" and save.'];
		if (!empty($meta['sections'])) {
			foreach ($meta['sections'] as $section) {
				$sectionID = advisory_id_from_string(@$section['name']);
				if ($sectionID == 'overview') continue;
				$sectionIDMeta = $sectionID.'_section';
				$data[] = ['type' => 'heading', 'class' => 'danger', 'content' => @$section['name']];
				if (!empty($meta[$sectionIDMeta])) {
					foreach ($meta[$sectionIDMeta] as $domain) {
						$domainID = advisory_id_from_string($domain['name']);
						// $data[] = ['type' => 'notice', 'class' => 'danger', 'content' => $domainID];
						$data[] = array(
							'id' => $domainID . '_domains',
							'type' => 'group',
							'title' => $domain['name'],
							'desc' => 'Each Area name should be unique',
							'button_title' => 'Add New',
							'accordion_title' => 'Add New Area',
							'fields' => [['id' => 'name', 'type' => 'text', 'title' => 'Name']],
						);
					}
				}
			}
		}
		return $data;
	}
}
function getActiveCSAMenu($postID=0) {
	if (!$postID) {
		$postType = 'csa';
		if (advisory_metrics_in_progress(advisory_get_user_company_id(), [$postType])) {
	        $postID = advisory_get_active_forms(advisory_get_user_company_id(), [$postType]);
	    } else {
	        $id = new WP_Query([
	            'post_type' => $postType,
	            'post_status' => 'archived',
	            'posts_per_page' => 1,
	            'meta_query' => [[
	                'key' => 'assigned_company',
	                'value' => advisory_get_user_company_id(),
	            ]],
	            'fields' => 'ids',
	        ]);
	        if ($id->found_posts > 0) {
	            $postID = $id->posts;
	        }
	    }
	} else $postID = [$postID];
    if (!empty($postID[0])) {
		$form_meta = get_post_meta($postID[0], 'form_opts', true);
		echo '<li><a href="#"><img src="' . $form_meta['icon'] .'" alt=""><span>' . advisory_get_form_name($postID[0]) .'</span></a>';
	        if (!empty($form_meta['sections'])) {
	            echo '<ul class="treeview-menu">';
	            foreach ($form_meta['sections'] as $section) {
	            	$sectionID = advisory_id_from_string($section['name']);
	            	if ($sectionID == 'overview') {
	            		echo '<li><a href="' . get_the_permalink($postID[0]) . '?area=' . advisory_id_from_string($section['name']) . '"><img src="' . (empty($section['icon_menu']) ? $form_meta['icon'] : $section['icon_menu']) .'"><span>' . $section['name'] . '</span></a></li>';
	            	} else {
	            		$sectionIDMeta = $sectionID .'_section';
	            		echo '<li><a href="#"><img src="' . $form_meta['icon'] .'" alt=""><span>' . $section['name'] .'</span></a>';
	            			if ($form_meta[$sectionIDMeta]) {
			            		echo '<ul class="treeview-menu">';
		            				foreach ($form_meta[$sectionIDMeta] as $domain) {
			                			echo '<li style="margin-left: 10px;font-size:17px;color:#fff;"><a href="' . get_the_permalink($postID[0]) . '?area=' . advisory_id_from_string($domain['name']) . '">'. $domain['name'] .'</a></li>';
		            				}
			            		echo '</ul>';
	            			}
	            		echo '</li>';
	            	}
	            }
	    	echo '</ul>';
	        }
	    echo '</li>';
    }
}
function customExcerpt($str, $words=4, $dots='...') {
	if (!$str) return '';
	$pstr =  str_replace(['<p>', '</p>'], '', $str);
	$pieces = explode(" ",$pstr, $words +1 );
	return implode(' ', array_splice($pieces, 0, $words)).$dots;
}
function getActiveCSAClass($value=0, $type='') {
	if ($type == 'rate') {
		$data = ['least' => '', 'minimal' => '', 'moderate' => '', 'significant' => '', 'most' => ''];
		switch ($value) {
			case 5: $data['most'] 			= 'active'; break;
			case 4: $data['significant'] 	= 'active'; break;
			case 3: $data['moderate'] 		= 'active'; break;
			case 2: $data['minimal'] 		= 'active'; break;
			case 1: $data['least'] 			= 'active'; break;
			default: break;
		}
	} else {
		$data = ['baseline' => '', 'evolving' => '', 'intermediate' => '', 'advanced' => '', 'innovative' => ''];
		switch ($value) {
			case 5: $data['innovative'] 	= 'active'; break;
			case 4: $data['advanced'] 		= 'active'; break;
			case 3: $data['intermediate'] 	= 'active'; break;
			case 2: $data['evolving'] 		= 'active'; break;
			case 1: $data['baseline'] 		= 'active'; break;
			default: break;
		}
	}
	return $data;
}
function advisory_csa_area_exists($opts, $searching_for) {
	if (is_array($opts['sections'])) {
		foreach ($opts['sections'] as $section) {
			if ((advisory_id_from_string($section['name']) == $searching_for) && ($searching_for == 'overview')) return $section;
			$sectionID = advisory_id_from_string($section['name']) .'_section';
			if (!empty($opts[$sectionID])) {
				foreach ($opts[$sectionID] as $domain) {
					if (advisory_id_from_string($domain['name']) == $searching_for) {
						return $domain;
					}
				}
			}
		}
	}
	return false;
}
// generate content section
function advisory_generate_csa_form_cyber_security_contents($domain) {
	$data = [];
	// DOMAIN 1
	$data['domain_1'][advisory_id_from_string('Oversight')] = [
		'baseline' 		=> '<p>Designated members of management are held accountable for implementing and managing the information security and business continuity programs.</p><p>The budgeting process includes information security related expenses and tools.</p>',
		'evolving' 		=> '<p>At least annually an appropriate committee reviews and approves the organization’s cybersecurity program. </p><p>Management is responsible for ensuring compliance with legal and regulatory requirements related to cybersecurity.</p><p>Management provides a written report on the overall status of the information security and business continuity programs to the board or an appropriate board committee at least annually.</p>',
		'intermediate' 	=> '<p>An appropriate committee has cybersecurity expertise or engages experts to assist with oversight responsibilities.</p><p>The standard oversight committee meeting package includes reports and metrics that go beyond events and incidents to address threat intelligence trends and the organization’s security posture.</p><p>The organization has a cyber risk appetite statement approved by the committee.</p><p>Cyber risks that exceed the risk appetite are escalated to management.</p><p>An appropriate committee ensures management’s annual cybersecurity self-assessment evaluates the organization’s ability to meet its cyber risk management standards.</p><p>An appropriate committee reviews and approves management’s prioritization and resource allocation decisions based on the results of the cyber assessments.</p><p>An appropriate committee ensures management takes appropriate actions to address changing cyber risks or significant cybersecurity issues.</p><p>The budget process for requesting additional cybersecurity staff and tools is integrated into business units’ budget processes. </p>',
		'advanced' 		=> '<p>The oversight committee approved cyber risk appetite statement is part of the enterprise-wide risk appetite statement.</p><p>Management has a formal process to continuously improve cybersecurity oversight.</p><p>The budget process for requesting additional cybersecurity staff and tools maps current resources and tools to the cybersecurity strategy. </p><p>Management and an appropriate committee hold business units accountable for effectively managing all cyber risks associated with their activities.</p><p>Management identifies root cause(s) when cyber attacks result in material loss.</p>',
		'innovative' 	=> '<p>An appropriate committee discusses ways for management to develop cybersecurity improvements that may be adopted across the organization.</p><p>An appropriate committee verifies that management’s actions consider the cyber risks that the organization poses to other critical infrastructures (e.g., telecommunications,).</p>',
	];
	$data['domain_1'][advisory_id_from_string('Strategy/Policies')] = [
		'baseline' 		=> '<p>Information security strategies are ad hoc and not formalized.</p><p>Some information security policies are in place but may require updating.</p><p>The organization has some policies commensurate with its risk and complexity that address the concepts of information technology risk management.</p><p>The organization has some approved policies commensurate with its risk and complexity that address information security.</p>',
		'evolving' 		=> '<p>The organization augmented its information security strategy to incorporate cybersecurity and resilience.</p><p>The organization has a formal cybersecurity program that is based on technology and security industry standards or benchmarks.</p><p>A formal process is in place to update policies as the organization’s inherent risk profile change</p><p>The organization has an information security strategy that integrates technology, policies, procedures, and training to mitigate risk.</p><p>The organization has policies commensurate with its risk and complexity that address the concepts of external dependency or third-party management.</p>',
		'intermediate' 	=> '<p>The organization has a comprehensive set of policies commensurate with its risk and complexity that address the concepts of threat intelligence.</p><p>A formal process is in place to cross-reference and simultaneously update all policies related to cyber risks across business lines.</p><p>The organization has policies commensurate with its risk and complexity that address the concepts of incident response and resilience</p>',
		'advanced' 		=> '<p>Industry-recognized cybersecurity standards are used as sources during the analysis of cybersecurity program gaps.</p><p>Management periodically reviews the cybersecurity strategy to address evolving cyber threats and changes to the organization’s inherent risk profile.</p><p>The cybersecurity strategy is incorporated into, or conceptually fits within, the organization’s enterprise-wide risk management strategy.</p><p>Management links strategic cybersecurity objectives to tactical goals.</p>',
		'innovative' 	=> '<p>The cybersecurity strategy identifies and communicates the organization’s role as it relates to other critical infrastructures.</p><p>The cybersecurity strategy outlines the organization’s future state of cybersecurity with short-term and long-term perspectives.</p><p>Management is continuously improving the existing cybersecurity program to adapt as the desired cybersecurity target state changes</p><p>All elements of the information security program are coordinated enterprise wide.</p>',
	];
	$data['domain_1'][advisory_id_from_string('IT Asset Management')] = [
		'baseline' 		=> '<p>Change management procedures are ad hoc for systems configurations, hardware, software, applications, and security tools</p>',
		'evolving' 		=> '<p>The organization proactively manages system EOL (e.g., replacement) to limit security risks.</p><p>Changes are formally approved by an individual or committee with appropriate authority and with separation of duties</p>
		<p>An inventory of organizational assets (e.g., hardware, software, data, and systems hosted externally) is maintained.</p>
		<p>Organizational assets (e.g., hardware, systems, data, and applications) are prioritized for protection based on the data classification and business value.</p>',
		'intermediate' 	=> '<p>Baseline configurations cannot be altered without a formal change request, documented approval, and an assessment of security implications.</p><p>A formal IT change management process requires cybersecurity risk to be evaluated during the analysis, approval, testing, and reporting of changes.</p>
		<p>The asset inventory, including identification of critical assets, is updated at least annually to address new, relocated, re-purposed, and sunset assets.</p>',
		'advanced' 		=> '<p>Supply chain risk is reviewed before the acquisition of mission-critical information systems including system components.</p><p>Automated tools enable tracking, updating, asset prioritizing, and custom reporting of the asset inventory.</p><p>Automated processes are in place to detect and block unauthorized changes to software and hardware. </p><p>The change management system uses thresholds to determine when a risk assessment of the impact of the change is required</p>',
		'innovative' 	=> '<p>A formal change management function governs decentralized or highly distributed change requests and identifies and measures security risks that may cause increased exposure to cyber attack.</p><p>Comprehensive automated enterprise tools are implemented to detect and block unauthorized changes to software and hardware.</p>',
	];
	$data['domain_1'][advisory_id_from_string('Risk Management Program')] = [
		'baseline' 		=> '<p>Information Security Risk Management is performed in an ad hoc manner</p>',
		'evolving' 		=> '<p>An information security and business continuity risk management function(s) exists within the organization</p><p>The risk management program incorporates cyber risk identification, measurement, mitigation, monitoring, and reporting.</p><p>Management reviews and uses the results of audits to improve existing cybersecurity policies, procedures, and controls. </p><p>Management monitors moderate and high residual risk issues from the cybersecurity risk assessment until items are addressed.</p>',
		'intermediate' 	=> '<p>The cybersecurity function has a clear reporting line that does not present a conflict of interest.</p><p>The risk management program specifically addresses cyber risks beyond the boundaries of the technological impacts (e.g., financial, strategic, regulatory, compliance).</p><p>Benchmarks or target performance metrics have been established for showing improvements or regressions of the security posture over time.</p><p>Management uses the results of independent audits and reviews to improve cybersecurity.</p><p>There is a process to analyze potential expenses associated with cybersecurity incidents.</p>',
		'advanced' 		=> '<p>Cybersecurity metrics are used to facilitate strategic decision-making and funding in areas of need.</p><p>Independent risk management sets and monitors cyber-related risk limits for business units.</p><p>Independent risk management staff escalates to management or an appropriate committee </p><p>A process is in place to analyze the financial impact cyber incidents have on the organization.</p><p>The cyber risk data aggregation and real-time reporting capabilities support the organization’s ongoing reporting needs, particularly during cyber incidents</p>',
		'innovative' 	=> '<p>The risk management function identifies and analyzes commonalities in cyber events that occur at the organization to enable more predictive risk management.</p><p>A process is in place to analyze the financial impact that a cyber incident at the organization may have across the organization.</p>',
	];
	$data['domain_1'][advisory_id_from_string('Risk Assessment')] = [
		'baseline' 		=> '<p>There are no formal risk assessment processes in place.</p><p>Risk assessment is performed on an ad hoc basis and not in all cases.</p>',
		'evolving' 		=> '<p>A risk assessment focused on safeguarding information identifies reasonable and foreseeable internal and external threats, the likelihood and potential damage of threats, and the sufficiency of policies, procedures, and information systems.</p><p>The risk assessment identifies internet-based systems and high-risk transactions that warrant additional authentication controls.</p><p>The risk assessment is updated to address new technologies, products, services, and connections before deployment.</p>',
		'intermediate' 	=> '<p>The risk assessment is adjusted to consider widely known risks or risk management practices.</p><p>Risk assessments are used to identify the cybersecurity risks stemming from new products, services, or relationships.</p><p>The focus of the risk assessment addresses all information assets.</p><p>The risk assessment considers the risk of using EOL software and hardware components.</p>',
		'advanced' 		=> '<p>A formal process is in place for the independent audit function to update its procedures based on changes to the evolving threat landscape across the sector.</p><p>The independent audit function regularly reviews the organization’s cyber risk appetite statement in comparison to assessment results and incorporates gaps into the audit strategy.</p><p>Independent audits or reviews are used to identify cybersecurity weaknesses, root causes, and the potential impact to business units.</p>',
		'innovative' 	=> '<p>The risk assessment is updated in real time as changes to the risk profile occur, new applicable standards are released or updated, and new exposures are anticipated.</p><p>The organization uses information from risk assessments to predict threats and drive real-time responses.</p><p>Advanced or automated analytics offer predictive information and real-time risk metrics.</p>',
	];
	$data['domain_1'][advisory_id_from_string('Audit')] = [
		'baseline' 		=> '<p>Management ensures continuous improvement of cyber risk cultural awareness</p>',
		'evolving' 		=> '<p>The independent audit function validates that the risk management function is commensurate with the organization’s risk and complexity.</p><p>The independent audit function validates controls related to the storage or transmission of confidential data.</p><p>Independent audit or review evaluates policies, procedures, and controls across the organization for significant risks and control issues associated with the organizations operations, including risks in new products, emerging technologies, and information systems.</p>',
		'intermediate' 	=> '<p>A formal process is in place for the independent audit function to update its procedures based on changes to the organization’s inherent risk profile.</p><p>The independent audit function validates that the organization’s threat intelligence and collaboration are commensurate with the organization’s risk and complexity.</p><p>The independent audit function regularly reviews management’s cyber risk appetite.</p><p>Independent audits or reviews are used to identify gaps in existing security capabilities and expertise.</p><p>The independent audit function validates that the organization’s incident response program and resilience are commensurate with the organization’s risk and complexity.</p><p>Logging practices are independently reviewed periodically to ensure appropriate log management (e.g., access controls, retention, and maintenance).</p><p>Issues and corrective actions from internal audits and independent testing/assessments are formally tracked to ensure procedures and control lapses are resolved in a timely manner.</p>',
		'advanced' 		=> '<p>The independent audit function regularly reviews the organization’s cyber risk appetite statement in comparison to assessment results and incorporates gaps into the audit strategy.</p><p>Independent audits or reviews are used to identify cybersecurity weaknesses, root causes, and the potential impact to business units</p>',
		'innovative' 	=> '<p>A formal process is in place for the independent audit function to update its procedures based on changes to the evolving threat landscape. </p>',
	];
	$data['domain_1'][advisory_id_from_string('Staffing')] = [
		'baseline' 		=> '<p>Security is performed by staff with other roles without dedicated security staff</p><p>There is no formal process to identify additional expertise required to improve information security defences.</p>',
		'evolving' 		=> '<p>Information security roles and responsibilities have been identified.</p><p>Processes are in place to identify additional expertise needed to improve information security defences.</p><p>Staff with cybersecurity responsibilities have the requisite qualifications to perform the necessary tasks of the position.</p><p>Employment candidates, contractors, and third parties are subject to background verification proportional to the confidentiality of the data accessed, business requirements, and acceptable risk.</p>',
		'intermediate' 	=> '<p>A formal process is used to identify cybersecurity tools and expertise that may be needed.</p><p>Management with appropriate knowledge and experience leads the organization’s cybersecurity efforts.</p>',
		'advanced' 		=> '<p>The organization benchmarks its cybersecurity staffing against peers to identify whether its recruitment, retention, and succession planning are commensurate.</p><p>The organization has a program for talent recruitment, retention, and succession planning for the cybersecurity and resilience staff.</p>',
		'innovative' 	=> '<p>Dedicated cybersecurity staff develops, or contributes to developing, integrated enterprise-level security and cyber defense strategies.</p>',
	];
	$data['domain_1'][advisory_id_from_string('Training')] = [
		'baseline' 		=> '<p>There is no formal information security training program or training is not provided to all staff.</p>',
		'evolving' 		=> '<p>Annual information security training is provided to all staff.</p><p>Annual information security training includes incident response, current cyber threats (e.g., phishing, spear phishing, social engineering, and mobile security), and emerging issues.</p><p>The organization has a program for continuing cybersecurity training and skill development for cybersecurity staff.</p><p>The organization validates the effectiveness of training (e.g., social engineering or phishing tests).</p>',
		'intermediate' 	=> '<p>Management incorporates lessons learned from social engineering and phishing exercises to improve the employee awareness programs.<p></p>Business units are provided cybersecurity training relevant to their particular business risks, over and above what is required of the organization as a whole.<p></p>The organization routinely updates its training to security staff to adapt to new threats. <p></p>Employees with privileged account permissions receive additional cybersecurity training commensurate with their levels of responsibility.</p><p>Business units are provided cybersecurity training relevant to their particular business risks.</p><p>Management is provided cybersecurity training relevant to their job responsibilities.</p>',
		'advanced' 		=> '<p>Business unit leaders are provided with cybersecurity training that addresses how complex products, services, and lines of business affect the organization’s cyber risk.</p>',
		'innovative' 	=> '<p>Key performance indicators are used to determine whether training and awareness programs positively influence behavior</p>',
	];
	$data['domain_1'][advisory_id_from_string('Culture')] = [
		'baseline' 		=> '<p>The IT Department is collectively accountable for information security but no direct accountability for staff exists.</p><p>Employees do not have the necessary skills or a process to identify and escalate potential security issues.</p>',
		'evolving' 		=> '<p>Management holds employees accountable for complying with the information security program</p><p>Employees have a clear understanding of how to identify and escalate potential cybersecurity issues.</p>',
		'intermediate' 	=> '<p>Cyber risk reporting is presented and discussed at the independent risk management meetings </p><p>The organization has formal standards of conduct that hold all employees accountable for complying with cybersecurity policies and procedures.</p><p>Cyber risks are actively discussed at business unit meetings.</p>',
		'advanced' 		=> '<p>Management ensures performance plans are tied to compliance with cybersecurity policies and standards in order to hold employees accountable.</p><p>The risk culture requires formal consideration of cyber risks in all business decisions.</p>',
		'innovative' 	=> '<p>Management ensures continuous improvement of cyber risk cultural awareness</p>',
	];
	// DOMAIN 2
	$data['domain_2'][advisory_id_from_string('Information Sharing')] = [
		'baseline' 		=> '<p>No information sharing occurs, or it is informal and ad hoc.</p>',
		'evolving' 		=> '<p>Information security threats are gathered and shared with applicable internal employees.</p><p>Contact information for regulator(s) is maintained and updated regularly.</p><p>Information about threats is shared with regulators when required or prompted</p>',
		'intermediate' 	=> '<p>A formal protocol is in place for sharing threat, vulnerability, and incident information to employees based on their specific job function.</p><p>Information-sharing agreements are used as needed or required to facilitate sharing threat information with other organizations or third parties.</p>',
		'advanced' 		=> '<p>Management communicates threat intelligence with business risk context and specific risk management recommendations to the business units.</p><p>Relationships exist with employees of peer organizations for sharing cyber threat intelligence.</p><p>A network of trust relationships (formal and/or informal) has been established to evaluate information about cyber threats.</p>',
		'innovative' 	=> '<p>A mechanism is in place for sharing cyber threat intelligence with business units in real time including the potential financial and operational impact of inaction.</p><p>A system automatically informs management of the level of business risk specific to the organization and the progress of recommended steps taken to mitigate the risks.</p>',
	];
	$data['domain_2'][advisory_id_from_string('Monitoring And Analyzing')] = [
		'baseline' 		=> '<p>Audit logs are maintained but not reviewed on a regular basis.</p>',
		'evolving' 		=> '<p>A process is implemented to monitor threat information to discover emerging threats.</p><p>The threat information and analysis process is assigned to a specific group or individual.</p><p>Security processes and technology are centralized and coordinated in a Security Operations Center (SOC) or equivalent.</p><p>Monitoring systems operate continuously with adequate support for efficient incident handling.</p><p>Audit log records and other security event logs are reviewed and retained in a secure manner.</p><p>Computer event logs are used for investigations once an event has occurred.</p>',
		'intermediate' 	=> '<p>A threat intelligence team is in place that evaluates threat intelligence from multiple sources for credibility, relevance, and exposure.</p><p>A profile is created for each threat that identifies the likely intent, capability, and target of the threat.</p><p>Threat information sources that address all components of the threat profile are prioritized and monitored.</p><p>Threat intelligence is analyzed to develop cyber threat summaries including risks to the organization and specific actions for the organization to consider.</p>',
		'advanced' 		=> '<p>A cyber threat identification and analysis team exists to centralize and coordinate initiatives and communications.</p><p>Emerging internal and external threat intelligence and correlated log analysis are used to predict future attacks.</p><p>Threat intelligence is viewed within the context of the organizations risk profile and risk appetite to prioritize mitigating actions in anticipation of threats.</p><p>Threat intelligence is used to update architecture and configuration standards.</p>',
		'innovative' 	=> '<p>The organization uses multiple sources of intelligence, correlated log analysis, alerts, internal traffic flows, and geopolitical events to predict potential future attacks and attack trends.</p><p>Highest risk scenarios are used to predict threats against specific business targets.</p><p>IT systems automatically detect configuration weaknesses based on threat intelligence and alert management so actions can be prioritized.</p>',
	];
	$data['domain_2'][advisory_id_from_string('Threat Intelligence And Information')] = [
		'baseline' 		=> '<p>Threat information gathering is informal and no formal monitoring of threats and vulnerabilities is in place.</p>',
		'evolving' 	=> '<p>Threat information received by the organization includes analysis of tactics, patterns, and risk mitigation recommendations.</p><p>The organization belongs or subscribes to a threat and vulnerability information sharing source(s) that provides information on threats (e.g., Financial Services Information Sharing and Analysis Center.</p><p>Threat information is used to monitor threats and vulnerabilities.</p><p>Threat information is used to enhance internal risk management and controls.</p>',
		'intermediate' 		=> '<p>A formal threat intelligence program is implemented and includes subscription to threat feeds from external providers and internal sources.</p><p>Protocols are implemented for collecting information from industry peers and government.</p><p>A read-only, central repository of cyber threat intelligence is maintained.</p>',
		'advanced' 	=> '<p>A cyber intelligence model is used for gathering threat information.</p>Threat intelligence is automatically received from multiple sources in real time.<p></p><p>The organization’s threat intelligence includes information related to geopolitical events that could increase cybersecurity threat levels.</p>',
		'innovative' => '<p>A threat analysis system automatically correlates threat data to specific risks and then takes risk-based automated actions while alerting management.</p><p>The organization is investing in the development of new threat intelligence and collaboration mechanisms (e.g., technologies, business processes) that will transform how information is gathered and shared.</p>',
	];
	// DOMAIN 3
	$data['domain_3'][advisory_id_from_string('Access And Data Management')] = [
		'baseline' 		=> '<p>Identification and authentication are required and managed for access to systems, applications, and hardware.</p><p>Access controls include password complexity and limits to password attempts and reuse.</p><p>All default passwords and unnecessary default accounts are changed before system implementation.</p><p>Physical security controls are used to prevent unauthorized access to information systems and telecommunication systems.</p><p>Administrative, physical, or technical controls are in place to prevent users without administrative responsibilities from installing unauthorized software.</p><p>Administrative, physical, or technical controls are in place to prevent users without administrative responsibilities from installing unauthorized software.</p>',
		'evolving' 		=> '<p>Employee access is granted to systems and confidential data based on job responsibilities and the principles of least privilege.</p><p>Employee access to systems and confidential data provides for separation of duties.</p><p>Elevated privileges (e.g., administrator privileges) are limited and tightly controlled (e.g., assigned to individuals, not shared, and require stronger password controls).</p><p>User access reviews are performed periodically for all systems and applications based on the risk to the application or system.</p><p>Changes to physical and logical user access, including those that result from voluntary and involuntary terminations, are submitted to and approved by appropriate personnel.</p><p>Administrators have two accounts: one for administrative use and one for general purpose, non-administrative tasks.</p><p>Use of customer data in non-production environments complies with legal, regulatory, and internal policy requirements for concealing or removing of sensitive data elements.</p><p>Physical access to high-risk or confidential systems is restricted, logged, and unauthorized access is blocked.</p>',
		'intermediate' 	=> '<p>The organization has implemented tools to prevent unauthorized access to or exfiltration of confidential data.</p><p>Controls are in place to prevent unauthorized escalation of user privileges.</p><p>Access controls are in place for database administrators to prevent unauthorized downloading or transmission of confidential data.</p><p>All physical and logical access is removed immediately upon notification of involuntary termination and within 24 hours of an employee’s voluntary departure.</p><p>Confidential data are encrypted in transit across private connections (e.g., frame relay and T1) and within the organization’s trusted zones.</p><p>Changes to user access permissions trigger automated notices to appropriate personnel.</p><p>Controls are in place to prevent unauthorized access to cryptographic keys</p>',
		'advanced' 		=> '<p>Encryption of select data at rest is determined by the organization’s data classification and risk assessment.</p><p>Customer authentication for high-risk transactions includes methods to prevent malware and man-in-the-middle attacks (e.g., using visual transaction signing).</p><p>Multifactor authentication and/or layered controls have been implemented to secure all third-party access to the organizations network and/or systems and applications.</p><p>Multifactor authentication (e.g., tokens, digital certificates) techniques are used for employee access to high-risk systems as identified in the risk assessment(s). (*N/A if no high risk systems.)</p>',
		'innovative' 	=> '<p>Adaptive access controls de-provision or isolate an employee, third-party, or customer credentials to minimize potential damage if malicious behavior is suspected.</p><p>Unstructured confidential data are tracked and secured through an identity-aware, cross-platform storage system that protects against internal threats, monitors user access, and tracks changes.</p><p>Tokenization is used to substitute unique values for confidential information (e.g., virtual credit card).</p><p>Real-time risk mitigation is taken based on automated risk scoring of user credentials.</p>',
	];
	$data['domain_3'][advisory_id_from_string('Anomalous Activity Detection')] = [
		'baseline' 		=> '<p>The organization is partially able to detect anomalous activities through monitoring across the environment.</p>',
		'evolving' 		=> '<p>Systems are in place to detect anomalous behavior automatically during, employee, and third-party authentication.</p><p>Security logs are reviewed regularly.</p><p>Logs provide traceability for all system access by individual users.</p><p>Logs of physical and/or logical access are reviewed following events.</p><p>Thresholds have been established to determine activity within logs that would warrant management response.</p><p>Access to critical systems by third parties is monitored for unauthorized or unusual activity.</p><p>Elevated privileges are monitored.</p>',
		'intermediate' 	=> '<p>Tools to detect unauthorized data mining are used.</p><p>Tools actively monitor security logs for anomalous behavior and alert within established parameters.</p><p>Audit logs are backed up to a centralized log server or media that is difficult to alter.</p><p>Thresholds for security logging are evaluated periodically.</p><p>Anomalous activity and other network and system alerts are correlated across business units to detect and prevent multifaceted attacks (e.g., simultaneous account takeover and DDoS attack).</p>',
		'advanced' 		=> '<p>A system is in place to monitor and analyze employee behavior (network use patterns, work hours, and known devices) to alert on anomalous activities.</p><p>An automated tool(s) is in place to detect and prevent data mining by insider threats. Tags on fictitious confidential data or files are used to provide advanced alerts of potential malicious activity when the data is accessed</p>',
		'innovative' 	=> '<p>The organization has a mechanism for real-time automated risk scoring of threats.</p><p>The organization is developing new technologies that will detect potential insider threats and block activity in real time.</p>',
	];
	$data['domain_3'][advisory_id_from_string('Device_end-Point Security')] = [
		'baseline' 		=> '<p>Antivirus and anti-malware tools are deployed on end-point devices (e.g., workstations, laptops, and mobile devices).</p>',
		'evolving' 		=> '<p>Tools automatically block attempted access from unpatched employee and third-party devices.</p><p>Tools automatically block attempted access by unregistered devices to internal networks.</p><p>The organization has controls to prevent the unauthorized addition of new connections.</p><p>Mobile devices with access to the organization’s data are centrally managed for antivirus and patch deployment. (*N/A if mobile devices are not used.)</p><p>The organization wipes data remotely on mobile devices when a device is missing or stolen. (*N/A if mobile devices are not used.)</p>',
		'intermediate' 	=> '<p>Data loss prevention controls or devices are implemented for inbound and outbound communications (e.g., e-mail, FTP, Telnet, prevention of large file transfers).</p><p>Controls are in place to prevent unauthorized individuals from copying confidential data to removable media.</p><p>Controls are in place to restrict the use of removable media to authorized personnel</p>',
		'advanced' 		=> '<p>Mobile device management includes integrity scanning (e.g., jailbreak/rooted detection). (*N/A if mobile devices are not used.)</p><p>Mobile devices connecting to the corporate network for storing and accessing company information allow for remote software version/patch validation.</p><p>Employees’ and third parties’ devices (including mobile) without the latest security patches are quarantined and patched before the device is granted access to the network.</p>',
		'innovative' 	=> '<p>Confidential data and applications on mobile devices are only accessible via a secure, isolated sandbox or a secure container.</p>',
	];
	$data['domain_3'][advisory_id_from_string('Event Detection')] = [
		'baseline' 		=> '<p>Mechanisms (e.g., antivirus alerts, log event alerts) are in place to alert management to potential attacks.</p>',
		'evolving' 		=> '<p>A process is in place to correlate event information from multiple sources (e.g., network, application, or firewall).</p><p>A normal network activity baseline is established.</p><p>Processes are in place to monitor for the presence of unauthorized users, devices, connections, and software.</p><p>Responsibilities for monitoring and reporting suspicious systems activity have been assigned.</p><p>The physical environment is monitored to detect potential unauthorized access.</p><p></p>',
		'intermediate' 	=> '<p>Controls or tools (e.g., data loss prevention) are in place to detect potential unauthorized or unintentional transmissions of confidential data.</p><p>Event detection processes are proven reliable.</p><p>Specialized security monitoring is used for critical assets throughout the infrastructure.</p>',
		'advanced' 		=> '<p>Automated tools detect unauthorized changes to critical system files, firewalls, IPS, IDS, or other security devices.</p><p>Real-time network monitoring and detection is implemented and incorporates organization-wide event information.</p><p>Real-time alerts are automatically sent when unauthorized software, hardware, or changes occur.</p><p>Tools are in place to actively correlate event information from multiple sources and send alerts based on established parameters.</p>',
		'innovative' 	=> '<p>The organization has detection systems that will correlate in real time when events are about to occur.</p>',
	];
	$data['domain_3'][advisory_id_from_string('Infrastructure And Management')] = [
		'baseline' 		=> '<p>Network perimeter defense tools (e.g., border router and firewall) are used.</p><p>Systems that are accessed from the Internet or by external parties are protected by firewalls or other similar devices.</p><p>All ports are monitored.</p><p>Up to date antivirus and anti-malware tools are used.</p><p>Wireless network environments require security settings with strong encryption for authentication and transmission. </p>',
		'evolving' 		=> '<p>There is a firewall at each Internet connection and between any Demilitarized Zone (DMZ) and internal network(s).</p><p>Antivirus and intrusion detection/prevention systems (IDS/IPS) detect and block actual and attempted attacks or intrusions.</p><p>Technical controls prevent unauthorized devices, including rogue wireless access devices and removable media, from connecting to the internal network(s).</p><p>A risk-based solution is in place at the organization or Internet hosting provider to mitigate disruptive cyber attacks (e.g., DDoS attacks).</p><p>Guest wireless networks are fully segregated from the internal network(s).</p><p>Domain Name System Security Extensions (DNSSEC) is deployed across the enterprise.</p><p>Systems configurations (for servers, desktops, routers, etc.) follow industry standards and are enforced.</p><p>Ports, functions, protocols and services are prohibited if no longer needed for business purposes.</p><p>Access to make changes to systems configurations (including virtual machines and hypervisors) is controlled and monitored.</p><p>Programs that can override system, object, network, virtual machine, and application controls are restricted.</p>',
		'intermediate' 	=> '<p>Security controls are used for remote access to all administrative consoles, including restricted virtual systems.</p><p>Wireless network environments have perimeter firewalls that are implemented and configured to restrict unauthorized traffic. </p><p>Wireless networks use strong encryption with encryption keys that are changed frequently. (*N/A if there are no wireless networks.)</p><p>The broadcast range of the wireless network(s) is confined to organization-controlled boundaries. (*N/A if there are no wireless networks.)</p><p>Technical measures are in place to prevent the execution of unauthorized code on organization owned or managed devices, network infrastructure, and systems components.</p><p>Critical systems supported by legacy technologies are regularly reviewed to identify for potential vulnerabilities, upgrade opportunities, or new defense layers.</p><p>System sessions are locked after a pre-defined period of inactivity and are terminated after pre-defined conditions are met.</p>',
		'advanced' 		=> '<p>Network environments and virtual instances are designed and configured to restrict and monitor traffic between trusted and untrusted zones.</p><p>Only one primary function is permitted per server to prevent functions that require different security levels from co-existing on the same server.</p><p>Anti-spoofing measures are in place to detect and block forged source IP addresses from entering the network.</p><p>The enterprise network is segmented in multiple, separate trust/security zones with defense-in-depth strategies (e.g., logical network segmentation, hard backups, air-gapping) to mitigate attacks.</p>',
		'innovative' 	=> '<p>The organization risk scores all of its infrastructure assets and updates in real time based on threats, vulnerabilities, or operational changes.</p><p>Automated controls are put in place based on risk scores to infrastructure assets, including automatically disconnecting affected assets.</p><p>The organization proactively seeks to identify control gaps that may be used as part of a zero-day attack.</p><p>The organization proactively seeks to identify control gaps that may be used as part of a zero-day attack.</p>',
	];
	$data['domain_3'][advisory_id_from_string('Patch Management')] = [
		'baseline' 		=> '<p>A patch management program is implemented and ensures that software and firmware patches are applied. Patching is ad hoc and not always performed within a certain time frame.</p>',
		'evolving' 		=> '<p>A formal process is in place to acquire, test, and deploy software patches based on criticality.</p><p>Operational impact is evaluated before deploying security patches.</p><p>Patches are tested before being applied to systems and/or software.</p><p>Patches are tested before being applied to systems and/or software.</p>',
		'intermediate' 	=> '<p>Patches for high-risk vulnerabilities are tested and applied when released or the risk is accepted and accountability assigned.</p><p>An automated tool(s) is used to identify missing security patches as well as the number of days since each patch became available.</p><p>Missing patches across all environments are prioritized and tracked</p>',
		'advanced' 		=> '<p>Patch monitoring software is installed on all servers to identify any missing patches for the operating system software, middleware, database, and other key software.</p><p>The organization monitors patch management reports to ensure security patches are tested and implemented within aggressive time frames (e.g., 0-30 days).</p>',
		'innovative' 	=> '<p>Segregated or separate systems are in place that mirror production systems allowing for rapid testing and implementation of patches and provide for rapid fallback when needed.</p>',
	];
	$data['domain_3'][advisory_id_from_string('Remediation')] = [
		'baseline' 		=> '<p>This area is blank. Should say: There are no formal programs or plans in place to re-mediate identified vulnerabilities. Remediation is performed on an ad hoc basis.</p>',
		'evolving' 		=> '<p>Issues identified in assessments are prioritized and resolved based on criticality and within the time frames established in the response to the assessment report.</p><p>Data is destroyed or wiped on hardware and portable/mobile media when a device is missing, stolen, or no longer needed.</p><p>Formal processes are in place to resolve weaknesses identified during penetration testing</p>',
		'intermediate' 	=> '<p>Remediation efforts are confirmed by conducting a follow-up vulnerability scan.</p><p>Penetration testing is repeated to confirm that medium- and high-risk, exploitable vulnerabilities have been resolved.</p><p>Security investigations, forensic analysis, and remediation are performed by qualified staff or third parties.</p><p>Generally accepted and appropriate forensic procedures, including chain of custody, are used to gather and present evidence to support potential legal action.</p><p>The maintenance and repair of organizational assets are performed by authorized individuals with approved and controlled tools.</p>',
		'advanced' 		=> '<p>All medium and high risk issues identified in penetration testing, vulnerability scanning, and other independent testing are escalated to an appropriate committee for risk acceptance if not resolved in a timely manner.</p>',
		'innovative' 	=> '<p>The organization has technologies in place that will remediate systems damaged by zero-day attacks to maintain current recovery time objectives.p>',
	];
	$data['domain_3'][advisory_id_from_string('Secure Coding')] = [
		'baseline' 		=> '<p>Software development takes place in the organization but no processes or practices to check for security are in place</p>',
		'evolving' 		=> '<p>Developers working for the institution follow secure program coding practices, as part of a system development life cycle (SDLC), that meet industry standards.</p><p>The security controls of internally developed software are periodically reviewed and tested. </p><p>The security controls in internally developed software code are independently reviewed before migrating the code to production. </p><p>Intellectual property and production code are held in escrow.</p><p>Security testing occurs at all post-design phases of the SDLC for all applications, including mobile applications</p>',
		'intermediate' 	=> '<p>Processes are in place to mitigate vulnerabilities identified as part of the secure development of systems and applications.</p><p>The security of applications, including Web-based applications connected to the Internet, is tested against known types of cyber attacks (e.g., SQL injection, cross-site scripting, buffer overflow) before implementation or following significant changes.</p><p>Software code executables and scripts are digitally signed to confirm the software author and guarantee that the code has not been altered or corrupted.</p><p>A risk-based, independent information assurance function evaluates the security of internal applications.</p>',
		'advanced' 		=> '<p>Vulnerabilities identified through a static code analysis are remediated before implementing newly developed or changed applications into production.</p><p>All interdependencies between applications and services have been identified.</p><p>Independent code reviews are completed on internally developed or vendor-provided custom applications to ensure there are no security gaps.</p><p>Independent code reviews are completed on internally developed or vendor-provided custom applications to ensure there are no security gaps.</p>',
		'innovative' 	=> '<p>No internal software development takes place in the organization.</p>',
	];
	$data['domain_3'][advisory_id_from_string('Threat And Vulnerability Detection')] = [
		'baseline' 		=> '<p>Antivirus and anti-malware tools are used to detect attacks.</p><p>Firewall rules are in place that help to detect threats and vulnerabilities.</p><p>E-mail protection mechanisms are used to filter for common cyber threats (e.g., attached malware or malicious links).</p>',
		'evolving' 		=> '<p>Independent testing (including penetration testing and vulnerability scanning) is conducted according to the risk assessment for external facing systems and the internal network.</p><p>Firewall rules are audited or verified at least quarterly.</p><p>Independent penetration testing of network boundary and critical Web facing applications is performed routinely to identify security control gaps.</p><p>Independent penetration testing is performed on Internet-facing applications or systems before they are launched or undergo significant change.</p><p>Antivirus and anti-malware tools are updated automatically.</p><p>Firewall rules are updated routinely.</p>',
		'intermediate' 	=> '<p>Audit, risk management resources or an independent third-party review the penetration testing scope and results to help determine the need for rotating companies based on the quality of the work.</p><p>Vulnerability scanning is conducted and analyzed before deployment/redeployment of new/existing devices.</p><p>Processes are in place to monitor potential insider activity that could lead to data theft or destruction.</p>',
		'advanced' 		=> '<p>Vulnerabilities identified through a static code analysis are remediated before implementing newly developed or changed applications into production.</p><p>All interdependencies between applications and services have been identified.</p><p>Independent code reviews are completed on internally developed or vendor-provided custom applications to ensure there are no security gaps.</p>',
		'innovative' 	=> '<p>User tasks and content (e.g., opening an e-mail attachment) are automatically isolated in a secure container or virtual environment so that malware can be analyzed but cannot access vital data, end-point operating systems, or applications on the institution’s network.</p><p>Vulnerability scanning is performed on a weekly basis across all environments.</p>',
	];
	// DOMAIN 4
	$data['domain_4'][advisory_id_from_string('Connections')] = [
		'baseline' 		=> '<p>The organization ensures that third-party connections are authorized.</p><p>A network diagram is in place and identifies all external connections.</p>',
		'evolving' 		=> '<p>The network diagram is updated when connections with third parties change or at least annually.</p><p>Network and systems diagrams are stored in a secure manner with proper restrictions on access.</p><p>Controls for primary and backup third-party connections are monitored and tested on a regular basis.</p><p>The critical business processes that are dependent on external connectivity have been identified.</p><p>Data flow diagrams are in place and document information flow to external parties.</p>',
		'intermediate' 	=> '<p>Monitoring controls cover all external connections (e.g., third-party service providers, business partners, customers).</p><p>Monitoring controls cover all internal network-to-network connections.</p><p>Critical business processes have been mapped to the supporting external connections.</p>',
		'advanced' 		=> '<p>The security architecture is validated and documented before network connection infrastructure changes.</p><p>The organization works closely with third-party service providers to maintain and improve the security of external connections.</p><p>A validated asset inventory is used to create comprehensive diagrams depicting data repositories, data flow, infrastructure, and connectivity. Security controls are designed and verified to detect and prevent intrusions from third-party connections.</p>',
		'innovative' 	=> '<p>Diagram(s) of external connections is interactive, shows real-time changes to the network connection infrastructure, new connections, and volume fluctuations, and alerts when risks arise.</p><p>The organization’s connections can be segmented or severed instantaneously to prevent contagion from cyber attacks.</p>',
	];
	$data['domain_4'][advisory_id_from_string('Contracts')] = [
		'baseline' 		=> '<p>Some contract administration is performed but no official SLAs exist, responsibilities are not always defined, security and privacy requirements are not always in place.</p>',
		'evolving' 		=> '<p>Responsibilities for managing devices (e.g., firewalls, routers) that secure connections with third parties are formally documented in the contract. Responsibility for notification of direct and indirect security incidents and vulnerabilities is documented in contracts or service-level agreements (SLAs).</p><p>Contracts stipulate geographic limits on where data can be stored or transmitted.</p><p>Formal contracts that address relevant security and privacy requirements are in place for all third parties that process, store, or transmit confidential data or provide critical services.</p><p>Contracts acknowledge that the third party is responsible for the security of the institution’s confidential data that it possesses, stores, processes, or transmits.</p><p>Contracts specify the security requirements for the return or destruction of data upon contract termination.</p>',
		'intermediate' 	=> '<p>Third-party SLAs or similar means are in place that require timely notification of security events.</p><p>Contracts stipulate that the third-party security controls are regularly reviewed and validated by an independent party.</p><p>Contracts identify the recourse available to the institution should the third party fail to meet defined security requirements.</p><p>Contracts establish responsibilities for responding to security incidents.</p>',
		'advanced' 		=> '<p>Contracts require third-party service provider’s security policies meet or exceed those of the institution.</p><p>A third-party termination/exit strategy has been established and validated with management.</p>',
		'innovative' 	=> '<p>The organization has a dedicated Contract Administrator</p>',
	];
	$data['domain_4'][advisory_id_from_string('Due Diligence')] = [
		'baseline' 		=> '<p>Risk-based due diligence is performed on prospective third parties before contracts are signed, including reviews of their background, reputation, financial condition, stability, and security controls.</p> <p>A list of third-party service providers is maintained.</p> <p>A risk assessment is conducted to identify criticality of service providers.</p><p>An appropriate committee reviews a summary of due diligence results including management’s recommendations to use third parties that will affect the organization’s inherent risk profile.</p>',
		'evolving' 		=> '<p>A formal process exists to analyze assessments of third-party cybersecurity controls.</p> <p>The board or an appropriate board committee reviews a summary of due diligence results including management’s recommendations to use third parties that will affect the institution’s inherent risk profile.</p>',
		'intermediate' 	=> '<p>A process is in place to confirm that the institution’s third-party service providers conduct due diligence of their third parties (e.g., subcontractors).</p> <p>Pre-contract, physical site visits of high-risk vendors are conducted by the institution or by a qualified third party.</p>',
		'advanced' 		=> '<p>A continuous process improvement program is in place for third-party due diligence activity.</p> <p>Audits of high-risk vendors are conducted on an annual basis.</p>',
		'innovative' 	=> '<p>The institution promotes sector-wide efforts to build due diligence mechanisms that lead to in-depth and efficient security and resilience reviews.</p> <p>The institution is leading efforts to develop new auditable processes and for conducting due diligence and ongoing monitoring of cybersecurity risks posed by third parties.</p>',
	];
	$data['domain_4'][advisory_id_from_string('Ongoing Monitoring')] = [
		'baseline' 		=> '<p>Risk assessments of third-parties are minimal or non-existent.</p>',
		'evolving' 		=> '<p>A process to identify new third-party relationships is in place, including identifying new relationships that were established without formal approval. </p><p>The third-party risk assessment is updated regularly.</p><p>Audits, assessments, and operational performance reports are obtained and reviewed regularly validating security controls for critical third parties.</p><p>Ongoing monitoring practices include reviewing critical third-parties’ resilience plans.</p>',
		'intermediate' 	=> '<p>A formal program assigns responsibility for ongoing oversight of third-party access.</p><p>Monitoring of third parties is scaled, in terms of depth and frequency, according to the risk of the third parties. </p><p>Automated reminders are in place to identify when required third-party information needs to be obtained or analyzed.</p>',
		'advanced' 		=> '<p>Third-party employee access to the organization’s confidential data are tracked actively based on the principles of least privilege. </p><p>Periodic on-site assessments of high-risk vendors are conducted to ensure appropriate security controls are in place.</p>',
		'innovative' 	=> '<p>The organization is developing new auditable processes for ongoing monitoring of cybersecurity risks posed by third parties.</p><p>Third-party employee access to confidential data on third-party hosted systems is tracked actively via automated reports and alerts.</p>',
	];

	// DOMAIN 5
	$data['domain_5'][advisory_id_from_string('Detection')] = [
		'baseline' 		=> '<p>Detection and alerting systems are minimal or non-existent.</p>',
		'evolving' 		=> '<p>The organization has processes to detect and alert the incident response team when potential insider activity manifests that could lead to data theft or destruction.</p><p>Alert parameters are set for detecting information security incidents that prompt mitigating actions.</p><p>System performance reports contain information that can be used as a risk indicator to detect information security incidents.</p><p>Tools and processes are in place to detect, alert, and trigger the incident response program.</p>',
		'intermediate' 	=> '<p>The incident response program is triggered when anomalous behaviors and attack patterns or signatures are detected.</p><p>The organization has the ability to discover infiltration, before the attacker traverses across systems, establishes a foothold, steals information, or causes damage to data and systems.</p><p>Incidents are detected in real time through automated processes that include instant alerts to appropriate personnel who can respond.</p><p>Network and system alerts are correlated across business units to better detect and prevent multifaceted attacks (e.g., simultaneous DDoS attack and account takeover).</p><p>Incident detection processes are capable of correlating events across the enterprise.</p>',
		'advanced' 		=> '<p>Sophisticated and adaptive technologies are deployed that can detect and alert the incident response team of specific tasks when threat indicators across the enterprise indicate potential external and internal threats.</p><p>Automated tools are implemented to provide specialized security monitoring based on the risk of the assets to detect and alert incident response teams in real time.</p>',
		'innovative' 	=> '<p>The organization is able to detect and block zero-day attempts and inform management and the incident response team in real time.</p>',
	];
	$data['domain_5'][advisory_id_from_string('Escalating And Reporting')] = [
		'baseline' 		=> '<p>A process exists to contact personnel who are responsible for analyzing and responding to an incident. However, the process is not documented or tested, and a full incident response plan is not in place.</p>',
		'evolving' 		=> '<p>Criteria have been established for escalating cyber incidents or vulnerabilities to the board and senior management based on the potential impact and criticality of the risk.</p><p>Regulators, law enforcement, and service providers, as appropriate, are notified when the organization is aware of any unauthorized access to systems or a cyber incident occurs that could result in degradation of services.</p><p>Tracked cyber incidents are correlated for trend analysis and reporting.</p><p>Procedures exist to notify customers, regulators, and law enforcement as required or necessary when the organization becomes aware of an incident involving the unauthorized access to or use of sensitive customer information.</p><p>The organization prepares an annual report of security incidents or violations for an appropriate committee.</p><p>Incidents are classified, logged, and tracked.</p>',
		'intermediate' 	=> '<p>Employees that are essential to mitigate the risk (e.g., fraud, business resilience) know their role in incident escalation.</p><p>A communication plan is used to notify other organizations, including third parties, of incidents that may affect them or their customers.</p><p>An external communication plan is used for notifying media regarding incidents when applicable.</p>',
		'advanced' 		=> '<p>The organization has established quantitative and qualitative metrics for the cybersecurity incident response process.</p><p>Detailed metrics, dashboards, and/or scorecards outlining cyber incidents and events are provided to management and are part of the board meeting package.</p>',
		'innovative' 	=> '<p>A mechanism is in place to provide instantaneous notification of incidents to management and essential employees through multiple communication channels with tracking and verification of receipt.</p>',
	];
	$data['domain_5'][advisory_id_from_string('Planning')] = [
		'baseline' 		=> '<p>The organization plans to use business continuity, disaster recovery, and data backup programs to recover operations following an incident. However, the plans associated with these programs are outdated or non-existent.</p><p>Communication channels exist to provide employees a means for reporting information security events in a timely manner However, these channels are largely undocumented and ad hoc.</p><p>Backups are performed regularly and copies stored offsite.</p>',
		'evolving' 		=> '<p>The remediation plan and process outlines the mitigating actions, resources, and time parameters.</p><p>The corporate disaster recovery, business continuity, and crisis management plans have integrated consideration of cyber incidents.</p><p>Alternative processes have been established to continue critical activity within a reasonable time period.</p><p>Business impact analyses have been updated to include cybersecurity.</p><p>Due diligence has been performed on technical sources, consultants, or forensic service firms that could be called to assist the organization during or following an incident.</p><p>The organization has documented how it will react and respond to cyber incidents.</p><p>Roles and responsibilities for incident response team members are defined.</p><p>The response team includes individuals with a wide range of backgrounds and expertise, from many different areas within the organization (e.g., management, legal, public relations, as well as information technology).</p><p>A formal backup and recovery plan exists for all critical business lines.</p>',
		'intermediate' 	=> '<p>A strategy is in place to coordinate and communicate with internal and external stakeholders during or following a cyber attack.</p><p>Plans are in place to re-route or substitute critical functions and/or services that may be affected by a successful attack on Internet-facing systems.</p><p>A direct cooperative or contractual agreement(s) is in place with an incident response organization(s) or provider(s) to assist rapidly with mitigation efforts.</p><p>Lessons learned from real-life cyber incidents and attacks on the organization and other organizations are used to improve the organization’s risk mitigation capabilities and response plan</p>',
		'advanced' 		=> '<p>Methods for responding to and recovering from cyber incidents are tightly woven throughout the business units’ disaster recovery, business continuity, and crisis management plans.</p><p>Multiple systems, programs, or processes are implemented into a comprehensive cyber resilience program to sustain, minimize, and recover operations from an array of potentially disruptive and destructive cyber incidents.</p>',
		'innovative' 	=> '<p>The incident response plan is designed to ensure recovery from disruption of services, assurance of data integrity, and recovery of lost or corrupted data following a cybersecurity incident.</p><p>The incident response process includes detailed actions and rule-based triggers for automated response.</p><p>A process is in place to continuously improve the resilience plan.</p>',
	];
	$data['domain_5'][advisory_id_from_string('Response And Mitigation')] = [
		'baseline' 		=> '<p>Appropriate steps are taken to contain and control an incident to prevent further unauthorized access to or use of customer information. However, these steps are undocumented and there is no formal incident response plan.</p>',
		'evolving' 		=> '<p>The incident response plan is designed to prioritize incidents, enabling a rapid response for significant cybersecurity incidents or vulnerabilities.</p><p>A process is in place to help contain incidents and restore operations with minimal service disruption.</p><p>Containment and mitigation strategies are developed for multiple incident types (e.g., DDoS, malware, Ransomware).</p><p>Procedures include containment strategies and notifying potentially impacted third parties.</p><p>Processes are in place to trigger the incident response program when an incident occurs at a third party.</p><p>Records are generated to support incident investigation and mitigation.</p><p>The organization calls upon third parties, as needed, to provide mitigation services.</p><p>Analysis of events is used to improve the organization’s security measures and policies.</p>',
		'intermediate' 	=> '<p>Analysis of security incidents is performed in the early stages of an intrusion to minimize the impact of the incident.</p><p>Any changes to systems/applications or to access entitlements necessary for incident management are reviewed by management for formal approval before implementation.</p><p>Processes are in place to ensure assets affected by a security incident that cannot be returned to operational status are quarantined, removed, disposed of, and/or replaced.</p><p>Processes are in place to ensure that restored assets are appropriately reconfigured and thoroughly tested before being placed back into operation.</p>',
		'advanced' 		=> '<p>The incident management function collaborates effectively with the cyber threat intelligence function during an incident.</p><p>Links between threat intelligence, network operations, and incident response allow for proactive response to potential incidents.</p><p>Technical measures apply defense-in-depth techniques such as deep packet inspection and black holing for detection and timely response to network-based attacks associated with anomalous ingress or egress traffic patterns and/or DDoS attacks. </p>',
		'innovative' 	=> '<p>The organization’s risk management of significant cyber incidents results in limited to no disruptions to critical services.</p><p>The technology infrastructure has been engineered to limit the effects of a cyber attack on the production environment from migrating to the backup environment (e.g., air-gapped environment and processes).</p>',
	];
	$data['domain_5'][advisory_id_from_string('Testing')] = [
		'baseline' 		=> '<p>Testing of responses to incidents is minimal or non-existent.</p>',
		'evolving' 		=> '<p>Recovery scenarios include plans to recover from data destruction and impacts to data integrity, data loss, and system and data availability.</p><p>Widely reported events are used to evaluate and improve the organization’s response.</p><p>Information backups are tested periodically to verify they are accessible and readable.</p><p>Scenarios are used to improve incident detection and response.</p><p>Business continuity testing involves collaboration with critical third parties.</p><p>Systems, applications, and data recovery is tested at least annually.</p>',
		'intermediate' 	=> '<p>Cyber-attack scenarios are analyzed to determine potential impact to critical business processes.</p><p>Resilience testing is based on analysis and identification of realistic and highly likely threats as well as new and emerging threats facing the organization.</p><p>The critical online systems and processes are tested to withstand stresses for extended periods (e.g., DDoS).</p><p>The results of cyber event exercises are used to improve the incident response plan and automated triggers.</p>',
		'advanced' 		=> '<p>Resilience testing is comprehensive and coordinated across all critical business functions.</p><p>The organization validates that it is able to recover from cyber events similar to by known sophisticated attacks at other organizations.</p><p>Incident response testing evaluates the organization from an attacker’s perspective to determine how the organization or its assets at critical third parties may be targeted.</p><p>The organization corrects root causes for problems discovered during cybersecurity resilience testing.</p><p>Cybersecurity incident scenarios involving significant financial loss are used to stress test the organizations risk management.</p>',
		'innovative' 	=> '<p>The organization tests the ability to shift business processes or functions between different processing centers or technology systems for cyber incidents without interruption to business or loss of productivity or data.</p><p>The organization has validated that it is able to remediate systems damaged by zero-day attacks to maintain current recovery time objectives.</p><p>Cyber incident scenarios are used to stress test potential impacts.</p>',
	];
	$data['domain_5'][advisory_id_from_string('Backups')] = [
		'baseline' 		=> '<p>Minimal or no backups are performed.</p>',
		'evolving' 		=> '<p>Weekly full backups are performed andstored onsite.</p>',
		'intermediate' 	=> '<p>Weekly backups are performed, and full monthly backups are stored offsite.</p>',
		'advanced' 		=> '<p>Incremental backups are performed daily. Full backups are performed weekly and stored offsite. Some testing of restores occurs.</p>',
		'innovative' 	=> '<p>Incremental backups are performed daily. Copies are stored offsite twice a week. Full backups are performed weekly and stored offsite. Restores are frequently tested to ensure integrity of backups.</p>',
	];
	return empty($data[$domain]) ? false : $data[$domain];
}
function advisory_generate_csa_form_risk_contents($domain) {
	$data = [];
	// CATEGORY 1
	$data['category_1'][advisory_id_from_string('Total number of Internet service provider (ISP) connections (including remote sites)')] = [
		'least' 		=> '<p>No connections</p>',
		'minimal' 		=> '<p>1 – 5 Connections</p>',
		'moderate' 		=> '<p>6 – 10 Connections</p>',
		'significant'	=> '<p>11 – 20 Connections</p>',
		'most' 			=> '<p>More than 20 Connections</p>',
	];
	$data['category_1'][advisory_id_from_string(' Unsecured external connections, number of connections not users (e.g., file transfer protocol (FTP), Telnet, rlogin)')] = [
		'least' 		=> '<p>None</p>',
		'minimal' 		=> '<p>Few instances of unsecured connections (1–5)</p>',
		'moderate' 		=> '<p>Several instances of unsecured connections (6–10)</p>',
		'significant'	=> '<p>Significant instances of unsecured connections (11–25)</p>',
		'most' 			=> '<p>Substantial instances of unsecured connections (>25)</p>',
	];
	$data['category_1'][advisory_id_from_string('Wireless network access')] = [
		'least' 		=> '<p>No wireless access</p>',
		'minimal' 		=> '<p>Separate corporate and guest access with both being fully secured</p>',
		'moderate' 		=> '<p>Wireless access for corporate and guest have separate access points</p>',
		'significant'	=> '<p>Wireless access for corporate and Guest are separated logically</p>',
		'most' 			=> '<p>Wireless access for corporate and guest are the same</p>',
	];
	$data['category_1'][advisory_id_from_string('Wireless Access Users')] = [
		'least' 		=> '<p>No wireless users</p>',
		'minimal' 		=> '<p>1 – 25 users</p>',
		'moderate' 		=> '<p>26 – 100 users</p>',
		'significant'	=> '<p>101 – 250 users</p>',
		'most' 			=> '<p>More than 250 users</p>',
	];
	$data['category_1'][advisory_id_from_string('Wireless Access Points')] = [
		'least' 		=> '<p>No wireless access points</p>',
		'minimal' 		=> '<p>1 – 10 access points</p>',
		'moderate' 		=> '<p>11 – 25 access points</p>',
		'significant'	=> '<p>26 – 100 access points</p>',
		'most' 			=> '<p>More than 100 access points</p>',
	];
	$data['category_1'][advisory_id_from_string('Personal devices allowed to connect to the corporate network')] = [
		'least' 		=> '<p>None</p>',
		'minimal' 		=> '<p>Only one device type available; available to <5% of employees (staff, executives, managers); e-mail access only</p>',
		'moderate' 		=> '<p>Multiple device types used; available to <10% of employees (staff, executives, managers) and board; e-mail access only</p>',
		'significant'	=> '<p>Multiple device types used; available to <25% of authorized employees (staff, executives, managers) and board; e-mail and some applications accessed</p>',
		'most' 			=> '<p>Any devices  type used; available to >25% of employees (staff, executives, managers) and board; all applications accessed</p>',
	];
	$data['category_1'][advisory_id_from_string('Third parties, including number of organizations and number of individuals from vendors and subcontractors, with access to internal systems (e.g., virtual private network, modem, intranet, direct connection)')] = [
		'least' 		=> '<p>No third parties and no individuals from third parties with access to systems</p>',
		'minimal' 		=> '<p>Limited number of third parties and limited number of individuals from third parties with access; low complexity in how they access systems</p>',
		'moderate' 		=> '<p>Moderate number of third parties and moderate number of individuals from third parties with access; some complexity in how they access systems</p>',
		'significant'	=> '<p>Significant number of third parties and significant number of individuals from third parties with access; high level of complexity in terms of how they access systems</p>',
		'most' 			=> '<p>Substantial number of third parties and substantial number of individuals from third parties with access; high complexity in how they access systems</p>',
	];
	$data['category_1'][advisory_id_from_string('Customers with dedicated connections')] = [
		'least' 		=> '<p>None</p>',
		'minimal' 		=> '<p>Few dedicated connections (between 1–5)</p>',
		'moderate' 		=> '<p>Several dedicated connections (between 6–10)</p>',
		'significant'	=> '<p>Significant number of dedicated connections (between 11–25)</p>',
		'most' 			=> '<p>Substantial number of dedicated connections (>25)</p>',
	];
	$data['category_1'][advisory_id_from_string('Internally hosted and developed or modified vendor applications supporting critical activities')] = [
		'least' 		=> '<p>No applications</p>',
		'minimal' 		=> '<p>Few applications (between 1–5)</p>',
		'moderate' 		=> '<p>Several applications (between 6–10)</p>',
		'significant'	=> '<p>Significant number of applications (between 11–25)</p>',
		'most' 			=> '<p>Substantial number of applications and complexity (>25)</p>',
	];
	$data['category_1'][advisory_id_from_string('Internally hosted, vendor-developed applications supporting critical activities')] = [
		'least' 		=> '<p>No applications</p>',
		'minimal' 		=> '<p>1 – 5 applications</p>',
		'moderate' 		=> '<p>6 – 10 applications</p>',
		'significant'	=> '<p>11 – 25 applications</p>',
		'most' 			=> '<p>More than 25 applications</p>',
	];
	$data['category_1'][advisory_id_from_string('User-developed technologies and user computing that support critical activities (includes Microsoft Excel spreadsheets and Access databases or other user-developed tools)')] = [
		'least' 		=> '<p>No user-developed technologies</p>',
		'minimal' 		=> '<p>1 – 5</p>',
		'moderate' 		=> '<p>6 – 10</p>',
		'significant'	=> '<p>11 – 25</p>',
		'most' 			=> '<p>> 25</p>',
	];
	$data['category_1'][advisory_id_from_string('End-of-life (EOL) systems')] = [
		'least' 		=> '<p>No systems (hardware or software) that are past EOL or at risk of nearing EOL within 2 years</p>',
		'minimal' 		=> '<p>Few systems that are at risk of EOL and none that support critical operations</p>',
		'moderate' 		=> '<p>Several systems that will reach EOL within 2 years and some that support critical operation</p>',
		'significant'	=> '<p>A large number of systems that support critical operations at EOL or are at risk of reaching EOL in 2 years</p>',
		'most' 			=> '<p>Majority of critical operations dependent on systems that have reached EOL or will reach EOL within the next 2 years or an unknown number of systems that have reached EOL</p>',
	];
	$data['category_1'][advisory_id_from_string('Open Source Software (OSS)')] = [
		'least' 		=> '<p>No OSS</p>',
		'minimal' 		=> '<p>Limited OSS and none that support critical operations</p>',
		'moderate' 		=> '<p>Several OSS that support critical operations</p>',
		'significant'	=> '<p>Large number of OSS that support critical operations</p>',
		'most' 			=> '<p>Majority of operations dependent on OSS</p>',
	];
	$data['category_1'][advisory_id_from_string('Network devices (e.g., servers, routers, and firewalls; include physical and virtual)')] = [
		'least' 		=> '<p>Less than 25</p>',
		'minimal' 		=> '<p>26 – 50</p>',
		'moderate' 		=> '<p>51 – 100</p>',
		'significant'	=> '<p>101 – 250</p>',
		'most' 			=> '<p>> 250</p>',
	];
	$data['category_1'][advisory_id_from_string('Third-party service providers storing and/or processing information that support critical activities (Do not have access to internal systems, but the organization relies on their services)')] = [
		'least' 		=> '<p>No third parties</p>',
		'minimal' 		=> '<p>1 – 5</p>',
		'moderate' 		=> '<p>6 – 10</p>',
		'significant'	=> '<p>11 – 25</p>',
		'most' 			=> '<p>> 25</p>',
	];
	$data['category_1'][advisory_id_from_string('Cloud computing services hosted externally to support critical activities')] = [
		'least' 		=> '<p>No cloud providers</p>',
		'minimal' 		=> '<p>Few cloud providers; private cloud only (1– 3)</p>',
		'moderate' 		=> '<p>Several cloud providers (4–7)</p>',
		'significant'	=> '<p>Significant number of cloud providers (8– 10); cloud-provider locations used include international; use of public cloud</p>',
		'most' 			=> '<p>Substantial number of cloud providers (>10); cloud-provider locations used include international; use of public cloud</p>',
	];


	// CATEGORY 2
	// $data['category_2'][advisory_id_from_string('Are identities and credentials managed for authorized devices and users?')] = [
	// 	'least' 		=> '<p>Authorized users and devices are formally managed. Policies are in place to ensure the proper lifecycle management for authorized users and devices. Temporary accounts like contractors and temporary employees have time constraints defined for access</p>',
	// 	'minimal' 		=> '<p>Identities are managed for both authorized devices and users.</p>',
	// 	'moderate' 		=> '<p>Identities are managed for some authorized devices and users.</p>',
	// 	'significant'	=> '<p>Identities are managed for either authorized devices or users.</p>',
	// 	'most' 			=> '<p>Identities and credentials are not managed for devices or users.</p>',
	// ];
	$data['category_2'][advisory_id_from_string('Are identities and credentials managed for authorized devices and users?')] = [
		'least' 		=> '<p>Authorized users and devices are formally managed using an IAM system. Policies are in place to ensure the proper lifecycle management for authorized users and devices. Temporary accounts like contractors and temporary employees have time constraints defined for access.</p>',
		'minimal' 		=> '<p>Identities are managed for both authorized Active Directory (or equivalent) accounts for devices and users.</p>',
		'moderate' 		=> '<p>Identities are managed for some authorized Active Directory (or equivalent) accounts for devices and users.</p>',
		'significant'	=> '<p>Identities are managed for either authorized Active Directory (or equivalent) accounts for devices or users.</p>',
		'most' 			=> '<p>Identities and credentials are not managed for Active Directory (or equivalent) accounts for devices or users.</p>',
	];
	$data['category_2'][advisory_id_from_string('Is physical access to assets managed and protected?')] = [
		'least' 		=> '<p>Physical access to all critical assets is managed and protected including logging access attempts, reviewing access logs, and two-factor authentication where appropriate.</p>',
		'minimal' 		=> '<p>Physical access to all critical assets is managed and protected.</p>',
		'moderate' 		=> '<p>Physical access to most critical assets is managed or protected.</p>',
		'significant'	=> '<p>Physical access to some critical assets is managed or protected.</p>',
		'most' 			=> '<p>Physical access to assets is not managed or protected.</p>',
	];
	$data['category_2'][advisory_id_from_string('Is remote access managed?')] = [
		'least' 		=> '<p>Remote access is managed for VPN using two-factor authentication and logs are reviewed. Additionally, third party connections for contractors and support personnel are approved and documented.</p>',
		'minimal' 		=> '<p>Remote access is managed for VPN using two-factor authentication and access logs are reviewed.</p>',
		'moderate' 		=> '<p>Remote access is managed for VPN connectivity and a roadmap is in place to implement two-factor authentication.</p>',
		'significant'	=> '<p>Remote access is managed for VPN connectivity.</p>',
		'most' 			=> '<p>Remote access is not managed.</p>',
	];
	$data['category_2'][advisory_id_from_string('Are access permissions managed, incorporating the principles of least privilege and separation of duties?')] = [
		'least' 		=> '<p>Privileges are assigned using the principles of least privileges and separation of duties as dictated by policy. User privileges are reviewed to account for changes in user roles and responsibilities.</p>',
		'minimal' 		=> '<p>Privileges are assigned using the principles of least privileges and separation of duties as dictated by policy</p>',
		'moderate' 		=> '<p>Privileges are assigned with principles of least privilege and separation duties in mind. </p>',
		'significant'	=> '<p>Access permissions occasionally incorporate the principles of least privilege or separation of duties  (process is not enforced).</p>',
		'most' 			=> '<p>Access permissions do not incorporate the principles of least privilege or separation of duties.</p>',
	];
	$data['category_2'][advisory_id_from_string('Is network integrity protected by incorporating network segregation where appropriate?')] = [
		'least' 		=> '<p>Network segregation is used for all areas within in the network. Critical network segments are separated using a firewall and access attempts across critical segments are logged.</p>',
		'minimal' 		=> '<p>Network segregation is used for all appropriate areas within the network.</p>',
		'moderate' 		=> '<p>Network segregation is used for most areas of the network.</p>',
		'significant'	=> '<p>Network segregation is used for some areas of the network.</p>',
		'most' 			=> '<p>Network segregation is not used.</p>', 
	];
	// DOMAIN 3
	$data['category_3'][advisory_id_from_string('Physical systems and devices are inventoried')] = [
		'least' 		=> '<p>All physical systems and devices are inventoried. Policies have been established to ensure regular review of all physical systems and devices.</p>',
		'minimal' 		=> '<p>All physical systems and devices are inventoried.</p>',
		'moderate' 		=> '<p>Most physical systems and devices are inventoried.</p>',
		'significant'	=> '<p>Some physical systems and devices are inventoried.</p>',
		'most' 			=> '<p>No physical systems or devices are inventoried.</p>',
	];
	$data['category_3'][advisory_id_from_string('Software platforms and  applications are inventoried')] = [
		'least' 		=> '<p>All software platforms and applications are inventoried. Policies have been established to ensure regular review of all software platforms and applications.</p>',
		'minimal' 		=> '<p>All software platforms and applications are inventoried.</p>',
		'moderate' 		=> '<p>Most software platforms and applications are inventoried.</p>',
		'significant'	=> '<p>Some software platforms and applications are inventoried.</p>',
		'most' 			=> '<p>No software platforms and applications are inventoried.</p>',
	];
	$data['category_3'][advisory_id_from_string('Systems, devices, and software platforms prioritized based on classification, criticality, and business value')] = [
		'least' 		=> '<p>All systems are prioritized. The classification and business value is reviewed and appropriate resource classifications are being designated like confidential, internal, and public.</p>',
		'minimal' 		=> '<p>All systems are prioritized.</p>',
		'moderate' 		=> '<p>Some systems are prioritized</p>',
		'significant'	=> '<p>Critical systems like servers and databases are prioritized.</p>',
		'most' 			=> '<p>Systems are not prioritized.</p>',
	];
	$data['category_3'][advisory_id_from_string('Clearly defined cybersecurity roles and responsibilities for internal users, external customers and partners')] = [
		'least' 		=> '<p> All roles and responsibilities are clearly defined and communicated throughout the organization as dictated by policy. An appropriate vendor management program is in place.</p>',
		'minimal' 		=> '<p>All roles and responsibilities are clearly defined and communicated throughout the organization for all employees and external personnel (vendors, customers, and partners)</p>',
		'moderate' 		=> '<p>Cybersecurity roles and responsibilities are defined for internal employees, as well as some external personnel (vendors, customers, and partners).</p>',
		'significant'	=> '<p>Cybersecurity roles are defined and communicated internally</p>',
		'most' 			=> '<p>Not defined</p>',
	];
	$data['category_3'][advisory_id_from_string('Host IT services for other organizations (either through joint systems or administrative support)')] = [
		'least' 		=> '<p>No hosted services for other organizations</p>',
		'minimal' 		=> '<p>1 – 5</p>',
		'moderate' 		=> '<p>6 – 10</p>',
		'significant'	=> '<p>11 – 25</p>',
		'most' 			=> '<p>> 25</p>',
	];
	
	// CATEGORY 4
	$data['category_4'][advisory_id_from_string('Mergers and acquisitions (including divestitures and joint ventures)')] = [
		'least' 		=> '<p>None planned</p>',
		'minimal' 		=> '<p>Open to initiating discussions or actively seeking a merger or acquisition</p>',
		'moderate' 		=> '<p>In discussions with at least at least 1 party</p>',
		'significant'	=> '<p>A sale or acquisition has been publicly announced within the past year, in negotiations with 1 or more parties</p>',
		'most' 			=> '<p>Multiple ongoing integrations of acquisitions are in process</p>',
	];
	$data['category_4'][advisory_id_from_string('Direct employees (including information technology and cybersecurity contractors)')] = [
		'least' 		=> '<p>1 – 50</p>',
		'minimal' 		=> '<p>51 – 250</p>',
		'moderate' 		=> '<p>251 – 500</p>',
		'significant'	=> '<p>501 – 1,000</p>',
		'most' 			=> '<p>> 1,000</p>',
	];
	$data['category_4'][advisory_id_from_string('Changes in IT and information security staffing')] = [
		'least' 		=> '<p>Key positions filled; low or no turnover of personnel</p>',
		'minimal' 		=> '<p>Staff vacancies exist for non-critical roles</p>',
		'moderate' 		=> '<p>Some turnover in key or senior positions</p>',
		'significant'	=> '<p>Frequent turnover in key staff or senior positions</p>',
		'most' 			=> '<p>Vacancies in senior key positions for long periods; high level of employee turnover in IT or information security</p>',
	];
	$data['category_4'][advisory_id_from_string('Privileged access (Administrators– network, database, applications, systems, etc.)')] = [
		'least' 		=> '<p>Limited number of administrators; limited or no external administrators</p>',
		'minimal' 		=> '<p>Level of turnover in administrators does not affect operations or activities; may utilize some external administrators</p>',
		'moderate' 		=> '<p>Level of turnover in administrators affects operations; number of administrators for individual systems or applications exceeds what is necessary</p>',
		'significant'	=> '<p>High reliance on external administrators; number of administrators is not sufficient to support level or pace of change</p>',
		'most' 			=> '<p>High employee turnover in network administrators; many or most administrators are external (contractors or vendors); experience in network administration is limited</p>',
	];
	$data['category_4'][advisory_id_from_string('Changes in IT environment (e.g., network, infrastructure, critical applications, technologies supporting new products or services)')] = [
		'least' 		=> '<p>Stable IT environment</p>',
		'minimal' 		=> '<p>Infrequent or minimal changes in the IT environment</p>',
		'moderate' 		=> '<p>Frequent adoption of new technologies</p>',
		'significant'	=> '<p>Volume of significant changes is high</p>',
		'most' 			=> '<p>Substantial change in outsourced provider(s) of critical IT services; large and complex changes to the environment occur frequently</p>',
	];
	$data['category_4'][advisory_id_from_string('Locations of branches/business presence')] = [
		'least' 		=> '<p>1 municipality</p>',
		'minimal' 		=> '<p>1 province</p>',
		'moderate' 		=> '<p>1 country</p>',
		'significant'	=> '<p>1 – 10 countries</p>',
		'most' 			=> '<p>>10 countries</p>',
	];
	$data['category_4'][advisory_id_from_string('Locations of operations/data centers')] = [
		'least' 		=> '<p>1 municipality</p>',
		'minimal' 		=> '<p>1 province</p>',
		'moderate' 		=> '<p>1 country</p>',
		'significant'	=> '<p>1 – 10 countries</p>',
		'most' 			=> '<p>>10 countries</p>',
	];

	// CATEGORY 5
	$data['category_5'][advisory_id_from_string('Asset vulnerabilities are identified and documented')] = [
		'least' 		=> '<p>The organization has a mature vulnerability management program that is constantly improving and evolving.</p>',
		'minimal' 		=> '<p>Asset vulnerabilities are addressed by an origination\'s vulnerability management program.</p>',
		'moderate' 		=> '<p>Vulnerability management is on the roadmap and is currently an ad hoc process</p>',
		'significant'	=> '<p>Vulnerability management is an informal process occurring once a year or less.</p>',
		'most' 			=> '<p>Asset vulnerabilities are not identified and documented.</p>',
	];
	$data['category_5'][advisory_id_from_string('Where does the organization currently receive its vulnerability and threat information?')] = [
		'least' 		=> '<p>Special interests forums and external professionals are leveraged in conjunction with internal staff to research and identify threats and vulnerabilities.</p>',
		'minimal' 		=> '<p>Threat and vulnerability information is researched by multiple groups within the organization.</p>',
		'moderate' 		=> '<p>A process is being developed to expand threat and vulnerability research beyond IT.</p>',
		'significant'	=> '<p>The internal IT staff is responsible for researching and identifying threat and vulnerability information.</p>',
		'most' 			=> '<p>Threat and vulnerability information is not sought out by the organization.</p>',
	];
	$data['category_5'][advisory_id_from_string('What kind of threats are identified and documents?')] = [
		'least' 		=> '<p>A mature vulnerability program exists to address internal and external threats. The threats are reviewed and prioritized by management.</p>',
		'minimal' 		=> '<p>Vulnerability management programs address internal or external threats.</p>',
		'moderate' 		=> '<p>Most internal threats are identified and documented</p>',
		'significant'	=> '<p>Some internal threats are identified and documented.</p>',
		'most' 			=> '<p>Threats are not identified and documented.</p>',
	];
	$data['category_5'][advisory_id_from_string('What type of business impacts are identified in relation to vulnerabilities and threats?')] = [
		'least' 		=> '<p>The organization uses a repeatable process to consider the likelihood a vulnerability is exploited and the likelihood of business impact due to the exploit.</p>',
		'minimal' 		=> '<p>The organization has identified all threats and impacted formally and continually.</p>',
		'moderate' 		=> '<p>The likeliness and business impact has been identified for most threats to the organization.</p>',
		'significant'	=> '<p>The likeliness and business impact has been identified for some threats to the organization.</p>',
		'most' 			=> '<p>Impact to the business has not been outlined as it pertains to vulnerabilities and threats.</p>',
	];
	$data['category_5'][advisory_id_from_string('How does the organization determine risk?')] = [
		'least' 		=> '<p>Risk management is performed formally as dictated by policy.  The organization prioritizes threats and uses a repeatable process to manage threats considering compensating controls.</p>',
		'minimal' 		=> '<p>Risk management is performed formally as dictated by policy using a repeatable process.</p>',
		'moderate' 		=> '<p>Risk management is on the roadmap and is currently an ad hoc process</p>',
		'significant'	=> '<p>Risk management is performed informally.</p>',
		'most' 			=> '<p>The organization does not perform risk management.</p>',
	];
	$data['category_5'][advisory_id_from_string('Vulnerability Testing')] = [
		'least' 		=> '<p>Independent third parties perform annual external and internal penetration testing. Organization creates formalplan to address discovered vulnerabilities.</p>',
		'minimal' 		=> '<p>Independent third parties perform annual external penetration testing.  Organization creates formalplan to address discovered vulnerabilities.</p>',
		'moderate' 		=> '<p>Regular vulnerability testing (at least monthly)is performed by IT staff using tools such as Nessus or GFI Languard. Organization creates plan to address discovered vulnerabilities.</p>',
		'significant'	=> '<p>IT Staff sporadically run a vulnerability tool such as Nessus or GFI Languard. Organization creates plan to address discovered vulnerabilities.</p>',
		'most' 			=> '<p>No vulnerability testing is performed.</p>',
	];
	return empty($data[$domain]) ? false : $data[$domain];
}
function advisory_generate_csa_pdf_subsections() {
	if (is_admin()) {
		$data = [];
		$data[] = ['type' => 'subheading', 'content' => 'Heading'];
		$data[] = ['id' => 'csa_risk', 'type' => 'textarea', 'title' => 'Risk - Intro', 'default' => demoContent('',1)];
		$data[] = ['id' => 'csa_cybersecurity', 'type' => 'textarea', 'title' => 'Cybersecurity - Intro', 'default' => demoContent('',2)];
		return $data;
	}
}