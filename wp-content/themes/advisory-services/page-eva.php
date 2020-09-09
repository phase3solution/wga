<?php 
/* Template Name: EVA */
get_header();
$user_data = wp_get_current_user();
$user_company_id = advisory_get_user_company_id(); 
$allCompanies = advisory_registered_companies();

$args = ['post_type' => 'csa', 'post_status' => 'published', 'meta_query' => array(array('key' => 'assigned_company', 'value' => $user_company_id))];
$activePosts = get_posts($args);

// echo '<br><br><br><br><br><pre style="margin-left:300px;">'. print_r($user_data, true) .'</pre>'; exit();
?>
<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h1><img class="dashboardIcon" src="<?php echo get_template_directory_uri(); ?>/images/icon-itscm.png" alt=""> <?php echo the_title(); ?></h1>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="#"><?php echo the_title(); ?></a></li>
            </ul>
        </div>
    </div>
    <?php if (current_user_can('viewer')) { ?>
        <div class="row">
            <div class="col-md-12 col-lg-6">
                <div class="bs-component">
                    <div class="panel">
                        <div class="panel-heading text-center">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/current-metrics.jpg" alt="" class="img-responsive">
                        </div>
                        <div class="panel-body panel-dark">
                            <div class="panel-option">
                                <ul>
                                    <?php if (count($activePosts)) {
                                        $transient_data = advisory_transient_csa_avg($activePosts[0]);
                                        // echo '<br><pre>'. print_r($transient_data, true) .'</pre>';
                                        foreach ($transient_data as $area) {
                                            echo '<li>
                                                <h3 class="' . coloring_elements(number_format($area['value'], 1), 'ihc-metrics') . '">
                                                    ' . ($area['value'] == 0 ? 'N/A' : number_format($area['value'], 1)) . '
                                                </h3>
                                                <p class="no-margin">' . $area['name'] . '</p>
                                            </li>';
                                        }
                                    } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="panel-chart">
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <img style="max-width:600px" src="<?php echo get_template_directory_uri(); ?>/images/eva.png" alt=""/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="bs-component">
                    <div class="card">
                        <div class="row">
                            <div class="col-sm-8">
                                <select class="ajax-eva-select bold">
                                    <?php $query = new WP_Query(['post_type' => 'csa', 'post_status' => 'published', 'meta_query' => array(array('key' => 'assigned_company', 'value' => $user_company_id))]);
                                    if ($query->have_posts()) {
                                        while ($query->have_posts()) {
                                            $query->the_post();
                                            echo '<option value="' . get_the_ID() . '">' . advisory_get_form_name(get_the_ID()) . ' - ' . get_the_date() . '</option>';
                                        }
                                        wp_reset_postdata();
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div id="ajax-eva-data"> </div>
                        <!-- <div id="ajax-eva-data" style="max-width:600px;"> </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-6">
                <div class="card five-row-table">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/prev-health-check.jpg" alt="" class="img-responsive">
                    <div class="card-body">
                        <table class="table table-condensed panel-option-right bold">
                            <thead>
                                <tr>
                                    <th class="bold">Category</th>
                                    <th class="bold">Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $query = new WP_Query(['post_type' => 'csa', 'post_status' => 'archived', 'meta_query' => array(array('key' => 'assigned_company', 'value' => $user_company_id))]); ?>
                                <?php if ($query->have_posts()) {
                                    while ($query->have_posts()) {
                                        $query->the_post();
                                        $post_id = get_the_ID();
                                        $areas = advisory_template_csa_areas($post_id);
                                        echo '<tr>
                                            <td>' . advisory_get_form_name($post_id) . '</td>
                                            <td>'. get_the_date() .'</td>
                                            <td class="text-right">
                                                <div class="btn-group" data-toggle="tooltip" title="View">
                                                    <a class="btn btn-primary dropdown-toggle" href="#" data-toggle="dropdown"><span class="fa fa-eye"></span></a>
                                                    <ul class="dropdown-menu">';
                                                        foreach ($areas as $area) {
                                                            echo '<li><a href="' . get_the_permalink($post_id) . '?view=true&area=' . advisory_id_from_string($area) . '" target="_blank">' . $area . '</a></li>';
                                                        }
                                                    echo '</ul>
                                                </div>';
                                                if (advisory_has_survey_edit_permission(get_the_ID())) {
                                                    echo ' <div class="btn-group" data-toggle="tooltip" title="Edit">
                                                        <a class="btn btn-warning dropdown-toggle" href="#" data-toggle="dropdown"><span class="fa fa-edit"></span></a>
                                                        <ul class="dropdown-menu">';
                                                            foreach ($areas as $area) {
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
                                } else {
                                    echo '<tr>
                                        <td colspan="3">Nothing Found</td>
                                    </tr>';
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php get_footer(); ?>