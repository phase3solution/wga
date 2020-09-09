<?php 
/* Template Name: MTA Register PDF */
require_once get_template_directory() . '/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
if (empty($_REQUEST['archive'])) {
	$title = 'Report';
	$data = MTAR_PDF_Data();
} else {
	$title = 'Archive';
	$data = MTAR_Arhcive_PDF_Data($_REQUEST['archive']);
}
// $data['desc'] .= ' '. $data['desc'].' '. $data['desc'];
// $data['pa'] = 'PerfectMind implemented in Feb 2019 (successfully integrated with GP) Leveraged PerfectMind for Pet Adoption appointments PerfectMind implemented in Feb 2019 (successfully integrated with GP) Leveraged PerfectMind for Pet Adoption appointmentsPerfectMind implemented in Feb 2019 (successfully integrated with GP) Leveraged PerfectMind for Pet Adoption appointments PerfectMind implemented in Feb 2019 (successfully integrated with GP) Leveraged PerfectMind for Pet Adoption appointments Leveraged PerfectMind for Pet Adoption appointments ';
// help($data); exit();
if (!empty($data)) {
	$html = '';
	$html .= '<style>';
		$html .= '.color-zero {background: #ccc; }';
		$html .= '.color-one {background: #df5e5f; }';
		$html .= '.color-two {background: #f77e28; }';
		$html .= '.color-three {background: #ffdc5d; }';
		$html .= '.color-four {background: #64bc46; }';
		$html .= '.color-five {background: #abcdef; }';
		
		$html .= '.color-gap, .btn.color-gap{background: #d5d5d5;}';
		$html .= '.color-0, .btn.color-0{background: #ed2227;}';
		$html .= '.color-1, .btn.color-1{background: #ed2227;}';
		$html .= '.color-2, .btn.color-2{background: #ed2227;}';
		$html .= '.color-3, .btn.color-3{background: #ed2227;}';
		$html .= '.color-4, .btn.color-4{background: #ed2227;}';
		$html .= '.color-5, .btn.color-5{background: #ed2227;}';
		$html .= '.color-6, .btn.color-6{background: #ed2327;}';
		$html .= '.color-7, .btn.color-7{background: #ed2427;}';
		$html .= '.color-8, .btn.color-8{background: #ed2727;}';
		$html .= '.color-9, .btn.color-9{background: #ed2927;}';
		$html .= '.color-10, .btn.color-10{background: #ed2e27;}';
		$html .= '.color-11, .btn.color-11{background: #ed3127;}';
		$html .= '.color-12, .btn.color-12{background: #ed3427;}';
		$html .= '.color-13, .btn.color-13{background: #ee3827;}';
		$html .= '.color-14, .btn.color-14{background: #ee3c27;}';
		$html .= '.color-15, .btn.color-15{background: #ee4027;}';
		$html .= '.color-16, .btn.color-16{background: #ee4627;}';
		$html .= '.color-17, .btn.color-17{background: #ee4a27;}';
		$html .= '.color-18, .btn.color-18{background: #ef4e28;}';
		$html .= '.color-19, .btn.color-19{background: #ef5228;}';
		$html .= '.color-20, .btn.color-20{background: #f05827;}';
		$html .= '.color-21, .btn.color-21{background: #f05c28;}';
		$html .= '.color-22, .btn.color-22{background: #f16028;}';
		$html .= '.color-23, .btn.color-23{background: #f16428;}';
		$html .= '.color-24, .btn.color-24{background: #f26928;}';
		$html .= '.color-25, .btn.color-25{background: #f26c29;}';
		$html .= '.color-26, .btn.color-26{background: #f27128;}';
		$html .= '.color-27, .btn.color-27{background: #f37628;}';
		$html .= '.color-28, .btn.color-28{background: #f37a28;}';
		$html .= '.color-29, .btn.color-29{background: #f47e28;}';
		$html .= '.color-30, .btn.color-30{background: #f58529;}';
		$html .= '.color-31, .btn.color-31{background: #f58929;}';
		$html .= '.color-32, .btn.color-32{background: #f68d28;}';
		$html .= '.color-33, .btn.color-33{background: #f69128;}';
		$html .= '.color-34, .btn.color-34{background: #f79628;}';
		$html .= '.color-35, .btn.color-35{background: #f89a29;}';
		$html .= '.color-36, .btn.color-36{background: #f9a028;}';
		$html .= '.color-37, .btn.color-37{background: #faa529;}';
		$html .= '.color-38, .btn.color-38{background: #fba928;}';
		$html .= '.color-39, .btn.color-39{background: #fcae27;}';
		$html .= '.color-40, .btn.color-40{background: #fcb628;}';
		$html .= '.color-41, .btn.color-41{background: #fdba26;}';
		$html .= '.color-42, .btn.color-42{background: #fec025;}';
		$html .= '.color-43, .btn.color-43{background: #ffc524;}';
		$html .= '.color-44, .btn.color-44{background: #ffca23;}';
		$html .= '.color-45, .btn.color-45{background: #ffd023;}';
		$html .= '.color-46, .btn.color-46{background: #ffd71f;}';
		$html .= '.color-47, .btn.color-47{background: #ffdc1f;}';
		$html .= '.color-48, .btn.color-48{background: #ffe41e;}';
		$html .= '.color-49, .btn.color-49{background: #ffea1e;}';
		$html .= '.color-50, .btn.color-50{background: #f9ef23;}';
		$html .= '.color-51, .btn.color-51{background: #f2ea27;}';
		$html .= '.color-52, .btn.color-52{background: #e9e42b;}';
		$html .= '.color-53, .btn.color-53{background: #dedf30;}';
		$html .= '.color-54, .btn.color-54{background: #d4da35;}';
		$html .= '.color-55, .btn.color-55{background: #c7d438;}';
		$html .= '.color-56, .btn.color-56{background: #bccf3c;}';
		$html .= '.color-57, .btn.color-57{background: #b2ca3e;}';
		$html .= '.color-58, .btn.color-58{background: #a8c640;}';
		$html .= '.color-59, .btn.color-59{background: #9ec241;}';
		$html .= '.color-60, .btn.color-60{background: #8ebb43;}';
		$html .= '.color-61, .btn.color-61{background: #86b944;}';
		$html .= '.color-62, .btn.color-62{background: #7fb645;}';
		$html .= '.color-63, .btn.color-63{background: #76b346;}';
		$html .= '.color-64, .btn.color-64{background: #6db047;}';
		$html .= '.color-65, .btn.color-65{background: #65ae47;}';
		$html .= '.color-66, .btn.color-66{background: #5bac47;}';
		$html .= '.color-67, .btn.color-67{background: #52a948;}';
		$html .= '.color-68, .btn.color-68{background: #4aa748;}';
		$html .= '.color-69, .btn.color-69{background: #43a549;}';
		$html .= '.color-70, .btn.color-70{background: #30a349;}';
		$html .= '.color-71, .btn.color-71{background: #2ba249;}';
		$html .= '.color-72, .btn.color-72{background: #22a14a;}';
		$html .= '.color-73, .btn.color-73{background: #1ca049;}';
		$html .= '.color-74, .btn.color-74{background: #189f49;}';
		$html .= '.color-75, .btn.color-75{background: #189f49;}';
		$html .= '.color-76, .btn.color-76{background: #14a04d;}';
		$html .= '.color-77, .btn.color-77{background: #13a154;}';
		$html .= '.color-78, .btn.color-78{background: #12a25a;}';
		$html .= '.color-79, .btn.color-79{background: #0fa361;}';
		$html .= '.color-80, .btn.color-80{background: #0ea46d;}';
		$html .= '.color-81, .btn.color-81{background: #0ca573;}';
		$html .= '.color-82, .btn.color-82{background: #0aa67b;}';
		$html .= '.color-83, .btn.color-83{background: #0aa782;}';
		$html .= '.color-84, .btn.color-84{background: #06a888;}';
		$html .= '.color-85, .btn.color-85{background: #02a990;}';
		$html .= '.color-86, .btn.color-86{background: #02aa96;}';
		$html .= '.color-87, .btn.color-87{background: #01ab9c;}';
		$html .= '.color-88, .btn.color-88{background: #00aca4;}';
		$html .= '.color-89, .btn.color-89{background: #00adaa;}';
		$html .= '.color-90, .btn.color-90{background: #00afb5;}';
		$html .= '.color-91, .btn.color-91{background: #00afba;}';
		$html .= '.color-92, .btn.color-92{background: #00b0c0;}';
		$html .= '.color-93, .btn.color-93{background: #00b1c6;}';
		$html .= '.color-94, .btn.color-94{background: #00b2cc;}';
		$html .= '.color-95, .btn.color-95{background: #00b2cc;}';
		$html .= '.color-96, .btn.color-96{background: #00b4d9;}';
		$html .= '.color-97, .btn.color-97{background: #00b5df;}';
		$html .= '.color-98, .btn.color-98{background: #00b6e6;}';
		$html .= '.color-99, .btn.color-99{background: #00b6e9;}';
		$html .= '.color-100, .btn.color-100{background: #00b6e9;}';
		
		$html .= 'td.metrics-orange {background-color: #f77e28;}';
		$html .= 'td.metrics-blue {background-color: #abcdef;}';
		$html .= 'td.metrics-red {background-color: #ed2227; }';
		$html .= 'td.metrics-yellow {background-color: #ffd53f; }';
		$html .= 'td.metrics-green {background-color: #c6e0b4; }';

	    $html .= '.page-border {position: fixed; left: 0; top: 0; bottom: 20px; right: 0; border: 1px solid #eee; }';
		$html .= 'body{font-family: arial,sans-serif-light,sans-serif;color: #000;font-size: 15px;}';
	 	$html .= 'p {margin: 0;line-height: 1.3; color: #232323;}';
	 	$html .= '.main {padding: 15px 15px 35px 15px;padding-top: 0;}';
	 	$html .= '.text-right {text-align: right;}';
		$html .= '.header .title {color: #000;font-size: 20px;line-height: 1.3;margin-bottom: 10px;}';
		$html .= '.header .title.hidden {visibility: hidden;}';
		$html .= '.header .seperator {border: 1px solid #adb9ca;}';
		$html .= '.header .cat {font-weight: 600;font-size: 17px;}';
		$html .= '.header span {font-size: 14px;color: #8e8495;}';
		$html .= '.header .rating span {padding: 12px;background-color: #ff0000;color: #000;font-weight: 700;margin-left: 15px;}';
		$html .= '.wrapper .heading {background: #203864;padding: 7px;color: #fff;font-size: 16px;font-weight: 700;margin-bottom: 20px;}';
		$html .= '.no-border {border-left: 1px solid #fff;border-right: 1px solid #fff;}';
		$html .= '.full .box {min-height: 100px;}';
		$html .= '.table {border-spacing: 15px;}';
		$html .= '.cell-zero {border-spacing: 0;}';
		$html .= '.table.no-margin {margin-top: -15px}';
		$html .= 'table .td-block {padding: 8px 12px;}';
		$html .= 'table .td-black {background: #000;color: #fff;padding: 8px 12px;}';
		$html .= 'table .td-blue {background: #8faadc;padding: 8px 12px;color: #fff;}';
		$html .= 'table td {border: 0}';
		$html .= '.footer {color: #000;padding: 10px 0 0;font-size: 12px;position: fixed; bottom: 60px;left: 30px}';
		$html .= '.td-bgcolor {border: 1px solid #8c8c8c;padding: 10px;background: #f9f9f9;color: #000;vertical-align: top;font-weight: 300;font-size: 14px !important;}';
		// $html .= '.td-bg {background: url("/wp-content/themes/advisory-services/images/mta/big-bg.png");background-size: contain;background-repeat: no-repeat;padding: 10px;margin: 20px;}';
	$html .= '</style>';

	$html .= '<div class="page-border"></div>';
	$html .= '<div class="main">';
	    $html .= '<div class="header">';
	        $html .= '<table width="100%" class="table">'; // Header title Start
	            $html .= '<tr>';
	                $html .= '<td style="width: 50%;">';
	                    $html .= '<div class="title">'.@$data['customer_name'].': MTA Progress '.$title.'</div>';
				        $html .= '<div class="seperator"></div>';
				    $html .= '</td>';
				    $html .= '<td style="width: 50%;text-align: right">';
				        $html .= '<p class="cat">Category: '.@$data['cat'].'</p>';
	        			$html .= '<span>'.@$data['base'].': '.@$data['area'].'</span>';
				    $html .= '</td>';
	            $html .= '</tr>';    
	        $html .= '</table>'; // Header title End
	        $html .= '<table width="100%" class="table no-margin">'; // Header Date, Category Start
	            $html .= '<tr>';
	                $html .= '<td style="width: 70%;">';
	                    $html .= '<div class="date"><p>'.$title.' Date: '.@$data['report_date'].'</p> <p>Last Assessment: '.@$data['last_assessment'].'</p></div>';
	                $html .= '</td>';
	                $html .= '<td style="width: 30%;">';
	        			$html .= '<div class="text-right"> <table width="100%"><tr class="rating"><td class="text-right">Rating:</td><td class="'.@$data['avg_cls'].'" style="text-align: center;padding: 12px 0; width: 50px">'.@$data['avg'].'</td></tr></table></div>';
	                $html .= '</td>';
	            $html .= '</tr>';
	        $html .= '</table>'; // Header Date, Category End
	    $html .= '</div>'; // Header End

	    $html .= '<div class="wrapper">';
	        $html .= '<table width="100%" class="table no-margin"><tr><td class="heading">'.@$data['subarea'].'</td></tr></table>'; // Form Title End
	        $html .= '<table width="100%" class="table no-margin"><tr><td class="td-bgcolor td-bg"><strong>Description:</strong> '.@$data['desc'].' </td></tr></table>'; // Description End
	        $html .= '<table width="100%" class="table no-margin">'; // Asses & Recom Start
	            $html .= '<tr>';
	                $html .= '<td style="width: 50%;" class="td-bgcolor td-bg"><strong>Assessment Results:</strong> '.@$data['assessment'].'</td>';            
	                $html .= '<td style="width: 50%;" class="td-bgcolor td-bg"><strong>Recommendations:</strong> '.@$data['recommendations'].'</td>';
	            $html .= '</tr>';
	        $html .= '</table>'; // Asses & Recom End
	        $html .= '<table width="100%" class="table no-margin">';
	            $html .= '<tr>';
	                $html .= '<td style="width: 50%;" class="td-bgcolor td-bg"><strong>Planned Activities:</strong> '.@$data['pa'].' </td>';
	                $html .= '<td style="width: 50%;" class="td-bgcolor td-bg"><strong>Current Activities:</strong> '.@$data['ca'].' </td>';
	            $html .= '</tr>';
	       $html .= '</table>';
	        $html .= '<table width="100%" class="table no-margin">';
	    		$html .= '<tr>';
	    			$html .= '<td class="td-bgcolor td-bg" style="width: 50%;"><strong>Accountable:</strong>'.@$data['owner'].'</td>';
	    			$html .= '<td style="width: 50%">';
	    			    $html .= '<table width="100%" class="cell-zero">';
                    		$html .= '<tr>';
                    			$html .= '<td width="15%" class="td-black">Status:</td>';
                    			$html .= '<td class="td-block '.@$data['status_cls'].'" colspan="2">'.@$data['status_txt'].'</td>';
                    		$html .= '</tr>';
                    	$html .= '</table>';
	    			$html .= '</td>';
	    		$html .= '</tr>';
	    	$html .= '</table>';
	    	// $html .= '<div class="footer">This document is strictly confidential communication to and solely for the use of the recipient. if you are not the intended recipient, you are hereby notified that any review, distribution, or duplication is strictly prohibited.</div>';        
	    $html .= '</div>'; // Wrapper End
	$html .= '</div>'; // main End
    // echo $html;
	$filename 	= "Executive Summary";
	$dompdf 	= new Dompdf();
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->set_option("isPhpEnabled", true);
	$dompdf->render();

	$dompdf->get_canvas()->page_text(35, 553, "This document is strictly confidential communication to and solely for the use of the recipient. if you are not the intended recipient, you are hereby notified that any review,", '', 9, array(0,0,0));
	$dompdf->get_canvas()->page_text(35, 565, "distribution, or duplication is strictly prohibited.", '', 9, array(0,0,0));
	$dompdf->get_canvas()->page_text(765, 565, "Page : {PAGE_NUM} of {PAGE_COUNT}", '', 8, array(0,0,0));
	$dompdf->stream($filename,array("Attachment"=>0));
}