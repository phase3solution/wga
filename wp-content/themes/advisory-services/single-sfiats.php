<?php
get_header();
global $sfia_premission;
if ( !$sfia_premission ) { exit(wp_redirect(home_url())); }
if ($sfia_premission == 'full') $select_attr = '';
else $select_attr = 'disabled';

$opts = get_post_meta(get_the_ID(), 'form_opts', true);
$transient_post_id = get_the_ID();
$summaryLength = 5;
?>
<script src="<?php echo P3_TEMPLATE_URI. '/js/plugins/jquery.tinymce.min.js'; ?>"></script>
<div class="content-wrapper">
    <!-- Page Title -->
    <div class="page-title">
        <div>
            <h1><?php echo (!empty($opts['icon']) ? '<img src="'. $opts['icon'] .'"> ' : '') ?><?php echo $opts['display_name'] ?></h1>
        </div>
        <?php if ($select_attr == '') {
            echo '<div>';
                echo '<a class="btn btn-lg btn-info btn-publish-sfiats" href="#" data-id="' . $transient_post_id . '">Publish</a>';
                echo '<a class="btn btn-lg btn-warning btn-reset-sfiats" href="#" data-id="' . $transient_post_id . '">Reset</a>';
            echo '</div>';
        } ?>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="#"><?php echo advisory_get_form_name(get_the_ID()) ?></a></li>
            </ul>
        </div>
    </div>
    <?php if ($opts['questions']) {
        $default = advisory_form_default_values($transient_post_id, 'poles');
        $out_of = count($opts['questions']) * 5;
        $avg = !empty($default['avg']) ? number_format($default['avg']).'%'  : 0;
        $total = advisory_get_sfiats_total($default, $opts['questions']);
        $rating_options = cs_get_option('criteria_sfiats');
        $rating_options = !empty($rating_options) ? advisory_select_array($rating_options) : [];

        echo '<div class="row">';
            echo '<div class="col-md-12">';
                echo '<form class="sfiats-form single" method="post" data-meta="poles" data-id="'.$transient_post_id.'">';
                    echo '<div class="card">';
                        echo '<div class="card-title-w-btn">
                                <h4 class="title">Questions</h4>
                                <div class="btn-group">';
                                    if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                                echo '</div>
                            </div>';
                        echo '<div class="card-body">';
                            echo '<div class="table-responsive">';
                                echo '<table class="table table-bordered table-survey table-single-criteria-sfiats">';
                                    echo '<thead>';
                                        echo '<tr>';
                                            echo '<th class="t-heading-dark text-center"><big><strong>Topic Area</strong></big></th>';
                                            echo '<th class="t-heading-dark text-center"><strong>Rating</strong>';
                                                echo '<br>(<span class="total">'.$total.'</span> out of '.$out_of.' / Avg: <span class="avg">'.$avg.'</span>)</th>';
                                            echo '<th class="t-heading-dark text-center"><big><strong>Response</strong></big></th>';
                                        echo '</tr>';
                                    echo '</thead>';
                                    echo '<tbody>';
                                        $counter = 1;
                                        foreach ($opts['questions'] as $question) {
                                            $name = trim($question['name']);
                                            $desc = trim($question['desc']);
                                            $rating_id = 'rating_'.$counter;
                                            $comment_id = 'comment_'.$counter;
                                            $summary = !empty($default[$comment_id]) ? $default[$comment_id] : '';

                                            $title = !empty($desc) ? '<strong>'.$name.'</strong> <i>('.$desc.')</i>' : $name;
                                            $default_value = !empty($default[$rating_id]) ? $default[$rating_id] : 0;
                                            echo '<tr>';
                                                echo '<td width="60%">'.$counter.'. '.$title.'</td>';
                                                echo '<td width="15%" class="no-padding text-center '.advisory_sfiats_get_class_by_value($default_value).'">';
                                                    echo advisory_opt_select($rating_id, $rating_id, '', $select_attr, $rating_options, $default_value);
                                                    echo '<input type="hidden" class="rating" value="1">';
                                                echo '</td>';
                                                echo '<td class="bigComment pointer" isactive="'.$select_attr.'"><span>'.get_excerpt($summary, $summaryLength).'</span><textarea name="'. $comment_id .'" class="hidden bigComment_text" excerpt_length='.$summaryLength.'>'. htmlentities($summary).'</textarea></td>';
                                            echo '</tr>';
                                            $counter++;
                                        }
                                    echo '</tbody>';
                                echo '</table>';
                            echo '</div>';
                        echo '</div>';
                        echo '<div class="card-footer text-right">';
                            echo '<input type="hidden" name="avg" class="hidden-sfiats-avg" value="'.@$default['avg'].'">';
                            if ($select_attr == '') echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
                        echo '</div>';
                    echo '</div>';
                echo '</form>';
            echo '</div>';
        echo '</div>';
    }
    if ($select_attr == '') {
        $pdfdefault = advisory_form_default_values($transient_post_id, 'pdf');
        $summary = !empty($pdfdefault['summary']) ? $pdfdefault['summary'] : '';
        echo '<div class="row">';
            echo '<div class="col-md-12">';
                echo '<form class="sfiats-form single" method="post" data-meta="pdf" data-id="'.$transient_post_id.'">';
                    echo '<div class="card">';
                        echo '<div class="card-title-w-btn"> <h4 class="title">Summary</h4> </div>';
                        echo '<div class="card-body"><textarea name="summary" class="tinymce-editor">'.$summary.'</textarea></div>';
                        echo '<div class="card-footer text-right"><button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button></div>';
                    echo '</div>';
                echo '</form>';
            echo '</div>';
        echo '</div>';
    }

