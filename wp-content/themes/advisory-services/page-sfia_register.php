<?php // Template Name: SFIA Register ?>
<?php get_header();
$post_id = get_the_ID();
$company_id = advisory_get_user_company_id();
$sfia_id = advisory_sfia_get_active_post_id($company_id);
if (empty($sfia_id)) echo '<p>'. __('SFIA Not Available Yet') .'</p>';
else {
    $sfiar_id = advisory_sfiar_get_active_post_id($company_id);
    $opts = get_post_meta($sfiar_id, 'form_opts', true);
    $company = get_term_meta($company_id, 'company_data', true);
    $teams = !empty($company['sfia_teams']) ? advisory_select_array($company['sfia_teams']) : [];
    $current_team_name = !empty($teams[$_GET['team']]) ? $teams[$_GET['team']] : false;
    $opts['display_name'] = !empty($opts['display_name']) ? $opts['display_name'] : get_the_title($post_id);
    $published_users = get_post_meta( $sfia_id, 'published_users', true);
?>
<script src="<?php echo P3_TEMPLATE_URI. '/js/plugins/jquery.tinymce.min.js'; ?>"></script>
<div class="content-wrapper sfiarWrapper">
    <div class="page-title" style="padding-bottom: 5px;">
        <div>
            <h1><img class="dashboardIcon" src="<?php echo get_template_directory_uri(); ?>/images/sfia/register.png" alt="SFIA Title Logo"> <?php echo $opts['display_name'] .' - <span style="font-size: 18px;">'. $current_team_name; ?><span></h1>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="#"><?php echo $opts['display_name']; ?></a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <select name="user" id="uniqueUser" class="from-control sfiarSlectUser">
                                <option value="">Select User</option>
                                <?php if (!empty($published_users)) {
                                    foreach ($published_users as $team_id => $team_users) {
                                        if ($team_id != $_GET['team']) continue;
                                        if (!empty($team_users)) {
                                            foreach ($team_users as $user_id => $user_name) {
                                                echo '<option team_id="'.$team_id.'" value="'.$user_id.'">'.$user_name.'</option>';
                                            }
                                        }
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row user_info"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row"><div class="col-sm-12 skills_form"></div></div>
    <div class="row"><div class="col-sm-12 skillsHistory"></div></div>
    <div class="row"><div class="col-sm-12 archives"></div></div>
</div>
<div class="modal fade" id="bigCommentModal">
    <div class="modal-dialog modal-llg">
        <div class="modal-content modal-inverse">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Comment</h4>
            </div>
            <div class="modal-body no-padding"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="archiveModal">
    <div class="modal-dialog modal-llg">
        <div class="modal-content modal-inverse">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Archive</h4>
            </div>
            <div class="modal-body no-padding" style="max-height: 650px;overflow-y: scroll;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="archiveCommentModal">
    <div class="modal-dialog modal-llg">
        <div class="modal-content modal-inverse">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Archive Comment - <span></span></h4>
            </div>
            <div class="modal-body bold" style="font-size: 16px;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary archiveBackBtn">Back</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<script>
(function($) {
    'use strict';
    $(document).on('change', '.sfiarSlectUser', function(e) {
        e.preventDefault()
        var button = $(this);
        var user_info = $('.user_info');
        var skills_form = $('.skills_form');
        var skillsHistory = $('.skillsHistory');
        var archives = $('.archives');
        var post_id = <?php echo $post_id; ?>;
        var sfia_id = <?php echo $sfia_id; ?>;
        var company_id = <?php echo $company_id; ?>;
        var team_id = button.find('option:selected').attr('team_id');
        var user_id = button.val();
        var user_name = button.find('option:selected').text();
        if ( post_id && company_id && team_id && user_id ) {
            $.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=sfiar_get_user_data',
                cache: false,
                data: {
                    security: object.ajax_nonce,
                    post_id: post_id,
                    sfia_id: sfia_id,
                    company_id: company_id,
                    team_id: team_id,
                    user_id: user_id,
                    user_name: user_name,
                },
                beforeSend: function() { 
                    button.attr('disabled', true);
                    user_info.html('<div class="col-sm-12">Loading ...</div>');
                    skills_form.html('<div class="col-sm-12">Loading ...</div>');
                    archives.html('<div class="col-sm-12">Loading ...</div>');
                    skillsHistory.html('');
                },
                success: function(response, status, xhr) {
                    if (response) {
                        user_info.html(response.user_info);
                        skills_form.html(response.skills_form);
                        archives.html(response.archives);
                    } else {
                        swal("Error!", "Something went wrong.", "error")
                    }
                    button.attr('disabled', false);
                },
                error: function(error) {
                    console.log(error);
                    user_info.html('');
                    skills_form.html('');
                    archives.html('');
                    button.attr('disabled', false);
                }
            })
        } else {
            user_info.html('');
            skills_form.html('');
            archives.html('');
            skillsHistory.html('');
        }
    })
    $(document).on('submit', '.sfiar-form', function(e) {
        e.preventDefault()
        var postID = $(this).attr('data-id')
        var user_id = $(this).attr('user_id')
        var formMeta = $(this).attr('data-meta')
        var formData = $(this).serialize()
        if ( postID && user_id && formMeta && formData ) {
            $(this).find('.btn-success').addClass('loading')
            $.post(object.ajaxurl + '?action=sfiar_save', {
                post_id: postID,
                user_id: user_id,
                meta: formMeta,
                data: formData,
                security: object.ajax_nonce
            }, function(response) {
                $('.btn-success').removeClass('loading')
                if (response == true) {
                    $.notify({
                        title: "Update Complete : ",
                        message: "Something cool is just updated!",
                        icon: 'fa fa-check'
                    }, {
                        type: "success"
                    })
                } else {
                    $.notify({
                        title: "Update Failed : ",
                        message: "Something wrong! Or you changed nothing!",
                        icon: 'fa fa-times'
                    }, {
                        type: "danger"
                    })
                }
            })
        }
    })
    $(document).on('click', '.bigComment', function(event) {
        event.preventDefault();
        $(this).addClass('activeComment');

        var comment = $('.bigComment.activeComment .bigComment_text');
        var modal = $('#bigCommentModal');
        var commentHTML = comment.val();
        var excerpt_length = comment.attr('excerpt_length');
        var title = comment.attr('title');
        var textareaSelector = modal.find('.modal-body');
        var textarea = $(textareaSelector);
        var is_active = $('.bigComment.activeComment').attr('isactive');

        $('#bigCommentModal').find('.modal-title').html(title);
        if (is_active.length > 0) {
            modal.find('.saveBtn').addClass('hide');
            textarea.html('<div style="font-size:18px; font-family: Roboto, sans-serif;padding: 15px;">'+ commentHTML +'</div>');
        } else {
            modal.find('.saveBtn').removeClass('hide');
            textarea.html('<textarea rows="18" class="no-border" excerpt_length='+excerpt_length+'>'+ commentHTML +'</textarea>');
            tinymce();
        }

        modal.modal('show');
    });
    $('#bigCommentModal .saveBtn').on('click', function() {
        var modal = $('#bigCommentModal');
        var modal_comment = $('#bigCommentModal textarea');
        var main_comment_area = $('.bigComment.activeComment');

        var commentHTML = tinyMCE.activeEditor.getContent();
        var commentText = commentHTML.replace(/(<([^>]+)>)/ig,"");
        var excerptLength = modal_comment.attr('excerpt_length');

        modal_comment.html('');
        main_comment_area.removeClass('bg-red bg-green');
        // main_comment_area.find('span').html(excerpt);
        if (commentText.length > 0) main_comment_area.addClass('bg-red');
        else main_comment_area.addClass('bg-green');
        main_comment_area.find('.bigComment_text').val(commentHTML);
        modal.modal('hide');
    });
    $('#bigCommentModal').on('hide.bs.modal', function() {
        $('.bigComment').removeClass('activeComment');
        $(this).find('textarea').val('');
    });
    $(document).on('change', '.sfia_assessment', function(event) {
        var button = $(this);
        var skillsHistory = $('.skillsHistory');
        var post_id = button.val();
        var user_id = button.attr('user_id');
        $('.technical_assessments').val('');
        if (post_id && user_id ) {
            $.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=sfiar_sfia_assessments_archives',
                cache: false,
                data: {
                    post_id: post_id,
                    user_id: user_id,
                    security: object.ajax_nonce
                },
                beforeSend: function() { button.attr('disabled', true); },
                success: function(response, status, xhr) {
                    skillsHistory.html(response);
                    button.attr('disabled', false);
                },
                error: function(error) { button.attr('disabled', false); swal("Error!", "Something went wrong.", "error") }
            })
        } else skillsHistory.html('');
    });
    $(document).on('change', '.technical_assessments', function(event) {
        var button = $(this);
        var skillsHistory = $('.skillsHistory');
        var post_id = button.val();
        $('.sfia_assessment').val('');
        if (!post_id) skillsHistory.html('');
        else {
            $.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=sfiar_technical_assessments_archives',
                cache: false,
                data: {
                    post_id: post_id,
                    security: object.ajax_nonce
                },
                beforeSend: function() { button.attr('disabled', true); },
                success: function(response, status, xhr) {
                    skillsHistory.html(response);
                    button.attr('disabled', false);
                },
                error: function(error) { button.attr('disabled', false); swal("Error!", "Something went wrong.", "error") }
            })
        }
    });
    $(document).on('click', '.showArchiveItem', function(event) {
        var button = $(this);
        var modal = $('#archiveModal');
        var post_id = button.attr('post_id');
        var user_id = button.attr('user_id');
        var archive_time = button.attr('archive_time');
        if (post_id && user_id && archive_time) {
            $.ajax({
                type: 'POST',
                url: object.ajaxurl + '?action=sfiar_get_archive_item',
                cache: false,
                data: {
                    post_id: post_id,
                    user_id: user_id,
                    archive_time: archive_time,
                    security: object.ajax_nonce
                },
                beforeSend: function() { button.attr('disabled', true); },
                success: function(response, status, xhr) {
                    if (!response) swal("Error!", "Something went wrong.", "error")
                    else {
                        modal.find('.modal-body').html(response);
                        modal.modal('show');
                    }
                    button.attr('disabled', false);
                },
                error: function(error) { button.attr('disabled', false); swal("Error!", "Something went wrong.", "error") }
            })
        }
    });
    $(document).on('click', '.deleteArchiveItem', function(event) {
        var button  = $(this);
        var table   = $(this).parents('table');
        var modal   = $('#bigCommentModal');
        var post_id = button.attr('post_id');
        var user_id = button.attr('user_id');
        var archive_time = button.attr('archive_time');
        if (post_id && user_id && archive_time) {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to revert this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4caf50",
                    confirmButtonText: "Yes, Delete!",
                    closeOnConfirm: false
                }, function() { 
                    $.ajax({
                        type: 'POST',
                        url: object.ajaxurl + '?action=sfiar_delete_archive_item',
                        cache: false,
                        data: {
                            post_id: post_id,
                            user_id: user_id,
                            archive_time: archive_time,
                            security: object.ajax_nonce
                        },
                        beforeSend: function() { button.attr('disabled', true); },
                        success: function(response, status, xhr) {
                            if (!response) swal("Error!", "Something went wrong.", "error")
                            else {
                                $('#archive_'+archive_time).remove();
                                swal({title: "Success!", text:"Archive is removed.", type: "success", timer: 2000 });
                            }
                        },
                        error: function(error) { button.attr('disabled', false); swal("Error!", "Something went wrong.", "error") }
                    })
                })
        }
    });
    $(document).on('click', '.showArchivedComment', function(event) {
        var button  = $(this);
        var table   = button.parents('table');
        var archiveModal   = $('#archiveModal');
        var commentModal   = $('#archiveCommentModal');
        var post_id = table.attr('post_id');
        var user_id = table.attr('user_id');
        var archive_time = table.attr('archive_time');
        var title = button.attr('title'); 
        var comment = button.find('.hidden').html(); 
        // console.log({post_id:post_id, user_id:user_id, archive_time:archive_time, title:title, comment:comment}); return false;
        if (post_id && user_id && archive_time) {
            archiveModal.modal('hide');
            commentModal.find('.archiveBackBtn').attr({post_id:post_id, user_id:user_id, archive_time:archive_time});
            commentModal.find('.modal-title span').html(title);
            commentModal.find('.modal-body').html(comment);
            commentModal.modal('show');
        }
    });
    $(document).on('click', '.archiveBackBtn', function(event) {
        $('#archiveCommentModal').modal('hide');
        $('#archiveModal').modal('show');
    });
    function tinymce() {
        tinyMCE.init({
            selector: '#bigCommentModal textarea',
            height: 450,
            plugins: 'lists link autolink',
            toolbar: 'styleselect fontsizeselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            // contextmenu: "link cut copy paste",
            // theme_advanced_font_sizes : "8pt,10pt,12pt,14pt,16pt,18pt,20pt,22pt,24pt,26pt,28pt,30pt,32pt,34pt,36pt",
            menubar: false,
            branding: false,
            toolbar_drawer: 'floating',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            content_style: ".mce-content-body {font-family: 'Roboto', sans-serif;}",
            // height:"350px",
            // width:"600px"
        });
    }
}(jQuery))
</script>
<?php get_footer(); ?>