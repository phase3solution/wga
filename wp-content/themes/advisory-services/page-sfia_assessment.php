<?php // Template Name: SFIA Assessment ?>
<?php 
global $sfia_premission;
// if (empty($sfia_premission)) { wp_redirect( home_url('/dashboard/')); exit(); }
get_header();
if (!empty($_GET['edit']) && $_GET['edit'] == true) {
    $select_attr = '';
    $publish_btn = true;
} else if (!empty($_GET['view']) && $_GET['view'] == true) {
    $select_attr = 'disabled';
    $publish_btn = false;
}  else if ($sfia_premission == 'full') {
    $select_attr = '';
    $publish_btn = true;
} else {
    $select_attr = 'disabled';
    $publish_btn = false;
}
$company_id = advisory_get_user_company_id();
$sfiaUsers = advisory_sfia_get_company_users($company_id);
$post_id = advisory_sfia_get_active_post_id($company_id);
if (empty($post_id)) echo '<p>'. __('SFIA Not Available Yet') .'</p>';
else {
    $opts = get_post_meta($post_id, 'form_opts', true);
    $opts['display_name'] = !empty($opts['display_name']) ? $opts['display_name'] : get_the_title($post_id);
    $head = advisory_form_default_values($post_id, 'head');
    $default_skills = advisory_form_default_values($post_id, 'skills');
    $skills_fit = 0;
    if (!empty($default_skills['avg'])) {
        $skills_fit = !empty($default_skills['avg']) ? $default_skills['avg'] : 0;
        unset($default_skills['avg']);
    }
    $company = get_term_meta($company_id, 'company_data', true);
    $teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
    $available_users = advisory_sfia_get_company_available_users($company, $post_id);

    // help($post_id);
    // $active_users = get_post_meta( $post_id, 'active_users', true); help($active_users);
    // $published_users = get_post_meta( $post_id, 'published_users', true); help($published_users);
    // update_post_meta( $post_id, 'active_users', $published_users);
    // delete_post_meta( $post_id, 'published_users');
?>
<!-- <style> .main-header, .main-sidebar{display: none;} </style> -->
<script src="<?php echo P3_TEMPLATE_URI. '/js/plugins/jquery.tinymce.min.js'; ?>"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="content-wrapper">
    <div class="page-title" style="padding-bottom: 5px;">
        <div>
            <h1><img class="dashboardIcon" src="<?php echo get_template_directory_uri(); ?>/images/sfia/sfia-logo.png" alt="SFIA Title Logo"> <?php echo $opts['display_name']; ?></h1>
        </div>
        <?php if ($select_attr == '') { 
            echo '<div>';
                echo '<a class="btn btn-lg btn-info btn-publish-user" href="#" data-id="'. $post_id .'" company_id='. $company_id .' user_id=0 disabled>Publish</a>'; 
                echo '<a class="btn btn-lg btn-danger btn-reset-assessment" href="#" data-id="'. $post_id .'" company_id='. $company_id .'>Reset All</a>';
                echo '<a class="btn btn-lg btn-warning btn-publish-assessment" href="#" data-id="'. $post_id .'" company_id='. $company_id .'>Publish All</a>';
            echo '</div>';
        } ?>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="#"><?php echo $opts['display_name']; ?></a></li>
            </ul>
        </div>
    </div>
    <div class="row"> <div class="col-sm-12 uniqueIdContainer"></div> </div>
    <div class="row">
        <div class="col-sm-12">
            <form class="survey-form" method="post" data-meta="head" data-id="<?php echo $post_id ?>">
                <div class="card">
                    <div class="card-body sfia-wrapper">
                        <div class="sfia-header">
                            <div class="row">
                                <div class="col-sm-1 p-0">
                                    <div class="logo"><a href="<?php echo site_url('sfia-dashboard/'); ?>"><img src="<?php echo IMAGE_DIR_URL ?>sfia/sfia-logo.png" alt="SFIA LOGO"></a></div>
                                </div>
                                <div class="col-sm-8 formContainer">
                                    <div class="row">
                                        <div class="col-sm-3 form-group teamGroupContainer">
                                            <label for="name"><img src="<?php echo IMAGE_DIR_URL ?>sfia/assessment/team.png" class="levelImg"></label>
                                            <?php if (!empty($teams)) {
                                                $load_published_users = !empty($_GET['edit']) || !empty($_GET['view']) ? 1 : 0;
                                                echo '<select id="sfia_team" load_published_users="'.$load_published_users.'">';
                                                echo '<option value="">Select Team</option>';
                                                foreach ($teams as $team_id => $teamName) {
                                                    echo '<option value="'.$team_id.'">'.$teamName.'</option>';
                                                }
                                                echo '</select>';
                                                
                                            } ?>
                                        </div>
                                        <div class="col-sm-3 form-group nameContainer">
                                            <label for="name"><img src="<?php echo IMAGE_DIR_URL ?>sfia/assessment/name.png" class="levelImg"></label>
                                            <select name="name" id="sfia_name" disabled><option value="" selected="">Select Name</option></select>
                                        </div>
                                        
                                        <div class="col-sm-5 form-group roleContainer"></div>
                                        <div class="col-sm-1 form-group levelContainer" data-toggle="tooltip"> </div>
                                        <div class="col-sm-12 form-group notesContainer"></div>

                                    </div>
                                </div>
                                <div class="col-sm-3">
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
                            <div class="row">
                                <div class="col-sm-1"> <div class="skillPointContainer"></div> </div>
                                <div class="col-sm-11"> <div class="sfiaTechnicalScoreContainer"></div> </div>
                            </div>
                        </div>
                        <br>
                        <div class="clearfix">
                            <?php if ($select_attr == '') echo '<button class="btn btn-primary pull-left add_sfia_user" type="button"><i class="fa fa-lg fa-plus"></i> Add User</button>'; ?>
                            <?php if ($select_attr == '') echo '<button class="btn btn-success pull-right" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>'; ?> 
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row"> <div class="col-sm-12 sfiaWrapper"> </div> </div>
    <?php if ($select_attr == '') echo '<div class="row"> <div class="col-sm-12 summaryWrapper"> </div> </div>'; ?>
</div>
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
            <div class="modal-footer"> <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button> </div>
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
                <h4 class="modal-title">Level information</h4>
            </div>
            <div class="modal-body p-0"></div>
            <div class="modal-footer"> <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button> </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="SFIAAddUser">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-inverse">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Add New User</h4>
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
                            <select id="SFIAAddUserTeam" class="form-control" disabled></select>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary SFIAAddUserBtn" post_id="<?php echo $post_id ?>">Save</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>
<script>
(function($) {
    'use strict';
    var publish_btn = $('.btn-publish-user')
    var reset_btn = $('.btn-reset-user')
    function sfia_reset_subcategory(skill){
        skill.find('.subcategory').html('<option value="">Sub-Category</option>').attr('disabled', true);
    }
    function sfia_reset_skills(skill){
        skill.find('.skills').html('<option value="">Skills</option>').attr('disabled', true);
    }
    function sfia_reset_code(skill){
        skill.find('.code').html('');
        skill.find('.SFIAInfoBtn').attr({'data-skill': '', 'disabled': true});
    }
    function sfia_reset_assess_level(skill){
        skill.find('.assessed_level').html('<option value="">Evaluation</option>').attr('disabled', true);
    }
    function sfia_reset_target_level(skill){
        skill.find('.target_level').html('<option value="">Target</option>').attr('disabled', true);
    }
    function sfia_activate_submit_btn() {
        var assessed_level = false;
        var target_level = false;
        $('.target_level').each(function() {
            let target_levelVal = $(this).val();
            if (target_levelVal == 'undefined' || target_levelVal == '') {
                target_level = true; return ;
            }
        });
        if (assessed_level || target_level) {$('.SFIASubmitBtn').attr('disabled', true);}
        else  { $('.SFIASubmitBtn').attr('disabled', false); }
    }
    function sfia_category_class(skillContainer, value=false) {
        var cls = '';
        if (value) {
            switch (value) {
                case 'strategy_and_architecture':       cls = 'color-one';     break;
                case 'change_and_transformation':       cls = 'color-two';     break;
                case 'development_and_implementation':  cls = 'color-three';   break;
                case 'delivery_and_operation':          cls = 'color-four';    break;
                case 'skills_and_quality':              cls = 'color-five';    break;
                case 'relationships_and_engagement':    cls = 'color-six';     break;
                default:                                cls = '';              break;
            }
        }
        skillContainer.removeClass().addClass('skillContainer '+ cls);
        // return cls;
    }
    function sfia_activate_remove_btn() {
        if ($('.skillWrapper .skillContainer').length > 1) { $('.SFIARemoveBtn').attr('disabled', false) } 
        else { $('.SFIARemoveBtn').attr('disabled', true) }
    }
    function sfia_calculate_skills_fit() {
        var rowCount = 0, total = 0, avg  = '', avg_value  = 'N/A', avg_text = 'N/A';
        $('.skillWrapper .skillContainer').each(function(index, element) {
            let ranks = $(this).find('.ranks').val();
            if (ranks == 'core') {
                let assessed_level = parseInt($(this).find('.assessed_level').val());
                let target_level = parseInt($(this).find('.target_level').val());
                if (!isNaN(assessed_level) && !isNaN(target_level)) {
                    total += advisory_sfia_calculate_level_scores(assessed_level, target_level);
                    rowCount++;
                }
            }
        })
        if (rowCount) { 
            avg = (total / rowCount) * 100; 
            avg_value = avg;
            avg_text = avg.toFixed(0) +'%';
        }
        // console.log({total:total, rowCount:rowCount, avg:avg});

        $('#skills_fit').val(avg);
        $('.skill-point').html(avg_text).removeClass().addClass(advisory_get_sfia_skills_fit_class(avg_value));
    }
    function advisory_get_sfia_skills_fit_class(avg) {
        var cls = 'skill-point ';
        if (avg == 'N/A'){ cls += 'bg-gap'; }
        else if (avg >= 100){ cls += 'bg-deepblue'; }
        else if (avg >= 90) { cls += 'bg-deepgreen'; }
        else if (avg >= 80) { cls += 'bg-yellow'; }
        else if (avg >= 70) { cls += 'bg-orange'; }
        else                { cls += 'bg-red'; }
        return cls;
    }
    function advisory_sfia_calculate_level_scores(assessed_level, target_level) {
        if (assessed_level && target_level && assessed_level > 0) {
            if (assessed_level >= target_level) { return 1; }
            else if (assessed_level == (target_level - 1)) { return 0.5; }
            else if (assessed_level == (target_level - 2)) { return 0.3; }
            else if (assessed_level == (target_level - 3)) { return 0.1; }
            else return 0;
        }
        return 0;
    }
    function advisory_get_sfia_access_level_container_class(assessed_level, target_level) {
        if (assessed_level && target_level && assessed_level > 0) {
            if (assessed_level > target_level)              { return 'bg-deepblue'; }
            else if (assessed_level == target_level)        { return 'bg-deepgreen'; }
            else if (assessed_level == (target_level - 1))  { return 'bg-yellow'; }
            else if (assessed_level == (target_level - 2))  { return 'bg-orange'; }
            else if (assessed_level == (target_level - 3))  { return 'bg-red'; }
            else                                            { return 'bg-red'; }
        }
        return 'bg-red';
    }
    function tinymce() {
        tinyMCE.init({
            selector: '.tinymce-editor',
            height: 220,
            plugins: 'lists link autolink paste',
            toolbar: 'bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            menubar: false,
            branding: false,
            paste_as_text: true,
            toolbar_drawer: 'floating',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            content_style: ".mce-content-body {font-size:16px;line-height:1;font-family: 'Roboto', sans-serif;}",
        });
    }
    $(document).on('click', '.SFIAInfoBtn', function(e) {
        var imgLink = $(this).attr('data-skill');
        // alert(imgLink); return false; 
        var activeSkill = $(this).parents('.skillContainer').find('.skills').val();
        var modal = $('#SFIALevelInfoModal');
        if (imgLink.length > 0) { modal.find('.modal-body').html('<iframe src="'+imgLink+'" frameborder="0" style="width:100%; height: 500px;"></iframe>');}
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
    $(document).on('change', '.categoryItem', function(e) {
        var button = $(this);
        var skill = button.parents('.skillContainer');
        var target = skill.find('.subcategory');
        var category = button.val();
        var ajaxData = {};
        if (category.length > 0) {
            sfia_category_class(skill, category);
            ajaxData.category = category;
            ajaxData.security = object.ajax_nonce;
            ajaxData.post_id = <?php echo $post_id; ?>;
            jQuery.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=get_sfia_subcategory',
                cache: false,
                data: ajaxData,
                beforeSend: function() { 
                    button.attr('disabled', true);
                    sfia_reset_subcategory(skill);
                    sfia_reset_skills(skill);
                    sfia_reset_code(skill);
                    sfia_reset_assess_level(skill);
                    sfia_reset_target_level(skill);
                    // sfia_category_class(skill);
                },
                success: function(response, status, xhr) {
                    if (response.length > 0) target.html(response).attr('disabled', false);
                    button.attr('disabled', false);
                },
                error: function(error) {
                    button.attr('disabled', false);
                }
            })
        } else {
            sfia_reset_subcategory(skill);
            sfia_reset_skills(skill);
            sfia_reset_code(skill);
            sfia_reset_assess_level(skill);
            sfia_reset_target_level(skill);
        }
    })
    $(document).on('change', '.subcategory', function(e) {
        var button = $(this);
        var skill = button.parents('.skillContainer');
        var target = skill.find('.skills');
        var subcategory = button.val();
        var ajaxData = {};
        if (subcategory.length > 0) {
            ajaxData.category = skill.find('.categoryItem').val();
            ajaxData.subcategory = subcategory;
            ajaxData.security = object.ajax_nonce;
            ajaxData.post_id = <?php echo $post_id; ?>;
            jQuery.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=get_sfia_skills',
                cache: false,
                data: ajaxData,
                beforeSend: function() { 
                    button.attr('disabled', true);
                    sfia_reset_skills(skill);
                    sfia_reset_code(skill);
                    sfia_reset_assess_level(skill);
                    sfia_reset_target_level(skill);
                },
                success: function(response, status, xhr) {
                    if (response.length > 0) target.html(response).attr('disabled', false);
                    button.attr('disabled', false);
                },
                error: function(error) {
                    button.attr('disabled', false);
                }
            })
        } else {
            sfia_reset_skills(skill);
            sfia_reset_code(skill);
            sfia_reset_assess_level(skill);
            sfia_reset_target_level(skill);
        }
    })
    $(document).on('change', '.skills', function(e) {
        var button = $(this);
        var skill = button.parents('.skillContainer');
        var target = skill.find('.skills');
        var skills = button.val();
        var code = button.find(':selected').attr('code');
        var ajaxData = {};
        if (skills.length > 0) {
            skill.find('.code').html(code);
            skill.find('.SFIAInfoBtn').attr({'data-skill': object.template_dir_url+'/images/sfia/info/'+code+'.pdf', 'disabled': false});
            ajaxData.category = skill.find('.categoryItem').val();
            ajaxData.subcategory = skill.find('.subcategory').val();
            ajaxData.skills = skills;
            ajaxData.security = object.ajax_nonce;
            ajaxData.post_id = <?php echo $post_id; ?>;
            jQuery.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=get_sfia_assessed_level',
                cache: false,
                data: ajaxData,
                beforeSend: function() { 
                    button.attr('disabled', true);
                    // sfia_reset_code(skill);
                    sfia_reset_assess_level(skill);
                    sfia_reset_target_level(skill);
                },
                success: function(response, status, xhr) {
                    if (response.length > 0) {
                        skill.find('.assessed_level').html('<option value="">Evaluation</option><option value="0">Missing</option>'+response).attr('disabled', false);
                        skill.find('.target_level').html('<option value="">Target</option>'+response).attr('disabled', false);
                    }
                    button.attr('disabled', false);
                },
                error: function(error) {
                    button.attr('disabled', false);
                }
            })
        } else {
            sfia_reset_code(skill);
            sfia_reset_assess_level(skill);
            sfia_reset_target_level(skill);
        }
    })
    $(document).on('change', '.assessed_level, .target_level, .ranks', function(e) {
        let skillContainer = $(this).parents('.skillContainer');
        let assessed_level = skillContainer.find('.assessed_level').val();
        let target_level = skillContainer.find('.target_level').val();
        let cls = advisory_get_sfia_access_level_container_class(assessed_level, target_level);
        skillContainer.find('.access_level_container').removeClass().addClass('access_level_container '+ cls);
        sfia_calculate_skills_fit();
        sfia_activate_submit_btn();
    })
    $(document).on('click', '.addMoreSkill', function(e) {
        var button = $(this);
        var skillWrapper = $('.skillWrapper');
        var skillCount = skillWrapper.find('.skillContainer').length;
        if (skillCount) {
            jQuery.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=get_sfia_skill_items',
                cache: false,
                data: {'post_id' : <?php echo $post_id ?>, 'counter': skillCount},
                beforeSend: function() { button.attr('disabled', true); },
                success: function(response, status, xhr) {
                    if (response.length > 0) {
                        skillWrapper.append(response);
                        $('.SFIASubmitBtn').attr('disabled', true);
                        $('.SFIARemoveBtn').attr('disabled', false);
                    }
                    button.attr('disabled', false);
                },
                error: function(error) {
                    button.attr('disabled', false);
                }
            })
        }
    })
    $(document).on('click', '.SFIARemoveBtn', function(e) {
        var button = $(this);
        $(this).parents('.skillContainer').remove();
        $('.skillWrapper .skillContainer').each(function(index, element) {
            // RESET ALL ID, NAME AND OTHER ATTRIBUTES
            $(this).attr({'id': 'skillContainer_'+index, 'skillcount': index});
            $(this).find('.categoryItem').attr({'id': 'category_'+index, 'name': 'category_'+index});
            $(this).find('.subcategory').attr({'id': 'subcategory_'+index, 'name': 'subcategory_'+index});
            $(this).find('.skills').attr({'id': 'skill_'+index, 'name': 'skill_'+index});
            $(this).find('.assessed_level').attr({'id': 'assess_level_'+index, 'name': 'assess_level_'+index});
            $(this).find('.target_level').attr({'id': 'target_level_'+index, 'name': 'target_level_'+index});
        })

        sfia_calculate_skills_fit();
        sfia_activate_submit_btn();
        sfia_activate_remove_btn();
    })
    $(document).on('change', '#sfia_team', function(e) {
        e.preventDefault();
        var name = $('#sfia_name');
        var button = $(this);
        var post_id = button.closest('form').attr('data-id');
        var company_id = <?php echo $company_id; ?>;
        var team_id = button.val();
        var load_published_users = button.attr('load_published_users');
        $.ajax({
            type: 'POST',
            url: object.ajaxurl + '?action=sfia_get_team_users',
            cache: false,
            data: {post_id: post_id, company_id: company_id, team_id: team_id, load_published_users: load_published_users, security: object.ajax_nonce },
            beforeSend: function() { 
                name.attr('disabled', true); 
                button.attr('disabled', true); 
                publish_btn.attr('disabled', true);
                reset_btn.attr('disabled', true);
                $('.notesContainer, .roleContainer, .levelContainer, .uniqueIdContainer, .skillPointContainer, .sfiaTechnicalScoreContainer, .sfiaWrapper, .summaryWrapper').html(''); 
            }, success: function(response, status, xhr) {
                if (response.length > 0) name.html(response).attr('disabled', false);
                button.attr('disabled', false);
            }, error: function(error) {
                button.attr('disabled', false);
            }
        })
    })
    $(document).on('change', '#sfia_name', function(e) {
        e.preventDefault();
        var button = $(this);
        var topForm = $(this).closest('form');
        var bottomForm = $('form.sfia-wrapper-bottom');
        var user_id = button.val();
        var post_id = button.closest('form').attr('data-id');
        var team_id = $('#sfia_team').val();
        var select_attr = "<?php echo $select_attr ?>";
        var archive = "<?php echo !empty($_GET['edit']) ? $_GET['edit'] : false ?>";
        var roleContainer = $('.roleContainer');
        var levelContainer = $('.levelContainer');
        var uniqueIdContainer = $('.uniqueIdContainer');
        var notesContainer = $('.notesContainer');
        var skillPointContainer = $('.skillPointContainer');
        var sfiaTechnicalScoreContainer = $('.sfiaTechnicalScoreContainer');
        var sfiaWrapper = $('.sfiaWrapper');
        var summaryWrapper = $('.summaryWrapper');
        // bottomForm.attr('data-meta', user_id+'_skills'); bottomForm.css('background', 'red'); return false;
        if (user_id) {
            $.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=sfia_get_team_user_form',
                cache: false,
                data: { post_id: post_id, team_id: team_id, user_id: user_id, select_attr: select_attr, archive: archive, security: object.ajax_nonce },
                beforeSend: function() { 
                    sfiaTechnicalScoreContainer.html('Loading...'); 
                    skillPointContainer.html('Loading...'); 
                    roleContainer.html('Loading...');
                    levelContainer.html('Loading...');
                    uniqueIdContainer.html('Loading...');
                    notesContainer.html('Loading...');
                    sfiaWrapper.html('Loading...');
                    summaryWrapper.html('Loading...');
                    publish_btn.attr('disabled', true);
                    reset_btn.attr('disabled', true);
                },
                success: function(response, status, xhr) {
                    if (response.length > 0) {
                        response =JSON.parse(response);
                        topForm.attr('data-meta', user_id+'_head'); 
                        bottomForm.attr('data-meta', user_id+'_skills'); 
                        roleContainer.html(response.role); 
                        levelContainer.html(response.level); 
                        uniqueIdContainer.html('<span style="font-size: 17px;font-weight: bold">ID : '+ user_id +'</span>'); 
                        notesContainer.html(response.notes); 
                        skillPointContainer.html(response.skillAvg); 
                        sfiaTechnicalScoreContainer.html(response.tsAvg); 
                        sfiaWrapper.html(response.bottom);
                        summaryWrapper.html(response.pdfsummary);
                        tinymce();

                        // ACTIVATE BUTTONS
                        publish_btn.attr({'user_id': user_id, 'disabled': false});
                        reset_btn.attr({'user_id': user_id, 'disabled': false});
                        $("#sortable").sortable({ helper: fixHelperModified, stop: updateIndex }).disableSelection();
                    } else {
                        topForm.attr('data-meta', 'head');
                        bottomForm.attr('data-meta', 'head');
                    }
                },
                error: function(error) {
                    roleContainer.html('Nothing Found');
                    levelContainer.html('Nothing Found');
                    uniqueIdContainer.html('Nothing Found');
                    notesContainer.html('Nothing Found');
                    sfiaTechnicalScoreContainer.html('Nothing Found');
                    skillPointContainer.html('Nothing Found');
                    sfiaWrapper.html('Nothing Found');
                    summaryWrapper.html('Nothing Found');
                }
            })
        } else {
            roleContainer.html('');
            levelContainer.html('');
            uniqueIdContainer.html('');
            notesContainer.html('');
            sfiaTechnicalScoreContainer.html('');
            skillPointContainer.html('');
            sfiaWrapper.html('');
            summaryWrapper.html('');

            publish_btn.attr('disabled', true);
            reset_btn.attr('disabled', true);
        }
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
        var modal = $('#SFIAAddUser');
        var button = $(this);
        var post_id = button.attr('post_id');
        var team_id = $('#SFIAAddUserTeam').val(); // modal user data
        var user_id = $('#SFIAAddUserName').val(); // modal user data
        var user_name = $('#SFIAAddUserName option:selected').text(); // modal user data
        if (post_id && team_id && user_id && user_name) {
            modal.modal('hide');
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
    $(document).on('click', '.levelContainer ul li a', function(e) {
        e.preventDefault();
        var level = $(this).attr('data-level');
        var container = $(this).parents('.levelContainer');
        container.find('input').val(level);
        container.find('.levelValue').html(level);
    })
    $(document).on('click', '.btn-publish-user', function(e) {
        e.preventDefault()
        var post_id = $(this).attr('data-id');
        var company_id = $(this).attr('company_id');
        var team_id = $('#sfia_team').val();
        var user_id = $(this).attr('user_id');
        $.post(object.ajaxurl + '?action=validate_form_submission_sfia', {
            post_id: post_id,
            company_id: company_id,
            team_id: team_id,
            user_id: user_id,
            security: object.ajax_nonce
        }, function(response) {
            if (response == true) {
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to revert this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4caf50",
                    confirmButtonText: "Yes, Publish!",
                    closeOnConfirm: false
                }, function() { 
                    $.post(object.ajaxurl + '?action=sfia_publish_user', {
                        post_id: post_id,
                        company_id: company_id,
                        team_id: team_id,
                        user_id: user_id,
                        security: object.ajax_nonce
                    }, function(response) {
                        // console.log(response);return false; 
                        if (response == true) {
                            swal("Success!", "Your draft survey has been published.", "success");
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000)
                        } else {
                            swal("Error!", "Something went wrong.", "error");
                        }
                    })
                })
            } else {
                swal({
                    title: "Please fill out all sections",
                    text: "Please make sure all sections have been completed",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#4caf50",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                })
            }
        })
    })
    $(document).on('click', '.btn-reset-assessment', function(e) {
        e.preventDefault()
        var post_id = jQuery(this).attr('data-id')
        swal({
            title: "WARNING",
            text: "Activating a new assessment will reset all current values. Are you sure you want to proceed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4caf50",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function() {
            jQuery.post(object.ajaxurl + '?action=sfia_reset_assessment', {
                security: object.ajax_nonce,
                post_id: post_id,
            }, function(response) {
                if (response == true) {
                    swal("Success!", "New Assessment has been activated", "success")
                    setTimeout(function() {
                        window.location.reload()
                    }, 2000)
                } else {
                    swal("Error!", "Something went wrong.", "error")
                }
            })
        })
    })
    $(document).on('click', '.btn-publish-assessment', function(e) {
        e.preventDefault()
        var formID = jQuery(this).attr('data-id')
        var company_id = jQuery(this).attr('company_id')
        jQuery.post(object.ajaxurl + '?action=validate_form_submission_sfia_assessment', {
            post_id: formID,
            company_id: company_id,
            security: object.ajax_nonce
        }, function(response) { 
            console.log(response)
            if (response == true) {
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to revert this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4caf50",
                    confirmButtonText: "Yes, Publish!",
                    closeOnConfirm: false
                }, function() {
                    jQuery.post(object.ajaxurl + '?action=sfia_publish_assessment', {
                        post_id: formID,
                        company_id: company_id,
                        security: object.ajax_nonce
                    }, function(response) {
                        if (response == true) {
                            swal("Success!", "Your draft survey has been published.", "success");
                            setTimeout(function() {
                                window.location.href = object.home_url+'/sfia-dashboard/';
                            }, 2000)
                        } else {
                            swal("Error!", "Something went wrong.", "error");
                        }
                    })
                })
            } else {
                swal({
                    title: "Please fill out all active users",
                    text: "Please make sure all sections have been completed",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#4caf50",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                })
            }
        })
    })
    $(document).on('submit', '.sfia-form', function(e) {
        e.preventDefault()
        var formData = jQuery(this).serialize()
        var formMeta = jQuery(this).attr('data-meta')
        var postID = jQuery(this).attr('data-id')
        jQuery(this).find('.btn-success').addClass('loading')
        jQuery.post(object.ajaxurl + '?action=save_survey', {
            data: formData,
            meta: formMeta,
            post_id: postID,
            security: object.ajax_nonce
        }, function(response) {
            jQuery('.btn-success').removeClass('loading')
            if (response == true) {
                jQuery.notify({
                    title: "Update Complete : ",
                    message: "Something cool is just updated!",
                    icon: 'fa fa-check'
                }, {
                    type: "success"
                })
            } else {
                jQuery.notify({
                    title: "Update Failed : ",
                    message: "Something wrong! Or you changed nothing!",
                    icon: 'fa fa-times'
                }, {
                    type: "danger"
                })
            }
        })
    })
    var fixHelperModified = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    },
    updateIndex = function(e, ui) {
        $('td.move', ui.item.parent()).each(function (i) {
            $(this).html(i + 1);
            var skill = $(this).parents('.skillContainer');
            skill.attr({'id': 'skillContainer_'+i, 'skillcount': i});
            skill.find('.category_container .categoryItem').attr({'id': 'category_'+i, 'name': 'category_'+i})
            skill.find('.subcategory').attr({'id': 'subcategory_'+i, 'name': 'subcategory_'+i})
            skill.find('.skills').attr({'id': 'skill_'+i, 'name': 'skill_'+i})
            skill.find('.code').attr({'id': 'code_'+i})
            skill.find('.ranks').attr({'id': 'rank_'+i, 'name': 'rank_'+i})
            skill.find('.target_level').attr({'id': 'target_level_'+i, 'name': 'target_level_'+i})
            skill.find('.assessed_level').attr({'id': 'assess_level_'+i, 'name': 'assess_level_'+i})
        });
        sfia_activate_submit_btn();
    };

    // $("#sortable").sortable({ helper: fixHelperModified, stop: updateIndex }).disableSelection();
}(jQuery))
</script>
<?php get_footer(); ?>