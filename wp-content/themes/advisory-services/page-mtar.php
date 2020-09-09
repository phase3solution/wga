<?php /* Template Name: MTA Register */
get_header();
$postType = 'mtar';
global $user_switching;
$preSelectedThreat = !empty($_GET['th']) ? $_GET['th'] : false;
$preSelectedSubThreat = !empty($_GET['sub']) ? $_GET['sub'] : false;
if (current_user_can('administrator') || current_user_can('advisor') || $user_switching->get_old_user()) {
    $select_attr = '';
	$disabled = '';
    $save_btn = true;
} else if (get_the_author_meta( 'specialmtauser', get_current_user_id()) && empty($user_switching->get_old_user())) {
	$select_attr = 'disabled';
	$disabled = '';
    $save_btn = true;
} else {
	$disabled = 'readonly';
	$select_attr = 'readonly';
    $save_btn = true;
}

$Pa_ca_Length = 50;
$summaryLength = 20;
$descriptionLength = 15;
echo '<script src="'.P3_TEMPLATE_URI.'/js/plugins/jquery.tinymce.min.js"></script>';

echo '<style>';
	echo 'textarea:read-only { cursor:not-allowed; }';
	echo '.areaWrapper {position: relative; }';
	echo '.mtarThreatSelect {position: absolute; top: -61px; width: 300px; left: 50%; margin-left: -150px; padding-left: 13px; cursor: pointer; }';
