<?php /* Template Name: BCP Registry */
get_header();
global $user_switching;
$volnerabilityOptions = ['1'=>'NONE', '2'=>'LOW', '3'=>'MODERATE', '4'=>'HIGH', '5'=>'VERY HIGH'];
$impactOptions = ['1'=>'NONE', '2'=>'MINOR', '3'=>'NOTICEABLE', '4'=>'SEVERE', '5'=>'DEVASTATING'];
$probablityOptions = ['1'=>'POSSIBLE', '2'=>'PREDICTED', '3'=>'ANTICIPATED', '4'=>'EXPECTED', '5'=>'CONFIRMED'];
if (current_user_can('administrator') || current_user_can('advisor') || $user_switching->get_old_user()) {
    $select_attr = '';
    $save_btn = true;
} else {
	$select_attr = '';
    $save_btn = true;
	// $select_attr = 'disabled';com
    // $save_btn = false;
} 
$data = get_term_meta(advisory_get_user_company_id(), 'company_data', true);
$excerptLength = 15;
?>
<div class="content-wrapper">
	<div class="page-title">
        <div> <h1><img class="dashboardIcon" src="<?php echo get_template_directory_uri(); ?>/images/icon-rr.png" alt=""><?php _e('BCP/DR Register', 'advisory'); ?> <img class="pdfIcon" src="<?php echo get_template_directory_uri(); ?>/images/icon-pdf2.png" alt=""></h1> </div>
        <div> <ul class="breadcrumb"> <li><i class="fa fa-home fa-lg"></i></li> <li><a href="#"><?php _e('BCP/DR Register', 'advisory'); ?></a></li> </ul> </div>
    </div>
    <?php
    $company_id = advisory_get_user_company_id();
	if (advisory_metrics_in_progress($company_id, array('bcp'))) {
		$form_id = advisory_get_active_forms($company_id, array('bcp'));
	} else {
		$id = new WP_Query([
			'post_type' 		=> 'bcp',
			'post_status' 		=> 'archived',
			'posts_per_page' 	=> 1,
			'meta_query' 		=> [['key' => 'assigned_company', 'value' => $company_id]],
			'fields' 			=> 'ids',
		]);
		if ($id->found_posts > 0) { $form_id = $id->posts; }
	}
	if ($form_id[0]) {
		$rr_data = advisory_get_formatted_bcpr_data($form_id[0]);
	    foreach ($rr_data as $rr) {
	    	if (@$_GET['cat'] != advisory_id_from_string($rr['cat'])) { continue; }
	        $base = !empty($rr['base']) ? number_format($rr['base']) * 1000 : 0;
	        foreach ($rr['areas'] as $area) {
	        	// echo '<br><pre>'. print_r($area, true) .'</pre>';
	        	$base++;
		        $inherent = [];
		        foreach ($area['impact'] as $key=> $val) { $inherent[] = $val * $area['probability'][$key]; }
		        $inherent = array_merge($inherent, $area['bool']);
		        $inherent = (!empty($inherent) ? array_sum($inherent)/$area['nq']:0);
		        $vulnerability = (empty($area['vulnerability']) ? 0 : round(array_sum($area['vulnerability'])/count($area['vulnerability'])));
		        $impact = (empty($area['impact']) ? 0 : round(array_sum($area['impact'])/count($area['impact'])));
		        $probability = (empty($area['probability']) ? 0 : round(array_sum($area['probability'])/count($area['probability'])));
		        $avg = empty($area['avg']) ? 0 : $area['avg'];
		        $rr_id = advisory_id_from_string($area['name']) . '_bcp_registry';
		        $default = advisory_company_default_values(advisory_get_user_company_id(), $rr_id);
		        $defaultVulnerability = !empty($default['vulnerability']) ? $default['vulnerability'] : 0; 
		        $defaultImpact = !empty($default['impact']) ? $default['impact'] : 0; 
		        $defaultProbablity = !empty($default['probablity']) ? $default['probablity'] : 0; 
		        $defaultAvg = $defaultVulnerability * $defaultImpact * $defaultProbablity;
		        $vul = !empty($area['ai']) ? ['value' => $area['ai'], 'cls' => 'active', 'count'=>count(explode(',', $area['ai']))] : ['value' => '', 'cls' => '', 'count' => 0];
		        $ai = !empty($area['rw']) ? ['value' => $area['rw'], 'cls' => 'active', 'count'=>count(explode(',', $area['rw']))] : ['value' => '', 'cls' => '', 'count' => 0];
		        $PUCSummary = !empty($area['c_summary']) ? $area['c_summary'] : '';
		        $PUCHistoricalEvidence = !empty($area['c_historical_evidence']) ? $area['c_historical_evidence'] : '';
		        // $PUCImpact = !empty($area['c_impact']) ? $area['c_impact'] : '';
		        // $PUCProbablity = !empty($area['c_probablity']) ? $area['c_probablity'] : '';
		        echo '<div class="row" id="risk_'. $form_id[0] .'">
		            <div class="col-md-12">
		                <form class="form rr-form" method="post" data-meta="'. $rr_id .'" data-id="'. advisory_get_user_company_id() .'">
		                    <div class="card">
		                        <div class="card-title-w-btn">
		                            <h4 class="title">Category: ' . $rr['cat'] . '<br> <small>' . $base . ' : ' . $area['name'] . '</small></h4>';
		                            if ($save_btn) { echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>'; }
	                            echo '</div>
		                        <div class="card-body">
		                            <div class="table-responsive table-bcp-registry">
		                                <table class="table table-bordered table-survey mb-none">
		                                    <thead>
		                                        <tr>
		                                            <th style="width:45%;" class="t-heading-dark text-uppercase font-110p"><strong>Risk Description</big></th>
		                                            <th style="width:10%;" class="t-heading-dark text-uppercase font-110p"><strong>Assets Impacted</big></th>
		                                            <th style="width:10%;" class="t-heading-dark text-uppercase font-110p"><strong>Vulnerabilities</big></th>
		                                            <th style="width:23%;" class="t-heading-dark text-uppercase font-110p"><strong>Risk Owner</big></th>
		                                            <th style="width:12%;" class="t-heading-dark text-uppercase font-110p"><strong>Assessment Date</big></th>
		                                        </tr>
		                                    </thead>
		                                    <tbody>
		                                        <tr>
		                                            <td>'. $area['rd'] .'</td>
		                                            <th class="text-center BCPRAssetsBtn '. $ai['cls'] .'" data="'. $ai['value'] .'">'. $ai['count'] .'</th>
		                                            <th class="text-center BCPRvulnerabilityBtn '. $vul['cls'] .'" data="'. $vul['value'] .'">'. $vul['count'] .'</th>
		                                            <td class="no-padding"><textarea style="color: #333;" name="rw" class="form-control" ' . $select_attr . '>'.@$default['rw'].'</textarea></td>
		                                            <td class="text-center">'. $area['lad'] .'</td>
		                                        </tr>
		                                    </tbody>
		                                </table>
		                                <table class="table table-bordered table-survey mb-none">
		                                    <tbody>';
		                                        echo '<tr>';
		                                            echo '<th colspan="4" class="t-heading-l-dark text-uppercase text-center font-110p">Inherent Risk</th>';
		                                            echo '<th class="t-heading-l-dark text-uppercase text-center font-110p">Analysis</th>';
		                                            echo '<th colspan="3" class="t-heading-l-dark text-uppercase text-center font-110p">Controls</th>';
		                                        echo '</tr>';
		                                        echo '<tr>';
		                                            echo '<th class="t-heading-ll-dark text-center text-uppercase font-110p">Vulnerability</th>';
		                                            echo '<th class="t-heading-ll-dark text-center text-uppercase font-110p">Impact</th>';
		                                            echo '<th class="t-heading-ll-dark text-center text-uppercase font-110p">Probability</th>';
		                                            echo '<th class="t-heading-ll-dark text-center text-uppercase font-110p">Risk</th>';
		                                            echo '<td rowspan="2" class="bcprCommentWrapper p-0">';
		                                            echo '<div class="bigComment pointer" comment="'.htmlentities($PUCSummary).'"><img src="'. IMAGE_DIR_URL .'bcp/summary.png" class="img-responsive clickableIcon" alt="Summary" title="Summary"></div>';
		                                            echo '<div class="bigComment pointer" comment="'.htmlentities($PUCHistoricalEvidence).'"><img src="'. IMAGE_DIR_URL .'bcp/historical_evidence.png" class="img-responsive clickableIcon" alt="Historical Evidence" title="Historical Evidence"></div>';
		                                            // echo '<div class="bigComment pointer" comment="'.htmlentities($PUCImpact).'"><img src="'. IMAGE_DIR_URL .'bcp/impact.png" class="img-responsive clickableIcon" alt="Impact" title="Impact"></div>';
		                                            // echo '<div class="bigComment pointer" comment="'.htmlentities($PUCProbablity).'"><img src="'. IMAGE_DIR_URL .'bcp/probablity.png" class="img-responsive clickableIcon" alt="Probability" title="Probability"></div>';
		                                            echo '</td>';
		                                            echo '<td rowspan="3" colspan="3" class="no-padding width-65p">';
		                                                echo '<textarea name="controls" rows="3" class="form-control" ' . $select_attr . '>'.@$default['controls'].'</textarea>';
		                                            echo '</td>';
		                                        echo '</tr>';
		                                        echo '<tr>';
		                                            echo '<td class="no-paddding angleContainer font-110p '. BCPcolorByValue($vulnerability) .'"> <div class="angularArea risk"></div>' . $volnerabilityOptions[$vulnerability] .'</td>';
		                                            echo '<td class="no-paddding angleContainer font-110p '. BCPcolorByValue($impact) .'"> <div class="angularArea risk"></div>' . $impactOptions[$impact] .'</td>';
		                                            echo '<td class="no-paddding angleContainer font-110p '. BCPcolorByValue($probability) .'"><div class="angularArea risk"></div> ' . $probablityOptions[$probability] .'</td>';
		                                            echo '<td class="text-center no-paddding font-110p ' . bcp_risk_class($avg) . '">' . @$avg . '</td>';
		                                        echo '</tr>
		                                    </tbody>
		                                </table>
		                                <table class="table table-bordered table-survey">
		                                	<tbody>
		                                        <tr>
		                                            <th style="width:35%;" colspan="2" class="t-heading-l-dark text-uppercase text-center font-110p">Proposed Risk Treatment</th>
		                                            <th style="width:24%;" colspan="3" class="t-heading-l-dark text-uppercase text-center font-110p">Residual Risk</th>
		                                            <th style="width:41%;" class="t-heading-l-dark text-uppercase font-110p">Notes</th>
		                                        </tr>
		                                        <tr>
		                                            <th class="t-heading-ll-dark text-center text-uppercase font-110p">Avoid</th>
		                                            <td class="no-padding"><input type="text" name="avoid" class="form-control boldText no-border" ' . $select_attr . ' value="'.@$default['avoid'].'"></td>
		                                            <th class="t-heading-ll-dark text-center text-uppercase font-110p">Vulnerability</th>
		                                            <th class="t-heading-ll-dark text-center text-uppercase font-110p">Impact</th>
		                                            <th class="t-heading-ll-dark text-center text-uppercase font-110p">Probability</th>
		                                            <td rowspan="4" class="no-padding"><textarea name="notes" rows="6" class="form-control" ' . $select_attr . '>'.@$default['notes'].'</textarea></td>
		                                        </tr>
		                                        <tr>
		                                            <th class="t-heading-ll-dark text-center text-uppercase font-110p">Mitigate</th>
		                                            <td class="no-padding"><input type="text" name="mitigate" class="form-control boldText no-border" ' . $select_attr . ' value="'.@$default['mitigate'].'"></td>
		                                            <td rowspan="2" class="no-padding angleContainer font-110p '.BCPcolorByValue($defaultVulnerability).'"><div class="angularArea"></div>' . advisory_opt_select('vulnerability', '', '', $select_attr, $volnerabilityOptions, $defaultVulnerability) . '</td>
													<td rowspan="2" class="no-padding angleContainer font-110p '.BCPcolorByValue($defaultImpact).'"><div class="angularArea"></div>' . advisory_opt_select('impact', '', '', $select_attr, $impactOptions, $defaultImpact) . '</td>
		                                            <td rowspan="2" class="no-padding angleContainer font-110p '.BCPcolorByValue($defaultProbablity).'"><div class="angularArea"></div>' . advisory_opt_select('probablity', '', '', $select_attr, $probablityOptions, $defaultProbablity) . '</td>
		                                        </tr>
		                                        <tr>
		                                        	<th class="t-heading-ll-dark text-center text-uppercase font-110p">Accept</th>
		                                        	<td class="no-padding"><input type="text" name="accept" class="form-control boldText no-border" ' . $select_attr . ' value="'.@$default['accept'].'"></td>
		                                        </tr>
		                                        <tr>
		                                        	<th class="t-heading-ll-dark text-center text-uppercase font-110p">Transfer</th>
		                                        	<td class="no-padding"><input type="text" name="transfer" class="form-control boldText no-border" ' . $select_attr . ' value="'.@$default['transfer'].'"></td>
		                                        	<th class="t-heading-ll-dark text-center text-uppercase font-110p">TARGET</th>
		                                        	<td class="no-padding bcprAvg '.bcp_risk_class($defaultAvg).'" colspan="2">&nbsp;</td>
		                                        </tr>
		                                    </tbody>
		                                </table>
		                            </div>
		                        </div>
		                    </div>
		                </form>
		            </div>
		        </div>';
	        }
	    }
	} else {
		echo '<p>' . __('BCP/DR Register Not Available Yet') . '</p>';
	} ?>
</div>
<div class="modal fade" id="BCPRModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-inverse" style="background: #000000bf;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Select options</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="helpModal">Asset & Vulnerability Definitions</h4>
            </div>
            <div class="modal-body">
                <iframe width="100%" height="600" src="<?php echo get_template_directory_uri() .'/images/pdf/Asset_and_Vulnerability_Codes.pdf' ?>"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="bigCommentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-inverse">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Comment</h4>
            </div>
            <div class="modal-body" style="font-size: 16px;"></div>
            <div class="modal-footer"> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) {
"use strict"

$(document).on('click', '.pdfIcon', function(event) {
    event.preventDefault();
    $('#helpModal').modal('show');
});
$(document).on('click', '.bigComment', function(event) {
    event.preventDefault();
    var comment = $(this);
    var text = comment.attr('comment');
    var modal = $('#bigCommentModal');

    modal.find('.modal-body').html(text);
    modal.modal('show');
});
$('#bigCommentModal').on('hide.bs.modal', function() {
    $(this).find('.modal-body').html('');
});

});
</script>
<?php get_footer(); ?>