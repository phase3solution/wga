<?php 
/* Template Name: PDF */
require_once get_template_directory() . '/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$postID = !empty($_GET['pid']) ? $_GET['pid'] : false;
if ($postID == 'service_criticality_report_card') {
    $data = $services = [];
    $service_si = 1;
    $catalogue = 'Service Criticality Report Card';
	$dompdf = new Dompdf();
    $company   = advisory_get_user_company();
    $upstreams = advisory_get_reportcard_data();
    $services  = advisory_prepare_export_service_criticality_reportcard_data_for_pdf($upstreams);
    // echo '<br><pre>'. print_r($services, true) .'</pre>';
    
    if ($catalogue && !empty($services)) {
		$html = '';
		$html .= '<style>';
            $html .= '@page {margin: 10px 10px 30px 10px; }';
            $html .= '*{font-family: sans-serif;line-height:1.1;font-size: 12px;}';
            $html .= 'table {border-collapse: collapse;}';
            $html .= 'table td, table th{vertical-align: middle;}';
            $html .= '.m-0{margin:0;}';
            $html .= '.clearfix{clear: both;}';
            // HEADER
            $html .= '.header{}';
            $html .= '.titleContainer{float: left;}';
            $html .= '.title{ font-size: 22px; margin: 0;}';
            $html .= '.reportDate{margin: 0;font-weight:bold; padding-top: 7px;}';
            $html .= '.companyContainer{ text-align:center;}';
            $html .= '.companyName{font-size: 17px;font-weight: 700; margin:0;}';
            // LEGEND
            $html .= '.legendWrapper{margin: 30px 0 -20px 750px; position:relative;}';
            $html .= '.legendContainer{margin:0;}';
            $html .= '.legendContainer>li{display:inline-block;margin-left:20px;vertical-align:top;}';
            $html .= '.tierLegends{margin: 0; padding: 0; position: absolute;top:0; left: 200px;}';
            $html .= '.tierLegends li{ list-style: none; font-weight: 600; line-height:1.1;}';
            $html .= '.tierLegends li span{ padding: 2px 3px; font-size:10px;}';
            $html .= '.legends{margin: 0;padding: 0;}';
            $html .= '.legends li{ list-style: none; font-weight: 600;}';
            // CONTENT
            $html .= '.contentTitle{ font-size:15px; font-weight: 700;margin: 20px 0 10px 0; color:#3d5da8;line-height:1;}';
            $html .= '.content{border-top: 2px solid #000000; width: 100%; margin-bottom: 0px;}';
            $html .= '.content thead{}';
            $html .= '.content thead tr{ text-align:left !important;}';
            $html .= '.content thead tr th{border-bottom: 1px solid #000000;padding: 10px 0 5px 0;}';
            $html .= '.content tbody{}';
            $html .= '.content tbody tr td{padding: 2px 0;}';
            $html .= '.content tbody tr:nth-child(even) td{background:#e2e2e2;}';
            $html .= '.content tbody tr td:last-child{padding-left:3px;}';
            // COLORS
            $html .= ' .content tbody tr td.bg-red, .tierLegends .bg-red {background-color: #ed2227;}';
			$html .= ' .content tbody tr td.bg-orange, .tierLegends .bg-orange {background-color: #f77e28;}';
			$html .= ' .content tbody tr td.bg-yellow, .tierLegends .bg-yellow {background-color: #ffdc5d;}';
			$html .= ' .content tbody tr td.bg-deepgreen, .tierLegends .bg-deepgreen {background-color: #0f9742;}';
			$html .= ' .content tbody tr td.bg-deepblue, .tierLegends .bg-deepblue {background-color: #169ad7;}';
        $html .= '</style>';
        // HEADER
        $html .= '<div class="header">';
            $html .= '<div class="titleContainer">';
                $html .= '<h1 class="title">'.$catalogue.'</h1>';
                $html .= '<p class="reportDate">Report Date: '.date('F dS, Y').'</p>';
            $html .= '</div>';
            $html .= '<div class="companyContainer">';
                $html .= '<p class="companyName">'.$company->name.'</p>';
            $html .= '</div>';
            $html .= '<div class="clearfix"></div>';
        $html .= '</div>';
        // LEGEND
        $html .= '<div class="legendWrapper">';
            $html .= '<ul class="legends">';
                $html .= '<li>RTO - Recovery Time Objective</li>';
                $html .= '<li>&nbsp;</li>';
                $html .= '<li>&nbsp;</li>';
            $html .= '</ul>';

            $html .= '<ul class="tierLegends">';
                $html .= '<li>TIER 1 = <span class="bg-red">&nbsp;</span> Critical</li>';
                $html .= '<li>TIER 2 = <span class="bg-orange">&nbsp;</span> Urgent</li>';
                $html .= '<li>TIER 3 = <span class="bg-yellow">&nbsp;</span> Important</li>';
                $html .= '<li>TIER 4 = <span class="bg-deepgreen">&nbsp;</span> Normal</li>';
                $html .= '<li>TIER 5 = <span class="bg-deepblue">&nbsp;</span> Non-Essential</li>';
            $html .= '</ul>';
        $html .= '</div>';
        // echo $html; exit();
        // CONTENT
		$item_number = 1;
    	foreach ($services as $service_id => $service) {
			if (!empty($service)) {
				$serviceName = !empty($service[0]['catalogue']) ? $service[0]['catalogue'] : '';
				$pageBreak = ' style="page-break-inside: avoid"';
				$serviceCounter = count($service);
				$breakingPoint = 8;
				// echo '<br><pre>'. print_r($service, true) .'</pre>'; exit;
				for ($i=0; $i < $serviceCounter; $i+=$breakingPoint) { 
					$html .= '<div class="contentContainer"'.$pageBreak.'>';
						if ($i < 1) {$html .= '<h2 class="contentTitle">Service: '.$serviceName.'</h2>';}
						$html .= '<table class="content">';
							$html .= '<thead>';
								$html .= '<tr>';
									$html .= '<th style="width:40px">Item#</th>';
									$html .= '<th style="width:200px">Technology Dependency</th>';
									$html .= '<th style="width:330px">Department</th>';
									$html .= '<th>Service/Process</th>';
									$html .= '<th style="width:70px">RTO</th>';
									$html .= '<th style="width:40px">Tier</th>';
								$html .= '</tr>';
							$html .= '</thead>';
							$html .= '<tbody>';
							$itemCountPerTable = $i + $breakingPoint;
							for ($item=$i; $item < $itemCountPerTable; $item++) { 
								if ($item_number > 100) break 2;
								if (empty($service[$item])) break;
								$html .= '<tr>';
									$html .= '<td>'.$item_number.'</td>';
									$html .= '<td>'.$service[$item]['dependency'].'</td>';
									$html .= '<td>'.@$service[$item]['department'].'</td>';
									$html .= '<td>'.@$service[$item]['service'].'</td>';
									$html .= '<td>'.@$service[$item]['rto'].'</td>';
									$html .= '<td class="'.$service[$item]['tier_class'].'">'.$service[$item]['tier'].'</td>';
								$html .= '</tr>';
								$item_number++;
							}
							$html .= '</tbody>';
						$html .= '</table>';
					$html .= '</div>';
					$service_si++;
				}
			}
		}
    	// echo $html; exit();
    	$bottomLine = '_________________________________________________________________________________';
		$bottomLine .= '_________________________________________________________________________________';
		$bottomLine .= '_______________________';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->set_option("isPhpEnabled", true);
        $dompdf->render();
        $dompdf->get_canvas()->page_text(8, 560, $bottomLine, '', 8, array(0,0,0));
        $dompdf->get_canvas()->page_text(782, 573, "Page : {PAGE_NUM} of {PAGE_COUNT}", '', 8, array(0,0,0));
	}
	$dompdf->stream($filename,array("Attachment"=>0));
} else if ($postID == 'catelogue_summary') {
    $data = $services = [];
    $service_si = 1;
    $title = ['it'=>'IT Service Catalogue', 'desktop'=>'Desktop Service Catalogue', 'cloud'=>'Cloud Service Catalogue'];
	$catalogue = !empty($_GET['catalogue']) ? $_GET['catalogue'] : false;
	$filename = "Catelogue Summary - ".$catalogue;
	$dompdf = new Dompdf();
    $company    = advisory_get_user_company();
    $dependencies = advisory_get_dashboard_report_card_data();
    $services = advisory_prepare_catelogue_summary_pdf_data($dependencies[$catalogue]);
    // echo '<br><pre>'. print_r($services, true) .'</pre>';
    
    if ($catalogue && !empty($services)) {
		$html = '';
		$html .= '<style>';
            $html .= '@page {margin: 10px 10px 25px 10px; }';
            $html .= '*{font-family: sans-serif;line-height:1.1;font-size: 12px;}';
            $html .= 'table {border-collapse: collapse;}';
            $html .= 'table td, table th{vertical-align: middle;}';
            $html .= '.m-0{margin:0;}';
            $html .= '.clearfix{clear: both;}';
            // HEADER
            $html .= '.header{}';
            $html .= '.titleContainer{float: left;}';
            $html .= '.title{ font-size: 22px; margin: 0;}';
            $html .= '.reportDate{margin: 0;font-weight:bold; padding-top: 7px;}';
            $html .= '.companyContainer{ text-align:center;}';
            $html .= '.companyName{font-size: 17px;font-weight: 700; margin:0;}';
            // LEGEND
            $html .= '.legendWrapper{margin: 30px 0 -20px 750px; position:relative;}';
            $html .= '.legendContainer{margin:0;}';
            $html .= '.legendContainer>li{display:inline-block;margin-left:20px;vertical-align:top;}';
            $html .= '.tierLegends{margin: 0; padding: 0; position: absolute;top:0; left: 200px;}';
            $html .= '.tierLegends li{ list-style: none; font-weight: 600; line-height:1.1;}';
            $html .= '.tierLegends li span{ padding: 2px 3px; font-size:10px;}';
            $html .= '.legends{margin: 0;padding: 0;}';
            $html .= '.legends li{ list-style: none; font-weight: 600;}';
            // CONTENT
            $html .= '.contentTitle{ font-size:15px; font-weight: 700;margin: 20px 0 10px 0; color:#3d5da8;line-height:1;}';
            $html .= '.content{border-top: 2px solid #000000; width: 100%; margin-bottom: 0px;}';
            $html .= '.content thead{}';
            $html .= '.content thead tr{ text-align:left !important;}';
            $html .= '.content thead tr th{border-bottom: 1px solid #000000;padding: 10px 0 5px 0;}';
            $html .= '.content tbody{}';
            $html .= '.content tbody tr td{padding: 2px 0;}';
            $html .= '.content tbody tr:nth-child(even) td{background:#e2e2e2;}';
            $html .= '.content tbody tr td:last-child{padding-left:3px;}';
            // COLORS
            $html .= ' .content tbody tr td.bg-red, .tierLegends .bg-red {background-color: #ed2227;}';
			$html .= ' .content tbody tr td.bg-orange, .tierLegends .bg-orange {background-color: #f77e28;}';
			$html .= ' .content tbody tr td.bg-yellow, .tierLegends .bg-yellow {background-color: #ffdc5d;}';
			$html .= ' .content tbody tr td.bg-deepgreen, .tierLegends .bg-deepgreen {background-color: #0f9742;}';
			$html .= ' .content tbody tr td.bg-deepblue, .tierLegends .bg-deepblue {background-color: #169ad7;}';
        $html .= '</style>';
        // HEADER
        $html .= '<div class="header">';
            $html .= '<div class="titleContainer">';
                $html .= '<h1 class="title">'.$title[$catalogue].'</h1>';
                $html .= '<p class="reportDate">Report Date: '.date('F jS, Y').'</p>';
            $html .= '</div>';
            $html .= '<div class="companyContainer">';
                $html .= '<p class="companyName">'.$company->name.'</p>';
            $html .= '</div>';
            $html .= '<div class="clearfix"></div>';
        $html .= '</div>';
        // LEGEND
        $html .= '<div class="legendWrapper">';
            $html .= '<ul class="legends">';
                $html .= '<li>RTO - Recovery Time Objective</li>';
                $html .= '<li>RPO - Recovery Point Objective</li>';
                $html .= '<li> &nbsp;&nbsp; CL - Criticality Level</li>';
            $html .= '</ul>';

            $html .= '<ul class="tierLegends">';
                $html .= '<li>TIER 1 = <span class="bg-red">&nbsp;</span> Critical</li>';
                $html .= '<li>TIER 2 = <span class="bg-orange">&nbsp;</span> Urgent</li>';
                $html .= '<li>TIER 3 = <span class="bg-yellow">&nbsp;</span> Important</li>';
                $html .= '<li>TIER 4 = <span class="bg-deepgreen">&nbsp;</span> Normal</li>';
                $html .= '<li>TIER 5 = <span class="bg-deepblue">&nbsp;</span> Non-Essential</li>';
            $html .= '</ul>';
        $html .= '</div>';
        // CONTENT
		$item_number = 1;
    	foreach ($services as $service_id => $service) {
			if (!empty($service)) {
				$serviceName = !empty($service[0]['name']) ? $service[0]['name'] : '';
				$pageBreak = ' style="page-break-inside: avoid"';
				$serviceCounter = count($service);
				$breakingPoint = 8;
				// echo '<br><pre>'. print_r($service, true) .'</pre>'; exit;
				for ($i=0; $i < $serviceCounter; $i+=$breakingPoint) { 
					$html .= '<div class="contentContainer"'.$pageBreak.'>';
						if ($i < 1) {$html .= '<h2 class="contentTitle">Service: '.$serviceName.'</h2>';}
						$html .= '<table class="content">';
							$html .= '<thead>';
								$html .= '<tr>';
									$html .= '<th style="width:40px">Item#</th>';
									$html .= '<th style="width:200px">Technology Dependency</th>';
									$html .= '<th>Department</th>';
									$html .= '<th>Service/Process</th>';
									$html .= '<th style="width:75px; text-align:center!important;;">RTO</th>';
									$html .= '<th style="width:75px; text-align:center!important;;">RPO</th>';
									$html .= '<th style="width:30px; text-align:center!important;">CL</th>';
									$html .= '<th style="width:40px; text-align:center!important;;">Tier</th>';
								$html .= '</tr>';
							$html .= '</thead>';
							$html .= '<tbody>';
							$itemCountPerTable = $i + $breakingPoint;
							for ($item=$i; $item < $itemCountPerTable; $item++) { 
								// if ($item_number > 100) break 2;
								if (empty($service[$item])) break;
								$html .= '<tr>';
									$html .= '<td>'.$item_number.'</td>';
									$html .= '<td>'.$service[$item]['dependency'].'</td>';
									$html .= '<td>'.@$service[$item]['area'].'</td>';
									$html .= '<td>'.@$service[$item]['service'].'</td>';
									$html .= '<td style="text-align:center;">'.@$service[$item]['rto'].'</td>';
									$html .= '<td style="text-align:center;">'.@$service[$item]['rpo'].'</td>';
									$html .= '<td style="text-align:center;">'.@$service[$item]['cl']['value'].'</td>';
									$html .= '<td style="text-align:center;" class="'.advisory_rto_to_tier_class($service[$item]['rto']).'">'.advisory_rto_to_tier($service[$item]['rto']).'</td>';
								$html .= '</tr>';
								$item_number++;
							}
							$html .= '</tbody>';
						$html .= '</table>';
					$html .= '</div>';
					$service_si++;
				}
			}
		}
    	// echo $html; exit();

    	$bottomLine = '_________________________________________________________________________________';
		$bottomLine .= '_________________________________________________________________________________';
		$bottomLine .= '_______________________';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->set_option("isPhpEnabled", true);
        $dompdf->render();
        $dompdf->get_canvas()->page_text(8, 560, $bottomLine, '', 8, array(0,0,0));
        $dompdf->get_canvas()->page_text(782, 573, "Page : {PAGE_NUM} of {PAGE_COUNT}", '', 8, array(0,0,0));
	}
	$dompdf->stream($filename,array("Attachment"=>0));
} else if ($postID) {
	$postType 	=  get_post_type($postID);
	$filename = "Executive Summary";
	$dompdf = new Dompdf();
	if ($postType == 'csa') {
		$company 	= advisory_get_user_company();
		$type = !empty($_GET['area']) ? $_GET['area'] : false;
		$sectionID = $type.'_section';
		$opts = get_post_meta($postID, 'form_opts', true);
		$html = '';
		$html .= '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';	
		$html .= '<style>';
	        $html .= '@import url("https://fonts.googleapis.com/css?family=Roboto:400,500,700");';
	        $html .= '*{font-family: "Roboto", sans-serif !important;line-height:1.1;font-size: 14px;}';
	        $html .= 'strong{font-weight:700 !important;}';
	        $html .= 'table{width: 100%; margin-bottom: 1.3rem !important;}';
	        $html .= 'table td, table th{vertical-align: middle;}';
	        $html .= 'ul {padding: 0; margin: 0;padding-left: 15px;}';
	        $html .= '.title{font-size:18px !important;text-transform: uppercase;}';
	        $html .= '.content{font-size:13px !important;}';
	        $html .= '.heading{font-size:18px !important;color: #fff;}';
	        $html .= '.subhead{font-size: 16px !important;color: #fff;}';
	        $html .= '.gray{background-color:#585858;color: #fff;}';
	        $html .= '.black{background-color:#000;color:#fff;}';
	        $html .= '.blue{background-color: #001f5f;color:#fff;}';
	        $html .= '.red {background: #e40613;}';
            $html .= '.orange {background: #ea4e1b;}';
            $html .= '.yellow {background: #fdea11;}';
            $html .= '.green {background: #3baa34;}';
            $html .= '.aqua {background: #36a9e0;}';
	        $html .= '.technology{background-color: #6f6f00;color:#fff;}';
	        $html .= '.people{background-color: #400080;color:#fff;}';
	        $html .= '.center{text-align: center;}';
	        $html .= '.semibold{font-weight:600;}';
	        $html .= '.table td {padding: 5px 10px !important;border-bottom: 1px solid;}';
	        $html .= 'td.nopadding {padding: 0 !important;}';
	        $html .= '.table-bordered td.fixedbg{width: 25px !important;}';
	        $html .= '.table-bordered td {border: 1px solid #6e7275 !important;}';
	        $html .= '.table-bordered td.noborder {border: 0px !important;}';
	        $html .= '.logo {height: 90px; width: auto}';
	        $html .= '.small {font-size: 13px !important;}';
	        $html .= '@page {margin: 35px 25px 40px; }';
	        $html .= '.light-gray {background-color: #d9d9d9;}';
	        $html .= '.m0 {margin:0!important;}';
	        $html .= ' .avoidBreak {page-break-inside: avoid !important; margin: 4px 0 4px 0;  /* to keep the page break from cutting too close to the text in the div */ }';
	    $html .= '</style>';
        $title .= $company->name.' '.get_the_date('F jS, Y',$postID);
        $introduction = advisory_form_default_values($postID, 'intro');
	    if ($type == 'risk') {
	    	$title = 'Inherent Risk Profile  ';
	        $intro = !empty($introduction['risk']) ? $introduction['risk'] : '';
	        $headerTitle = 'RISK SELF-ASSESSMENT (CONFIDENTIAL) - Not for external distribution';
	    } else {
	        $title = 'Cybersecurity Maturity  ';
	        $intro = !empty($introduction['cyberc']) ? $introduction['cyberc'] : '';
	        $headerTitle = 'CYBERSECURITY SELF-ASSESSMENT (CONFIDENTIAL) - Not for external distribution';
	    }
		$html .= '<div class="tableContainer">';
            $html .= '<table class="table table-bordered">';
                $html .= '<tr>';
                    $html .= '<td class="subhead black align-middle" colspan="5" rowspan="2" style="width: 80%;font-size: 22px !important;">'.$title.'</span></td>';
                    $html .= '<td class="black center">Risk Level</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="nopadding" width: 10%;>';
                        $html .= '<table class="table table-bordered" style="margin-bottom: 0 !important;">';
                            $html .= '<tr>';
                                $html .= '<td class="nopadding aqua fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                                $html .= '<td class="noborder">Least</td>';
                            $html .= '</tr>';
                        $html .= '</table>';
                    $html .= '</td>';
                $html .= '</tr>';
                $html .= '';
                $html .= '<tr>';
                    $html .= '<td class="small" colspan="5" rowspan="3">'.$intro.'</td>';
                    $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding green fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">Minimal</td></tr></table></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding yellow fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">Moderate &nbsp;&nbsp;&nbsp;</td></tr></table></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding orange fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">Significant</td></tr></table></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                    $html .= '<td class="black" style="text-align: center;width: 250px;">Category</td>';
                    $html .= '<td class="black" style="text-align: center; width: 60px;padding-left: 0;padding-right: 0;">Risk Level</td>';
                    $html .= '<td class="black" style="text-align: center; width: 350px" colspan="3">Process Areas</td>';
                    $html .= '<td class="nopadding" style=" width: 150px">';
                        $html .= '<table class="table table-bordered" style="margin-bottom: 0 !important;">';
                            $html .= '<tr>';
                                $html .= '<td class="nopadding red fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                                $html .= '<td class="noborder">Most</td>';
                            $html .= '</tr>';
                        $html .= '</table>';
                    $html .= '</td>';
                $html .= '</tr>';
				if (!empty($opts[$sectionID])) {
					foreach ($opts[$sectionID] as $category) {
						$categoryID = advisory_id_from_string($category['name']) . '_domains';
						$department_id = $sectionID.'_'.$categoryID;
						$csa = advisory_form_default_values($postID, $department_id.'_csa');
						$pdf = advisory_form_default_values($postID, $department_id . '_pdf');
						$risk = !empty($csa['cls']) ? $csa['cls'] : '';
						$pa = !empty($pdf['pa']) ? $pdf['pa'] : '';
						// $html .= '<tr><td colspan="6">'.json_encode($pa).'</td></tr>';

		                $html .= '<tr>';
		                    $html .= '<td class="blue" style="text-align: center;vertical-align: middle;">'.$category['title'].'</td>';
		                    $html .= '<td class="'.$risk.' nopadding" style="text-align: center; max-width: 60px;padding-left: 0;padding-right: 0;" ><p style="visibility: hidden;">Sr-Only<br>Sr-Only</p></td>';
		                    $html .= '<td class="blue" style="text-align: center;vertical-align: middle;" colspan="4">'.$pa.'</td>';
		                $html .= '</tr>';
		                if ($pdf) {
			                $html .= '<tr>';
			                    $html .= '<td class="gray">Assessment</td>';
			                    $html .= '<td colspan="5" class="small">'.@$pdf['at'].'</td>';
			                $html .= '</tr>';
			                $html .= '<tr>';
			                    $html .= '<td colspan="6" class="small">'.@$pdf['ad'].'</td>';
			                $html .= '</tr>';
			                $html .= '<tr>';
			                    $html .= '<td class="gray">Summary</td>';
			                    $html .= '<td colspan="5" class="small">'.@$pdf['st'].'</td>';
			                $html .= '</tr>';
			                $html .= '<tr>';
			                    $html .= '<td colspan="6" class="small">'.@$pdf['sd'].'</td>';
			                $html .= '</tr>';
		                }
					}
				}
            $html .= '</table>';
        $html .= '</div>';
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'landscape');
		$dompdf->set_option("isPhpEnabled", true);
		$dompdf->render();
		if ($type == 'risk') $dompdf->get_canvas()->page_text(583, 10, $headerTitle, '', 7, array(0,0,0));
		else $dompdf->get_canvas()->page_text(540, 10, $headerTitle, '', 7, array(0,0,0));
        $dompdf->get_canvas()->page_script('
          // $pdf is the variable containing a reference to the canvas object provided by dompdf
          $pdf->line(20,545,822,545,array(0,0,0),1);
        ');
        $dompdf->get_canvas()->page_text(776, 572, "Page : {PAGE_NUM} of {PAGE_COUNT}", '', 8, array(0,0,0));
	} else if ($postType == 'bia') {
		$prefix 	= $postType.'_pdf_';
		$company 	= advisory_get_user_company();
		$data 		= advisory_get_scorecard_data($postID);
		$pdfData 	= get_term_meta($company->term_id, $prefix, true);
		// PDF DATA
		$html = '';
		$html .= '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';	
		if ($data) {
			$html .= '<style>';
		        $html .= '@import url("https://fonts.googleapis.com/css?family=Roboto:400,500,700");';
		        $html .= '*{font-family: "Roboto", sans-serif !important;line-height:1.1;font-size: 14px;}';
		        $html .= 'strong{font-weight:700 !important;}';
		        $html .= 'table{width: 100%; margin-bottom: 1.3rem !important;}';
		        $html .= 'ul {padding: 0; margin: 0;padding-left: 15px;}';
		        $html .= '.title{font-size:18px !important;text-transform: uppercase;}';
		        $html .= '.content{font-size:13px !important;}';
		        $html .= '.heading{font-size:18px !important;color: #fff;}';
		        $html .= '.subhead{font-size: 16px !important;color: #fff;}';
		        $html .= '.gray{background-color:#585858;color: #fff;}';
		        $html .= '.black{background-color:#000;color:#fff;}';
		        $html .= '.blue{background-color: #001f5f;color:#fff;}';
		        $html .= '.technology{background-color: #6f6f00;color:#fff;}';
		        $html .= '.people{background-color: #400080;color:#fff;}';
		        $html .= '.center{text-align: center;}';
		        $html .= '.semibold{font-weight:600;}';
		        $html .= '.table td {padding: 5px 10px !important;}';
		        $html .= 'td.nopadding {padding: 0 !important;}';
		        $html .= '.color-five {background-color: #64bc46;}';
		        $html .= '.color-four {background-color: #abcdef;}';
		        $html .= '.color-three {background-color: #ffff00;}';
		        $html .= '.color-two {background-color: #ffbf00;}';
		        $html .= '.color-one {background-color: #ff0000;}';
		        $html .= '.table-bordered td.fixedbg{width: 25px !important;}';
		        $html .= '.table-bordered td {border: 1px solid #6e7275 !important;}';
		        $html .= '.table-bordered td.noborder {border: 0px !important;}';
		        $html .= '.logo {height: 90px; width: auto}';
		        $html .= '.small {font-size: 13px !important;}';
		        $html .= '@page {margin: 35px 25px 40px; }';
		        $html .= '.light-gray {background-color: #d9d9d9;}';
		        $html .= '.m0 {margin:0!important;}';
		        // $html .= ' table>thead{display:block; }';
		        $html .= ' .avoidBreak {page-break-inside: avoid !important; margin: 4px 0 4px 0;  /* to keep the page break from cutting too close to the text in the div */ }';
		    $html .= '</style>';
			$department = empty($_GET['dept']) ? '' : $_GET['dept'];
			$peopleTitlte = cs_get_option('people_title');
			$peopleDesc = cs_get_option('people_desc');
			$tecTitle = cs_get_option('tec_title');
			$tecDesc = cs_get_option('tec_desc');
			foreach ($data as $key => $area) {
				$deptName = !empty($area['name']) ? $area['name'] : '';
				$areaID = advisory_id_from_string($deptName);
				if ($areaID != $department) continue;
				else {
					$avoidBreak     = false;
					$departmentID 	= advisory_id_from_string($deptName).'_services';
					// $serviceID 		= advisory_id_from_string($service['name']);
					$defaultTitle 	= advisory_form_default_values($postID, $departmentID.'_dq_hld');
					$defaultIntro 	= advisory_form_default_values($postID, $departmentID.'_dq_intro');
					$defaultQ2 		= advisoryGetFormatedDefaulValuesForPDFQ2($postID, $departmentID, $departmentID . '_list_staff');
					$defaultQ3 		= advisoryGetFormatedDefaulValuesForPDF($postID, $departmentID . '_dq_efbl', 'Q3');
					$defaultQ6 		= advisoryGetFormatedDefaulValuesForPDFQ6($postID, $departmentID . '_delivery', $area['opts']);
					$defaultQ7 		= advisoryGetFormatedDefaulValuesForPDFQ7($postID, $departmentID . '_dq_drc', $area['opts'], $area['services']);
					$defaultQ9 		= advisoryGetFormatedDefaulValuesForPDFQ9($postID, $departmentID . '_oid', $area['services']);
					$teamActionPlan = advisory_form_default_values($postID, $departmentID . '_tap');
					$evalQ3 		= advisoryGetFormatedDefaulValuesForPDFEvalQ3($postID, $departmentID . '_how_recreate', $area['services']);
					$upstream 		= advisoryGetFormatedDefaulValuesForPDFUpstream($postID);
					$title 			= 'Departmental Business Continuity Report<br>'. get_the_date('F jS, Y',$postID);
					$staff 			= !empty($defaultIntro['staff']) ? $defaultIntro['staff'] : 0;
					$intro 			= !empty($defaultTitle['desc']) ? $defaultTitle['desc'] : '';

					if ($pdfData) $sascsop = $pdfData[$prefix.$departmentID .'_sascsop'];
					else $sascsop = cs_get_option($prefix.$departmentID .'_sascsop');
					
					$html .= '<div class="tableContainer">';
			            $html .= '<table class="table table-bordered">';
			                $html .= '<tr>';
			                    $html .= '<td class="subhead black align-middle" colspan="3" rowspan="2" style="width: 40%;">'. $title .'</span></td>';
			                    $html .= '<td class="heading black center semibold align-middle" rowspan="2">'. $deptName .'</td>';
			                    $html .= '<td class="center light-gray">Staff</td>';
			                    $html .= '<td class="black center">RTO</td>';
			                $html .= '</tr>';
			                $html .= '<tr>';
			                    $html .= '<td class="center light-gray">'. $staff .'</td>';
			                    $html .= '<td class="nopadding" width: 10%;>';
			                        $html .= '<table class="table table-bordered" style="margin-bottom: 0 !important;">';
			                            $html .= '<tr>';
			                                $html .= '<td class="nopadding color-five fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
			                                $html .= '<td class="noborder">2-4 weeks</td>';
			                            $html .= '</tr>';
			                        $html .= '</table>';
			                    $html .= '</td>';
			                $html .= '</tr>';
			                $html .= '';
			                $html .= '<tr>';
			                    $html .= '<td class="small" colspan="5" rowspan="3">'. $intro .'</td>';
			                    $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding color-four fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">7-days</td></tr></table></td>';
			                $html .= '</tr>';
			                $html .= '<tr>';
			                    $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding color-three fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">3-days &nbsp;&nbsp;&nbsp;</td></tr></table></td>';
			                $html .= '</tr>';
			                $html .= '<tr>';
			                    $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding color-two fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">24-hours</td></tr></table></td>';
			                $html .= '</tr>';
			                $html .= '<tr>';
			                    $html .= '<td class="black" colspan="5">CRITICAL FUNCTIONS AND RECOVERY TIME OBJECTIVES </td>';
			                    $html .= '<td class="nopadding">';
			                        $html .= '<table class="table table-bordered" style="margin-bottom: 0 !important;">';
			                            $html .= '<tr>';
			                                $html .= '<td class="nopadding color-one fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
			                                $html .= '<td class="noborder">0-4&nbsp;hours</td>';
			                            $html .= '</tr>';
			                        $html .= '</table>';
			                    $html .= '</td>';
			                $html .= '</tr>';
			            $html .= '</table>';
			            if (!empty($area['services'])) {
			            	$Q2Length = 0;
			            	$stHead = '';
			                $stHead .= '<thead>';
				                $stHead .= '<tr>';
				                    $stHead .= '<td class="gray" style="width:25%;">Service/Process</td>';
				                    $stHead .= '<td class="gray" style="width:5%;">RTO</td>';
				                    $stHead .= '<td class="gray">Impact</td>';
				                    $stHead .= '<td class="gray">Manual Processes/Alternatives</td>';
				                $stHead .= '</tr>';
			                $stHead .= '</thead>';
				            $html .= '<table class="table table-bordered">';
					            $html .= $stHead;
					            $html .= '<tbody>';
					                foreach ($area['services'] as $servicesSI => $service) {
					                	$serviceID = advisory_id_from_string($service['name']);
										$Q2 = !empty($defaultQ2[$serviceID]) ? $defaultQ2[$serviceID] : '';
										$Q3 = !empty($defaultQ3[$serviceID]) ? $defaultQ3[$serviceID] : '';
						                $html .= '<tr>';
						                    $html .= '<td class="blue small text-uppercase">'. $service['name'] .'</td>';
						                    $html .= '<td class="'. coloring_elements($service['cr'], 'bia-score') .'">&nbsp;</td>';
						                    $html .= '<td class="blue small">'. $Q2 .'</td>';
						                    $html .= '<td class="blue small">'. $Q3 .'</td>';
						                $html .= '</tr>';
					                }
					            $html .= '</tbody>';
				            $html .= '</table>';
			            }
				        $html .= '<div class="avoidBreak">';
				            if ($tecTitle || $tecDesc) $html .= '<div style="padding: 20px;margin-bottom: 10px;width: 60%; border:1px solid #000;" class="technology"><span class="title">'. $tecTitle .'</span><br><span class="content">'. $tecDesc .'</span></div>';
				            if (!empty($defaultQ6['A'])) {
				            	$html .= '<table class="table table-bordered m0"><tr><td colspan="5" class="technology">VITAL RECORDS, DATABASES, FORMS AND DOCUMENTS</td></tr></table>';
				            	$html .= '<table class="table table-bordered">';
					                $html .= '<thead>';
						                $html .= '<tr>';
						                    $html .= '<td class="gray">Vital Record</td>';
						                    $html .= '<td class="gray">Description</td>';
						                    $html .= '<td class="gray">Storage Location</td>';
						                    $html .= '<td class="gray">Format</td>';
						                    $html .= '<td class="gray">Updated</td>';
						                $html .= '</tr>';
					                $html .= '</thead>';
					                $html .= '<tbody>';
						                foreach ($defaultQ6['A'] as $value) {
							                $html .= '<tr>';
							                    $html .= '<td class="small">'. $value['vr'] .'</td>';
												$html .= '<td class="small">'. $value['desc'] .'</td>';
												$html .= '<td class="small">'. $value['sl'] .'</td>';
												$html .= '<td class="small">'. $value['format'] .'</td>';
												$html .= '<td class="small">'. $value['updated'] .'</td>';
							                $html .= '</tr>';
						                }
						            $html .= '</tbody>';
					            $html .= '</table>';
				            }
				        $html .= '</div>';

			            if (!empty($evalQ3)) {
				            $html .= '<table class="table table-bordered m0"><tr><td colspan="4" class="technology">RECOVERY POINT OBJECTIVES (RPO)</td></tr></table>';
			            	$html .= '<table class="table table-bordered">';
				                $html .= '<thead>';
					                $html .= '<tr>';
					                    $html .= '<td class="gray" style="width:30%;">Service</td>';
					                    $html .= '<td class="gray" style="width:60px;text-align:center;">RPO</td>';
					                    $html .= '<td class="gray">Process to Recreate Data (if any)</td>';
					                $html .= '</tr>';
				                $html .= '</thead>';
				                $html .= '<tbody>';
					                foreach ($evalQ3 as $value) {
						                $html .= '<tr>';
						                    $html .= '<td class="small">'. $value['SName'] .'</td>';
											$html .= '<td class="small '.$value['rpoColor'].'" style="text-align:center;">'. $value['rpo'] .'</td>';
											$html .= '<td class="small">'. $value['process'] .'</td>';
						                $html .= '</tr>';
					                }
					            $html .= '</tbody>';
				            $html .= '</table>';
			            }
			            if (!empty($defaultQ6['B'])) {
				            $html .= '<table class="table table-bordered m0"><tr><td colspan="4" class="technology">TECHNOLOGY REQUIREMENTS</td></tr></table>';
			            	$html .= '<table class="table table-bordered">';
				                $html .= '<thead>';
					                $html .= '<tr>';
					                    $html .= '<td class="gray">Type</td>';
					                    $html .= '<td class="gray">Normal</td>';
					                    $html .= '<td class="gray">Minimal (MSL)</td>';
					                    $html .= '<td class="gray">Comments</td>';
					                $html .= '</tr>';
				                $html .= '</thead>';
				                $html .= '<tbody>';
					                foreach ($defaultQ6['B'] as $value) {
						                $html .= '<tr>';
						                    $html .= '<td class="small">'. $value['type'] .'</td>';
											$html .= '<td class="small">'. $value['normal'] .'</td>';
											$html .= '<td class="small">'. $value['msl'] .'</td>';
											$html .= '<td class="small">'. $value['comments'] .'</td>';
						                $html .= '</tr>';
					                }
					            $html .= '</tbody>';
				            $html .= '</table>';
			            }
			            if (!empty($defaultQ6['C']) && 0) {
				            $html .= '<table class="table table-bordered m0"><tr><td colspan="3" class="technology">SOFTWARE APPLICATIONS SUPPORTING CRITICAL SERVICES/PROCESSES</td></tr></table>';
			            	$html .= '<table class="table table-bordered">';
				                $html .= '<thead>';
					                $html .= '<tr>';
					                    $html .= '<td class="gray">Application</td>';
					                    $html .= '<td class="gray">Function</td>';
					                    $html .= '<td class="gray">Support Contact</td>';
					                $html .= '</tr>';
					            $html .= '</thead>';
					            $html .= '<tbody>';
					                foreach ($defaultQ6['C'] as $value) {
						                $html .= '<tr>';
						                    $html .= '<td class="small">'. $value['ca'] .'</td>';
											$html .= '<td class="small">'. $value['func'] .'</td>';
											$html .= '<td class="small">'. $value['sc'] .'</td>';
						                $html .= '</tr>';
					                }
				                $html .= '</tbody>';
				            $html .= '</table>';
			            }
			            if ($upstream) {
				            $html .= '<table class="table table-bordered m0"><tr><td colspan="5" class="technology">UPSTREAM DEPENDENCIES SUPPORTING CRITICAL SERVICES/PROCESSES</td></tr></table>';
			            	$html .= '<table class="table table-bordered">';
				                $html .= '<thead>';
					                $html .= '<tr>';
					                    $html .= '<td class="gray">Services/Processes</td>';
					                    $html .= '<td class="gray">IT Services</td>';
					                    $html .= '<td class="gray">Desktop</td>';
					                    $html .= '<td class="gray">Cloud</td>';
					                    $html .= '<td class="gray">Other</td>';
					                $html .= '</tr>';
					            $html .= '</thead>';
					            $html .= '<tbody>';
					                foreach ($upstream as $up) {
						                $html .= '<tr>';
						                    $html .= '<td class="small">'. $up['title'] .'</td>';
											$html .= '<td class="small">'. $up['upstream'] .'</td>';
											$html .= '<td class="small">'. $up['desktop'] .'</td>';
											$html .= '<td class="small">'. $up['cloud'] .'</td>';
											$html .= '<td class="small">'. $up['other'] .'</td>';
						                $html .= '</tr>';
					                }
				                $html .= '</tbody>';
				            $html .= '</table>';
			            }
			            if ($defaultQ9) {
				            $html .= '<table class="table table-bordered m0">';
				                $html .= '<tr>';
				                    $html .= '<td class="technology">OTHER INTERNAL DEPENDENCIES (UPSTREAM/DOWNSTREAM)</td>';
				                $html .= '</tr>';
				                $html .= '</table>';
			            		$html .= '<table class="table table-bordered">';
				                $html .= '<thead>';
					                $html .= '<tr>';
					                    $html .= '<td class="gray">Services/Processes</td>';
					                    $html .= '<td class="gray">Upstream Dependency</td>';
					                    $html .= '<td class="gray">Downstream Dependency</td>';
					                    $html .= '<td class="gray">Comments</td>';
					                $html .= '</tr>';
					            $html .= '</thead>';
					            $html .= '<tbody>';
					                foreach ($defaultQ9 as $oid) {
						                $html .= '<tr>';
						                    $html .= '<td class="small">'. $oid['service'] .'</td>';
											$html .= '<td class="small">'. $oid['ud'] .'</td>';
											$html .= '<td class="small">'. $oid['dd'] .'</td>';
											$html .= '<td class="small">'. $oid['comments'] .'</td>';
						                $html .= '</tr>';
					                }
				                $html .= '</tbody>';
				            $html .= '</table>';
			            }
			            $html .= '<div class="avoidBreak">';
				            if ($peopleTitlte || $peopleDesc) $html .= '<div style="padding: 20px;margin-bottom: 10px;width: 60%;border:1px solid #000;" class="people"><span class="title">'. $peopleTitlte .'</span><br><span class="content">'. $peopleDesc .'</span></div>';
				            if (!empty($defaultQ7['epct'])) {
					            $html .= '<table class="table table-bordered m0"><tr><td colspan="4" class="people">ESSENTIAL PERSONNEL AND CROSS-TRAINING</td></tr></table>';
				            	$html .= '<table class="table table-bordered">';
					                $html .= '<thead>';
						                $html .= '<tr>';
						                    $html .= '<td class="gray text-uppercase">Service/Process</td>';
						                    $html .= '<td class="gray text-uppercase">Performs this Service/Process</td>';
						                    $html .= '<td class="gray text-uppercase">Can be Cross-Trained</td>';
						                    $html .= '<td style="width: 35%;" class="gray text-uppercase">Comments</td>';
						                $html .= '</tr>';
					                $html .= '</thead>';
					                $html .= '<tbody>';
						                foreach ($defaultQ7['epct'] as $value) {
							                $html .= '<tr>';
							                    $html .= '<td class="small">'. ucwords(str_replace('_', ' ', $value['sop'])) .'</td>';
												$html .= '<td class="small">'. $value['psop'] .'</td>';
												$html .= '<td class="small">'. $value['cct'] .'</td>';
												$html .= '<td class="small">'. $value['comments'] .'</td>';
							                $html .= '</tr>';
						                }
					                $html .= '</tbody>';
					            $html .= '</table>';
				            }
			        	$html .= '</div>';
			        // $html .= '<div>';
		            if (!empty($defaultQ7['mnac'])) {
			            $html .= '<table class="table table-bordered m0"><tr><td colspan="5" class="people">MODES OF NOTIFICATION AND COMMUNICATION</td></tr></table>';
			            $html .= '<table class="table table-bordered">';
			                $html .= '<thead>';
				                $html .= '<tr>';
				                    $html .= '<td class="gray">System</td>';
				                    // $html .= '<td class="gray">Priority Use</td>';
				                    $html .= '<td class="gray" colspan="2">How to Use</td>';
				                    $html .= '<td class="gray">Support Items</td>';
				                    $html .= '<td class="gray">Access List</td>';
				                $html .= '</tr>';
			                $html .= '</thead>';
			                $html .= '<tbody>';
				                foreach ($defaultQ7['mnac'] as $value) {
					                $html .= '<tr>';
										$html .= '<td class="small">'. $value['system'] .'</td>';
					                   // $html .= '<td class="small">'. $value['pu'] .'</td>';
										$html .= '<td class="small" colspan="2">'. $value['hu'] .'</td>';
										$html .= '<td class="small">'. $value['si'] .'</td>';
										$html .= '<td class="small">'. $value['al'] .'</td>';
					                $html .= '</tr>';
				                }
			                $html .= '</tbody>';
			            $html .= '</table>';
		            }
		            if (!empty($defaultQ7['icl'])) {
			            $html .= '<table class="table table-bordered m0"><tr><td colspan="5" class="people">INTERNAL CONTACT LIST</td></tr></table>';
			            $html .= '<table class="table table-bordered">';
			                $html .= '<thead>';
				                $html .= '<tr>';
				                    $html .= '<td class="gray" style="width:20%;">Position</td>';
				                    $html .= '<td class="gray" style="width:29%;">Name</td>';
				                    $html .= '<td class="gray" style="width:11%;">Office Phone</td>';
				                    $html .= '<td class="gray" style="width:11%;">Cell Phone</td>';
				                    $html .= '<td class="gray" style="width:29%;">Email</td>';
				                $html .= '</tr>';
				            $html .= '</thead>';
				            $html .= '<tbody>';
				                foreach ($defaultQ7['icl'] as $value) {
					                $html .= '<tr>';
					                    $html .= '<td class="small">'. $value['position'] .'</td>';
					                    $html .= '<td class="small">'. $value['name'] .'</td>';
										$html .= '<td class="small">'. $value['op'] .'</td>';
										$html .= '<td class="small">'. $value['cp'] .'</td>';
										$html .= '<td class="small">'. $value['email'] .'</td>';
					                $html .= '</tr>';
				                }
				            $html .= '</tbody>';
			            $html .= '</table>';
		            }
		            if (!empty($defaultQ7['ecl'])) {
			            $html .= '<table class="table table-bordered m0"><tr><td colspan="5" class="people">EXTERNAL CONTACT LIST</td></tr></table>';
			            $html .= '<table class="table table-bordered">';
			                $html .= '<thead>';
				                $html .= '<tr>';
				                    $html .= '<td class="gray" style="width:20%;">VENDOR/SUPPLIER</td>';
				                    $html .= '<td class="gray" style="width:11%;">CONTACT</td>';
				                    $html .= '<td class="gray" style="width:11%;">Phone</td>';
				                    $html .= '<td class="gray" style="width:29%;">Email</td>';
				                    $html .= '<td class="gray" style="width:29%;">Comments</td>';
				                $html .= '</tr>';
				            $html .= '</thead>';
				            $html .= '<tbody>';
				                foreach ($defaultQ7['ecl'] as $value) {
					                $html .= '<tr>';
					                    $html .= '<td class="small">'. $value['vendor'] .'</td>';
					                    $html .= '<td class="small">'. $value['contact'] .'</td>';
										$html .= '<td class="small">'. $value['phone'] .'</td>';
										$html .= '<td class="small">'. $value['email'] .'</td>';
										$html .= '<td class="small">'. $value['comment'] .'</td>';
					                $html .= '</tr>';
				                }
				            $html .= '</tbody>';
			            $html .= '</table>';
		            }
		            if (!empty($teamActionPlan)) {
			            $html .= '<table class="table table-bordered m0"><tr><td colspan="5" class="people">CONTINUITY PLANS</td></tr></table>';
			            $html .= '<table class="table table-bordered">';
			                $html .= '<thead>';
				                $html .= '<tr>';
				                    $html .= '<td class="gray">DAY 1</td>';
				                    $html .= '<td class="gray">DAY 2</td>';
				                    $html .= '<td class="gray">DAY 3</td>';
				                    $html .= '<td class="gray">DAY 7</td>';
				                $html .= '</tr>';
				            $html .= '</thead>';
				            $html .= '<tbody>';
				                $html .= '<tr>';
				                    $html .= '<td class="small">'. $teamActionPlan['ap_day1'] .'</td>';
									$html .= '<td class="small">'. $teamActionPlan['ap_day2'] .'</td>';
									$html .= '<td class="small">'. $teamActionPlan['ap_day3'] .'</td>';
									$html .= '<td class="small">'. $teamActionPlan['ap_day4'] .'</td>';
				                $html .= '</tr>';
				            $html .= '</tbody>';
			            $html .= '</table>';
		            }
	                $html .= '<div style="position: fixed; bottom: -8px;left:0;right:0;text-align: center;font-size: 10px !important;" class="footer">This document is proprietary and confidential. No part of this document may be disclosed in any manner to a third party without the prior written consent of '. $company->name .'</div>';
				}
			}
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'landscape');
			$dompdf->set_option("isPhpEnabled", true);
			$dompdf->render();
			$dompdf->get_canvas()->page_text(510, 10, "DEPARTMENTAL BUSINESS CONTINUITY PLAN (CONFIDENTIAL)--Not for External Distribution", '', 7, array(0,0,0));
			$dompdf->get_canvas()->page_text(776, 572, "Page : {PAGE_NUM} of {PAGE_COUNT}", '', 8, array(0,0,0));
		}
	} else if ($postType == 'sfia') {
		$filename = "SFIA Summary";
		$prefix 	= $postType.'_pdf_';
		$user_id 	= !empty($_GET['user_id']) ? $_GET['user_id'] : '';
		$data 		= advisory_get_sfia_pdf_data($postID, $user_id);
		$imageUrl 	= get_template_directory() .'/images/sfia/pdf/';
		// PDF DATA
		$html = '';
		// $html = '<br><pre>'. print_r($data, true) .'</pre>';
		if (!empty($data)) {
			$html .= '<style>';
		        $html .= '@import url("https://fonts.googleapis.com/css?family=Roboto:400,500,700");';
		        // $html .= ' *{margin:0 20px;padding:0}';
		        $html .= ' *{font-family: "Roboto", sans-serif !important;line-height:1.1;font-size: 11px;}';
				// BACKGROUND COLORS
		        $html .= ' .bg-darkred {background-color: #8b0000;}';
				$html .= ' .bg-gap {background-color: #d5d5d5;}';
				$html .= ' .bg-red {background-color: #ed2227;}';
				$html .= ' .bg-orange {background-color: #f77e28;}';
				$html .= ' .bg-yellow {background-color: #ffdc5d;}';
				$html .= ' .bg-green {background-color: #64bc46;}';
				$html .= ' .bg-deepgreen {background-color: #0f9742;}';
				$html .= ' .bg-blue {background-color: #abcdef;}';
				$html .= ' .bg-deepblue {background-color: #169ad7;}';
				$html .= ' .bg-light-red{background-color:#fbd4ce;}';
				$html .= ' .bg-light-pink{background-color:#f2dae9;}';
				$html .= ' .bg-light-orange{background-color:#fbf2d2;}';
				$html .= ' .bg-light-yellow{background-color:#ecd7c2;}';
				$html .= ' .bg-light-blue{background-color:#cbe8f8;}';
				$html .= ' .bg-light-green{background-color:#cde7d4;}';
				$html .= ' .bg-aliceblue{background-color:aliceblue;}';
				// BACKGROUNDS IMAGES
				// $html .= ' .bgimg-darkred {background:url('. $imageUrl .'colors/bg-darkred.png);}';
				// $html .= ' .bgimg-gap {background:url('. $imageUrl .'colors/bg-gap.png);}';
				// $html .= ' .bgimg-red {background:url('. $imageUrl .'colors/bg-red.png);}';
				// $html .= ' .bgimg-orange {background:url('. $imageUrl .'colors/bg-orange.png);}';
				// $html .= ' .bgimg-yellow {background:url('. $imageUrl .'colors/bg-yellow.png);}';
				// $html .= ' .bgimg-green {background:url('. $imageUrl .'colors/bg-green.png);}';
				// $html .= ' .bgimg-deepgreen {background:url('. $imageUrl .'colors/bg-deepgreen.png);}';
				// $html .= ' .bgimg-blue {background:url('. $imageUrl .'colors/bg-blue.png);}';
				// $html .= ' .bgimg-deepblue {background:url('. $imageUrl .'colors/bg-deepblue.png);}';
				// $html .= ' .bgimg-light-red {background:url('. $imageUrl .'colors/bg-light-red.png);}';
				// $html .= ' .bgimg-light-pink {background:url('. $imageUrl .'colors/bg-light-pink.png);}';
				// $html .= ' .bgimg-light-orange {background:url('. $imageUrl .'colors/bg-light-orange.png);}';
				// $html .= ' .bgimg-light-yellow {background:url('. $imageUrl .'colors/bg-light-yellow.png);}';
				// $html .= ' .bgimg-light-blue {background:url('. $imageUrl .'colors/bg-light-blue.png);}';
				// $html .= ' .bgimg-light-green {background:url('. $imageUrl .'colors/bg-light-green.png);}';
				// $html .= ' .bgimg-aliceblue {background:url('. $imageUrl .'colors/bg-aliceblue.png);}';
				// COLORS
				$html .= ' .color-darkred {color: #8b0000;}';
				$html .= ' .color-gap {color: #d5d5d5;}';
				$html .= ' .color-red {color: #ed2227;}';
				$html .= ' .color-orange {color: #f77e28;}';
				$html .= ' .color-yellow {color: #ffdc5d;}';
				$html .= ' .color-green {color: #64bc46;}';
				$html .= ' .color-deepgreen {color: #0f9742;}';
				$html .= ' .color-blue {color: #abcdef;}';
				$html .= ' .color-deepblue {color: #169ad7;}';
				$html .= ' .color-light-red {color:#fbd4ce;}';
				$html .= ' .color-light-pink {color:#f2dae9;}';
				$html .= ' .color-light-orange {color:#fbf2d2;}';
				$html .= ' .color-light-yellow {color:#ecd7c2;}';
				$html .= ' .color-light-blue {color:#cbe8f8;}';
				$html .= ' .color-light-green {color:#cde7d4;}';
				$html .= ' .color-aliceblue {color:aliceblue;}';
				// COMMON CLASSES
				$html .= ' table {border-collapse: collapse; border-spacing: 0;}';
				$html .= ' table.table tr td.bold, table.table tr td p.bold, .bold {font-weight:bold}';
		        $html .= ' table.table {width:100%}';
				$html .= ' .rounded-bg {}';
				$html .= ' .text-center {text-align:center;}';
		        $html .= ' table.no-border {border:none;}';
		        $html .= ' table.no-border tr {border:none;}';
		        $html .= ' table.no-border tr th, table.no-border tr td {border:none;}';

		        // HEADER STYLES
		        $html .= ' table.headerTop {margin:0px 0 0 0}';
		        $html .= ' table.headerTop tr {}';
		        $html .= ' table.headerTop tr td.logo{width: 165px; text-align: center;padding:0}';
		        $html .= ' table.headerTop tr td.logo img{ width: 80%;}';
		        $html .= ' table.headerTop tr td.logo p{font-size: 15px; font-weight: bold;margin:0;}';
		        $html .= ' table.headerTop tr td.customerName{font-size:18px;padding-left:20px}';
		        $html .= ' table.headerBottom {}';
		        $html .= ' table.headerBottom tr {}';
		        $html .= ' table.headerBottom tr td{vertical-align:top}';
		        $html .= ' table.headerBottom tr td p, table.headerBottom tr td p strong{font-size: 13px; line-height: 1.3;}';
		        $html .= ' table.headerBottom tr td p{margin:0}';
		        $html .= ' table.headerBottom tr td p strong{font-weight: bold;}';
		        $html .= ' table.headerRight {margin: 10px 0 0 0;}';
		        $html .= ' table.headerRight tr {}';
		        $html .= ' table.headerRight tr td p{overflow:hidden;margin: 0 0 5px 0;text-align:center;}';
		        $html .= ' table.headerRight tr td p.rounded-bg{height: 50px;width: 50px;line-height: 37px;font-weight:bold;font-size: 14px;margin: 0 auto;}';
		        $html .= ' table.headerRight tr td p.rounded-bg{border-radius:25px 25px 25px 25px;margin: 0 auto;}';
		        // $html .= ' table.headerRight tr td p.rounded-bg{background-size:100%; background-position:center;background-repeat:no-repeat;}';
				// SKILLS STYLES
		        $html .= ' table.skills {border-top: 3px solid #000;border-bottom: 2px solid #000;margin-top:5px;}';
		        $html .= ' table.skills tr {}';
		        $html .= ' table.skills tr td{border:0}';
		        $html .= ' table.skills tr td.skillsContainer{vertical-align:top; width:50%;}';
		        $html .= ' table.skills tr td.skillsContainer .skillItems{margin:0;padding:3px 0 5px 0;}';
		        $html .= ' table.skills tr td.skillsLeftContainer{border-right: 2px solid #000;float:right;padding-right:5px;}';
		        $html .= ' table.skills tr td.skillsRightContainer{padding-left:5px;}';
		        $html .= ' table.skills tr td p{margin: 0 0 10px 0;}';
		        $html .= ' table.skills table.skillItems tr th{font-size:12px;padding-bottom: 3px;}';
		        $html .= ' table.skills table.skillItems tr th.text-left{text-align:left;}';
		        $html .= ' table.skills table.skillItems tr td{border:0;margin:0;padding:3px;font-weight:bold;}';
		        $html .= ' table.skills table.skillItems tr .code{width:35px;}';
		        $html .= ' table.skills table.skillItems tr .target{width:45px;}';
		        $html .= ' table.skills table.skillItems tr .evaluation{width:55px;}';
		        // SUMMARY
		        $html .= ' table.summary {margin-top: 10px;border-bottom: 2px solid #000;}';
		        $html .= ' table.summary tr td{vertical-align:top; padding:0 0 10px 0; width:50%;}';
		        $html .= ' table.summary tr td *{font-size:12px;}';
		        $html .= ' table.summary tr td.left{border-right: 2px solid #000;padding-right:10px;}';
		        $html .= ' table.summary tr td.right{padding-left:10px;}';
		        $html .= ' table.summary tr td p.title{font-weight:bold;margin: 0;font-size: 13px;line-height:1;}';
		        $html .= ' table.summary tr td ul, table.summary tr td ol{margin-top:0;padding-left:20px;}';
		        $html .= ' table.summary tr td ul li, table.summary tr td ol li{margin: 0 0 5px 0;}';
		        // ADDITIONAL SKILLS
		        // $html .= ' table.additionalSkills {border:2px solid #000;background:#b5daff;}';
		        $html .= ' table.additionalSkills {border:2px solid #000;background:#f2f8ff;}';
		        $html .= ' table.additionalSkills tr td{font-weight:500}';
		        // ADDITIONAL SKILLS ITEMS
		        $html .= ' table.additionalSkillItems tr td{padding: 5px 2px; text-align: center;border:none;}';
		        $html .= ' .footer{width:100%;border:2px solid #000;position:fixed; bottom: 20px;left: 0;)}';
		    $html .= '</style>';
			$logo = '<img src="'. $imageUrl .'sfia.jpg">';
			// HEADER
            $html .= '<table class="table m-0 no-border header">';
                $html .= '<tr>';
	                $html .= '<td>';
			            $html .= '<table class="table m-0 no-border headerTop">';
			                $html .= '<tr>';
			                    $html .= '<td class="logo">'. $logo .'<p>Assessment Summary</p></td>';
			                    $html .= '<td class="customerName">'.$data['company'].'</td>';
			                $html .= '</tr>';
			            $html .= '</table>';
			            $html .= '<table class="table m-0 no-border headerBottom">';
			                $html .= '<tr>';
			                    $html .= '<td><p><strong>Employee:</strong> '.$data['name'].'</p><p><strong>Code:</strong> '.$data['id'].'</p></td>';
			                    $html .= '<td><p><strong>Role/Title:</strong> '.$data['role'].'</p></td>';
			                    $html .= '<td><p><strong>Level of Responsibility:</strong> Level '.$data['level'].'</p></td>';
			                $html .= '</tr>';
			            $html .= '</table>';
	                $html .= '</td>';
	                $html .= '<td>';
	                	$html .= '<table class="table no-border headerRight">';
			                $html .= '<tr>';
			                    $html .= '<td class="text-center skillFit"><p><strong>SKILLS FIT %</strong></p><p class="rounded-bg '.$data['sfia']['class'].'">'.$data['sfia']['text'].'</p></td>';
			                    $html .= '<td class="text-center technicalScore"><p><strong>TECHNICAL SCORE</strong></p><p class="rounded-bg '.$data['sfiats']['class'].'">'.$data['sfiats']['text'].'</p></td>';
			                $html .= '</tr>';
			            $html .= '</table>';
	                $html .= '</td>';
                $html .= '</tr>';
			$html .= '</table>';
			// SKILLS
			$skillCount = !empty($data['skills']) ? count($data['skills']) : 0;
			$loop = $skillCount ? ceil($skillCount/2) : 0;
            $html .= '<table class="table m-0 no-border skills">';
                $html .= '<tr>';
	                $html .= '<td class="skillsContainer skillsLeftContainer">';
				        $html .= '<table class="table m-0 no-border skillItems skillsLeft">';
			                $html .= '<tr>';
				                $html .= '<th class="text-left">Skill</th>';
				                $html .= '<th class="text-center code">Code</th>';
				                $html .= '<th class="text-center target">Target</th>';
				                $html .= '<th class="text-center evaluation">Evaluation</th>';
			                $html .= '</tr>';
			                if ($loop) {
			                	for ($i=0; $i < $loop; $i++) { 
					                $html .= '<tr>';
						                $html .= '<td class="text-left '.$data['skills'][$i]['catcls'].'"> '.$data['skills'][$i]['title'].' </td>';
						                $html .= '<td class="text-center '.$data['skills'][$i]['catcls'].'"> '.$data['skills'][$i]['code'].' </td>';
						                $html .= '<td class="text-center bg-deepgreen">'.$data['skills'][$i]['tar'].' </td>';
						                $html .= '<td class="text-center '.$data['skills'][$i]['evalcls'].'">'. $data['skills'][$i]['eval'] .' </td>';
					                $html .= '</tr>';
			                	}
			                }
						$html .= '</table>';
	                $html .= '</td>';

	                $html .= '<td class="skillsContainer skillsRightContainer">';
				        $html .= '<table class="table m-0 no-border skillItems skillsRight">';
			                $html .= '<tr>';
				                $html .= '<th class="text-left">Skill</th>';
				                $html .= '<th class="text-center code">Code</th>';
				                $html .= '<th class="text-center target">Target</th>';
				                $html .= '<th class="text-center evaluation">Evaluation</th>';
			                $html .= '</tr>';
			                if ($loop) {
			                	for ($j=$i; $j < $skillCount; $j++) { 
					                $html .= '<tr>';
						                $html .= '<td class="text-left '.$data['skills'][$j]['catcls'].'"> '.$data['skills'][$j]['title'].' </td>';
						                $html .= '<td class="text-center bold '.$data['skills'][$j]['catcls'].'"> '.$data['skills'][$j]['code'].' </td>';
						                $html .= '<td class="text-center bold bg-deepgreen">'.$data['skills'][$j]['tar'].' </td>';
						                $html .= '<td class="text-center '.$data['skills'][$j]['evalcls'].'">'. $data['skills'][$j]['eval'] .' </td>';
					                $html .= '</tr>';
			                	}
			                }
						$html .= '</table>';
	                $html .= '</td>';
                $html .= '</tr>';
			$html .= '</table>';
			// SUMMARY
			$html .= '<table class="table m-0 no-border summary">';
				$html .= '<tr>';
					$html .= '<td class="left">';
						$html .= '<p class="title">SFIA Skills Fit Summary:</p><br>';
						$html .= $data['sfia']['summary'];
					$html .= ' </td>';
					$html .= '<td class="right">';
						$html .= '<p class="title">Technical Score Summary:</p><br>';
						$html .= $data['sfiats']['summary'];
					$html .= ' </td>';
				$html .= '</tr>';
			$html .= '</table>';
			
			$bottomLine = '_________________________________________________________________________________';
			$bottomLine .= '_________________________________________________________________________________';
			$bottomLine .= '____________';

			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'landscape');
			$dompdf->set_option("isPhpEnabled", true);
			$dompdf->render();
			$dompdf->get_canvas()->page_text(34, 560, $bottomLine, '', 8, array(0,0,0));
			$dompdf->get_canvas()->page_text(165, 572, "This document is strictly confidential and may not be reproduced or circulated without ".$data['company']." prior written consent.", "helvetica", 8, array(0,0,0));
		}
	} else {
		$prefix 	= $postType.'_pdf_';
		$company 	= advisory_get_user_company();
		$data 		= advisory_get_scorecard_data($postID);
		$pdfData 	= get_term_meta($company->term_id, $prefix, true);
		// PDF DATA
		$html = '';
		$html .= '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';	
		if ($data) {
			$pageCount 	= 0;
			if ($pdfData) $title = $pdfData[$prefix.'title'] ? $pdfData[$prefix.'title'] : '';
			else $title = cs_get_option($prefix.'title') ?? '';
			$PDFTitle = $title.' - '. $company->name;
			$html .= '<style>';
			    $html .= '@import url("https://fonts.googleapis.com/css?family=Roboto:400,500,700");';
			    $html .= '*{font-family: "Roboto", sans-serif !important;line-height:1.1;font-size: 14px;}';
			    $html .= 'strong{font-weight:700 !important;}';
			    $html .= '.heading{font-size:18px !important;color: #fff;}';
			    $html .= '.subhead{font-size: 16px !important;color: #fff;}';
			    $html .= '.gray{background-color:#585858;color: #fff;}';
			    $html .= '.black{background-color:#000;color:#fff;}';
			    $html .= '.blue{background-color: #001f5f;color:#fff;}';
			    $html .= '.center{text-align: center;}';
			    $html .= '.semibold{font-weight:600;}';
			    $html .= '.table td {padding: 5px 10px !important;}';
			    $html .= 'td.nopadding {padding: 0 !important;}';
			    $html .= '.color-five {background-color: #a6e7ff;}';
			    $html .= '.color-four {background-color: #00af4f;}';
			    $html .= '.color-three {background-color: #ffff00;}';
			    $html .= '.color-two {background-color: #ffbf00;}';
			    $html .= '.color-one {background-color: #ff0000;}';
			    // $html .= '.noBreak{page-break-inside: avoid !important;}';
			    $html .= '.table-bordered td.fixedbg{width: 25px !important;}';
			    $html .= '.table-bordered td {border: 1px solid #6e7275 !important;}';
			    $html .= '.table-bordered td.noborder {border: 0px !important;}';
			    $html .= '.logo {height: 50px; width: auto; margin-top: -20px; margin-bottom: 10px;}';
			    $html .= '.small {font-size: 13px !important;}';
			    $html .= '@page {margin: 35px 25px 40px; }';
			    $html .= ' .avoidBreak {page-break-inside: avoid !important; margin: 4px 0 4px 0;  /* to keep the page break from cutting too close to the text in the div */ }';
			$html .= '</style>';
			$html .= '<div>';
			    $html .= '<img class="logo" src="'. get_template_directory() .'/images/pdf/100.png">';
			$html .= '</div>';
			foreach ($data as $key => $area) {
				$introID = $prefix.'sections_'. advisory_id_from_string($area['name']).'_introduction';
				$introduction = $pdfData ? $pdfData[$introID] : cs_get_option($introID);
				$pageBreak = $pageCount > 0 ? ' style="page-break-before: always; position: relative;"' : '';
				$html .= '<div class="pageBreak"'.$pageBreak.'>';
					$html .= '<table class="table table-bordered" style="width: 100%;">';
					    $html .= '<tr>';
					        $html .= '<td class="subhead gray align-middle" colspan="3" rowspan="2" style="width: 40%;">'. $PDFTitle .'<br><span>'. get_the_date('F jS, Y',$postID) .'</span></td>';
					        $html .= '<td class="heading black center semibold align-middle" rowspan="2">'. $area['name'] .'</td>';
					        $html .= '<td class="black center">Maturity</td>';
					    $html .= '</tr>';
					    $html .= '<tr>';
					        $html .= '<td class="nopadding" width: 10%;>';
					            $html .= '<table class="table table-bordered" style="margin-bottom: 0 !important;">';
					                $html .= '<tr>';
					                    $html .= '<td class="nopadding color-five fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
					                    $html .= '<td class="noborder">Optimizing</td>';
					                $html .= '</tr>';
					            $html .= '</table>';
					        $html .= '</td>';
					    $html .= '</tr>';
					    $html .= '<tr>';
				            $html .= '<td class="small" colspan="4" rowspan="3">'. $introduction .'</td>';
				            $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding color-four fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">Measured</td></tr></table></td>';
				        $html .= '</tr>';
				        $html .= '<tr>';
				            $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding color-three fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">Defined &nbsp;&nbsp;&nbsp;</td></tr></table></td>';
				        $html .= '</tr>';
				        $html .= '<tr>';
				            $html .= '<td class="nopadding"><table class="table table-bordered" style="margin-bottom: 0 !important;"><tr><td class="nopadding color-two fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="noborder">Managed</td></tr></table></td>';
				        $html .= '</tr>';
					    $html .= '<tr>';
					        $html .= '<td class="black center" style="width: 25%;">Criteria</td>';
					        $html .= '<td class="black center" style="width:5%;">Maturity</td>';
					        $html .= '<td class="black center" colspan="2" style="width: 60%;">Process Areas</td>';
					        $html .= '<td class="nopadding" width: 10%;>';
					            $html .= '<table class="table table-bordered" style="margin-bottom: 0 !important;">';
					                $html .= '<tr>';
					                    $html .= '<td class="nopadding color-one fixedbg noborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
					                    $html .= '<td class="noborder">Initial &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
					                $html .= '</tr>';
					            $html .= '</table>';
					        $html .= '</td>';
					    $html .= '</tr>';
					    if (!empty($area['sections'])) {
					    	$sectionID = 'sections_' . advisory_id_from_string($area['name']);
					    	foreach ($area['sections'] as $section) {
					    		$tableID = $sectionID. '_tables_' . advisory_id_from_string($section['name']);
						    	$sectionData = getPDFSectionsData($tableID, $section, $prefix, $pdfData);
						    	$color = IHCRAvgStatus($sectionData['avg']);
							    // $html .= '<tr><td colspan="5">'.$section['name'].' ==='. json_encode($sectionData) .'</td></tr>';
							    $html .= '<tr class="noBreak">';
							        $html .= '<td class="blue align-middle">'. $section['name'] .'</td>';
							        $html .= '<td class="'. $color['cls'] .'"><br><br><br></td>';
							        // $html .= '<td class="'. ihcColorAVG(number_format($sectionData['avg'],1)) .'"><br><br><br></td>';
							        $html .= '<td colspan="3" class="blue">'. $sectionData['name'] .'</td>';
							    $html .= '</tr>';
							    $html .= '<tr>';
							        $html .= '<td class="align-middle gray">Assessment</td>';
							        $html .= '<td class="small" colspan="4">'. $sectionData['assessmentTitle'] .'</td>';
							    $html .= '</tr>';
							    $html .= '<tr>';
							        $html .= '<td class="small" colspan="5">'. $sectionData['assessmentDesc'] .'</td>';
							    $html .= '</tr>';
							    $html .= '<tr>';
							        $html .= '<td class="align-middle gray">Summary</td>';
							        $html .= '<td class="small" colspan="4">'. $sectionData['summaryTitle'] .'</td>';
							    $html .= '</tr>';
							    $html .= '<tr>';
							        $html .= '<td class="small" colspan="5">'. $sectionData['summaryDesc'] .'</td>';
							    $html .= '</tr>';
					    	}
					    }
					$html .= '</table>';
					$html .= '<div style="position: absolute; bottom: -20px;left:0;right:0;text-align: center;font-size: 10px !important;">&copy; 2019. All right reserved. encase<sup style="font-size: 7px !important;">TM</sup> is a trademark of WG Advisory Services, Inc. All other trademarks and registered trademarks are the properties of their respective holders.</div>';
				$html .= '<div>';
				$pageCount++;
			}
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'landscape');
			$dompdf->set_option("isPhpEnabled", true);
			$dompdf->render();
			$dompdf->get_canvas()->page_text(595, 10, "EXECUTIVE SUMMARY (CONFIDENTIAL)--Not for External Distribution", '', 7, array(0,0,0));
			$dompdf->get_canvas()->page_text(770, 572, "Page : {PAGE_NUM} of {PAGE_COUNT}", '', 8, array(0,0,0));
		}
	}
	$dompdf->stream($filename,array("Attachment"=>0));
}
// echo $html;