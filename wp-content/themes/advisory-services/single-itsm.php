<?php
get_header();
$opts = get_post_meta(get_the_ID(), 'form_opts', true);
$area_meta = advisory_area_exists(get_the_ID(), advisory_id_from_string($_GET['area']));
$area_id = 'sections_' . advisory_id_from_string($_GET['area']);
$transient_post_id = get_the_ID();
if (isset($_GET['view']) && $_GET['view'] == 'true') {
    $select_attr = '';
    $publish_btn = false;
} elseif (isset($_GET['edit']) && $_GET['edit'] == 'true') {
    $select_attr = '';
    $publish_btn = false;
} else {
    $select_attr = '';
    $publish_btn = true;
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
                        if (advisory_is_valid_form_submission(get_the_ID())) {
                            echo '<a class="btn btn-lg btn-info btn-publish" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                        } else {
                            echo '<a class="btn btn-lg btn-default btn-publish" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                        }
                    } ?>
                    <a class="btn btn-lg btn-success btn-save-all" href="#">Save All</a>
                    <a class="btn btn-lg btn-warning btn-reset-all" href="#">Reset</a>
                </div>
            <?php } ?>
            <div>
                <ul class="breadcrumb">
                    <li><i class="fa fa-home fa-lg"></i></li>
                    <li><a href="#"><?php echo advisory_get_form_name(get_the_ID()) ?></a></li>
                    <li><a href="#"><?php echo $area_meta['name'] ?></a></li>
                </ul>
            </div>
        </div>
        <?php if (!empty($opts[$area_id])) {
            foreach ($opts[$area_id] as $section) {
                $section_id = $area_id . '_tables_' . advisory_id_from_string($section['name']);
                $default = advisory_form_default_values($transient_post_id, $section_id);
                $fields = $section['fields'];
                echo '<div class="row">
                    <div class="col-md-12">
                        <form class="survey-form single" method="post" data-meta="'. $section_id .'" data-id="'. $transient_post_id .'">
                            <div class="card">
                                <div class="card-title-w-btn">
                                    <h4 class="title">' . $section['name'] . '</h4>
                                    <div class="btn-group">';
                                        if (!empty($opts['criteria_definition'])) {
                                            echo '<a class="btn btn-info" data-toggle="modal" data-target="#helpModal">Criteria Rating Definitions</a>';
                                        }
                                        if (!empty($section['docs'])) {
                                            echo '<a class="btn btn-warning" target="_blank" href="' . $section['docs'] . '">Documentation</a>';
                                        }
                                        if ($select_attr == '') {
                                            echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                        }
                                    echo '</div>
                                </div>
                                <div class="card-body">';
                                    if($section['desc']) {
                                        echo '<p>' . $section['desc'] . '</p>';
                                    }
                                    $tables = isset($opts[$section_id]) ? $opts[$section_id] : array();
                                    if (!empty($tables)) {
                                        foreach ($tables as $table) {
                                            $table_id = $section_id . '_questions_' . advisory_id_from_string($table['name']);
                                            echo '<div class="table-responsive">';
                                            $count = 0;
                                            echo '<table class="table table-bordered table-survey table-single-criteria">
                                                <thead>
                                                    <tr>
                                                        <th class="t-heading-dark text-center"><big><strong>Topic Area</strong></big></th>';
                                                        $total = 0;
                                                        $max = 0;
                                                        $questions = $opts[$table_id];
                                                        $max += count($questions) * 5;
                                                        foreach ($questions as $question) {
                                                            $question_id = $table_id . '_question_' . advisory_id_from_string($question['title']);
                                                            foreach ($fields as $f_key => $field) {
                                                                $field_id = $question_id . '_' . $field;
                                                                $total += (isset($default[$field_id]) ? $default[$field_id] : 0);
                                                            }
                                                        }
                                                        echo '<th class="t-heading-dark text-center"><strong>Rating</strong><br>(<span class="total">'. $total .'</span> out of ' . $max . ' / Avg: <span class="avg">'. @$default['avg'] .'</span>)</th>
                                                        <th class="t-heading-dark text-center"><big><strong>Comments</strong></big></th>';
                                                    echo '</tr>
                                                </thead>
                                                <tbody>';
                                                    foreach ($questions as $question) {
                                                        $question_id = $table_id . '_question_' . advisory_id_from_string($question['title']);
                                                        $title = trim($question['title']);
                                                        $desc = trim($question['desc']);
                                                        $count++;
                                                        echo '<tr>
                                                            <td width="60%">' . $count . '. ';
                                                                if (empty($desc)) {
                                                                    echo $title;
                                                                } else {
                                                                    echo '<strong>' . $title . '</strong> <i>('. $desc .')</i>';
                                                                }
                                                            echo '</td>';
                                                            foreach ($fields as $f_key => $field) {
                                                                $field_id = $question_id . '_' . $field;
                                                                echo '<td width="15%">' . advisory_opt_select($field_id, '', '', $select_attr, $field, @$default[$field_id]) .'<input type="hidden" class="rating" value="' . (isset($default[$field_id]) ? $default[$field_id] : 0) . '"></td>';
                                                            }
                                                            echo '<td class="no-padding"><textarea name="'. $table_id . '_' . $question_id .'_comment" class="form-control">'. @$default["{$table_id}_{$question_id}_comment"] .'</textarea></td>';
                                                        echo '</tr>';
                                                    }
                                                echo '</tbody>
                                            </table>';
                                            echo '</div>';
                                        }
                                    }
                                echo '</div>
                                <div class="card-footer text-right">
                                    <input type="hidden" name="avg" class="hidden-avg" value="' .  @$default['avg'] . '">';
                                    if ($select_attr == '') {
                                        echo '<button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                    }
                                echo '</div>
                            </div>
                        </form>
                    </div>
                </div>';
            }
        }
    echo '</div>';
    // Modal
    if ($opts['criteria_definition']) {
        echo '<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="helpModal">Criteria Rating Definitions</h4>
                    </div>
                    <div class="modal-body">
                        <div class="criteria-rating">
                            <div class="criteria-heading">
                                <h4>Criteria Rating Definitions</h4>
                            </div>
                            ' . cs_get_option('criteria_rating_definitions') . '
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>';
    }
get_footer(); ?>