<?php 
/**
 * PDF Content
 */
class PDFContent {
	static function render($postID='ihc') {
		if ($postID) {
			$requestedPostType = get_post_type($postID);
			if ($requestedPostType == 'ihc') return self::contentIHC($postID);
			else return self::contentMTA($postID);
		}
		return false;
	}
	static function contentMTA($postID) {
		if ($postID) {
			$pageCount = 0;
			$company = advisory_get_user_company();
			$data = advisory_get_scorecard_data($postID);

			$title = cs_get_option('mta_pdf_title') ?? 'Infrastructure Health Check Maturity Summary';
			$recomendations = cs_get_option('mta_pdf_recommendations') ?? '';
			$PDFTitle = $title.' - '. $company->name;
			// PDF DATA
			$html = '';
			$html = help($data);
			$html .= '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';
			$html .= '<style>';
			    $html .= '@import url("https://fonts.googleapis.com/css?family=Roboto:400,500,700");';
			    $html .= '*{font-family: "Roboto", sans-serif !important;line-height:1.1;font-size: 14px;}';
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
			    $html .= '.table-bordered td.fixedbg{width: 25px !important;}';
			    $html .= '.table-bordered td {border: 1px solid #6e7275 !important;}';
			    $html .= '.table-bordered td.noborder {border: 0px !important;}';
			    $html .= '.small {font-size: 13px !important;} @page { margin: 100px 25px 0; } header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; }';
			$html .= '</style>';
			foreach ($data as $key => $area) {
				if ($pageCount > 0) continue;
				$introID = 'mta_pdf_sections_'. advisory_id_from_string($area['name']).'_introduction';
				$pageBreak = $pageCount > 0 ? ' style="page-break-before: always;"' : '';
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
				            $html .= '<td class="small" colspan="4" rowspan="3">'. cs_get_option($introID) .'</td>';
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
						    	$sectionData = getPDFSectionsData($tableID, $section);
						    	$color = IHCRAvgStatus($sectionData['avg']);
							    // $html .= '<tr><td>'. help($sectionData, false); .'</td></tr>';
							    $html .= '<tr>';
							        $html .= '<td style="line-height: 30px;" class="blue align-middle">'. $area['name'] .'</td>';
							        $html .= '<td class="'. $color['cls'] .'"></td>';
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
					$html .= '<header>';
					        $html .= '<table style="width: 100%;" class="table table-bordered">';
					    $html .= '<tr>';
					        $html .= '<td class="subhead gray align-middle">'. $PDFTitle .'<br><span>'. get_the_date('F jS, Y',$postID) .'</span></td>';
					        $html .= '<td class="heading black center semibold align-middle">'. $area['name'] .'</td>';
					   	$html .= '</tr>';
					   	$html .= '</table>';
					$html .= '</header>';
				$html .= '<div>';
				$pageCount++;
			}
		}
		return $html;
	}
	static function contentIHC($postID) {
		if ($postID) {
			$pageCount = 0;
			$company = advisory_get_user_company();
			$data = advisory_get_scorecard_data($postID);
			$title = cs_get_option('ihc_pdf_title') ?? 'Infrastructure Health Check Maturity Summary';
			$recomendations = cs_get_option('ihc_pdf_recommendations') ?? '';
			$PDFTitle = $title.' - '. $company->name;
			// PDF DATA
			$html = '';
			$html .= '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';
			$html .= '<style>';
			    $html .= '@import url("https://fonts.googleapis.com/css?family=Roboto:400,500,700");';
			    $html .= '*{font-family: "Roboto", sans-serif !important;line-height:1.1;font-size: 14px;}';
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
			    $html .= '.table-bordered td.fixedbg{width: 25px !important;}';
			    $html .= '.table-bordered td {border: 1px solid #6e7275 !important;}';
			    $html .= '.table-bordered td.noborder {border: 0px !important;}';
			    $html .= '.small {font-size: 13px !important;} @page { margin: 100px 25px 0; } header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; }';
			$html .= '</style>';
			foreach ($data as $key => $area) {
				if ($pageCount > 0) continue;
				$introID = 'ihc_pdf_sections_'. advisory_id_from_string($area['name']).'_introduction';
				$pageBreak = $pageCount > 0 ? ' style="page-break-before: always;"' : '';
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
				            $html .= '<td class="small" colspan="4" rowspan="3">'. cs_get_option($introID) .'</td>';
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
						    	$sectionData = getPDFSectionsData($tableID, $section);
						    	$color = IHCRAvgStatus($sectionData['avg']);
							    // $html .= '<tr><td>'. help($sectionData, false); .'</td></tr>';
							    $html .= '<tr>';
							        $html .= '<td style="line-height: 30px;" class="blue align-middle">'. $area['name'] .'</td>';
							        $html .= '<td class="'. $color['cls'] .'"></td>';
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
					$html .= '<header>';
					        $html .= '<table style="width: 100%;" class="table table-bordered">';
					    $html .= '<tr>';
					        $html .= '<td class="subhead gray align-middle">'. $PDFTitle .'<br><span>'. get_the_date('F jS, Y',$postID) .'</span></td>';
					        $html .= '<td class="heading black center semibold align-middle">'. $area['name'] .'</td>';
					   	$html .= '</tr>';
					   	$html .= '</table>';
					$html .= '</header>';
				$html .= '<div>';
				$pageCount++;
			}
		}
		return $html;
	}
}