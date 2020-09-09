<?php /* Template Name: IHC Register */
get_header();
$postType = 'ihcr';
global $user_switching;
if (current_user_can('administrator') || current_user_can('advisor') || $user_switching->get_old_user()) {
    $select_attr = '';
    $save_btn = true;
    $disabled = '';
} else if (get_the_author_meta( 'specialihcuser', get_current_user_id()) && empty($user_switching->get_old_user())) {
	$select_attr = 'disabled';
	$disabled = '';
    $save_btn = true;
} else {
	$disabled = 'disabled';
	$select_attr = 'disabled';
    $save_btn = false;
}
$summaryLength = 25;
echo '<script src="'.P3_TEMPLATE_URI.'/js/plugins/jquery.tinymce.min.js"></script>';

echo '<div class="content-wrapper">';
    pageTitleAndBreadcrumb('IHC Register', 'icon-ihcr.png');
    $company_id = advisory_get_user_company_id();
	if (advisory_metrics_in_progress($company_id, [$postType])) {
		$form_id = advisory_get_active_forms($company_id, [$postType]);
	} else {
		$id = new WP_Query([
			'post_type' => $postType,
			'post_status' => 'archived',
			'posts_per_page' => 1,
			'meta_query' => [['key' => 'assigned_company', 'value' => $company_id, ]],
			'fields' => 'ids',
		]);
		if ($id->found_posts > 0) {
			$form_id = $id->posts;
		}
	}
	if (empty($form_id[0])) echo '<p>' . __('Register Not Available Yet') . '</p>'; 
	else {
		$rr_data = advisory_get_formatted_drr_data($form_id[0]);
	    foreach ($rr_data as $rr) {
	    	if (@$_GET['cat'] != advisory_id_from_string($rr['cat'])) continue;
	        $base = @number_format($rr['base']) * 1000; 
	        foreach ($rr['areas'] as $area) {
	        	$base++;
		        $rr_id = advisory_id_from_string($area['name']) . '_dmmr_registry';
		        $default = advisory_company_default_values(advisory_get_user_company_id(), $rr_id);
		        $avg = IHCRAvgStatus($area['avg']);
		        echo '<div class="row">
		            <div class="col-md-12">
		                <form class="form rr-form" method="post" data-meta="' . $rr_id . '" data-id="'. advisory_get_user_company_id() .'">
		                    <div class="card">
		                        <div class="card-title-w-btn">
		                            <h4 class="title">Category: ' . $rr['cat'] . '<br>
		                            <small>' . $base . ' : ' . $area['name'] . '</small></h4>';
		                            if ($save_btn) { echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>'; }
	                            echo '</div>
		                        <div class="card-body">
		                            <div class="table-responsive table-dynamic-registry">
		                                <table class="table table-bordered table-survey mb-none">
		                                    <thead>
		                                        <tr>
		                                            <th class="t-heading-dark font-120p"><strong>Description</big></th>
		                                            <th class="t-heading-dark font-120p"><strong>Owner</big></th>
		                                            <th class="t-heading-l-dark font-120p">Last Assessment</th>
		                                            <th class="t-heading-l-dark text-center font-120p">Rating</th>
		                                        </tr>
		                                    </thead>
		                                    <tbody>
		                                        <tr>
		                                            <td class="no-padding" style="width:50%"> <textarea name="desc" class="form-control strong font-110p" '.$disabled.'>'.@$default['desc'].'</textarea> </td>
		                                            <td class="no-padding" style="width:25%"> <textarea name="owner" class="form-control strong font-110p" '.$select_attr.'>'.@$default['owner'].'</textarea> </td>
		                                            <td class="no-padding" style="width:15%"> <textarea name="last_assessment" class="form-control strong font-110p" '.$select_attr.'>'.@$default['last_assessment'].'</textarea> </td>
		                                            <th class="text-center font-120p ' . $avg['cls'] . '" style="width:10%">' . $avg['txt'] .'</th>
		                                        </tr>
		                                    </tbody>
		                                </table>';
		                                echo '<table class="table table-bordered table-survey mb-0">';
		                                    echo '<tbody>';
		                                        echo '<tr>';
		                                        	echo '<th class="t-heading-l-dark text-center font-120p w-50">Assessment Results</th>';
		                                        	echo '<th class="t-heading-l-dark text-center font-120p w-50">Recommendations</th>';
		                                        echo '</tr>';
		                                        echo '<tr>';
		                                			$bigComment2 = !empty($default['assessment']) ? $default['assessment'] : '';
			                                        echo '<td class="pointer bigComment2" isactive="'.$disabled.'" rowspan="3" height="50">';
                                                        echo '<span>'.get_excerpt($bigComment2, $summaryLength).'</span>';
                                                        echo '<textarea class="hidden bigComment_text" name="assessment" excerpt_length='.$summaryLength.' title="Assessment Results">'. htmlentities($bigComment2).'</textarea>';
                                                    echo '</td>';
		                                			$bigComment2 = !empty($default['recommendations']) ? $default['recommendations'] : '';
                                                    echo '<td class="pointer bigComment2" isactive="'.$disabled.'" rowspan="3" height="50">';
                                                        echo '<span>'.get_excerpt($bigComment2, $summaryLength).'</span>';
                                                        echo '<textarea class="hidden bigComment_text" name="recommendations" excerpt_length='.$summaryLength.' title="Recommendations">'. htmlentities($bigComment2).'</textarea>';
                                                    echo '</td>';
		                                        echo '</tr>';
		                                    echo '</tbody>';
		                                echo '</table>';
		                            echo '</div>
		                        </div>
		                    </div>
		                </form>
		            </div>
		        </div>';
	        }
	    }
	}
echo '</div>';
echo '<div class="modal fade" id="bigCommentModal2">';
    echo '<div class="modal-dialog modal-lg">';
        echo '<div class="modal-content modal-inverse">';
            echo '<div class="modal-header">';
                echo '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
                echo '<h4 class="modal-title">Comment</h4>';
            echo '</div>';
            echo '<div class="modal-body no-padding"></div>';
            echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
                echo '<button type="button" class="btn btn-primary saveBtn2">Save changes</button>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>'; ?>

<script>
jQuery(function($) {
	"use strict"

	// SUMMARY
    $(document).on('click', '.bigComment2', function(event) {
        event.preventDefault();
        $(this).addClass('active');

        var comment = $('.bigComment2.active .bigComment_text');
        var modal = $('#bigCommentModal2');
        var commentHTML = comment.val();
        var excerpt_length = comment.attr('excerpt_length');
        var title = comment.attr('title');
        var textareaSelector = modal.find('.modal-body');
        var textarea = $(textareaSelector);
        var is_active = $('.bigComment2.active').attr('isactive');

        $('#bigCommentModal2').find('.modal-title').html(title);
        if (is_active.length > 0) {
            modal.find('.saveBtn2').addClass('hide');
            textarea.html('<div style="font-size: 16px; padding: 15px;">'+ commentHTML +'</div>');
        } else {
            modal.find('.saveBtn2').removeClass('hide');
            textarea.html('<textarea rows="22" excerpt_length='+excerpt_length+'>'+ commentHTML +'</textarea>');
            tinymce();
        }

        modal.modal('show');
    });
    $('#bigCommentModal2').on('hide.bs.modal', function() {
        $('.bigComment2').removeClass('active');
        $(this).find('textarea').val('');
    });
    $('#bigCommentModal2 .saveBtn2').on('click', function() {
        var comment = $('#bigCommentModal2 textarea');
        var commentHTML = tinyMCE.activeEditor.getContent();
        var commentText = commentHTML.replace(/(<([^>]+)>)/ig,"");
        var excerptLength = comment.attr('excerpt_length');
        var excerpt = commentText.split(" ").length > excerptLength ? commentText.split(" ").splice(0,excerptLength).join(" ") + '...' : commentText;

        comment.html('');
        $('.bigComment2.active span').html(excerpt);
        $('.bigComment2.active .bigComment_text').val(commentHTML);
        $('#bigCommentModal2').modal('hide');
    });
    function tinymce() {
        tinyMCE.init({
            selector: '#bigCommentModal2 textarea',
            // content_style: 'div { margin: 10px; border: 5px solid red; padding: 3px; }',
            plugins: 'lists link autolink',
            toolbar: '"styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            menubar: false,
            branding: false,
            toolbar_drawer: 'floating',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            height:450
        });
    }

});
</script>
<?php get_footer();