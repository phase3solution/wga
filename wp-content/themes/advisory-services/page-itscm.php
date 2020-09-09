<?php 
// Template Name: ITSCM
if (isset($_POST['docSubmits'])) {
    $user_company_data = advisory_get_user_company();
    $args = ['post_type' => 'docl','posts_per_page' => 1,'post_name__in'  => [$user_company_data->slug]];
    $post = get_posts($args);
    if ($post) $post = $post[0]->ID;
    else {
        $post = wp_insert_post(array (
            'post_type' => 'docl',
            'post_title' => $user_company_data->name,
            'post_content' => '',
            'post_status' => 'publish',
            'comment_status' => 'closed',   // if you prefer
            'ping_status' => 'closed',      // if you prefer
        ));
        if ($post) add_post_meta($post, 'form_opts', ['documents'=>[]]);
    }
    $meta_data = get_post_meta( $post, 'form_opts', true );
    $documents = !empty($meta_data['documents']) ? $meta_data['documents'] : [];
    $meta = [
        'name'=> $_POST['docName'], 
        'document_upload'=>$_POST['document_upload'], 
        'date_picker'=>$_POST['date_picker'], 
        'comments'=> $_POST['comment'], 
        'company'=>$user_company_data->term_id
    ];
    array_push($documents, $meta);
    update_post_meta($post,'form_opts',['documents'=>$documents]);
    // echo '<br><br><br><br><br><pre style="margin-left:300px;">sdf'.$post. print_r(get_post_meta( $post, 'form_opts', true ), true) .'</pre>';
}
get_header();
$user_data = wp_get_current_user();
$mediaPage = get_user_meta($user_data->ID, 'mediaPage', true );
$user_company_id = advisory_get_user_company_id(); 
$allCompanies = advisory_registered_companies();
// echo '<br><br><br><br><br><pre style="margin-left:300px;">'. print_r($user_data, true) .'</pre>'; exit();
?>
<div class="content-wrapper">
    <div class="page-title">
        <div> <h1><img class="dashboardIcon" src="<?php echo get_template_directory_uri(); ?>/images/icon-itscm.png" alt=""> <?php echo the_title(); ?></h1> </div>
        <div> <ul class="breadcrumb"> <li><i class="fa fa-home fa-lg"></i></li> <li><a href="#"><?php echo the_title(); ?></a></li> </ul> </div>
    </div>
    <?php if (current_user_can('viewer')) { ?>
        <div class="row">
            <div class="col-md-12 col-lg-6">
                <div class="bs-component">
                    <div class="panel"> <!-- .panel-info -->
                        <div class="panel-heading text-center pb-0">
                            <?php if (advisory_metrics_in_progress($user_company_id, array('drm'))) { ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/current-metrics-process.jpg" alt="" class="img-responsive">
                            <?php } else { ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/current-metrics.jpg" alt="" class="img-responsive">
                            <?php } ?>
                        </div>
                        <div class="panel-body p-0">
                            <div class="panel-option">
                                <ul>
                                    <?php 
                                    $current_metrics_post_type = 'drm';
                                    if (advisory_metrics_in_progress($user_company_id, array($current_metrics_post_type))) {
                                        $areas = advisory_transient_avg($user_company_id, array($current_metrics_post_type));
                                    } else {
                                        $areas = advisory_dashboard_avg($user_company_id, array($current_metrics_post_type));
                                        if (empty($areas)) {
                                            $areas = [
                                                ['name' => 'Organizational Readiness'], 
                                                ['name' => 'Technology Readiness'], 
                                                ['name' => 'Recovery Planning'], 
                                                ['name' => 'Maintenance & Improvement'] 
                                            ];
                                        }
                                    }
                                    if (!empty($areas)) {
                                        foreach ($areas as $area) {
                                            if (!empty($area['values'])) {
                                                $avg = array_sum($area['values']) / count($area['values']);
                                                $avgTxt = number_format($avg, 1);
                                                $imageUrl = IMAGE_DIR_URL.'current_metrics/'.$current_metrics_post_type.'/'.advisory_id_from_string($area['name']) .'_'. coloring_elements(number_format($avg, 1), 'metrics') . '.png';
                                                $title = $area['name'].' ('.$avgTxt.')';
                                            } else {
                                                $avg = 0;
                                                $avgTxt = 'N/A';
                                                $imageUrl = IMAGE_DIR_URL.'current_metrics/'.$current_metrics_post_type.'/'.advisory_id_from_string($area['name']) . '.png';
                                                $title = $area['name'].' ('.$avgTxt.')';
                                            }
                                            echo '<li><img src="'.$imageUrl.'" alt="'.$area['name'].'" title="'.$title.'" class="img-responsive"></li>';
                                        } 
                                    } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="panel-chart">
                                <div class="row">
                                    <div class="col-sm-9">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/dr_maturity.png" class="img-responsive text-center" usemap="#Map" hidefocus="true" />
                                        <map name="Map" id="Map">
                                            <area alt="" title="" href="<?php echo advisory_graphic_link(array('drm'), 'recovery_planning') ?>" shape="poly" coords="44, 253, 60, 253, 58, 315, 62, 326, 67, 333, 73, 338, 247, 337, 245, 497, 27, 493, 16, 485, 4, 474" />
                                            <area alt="" title="" href="<?php echo advisory_graphic_link(array('drm'), 'maintenance_sand_improvement') ?>" shape="poly" coords="484,332,369,294,357,316,346,333,321,352,299,365,257,375,256,496,284,495,314,489,341,480,369,467,398,450,430,422,462,381" />
                                            <area alt="" title="" href="<?php echo advisory_graphic_link(array('drm'), 'technology_readiness') ?>" shape="poly" coords="464, 4, 250, 4, 252, 163, 424, 164, 437, 174, 441, 180, 440, 246, 493, 247, 493, 32, 484, 17, 475, 9" />
                                            <area alt="" title="" href="<?php echo advisory_graphic_link(array('drm'), 'operations') ?>" shape="poly" coords="6,7,247,246" />
                                            <area alt="" title="" href="<?php echo advisory_graphic_link(array('drm'), 'organizational_readiness') ?>" shape="poly" coords="33,5,245,4,244,166,75,163,62,170,58,181,57,248,3,248,5,32,12,18,26,9" />
                                            <?php if(advisory_has_dashboard_reset_permission()) { ?>
                                                <area alt="" title="" href="#" class="reset-survey" shape="poly" coords="172,170,193,154,211,146,230,139,253,139,274,141,296,148,317,160,330,170,342,187,355,209,362,235,362,257,358,281,348,303,335,321,318,337,298,349,276,358,252,361,225,358,196,346,173,330,160,314,148,295,140,267,139,239,149,201" />
                                            <?php } ?>
                                        </map>        
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-6">
                                                <a href="<?php echo advisory_graphic_link(array('bia'), 'finance') ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/bia_assessment.jpg" class="img-responsive" alt=""></a>
                                            </div>
                                            <div class="col-sm-12 col-xs-6">
                                                <a href="<?php echo advisory_graphic_link(array('risk'), 'environmental') ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/risk_assessment.png" class="img-responsive" alt=""></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="row">
                            <div class="col-sm-12">
                                <div style="text-align:center; padding: 10px 25px 10px 77px;"><img src="<?php echo P3_TEMPLATE_URI; ?>/images/dashboard/dr_maturity_trend_analysis.png"></div>
                                <div class="embed-responsive embed-responsive-16by9 dr-chart">
                                    <canvas class="embed-responsive-item" id="surveyChartDr"></canvas>
                                </div>
                                <ul class="list-inline drmTrendAnalysisXAxis">
                                    <li class="organizational_readiness"><img title="Organizational Readiness" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/drm/organizational_readiness.png"></li>
                                    <li class="technology_readiness"><img title="Technology Readiness" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/drm/technology_readiness.png"></li>
                                    <li class="recovery_planning"><img title="Recovery Planning" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/drm/recovery_planning.png"></li>
                                    <li class="maintenance_sand_improvement"><img title="Maintenance & Improvement" src="<?php echo P3_TEMPLATE_URI; ?>/images/current_metrics/drm/maintenance_sand_improvement.png"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="bs-component">
                    <div class="card">
                        <div class="row">
                            <div class="col-sm-12"> <img src="<?php echo IMAGE_DIR_URL; ?>doc-library.jpg" class="img-responsive"> <img src="<?php echo IMAGE_DIR_URL; ?>dr_documentation_lifecycle_full.png" class="img-responsive DRLifecycleThumb"></div>
                            <?php if ($mediaPage): ?>
                                <div class="col-sm-6">  </div>
                                <div class="col-sm-6 btnWrapper text-right" style="margin-bottom:15px;"><button class="btn btn-primary btn-md docUploadBtn" data-toggle="collapse" data-target="#demo">Add Document</button></div>
                                <div class="col-sm-12">
                                    <div id="demo" class="collapse">
                                        <form action="" method="POST" role="form">
                                            <legend>Add document</legend>
                                            <input type="hidden" name="company" value="<?php echo $user_company_id ?>">
                                            <div class="form-group">
                                                <label for="">Document Name <span class="text-danger text-bold">*</span></label>
                                                <input type="text" class="form-control" name="docName" required>
                                            </div>
                                            <div class="form-group uploaderWrapper">
                                                <label for="">Document<span class="text-danger text-bold">*</span></label><br>
                                                <input id="upload_image" type="text" class="form-control uploadField" name="document_upload" style="line-height: 1.44" required><input id="upload_image_button" class=" btn btn-primary btn-md imageUploaderBtn" type="button" value="Browse File" />
                                            </div>
                                            <div class="form-group">
                                                <label for="">Upload Date</label>
                                                <input type="date" class="form-control" name="date_picker" style="line-height: 1.44;">
                                            </div>
                                            <div class="form-group">
                                                <label for="">Comment</label>
                                                <textarea name="comment" class="form-control" cols="30" rows="3"> </textarea>
                                            </div>  
                                            <div class="form-group text-right">
                                                <button type="submit" class="btn btn-primary" name="docSubmits">Submit</button>
                                            </div>
                                        </form>
                                        <br>
                                    </div>
                                </div>
                            <?php endif ?>
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table style="width:100%" class="table table-bordered  table-hover itscmDocuments">
                                        <tr>
                                            <th class="text-left" style="font-size:16px;">Document</th>
                                            <th class="text-center" style="min-width:100px;font-size:16px">Date Uploaded</th>
                                            <th class="text-left" style="font-size:16px">Comments</th>
                                        </tr>
                                        <?php
                                        $args = [
                                            'post_type' => 'docl',
                                            'posts_per_page' => -1,
                                            'post_status' => 'publish',
                                        ];
                                        $docls = get_posts($args);
                                        if ($docls) {
                                            foreach ($docls as $docl) {
                                                $meta_data = get_post_meta( $docl->ID, 'form_opts', true );
                                                foreach ($meta_data as $sections) {
                                                    if ($sections) {
                                                        
                                                        foreach ($sections as $section){
                                                            $user_company_id = advisory_get_user_company_id();
                                                            if ($section['company'] == $user_company_id){
                                                                echo '<tr>';
                                                                    echo '<td class="text-left" style="font-weight:bold;"><a href="'. $section['document_upload'] .'">'. $section['name'] .'</a></td>';
                                                                    echo '<td class="text-center" style="">'. $section['date_picker'] .'</td>';
                                                                    echo '<td class="text-left" style="line-height:1">'. $section['comments'] .'</td>';
                                                                echo '</tr>';
                                                           }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="card ten-row-table">
                    <h4 class="card-title">Previous Health Check</h4>
                    <div class="card-body">
                        <?php $meta_query = array();
                        $date_query = array();
                        $co = '';
                        $y = '';
                        if (isset($_GET['co'])) {
                            if (!empty($_GET['co'])) {
                                array_push($meta_query, array('key' => 'assigned_company', 'value' => $_GET['co']));
                                $co = $_GET['co'];
                            }
                        }
                        if (isset($_GET['y'])) {
                            if (!empty($_GET['y'])) {
                                array_push($date_query, array('year' => $_GET['y']));
                                $y = $_GET['y'];
                            }
                        } ?>
                        <form class="title-select" action="" method="get">
                            <div class="row">
                                <div class="col-sm-2">
                                    <select name="co">
                                        <option value="">Company</option>
                                        <?php $companies = advisory_registered_companies();
                                        foreach ($companies as $key => $name) {
                                            echo '<option value="' . $key . '" ' . ($key == $co ? 'selected' : '') . '>' . $name . '</option>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select name="y">
                                        <option value="">Year</option>
                                        <?php $years = range(2017, date("Y"));
                                        foreach ($years as $year) {
                                            echo '<option value="' . $year . '" ' . ($year == $y ? 'selected' : '') . '>' . $year . '</option>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-sm-8">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <?php if (isset($_GET['co']) || isset($_GET['y'])) {
                                        echo '<a href="' . home_url() . '" class="btn btn-primary">Reset</a>';
                                    } ?>
                                </div>
                            </div>
                        </form>
                        <table class="table table-condensed panel-option-right">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Category</th>
                                    <th>Published</th>
                                    <th>Modified</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $posts_per_page = 30;
                                $paged = (get_query_var('page')) ? get_query_var('page') : 1;
                                $query = new WP_Query([
                                    'post_type' => array('ihc', 'itsm', 'cra', 'bia', 'risk', 'drm'),
                                    'post_status' => 'archived',
                                    'posts_per_page' => $posts_per_page,
                                    'paged' => $paged,
                                    'meta_query' => $meta_query,
                                    'date_query' => $date_query,
                                ]); ?>
                                <?php if ($query->have_posts()) {
                                    while ($query->have_posts()) {
                                        $query->the_post();
                                        $post_id = get_the_ID();
                                        $company_data =  get_term(get_post_meta($post_id, 'assigned_company', true), 'company');
                                        echo '<tr>
                                            <td>' . $company_data->name . '</td>
                                            <td>' . advisory_get_form_name($post_id) . '</td>
                                            <td>'. get_the_date() .'</td>
                                            <td>'. get_the_modified_date() .'</td>
                                            <td class="text-right">
                                                <div class="btn-group" data-toggle="tooltip" title="View">
                                                    <a class="btn btn-primary dropdown-toggle" href="#" data-toggle="dropdown"><span class="fa fa-eye"></span></a>
                                                    <ul class="dropdown-menu">';
                                                        foreach (advisory_template_areas($post_id) as $area) {
                                                            echo '<li><a href="' . get_the_permalink($post_id) . '?view=true&area=' . advisory_id_from_string($area) . '" target="_blank">' . $area . '</a></li>';
                                                        }
                                                    echo '</ul>
                                                </div>';
                                                echo ' <a class="btn btn-primary" href="scorecard/?view=' . get_the_ID() . '" target="_blank" data-toggle="tooltip" title="Scorecard"><span class="fa fa-area-chart"></a>';
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
                                                }
                                            echo '</td>
                                        </tr>';
                                    }
                                    wp_reset_postdata();
                                } else {
                                    echo '<tr>
                                        <td colspan="4">Nothing Found</td>
                                    </tr>';
                                } ?>
                            </tbody>
                        </table>
                        <?php wp_pagenavi(array('query' => $query)); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="modal fade" id="docUploader">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="DRLifecycleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">DR Documentation LIfecycle</h4>
            </div>
            <div class="modal-body"><img src="<?php echo IMAGE_DIR_URL; ?>dr_documentation_lifecycle_full.png" class="img-responsive"> </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        $(document).on('click', '.docUploadBtn', function(event) {
            event.preventDefault();
            //$('#docUploader').modal('show');
        });
    })
</script>
<?php wp_enqueue_media(); ?>
<script>
    jQuery(document).ready( function( $ ) {
        // upload media image
        $('#upload_image_button').click(function(e) {
            e.preventDefault();
            var mainObj = $(this);
            var image = wp.media({
                title: 'Upload Image',
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open()
            .on('select', function(e){
                // This will return the selected image from the Media Uploader, the result is an object
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                var image_url = uploaded_image.toJSON().url;
                $('.imagePreview').prop("src",image_url);
                $('#upload_image').val( image_url );
                $('.imageWrapper').removeClass('hidden');
            });
        });
        // remove media image
        $(document).on( 'click', '.removeBtn', function () {
          $('#upload_image').val('');
            $('.imageWrapper').addClass('hidden');
        });
        jQuery(document).on('mouseover', '.DRLifecycleThumb', function(e) {
            e.preventDefault();
            $('#DRLifecycleModal').modal('show');
        });
    });
</script>
<?php get_footer(); ?>