echo '</div>';
?>
<div class="modal fade" id="bigCommentModal">
    <div class="modal-dialog modal-lg">
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
<script>
jQuery(function($) {
    "use strict";
    tinymce2();
    jQuery('.sfiats-form').on('submit', function(e) {
        e.preventDefault()
        var formData = jQuery(this).serialize()
        var formMeta = jQuery(this).attr('data-meta')
        var postID = jQuery(this).attr('data-id')
        jQuery(this).find('.btn-success').addClass('loading')
        jQuery.post(object.ajaxurl + '?action=sfiats_save', {
            data: formData,
            meta: formMeta,
            post_id: postID,
            security: object.ajax_nonce
        }, function(response) {
            console.log(response);
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
    $(document).on('click', '.btn-publish-sfiats', function(e) {
        e.preventDefault()
        var formID = $(this).attr('data-id');
        $.post(object.ajaxurl + '?action=validate_form_submission_sfiats', {
            form_id: formID,
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
                    $.post(object.ajaxurl + '?action=publish_sfiats', {
                        form_id: formID,
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
            } else swal("Error!", "Please fill out all sections", "error");
        })
    })
    $(document).on('click', '.btn-reset-sfiats', function(e) {
        e.preventDefault()
        var formID = $(this).attr('data-id')
        swal({
            title: "WARNING",
            text: "Activating a new assessment will reset all current values in the IT Management. Are you sure you want to proceed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4caf50",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function() {
            $.post(object.ajaxurl + '?action=reset_afiats', {
                security: object.ajax_nonce,
                form_id: formID,
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
    $('.table-single-criteria-sfiats select').on('change', function(e) {
        var form, val, total, count, avg;
        form = $(this).parents('form');
        total = count = avg = 0;
        val = $(this).val();

        $(this).parent('td').removeClass().addClass('no-padding text-center ' + advisory_sfiats_get_class_by_value(val)).find('.rating').val(val)
        $(this).parents('tbody').find('select').each(function() {
            total += Number($(this).val());
            count++;
        })
        avg = (total / (count * 5)) * 100;
        // console.log({total:total, count:count, avg:avg});
        form.find('.hidden-sfiats-avg').val(avg);
        form.find('.avg').html(avg.toFixed(0)+'%');
        form.find('.total').html(total);
    })
    $(document).on('click', '.bigComment', function(event) {
        event.preventDefault();
        $(this).addClass('active');

        var comment = $('.bigComment.active .bigComment_text');
        var modal = $('#bigCommentModal');
        var commentHTML = comment.val();
        var excerpt_length = comment.attr('excerpt_length');
        var title = comment.attr('title');
        var textareaSelector = modal.find('.modal-body');
        var textarea = $(textareaSelector);
        var is_active = $('.bigComment.active').attr('isactive');

        $('#bigCommentModal').find('.modal-title').html(title);
        if (is_active.length > 0) {
            modal.find('.saveBtn').addClass('hide');
            textarea.html('<div style="font-size:18px; font-family: Roboto, sans-serif;padding: 15px;">'+ commentHTML +'</div>');
        } else {
            modal.find('.saveBtn').removeClass('hide');
            // textarea.html('<textarea rows="18" excerpt_length='+excerpt_length+'>'+ commentHTML +'</textarea>');
            textarea.html('<textarea rows="18" class="no-border" excerpt_length='+excerpt_length+'>'+ commentHTML +'</textarea>');
            tinymce();
        }

        modal.modal('show');
    });
    $('#bigCommentModal .saveBtn').on('click', function() {
        var modal = $('#bigCommentModal');
        var modal_comment = $('#bigCommentModal textarea');
        var main_comment_area = $('.bigComment.active');

        var commentHTML = tinyMCE.activeEditor.getContent();
        var commentText = commentHTML.replace(/(<([^>]+)>)/ig,"");
        var excerptLength = modal_comment.attr('excerpt_length');
        var excerpt = commentText.split(" ").length > excerptLength ? commentText.split(" ").splice(0,excerptLength).join(" ") + '...' : commentText;

        modal_comment.html('');
        main_comment_area.find('span').html(excerpt);
        main_comment_area.find('.bigComment_text').val(commentHTML);
        modal.modal('hide');
    });
    $('#bigCommentModal').on('hide.bs.modal', function() {
        $('.bigComment').removeClass('active');
        $(this).find('textarea').val('');
    });
    function advisory_sfiats_get_class_by_value(val) {
        var cl = '';
        switch (val) {
            case '0': cl = 'bg-darkred'; break;
            case '1': cl = 'bg-red'; break;
            case '2': cl = 'bg-orange'; break;
            case '3': cl = 'bg-yellow'; break;
            case '4': cl = 'bg-green'; break;
            case '5': cl = 'bg-blue'; break;
            default : cl = 'bg-darkred'; break;
        }
        return cl
    }
    function tinymce2() {
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
            content_style: ".mce-content-body {font-size:16px; font-family: 'Roboto', sans-serif;}",
        });
    }
    function tinymce() {
        tinyMCE.init({
            selector: '#bigCommentModal textarea',
            height: 450,
            plugins: 'lists link autolink paste',
            toolbar: '"styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            menubar: false,
            branding: false,
            paste_as_text: true,
            toolbar_drawer: 'floating',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            content_style: ".mce-content-body {font-size:16px; font-family: 'Roboto', sans-serif;}",
        });
    }
});
</script>
<?php get_footer(); ?>