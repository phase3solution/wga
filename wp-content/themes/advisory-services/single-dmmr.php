<?php
get_header();
$transient_post_id = get_the_ID();
$opts = get_post_meta($transient_post_id, 'form_opts', true);
$area_meta = advisory_area_exists($transient_post_id, advisory_id_from_string($_GET['area']));
$area_id = advisory_id_from_string($_GET['area']);
if (isset($_GET['view']) && $_GET['view'] == 'true') {
    $select_attr = 'disabled';
    $publish_btn = true;
    $prefix = 'view=true&';
} elseif (isset($_GET['edit']) && $_GET['edit'] == 'true') {
    $select_attr = '';
    $publish_btn = false;
    $prefix = 'edit=true&';
} else {
    $select_attr = '';
    $publish_btn = true;
    $prefix = '';
} ?>
<div class="content-wrapper">
    <!-- Page Title -->
    <div class="page-title">
        <div>
            <h1><?php echo (!empty($area_meta['icon_title']) ? '<img src="' . $area_meta['icon_title'] . '"> ' : '') ?><?php echo $area_meta['name'] ?></h1>
        </div>
        <?php if ($select_attr == '') { ?>
            <div>
                <?php if($publish_btn == true) {
                    if (advisory_is_valid_form_submission($transient_post_id)) {
                        echo '<a class="btn btn-lg btn-info btn-publish is-bia" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                    } else {
                        echo '<a class="btn btn-lg btn-default btn-publish is-bia" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                    }
                } ?>
                <a class="btn btn-lg btn-success btn-save-all" href="#">Save All</a>
                <a class="btn btn-lg btn-warning btn-reset-all" href="#">Reset</a>
            </div>
        <?php } ?>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="#"><?php echo advisory_get_form_name($transient_post_id) ?></a></li>
                <li><a href="#"><?php echo $area_meta['name'] ?></a></li>
            </ul>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-7">
                    <img src="<?php echo get_template_directory_uri()?>/images/project_risk_process.png" class="img-responsive">
                </div>
            </div>
        </div>
    </div>
    <?php if ($opts['areas']) {
        foreach ($opts['areas'] as $threat_cat) {
            if (@$_GET['area'] != advisory_id_from_string($threat_cat['name'])) {
                continue;
            }
            $cat_id = advisory_id_from_string($threat_cat['name']);
            $default = advisory_form_default_values($transient_post_id, $cat_id);
            echo '<form class="form survey-form" method="post" data-meta="' . $cat_id . '" data-id="'. $transient_post_id .'">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-title-w-btn">
                                <h4 class="title">Threat Category: ' . $threat_cat['name'] . '</h4>';
                                if ($select_attr == '') {
                                    echo '<button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                }
                            echo '</div>
                            <div class="card-body">
                                <textarea name="comment" id="comment" class="form-control" cols="30" rows="10" ' . $select_attr . '>' . @$default['comment'] . '</textarea>
                                <input type="hidden" name="reset" value="true">
                            </div>
                        </div>
                    </div>
                </div>
            </form>';
            if ($threats = $opts[advisory_id_from_string($threat_cat['name']) . '_threats']) {
                foreach ($threats as $threat) {
                    $threat_id = $cat_id . '_' . advisory_id_from_string($threat['name']);
                    $default = advisory_form_default_values($transient_post_id, $threat_id);
                    echo '<form class="form survey-form bia-risk" method="post" data-meta="' . $threat_id . '" data-id="'. $transient_post_id .'">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-title-w-btn">
                                        <h4 class="title">Threat: ' . @$threat['name'] . '</h4>';
                                        if ($select_attr == '') {
                                            echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                        }
                                    echo '</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <p class="text-left strong font-110p">' . @$threat['desc'] . '</p>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="total-risk text-center font-120p"><strong>Total Risk Level</strong></div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="total-risk-avg text-center font-120p"><strong>Avg: <span></span></strong></div>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="table-responsive table-risk-wrapper">
                                            <table class="table table-bordered table-survey table-bia-risk">';
                                                if ($questions = $opts[advisory_id_from_string($threat_cat['name']) . '_threat_' . advisory_id_from_string($threat['name']) . '_fields']) {
                                                    $count = 0;
                                                    //echo '<br><pre>'. print_r($questions, true) .'</pre>';
                                                    //echo '<br><pre>'. print_r($default, true) .'</pre>';
                                                    foreach ($questions as $question) {
                                                        $count++;
                                                        $q_id = $threat_id . '_q' . $count;
                                                        $commentExist = $question['field_4_id'] == 'na' ? false : true;
                                                        echo '<thead>
                                                            <tr>
                                                                <th class="t-heading-dark w-120px"><big><strong>Question ' . $count . '</big></strong></th>
                                                                <th colspan="4" class="t-heading-dark"><big><strong>' . $question['name'] . '</big></strong></th>
                                                            </tr>
                                                            <tr>';
                                                                if ($bool = in_array('bool_positive', array($question['field_1_id'], $question['field_2_id'], $question['field_3_id'])) || in_array('bool_negative', array($question['field_1_id'], $question['field_2_id'], $question['field_3_id']))) {
                                                                    echo '<th class="t-heading-sky font-110p strong">' . advisory_get_criteria_label($question['field_1_id']) . '</th>
                                                                        <th class="t-heading-sky font-110p strong width-100px">Risk</th>';
                                                                    if ($commentExist) {
                                                                    echo '<th class="t-heading-sky font-110p strong width-100px">' . advisory_get_criteria_label($question['field_4_id']) .'</th>';
                                                                    }
                                                                    echo '<th class="no-border no-bg"></th>
                                                                        <th class="no-border no-bg"></th>';
                                                                } else {
                                                                    echo '<th class="t-heading-sky font-110p strong">' . advisory_get_criteria_label($question['field_1_id']) . '</th>
                                                                    <th class="t-heading-sky font-110p strong">' . advisory_get_criteria_label($question['field_2_id']) . '</th>
                                                                    <th class="t-heading-sky font-110p strong">' . advisory_get_criteria_label($question['field_3_id']) .'</th>
                                                                    <th class="t-heading-sky font-110p strong">Risk</th>';
                                                                    if ($commentExist) {
                                                                    echo '<th class="t-heading-sky font-110p strong">' . advisory_get_criteria_label($question['field_4_id']) .'</th>';
                                                                    }
                                                                }
                                                            echo '</tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>';
                                                                if ($bool) {
                                                                    echo '<td class="no-padding color-zero" id="bool">
                                                                        ' . advisory_opt_select($q_id . '_' . $question['field_1_id'], '', '', $select_attr, $question['field_1_id'], @$default[$q_id . '_' . $question['field_1_id']]) . '
                                                                    </td>
                                                                    <td class="text-center"><span class="risk">0</span></td>';
                                                                    if ($commentExist) {
                                                                    echo '<td class="text-center no-padding"><textarea class="no-border bigComment" name="'. $q_id . '_' . $question['field_4_id'] .'">'. @$default[$q_id . '_' . $question['field_4_id']] .'</textarea></td>';
                                                                    }
                                                                    echo '<td class="no-border"></td>
                                                                        <td class="no-border"></td>';
                                                                } else {
                                                                    echo '<td class="no-padding color-zero">
                                                                        ' . advisory_opt_select($q_id . '_' . $question['field_1_id'], '', '', $select_attr, $question['field_1_id'], @$default[$q_id . '_' . $question['field_1_id']]) . '
                                                                    </td>
                                                                    <td class="no-padding color-zero">
                                                                        ' . advisory_opt_select($q_id . '_' . $question['field_2_id'], '', '', $select_attr, $question['field_2_id'], @$default[$q_id . '_' . $question['field_2_id']]) . '
                                                                    </td>
                                                                    <td class="no-padding color-zero">
                                                                        ' . advisory_opt_select($q_id . '_' . $question['field_3_id'], '', '', $select_attr, $question['field_3_id'], @$default[$q_id . '_' . $question['field_3_id']]) . '
                                                                    </td>
                                                                    <td class="text-center"><span class="risk">0</span></td>';
                                                                    if ($commentExist) {
                                                                    echo '<td class="text-center no-padding"><textarea class="no-border bigComment" name="'. $q_id . '_' . $question['field_4_id'] .'">'. @$default[$q_id . '_' . $question['field_4_id']] .'</textarea></td>';
                                                                    }
                                                                }
                                                            echo '</tr>
                                                        </tbody>';
                                                    }
                                                }
                                            echo '<input type="hidden" name="nq" value="' . $count . '"></table>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">';
                                        if ($select_attr == '') {
                                            echo '<input type="hidden" name="avg" class="hidden-avg" value="0"><button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                        }
                                    echo '</div>
                                </div>
                            </div>
                        </div>
                    </form>';
                }
            }
        }
    }
echo '</div>';
get_footer(); ?>