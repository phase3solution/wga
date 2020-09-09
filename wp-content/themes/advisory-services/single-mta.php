<?php
get_header();
$opts = get_post_meta(get_the_ID(), 'form_opts', true);
$area_meta = advisory_area_exists(get_the_ID(), advisory_id_from_string($_GET['area']));
$area_id = 'sections_' . advisory_id_from_string($_GET['area']);
$transient_post_id = get_the_ID();
// SET DEFAULT VALUES
if (get_the_author_meta( 'spuser', get_current_user_id())) {
    $_GET['edit'] = true;
    $_GET['view'] = false;
}
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
    $publish_btn = 1;
}
?>
<div class="content-wrapper">
    <div class="page-title">
        <?php 
            echo '<div></div>';
            if ($opts['areas']) {
                echo '<div><ul class="list-inline list-unstyled ihcLinks">';
                    foreach ($opts['areas'] as $areaSI => $area) {
                        // $menuIcon = $area['icon_menu'] ?? IMAGE_DIR_URL .'ihc/' . $areaID .'.jpg';
                        $areaID = advisory_id_from_string($area['name']);
                        $menuIcon = IMAGE_DIR_URL .'mta/icon-' . $areaID .'.png';
                        $viewLink = isset($_GET['view']) && $_GET['view'] != 'true' ? 'view=true&' : '';
                        $currentAreaID = advisory_id_from_string($_GET['area']);
                        if ($currentAreaID != $areaID) {
                            $aleaLink = site_url('mta/'. get_the_ID()) .'/?'.$viewLink.'area='. $areaID;
                            echo '<li> '.'<a href="'. $aleaLink .'"><img src="'. $menuIcon .'"></a></li>';
                        }
                    }
                echo '</ul></div>';
            }
         ?>
        <?php if ($select_attr == '') {
            echo '<div>';
                if($publish_btn == true) {
                    if (advisory_is_valid_form_submission(get_the_ID())) echo '<a class="btn btn-lg btn-info btn-publish" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                    else echo '<a class="btn btn-lg btn-default btn-publish" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                } 
                echo '<a class="btn btn-lg btn-warning btn-reset-all" href="#">Reset</a>';
                // echo '<a class="btn btn-lg btn-success btn-save-all" href="#">Save All</a>';
            echo '</div>';
        } ?>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="#"><?php echo advisory_get_form_name(get_the_ID()) ?></a></li>
                <li><a href="#"><?php echo $area_meta['name'] ?></a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <img style="width: 100%; margin-bottom: 10px;" src="<?php echo IMAGE_DIR_URL; ?>mta/bg-<?php echo advisory_id_from_string($_GET['area']); ?>.png" alt="Rating Scale">
            <div class="row sectionAVGWrapper">
                <div class="col-sm-6 sectionAVG"> <div class="color-zero">Rating</div> </div>
                <div class="col-sm-6"><h3><?php // echo $area_meta['name']; ?></h3></div>
            </div>
        </div>
        <div class="col-sm-9 text-right"> <div class="scale-img"><img height="100" src="<?php echo IMAGE_DIR_URL; ?>mta/rating-scale.png" alt="Rating Scale"></div> </div>
    </div>
    <br>

    <?php if (!empty($opts[$area_id])) { ?>
        <?php echo '<div class="row">';
            foreach ($opts[$area_id] as $areaIndex => $section) {
                $tableTotal = $tableCount = 0;
                $sectionID = advisory_id_from_string($section['name']);
                $section_id = $area_id . '_tables_' . $sectionID;
                $default = advisory_form_default_values($transient_post_id, $section_id);
                $fields = $section['fields'] ?? false;
                $secDesc = @$section['desc'] ? '<br><span style="font-size:14px;font-weight:400;">' . $section['desc'] . '</span>' : '';
                echo '<div class="col-sm-12">';
                echo '<form class="survey-form single" method="post" data-meta="'. $section_id .'" data-id="'. $transient_post_id .'">';
                echo '<div class="row">
                    <div class="col-md-12">
                            <div class="card">
                                <div class="card-title-w-btn t-heading-sky no-margin">
                                    <h4 class="title">' . $section['name'] . $secDesc .'</h4>';
                                    echo '<div class="btn-group">';
                                        echo '<button class="btn btn-default btnSecAVG" type="button">AVG : <span>0</span></button>';
                                        if (!empty($opts['criteria_definition'])) echo '<a class="btn btn-info" data-toggle="modal" data-target="#helpModal">Criteria Rating Definitions</a>';
                                        if ($section['docs']) echo '<a class="btn btn-warning" target="_blank" href="' . $section['docs'] . '">Documentation</a>';
                                        if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                    echo '</div>
                                </div>
                                <div class="card-body">';
                                    $tables = isset($opts[$section_id]) ? $opts[$section_id] : array();
                                    if (!empty($tables)) {
                                        $defaultAVG = !empty($default['avg']) ? $default['avg'] : 0;
                                        echo '<input type="hidden" name="avg" value="'. $defaultAVG .'" class="ihcAVG">';
                                        // echo '<br><pre>'. print_r($tables, true) .'</pre>';
                                        foreach ($tables as $table) {
                                            $table_id = $section_id . '_questions_' . advisory_id_from_string($table['name']);
                                            $count = 0;
                                            $total = 0;
                                            $max = 0;
                                            $questions = $opts[$table_id] ?? [];
                                            $max += @count($questions) * 5;
                                            $subRatingID = $table_id .'_avg';
                                            $defaultSubRating = $default[$subRatingID] ?? 0;
                                            $tableDesc = !empty($table['desc']) ? '<br><p style="font-size: 14px;line-height: 1;color: #fff;">'. $table['desc'] .'</p>' : '';
                                            // echo '<br><pre>'. print_r([0,1,2,3,4,5,6,7,8,9,10, 'g'=>'G'], true) .'</pre>';
                                            echo '<div class="equalfont t-heading-dark">';
                                                echo '<table class="table">';
                                                    echo '<tr>';
                                                        echo '<td style="width: 49%;">'. $table['name'] .$tableDesc. '</td>';
                                                        echo '<td style="width: 56px;padding:8px 0;" class="select-color '. ihcColorAVG($defaultSubRating) .'"> '. advisory_opt_select($subRatingID, $subRatingID, 'ihcSubAVG', '', [0,1,2,3,4,5,6,7,8,9,10, 'g'=>'G'], $defaultSubRating); ' </td>';
                                                        echo '<td class="">Response</td>';
                                                    echo '</td>';
                                        
                                                echo '</table>';
                                            echo '</div>';
                                            echo '<div class="equalfontIHC">';
                                                if ($questions) {
                                                    foreach ($questions as $question) {
                                                        $question_id = $table_id . '_question_' . advisory_id_from_string($question['title']);
                                                        $title = trim($question['title']);
                                                        $desc = trim($question['desc']);
                                                        $count++;
                                                        echo '<div class="bordered">';
                                                        echo '<div class="col-xs-6">' . $count . '. ';
                                                                if (empty($desc)) { echo $title; } 
                                                                else { echo '<strong>' . $title . '</strong> <i>('. $desc .')</i>'; }
                                                            echo '</div>';
                                                            echo '<div class="col-xs-6"><textarea name="'. $table_id . '_' . $question_id .'_comment" class="form-control no-border" rows="4" '. $select_attr .'>'. @$default["{$table_id}_{$question_id}_comment"] .'</textarea></div>';
                                                        echo '<div class="clearfix"></div>';
                                                        echo '</div>';
                                                    }
                                                }
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
if (!empty($opts['criteria_definition'])) {
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
                        '. cs_get_option('criteria_rating_definitions') .'
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>';
}
?>
<script>
    var sectionAVG = function() {
        var avg = getSectionAVG();
        var avgData = getSectionData(avg);
        jQuery('.sectionAVG > div').removeClass().addClass(avgData.cls).text(avgData.txt);
        // console.log([avg, avgData]);
    }
    var getSectionData = function(val) {
        var data = {}
        if (val == 'g')   { data.cls = 'color-gap';    data.txt = 'GAP'; }
        else if(val < 3)   { data.cls = 'color-one-n';    data.txt = '';      }
        else if(val < 5)   { data.cls = 'color-two-n';    data.txt = '';      }
        else if(val < 7)   { data.cls = 'color-three-n';  data.txt = '';      }
        else if(val < 9)   { data.cls = 'color-four-n';   data.txt = '';      }
        else if(val < 11)  { data.cls = 'color-five-n';   data.txt = '';      }
        else               { data.cls = 'color-one-n';    data.txt = '';      }
        return data;
    }
    var getSectionAVG = function() {
        var avg = 0; total = 0; count = 0;
        jQuery('.ihcSubAVG').each(function() {
            var value = jQuery(this).val();
            if (value != 'g') {
                total += parseInt(value);
                count++
            } 
        })
        if (count) avg = total / count;
        else avg = 'g';
        return avg
    }
    var ihcColorAVG = function(value) {
        var cls = ''
        if (value == 'g') cls = 'color-gap';
        else {
            value = parseFloat(value).toFixed(1) * 10
            switch(parseInt(value)){
                case 1:  cls = 'color-1';     break;
                case 2:  cls = 'color-2';     break;
                case 3:  cls = 'color-3';     break;
                case 4:  cls = 'color-4';     break;
                case 5:  cls = 'color-5';     break;
                case 6:  cls = 'color-6';     break;
                case 7:  cls = 'color-7';     break;
                case 8:  cls = 'color-8';     break;
                case 9:  cls = 'color-9';     break;
                case 10:  cls = 'color-10';   break;
                case 11:  cls = 'color-11';   break;
                case 12:  cls = 'color-12';   break;
                case 13:  cls = 'color-13';   break;
                case 14:  cls = 'color-14';   break;
                case 15:  cls = 'color-15';   break;
                case 16:  cls = 'color-16';   break;
                case 17:  cls = 'color-17';   break;
                case 18:  cls = 'color-18';   break;
                case 19:  cls = 'color-19';   break;
                case 20:  cls = 'color-20';   break;
                case 21:  cls = 'color-21';   break;
                case 22:  cls = 'color-22';   break;
                case 23:  cls = 'color-23';   break;
                case 24:  cls = 'color-24';   break;
                case 25:  cls = 'color-25';   break;
                case 26:  cls = 'color-26';   break;
                case 27:  cls = 'color-27';   break;
                case 28:  cls = 'color-28';   break;
                case 29:  cls = 'color-29';   break;
                case 30:  cls = 'color-30';   break;
                case 31:  cls = 'color-31';   break;
                case 32:  cls = 'color-32';   break;
                case 33:  cls = 'color-33';   break;
                case 34:  cls = 'color-34';   break;
                case 35:  cls = 'color-35';   break;
                case 36:  cls = 'color-36';   break;
                case 37:  cls = 'color-37';   break;
                case 38:  cls = 'color-38';   break;
                case 39:  cls = 'color-39';   break;
                case 40:  cls = 'color-40';   break;
                case 41:  cls = 'color-41';   break;
                case 42:  cls = 'color-42';   break;
                case 43:  cls = 'color-43';   break;
                case 44:  cls = 'color-44';   break;
                case 45:  cls = 'color-45';   break;
                case 46:  cls = 'color-46';   break;
                case 47:  cls = 'color-47';   break;
                case 48:  cls = 'color-48';   break;
                case 49:  cls = 'color-49';   break;
                case 50:  cls = 'color-50';   break;
                case 51:  cls = 'color-51';   break;
                case 52:  cls = 'color-52';   break;
                case 53:  cls = 'color-53';   break;
                case 54:  cls = 'color-54';   break;
                case 55:  cls = 'color-55';   break;
                case 56:  cls = 'color-56';   break;
                case 57:  cls = 'color-57';   break;
                case 58:  cls = 'color-58';   break;
                case 59:  cls = 'color-59';   break;
                case 60:  cls = 'color-60';   break;
                case 61:  cls = 'color-61';   break;
                case 62:  cls = 'color-62';   break;
                case 63:  cls = 'color-63';   break;
                case 64:  cls = 'color-64';   break;
                case 65:  cls = 'color-65';   break;
                case 66:  cls = 'color-66';   break;
                case 67:  cls = 'color-67';   break;
                case 68:  cls = 'color-68';   break;
                case 69:  cls = 'color-69';   break;
                case 70:  cls = 'color-70';   break;
                case 71:  cls = 'color-71';   break;
                case 72:  cls = 'color-72';   break;
                case 73:  cls = 'color-73';   break;
                case 74:  cls = 'color-74';   break;
                case 75:  cls = 'color-75';   break;
                case 76:  cls = 'color-76';   break;
                case 77:  cls = 'color-77';   break;
                case 78:  cls = 'color-78';   break;
                case 79:  cls = 'color-79';   break;
                case 80:  cls = 'color-80';   break;
                case 81:  cls = 'color-81';   break;
                case 82:  cls = 'color-82';   break;
                case 83:  cls = 'color-83';   break;
                case 84:  cls = 'color-84';   break;
                case 85:  cls = 'color-85';   break;
                case 86:  cls = 'color-86';   break;
                case 87:  cls = 'color-87';   break;
                case 88:  cls = 'color-88';   break;
                case 89:  cls = 'color-89';   break;
                case 90:  cls = 'color-90';   break;
                case 91:  cls = 'color-91';   break;
                case 92:  cls = 'color-92';   break;
                case 93:  cls = 'color-93';   break;
                case 94:  cls = 'color-94';   break;
                case 95:  cls = 'color-95';   break;
                case 96:  cls = 'color-96';   break;
                case 97:  cls = 'color-97';   break;
                case 98:  cls = 'color-98';   break;
                case 99:  cls = 'color-99';   break;
                case 100: cls = 'color-100';  break;
                default: cls = 'color-0';     break;
            }
        }
        return cls
    }
    var ihcAVG = function(element) {
        var total = count = avg = 0;
        element.parents('.card-body').find('.ihcSubAVG').each(function(){
            var value = jQuery(this).val();
            if (value != 'g') {
                total += parseInt(value)
                count++
            }
        })
        if (count) avg = total / count;
        else avg = 'g';
        element.parents('.card-body').find('.ihcAVG').val(avg)
        btnSecAVG()
    }
    var btnSecAVG = function() {
        jQuery('.ihcAVG').each(function() {
            var element = jQuery(this);
            var elemVal = element.val();
            var inputVal = elemVal != 'g' ? parseFloat(elemVal) : 0;
            var cssClass = ihcColorAVG(inputVal)
            element.parents('.card').find('.btnSecAVG span').text(inputVal.toFixed(1))
            element.parents('.card').find('.btnSecAVG').removeClass().addClass('btn btn-default btnSecAVG ' + cssClass)
        })
    }
    jQuery(function($) {
        sectionAVG()
        btnSecAVG()
        jQuery('.ihcSubAVG').on('change', function(e) {
            var value = $(this).val()
            var color = ihcColorAVG(value)
            $(this).parent().removeClass().addClass('col-sm-2 '+ color)
            sectionAVG()
            // UPDATE TABLE AVG
            ihcAVG($(this))
        })
        
    })
</script>
<?php get_footer(); ?>