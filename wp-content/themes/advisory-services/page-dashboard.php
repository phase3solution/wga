<?php 
// Template Name: Dashboard
exportSummaryToCSV();
get_header();
$user_company_id = advisory_get_user_company_id();
?>
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1><img class="dashboardIcon" src="<?php echo get_template_directory_uri(); ?>/images/dashboard.png" alt=""> <?php echo the_title(); ?></h1>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="#"><?php echo the_title(); ?></a></li>
            </ul>
        </div>
    </div>
    <?php if (current_user_can('viewer')) {
        $data = get_term_meta($user_company_id, 'company_data', true);
        $dashboard = @$data['userDashboard'] == 'Dashboard B' ? true : false;
        $postTypes = json_decode(ALL_POST_TYPES);
        if ($dashboard) {
            if (($key = array_search('ihc', $postTypes)) !== false) {
                unset($postTypes[$key]);
            }
        } else {
            if (($key = array_search('mta', $postTypes)) !== false) {
                unset($postTypes[$key]);
            }
        }
        if ($dashboard) {
            include (locate_template('includes/dashboard-B.php'));
        } else {
            include (locate_template('includes/dashboard-A.php'));
        }
    } else { ?>
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
                                ]);
                                if ($query->have_posts()) {
                                    while ($query->have_posts()) {
                                        $query->the_post();
                                        $post_id = get_the_ID();
                                        $company_data =  get_term(get_post_meta($post_id, 'assigned_company', true), 'company');
                                        $companyName = !empty($company_data->name) ? $company_data->name : 'Not assigned';
                                        echo '<tr>';
                                            echo '<td>' . $companyName . '</td>';
                                            echo '<td>' . advisory_get_form_name($post_id) . '</td>';
                                            echo '<td>'. get_the_date() .'</td>';
                                            echo '<td>'. get_the_modified_date() .'</td>
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
                                } else {echo '<tr> <td colspan="4">Nothing Found</td> </tr>'; } 
                                ?>
                            </tbody>
                        </table>
                        <?php wp_pagenavi(array('query' => $query)); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php include (locate_template('includes/modal-noDMM.php')); ?>
<?php include (locate_template('includes/modal-dashboard.php')); ?>  
<?php include (locate_template('includes/modal-sfia_not_accessible.php')); ?>  
<script>
jQuery(function($) {
    jQuery(document).on('click', '.emptyDMMPopup', function() {
        $('#noDMM').modal('show');
    });
    jQuery(document).on('click', '.reportCardItems', function() {
        var title = $(this).attr('title');
        var services = $(this).attr('services');
        var modalBody = '';
        if (!services) modalBody += '<p class="text-danger text-center"> <strong> No item found for this service. </strong> </p>';
        else {
            services = services.split('###');
            var serviceLength = services.length;
            if (serviceLength) {
                modalBody += '<table class="strip">';
                    modalBody += '<tr>';
                        modalBody += '<th style="width:50%;"> DEPARTMENT </th>';
                        modalBody += '<th style="width:50%;"> SERVICE </th>';
                    modalBody += '</tr>';
                    for (var i = 0; i < serviceLength; i++) { 
                        var serviceItem = services[i].split('&&&');
                        modalBody += '<tr>';
                            modalBody += '<td style="font-weight: 700;">'+ serviceItem[0] +'</td>'; 
                            modalBody += '<td style="font-weight: 700;">'+ serviceItem[1] +'</td>'; 
                        modalBody += '</tr>';
                    }
                modalBody += '</table>';
            }
        }
        $('#modal-bia .modal-title').text(title);
        $('#modal-bia .modal-body').html(modalBody);
        $('#modal-bia').modal('show');
    });
});
</script>
<?php 
 ?>
<?php get_footer(); ?>