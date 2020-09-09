<?php
get_header();
$opts = get_post_meta(get_the_ID(), 'form_opts', true);
$area_meta = advisory_area_exists(get_the_ID(), advisory_id_from_string($_GET['area']));
$area_id = 'sections_' . advisory_id_from_string($_GET['area']);
$transient_post_id = get_the_ID();
// SET DEFAULT VALUES
if (!isset($_GET['edit'])) $_GET['edit'] = true;
if (!isset($_GET['number'])) $_GET['number'] = 1;
if (!isset($_GET['section'])) $_GET['section'] = advisory_id_from_string($opts[$area_id][1]['name']);

if (isset($_GET['view']) && $_GET['view'] == 'true') {
    $select_attr = 'disabled';
    $publish_btn = true;
} elseif (isset($_GET['edit']) && $_GET['edit'] == 'true') {
    $select_attr = '';
    $publish_btn = true;
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
    <div class="row">
        <div class="card">
            <div class="landing-wrapper">
                <div class="icon" style="display: inline-block;">
                    <a href="<?php echo site_url('dm-maturity');?>">
                        <img src="<?php echo IMAGE_DIR_URL; ?>icon-dm.png" alt="assessment dmm">
                    </a>
                </div>
                <div class="card-footer text-right" style="width: 300px; display: inline-block;position: relative;top: 25px;left: 50px;">
                    <button style="height: 60px;margin-top: -4px;border-radius: 0;" class="btn btn-dark">Total<br>Rating</button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="inlineSelect">
                        <?php $rationgOptions = [0=>'Rating',5=>'Initial',4=>'Managed',3=>'Defined',2=>'Measured',1=>'Optimizing'];
                        echo advisory_opt_select('avg', '', 'dmm_avg', '', $rationgOptions, 0); ?>
                    </div>
                </div>
                <div class="landing-list">
                    <?php if ($opts[$area_id]) {
                        echo '<table class="table table-bordered landing">';
                            echo '<tbody>';
                            echo '<div class="nav nav-tabs" role="tablist">';
                                foreach ($opts[$area_id] as $key => $navArea) {
                                    $navID = advisory_id_from_string($navArea['name']);
                                    $activeItem = $navID == $_GET['section'] ? ' active' : '';
                                    echo '<tr>';
                                        if ($key == 1) {
                                            echo '<th class="areaBg_'. $_GET['number'] .'" rowspan="3"><h2>'. $area_meta['name'] .'</h2> <span class="badge">'. $_GET['number'] .'</span></th>';
                                        }
                                        echo '<td class="single-subItem equalfont single-subItem'. $_GET['number'] . $activeItem .'"><a href="#tab_'. $navID .'" aria-controls="tab_'. $navID .'" role="tab" data-toggle="tab"><span class="fa fa-circle"></span> '. $navArea['name'] .' </a></td>';
                                    echo '</tr>';
                                }
                            echo '</div>';
                            echo '</tbody>';
                        echo '</table>';
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($opts[$area_id])) { ?>
        <?php echo '<div class="tab-content">';
            foreach ($opts[$area_id] as $areaIndex => $section) {
                $sectionID = advisory_id_from_string($section['name']);
                $activeItem = $sectionID == $_GET['section'] ? ' active' : '';
                $section_id = $area_id . '_tables_' . $sectionID;
                $default = advisory_form_default_values($transient_post_id, $section_id);
                $fields = $section['fields'];
                echo '<div role="tabpanel" class="tab-pane '. $activeItem .'" id="'."tab_". $sectionID .'">'; 
                echo '<form class="survey-form single" method="post" data-meta="'. $section_id .'" data-id="'. $transient_post_id .'">';
                echo '<input type="hidden" name="avg" id="'. $sectionID .'" value="'. @$default['avg'] .'">';
                echo '<div class="row">';
                    echo '<div class="col-md-8">';
                        echo '<div class="dmmDetail">';
                            echo '<img src="'. IMAGE_DIR_URL .'dmm/area/'. $sectionID .'.jpg" alt="assessment dmm">';
                            echo '<div class="popUpImageLink" popImg="'. IMAGE_DIR_URL .'dmm/pdf/'. $sectionID .'.pdf"></div>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="col-md-4">';
                        echo '<table class="table table-bordered landing">';
                            echo '<tbody>';
                                foreach ($opts['areas'] as $areaSI => $area) {
                                    $areaID = advisory_id_from_string($area['name']);
                                    $subSectionID = 'sections_'. $areaID;
                                    if ($opts[$subSectionID]) {
                                        foreach ($opts[$subSectionID] as $subFieldID => $subField) {
                                            $subFieldLink = site_url('dmm/'. get_the_ID()) .'/?edit=true&area='. $areaID .'&number='. $areaSI .'&section='. advisory_id_from_string($subField['name']);
                                            echo '<tr>';
                                                if ($subFieldID == 1) echo '<th class="text-uppercase text-center areaBg_'. $areaSI .'" rowspan="3"> '.'<a href="'. $subFieldLink .'"><h3>'. $area['name'] .'</h3></a>'.' <span class="badge">'. $areaSI .'</span></th>';
                                            echo '</tr>';
                                        }
                                    }
                                }
                            echo '</tbody>';
                        echo '</table>';
                    echo '</div>';
                echo '</div>';
                echo '<div class="row">
                    <div class="col-md-12">
                            <div class="card">
                                <div class="card-title-w-btn">
                                    <h4 class="title">' . $section['name'] . '</h4>
                                    <div class="btn-group">';
                                        if ($select_attr == '') {
                                            echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                        }
                                    echo '</div>
                                </div>
                                <div class="card-body">';
                                    if($section['desc']) { echo '<p>' . $section['desc'] . '</p>'; }
                                    $tables = isset($opts[$section_id]) ? $opts[$section_id] : array();
                                    if (!empty($tables)) {
                                        foreach ($tables as $table) {
                                            $table_id = $section_id . '_questions_' . advisory_id_from_string($table['name']);
                                            $count = 0;
                                            echo '<div class="table-responsive">';
                                            echo '<table class="table table-bordered table-bordered-2x table-survey table-single-criteria table-single-dmm stab equalfont">
                                                <thead>
                                                    <tr>
                                                        <th class="t-heading-dark equalfont"><strong>Question</strong></th>';
                                                        $total = 0;
                                                        $max = 0;
                                                        $questions = $opts[$table_id];
                                                        $max += @count($questions) * 5;
                                                        echo '<th class="t-heading-dark equalfont"><strong>Response</strong></th>';
                                                    echo '</tr>
                                                </thead>
                                                <tbody>';
                                                    if ($questions) {
                                                        foreach ($questions as $question) {
                                                            $question_id = $table_id . '_question_' . advisory_id_from_string($question['title']);
                                                            $title = trim($question['title']);
                                                            $desc = trim($question['desc']);
                                                            $count++;
                                                            echo '<tr>
                                                                <td width="40%">' . $count . '. ';
                                                                    if (empty($desc)) {
                                                                        echo $title;
                                                                    } else {
                                                                        echo '<strong>' . $title . '</strong> <i>('. $desc .')</i>';
                                                                    }
                                                                echo '</td>';
                                                                echo '<td class="no-padding"><textarea name="'. $table_id . '_' . $question_id .'_comment" class="form-control" row="4">'. @$default["{$table_id}_{$question_id}_comment"] .'</textarea></td>';
                                                            echo '</tr>';
                                                        }
                                                    }
                                                echo '</tbody>
                                            </table>';
                                            echo '</div>';
                                        }
                                    }
                                echo '</div>
                                    
                            </div>
                    </div>
                </div>';
                echo '</div>';
                echo '</form>';
            }
        echo '</div>';
    }
echo '</div>';
// Modal
echo '<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="helpModal">Criteria Rating Definitions</h4>
            </div>
            <div class="modal-body" id="popUpModal">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>'; ?>
<script>
jQuery(function($) {
    loadInitialAVG()
    // rr on change
    jQuery('.dmm_avg').on('change', function(e) {
        var value = Number(jQuery(this).val())
        var inputID = '#'+ $('.tab-pane.active').attr('id').replace('tab_', '')
        jQuery(inputID).val(value)
        jQuery('.inlineSelect').removeClass().addClass('inlineSelect '+ dmmColorAVG(value))
    })
    jQuery(document).on('click', '.single-subItem', function() {
        loadInitialAVG();
        jQuery('.single-subItem').removeClass('active');
        jQuery(this).addClass('active');
    })
    jQuery(document).on('click', '.popUpImageLink', function() {
        var popImg = jQuery(this).attr('popImg');
        window.open(this.href=popImg, '_blank');
        //window.location.href=popImg;
        //jQuery('#popUpModal').html('<img src="'+ popImg +'" alt="help modal" width="100%">');
        //jQuery('#helpModal').modal('show');
    })
    function dmmColorAVG(value) {
        var color
        if (value == 5) color = 'color-one';
        else if (value == 4) color = 'color-two';
        else if (value == 3) color = 'color-three';
        else if (value == 2) color = 'color-four';
        else if (value == 1) color = 'color-five';
        else color = '';
        return color;
    }
    function loadInitialAVG() {
        var initialID = '#'+ $('.tab-pane.active').attr('id').replace('tab_', '')
        var avg =jQuery(initialID).val()
        $('.dmm_avg > option').each(function(){
            if($(this).val()==avg) $(this).attr('selected', true)
        })
        jQuery('.inlineSelect').removeClass().addClass('inlineSelect '+ dmmColorAVG(avg))
    }
})
</script>
<?php get_footer(); ?>