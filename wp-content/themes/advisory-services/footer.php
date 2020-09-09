	</div><!-- .wrapper -->
    <?php wp_footer(); ?>
	<?php if (is_page('dashboard') && is_user_logged_in()) { 
		$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
		$dashboard = @$data['userDashboard'] == 'Dashboard B' ? true : false;
		if ($dashboard) : ?>
			<script type="text/javascript">
				var config = {
		            type: 'line',
		            data: {
		                labels: ["C", "I", "B", "T"],
		                datasets: [<?php foreach ($data['mta_static'] as $static) {
		                	echo '{';
			                echo 'label: "' . $static['date'] . '",
			                    backgroundColor: "' . $static['color'] . '",
			                    borderColor: "' . $static['color'] . '",
			                    fill: false,
					            pointRadius: 5,
					            pointBackgroundColor: "#fff",
					            pointBorderColor: "' . $static['color'] . '",
					            pointBorderWidth: 3,';
			                echo 'data: ['.$static['customer_facing'].','.$static['integration'].','.$static['business_solutions'].','.$static['technology_infrastructure'] . '],';
			                echo '},';
		                } ?>]
		            },
		            options: {
		                responsive: true,
		                title:{
		                    display:false,
		                    text:'MTA Trend Analysis',
		                    fontSize: 30,
		                    fontStyle:'bold',
							fontColor:'#000',
							// fontFamily:'Arial'
		                },
		                tooltips: { 
		                	mode: 'index', 
		                	intersect: false,
		                	callbacks: {
		                		title : function(tooltipItems, data) {
		                			if (tooltipItems[0].xLabel) {
		                				switch (tooltipItems[0].xLabel) {
		                					case 'C': return 'Customer Facing'; break;
		                					case 'I': return 'Integration'; break;
		                					case 'B': return 'Business Solutions'; break;
		                					case 'T': return 'Technology Infrastructure'; break;
		                					default: return ''; break;
		                				}
		                			}
		                			return '';
		                		}
		                	}
		                },
		                hover: { mode: 'nearest', intersect: true },
		                legend: { display: true, labels : {padding : 10}},
		                scales: {
		                    xAxes: [{
		                        display: true,
		                        scaleLabel: { display: false },
		                        ticks: {
		                        	autoSkip: false,
		                            fontSize:12,
									fontStyle:'bold',
									fontColor: 'transparent'
		                        },
		                        gridLines: { display: false, color: "black"},
		                    }],
		                    yAxes: [{
		                        display: true,
		                        scaleLabel: {
		                            display: true,
		                            labelString: 'Scorecard Rating',
		                            fontSize:16,
									fontStyle:'700',
									fontColor:'#000',
									padding: 10
		                        },
		                        gridLines: {display: false, color: "black"},
		                        ticks: {suggestedMin: 1, max: 10 }
		                    }]
		                },
		                onClick: function(e) {
		                	var activePoints = lineChart.getElementAtEvent(e)
		                	<?php if (!empty($data['static'])) { $key = 0; foreach ($data['static'] as $static) {
			                	echo 'if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 0) {
		                    		' . ($static['operations_link'] ? 'window.open("' . $static['operations_link'] . '", "_blank")' : "" ) .'
		                    	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 1) {
		                    		' . ($static['hardware_link'] ? 'window.open("' . $static['hardware_link'] . '", "_blank")' : "" ) .'
		                    	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 2) {
		                    		' . ($static['software_link'] ? 'window.open("' . $static['software_link'] . '", "_blank")' : "" ) .'
		                    	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 3) {
		                    		' . ($static['network_link'] ? 'window.open("' . $static['network_link'] . '", "_blank")' : "" ) .'
		                    	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 4) {
		                    		' . ($static['data_management_link'] ? 'window.open("' . $static['data_management_link'] . '", "_blank")' : "" ) .'
		                    	}';
		                    	$key++;
			                } } ?>
		                }
		            }
		        }
				var ctxl = jQuery("#surveyChart").get(0).getContext("2d");
				var lineChart = new Chart(ctxl, config)
			</script>
	<?php else : ?>
		    <script type="text/javascript">
				var config = {
		            type: 'line',
		            data: {
		                labels: ["O", "H", "S", "N", "D"],
		                datasets: [<?php foreach ($data['static'] as $static) {
		                	echo '{
			                    label: "' . $static['date'] . '",
			                    backgroundColor: "' . $static['color'] . '",
			                    borderColor: "' . $static['color'] . '",
			                    data: [' . $static['operations'] . ',' . $static['hardware'] . ',' . $static['software'] . ',' . $static['network'] . ',' . $static['data_management'] . '],
			                    fill: false,
					            pointRadius: 5,
					            pointBackgroundColor: "#fff",
					            pointBorderColor: "'. $static['color'] .'",
					            pointBorderWidth: 3,
			                },';
		                } ?>]
		            },
		            options: {
		            	responsive: true,
		                title:{
		                    display: false,
                            text:'IHC Trend Analysis',
		                    fontSize: 30,
		                    fontStyle:'bold',
							fontColor:'#000',
							// fontFamily:'Arial'
		                },
		                legend: { display: true, labels : { padding : 10, fontSize: 14, fontStyle:'bold'}},
		                hover: { mode: 'nearest', intersect: true },
						tooltips: { 
		                	mode: 'index', 
		                	intersect: false,
		                	callbacks: {
		                		title : function(tooltipItems, data) {
		                			if (tooltipItems[0].xLabel) {
		                				switch (tooltipItems[0].xLabel) {
		                					case 'O': return 'Operations'; break;
		                					case 'H': return 'Hardware'; break;
		                					case 'S': return 'Software'; break;
		                					case 'N': return 'Network'; break;
		                					case 'D': return 'Data Management'; break;
		                					default: return ''; break;
		                				}
		                			}
		                			return '';
		                		}
		                	}
		                },
						scales: {
							xAxes: [{
							   	display: true,
							   	scaleLabel: {display: false },
							   	ticks: {autoSkip: false, fontSize:12, fontStyle:'bold', fontColor: 'transparent'},
							   	gridLines: { display: false, color: "black"},
							}],
							yAxes: [{
							   	display: true,
							   	scaleLabel: {
							        display: true,
							       	labelString: 'Scorecard Rating',
							        fontSize:16,
									fontStyle:'700',
									fontColor:'#000',
									padding: 10
							    },
							    gridLines: {display: false, color: "black"},
							    ticks: {suggestedMin: 1, max: 5 }
							}]
                       	},
                        onClick: function(e) {
                           	var activePoints = lineChart.getElementAtEvent(e)
                           	<?php $key = 0; foreach ($data['static'] as $static) {
                           	echo 'if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 0) {
                               	' . ($static['operations_link'] ? 'window.open("' . $static['operations_link'] . '", "_blank")' : "" ) .'
                               	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 1) {
                               	' . ($static['hardware_link'] ? 'window.open("' . $static['hardware_link'] . '", "_blank")' : "" ) .'
                               	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 2) {
                               	' . ($static['software_link'] ? 'window.open("' . $static['software_link'] . '", "_blank")' : "" ) .'
                               	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 3) {
                               	' . ($static['network_link'] ? 'window.open("' . $static['network_link'] . '", "_blank")' : "" ) .'
                               	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 4) {
                               	' . ($static['data_management_link'] ? 'window.open("' . $static['data_management_link'] . '", "_blank")' : "" ) .'
                               	}';
                               	$key++;
                           } ?>
                        }
                    }
		        }
				var ctxl = jQuery("#surveyChart").get(0).getContext("2d");
				var lineChart = new Chart(ctxl, config)
			</script>
	<?php endif; ?>
	<?php } ?>
	<?php if (is_page('itscm') && is_user_logged_in()) { $tid = advisory_get_user_company_id(); 
		$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
		// Newly added by kamrul
		$areas = advisory_dashboard_avg($tid, array('drm'));
		if (!empty($areas)) {
			foreach ($areas as $area) {
				if (!empty($area['values'])) { $avg = array_sum($area['values']) / count($area['values']); } 
				else { $avg = 0; }
                // prepare the array to use in data map
                if ($area['name'] == 'Organizational Readiness') 	$data['dr_static'][1]['organizational_readiness'] = $avg;
                if ($area['name'] == 'Technology Readiness') 		$data['dr_static'][1]['technology_readiness'] = $avg;
                if ($area['name'] == 'Recovery Planning') 			$data['dr_static'][1]['recovery_planning'] = $avg;
                if ($area['name'] == 'Maintenance & Improvement') 	$data['dr_static'][1]['maintenance_sand_improvement'] = $avg;
			}
		}
		?>
	    <script type="text/javascript">
			var config = {
	            type: 'line',
	            data: {
	                labels: ["O", "T", "R", "M"],
	                datasets: [<?php foreach ($data['dr_static'] as $static) {
	                	echo '{
		                    label: "' . $static['dr_date'] . '",
		                    backgroundColor: "' . $static['dr_color'] . '",
		                    borderColor: "' . $static['dr_color'] . '",
		                    fill: false,
				            pointRadius: 5,
				            pointBackgroundColor: "#fff",
				            pointBorderColor: "' . $static['dr_color'] . '",
				            pointBorderWidth: 3,';
				            echo 'data: [' . $static["organizational_readiness"] . ',' . $static["technology_readiness"] . ',' . $static["recovery_planning"] . ',' . $static["maintenance_sand_improvement"].'],';
				            // echo 'data: [1,2,3,1],';
		                echo '},';
	                } ?>]
	            },
	            options: {
	                responsive: true,
	                title:{ display:false, text:'Disaster Recovery Trend Analysis', fontSize: 15 },
	                tooltips: { 
	                	mode: 'index', 
	                	intersect: false,
	                	callbacks: {
	                		title : function(tooltipItems, data) {
	                			if (tooltipItems[0].xLabel) {
	                				switch (tooltipItems[0].xLabel) {
	                					case 'O': return 'Organizational Readiness'; break;
	                					case 'T': return 'Technology Readiness'; break;
	                					case 'R': return 'Recovery Planning'; break;
	                					case 'M': return 'Maintenance & Improvement'; break;
	                					default: return ''; break;
	                				}
	                			}
	                			return '';
	                		}
	                	}
	                },
	                hover: { mode: 'nearest', intersect: true },
	                scales: {
	                    xAxes: [{
	                        display: true,
	                        scaleLabel: { display: false },
	                        ticks: { autoSkip: false, fontSize:12, fontStyle:'bold', fontColor: 'transparent' },
	                        gridLines: { display: false, color: "black"},
	                    }],
	                    yAxes: [{
	                        display: true,
	                        scaleLabel: {
	                            display: true,
	                            labelString: 'Scorecard Rating',
	                            fontSize:16,
								fontStyle:'700',
								fontColor:'#000',
								padding: 10
	                        },
	                        gridLines: { display: false, color: "black"},
	                        ticks: { suggestedMin: 1, max: 5 }
	                    }]
	                },
	                onClick: function(e) {
	                	var activePoints = lineChartDr.getElementAtEvent(e)
	                	<?php $key = 0; foreach ($data['dr_static'] as $static) {
		                	echo 'if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 0) {
	                    		' . ($static['organizational_readiness_link'] ? 'window.open("' . $static['organizational_readiness_link'] . '", "_blank")' : "" ) .'
	                    	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 1) {
	                    		' . ($static['technology_readiness_link'] ? 'window.open("' . $static['technology_readiness_link'] . '", "_blank")' : "" ) .'
	                    	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 2) {
	                    		' . ($static['recovery_planning_link'] ? 'window.open("' . $static['recovery_planning_link'] . '", "_blank")' : "" ) .'
	                    	}if (activePoints[0]._datasetIndex == ' . $key . ' && activePoints[0]._index == 3) {
	                    		' . ($static['maintenance_sand_improvement_link'] ? 'window.open("' . $static['maintenance_sand_improvement_link'] . '", "_blank")' : "" ) .'
	                    	}';
	                    	$key++;
		                } ?>
	                }
	            }
	        }
			var ctxldr = jQuery("#surveyChartDr").get(0).getContext("2d");
			var lineChartDr = new Chart(ctxldr, config)
		</script>
	<?php } ?>
</body>
</html>