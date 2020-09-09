<?php
get_header();
global $user_switching;
$oldUserID = $user_switching->get_old_user()->ID;
$transient_post_id = get_the_ID();
$opts = get_post_meta($transient_post_id, 'form_opts', true);
$area_meta = advisory_csa_area_exists($opts, advisory_id_from_string($_GET['area']));
$area_id = advisory_id_from_string($_GET['area']);

if (isset($_GET['edit']) && $_GET['edit'] == 'true') {
    $select_attr = '';
    $publish_btn = false;
    $prefix = 'edit=true&';
} else if (get_the_author_meta( 'spuser', get_current_user_id())) {
    $select_attr = '';
    $publish_btn = false;
    $prefix = 'edit=true&';
    $oldUserID = 2;
} else if (isset($_GET['view']) && $_GET['view'] == 'true') {
    $select_attr = 'disabled';
    $publish_btn = true;
    $prefix = 'view=true&';
} else {
    $select_attr = '';
    $publish_btn = true;
    $prefix = '';
} 
$icon = !empty($opts['icon']) ? '<img style="max-height:50px;" src="' . $opts['icon'] . '">' : '<img style="max-height:50px;" src="'. IMAGE_DIR_URL .'/icon-itcl.png">';
$editor =[
    'wpautop' => false,
    'media_buttons' => 0,
    'textarea_name' => '', 
    'textarea_rows' =>get_option('default_post_edit_rows', 2), 
    'tabindex' => '',
    'editor_css' => '<h4 class="title">Assessment Title : </h4>',
    'editor_class' => 'form-control',
    'dfw' => 0,
    'tinymce' => 1,
    'quicktags' => 0
];
?>
    <div class="content-wrapper">
        <!-- Page Title -->
        <div class="page-title">
            <div> <h1><?php echo $icon.'<span class="text-uppercase">'.$area_meta['name'] .'</span> : '. $area_meta['title'] ?></h1> </div>
            <div>
                <?php if($publish_btn == true) {
                    if (advisory_is_valid_form_submission($transient_post_id)) {
                        echo '<a class="btn btn-lg btn-info btn-publish is-bia" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                    } else {
                        echo '<a class="btn btn-lg btn-default btn-publish is-bia" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                    }
                } ?>
                <a class="btn btn-lg btn-success btn-save-all" href="#">Save All</a>
                <a class="btn btn-lg btn-warning btn-reset-all" area="<?php echo $area_id; ?>" href="#">Reset</a>
            </div>
            <div>
                <ul class="breadcrumb">
                    <li><i class="fa fa-home fa-lg"></i></li>
                    <li><a href="#"><?php echo advisory_get_form_name($transient_post_id) ?></a></li>
                    <li><a href="#"><?php echo $area_meta['name'] ?></a></li>
                </ul>
            </div>
        </div>
        <?php 
        if ($area_id == 'overview') {
            echo '<div class="card"><div class="row"><div class="col-sm-9"><img src="'.IMAGE_DIR_URL.'csa/overview.jpg" alt="'.$area_meta['name'].'" class="img-responsive"></div><div class="col-sm-3"></div></div></div>';
            $intro = advisory_form_default_values($transient_post_id, 'intro');
            echo '<form class="form survey-form" method="post" data-meta="' . 'intro" data-id="'. $transient_post_id .'">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-title-w-btn">
                                    <h4 class="title">Report Title</h4>';
                                    if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                echo '</div>';
                                echo '<div class="card-body">';
                                    if ($select_attr) {
                                        echo '<div class="form-group"> <h4 class="title">RISK Title : </h4> <textarea class="form-control font-130p texarea" name="risk" id="risk" cols="30" rows="2" ' . $select_attr . '>'. @$intro['risk'] .'</textarea> </div>';
                                    } else {
                                        $editor_id = 'risk';
                                        $content = @$intro[$editor_id];
                                        $editor['textarea_name'] = $editor_id;
                                        $editor['editor_css'] = '<h4 class="title">RISK Title : </h4>';
                                        wp_editor( $content, $editor_id, $editor);
                                    }
                                    echo '<br>';
                                    if ($select_attr) {
                                        echo '<div class="form-group"> <h4 class="title">CYBERSECURITY Title : </h4> <textarea class="form-control font-130p" name="cyberc" id="cyberc" cols="30" rows="2" ' . $select_attr . '>'. @$intro['cyberc'] .'</textarea> </div>';
                                    } else {
                                        $editor_id = 'cyberc';
                                        $content = @$intro[$editor_id];
                                        $editor['textarea_name'] = $editor_id;
                                        $editor['editor_css'] = '<h4 class="title">CYBERSECURITY Title : </h4>';
                                        wp_editor( $content, $editor_id, $editor);
                                    }
                                    
                                echo '</div>
                            </div>
                        </div>
                    </div>
                </form>';
        } else if ($opts['sections']) {
            foreach ($opts['sections'] as $section) {
                $sectionID = advisory_id_from_string($section['name']).'_section';
                if ('risk' == advisory_id_from_string($section['name']) && !empty($opts[$sectionID])) {
                    foreach ($opts[$sectionID] as $category) {
                        if (@$_GET['area'] != advisory_id_from_string($category['name'])) continue;
                        $categoryID = advisory_id_from_string($category['name']) . '_domains';
                        $categoryImg = advisory_id_from_string($category['name']);
                        $department_id = $sectionID.'_'.$categoryID;
                        $contents = advisory_generate_csa_form_risk_contents(advisory_id_from_string($category['name']));
                        
                        echo '<div class="row">';
                            echo '<div class="col-md-6"><div class="card"><img src="'. IMAGE_DIR_URL .'csa/'. $categoryImg .'.jpg" alt="category heading"></div></div>';
                            echo '<div class="col-md-6"><div class="card"><img src="'. IMAGE_DIR_URL .'csa/category_rating.jpg" alt="category rating"></div></div>';
                        echo '</div>';
                        $default = advisory_form_default_values($transient_post_id, $department_id.'_csa');
                        echo '<form class="form survey-form" method="post" data-meta="'.$department_id.'_csa" data-id="'. $transient_post_id .'">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-title-w-btn">
                                            <h4 class="title"></h4>';
                                            if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                        echo '</div>';
                                        echo '<style>';
                                            echo 'input {width: 100%; display:block; font-size:16px !important;font-weight:normal !important;}';
                                            // echo 'input, input:focus, input:active {border:none !important;outline:none !important;}';
                                            echo '.csaForm .main-heading {background: #000;color: #fff;padding: 5px 10px;font-size: 20px !important;font-weight: 700 !important;}';
                                            echo '.csaForm .table {margin-bottom: 0 !important;}';
                                            echo '.csaForm .table-bordered>tbody>tr>td,.csaForm .table-bordered>tbody>tr>th {border: 1px solid #000;padding: 4px;}';
                                            echo '.csaForm .center {text-align:center;}';
                                            echo '.csaForm .strong {font-weight:bold;}';
                                            echo '.csaForm .black {background: #000; color: #fff;}';
                                            echo '.csaForm .red {background: #e40613;}';
                                            echo '.csaForm .orange {background: #ea4e1b;}';
                                            echo '.csaForm .yellow {background: #fdea11;}';
                                            echo '.csaForm .green {background: #3baa34;}';
                                            echo '.csaForm .aqua {background: #36a9e0;}';
                                            echo '.csaForm .table .gray {background: #d9d9d9; font-size: 18px;color: #000;padding: 10px 5px;}';
                                            echo '.csaForm .table .sub-heading th, ';
                                            echo '.csaForm .table .sub-heading td {font-size: 18px;}';
                                            echo '.csaForm .table .modalCSA {cursor:pointer;}';
                                            echo '.csaForm .modalCSA_1.active {background: rgba(54,169,224, 0.3);}';
                                            echo '.csaForm .modalCSA_2.active {background: rgba(59,170,52, 0.3);}';
                                            echo '.csaForm .modalCSA_3.active {background: rgba(253,234,17, 0.3);}';
                                            echo '.csaForm .modalCSA_4.active {background: rgba(234,78,27, 0.3);}';
                                            echo '.csaForm .modalCSA_5.active {background: rgba(228,6,19, 0.3);}';
                                            echo '.modal-header {background-color: #333;}';
                                            echo '.modal-title {color:#fff;}';
                                            echo '.modal-body p {font-weight:500; font-size: 18px;color: #333;}';
                                        echo '</style>';
                                        
                                        echo '<div class="csaForm" type="rate">';
                                            echo '<table class="table table-bordered">';
                                                // echo '<tr><th class="black" colspan="100" style="font-size: 21px;">'. $category['name'] .'</th></tr>';
                                                echo '<tr>';
                                                    echo '<th class="black" colspan="100" style="font-size: 21px;"><span class="text-uppercase">'. $category['name'] .'</span> <div class="pull-right"><ul class="list-inline" style="margin-bottom:0;"><li>Total Rating: </li><li id="rate" class="'.@$default['cls'].'" style="width:50px;color:transparent;">lore<input type="hidden" name="cls" id="cls" value="'.@$default['cls'].'"><input type="hidden" name="avg" id="avg" value="'.@$default['avg'].'"></li></div></th>';
                                                echo '</tr>';
                                                echo '<tr class="sub-heading">';
                                                    echo '<td class="black" colspan="23" style="width:23%;font-size: 21px;">'. $category['title'] .'</td>';
                                                    echo '<th class="center aqua">Least</th>';
                                                    echo '<th class="center green">Minimal</th>';
                                                    echo '<th class="center yellow">Moderate</th>';
                                                    echo '<th class="center orange">Significant</th>';
                                                    echo '<th class="center red">Most</th>';
                                                echo '</tr>';
                                                if ($opts[$categoryID]) {
                                                    $areaSI = 1;
                                                    foreach ($opts[$categoryID] as $area) {
                                                        $areaID         = advisory_id_from_string($area['name']);
                                                        $least          = empty($contents[$areaID]['least']) ? '' : $contents[$areaID]['least'];
                                                        $minimal        = empty($contents[$areaID]['minimal']) ? '' : $contents[$areaID]['minimal'];
                                                        $moderate       = empty($contents[$areaID]['moderate']) ? '' : $contents[$areaID]['moderate'];
                                                        $significant    = empty($contents[$areaID]['significant']) ? '' : $contents[$areaID]['significant'];
                                                        $most           = empty($contents[$areaID]['most']) ? '' : $contents[$areaID]['most'];
                                                        $areaDefault    = empty($default[$areaID]) ? 0 : $default[$areaID];
                                                        $activeClass    = getActiveCSAClass($areaDefault, 'rate');

                                                        echo '<tr id="'. $areaID .'">';
                                                            echo '<th class="gray" colspan="23"><input type="hidden" name="'.$areaID.'" value="'. $areaDefault .'">'. $areaSI .'. '. $area['name'] .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_1 '. $activeClass['least'] .'" value=1 area="'.$areaID.'" title="Least - '.$area['name'].'" content="'. htmlentities($least) .'">'. customExcerpt($least, 9999, '') .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_2 '. $activeClass['minimal'] .'" value=2 area="'.$areaID.'" title="Minimal - '.$area['name'].'" content="'. htmlentities($minimal) .'">'. customExcerpt($minimal, 9999, '') .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_3 '. $activeClass['moderate'] .'" value=3 area="'.$areaID.'" title="Moderate - '.$area['name'].'" content="'. htmlentities($moderate) .'">'. customExcerpt($moderate, 9999, '') .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_4 '. $activeClass['significant'] .'" value=4 area="'.$areaID.'" title="Significant - '.$area['name'].'" content="'. htmlentities($significant) .'">'. customExcerpt($significant, 9999, '') .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_5 '. $activeClass['most'] .'" value=5 area="'.$areaID.'" title="Most - '.$area['name'].'" content="'. htmlentities($most) .'">'. customExcerpt($most, 9999, '') .'</th>';
                                                        echo '</tr>';
                                                        $areaSI++;
                                                    }
                                                }
                                            echo '</table><br>';
                                        echo '</div>';
                                        
                                        echo '<div class="card-footer text-right">';
                                            if ($select_attr == '') {
                                                echo '<input type="hidden" name="reset" value="true"><button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                            }
                                        echo '</div>
                                    </div>
                                </div>
                            </div>
                        </form>';
                        if($oldUserID == 1 || $oldUserID == 2) {
                            $pdf = advisory_form_default_values($transient_post_id, $department_id . '_pdf');
                            echo '<form class="form survey-form" method="post" data-meta="' . $department_id . '_pdf" data-id="'. $transient_post_id .'">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-title-w-btn">
                                                <h4 class="title">1. Provide description for assessment as well as summary</h4>';
                                                if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                            echo '</div>';
                                            echo '<div class="card-body">';
                                                echo '<div class="form-group">
                                                    <h4 class="title">Assessment Title : </h4>
                                                    <input class="form-control font-130p" type="text" name="at" id="at" ' . $select_attr . ' value="'. @$pdf['at'] .'">
                                                </div>';
                                                if ($select_attr) {
                                                    echo '<div class="form-group"> <h4 class="title">Assessment Desc : </h4> <textarea class="form-control font-130p texarea" name="ad" id="ad" cols="30" rows="2" ' . $select_attr . '>'. @$pdf['ad'] .'</textarea> </div>';
                                                } else {
                                                    $editor_id = 'ad';
                                                    $content = @$pdf[$editor_id];
                                                    $editor['textarea_name'] = $editor_id;
                                                    $editor['editor_css'] = '<h4 class="title">Assessment Desc : </h4>';
                                                    wp_editor( $content, $editor_id, $editor);
                                                    echo "<br>";
                                                }
                                                echo '<div class="form-group">
                                                    <h4 class="title">Summary Title : </h4>
                                                    <input class="form-control font-130p" type="text" name="st" id="st" ' . $select_attr . ' value="'. @$pdf['st'] .'">
                                                </div>';
                                                if ($select_attr) {
                                                    echo '<div class="form-group"> <h4 class="title">Summary Desc : </h4> <textarea class="form-control font-130p" name="sd" id="sd" cols="30" rows="2" ' . $select_attr . '>'. @$pdf['sd'] .'d</textarea> </div>';
                                                } else {
                                                    $editor_id = 'sd';
                                                    $content = @$pdf[$editor_id];
                                                    $editor['textarea_name'] = $editor_id;
                                                    $editor['editor_css'] = '<h4 class="title">Summary Desc : </h4>';
                                                    wp_editor( $content, $editor_id, $editor);
                                                    echo "<br>";
                                                }
                                                echo '<div class="form-group">
                                                    <h4 class="title">Process Areas : </h4>
                                                    <textarea class="form-control font-130p" name="pa" id="pa" cols="30" rows="2" ' . $select_attr . '>'. @$pdf['pa'] .'</textarea>
                                                </div>';
                                            echo '</div>
                                            <div class="card-footer text-right">';
                                                if ($select_attr == '') {
                                                    echo '<input type="hidden" name="reset" value="true"><button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                                }
                                            echo '</div>
                                        </div>
                                    </div>
                                </div>
                            </form>';
                        }
                    }
                } else if('cybersecurity' == advisory_id_from_string($section['name']) && !empty($opts[$sectionID])){
                    foreach ($opts[$sectionID] as $domain) {
                        if (@$_GET['area'] != advisory_id_from_string($domain['name'])) continue;
                        $domain_id = advisory_id_from_string($domain['name']) . '_domains';
                        $domainImg = advisory_id_from_string($domain['name']);
                        $department_id = $sectionID.'_'.$domain_id;
                        $contents = advisory_generate_csa_form_cyber_security_contents(advisory_id_from_string($domain['name']));
                        echo '<div class="row">';
                            echo '<div class="col-md-6"><div class="card"><img src="'. IMAGE_DIR_URL .'csa/'. $domainImg .'.jpg" alt="domain heading"></div></div>';
                            echo '<div class="col-md-6"><div class="card"><img src="'. IMAGE_DIR_URL .'csa/domain_rating.jpg" alt="domain rating"></div></div>';
                        echo '</div>';
                        
                        $default = advisory_form_default_values($transient_post_id, $department_id.'_csa');
                        echo '<form class="form survey-form" method="post" data-meta="'.$department_id.'_csa" data-id="'. $transient_post_id .'">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-title-w-btn">
                                            <h4 class="title"></h4>';
                                            if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                        echo '</div>';
                                        echo '<style>';
                                            echo 'input {width: 100%; display:block; font-size:16px !important;font-weight:normal !important;}';
                                            // echo 'input, input:focus, input:active {border:none !important;outline:none !important;}';
                                            echo '.csaForm .main-heading {background: #000;color: #fff;padding: 5px 10px;font-size: 20px !important;font-weight: 700 !important;}';
                                            echo '.csaForm .table {margin-bottom: 0 !important;}';
                                            echo '.csaForm .table-bordered>tbody>tr>td,.csaForm .table-bordered>tbody>tr>th {border: 1px solid #000;padding: 4px;}';
                                            echo '.csaForm .center {text-align:center;}';
                                            echo '.csaForm .strong {font-weight:bold;}';
                                            echo '.csaForm .black {background: #000; color: #fff;}';
                                            echo '.csaForm .red {background: #e40613;}';
                                            echo '.csaForm .orange {background: #ea4e1b;}';
                                            echo '.csaForm .yellow {background: #fdea11;}';
                                            echo '.csaForm .green {background: #3baa34;}';
                                            echo '.csaForm .aqua {background: #36a9e0;}';
                                            echo '.csaForm .table .gray {background: #d9d9d9; font-size: 18px;color: #000;padding: 10px 5px;}';
                                            echo '.csaForm .table .sub-heading th, ';
                                            echo '.csaForm .table .sub-heading td {font-size: 18px;}';
                                            echo '.csaForm .table .modalCSA {cursor:pointer;}';
                                            echo '.csaForm .modalCSA_1.active {background: rgba(228,6,19, 0.3);}';
                                            echo '.csaForm .modalCSA_2.active {background: rgba(234,78,27, 0.3);}';
                                            echo '.csaForm .modalCSA_3.active {background: rgba(253,234,17, 0.3);;}';
                                            echo '.csaForm .modalCSA_4.active {background: rgba(59,170,52, 0.3);;}';
                                            echo '.csaForm .modalCSA_5.active {background: rgba(54,169,224, 0.3);;}';
                                            echo '.modal-header {background-color: #333;}';
                                            echo '.modal-title {color:#fff;}';
                                            echo '.modal-body p {font-weight:500; font-size: 18px;color: #333;}';
                                        echo '</style>';
                                        
                                        echo '<div class="csaForm" type="cyber_security">';
                                            echo '<table class="table table-bordered">';
                                                // echo '<tr><th class="black" colspan="100" style="font-size: 21px;">'. $domain['name'] .'</th></tr>';
                                                echo '<tr>';
                                                    echo '<th class="black" colspan="100" style="font-size: 21px;"><span class="text-uppercase">'. $domain['name'] .'</span> <div class="pull-right"><ul class="list-inline" style="margin-bottom:0;"><li>Total Rating: </li><li id="rate" class="'.@$default['cls'].'" style="width:50px;color:transparent;">lore<input type="hidden" name="cls" id="cls" value="'.@$default['cls'].'"><input type="hidden" name="avg" id="avg" value="'.@$default['avg'].'"></li></div></th>';
                                                echo '</tr>';
                                                echo '<tr class="sub-heading">';
                                                    echo '<td class="black" colspan="23" style="width:23%;font-size: 21px;">'. $domain['title'] .'</td>';
                                                    echo '<th class="center aqua">Innovative</th>';
                                                    echo '<th class="center green">Advanced</th>';
                                                    echo '<th class="center yellow">Intermediate</th>';
                                                    echo '<th class="center orange">Evolving</th>';
                                                    echo '<th class="center red">Baseline</th>';
                                                echo '</tr>';
                                                if ($opts[$domain_id]) {
                                                    $domainSI = 1;
                                                    foreach ($opts[$domain_id] as $area) {
                                                        $areaID         = advisory_id_from_string($area['name']);
                                                        $baseline       = empty($contents[$areaID]['baseline']) ? '' : $contents[$areaID]['baseline'];
                                                        $evolving       = empty($contents[$areaID]['evolving']) ? '' : $contents[$areaID]['evolving'];
                                                        $intermediate   = empty($contents[$areaID]['intermediate']) ? '' : $contents[$areaID]['intermediate'];
                                                        $advanced       = empty($contents[$areaID]['advanced']) ? '' : $contents[$areaID]['advanced'];
                                                        $innovative     = empty($contents[$areaID]['innovative']) ? '' : $contents[$areaID]['innovative'];
                                                        $areaDefault    = empty($default[$areaID]) ? 0 : $default[$areaID];
                                                        $activeClass    = getActiveCSAClass($areaDefault);
                                                        echo '<tr id="'. $areaID .'">';
                                                            echo '<th class="gray" colspan="23"><input type="hidden" name="'.$areaID.'" value="'. $areaDefault .'">'. $domainSI .'. '. $area['name'] .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_5 '. $activeClass['innovative'] .'" value=5 area="'.$areaID.'" title="Innovative - '.$area['name'].'" content="'. htmlentities($innovative) .'">'. customExcerpt($innovative) .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_4 '. $activeClass['advanced'] .'" value=4 area="'.$areaID.'" title="Advanced - '.$area['name'].'" content="'. htmlentities($advanced) .'">'. customExcerpt($advanced) .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_3 '. $activeClass['intermediate'] .'" value=3 area="'.$areaID.'" title="Intermediate - '.$area['name'].'" content="'. htmlentities($intermediate) .'">'. customExcerpt($intermediate) .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_2 '. $activeClass['evolving'] .'" value=2 area="'.$areaID.'" title="Evolving - '.$area['name'].'" content="'. htmlentities($evolving) .'">'. customExcerpt($evolving) .'</th>';
                                                            echo '<th class="center '.$areaID.' modalCSA modalCSA_1 '. $activeClass['baseline'] .'" value=1 area="'.$areaID.'" title="Baseline - '.$area['name'].'" content="'. htmlentities($baseline) .'">'. customExcerpt($baseline) .'</th>';
                                                        
                                                        echo '</tr>';
                                                        $domainSI++;
                                                    }
                                                }
                                            echo '</table><br>';
                                        echo '</div>';
                                        
                                        echo '<div class="card-footer text-right">';
                                            if ($select_attr == '') {
                                                echo '<input type="hidden" name="reset" value="true"><button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                            }
                                        echo '</div>
                                    </div>
                                </div>
                            </div>
                        </form>';
                        if($oldUserID == 1 || $oldUserID == 2) {
                            $pdf = advisory_form_default_values($transient_post_id, $department_id . '_pdf');
                            echo '<form class="form survey-form" method="post" data-meta="' . $department_id . '_pdf" data-id="'. $transient_post_id .'">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-title-w-btn">
                                                <h4 class="title">1. Provide description for assessment as well as summary</h4>';
                                                if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                            echo '</div>';
                                            echo '<div class="card-body">';
                                                echo '<div class="form-group">
                                                    <h4 class="title">Assessment Title : </h4>
                                                    <input class="form-control font-130p" type="text" name="at" id="at" ' . $select_attr . ' value="'. @$pdf['at'] .'">
                                                </div>';
                                                if ($select_attr) {
                                                    echo '<div class="form-group"> <h4 class="title">Assessment Desc : </h4> <textarea class="form-control font-130p" name="ad" id="ad" cols="30" rows="2" ' . $select_attr . '>'. @$pdf['ad'] .'</textarea> </div>';
                                                } else {
                                                    $editor_id = 'ad';
                                                    $content = @$pdf[$editor_id];
                                                    $editor['textarea_name'] = $editor_id;
                                                    $editor['editor_css'] = '<h4 class="title">Assessment Desc : </h4>';
                                                    wp_editor( $content, $editor_id, $editor);
                                                    echo "<br>";
                                                }
                                                echo '<div class="form-group">
                                                    <h4 class="title">Summary Title : </h4>
                                                    <input class="form-control font-130p" type="text" name="st" id="st" ' . $select_attr . ' value="'. @$pdf['st'] .'">
                                                </div>';
                                                if ($select_attr) {
                                                    echo '<div class="form-group"> <h4 class="title">Summary Desc : </h4> <textarea class="form-control font-130p" name="sd" id="sd" cols="30" rows="2" ' . $select_attr . '>'. @$pdf['sd'] .'</textarea> </div>';
                                                } else {
                                                    $editor_id = 'sd';
                                                    $content = @$pdf[$editor_id];
                                                    $editor['textarea_name'] = $editor_id;
                                                    $editor['editor_css'] = '<h4 class="title">Summary Desc : </h4>';
                                                    wp_editor( $content, $editor_id, $editor);
                                                    echo "<br>";
                                                }
                                                echo '<div class="form-group">
                                                    <h4 class="title">Process Areas : </h4>
                                                    <textarea class="form-control font-130p" name="pa" id="sinesdfkjdfpa" cols="30" rows="2" ' . $select_attr . '>'. @$pdf['pa'] .'</textarea>
                                                </div>';
                                            echo '</div>';
                                            echo '<div class="card-footer text-right">';
                                                if ($select_attr == '') {
                                                    echo '<input type="hidden" name="reset" value="true"><button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
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
        }
    echo '</div>';
    echo '<div id="modal-csa" class="modal fade">';
        echo '<div class="modal-dialog">';
            echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                    echo '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
                    echo '<h4 class="modal-title">Select options</h4>';
                echo '</div>';
                echo '<div class="modal-body"></div>';
                echo '<div class="row modal-footer">';
                    echo '<div class="col-sm-6 text-left"><label> <input style="float: left;width: 20px;" type="checkbox" id="areaSelect"> Select this area </label></div>';
                    echo '<div class="col-sm-6">';
                        echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
                        echo '<button type="button" class="btn btn-primary saveCSA">Save changes</button>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>'; ?>
    <script>
        jQuery(function($) {
            $(document).on('click', '.modalCSA', function(e) {
                var modalBody = $(this).attr('content');
                var modalTitle = $(this).attr('title');
                var area = $(this).attr('area');
                var value = $(this).attr('value');
                var type = $(this).parents('.csaForm').attr('type');
                var isAcitve = $(this).is('.active');

                $('#modal-csa .modal-title').text(modalTitle);
                $('#modal-csa .modal-body').html(modalBody);
                $('#modal-csa .saveCSA').attr({'area':area, 'value': value, 'csatype': type});
                if (isAcitve) $('#modal-csa #areaSelect').attr('checked', true);
                else $('#modal-csa #areaSelect').attr('checked', false);
                $('#modal-csa').modal('show');
            })
            $(document).on('click', '.saveCSA', function(e) {
                var button = $(this);
                var area = button.attr('area');
                var value = button.attr('value');
                var csatype = button.attr('csatype');
                var isActiveCSA = button.parents('.modal-footer').find('#areaSelect:checked').val();
                if (!isActiveCSA) alert('You haven\'t choose the option.');
                else {
                    var resetElements = '.'+ area +'.modalCSA';
                    var selelectedElement = '.'+ area +'.modalCSA_'+ value;
                    var inputText = '#'+ area +' input';
                    $(inputText).val(value);

                    var avg = rateClass(csatype);
                    $(resetElements).removeClass('active');
                    $(selelectedElement).addClass('active');
                    $('#cls').val(avg.cls);
                    $('#avg').val(avg.val);
                    $('#rate').removeClass().addClass(avg.cls);
                    $('#modal-csa').modal('hide');
                }
            })
            function rateClass(csatype) {
                var avg = {};
                var total = counter = avarage = 0;
                $('.gray').each(function() {
                    total += parseInt($(this).find('input').val());
                    counter++;
                });
                avarage = (total / counter).toFixed(2);
                if (csatype == 'rate') {
                    if (avarage < 1)                               { avg.val = avarage; avg.cls = "default"; }
                    else if ((avarage >= 1) && (avarage < 1.5))     { avg.val = avarage; avg.cls = "aqua"; }
                    else if ((avarage >= 1.5) && (avarage < 2.5))   { avg.val = avarage; avg.cls = "green"; }
                    else if ((avarage >= 2.5) && (avarage < 3.5))   { avg.val = avarage; avg.cls = "yellow"; }
                    else if ((avarage >= 3.5) && (avarage < 4.5))   { avg.val = avarage; avg.cls = "orange"; }
                    else if (avarage >= 4.5)                        { avg.val = avarage; avg.cls = "red"; }
                } else {
                    if (avarage < 1)                               { avg.val = avarage; avg.cls = "default"; }
                    else if ((avarage >= 1) && (avarage < 1.5))     { avg.val = avarage; avg.cls = "red"; }
                    else if ((avarage >= 1.5) && (avarage < 2.5))   { avg.val = avarage; avg.cls = "orange"; }
                    else if ((avarage >= 2.5) && (avarage < 3.5))   { avg.val = avarage; avg.cls = "yellow"; }
                    else if ((avarage >= 3.5) && (avarage < 4.5))   { avg.val = avarage; avg.cls = "green"; }
                    else if (avarage >= 4.5)                        { avg.val = avarage; avg.cls = "aqua"; }
                }
                return avg;
            }
        })
    </script>
    <script type="text/javascript">
        jQuery(function ($) {
            setTimeout(function () {
                for (var i = 0; i < tinymce.editors.length; i++) {
                    tinymce.editors[i].onChange.add(function (ed, e) {
                        ed.save();
                    });
                }
            }, 1000);
        });
    </script>
<?php get_footer(); ?>