echo '</style>';
echo '<div class="content-wrapper">';
    pageTitleAndBreadcrumb('MTA Register', 'icon-'. $postType .'.png');
    $company_id = advisory_get_user_company_id();
	if (advisory_metrics_in_progress($company_id, [$postType])) {
		$form_id = advisory_get_active_forms($company_id, [$postType]);
	} else {
		$id = new WP_Query([
			'post_type' => $postType,
			'post_status' => 'archived',
			'posts_per_page' => 1,
			'meta_query' => [['key' => 'assigned_company', 'value' => $company_id]],
			'fields' => 'ids',
		]);
		if ($id->found_posts > 0) $form_id = $id->posts;
	}
	if (empty($form_id[0])) echo '<p>'. __('Register Not Available Yet') .'</p>';
	else {
		$lastAssessment = get_the_date("F j, Y", $form_id[0]);
		$rr_data = advisory_get_formatted_mtar_data($form_id[0]);
	    foreach ($rr_data as $rr) {
	    	if (@$_GET['cat'] != advisory_id_from_string($rr['cat'])) continue;
	        $base = @number_format($rr['base']) * 1000; 
		    echo '<div class="areaWrapper">';
		    	echo category_select_options($rr['areas'], $preSelectedThreat);
		        foreach ($rr['areas'] as $areaSI => $area) {
			        if (!empty($area['subthreats'])) {
			        	$areaID = advisory_id_from_string($area['name']);
			        	if ($preSelectedThreat) $isThreatAreaActive = $preSelectedThreat != $areaID ? ' hidden' : '';
				        else $isThreatAreaActive = $areaSI > 1 ? ' hidden' : '';
			        	echo '<div id="'.$areaID.'" class="areaContainer'.$isThreatAreaActive.'">';
		    				$subThreats = subcategory_select_options($areaID, $area['subthreats'], $preSelectedSubThreat);
				        	foreach ($area['subthreats'] as $subthreatSI => $subthreat) {
					        	$base++;
					        	$subthreatID = advisory_id_from_string($subthreat['name']);
						        $rr_id = $areaID . '_'. $subthreatID;
						        $default = advisory_company_default_values(advisory_get_user_company_id(), $rr_id);
						        // $avg = IHCRAvgStatus($subthreat['avg']);
						        $mtarAvg = ['cls'=>0, 'text'=>0];
                                if ($subthreat['avg'] == 'g') $mtarAvg = ['cls'=>ihcColorAVG($subthreat['avg']), 'text'=>'GAP'];
                                else $mtarAvg = ['cls'=>ihcColorAVG(number_format($subthreat['avg'], 1)), 'text'=>number_format($subthreat['avg'], 1)];
						        // select option
						        if ($preSelectedSubThreat) {
							        if ($areaID == $preSelectedThreat) $isSubThreatAreaActive = $preSelectedSubThreat != $subthreatID ? ' hidden' : '';
					        		else $isSubThreatAreaActive = $subthreatSI > 1 ? ' hidden' : '';
							        echo '<div id="'.$rr_id.'" class="row subThreatContainer'.$isSubThreatAreaActive.'">';
							            echo '<div class="col-md-12">';
							                echo '<form class="form rr-form" method="post" data-meta="' . $rr_id . '" data-id="'. advisory_get_user_company_id() .'" data-archivedby="'. get_current_user_id() .'">';
							                	echo '<input type="hidden" name="post_id" value="'. $form_id[0] .'">';
							                	echo '<input type="hidden" name="category_name" value="'. $rr['cat'] .'">';
							                	echo '<input type="hidden" name="threat_name" value="'. $area['name'] .'">';
							                	echo '<input type="hidden" name="subthreat_name" value="'.$subthreat['name'].'">';
							                    echo '<div class="card">';
							                        echo '<div class="card-title-w-btn">';
							                            echo '<h4 class="title">Category: ' . $rr['cat'] . '<br>';
							                            echo '<small>' . $base . ' : ' . $area['name'] . '</small></h4>';
							                            if ($save_btn) echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
						                            echo '</div>';
						                            echo '<div class="card-body">';
							                            echo '<div class="table-responsive table-mtar">';
							                                echo '<table class="table table-bordered table-survey mb-none">';
							                                    $pdfLink = site_url('mtar-pdf/').'?post='.$form_id[0].'&company='.$company_id.'&cat='.$_GET['cat'].'&th='.$areaID.'&subth='.$subthreatID;
						                                        echo '<tr>';
						                                        	echo '<th class="t-heading-sky no-margin" style="font-size: 20px;">'.$subThreats.'</th>';
						                                        	echo '<td class="t-heading-sky no-margin" style="width:50px"><a target="_blank" href="'.$pdfLink.'" class="btn btn-primary">PDF</a></td>';
						                                        echo '<tr>';
						                                    echo '</table>';
							                                echo '<table class="table table-bordered table-survey mb-none">';
							                                    echo '<thead>';
							                                        echo '<tr>';
							                                            echo '<th class="t-heading-dark font-120p"><strong>Description</strong></th>';
							                                            echo '<th class="t-heading-l-dark font-120p"><strong>Last Assessment</strong></th>';
							                                            echo '<th class="t-heading-l-dark text-center font-120p"><strong>Rating</strong></th>';
							                                        echo '</tr>';
							                                    echo '</thead>';
							                                    echo '<tbody>';
							                                        echo '<tr>';
							                                            // echo '<td class="no-padding" style="width:75%"> <textarea name="desc" class="form-control strong font-120p" '.$disabled.'>'.@$default['desc'].'</textarea> </td>';
							                                            $bigComment2 = !empty($default['desc']) ? $default['desc'] : '';
							                                            echo '<td class="pointer bigComment2" style="width:75%" isactive="'.$disabled.'" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $descriptionLength).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="desc" excerpt_length='.$descriptionLength.' title="Assessment Results">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                            echo '<td class="text-center" style="width:15%">'.$lastAssessment.'</td>';
							                                            echo '<td class="text-center font-120p ' . $mtarAvg['cls'] . '" style="width:10%"> ' . $mtarAvg['text'] .'</td>';
							                                        echo '</tr>';
							                                    echo '</tbody>';
							                                echo '</table>';
							                                echo '<table class="table table-bordered table-survey mb-0">';
							                                    echo '<tbody>';
							                                        echo '<tr>';
							                                        	echo '<th class="t-heading-l-dark text-center font-120p w-50">Assessment Results</th>';
							                                        	echo '<th class="t-heading-l-dark text-center font-120p w-50">Recommendations</th>';
							                                        echo '</tr>';
							                                        echo '<tr>';
							                                			$bigComment2 = !empty($default['assessment']) ? $default['assessment'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="'.$disabled.'" rowspan="3" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $summaryLength).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="assessment" excerpt_length='.$summaryLength.' title="Assessment Results">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                			$bigComment2 = !empty($default['recommendations']) ? $default['recommendations'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="'.$disabled.'" rowspan="3" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $summaryLength).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="recommendations" excerpt_length='.$summaryLength.' title="Recommendations">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                        echo '</tr>';
							                                    echo '</tbody>';
							                                echo '</table>';
							                                echo '<table class="table table-bordered table-mtar mb-none">';
							                                    echo '<thead>';
							                                        echo '<tr> <th colspan="4" class="t-heading-green no-margin text-center" style="font-size: 20px;font-weight: 700;">Customer Tracking</th> </tr>';
							                                        echo '<tr>';
							                                            echo '<th class="t-heading-dark font-120p" style="width:35%"><strong>Completed/Planned Activities</strong></th>';
							                                            echo '<th class="t-heading-l-dark font-120p" style="width:35%"><strong>Current Activities</strong></th>';
							                                            echo '<th class="t-heading-l-dark font-120p" style="width:30%" colspan="2"><strong>Accountable</strong></th>';
							                                        echo '</tr>';
							                                    echo '</thead>';
							                                    echo '<tbody>';
							                                        echo '<tr>';
							                                            $bigComment2 = !empty($default['pa']) ? $default['pa'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="editable" rowspan="3" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $Pa_ca_Length).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="pa" excerpt_length='.$Pa_ca_Length.' title="Completed/Planned Activities">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
				                                                        $bigComment2 = !empty($default['ca']) ? $default['ca'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="editable" rowspan="3" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $Pa_ca_Length).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="ca" excerpt_length='.$Pa_ca_Length.' title="Current Activities">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                            echo '<td class="no-padding font-120p" colspan="2"> <textarea name="owner" class="form-control strong font-120p">'.@$default['owner'].'</textarea> </td>';
							                                        echo '</tr>';
							                                        echo '<tr>';
							                                            echo '<th class="t-heading-l-dark text-center font-120p" style="width: 265px;"> Status </th>';
							                                            echo '<th class="t-heading-l-dark text-center font-120p"> Date Closed </th>';
							                                        echo '</tr>';
							                                        echo '<tr>';
							                                        	$statusOpts = ['1'=>'Not Required/Not Started', '2'=>'Work Deferred', '3'=>'Work In Progress','4'=>'Work Completed','5'=>'Reassessment Required'];
							                                            echo '<td class="no-padding font-120p" style="font-size: 18px;"> '.advisory_opt_select('status', 'status-'.$areaSI.$subthreatSI, '', '', $statusOpts, @$default['status']).' </td>';
							                                            echo '<td class="no-padding" style="width:80px;"> <textarea name="dc" class="form-control strong font-120p">'.@$default['dc'].'</textarea> </td>';
							                                        echo '</tr>';
							                                    echo '</tbody>';
							                                echo '</table>';
							                            echo '</div>';
							                        echo '</div>';
							                        echo '<div class="card-footer text-right">';
			                                        	if ($save_btn) echo '<button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
			                                        echo '</div>';
			                                            
							                    echo '</div>';
							                echo '</form>';
							            echo '</div>';
							        echo '</div>';
						        } else {
							        echo '<div id="'.$rr_id.'" class="row subThreatContainer">';
							            echo '<div class="col-md-12">';
							                echo '<form class="form rr-form" method="post" data-meta="' . $rr_id . '" data-id="'. advisory_get_user_company_id() .'" data-archivedby="'. get_current_user_id() .'">';
							                	echo '<input type="hidden" name="post_id" value="'. $form_id[0] .'">';
							                	echo '<input type="hidden" name="category_name" value="'. $rr['cat'] .'">';
							                	echo '<input type="hidden" name="threat_name" value="'. $area['name'] .'">';
							                	echo '<input type="hidden" name="subthreat_name" value="'.$subthreat['name'].'">';
							                    echo '<div class="card">';
							                        echo '<div class="card-title-w-btn">';
							                            echo '<h4 class="title">Category: ' . $rr['cat'] . '<br>';
							                            echo '<small>' . $base . ' : ' . $area['name'] . '</small></h4>';
							                            if ($save_btn) echo '<button class="btn btn-success" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
						                            echo '</div>';
						                            echo '<div class="card-body">';
							                            echo '<div class="table-responsive table-mtar">';
							                                echo '<table class="table table-bordered table-survey mb-none">';
						                                        $pdfLink = site_url('mtar-pdf/').'?post='.$form_id[0].'&company='.$company_id.'&cat='.$_GET['cat'].'&th='.$areaID.'&subth='.$subthreatID;
						                                        echo '<tr>';
						                                        	echo '<th class="t-heading-sky no-margin" style="font-size: 20px;">'.$subthreat['name'].'</th>';
						                                        	echo '<td class="t-heading-sky no-margin" style="width:50px"><a target="_blank" href="'.$pdfLink.'" class="btn btn-primary">PDF</a></td>';
						                                        echo '<tr>';
						                                    echo '</table>';
						                                    
							                                echo '<table class="table table-bordered table-survey mb-none">';
							                                    echo '<thead>';
							                                        echo '<tr>';
							                                            echo '<th class="t-heading-dark font-120p" style="width: 80%;"><strong>Description</strong></th>';
							                                            echo '<th class="t-heading-l-dark font-120p" style="width: 10%;"><strong>Last Assessment</strong></th>';
							                                            echo '<th class="t-heading-l-dark text-center font-120p" style="width: 10%;"><strong>Rating</strong></th>';
							                                        echo '</tr>';
							                                    echo '</thead>';
							                                    echo '<tbody>';
							                                        echo '<tr>';
							                                            $bigComment2 = !empty($default['desc']) ? $default['desc'] : '';
							                                            echo '<td class="pointer bigComment2" style="width:75%" isactive="'.$disabled.'" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $descriptionLength).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="desc" excerpt_length='.$descriptionLength.' title="Assessment Results">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                            echo '<td class="text-center" style="width:15%">'.$lastAssessment.'</td>';
							                                            echo '<td class="text-center font-120p '. $mtarAvg['cls'] .'" style="width:10%">'. $mtarAvg['text'] .'</td>';
							                                        echo '</tr>';
							                                    echo '</tbody>';
							                                echo '</table>';
							                                echo '<table class="table table-bordered table-survey mb-0">';
							                                    echo '<tbody>';
							                                        echo '<tr>';
							                                        	echo '<th class="t-heading-l-dark text-center font-120p w-50">Assessment Results</th>';
							                                        	echo '<th class="t-heading-l-dark text-center font-120p w-50">Recommendations</th>';
							                                        echo '</tr>';
							                                        echo '<tr>';
							                                			$bigComment2 = !empty($default['assessment']) ? $default['assessment'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="'.$disabled.'" rowspan="3" height="50">';
				                                                            // echo '<span class="strong font-120p">'; var_dump($bigComment2); echo '</span>';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $summaryLength).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="assessment" excerpt_length='.$summaryLength.' title="Assessment Results">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                			$bigComment2 = !empty($default['recommendations']) ? $default['recommendations'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="'.$disabled.'" rowspan="3" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $summaryLength).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="recommendations" excerpt_length='.$summaryLength.' title="Recommendations">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                        echo '</tr>';
							                                    echo '</tbody>';
							                                echo '</table>';
							                                echo '<table class="table table-bordered table-mtar mb-none">';
							                                    echo '<thead>';
							                                        echo '<tr> <th colspan="4" class="t-heading-green no-margin text-center" style="font-size: 20px;font-weight: 700;">Customer Tracking</th> </tr>';
							                                        echo '<tr>';
							                                            echo '<th class="t-heading-dark font-120p" style="width:35%"><strong>Completed/Planned Activities</strong></th>';
							                                            echo '<th class="t-heading-l-dark font-120p" style="width:35%"><strong>Current Activities</strong></th>';
							                                            echo '<th class="t-heading-l-dark font-120p" style="width:30%" colspan="2"><strong>Accountable</strong></th>';
							                                        echo '</tr>';
							                                    echo '</thead>';
							                                    echo '<tbody>';
							                                        echo '<tr>';
							                                        	$bigComment2 = !empty($default['pa']) ? $default['pa'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="editable" rowspan="3" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $Pa_ca_Length).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="pa" excerpt_length='.$Pa_ca_Length.' title="Completed/Planned Activities">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
				                                                        $bigComment2 = !empty($default['ca']) ? $default['ca'] : '';
								                                        echo '<td class="pointer bigComment2" isactive="editable" rowspan="3" height="50">';
				                                                            echo '<span class="strong font-120p">'.get_excerpt($bigComment2, $Pa_ca_Length).'</span>';
				                                                            echo '<textarea class="hidden bigComment_text" name="ca" excerpt_length='.$Pa_ca_Length.' title="Current Activities">'. htmlentities($bigComment2).'</textarea>';
				                                                        echo '</td>';
							                                            echo '<td class="no-padding font-120p" colspan="2"> <textarea name="owner" class="form-control strong font-120p">'.@$default['owner'].'</textarea> </td>';
							                                        echo '</tr>';
							                                        echo '<tr>';
							                                            echo '<th class="t-heading-l-dark text-center font-120p" style="width: 265px;"> Status </th>';
							                                            echo '<th class="t-heading-l-dark text-center font-120p"> Date Closed </th>';
							                                        echo '</tr>';
							                                        echo '<tr>';
							                                        	$statusOpts = ['1'=>'Not Required/Not Started', '2'=>'Work Deferred', '3'=>'Work In Progress','4'=>'Work Completed','5'=>'Reassessment Required'];
							                                            echo '<td class="no-padding font-120p" style="font-size: 18px;"> '.advisory_opt_select('status', 'status-'.$areaSI.$subthreatSI, '', '', $statusOpts, @$default['status']).' </td>';
							                                            echo '<td class="no-padding" style="width:80px;"> <textarea name="dc" class="form-control strong font-120p">'.@$default['dc'].'</textarea> </td>';
							                                        echo '</tr>';
							                                    echo '</tbody>';
							                                echo '</table>';
							                            echo '</div>';
							                        echo '</div>';
							                        echo '<div class="card-footer text-right">';
			                                        	if ($save_btn) echo '<button class="btn btn-success btn-submit-primary" type="submit"><i class="fa fa-lg fa-floppy-o"></i> Save</button>';
			                                        echo '</div>';
							                    echo '</div>';
							                echo '</form>';
							            echo '</div>';
							        echo '</div>';
						        }
				        	}
				        echo '</div>';
			        }
		        }
		    echo '</div>';
	    }
	}
	global $current_user;
	$archives = MTARegisterGetArchive($current_user->ID);
	echo '<div class="col-lg-8">'. $archives. '</div>';
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
                echo '<button type="button" class="btn btn-primary editBtn2 hide">Edit</button>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>'; ?>
<script>
jQuery(function($) {
	"use strict"
	// SUMMARY
    $(document).on('click', '.editBtn2', function(event) {
        event.preventDefault();
        var activeElement = $('.bigComment2.active');
        var comment = activeElement.find('.bigComment_text');
        var modal = $('#bigCommentModal2');
        var commentHTML = comment.val();
        var excerpt_length = comment.attr('excerpt_length');
        var textareaSelector = modal.find('.modal-body');
        var textarea = $(textareaSelector);
        var is_active = activeElement.attr('isactive');
        if (is_active.length > 0 && is_active == 'editable') {
	        modal.find('.editBtn2').addClass('hide');
	        modal.find('.saveBtn2').removeClass('hide');
	        textarea.html('<textarea rows="22" excerpt_length='+excerpt_length+'>'+ commentHTML +'</textarea>');
	        tinymce();
        }
    });
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
        if (is_active.length > 0 && is_active == 'editable') {
        	modal.find('.saveBtn2').addClass('hide');
        	modal.find('.editBtn2').removeClass('hide');
            textarea.html('<div class="popUpReadonlyContent">'+ commentHTML +'</div>');
        } else if (is_active.length > 0) {
        	modal.find('.editBtn2').addClass('hide');
            modal.find('.saveBtn2').addClass('hide');
            textarea.html('<div class="popUpReadonlyContent">'+ commentHTML +'</div>');
        } else {
        	modal.find('.editBtn2').addClass('hide');
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
        // var commentText = tinyMCE.activeEditor.getContent({format: 'text'}).split("&nbsp;").join(' ');
        var commentText = commentHTML.replace(/(<([^>]+)>)/ig,"").split("&nbsp;").join(' ');
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
            toolbar: 'styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            menubar: false,
            branding: false,
            toolbar_drawer: 'floating',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            height:450,
            content_style: 'body {font-size: 18px; color:#000; font-family: Arial;}'
        });
    }

});
</script>
<?php get_footer();