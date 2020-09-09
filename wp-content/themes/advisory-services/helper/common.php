<?php
function exportSummaryToCSV() {
	$data = [];
	if ( isset($_POST['export_summary']) ) {
		$dependencies = advisory_get_dashboard_report_card_data();
		$data = advisory_prepare_export_catalogue_summary_data($dependencies);
		if (!empty($data)) {
			if ($_POST['export_summary'] == 'csv') advisory_downloadAsCSV($data);
			if ($_POST['export_summary'] == 'excel') advisory_downloadAsExcel($data);
		}
	} else if ( isset($_POST['export_service_criticality_reportcard']) ) {
		$upstreams = advisory_get_reportcard_data();
		$data = advisory_prepare_export_service_criticality_reportcard_data_for_excel_and_csv($upstreams);
		if ( !empty($data) ) {
			$title = 'Criticality Service Report Card';
			if ( $_POST['export_service_criticality_reportcard'] == 'csv' ) advisory_downloadAsCSV($data, $title);
			if ( $_POST['export_service_criticality_reportcard'] == 'excel' ) advisory_downloadAsExcel_for_criticality_reportcard($data, $title);
		}
	} else if ( isset($_POST['export_cloud_service_catalogue']) ) {
		// $upstreams = advisory_get_cloud_reportcard_data();
		// $data = advisory_prepare_export_cloud_service_catalogue_data_for_excel_and_csv($upstreams);
		// help($data);
	}
	return true;
}

function advisory_downloadAsCSV($data, $title='Catalogue Summary') {
	if (!empty($data)) {
	    $fileName = $title.'.csv';
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header('Content-Description: File Transfer');
	    header("Content-type: text/csv");
	    header("Content-Disposition: attachment; filename={$fileName}");
	    header("Expires: 0");
	    header("Pragma: public");
	    $fh1 = @fopen( 'php://output', 'w' );
	    $headerDisplayed1 = false;
	    foreach ( $data as $data1 ) {
	        // Add a header row if it hasn't been added yet
	        if ( !$headerDisplayed1 ) {
	            // Use the keys from $data as the titles
	            fputcsv($fh1, array_keys($data1));
	            $headerDisplayed1 = true;
	        }
	        // Put the data into the stream
	        fputcsv($fh1, $data1);
	    }
	    // Close the file
	    fclose($fh1);
	    // Make sure nothing else is sent, our file is done
	    exit;
	}
}
function advisory_downloadAsExcel($rows) {
	if (!empty($rows)) {
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Document")
									 ->setSubject("Office 2007 XLSX Document")
									 ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Result file");

		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Catalogue')
		            ->setCellValue('B1', 'Full Service')
		            ->setCellValue('C1', 'Service')
		            ->setCellValue('D1', 'Technology Dependency')
		            ->setCellValue('E1', 'Department')
		            ->setCellValue('F1', 'Service/Process')
		            ->setCellValue('G1', 'RTO')
		            ->setCellValue('H1', 'RPO')
		            ->setCellValue('I1', 'CL')
		            ->setCellValue('J1', 'Tier');
		// Miscellaneous glyphs, UTF-8
		
		foreach ($rows as $rowSI => $row) {
			$columnSI = $rowSI + 2;
			$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue( 'A'.$columnSI, trim($row['Catalogue']) )
			            ->setCellValue( 'B'.$columnSI, trim($row['Full Service']) )
			            ->setCellValue( 'C'.$columnSI, trim($row['Service']) )
			            ->setCellValue( 'D'.$columnSI, trim($row['Technology Dependency']) )
			            ->setCellValue( 'E'.$columnSI, trim($row['Department']) )
			            ->setCellValue( 'F'.$columnSI, trim($row['Service/Process']) )
			            ->setCellValue( 'G'.$columnSI, trim($row['RTO']) )
			            ->setCellValue( 'H'.$columnSI, trim($row['RPO']) )
			            ->setCellValue( 'I'.$columnSI, trim($row['CL']) )
			            ->setCellValue( 'J'.$columnSI, trim($row['Tier']) );
		}

		// $objPHPExcel->setActiveSheetIndex(0) ->setCellValue('A4', 'Miscellaneous glyphs') ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Catalogue Summary');

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(60);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(6);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Catalogue Summary.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		// $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
		exit;
	}
}
function advisory_downloadAsExcel_for_criticality_reportcard($rows, $title) {
	if (!empty($rows)) {
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Document")
									 ->setSubject("Office 2007 XLSX Document")
									 ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Result file");

		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Full Service')
		            ->setCellValue('B1', 'Service')
		            ->setCellValue('C1', 'Technology Dependency')
		            ->setCellValue('D1', 'Department')
		            ->setCellValue('E1', 'Service/Process')
		            ->setCellValue('F1', 'RTO')
		            ->setCellValue('G1', 'Tier');
		// Miscellaneous glyphs, UTF-8
		
		foreach ($rows as $rowSI => $row) {
			$columnSI = $rowSI + 2;
			$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue( 'A'.$columnSI, trim($row['Full Service']) )
			            ->setCellValue( 'B'.$columnSI, trim($row['Service']) )
			            ->setCellValue( 'C'.$columnSI, trim($row['Technology Dependency']) )
			            ->setCellValue( 'D'.$columnSI, trim($row['Department']) )
			            ->setCellValue( 'E'.$columnSI, trim($row['Service/Process']) )
			            ->setCellValue( 'F'.$columnSI, trim($row['RTO']) )
			            ->setCellValue( 'G'.$columnSI, trim($row['Tier']) );
		}

		// $objPHPExcel->setActiveSheetIndex(0) ->setCellValue('A4', 'Miscellaneous glyphs') ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle($title);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(6);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		// $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
		exit;
	}
}
// BIA demo content (do not remove)
// helper\ihc.php
// helper\bia.php
// helper\mta.php
function demoContent($type='paragraph', $count=1) {
	$content = [
		'Quia distinctio non perferendis ',
		'Lorem ipsum dolor sit amet',
		'consectetur adipisicing elit',
		'maiores cum veritatis unde iste vero libero',
		'recusandae eaque aspernatur eveniet',
		'quibusdam laboriosam quo',
		'maiores cum veritatis unde iste',
	];
	$cIndex = count($content)-1;
	$paragraph = implode(' ', $content).'. ';
	// BUILDING MAIN CONTENT
	$data = '';
	if ($type == 'paragraph') {
		for ($i=0; $i < $count; $i++) $data .= '<p>'.$paragraph.'</p>';
	} else if ($type == 'list') {
		$data .= '<ul>';
		for ($i=0; $i < $count; $i++) $data .= '<li>'.$content[rand(0, $cIndex)].'</li>';
		$data .= '</ul>';
	} else if ($type == 'title') {
		for ($i=0; $i < $count; $i++) $data .= $content[rand(0, $cIndex)];
	} else {
		for ($i=0; $i < $count; $i++) $data .= $paragraph;
	}
	return $data;
}
function cleanData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}