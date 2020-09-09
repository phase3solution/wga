<?php 
// echo $RM->memories()->keys()->array(0,2)->pre();
// echo $RM->memories()->keys()->item(1)->chunk()->pre();
// To begin with. For example. As a consequence. Besides. Moreover. 
// The first point is that. It can be examplified by the fact that. What is more. It is needless to say that.
class P3RM {
	public $item;
	public $keys;
	public $data;
	public $color;
	function __construct() {
	}
	function get() {
		return $this->data;
	}
	function pre() {
		return '<br><pre'.$this->color.'>'. print_r($this->data, true) .'</pre>';
	}
	function json() {
		return json_encode($this->data);
	}
	function index($index=0) {
		$this->data = !empty($this->item[$index]) ? $this->item[$index] : '';
		return $this;
	}
	function paragraph() {
		$result = '<div class="rdContainer">';
		if (!is_array($this->data)) $result .= '<p>'.$this->data.'<p>';
		else {
			foreach ($this->data as $SI => $value) {
				$data = !empty($this->value[$SI]) ? $this->value[$SI] : '';
				if ($data) $result .= '<'.'p'.'>'.$data.'<'.'p'.'>';
			}
		}
		$result .= '</div>';
		$this->data = $result;
		return $this;
	}
	function image() {
		$data = [];
		$data[] = 'The image type illustrates subject';
		$data[] = 'The figures have been clearly shown for categories/types';
		$data[] = 'Some of these are..';
		$data[] = 'Evidently, the highest number is recorded for Factor at Figure, whereas the lowest is recorded for Factor at Figure';
		$data[] = 'Surprisingly, the second highest number or percentage';
		$data[] = 'after analyzing the information it can be concluded that this image Type is showing crucial information strongly supported by facts and figurs having great impact on S prediction.';
		$data[] = 'The possible development is impactful';
		$this->data = $data;
		return $this;
	}
	function notice($index=0) {
		$this->data = '<br><b>Deprecated</b>: '. $this->data .'<br><b>/home/liv3/public_html/roltek/wp-content/plugins/js_composer/include/classes/core/class-vc-mapper.php</b>  "on line" <b>'. rand(1, 100).'</b>';
		return $this;
	}
	function color($color=false) {
		$lightColor = ' style="background-color: #16161685; padding: 15px; white-space: pre-wrap; word-wrap: break-word;"';
		$deepColor  = ' style="background-color: #161616; padding: 15px; white-space: pre-wrap; word-wrap: break-word;"';
		$color = $color ? $lightColor : $deepColor;
		$this->data = $color;
		return $this;
	}
	function item($index=0) {
		$result = [];
		$key = !empty($this->keys) ? $this->keys[$index] : $index;
		$result[$key] = !empty($this->item[$index]) ? $this->item[$index] : '';
		$this->data = $result;
		return $this;
	}
	function array($start=0, $items=1) {
		$result = [];
		$counter = $start + $items;
		for ($i=$start; $i < $counter; $i++) { 
			$key = !empty($this->keys) ? $this->keys[$i] : $i;
			$result[$key] = !empty($this->item[$i]) ? $this->item[$i] : '';
		}
		$this->data = $result;
		return $this;
	}
	function chunk() {
		$this->multi();
		return $this;
	}
	function multi() {
		$result = [];
		if (!empty($this->data)) {
			$localSI = 1;
			foreach ($this->data as $SI => $value) {
				$key = !empty($this->keys) ? $this->keys[$localSI] : $localSI;
				if (is_array($value)) {
					foreach ($value as $SI2 => $value2) {
						$result[] = !empty($value2) ? $this->strToArray($value2 ) : '';
					}
				} else $result[$key] = !empty($value) ? $this->strToArray($value ) : '';
				$localSI++;
			}
		}
		$this->data = $result;
		return $this;
	}
	function strToArray($str) {
		$arr = [];
		$str = rtrim($str, '.');
		$str = explode('.', $str);
		if (!empty($str)) {
			foreach ($str as $SI => $value) {
				$arr[] = explode(',', $value);
			}
		}
		return $arr;
	}
	function article($index=0) {
		$item = $this->allContent();
		$this->item = !empty($item[$index]) ? $item[$index] : '';
		return $this;
	}
	function memory($index=0) {
		$memories = $this->memories();
		$this->item = !empty($memories[$index]) ? $memories[$index] : '';
		return $this;
	}
	function keys() {
		// user_login
		$str = 'Title avg Desc Site Observations Operational Resilience Fire Suppression Workload and Drivers IOPS Analysis I/O Load Analysis Storage Analysis Procurement Power Cooling Standards Access Control Video Surveillance Utilization Operations Power Server Population Implementation Storage Efficiency Storage Decommissioning Provisioning Inventory and MDM AntiVirus Operating System Version Control Hard Disk Encryption Application Software description Version Control Account PasswordTools Capacity Process Implementation Storage Efficiency Power Cooling Standards Access Control Video Surveillance Storage Decommissioning Provisioning Inventory and MDM AntiVirus Operating System Version Control Hard Disk Encryption Application Software Version Control Account Password Web item Local Data Firmware and Drivers IOPS Analysis I/O Load Analysis Storage Analysis Power Cooling Standards Access Control Video Surveillance Tools Capacity Process Implementation Storage Efficiency Storage Decommissioning Provisioning Architecture Operations Storage Technology Trend Analysis Implementation Storage Efficiency Storage Decommissioning Provisioning Inventory and MDM AntiVirus Operating System Version Control Hard Disk Encryption Application Software Version Control Account Password';
		$arr = array_unique( array_filter(explode(' ', strtolower(str_replace('and', '', $str)))) );
		sort($arr);
		shuffle($arr);
		$this->keys = $arr;
		return $this;
	}
	function allContent() {
		return;
	}
	function memories($item=false) {
		$items =  [
			[
				'Advantages & Disadvantages',
				'The subject is a frequently ventilated issue nowadays with argumentation involving both sides. While it offers a good number of positives leading towards a more propestive lifestyle, but to the contrary, it might come up with few drawbacks which are likely to cause inconveniences.', 
				'Now let us reveal the positive aspects of this phenomenon. To begin with. For example. As a consequence. Besides. Moreover.',
				'In addition to this advantages, we are more likely to experience a few disadvantages as well. The first point is that. It can be examplified by the fact that. What is more. It is needless to say that.',
				'Taking all the remarks apropos of the circumstances into account, we may conclude that {subject} has both merits and demerits. However, if, in the future, efficacious maneuver is administrated, there is good reason to believe that these negative consequences will fall entirely away.'
			],
			[
				'Agree Disagree',
				'Being a singnificantly controversial issue nowadays, the subject deserves to be perused vigilantly. While some people opine strongly for it, the opponents are found to be quite consistent. In this essay, I will draw upon both point of view before reckoning with my own opinion straightaway',
				'Now, let us reveal the rationales that drive a group of people towards this statement. To bigin with. For example. As a consequence. Besides. It is generally discerned that. Moreover.', 
				'On the other hand, there are few convincing arguments against this statement. The first point is that. It can be examplified by the fact that. What is more. It is needless to say that.',
				'To conclude, I believe that it is a logical affirmation that {statement}. What is put forward in this essay is a good reflection of what I want to express my own verdict.'
			],
			[
				'Problem Solution',
				'among a thousand different issues that world is facing at this point, "the Subject" is the most concerning issue for the world and the living being. It is continuing to create problem more than ever before. On one hand, there are a number of factors which are held liable for this happening, but at the same time, we can tackle this predicament by administering a few pragmatic maneuver.',
				'Now, let us point out the reasons for this incidence. To begin with, __. For example. Besides. It is generally discerned that. Moreover',
				'In an attempt to overcome these plights, a few guidelines will be rewarding. The first point is that. This can be examplified by the fact that. What is more. It is needless to say that',
				'Taking all the remarks apropos of the circumstances into account, we can conclude that, "The Subject" is a perturbing issue in the ongoing perspective. Being aware of this is a vaiable fight back, but the measures put forward in this essay are the keys to reach a plasible solution.'
			]
		];
		if ($item != false) $this->item = $items[$item];
		else $this->item = $items;
		return $this;
	}
	function globalization() {
		
		// Downsides of globalization made the world overwhelmed - what are the drastic initiatives the governments and organisations worldwide can take ?
			$html .= '<among the="thoudand different issues"> the world is fetching at this point the downsides of globalization is the concering one. It is continuing to create problems more than ever before. In this essay I will draw upon both points of view before reckoing with my own opinion </straightaway>';
		    $html .= '<nowletus pointout="thereasons responsible forthese plights"> To begin with, for globalization people are looking for chepest workers around the world. For example, an Indian labour is much more cheaper than a US labour and employers are interested to hire Indian labour than developed countries labours. As a concequence, developed countries labours are forced to become jobless or doing jobs in less salary. Moreover, different types of disease are spreading unintentionally for the globalization</period>';
		    $html .= '<inanattempt> to overcome these plights, a few guidelines will be rewarding. ';
		$html .= 'The first point is that, in order to a nation to prosper, its people must be aware of the negative effects of the globalization.';
		$html .= 'It can be exemplified by tha fact that, ';
		$html .= 'people and government should be aware of the local economy and the epidemic.';
		$data[] = ['it is needless to say that,', ];
		if ($needless) {
			$html = '<div>Along with its advantages the Goverment should take precausations to defend all the negative effects of globalization.</div>';
		}
		if (isset($_POST['export_summary_csv'])) {
			$dependencies = advisory_get_dashboard_report_card_data();
			if (!empty($dependencies['it'])) {
				foreach ($dependencies['it'] as $service) {
					if ($service['items']) {
						foreach ($service['items'] as $item) {
							$data[] = ['Catalogue' => 'IT Service Catalogue', 'Service' => @$service['name'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'CL' => @$item['cl']['value']];
						}
					}
				}
			}
			if (!empty($dependencies['cloud'])) {
				foreach ($dependencies['cloud'] as $service) {
					if ($service['items']) {
						foreach ($service['items'] as $item) {
							$data[] = ['Catalogue' => 'Cloud Service Catalogue', 'Service' => @$service['name'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'CL' => @$item['cl']['value']];
						}
					}
				}
			}
			if (!empty($dependencies['desktop'])) {
				foreach ($dependencies['desktop'] as $service) {
					if ($service['items']) {
						foreach ($service['items'] as $item) {
							$data[] = ['Catalogue' => 'Desktop Service Catalogue', 'Service' => @$service['name'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'CL' => @$item['cl']['value']];
						}
					}
				}
			}
			if (!empty($data)) downloadAsCSV($data);
		}

		if ($condition && $condition2) {
			$con = 'Taking all the remars apropos of the circumstances into account, it can be concluded that downsides of the globalization has made the world overwhelmed. Being aware of it is a viable fight back, but the mesures put forward in this essay are keys to reach a plasible solution.';
		}
	}
}
$RM = new P3RM;
// echo $RM->memories()->keys()->array(0,2)->pre();

function exportSummdaryToCSV() {
	$data = [];
		if (isset($_POST['export_summary_csv'])) {
			$dependencies = advisory_get_dashboard_report_card_data();
			if (!empty($dependencies['it'])) {
				foreach ($dependencies['it'] as $service) {
					if ($service['items']) {
						foreach ($service['items'] as $item) {
							$data[] = ['Catalogue' => 'IT Service Catalogue', 'Service' => @$service['name'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'CL' => @$item['cl']['value']];
						}
					}
				}
			}
			if (!empty($dependencies['cloud'])) {
				foreach ($dependencies['cloud'] as $service) {
					if ($service['items']) {
						foreach ($service['items'] as $item) {
							$data[] = ['Catalogue' => 'Cloud Service Catalogue', 'Service' => @$service['name'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'CL' => @$item['cl']['value']];
						}
					}
				}
			}
			if (!empty($dependencies['desktop'])) {
				foreach ($dependencies['desktop'] as $service) {
					if ($service['items']) {
						foreach ($service['items'] as $item) {
							$data[] = ['Catalogue' => 'Desktop Service Catalogue', 'Service' => @$service['name'], 'Department' => @$item['area'], 'RTO' => @$item['rto'], 'CL' => @$item['cl']['value']];
						}
					}
				}
			}
			if (!empty($data)) downloadAsCSV($data);
		}
}
function downldfoadAsCSV($data) {
	if (!empty($data)) {
	    $fileName_1 = 'Catalogue Summary.csv';
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header('Content-Description: File Transfer');
	    header("Content-type: text/csv");
	    header("Content-Disposition: attachment; filename={$fileName_1}");
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
// BIA demo content (do not remove)
// helper\ihc.php
// helper\bia.php
// helper\mta.php
function demfoContent($type='paragraph', $count=1) {
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