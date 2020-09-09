<?php 
// DMM Maturity
$disableLinks = false;
$dmmMaturity = new WP_Query([
    'post_type' => 'dmm',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'meta_query' => [['key' => 'assigned_company', 'value' => $user_company_id, ]],
    'fields' => 'ids',
]);
if ($dmmMaturity->found_posts > 0) $disableLinks = true;
?>
<div class="row">
    <div class="col-md-12 col-lg-6">
        <div class="bs-component">
            <div class="panel">
                <?php 
                echo '<div class="panel-heading text-center pb-0">';
                    if (advisory_metrics_in_progress($user_company_id, array('mta'))) echo '<img src="'. IMAGE_DIR_URL .'current-metrics-process.jpg" alt="" class="img-responsive">';
                    else echo '<img src="'.IMAGE_DIR_URL.'current-metrics.jpg" alt="" class="img-responsive">'; 
                echo '</div>';
                echo '<div class="panel-body mta-panel">';
                    echo '<div class="panel-option">';
                        echo '<ul class="m-0">';
                            $current_metrics_post_type = 'mta';
                            if (advisory_metrics_in_progress($user_company_id, array($current_metrics_post_type))) {
                                $areas = advisory_transient_avg_mta($user_company_id, array($current_metrics_post_type));
                            } else {
                                $areas = advisory_dashboard_avg_mta($user_company_id, array($current_metrics_post_type));
                                if (empty($areas)) { $areas = [['name' => 'Customer Facing'], ['name' => 'Integration'], ['name' => 'Business Solutions'], ['name' => 'Technology Infrastructure']]; }
                            }
                            if (!empty($areas)) {
                                foreach ($areas as $area) {
                                    if (!empty($area['values'])) {
                                        $avg = array_sum($area['values']) / count($area['values']);
                                        $avgTxt = number_format($avg, 1);
                                        $imageUrl = IMAGE_DIR_URL.'current_metrics/'.$current_metrics_post_type.'/'.advisory_id_from_string($area['name']) .'_'. coloring_elements(number_format($avg, 1), 'mta-panel') . '.png';
                                    } else {
                                        $avg = 0;
                                        $avgTxt = 'N/A';
                                        $imageUrl = IMAGE_DIR_URL.'current_metrics/'.$current_metrics_post_type.'/'.advisory_id_from_string($area['name']) . '.png';
                                    }
                                    echo '<li><img src="'.$imageUrl.'" alt="'.$area['name'].'" title="'.$area['name'].' ('.$avgTxt.')" class="img-responsive"></li>';
                                }
                            }
                        echo '</ul>';
                    echo '</div>';
                    // echo '<br><pre>'. print_r(advisory_dashboard_avg_mta($user_company_id, [$current_metrics_post_type]), true) .'</pre>';
                echo '</div>'; ?>
                <div class="panel-body">
                    <div class="panel-chart">
                        <div class="row">
                            <div class="col-sm-9">
                                <img src="<?php echo P3_TEMPLATE_URI; ?>/images/mta.jpg" class="img-responsive text-center" usemap="#Map" hidefocus="true">
                                <map name="Map" id="Map">
                                    <area alt="" title="Customer Facing" href="<?php echo advisory_graphic_link(array('mta'), 'customer_facing') ?>" shape="poly" coords="2,440,80,440,195,440,470,441,751,440,1095,440,1675,439,1949,439,1950,580,1950,746,1352,746,587,747,177,747,5,745" />
                                    <area alt="" title="Integration" href="<?php echo advisory_graphic_link(array('mta'), 'integration') ?>" shape="poly" coords="3,748,520,751,1269,750,1733,747,1864,747,1949,749,1950,898,1949,1051,1353,1049,666,1050,231,1050,6,1049,0,1047" />
                                    <area alt="" title="Business Solutions" href="<?php echo advisory_graphic_link(array('mta'), 'business_solutions') ?>" shape="poly" coords="4,1050,967,1052,1855,1052,1946,1051,1950,1187,1949,1356,1356,1356,396,1356,77,1354,4,1355" />
                                    <area alt="" title="Technology Infrastructure" href="<?php echo advisory_graphic_link(array('mta'), 'technology_infrastructure') ?>" shape="poly" coords="3,1357,666,1357,1303,1357,1878,1359,1946,1357,1951,1494,1950,1662,1354,1663,495,1662,67,1663,3,1663" />
                                </map>
                                <div class="row dashboardBLolo hidden">
                                    <div class="col-sm-3">
                                        <a href="<?php echo advisory_graphic_link(array('itsm'), 'it_management') ?>"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/icon-itos.jpg" class="img-responsive" alt=""></a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="<?php echo advisory_graphic_link(array('cra'), 'technical_architecture') ?>"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/icon-cr.jpg" class="img-responsive" alt=""></a>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-sm-3">
                                <div class="row">
                                    <?php
                                        $itsmLink = advisory_graphic_link(array('itsm'), 'it_management');
                                        $craLink = advisory_graphic_link(array('cra'), 'technical_architecture');
                                        if ($itsmLink == '#') $itsmLink = 'javascript:;';
                                        if ($craLink == '#') $craLink = 'javascript:;';
                                        $sfia = $sfia_premission ? 'href="'.home_url('sfia-dashboard').'"' : 'href="javascript:;" data-toggle="modal" data-target="#sfia"';
                                    ?>
                                    <br>
                                    <div class="col-xs-4 visible-xs-custom visible-xs text-center">
                                        <a href="<?php echo advisory_graphic_link(array('itsm'), 'it_management') ?>"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/icon-itos.png"></a>
                                    </div>
                                    <div class="col-xs-4 visible-xs-custom visible-xs text-center">
                                        <a href="<?php echo advisory_graphic_link(array('cra'), 'technical_architecture') ?>"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/icon-dm.png"></a>
                                    </div>
                                    <div class="col-xs-4 visible-xs-custom visible-xs text-center">
                                        <a href="<?php echo advisory_graphic_link(array('cra'), 'technical_architecture') ?>"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/icon-cr.png"></a>
                                    </div>
                                    <div class="col-sm-offset-0 col-sm-12 col-xs-offset-4 col-xs-4 text-right" style="padding-left: 0;">
                                        <a href="<?php echo get_site_url(null, '/itscm/') ?>"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/itscm-readinesst.png" class="img-responsive" alt=""></a>
                                        <a href="<?php echo site_url('eva'); ?>"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/icon-eva.png" class="img-responsive" alt=""></a>
                                        <a <?php echo $sfia ?>><img src="<?php echo P3_TEMPLATE_URI; ?>/images/dashboard/sfial.png" class="img-responsive" alt=""></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<ul class="list-inline list-unstyled dasboard-float text-left hidden-xs">-->
                        <!--    <li><a class="<?php //echo $itsmLink != 'javascript:;' ? '' :'emptyDMMPopup'; ?>" href="<?php //echo $itsmLink ?>"><img src="<?php //echo P3_TEMPLATE_URI; ?>/images/icon-itos.png" class="img-responsive" alt=""></a></li>-->
                        <!--    <li><a class="<?php //echo $disableLinks ? '' :'emptyDMMPopup'; ?>" href="<?php //echo $disableLinks ? site_url('dm-maturity') :'javascript:;'; ?>"><img src="<?php //echo P3_TEMPLATE_URI; ?>/images/icon-dm.png" class="img-responsive" alt=""></a></li>-->
                        <!--    <li><a class="<?php //echo $craLink != 'javascript:;' ? '' :'emptyDMMPopup'; ?>" href="<?php //echo $craLink; ?>"><img src="<?php //echo P3_TEMPLATE_URI; ?>/images/icon-cr.png" class="img-responsive" alt=""></a></li>-->
                        <!--</ul>-->
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="row">
                    <div class="col-sm-12">
                        <div style="text-align:center; padding: 10px 25px 10px 77px;"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/dashboard/mta_trend_analysis.jpg"></div>
                        <div class="embed-responsive embed-responsive-16by9">
                            <canvas class="embed-responsive-item" id="surveyChart"></canvas>
                        </div>
                        <ul class="list-inline mtaTrendAnalysisXAxis">
                            <li class="business_solutions"><img title="Business Solutions" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/mta/business_solutions.png"></li>
                            <li class="customer_facing"><img title="Customer Facing" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/mta/customer_facing.png"></li>
                            <li class="integration"><img title="Integration" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/mta/integration.png"></li>
                            <li class="technology_infrastructure"><img title="Technology Infrastructure" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/mta/technology_infrastructure.png"></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card five-row-table">
                <img src="<?php echo P3_TEMPLATE_URI; ?>/images/prev-health-check_1.jpg" alt="" class="img-responsive">
                <div class="card-body">
                    <table class="table table-condensed panel-option-right bold">
                        <thead> <tr> <th class="bold">Category</th> <th class="bold">Date</th> <th></th> </tr> </thead>
                        <tbody>
                            <?php $query = new WP_Query(['post_type' => json_decode(ALL_SCORECARDS), 'post_status' => 'archived', 'meta_query' => array(array('key' => 'assigned_company', 'value' => $user_company_id))]); ?>
                            <?php if ($query->have_posts()) {
                                while ($query->have_posts()) {
                                    $query->the_post();
                                    $post_id = get_the_ID();
                                    echo '<tr>';
                                        echo '<td>'. advisory_get_form_name($post_id) .'</td>';
                                        $title = get_the_date();
                                        if (get_post_type($post_id) == 'mta') {
                                            $archivedDate = get_post_meta( $post_id, 'archive_date', true );
                                            if ($archivedDate) $title = date(get_option('date_format'), $archivedDate);
                                        }
                                        echo '<td>'. $title .'</td>';
                                        echo '<td class="text-right">
                                            <div class="btn-group" data-toggle="tooltip" title="View">
                                                <a class="btn btn-primary dropdown-toggle" href="#" data-toggle="dropdown"><span class="fa fa-eye"></span></a>
                                                <ul class="dropdown-menu">';
                                                    foreach (advisory_template_areas($post_id) as $area) {
                                                        echo '<li><a href="' . get_the_permalink($post_id) . '?view=true&area=' . advisory_id_from_string($area) . '" target="_blank">' . $area . '</a></li>';
                                                    }
                                                echo '</ul>
                                            </div>';
                                            if (advisory_has_survey_edit_permission(get_the_ID())) {
                                                echo ' <div class="btn-group" data-toggle="tooltip" title="Edit">
                                                    <a class="btn btn-warning dropdown-toggle" href="#" data-toggle="dropdown"><span class="fa fa-edit"></span></a>
                                                    <ul class="dropdown-menu">';
                                                        foreach (advisory_template_areas($post_id) as $area) {
                                                            echo '<li><a href="' . get_the_permalink($post_id) . '?edit=true&area=' . advisory_id_from_string($area) . '" target="_blank">' . $area . '</a></li>';
                                                        }
                                                    echo '</ul>
                                                </div>';
                                            }
                                            if (advisory_has_survey_delete_permission(get_the_ID())) {
                                                echo ' <a class="btn btn-danger delete-survey" href="#" data-id="' . get_the_ID() . '" data-toggle="tooltip" title="Delete"><span class="fa fa-trash"></a>';
                                                if (advisory_is_survey_locked(get_the_ID(), get_current_user_id())) {
                                                    echo ' <a class="btn btn-success lock-survey" href="#" data-id="' . get_the_ID() . '" data-user="' . get_current_user_id() . '" data-toggle="tooltip" title="Edit Permission"><span class="fa fa-lock"></a>';
                                                } else {
                                                    echo ' <a class="btn btn-danger lock-survey" href="#" data-id="' . get_the_ID() . '" data-user="' . get_current_user_id() . '" data-toggle="tooltip" title="Edit Permission"><span class="fa fa-unlock-alt"></a>';
                                                }
                                            }
                                        echo '</td>
                                    </tr>';
                                }
                                wp_reset_postdata();
                            } else { echo '<tr> <td colspan="3">Nothing Found</td> </tr>'; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-6">
        <div class="bs-component">
            <div class="card">
                <div class="row">
                    <div class="col-sm-8">
                        <select class="ajax-scorecard-select bold">
                            <?php $query = new WP_Query(['post_type' => json_decode(ALL_SCORECARDS), 'post_status' => 'archived', 'meta_query' => array(array('key' => 'assigned_company', 'value' => $user_company_id))]);
                            if ($query->have_posts()) {
                                $isMTAArchiveExists = false;
                                while ($query->have_posts()) {
                                    $query->the_post();
                                    $postID = get_the_ID();
                                    if (get_post_type(get_the_ID()) == 'mta') {
                                        $isMTAArchiveExists = true;
                                        $archivedDate = get_post_meta( $postID, 'archive_date', true );
                                        if ($archivedDate) $title = advisory_get_form_name($postID) .' - '. date(get_option('date_format'), $archivedDate);
                                        else $title = advisory_get_form_name($postID) .' - '. get_the_date();
                                        $mta_areas = getMtaAreas($postID);
                                        if (!empty($mta_areas)) {
                                            foreach ($mta_areas as $areaID => $areaName) { 
                                                echo '<option value="'. $postID .'" type="mta" areaid="'.$areaID.'">'. $title .' - '. $areaName .'</option>';
                                            }
                                        }
                                    } else {
                                        echo '<option value="'. $postID .'">'. advisory_get_form_name($postID) .' - '. get_the_date() .'</option>';
                                    }
                                }
                                wp_reset_postdata();
                                if ($isMTAArchiveExists && !empty($mta_areas)) {
                                    foreach ($mta_areas as $areaID => $areaName) { 
                                        echo '<option value="'. $postID .'" type="mta_register" areaid="'.$areaID.'">MTA Register - '. $areaName .'</option>';
                                    }
                                }
                                if (getLatestBIAID($user_company_id)) {
                                    echo '<option type="report">Service Criticality Report Card</option>';
                                    echo '<option type="cloud">Cloud Service Catalogue</option>';
                                    echo '<option type="catalogue_summary">Catalogue Summary</option>';
                                }
                            } ?>
                        </select>
                    </div>
                </div>
                <div id="ajax-scorecard-data"> </div>
            </div>
        </div>
    </div>
</div>