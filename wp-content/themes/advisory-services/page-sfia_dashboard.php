<?php // Template Name: SFIA Dashboard  
get_header(); 
global $sfia_premission;
if (!$sfia_premission) { exit(wp_redirect(home_url())); }
if ($sfia_premission == 'full') $select_attr = '';
else $select_attr = 'disabled';
$user_company_id = advisory_get_user_company_id();
$sfia_id = advisory_sfia_get_active_post_id($user_company_id);
$company = get_term_meta($user_company_id, 'company_data', true);
$users = !empty($company['sfia_users']) ? advisory_select_array($company['sfia_users']) : [];
$teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
$available_users = advisory_sfia_get_company_available_users($company, $sfia_id);
?>
<div class="content-wrapper">
    <div class="page-title">
        <div> <h1><img src="<?php echo IMAGE_DIR_URL; ?>dashboard.png" class="dashboardIcon" alt="SFIA Title Logo"> Dashboard</h1> </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body sfia-wrapper">
                    <div class="sfia-header">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="logo"><img src="<?php echo get_template_directory_uri(); ?>/images/dashboard/sfial.png" alt="SFIA LOGO"></div>
                            </div>
                            <div class="col-sm-9">
                                <ul class="sfiaDocuments">
                                    <li class="how_it_works" data-title="how it works" data-pdf="<?php echo IMAGE_DIR_URL ?>sfia/dashboard/how_it_works.pdf"><img src="<?php echo IMAGE_DIR_URL ?>sfia/dashboard/how_it_works.png"></li>
                                    <li class="levels_of_responsibility" data-title="levels of responsibility" data-pdf="<?php echo IMAGE_DIR_URL ?>sfia/dashboard/levels_of_responsibility.pdf"><img src="<?php echo IMAGE_DIR_URL ?>sfia/dashboard/levels_of_responsibility.png"></li>
                                    <li class="skills_management" data-title="skills management" data-pdf="<?php echo IMAGE_DIR_URL ?>sfia/dashboard/skills_management.pdf"><img src="<?php echo IMAGE_DIR_URL ?>sfia/dashboard/skills_management.png"></li>
                                </ul>
                            </div>
                            <div class="col-sm-12">
                                <div class="text-center"> <img src="<?php echo IMAGE_DIR_URL ?>sfia/dashboard/corporate_skills.jpg" class="img-responsive"> </div>
                            </div>
                            <div class="col-sm-12 sfiaAddEditBtnContainer">
                                <div class="btn-group" data-toggle="tooltip" title="" data-original-title=""> 
                                    <button type="button" class="btn btn-lg bg-deepgreen" style="color: #fff;" data-toggle="modal" data-target="#allUsers"> <span class="fa fa-users"></span> Users</button>
                                    <button type="button" class="btn btn-lg btn-warning dropdown-toggle editBtn" data-toggle="dropdown" aria-expanded="false"> <span class="fa fa-edit"></span> Review/Edit Role</button>
                                    <ul class="dropdown-menu" style="width: 100%;">
                                        <?php if ($teams) {
                                            foreach ($teams as $team_id => $team) {
                                                echo '<li><a class="SFIAReviewEditRole" href="javascript:;" team_id='.$team_id.'>'.$team.'</a></li>';
                                            }
                                        } ?>
                                    </ul>
                                </div>
                                <?php if (!$select_attr) echo '<button type="button" class="btn btn-lg btn-primary addBtn add_sfia_user"><span class="fa fa-user-plus"></span> Add Role</button>'; ?>
                            </div>
                            <div class="col-sm-4"> </div>
                            <div class="col-sm-8">
                                <div class="sfia-level">
                                    <ul>
                                        <li class="pointer categoryInfo" info-link="<?php echo IMAGE_DIR_URL ?>sfia/pdf/strategyandarchitecture.png"><div class="color-one">&nbsp;</div><span>Strategy and Architecture</span></li>
                                        <li class="pointer categoryInfo" info-link="<?php echo IMAGE_DIR_URL ?>sfia/pdf/changeandtransformation.png"><div class="color-two">&nbsp;</div><span>Change and Transformation</span></li>
                                        <li class="pointer categoryInfo" info-link="<?php echo IMAGE_DIR_URL ?>sfia/pdf/developmentandimplementation.png"><div class="color-three">&nbsp;</div><span>Development and Implementation</span></li>
                                        <li class="pointer categoryInfo" info-link="<?php echo IMAGE_DIR_URL ?>sfia/pdf/deliveryandoperation.png"><div class="color-four">&nbsp;</div><span>Delivery and Operation</span></li>
                                        <li class="pointer categoryInfo" info-link="<?php echo IMAGE_DIR_URL ?>sfia/pdf/skillsandquality.png"><div class="color-five">&nbsp;</div><span>Skills and Quality</span></li>
                                        <li class="pointer categoryInfo" info-link="<?php echo IMAGE_DIR_URL ?>sfia/pdf/relationshipsandmanagement.png"><div class="color-six">&nbsp;</div><span>Relationships and Engagement</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <img src="<?php echo IMAGE_DIR_URL; ?>prev-health-check_1.jpg" alt="" class="img-responsive">
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
                            <?php $query = new WP_Query(['post_type' => 'sfia', 'post_status' => 'archived', 'meta_query' => [['key' => 'assigned_company', 'value' => $user_company_id]] ]); ?>
                            <?php if ($query->have_posts()) {
                                while ($query->have_posts()) {
                                    $query->the_post();
                                    $post_id = get_the_ID();
                                    echo '<tr>
                                        <td>' . advisory_get_form_name($post_id) . '</td>
                                        <td>'. get_the_date() .'</td>
                                        <td class="text-right">
                                            <a class="btn btn-primary" href="' . home_url('sfia-assessment/') . '?view=true" target="_blank"><span class="fa fa-eye"></span></a>';
                                            if (advisory_has_survey_edit_permission(get_the_ID())) {
                                                echo ' <a class="btn btn-warning" href="' . home_url('sfia-assessment/') . '?edit=true" target="_blank"><span class="fa fa-edit"></span></a>';
                                            }
                                            if (advisory_has_survey_delete_permission(get_the_ID())) {
                                                echo ' <a class="btn btn-danger delete-survey" href="#" data-id="'. $post_id .'" data-toggle="tooltip" title="Delete"><span class="fa fa-trash"></a>';
                                                if (advisory_is_survey_locked(get_the_ID(), get_current_user_id())) {
                                                    echo ' <a class="btn btn-success lock-survey" href="#" data-id="'.$post_id.'" data-user="' . get_current_user_id() . '" data-toggle="tooltip" title="Edit Permission"><span class="fa fa-lock"></a>';
                                                } else {
                                                    echo ' <a class="btn btn-danger lock-survey" href="#" data-id="'.$post_id.'" data-user="' . get_current_user_id() . '" data-toggle="tooltip" title="Edit Permission"><span class="fa fa-unlock-alt"></a>';
                                                }
                                            }
                                        echo '</td>
                                    </tr>';
                                }
                                wp_reset_postdata();
                            } else {echo '<tr> <td colspan="3">Nothing Found</td> </tr>'; } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <span class="spinner ajax-sfia-scorecard-select-spinner"></span>
                    <?php $query = new WP_Query(['post_type' => 'sfia', 'post_status' => ['publish','archived'], 'meta_query' => array(array('key' => 'assigned_company', 'value' => $user_company_id))]);
                        if ($query->have_posts()) {
                            $scorecardHTML = '';
                            while ($query->have_posts()) {
                                $query->the_post();
                                $published_users = get_post_meta($post->ID, 'published_users', true);
                                if (!empty($published_users)) {
                                    $archive_date = get_post_meta( $post->ID, 'archive_date', true);
                                    $date = !empty($archive_date) ? date('M d, Y', $archive_date) : get_the_date();
                                    if (!empty($teams)) {
                                        foreach ($teams as $team_id => $team_name) {
                                            if (!empty($published_users[$team_id])) {
                                                $scorecardHTML .= '<option value="'.$post->ID.'" team_id='.$team_id.' team_name='.$team_name.' type="general">'.advisory_get_form_name($post->ID).' - '.$team_name.' - '.$date. '</option>';
                                                $scorecardHTML .= '<option value="'.$post->ID.'" team_id='.$team_id.' team_name='.$team_name.' type="skillgap">'.advisory_get_form_name($post->ID).' Requred Skills - '.$team_name.' - '.$date. '</option>';
                                            }
                                        }
                                    }
                                }
                            }
                            wp_reset_postdata();
                            if ($scorecardHTML) { echo '<select name="" id="" class="ajax-sfia-scorecard-select bold">'.$scorecardHTML.'</select>'; }
                            else { echo '<img src="'.IMAGE_DIR_URL.'sfia/dashboard/no_scorecard.jpg">'; }
                            // else { echo '<h4 class="text-center text-danger">No scorecard found!</h4>'; }
                        } 
                    ?>
                    <div id="ajax-sfia-scorecard-data"> </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="SFIAAddUser">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-inverse">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Assign User Role</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label for="name">USER NAME :</label>
                        <select id="SFIAAddUserName" class="form-control">
                            <option value="">Select User</option>
                            <?php if (!empty($available_users)) {
                                foreach ($available_users as $sfiaUserId => $sfiaUserName) {
                                    echo '<option value="'.$sfiaUserId.'">'.$sfiaUserName.'</option>';
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="name">TEAM/GROUP :</label>
                        <select id="SFIAAddUserTeam" class="form-control" disabled>
                            <option value="">Select Team</option>
                            <?php if ($teams) {
                                foreach ($teams as $team_id => $team) {
                                    echo '<option value="'.$team_id.'">'.$team.'</option>';
                                }
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary SFIAAddUserBtn" post_id="<?php echo $sfia_id ?>">Save</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="SFIALevelInfoModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title text-capitalize">SFIA Title</h4>
            </div>
            <div class="modal-body p-0"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade modal-inverse" id="SFIACategoryInfoModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Category information</h4>
            </div>
            <div class="modal-body p-0"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade modal-inverse" id="SFIAReviewEditRoleModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Role information</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"> <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button> </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade modal-inverse" id="SFIAEditRoleModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Edit Role</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label for="name">USER NAME :</label>
                        <input class="SFIAEditRoleModalUser form-control" type="text" value="" user_id=0 disabled>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="name">TEAM/GROUP :</label>
                        <select class="form-control teamName" disabled>
                            <option value="">Select Team</option>
                            <?php if ($teams) {
                                foreach ($teams as $team_id => $team) {
                                    echo '<option value="'.$team_id.'">'.$team.'</option>';
                                }
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-danger SFIAEditRoleModalBack" post_id="<?php echo $sfia_id ?>">Back</button>
                <button type="button" class="btn btn-sm btn-primary SFIAEditRoleModalSubmit" post_id="<?php echo $sfia_id ?>">Save</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="allUsers">
    <div class="modal-dialog modal-inverse">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">User List</h4>
            </div>
            <div class="modal-body">
                <?php if ( empty($users) ) echo '<h3 class="text-center text-danger"> No user found</h3>';
                else {
                    $user_counter = 1;
                    echo '<div class="table-responsive" style="max-height: 600px;overflow-y: scroll;">';
                        echo '<table class="table table-bordered table-hover m-0">';
                            echo '<thead>';
                            echo '<tr>';
                                echo '<th class="t-heading-sky" style="font-size:18px;">SI</th>';
                                echo '<th class="t-heading-sky" style="font-size:18px;">Id</th>';
                                echo '<th class="t-heading-sky" style="font-size:18px;">Name</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';
                            foreach ($users as $user_id => $user_name) {
                                echo '<tr>';
                                    echo '<th>'.$user_counter.'</th>';
                                    echo '<th>'.$user_id.'</th>';
                                    echo '<th>'.$user_name.'</th>';
                                echo '</tr>';
                                $user_counter++;
                            }
                            echo '<tbody>';
                        echo '</table>';
                    echo '</div>';
                } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php get_footer(); ?>
<script>
(function($) {
    'use strict';
    function sfia_load_dashboard_scorecard(button) {
        var loader  = button.parents('.card-body').find('.spinner');
        var post_id = button.val();
        var type = button.find('option:selected').attr('type');
        var team_id = button.find('option:selected').attr('team_id');
        var team_name = button.find('option:selected').attr('team_name');
        if (post_id && team_id && team_name && type) {
            $.ajax({
                type: 'post',
                url: object.ajaxurl + '?action=sfia_dashboard_scorecard',
                cache: false,
                data: {
                    post_id: post_id,
                    team_id: team_id,
                    team_name: team_name,
                    type: type,
                    security: object.ajax_nonce
                },
                beforeSend: function() { button.attr('disabled', true); loader.addClass('is-active');},
                success: function(response, status, xhr) {
                    $('#ajax-sfia-scorecard-data').empty().html(response)
                    button.attr('disabled', false);
                    loader.removeClass('is-active');
                },
                error: function(error) {
                    button.attr('disabled', false);
                    loader.removeClass('is-active');
                }
            });
        }
    }
    $(document).on('click', '.sfiaDocuments li', function(e) {
        var pdfLink = $(this).attr('data-pdf');
        var title = $(this).attr('data-title');
        // alert(pdfLink); return false; 
        var activeSkill = $(this).parents('.skillContainer').find('.skills').val();
        var modal = $('#SFIALevelInfoModal');
        if (pdfLink.length > 0) { 
            modal.find('.modal-title').html(title);
            modal.find('.modal-body').html('<iframe src="'+pdfLink+'" frameborder="0" style="width:100%; height: 500px;"></iframe>');
        }
        else modal.find('.modal-body').html('<h3 class="text-center text-info">Information not found!</h3>');
        modal.modal('show');
    })
    $(document).on('click', '.categoryInfo', function(e) {
        var modal = $('#SFIACategoryInfoModal');
        var info_link = $(this).attr('info-link');
        var info_text = $(this).find('span').text();
        // alert(info_text); return false;
        if (info_link.length > 0) { 
            modal.find('.modal-title').html(info_text);
            modal.find('.modal-body').html('<img src="'+ info_link +'" alt="">');
        }
        else modal.find('.modal-body').html('<h3 class="text-center text-info">Information not found!</h3>');
        modal.modal('show');
    })
    $(document).on('click', '.add_sfia_user', function(e) {
        e.preventDefault();
        $('#SFIAAddUserName').val('');
        $('#SFIAAddUserTeam').html($('#sfia_team').html()).attr('disabled', false);
        $(this).closest('form').addClass('autoSubmit');
        $('#SFIAAddUser').modal('show');
    })
    $(document).on('click', '.SFIAAddUserBtn', function(e) {
        e.preventDefault();
        var button = $(this);
        var post_id = button.attr('post_id');
        var team_id = $('#SFIAAddUserTeam').val(); // modal user data
        var user_id = $('#SFIAAddUserName').val(); // modal user data
        var user_name = $('#SFIAAddUserName option:selected').text(); // modal user data
        if (post_id && team_id && user_id && user_name) {
            $.post(object.ajaxurl + '?action=sfia_add_team_user', {
                post_id: post_id,
                team_id: team_id,
                user_id: user_id,
                user_name: user_name,
                security: object.ajax_nonce
            }, function(response) {
                if (response == true) {
                    swal("Success!", "User has been added.", "success");
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000)
                } else {
                    swal("Error!", "Something went wrong.", "error");
                }
            })
        } else { swal('Error!','Both Name and Team/Group are required', 'error'); }
    })
    $(document).on('click', '.SFIAReviewEditRole', function(e) {
        e.preventDefault();
        var button = $(this);
        var modal = $('#SFIAReviewEditRoleModal');
        var team_id = button.attr('team_id');
        var company_id = <?php echo $user_company_id ?>;
        var post_id = <?php echo $sfia_id ?>;
        var team = button.text();
        var select_attr = "<?php echo $select_attr ?>";
        if (team_id && company_id && team_id) {
            $.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=sfia_get_team_users_table',
                cache: false,
                data: {
                    post_id: post_id, 
                    team_id: team_id, 
                    company_id: company_id, 
                    select_attr: select_attr, 
                    security: object.ajax_nonce 
                },
                beforeSend: function() { button.attr('disabled', true); },
                success: function(response, status, xhr) {
                    if (response) {
                        modal.find('.modal-title').html(team)
                        modal.find('.modal-body').html(response)
                        modal.modal('show');
                    }
                    button.attr('disabled', false);
                },
                error: function(error) {
                    button.attr('disabled', false);
                }
            })
        }  else { swal('Error!','Something went wrong please try again', 'error'); }
    })
    $(document).on('click', '.btnEditTeamUser', function(e) {
        e.preventDefault();
        var button = $(this);
        var old_modal = $('#SFIAReviewEditRoleModal');
        var modal = $('#SFIAEditRoleModal');
        var post_id = button.attr('post_id');
        var team_id = button.attr('team_id');
        var term_name = old_modal.find('.modal-title').text();
        var user_id = button.attr('user_id');
        var user_name = button.attr('user_name');
        var user_status = button.attr('user_status');
        var company_id = <?php echo $user_company_id ?>;
        if (team_id && user_id && user_name && company_id) {
            modal.find('.SFIAEditRoleModalUser').attr('user_id', user_id).val(user_name);
            modal.find('.teamName').attr('team_id', team_id).val(team_id).attr('disabled',false);
            modal.find('.SFIAEditRoleModalBack').attr({team_id: team_id, team_name: term_name });
            modal.attr({post_id: post_id, company_id: company_id, team_id: team_id, team_name: term_name, user_id: user_id, user_name: user_name, user_status: user_status });
            old_modal.modal('hide'); modal.modal('show');
        }  else { swal('Error!','Something went wrong please try again', 'error'); }
    })
    $(document).on('click', '.SFIAEditRoleModalBack', function(e) {
        e.preventDefault();
        var button = $(this);
        var old_modal = $('#SFIAReviewEditRoleModal');
        var current_modal = $('#SFIAEditRoleModal');

        var post_id = <?php echo $sfia_id ?>;
        var company_id = <?php echo $user_company_id ?>;
        var team_id = button.attr('team_id');
        var term_name = button.attr('team_name');
        if (post_id && team_id && company_id) {
            current_modal.modal('hide');
            $.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=sfia_get_team_users_table',
                cache: false,
                data: {
                    post_id: post_id, 
                    team_id: team_id, 
                    company_id: company_id, 
                    security: object.ajax_nonce 
                },
                beforeSend: function() { button.attr('disabled', true); },
                success: function(response, status, xhr) {
                    if (response) {
                        old_modal.find('.modal-title').html(term_name)
                        old_modal.find('.modal-body').html(response)
                        old_modal.modal('show');
                    }
                    button.attr('disabled', false);
                },
                error: function(error) {
                    button.attr('disabled', false);
                }
            })
        }  else { swal('Error!','Something went wrong please try again', 'error'); }
    })
    $(document).on('click', '.SFIAEditRoleModalSubmit', function(e) {
        e.preventDefault();
        var button = $(this);
        var modal = $('#SFIAEditRoleModal');
        var post_id = modal.attr('post_id');
        var old_team_id = modal.attr('team_id');
        var user_id = modal.attr('user_id');
        var user_name = modal.attr('user_name');
        var user_status = modal.attr('user_status');

        var new_team_id = modal.find('.teamName').val();
        
        if (post_id && new_team_id && old_team_id && user_id && user_name && user_status) {
            $.post(object.ajaxurl + '?action=sfia_change_team_user', {
                post_id: post_id,
                new_team_id: new_team_id,
                old_team_id: old_team_id,
                user_id: user_id,
                user_name: user_name,
                user_status: user_status,
                security: object.ajax_nonce
            }, function(response) {
                if (response == true) {
                    modal.modal('hide');
                    swal("Success!", "User team has been changed.", "success");
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000)
                } else {
                    swal("Error!", "Something went wrong.", "error");
                }
            })
        } else { swal('Error!','Both Name and Team/Group are required', 'error'); }
    })
    sfia_load_dashboard_scorecard($('.ajax-sfia-scorecard-select'));
    $(document).on('change', '.ajax-sfia-scorecard-select', function(e) {
        sfia_load_dashboard_scorecard($(this));
    })
}(jQuery))
</script>