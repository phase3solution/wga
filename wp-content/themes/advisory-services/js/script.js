jQuery(document).ready(function(argument) {
	jQuery(document).on('click', '.exportReportPDF', function(e) {
		e.preventDefault();
        var button = jQuery(this);
        var postID = button.attr('postid');
        jQuery.ajax({
			type: 'POST',
			url: object.ajaxurl + '?action=export_report_pdf',
			cache: false,
			data: {id: postID, security: object.ajax_nonce },
			beforeSend: function() { button.attr('disabled', true); },
			success: function(response, status, xhr) {
				if (response != 200) alert('Something went wrong. Please try again.');
				button.attr('disabled', false);
			},
			error: function(error) {
				button.attr('disabled', false);
			}
		})
	})
	jQuery(document).on('click', '.uploadBtn', function(e) {
		e.preventDefault();
        var button = jQuery(this);
        var image = wp.media({
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            jQuery(button).parents('.uploadWrapper').find('.uploadUrl').val( image_url );
        });
	})
	jQuery(document).on('click', '.addNewAccordion', function(e) {
		e.preventDefault();
		var button 	= jQuery(this);
		var items 	= button.attr('items');
		var id 		= button.attr('id');
		var title 	= button.attr('btntitle');
		var asi 	= button.attr('asi');

		jQuery.ajax({
			type: 'POST',
			url: object.ajaxurl + '?action=new_accordion',
			cache: false,
			data: {
				id: id,
				title: title,
				items: items,
				asi: asi,
				security: object.ajax_nonce
			},
			beforeSend: function() { button.attr('disabled', true); },
			success: function(response, status, xhr) {
				response = JSON.parse(response);
				if (response.status == 200) {
					button.attr('asi', response.asi);
					button.parents('.panelGroup').find('.accordionWrapper').append(response.html);
				} else alert('Something went wrong. Please try again.');
				button.attr('disabled', false);
			},
			error: function(error) {
				button.attr('disabled', false);
			}
		})
	})
	jQuery(document).on('click', '.tabpanelRemoveBtn', function(e) {
		e.preventDefault();
		jQuery(this).parents('.panelWrapper').remove();
	})
	// Coming Soon
	jQuery('.coming-soon').on('click', function(e) {
		e.preventDefault()
		swal("Coming Soon", "Please be patient by this time", "info")
	})
	// Login
	jQuery('.login-form').on('submit', function(e) {
		e.preventDefault()
		var email = jQuery('#email').val()
		var pass = jQuery('#password').val()
		var remember = jQuery('#remember').is(':checked')
		if (email != '' && pass != '') {
			jQuery.ajax({
				type: 'POST',
				url: object.ajaxurl + '?action=user_login',
				cache: false,
				data: {
					email: email,
					pass: pass,
					remember: remember,
					security: object.ajax_nonce
				},
				success: function(response, status, xhr) {
					if (response == false) {
						jQuery.notify({
							title: "Invalid Credential : ",
							message: "Please type correct email and password!",
							icon: 'fa fa-times'
						}, {
							type: "danger"
						})
					} else {
						window.location.reload()
					}
				}
			})
			// jQuery.post(object.ajaxurl + '?action=user_login', {
			// 	email: email,
			// 	pass: pass,
			// 	remember: remember,
			// 	security: object.ajax_nonce
			// }, function(response) {
			// 	if (response == false) {
			// 		jQuery.notify({
			// 			title: "Invalid Credential : ",
			// 			message: "Please type correct email and password!",
			// 			icon: 'fa fa-times'
			// 		}, {
			// 			type: "danger"
			// 		})
			// 	} else {
			// 		window.location.reload()
			// 	}
			// })
		} else {
			jQuery.notify({
				title: "Invalid Credential : ",
				message: "Please type correct email and password!",
				icon: 'fa fa-times'
			}, {
				type: "danger"
			})
		}
	})
	// Menu
	jQuery('.sidebar-menu li').each(function() {
		var href = jQuery(this).find('a').attr('href')
		var win = window.location.href
		if (win.indexOf(href) != -1) {
			jQuery(this).addClass('active')
		}
	})
	// switch user
	jQuery('.switch-user').on('change', function() {
		var url = jQuery(this).val()
		setTimeout(function() {
			window.location.replace(url)
		}, 1000)
	})
	// Reset Survey
	jQuery('.reset-survey').on('click', function(e) {
		e.preventDefault()
		swal({
			title: "WARNING",
			text: "Activating a new assessment will reset all current values in the IHC. Are you sure you want to proceed?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#4caf50",
			confirmButtonText: "Yes",
			closeOnConfirm: false
		}, function() {
			jQuery.post(object.ajaxurl + '?action=reset_survey', {
				security: object.ajax_nonce
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
	// Delete Survey
	jQuery('.delete-survey').on('click', function(e) {
		e.preventDefault()
		var postID = jQuery(this).attr('data-id')
		swal({
			title: "Are you sure?",
			text: "You are going to delete a survey. You will not be able to revert this action",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#4caf50",
			confirmButtonText: "Delete",
			closeOnConfirm: false
		}, function() {
			jQuery.post(object.ajaxurl + '?action=delete_survey', {
				post_id: postID,
				security: object.ajax_nonce
			}, function(response) {
				if (response == true) {
					swal("Success!", "Survey has been deleted.", "success")
					setTimeout(function() {
						window.location.reload()
					}, 2000)
				} else {
					swal("Error!", "Something went wrong.", "error")
				}
			})
		})
	})
	// Lock Survey
	jQuery('.lock-survey').on('click', function(e) {
		e.preventDefault()
		var target = jQuery(this)
		var postID = jQuery(this).attr('data-id')
		var userID = jQuery(this).attr('data-user')
		jQuery.post(object.ajaxurl + '?action=lock_survey', {
			post_id: postID,
			user_id: userID,
			security: object.ajax_nonce
		}, function(response) {
			if (response == true) {
				target.removeClass('btn-danger').addClass('btn-success').html('<span class="fa fa-lock">')
			} else if (response == false) {
				target.removeClass('btn-success').addClass('btn-danger').html('<span class="fa fa-unlock-alt">')
			}
		})
	})
	// Load Survey
	load_dashboard_scorecard(jQuery('.ajax-scorecard-select'))
	jQuery('.ajax-scorecard-select').on('change', function() {
		var postID = jQuery(this).val()
		var type = jQuery(this).find('option:selected').attr('type')
		if (type == 'report') load_dashboard_reportcard(postID)
		else if (type == 'cloud') load_dashboard_cloud_reportcard(postID)
		else if (type == 'mta_register') load_dashboard_mtar_statuscard(jQuery(this))
		else if (type == 'report_card') load_dashboard_report_card_scorecard(jQuery(this))
		else if (type == 'catalogue_summary') load_dashboard_report_card_scorecard(jQuery(this))
		else load_dashboard_scorecard(jQuery(this))
	})
	load_eva_dashboard_scorecard(jQuery('.ajax-eva-select').val())
	jQuery('.ajax-eva-select').on('change', function() {
		var postID = jQuery(this).val()
		var type = jQuery(this).find('option:selected').attr('type')
		if (type == 'report') load_dashboard_reportcard(postID)
		else if (type == 'cloud') load_dashboard_cloud_reportcard(postID)
		else load_eva_dashboard_scorecard(postID)
	})
	// Profile Page
	jQuery.uploadPreview({
		input_field: "#image-upload",
		preview_box: "#image-preview",
		label_field: "#image-label"
	})
	jQuery(".profile-form").validate({
		rules: {
			f_name: {
				required: true,
			},
			l_name: {
				required: true,
			},
			email: {
				required: true,
				email: true
			},
			password: {
				minlength: 6
			}
		}
	})
	jQuery('.profile-form').on('submit', function(e) {
		e.preventDefault()
		var valid = jQuery(this).valid();
		if (valid == true) {
			jQuery('.btn-success').addClass('loading')
			var formData = new FormData(jQuery(this)[0]);
			formData.append('data', jQuery(this).serialize())
			formData.append('security', object.ajax_nonce)
			jQuery.ajax({
				url: object.ajaxurl + '?action=update_profile',
				type: 'POST',
				data: formData,
				success: function(response) {
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
						var msg
						jQuery(response.errors).each(function(index, value) {
							var errs = Object.values(value)
							jQuery(errs).each(function(i, v) {
								jQuery.notify({
									title: "Update Failed : ",
									message: v[i],
									icon: 'fa fa-times'
								}, {
									type: "danger"
								})
							})
						})
					}
				},
				cache: false,
				contentType: false,
				processData: false
			});
		}
	})
	// publish survey
	jQuery('.btn-publish').on('click', function(e) {
		e.preventDefault()
		var formID = jQuery(this).attr('data-id')
		var bia = jQuery(this).hasClass('is-bia')
		jQuery.post(object.ajaxurl + '?action=validate_form_submission', {
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
					jQuery.post(object.ajaxurl + '?action=publish_survey', {
						form_id: formID,
						security: object.ajax_nonce
					}, function(response) {
						if (response == true) {
							swal("Success!", "Your draft survey has been published.", "success");
							setTimeout(function() {
								window.location.href = object.home_url
							}, 2000)
						} else {
							swal("Error!", "Something went wrong.", "error");
						}
					})
				})
			} else {
				if (bia) {
					// if bia allow to publish
					swal({
						title: "Survey Incomplete!",
						text: "You will not be able to revert this action",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#4caf50",
						confirmButtonText: "Yes, Publish!",
						closeOnConfirm: false
					}, function() {
						jQuery.post(object.ajaxurl + '?action=publish_survey', {
							form_id: formID,
							security: object.ajax_nonce
						}, function(response) {
							if (response == true) {
								swal("Success!", "Your draft survey has been published.", "success");
								setTimeout(function() {
									window.location.href = object.home_url
								}, 2000)
							} else {
								swal("Error!", "Something went wrong.", "error");
							}
						})
					})
				} else {
					swal("Error!", "Please fill out all sections", "error")
				}
			}
		})
	})
	// save all
	jQuery('.btn-save-all').on('click', function(e) {
		e.preventDefault()
		jQuery(document).find('.btn-submit-primary').click()
		// validate btn
		form_id = jQuery('.btn-publish').attr('data-id')
		validate_publish_btn_color(form_id)
	})
	// reset all - single form
	jQuery('.btn-reset-all').on('click', function(e) {
		e.preventDefault()
		formID = jQuery('.btn-publish').attr('data-id')
		area = jQuery(this).attr('area')
		swal({
			title: "WARNING",
			text: "Activating a new assessment will reset all current values in the IT Management. Are you sure you want to proceed?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#4caf50",
			confirmButtonText: "Yes",
			closeOnConfirm: false
		}, function() {
			jQuery.post(object.ajaxurl + '?action=reset_survey', {
				security: object.ajax_nonce,
				form_id: formID,
				area: area,
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
	// Multiple Form Calc on init
	jQuery('.table-multiple-criteria tbody tr').each(function() {
		var rsum = wsum = sum = count = 0
		jQuery(this).find('td:has(select)').each(function() {
			var val = jQuery(this).find('select').val()
			if (val != 0) {
				count += 1
				rsum += (Number(jQuery(this).attr('data-w')) * Number(val))
				wsum += Number(jQuery(this).attr('data-w'))
				sum += Number(val)
			}
			jQuery(this).removeClass().addClass('no-padding text-center ' + colorByValue(val))
		})
		var avg = count == 0 ? 0 : (sum / count)
		var wavg = wsum == 0 ? 0 : (rsum / wsum)
		jQuery(this).find('.rating').html(avg.toFixed(1)).parent('td').removeClass().addClass('text-center ' + colorByValue(avg, 'avg'))
		jQuery(this).find('.adjusted-rating').html(wavg.toFixed(1)).parent('td').removeClass().addClass('text-center ' + colorByValue(wavg, 'avg'))
	})
	// Single Form Calc on Init
	jQuery('.table-single-criteria').each(function() {
		jQuery('.table-single-criteria tbody tr').each(function() {
			var val = jQuery(this).find('select').val()
			jQuery(this).find('td:has(select)').removeClass().addClass('no-padding text-center ' + colorByValue(val))
		})
	})
	jQuery('.table-single-criteria-cra').each(function() {
		jQuery('.table-single-criteria-cra tbody tr').each(function() {
			var val = jQuery(this).find('select').val()
			jQuery(this).find('td:has(select)').removeClass().addClass('no-padding text-center ' + colorByValueCRA(val))
		})
	})
	// RISK Form Calc on Init
	jQuery('.table-bia-risk').each(function(e) {
		jQuery(this).find('tr').each(function() {
			var data = {}
			jQuery(this).find('select').each(function(i, e) {
				var val = jQuery(this).val()
				data[i] = val
				var reverse = jQuery(this).hasClass('reverse')
				var increment = jQuery(this).hasClass('increment')
				if (reverse) {
					val = String(Number(val) + 1)
				}
				if (increment && val == '0') {
					val = String(Number(val) + 1)
				}
				jQuery(this).parent('td').removeClass().addClass('no-padding ' + colorByValue(val, 'select', !reverse))
			})
			var calc = bia_risk_calc(data)
			jQuery(this).find('.risk').html(calc.count).parent('td').removeClass().addClass('text-center ' + calc.color)
		})
	})
	// BIA Bool on Init
	jQuery('select.bool').each(function(e) {
		var val = jQuery(this).val()
		if (val == '1') val = '4';
		else if (val == '0') val = '1';
		jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValue(val, 'select', true))
	})
	// Table Avg Init Value
	calcFormAvg();
	bia_risk_calc_avg();
	// BIA Form Calc on Init
	jQuery('.table-bia-core').each(function() {
		total = 0
		var subTotal = weightToMultiply = 0;
		jQuery('select', this).each(function() {
			var val = jQuery(this).val()
			if (!jQuery(this).is('.biaWeight')) {
				subTotal += Number(val)
			} else {
				weightToMultiply = bia_weight_to_multiply(Number(val))
				total += (subTotal * weightToMultiply)
				subTotal = 0
			}
			finance = (jQuery(this).parent('td').attr('id') == 'finance-per-day' ? true : false)
			if (!jQuery(this).hasClass('esc')) {
				if (!jQuery(this).is('.biaWeight')) {
					jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValue(val, 'select', true, finance, true))
				} else {
					jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValueForWeight(val))
				}
			}
		})
		var form = jQuery(this).parents('.survey-form')
		calc = bia_calc(total)
		form.find('.level').html(calc.level).parent('td').removeClass().addClass(calc.color)
		form.find('.total').html(total).parent('td').removeClass().addClass(calc.color)
		form.find('.rto').html(calc.rto).parent('td').removeClass().addClass(calc.color)
		form.find('.hidden-rto').val(calc.rto)
		form.find('.hidden-avg').val(total)
	})
	// BIA Bool on click
	jQuery('select.bool').on('change', function(e) {
		var val = jQuery(this).val()
		if (val == '1') {
			val = '4'
		} else if (val == '0') {
			val = '1'
		}
		jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValue(val, 'select', true))
	})
	jQuery('select.rpo').each(function() {
	    var name = null;
		var val = jQuery(this).val()
		if (val == '0') {
			name = 'color-one'
		} else if (val == '1') {
			name = 'color-two'
		}
		else if (val == '2') {
			name = 'color-three'
		}
		else if (val == '3') {
			name = 'color-four'
		}
		jQuery(this).parent('td').removeClass().addClass('no-padding text-center '+ name)
	})
	// RPO Bool on click
	jQuery('select.rpo').on('change', function(e) {
	    var name = null;
		var val = jQuery(this).val()
		if (val == '0') {
			name = 'color-one'
		} else if (val == '1') {
			name = 'color-two'
		}
		else if (val == '2') {
			name = 'color-three'
		}
		else if (val == '3') {
			name = 'color-four'
		}
		jQuery(this).parent('td').removeClass().addClass('no-padding text-center '+ name)
	})
	// Muliple Form Calc on click
	jQuery('.table-multiple-criteria select').on('change', function(e) {
		jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValue(jQuery(this).val()))
		var rsum = wsum = sum = count = 0
		jQuery(this).parents('tr').find('select').each(function() {
			val = jQuery(this).val()
			if (val != 0) {
				count += 1
				rsum += (Number(jQuery(this).parent('td').attr('data-w')) * Number(val))
				wsum += Number(jQuery(this).parent('td').attr('data-w'))
				sum += Number(val);
			}
		})
		var avg = count == 0 ? 0 : (sum / count)
		var wavg = wsum == 0 ? 0 : (rsum / wsum)
		jQuery(this).parents('tr').find('.rating').html(avg.toFixed(1)).parent('td').removeClass().addClass('text-center ' + colorByValue(avg, 'avg'))
		jQuery(this).parents('tr').find('.adjusted-rating').html(wavg.toFixed(1)).parent('td').removeClass().addClass('text-center ' + colorByValue(wavg, 'avg'))
		calcFormAvg()
	})
	// Single Form Calc on Click
	jQuery('.table-single-criteria select').on('change', function(e) {
		var val = jQuery(this).val()
		jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValue(val)).find('.rating').val(val)
		calcFormAvg()
		total = 0
		var form = jQuery(this).parents('.survey-form')
		jQuery(this).parents('tbody').find('select').each(function() {
			total += Number(jQuery(this).val());
		})
		form.find('.avg').html(form.find('.hidden-avg').val())
		form.find('.total').html(total)
	})
	
	jQuery('.table-single-criteria-cra select').on('change', function(e) {
		var val = jQuery(this).val()
		jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValueCRA(val)).find('.rating').val(val)
		calcFormAvg()
		total = 0
		var form = jQuery(this).parents('.survey-form')
		jQuery(this).parents('tbody').find('select').each(function() {
			total += Number(jQuery(this).val());
		})
		form.find('.avg').html(form.find('.hidden-avg').val())
		form.find('.total').html(total)
	})
	// BIA Form Calc on Click
	jQuery('.table-bia-core select').on('change', function(e) {
		var val = jQuery(this).val()
		finance = (jQuery(this).parent('td').attr('id') == 'finance-per-day' ? true : false)
		if (!jQuery(this).hasClass('esc')) {
			if (!jQuery(this).is('.biaWeight')) {
				jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValue(val, 'select', true, finance, true))
			} else {
				jQuery(this).parent('td').removeClass().addClass('no-padding text-center ' + colorByValueForWeight(val))
			}
		}
		total = 0
		var selectVal = subTotal = 0
		var form = jQuery(this).parents('.survey-form')
		jQuery(this).parents('tbody').find('select').each(function() {
			selectVal = jQuery(this).val()
			if (!jQuery(this).is('.biaWeight')) {
				subTotal += Number(selectVal)
			} else {
				weightToMultiply = bia_weight_to_multiply(Number(selectVal))
				total += (subTotal * weightToMultiply)
				subTotal = 0
			}
		})
		var calc = bia_calc(total)
		form.find('.level').html(calc.level).parent('td').removeClass().addClass(calc.color)
		form.find('.total').html(total).parent('td').removeClass().addClass(calc.color)
		form.find('.rto').html(calc.rto).parent('td').removeClass().addClass(calc.color)
		form.find('.hidden-rto').val(calc.rto)
		form.find('.hidden-avg').val(total)
	})
	// RISK Form Calc on Click
	jQuery('.table-bia-risk select').on('change', function(e) {
		e.preventDefault()
		var data = {}
		var reverse = jQuery(this).hasClass('reverse')
		var increment = jQuery(this).hasClass('increment')
		var val = jQuery(this).val()
		if (reverse) {val = String(Number(val) + 1) }
		if (increment && val == '0') {val = String(Number(val) + 1) }
		jQuery(this).parent('td').removeClass().addClass('no-padding ' + colorByValue(val, 'select', !reverse))
		jQuery(this).parents('tr').find('select').each(function(i, e) {
			var val = Number(jQuery(this).val())
			data[i] = val
		})
		var calc = bia_risk_calc(data)
		jQuery(this).parents('tr').find('.risk').html(calc.count).parent('td').removeClass().addClass('text-center ' + calc.color)
		bia_risk_calc_avg()
	})
	// risk registry on click
	jQuery('.table-risk-registry select').on('change', function(e) {
		var val = jQuery(this).val()
		jQuery(this).parent('td').removeClass().addClass('no-padding ' + colorByValue(val, 'select', true))
	})
	jQuery('.table-bcp-registry select').on('change', function(e) {
		bia_bcp_calc($(this), true);
		// var val = jQuery(this).val()
		// jQuery(this).parent('td').removeClass('color-one color-two color-three color-four color-five').addClass(colorByValue(val, 'select', true))
	})
	
	// BCP Form Calc on INIT
	jQuery('.table-bia-bcp').each(function(e) {
		bia_bcp_calc_avg();
	})
	// BCP Form Calc on Click
	jQuery('.table-bia-bcp select').on('change', function(e) {
		e.preventDefault()
		bia_bcp_calc($(this));
		bia_bcp_calc_avg();
	})
	// Image Map
	jQuery('img[usemap]').rwdImageMaps();
	// Ajax Save
	jQuery('.survey-form').on('submit', function(e) {
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
			// validate btn
			form_id = jQuery('.btn-publish').attr('data-id')
			validate_publish_btn_color(form_id)
		})
	})
	// Ajax Save
	jQuery('.rr-form').on('submit', function(e) {
		e.preventDefault()
		var formData = jQuery(this).serialize()
		var formMeta = jQuery(this).attr('data-meta')
		var postID = jQuery(this).attr('data-id')
		var archivedby = jQuery(this).attr('data-archivedby')
		jQuery(this).find('.btn-success').addClass('loading')
		jQuery.post(object.ajaxurl + '?action=save_rr', {
			data: formData,
			meta: formMeta,
			post_id: postID,
			archivedby: archivedby,
			security: object.ajax_nonce
		}, function(response) {
			jQuery('.btn-success').removeClass('loading');
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
	// rr on init
	jQuery('.rr-form').each(function(e) {
		var inherent = Number(jQuery(this).find('#inherent-val').html())
		var mitigation = Number(jQuery(this).find('#mitigation').val())
		var rr = Math.round(Number(inherent - (inherent * (mitigation) / 100)))
		var color
		if (rr >= 0 && rr <= 3) {
			color = 'color-four'
		} else if (rr >= 4 && rr <= 8) {
			color = 'color-three'
		} else if (rr >= 9 && rr <= 12) {
			color = 'color-two'
		} else if (rr >= 13 && rr <= 16) {
			color = 'color-one'
		}
		jQuery(this).find('#residual-risk').html(rr).removeClass().addClass('text-center font-14px ' + color)
	})
	// rr on change
	jQuery('.mitigation').on('change', function(e) {
		var inherent = Number(jQuery(this).parents('tr').find('#inherent-val').html())
		var mitigation = Number(jQuery(this).val())
		var rr = Math.round(Number(inherent - (inherent * (mitigation) / 100)))
		var color
		if (rr >= 0 && rr <= 3) {
			color = 'color-four'
		} else if (rr >= 4 && rr <= 8) {
			color = 'color-three'
		} else if (rr >= 9 && rr <= 12) {
			color = 'color-two'
		} else if (rr >= 13 && rr <= 16) {
			color = 'color-one'
		}
		jQuery(this).parents('.rr-form').find('#residual-risk').html(rr).removeClass().addClass('text-center font-14px ' + color)
	})
	// dynamic registry on init
	jQuery('.table-dynamic-registry select').each(function() {
		var val = jQuery(this).val()
		jQuery(this).parent('td').removeClass().addClass('no-padding ' + dynamicRegisterColorByValue(val))
	})
	jQuery('.table-dynamic-registry select').on('change', function(e) {
		var val = jQuery(this).val()
		jQuery(this).parent('td').removeClass().addClass('no-padding ' + dynamicRegisterColorByValue(val))
	})
	jQuery(document).on('click', '.mtarThreatSelectItem', function(e) {
		e.preventDefault();
		var theID =  '#'+jQuery(this).attr("subthreat");
		jQuery(this).parents('.areaWrapper').find('.areaContainer').addClass('hidden');
		jQuery(theID).removeClass('hidden');
	})
	jQuery(document).on('change', '.mtarSubThreatSelect', function(e) {
		e.preventDefault();
		var selectVal =  jQuery(this).children("option:selected").val();
		var theID =  '#'+selectVal;
		jQuery(this).parents('.areaContainer').find('.subThreatContainer').addClass('hidden');
		jQuery(this).parents('.areaContainer').find('.mtarSubThreatSelect').val(selectVal);
		jQuery(theID).removeClass('hidden');
	})
	// MTAR
	jQuery('.table-mtar select').each(function() {
		var val = jQuery(this).val()
		jQuery(this).parent('td').removeClass().addClass('no-padding ' + MTARegisterColorByValue(val))
	})
	jQuery('.table-mtar select').on('change', function(e) {
		var val = jQuery(this).val()
		jQuery(this).parent('td').removeClass().addClass('no-padding ' + MTARegisterColorByValue(val))
	})
	jQuery('.removeMTAArchive').on('click', function(e) {
		var button = jQuery(this);
		// alert('Coming soon'); return false; 
		swal({
			title: "WARNING",
			text: "Removing the archive PDF created on  "+button.attr('date')+". Are you sure you want to proceed?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#4caf50",
			confirmButtonText: "Yes",
			closeOnConfirm: false
		}, function() {
			var archiveid = button.attr('mtar_archive_id');
			if (archiveid.length < 1) swal("Error!", "Something went wrong.", "error")
			else {
				jQuery.post(object.ajaxurl + '?action=mta_register_remove_archive', {
					security: object.ajax_nonce,
					archiveid: archiveid
				}, function(response) {
					console.log(response);
					if (response != false) {
						swal("Success!", "The Archive has been removed", "success")
						setTimeout(function() {
							window.location.reload()
						}, 2000)
					} else {
						swal("Error!", "Something went wrong.", "error")
					}
				})
			}
		})
	})
})
// Avg Count
function calcFormAvg() {
	jQuery('.survey-form').each(function() {
		var sum = count = 0
		var base = jQuery(this).attr('data-meta')
		var clss = ''
		if (jQuery(this).hasClass('single')) {
			clss = '.rating'
		} else {
			clss = '.adjusted-rating'
		}
		// .rating for avg
		jQuery(this).find(clss).each(function() {
			if (jQuery(this).is('span')) {
				val = Number(jQuery(this).html())
			} else if (jQuery(this).is('input')) {
				val = Number(jQuery(this).val())
			}
			sum += val
			if (val > 0) {
				count += 1
			}
		})
		var avg = (sum / count).toFixed(1)
		if (isNaN(avg)) {
			avg = (0).toFixed(1)
		}
		jQuery(this).find('.hidden-avg').val(avg)
		jQuery(document).find('.metric-data-' + base).removeClass('metrics-red metrics-yellow metrics-green').addClass(colorByValue(avg, 'avg')).html(avg)
	})
}
// Color Class on Value
function colorByValueForWeight(val) {
	if (val== 1) return cl = 'color-four'
	else if (val == 2) return cl = 'color-three'
	else return cl = 'color-one'
}
function colorByValueCRA(val) {
	var cl = ''
	switch (parseInt(val)) {
		case 0: cl = 'color-zero'; 	  break;
		case 1: cl = 'color-two'; 	  break;
		case 2: cl = 'color-one'; 	  break;
		case 3: cl = 'color-three';   break;
		case 4: cl = 'color-four 4';  break;
		case 5: cl = 'color-five 5';  break;
		default: cl = 'color-zero';   break;
	}
	return cl
}
function colorByValue(val, type, reverse, finance, bia) {
	var type = type || 'select'
	var reverse = reverse || false
	var finance = finance || false
	var bia = bia || false
	var cl = ''
	if (type == 'select') {
		switch (val) {
			case '0':
				if (reverse) {
					cl = 'color-five'
				} else {
					cl = 'color-zero'
				}
				break
			case '1':
				if (reverse) {
					cl = 'color-four'
				} else {
					cl = 'color-one'
				}
				break
			case '2':
				if (reverse) {
					cl = 'color-three'
				} else {
					cl = 'color-two'
				}
				break
			case '3':
				if (reverse) {
					cl = 'color-two'
				} else {
					cl = 'color-three'
				}
				break
			case '4':
				if (finance) {
					cl = 'color-three'
				} else if (reverse) {
					cl = 'color-one'
				} else {
					cl = 'color-four'
				}
				break
			case '8':
				cl = 'color-two'
				break
			case '7':
				cl = 'color-one'
				break
			case '5':
			case '16':
				if (finance) {
					cl = 'color-one'
				} else if (bia) {
					cl = 'color-two'
				} else if (reverse) {
					cl = 'color-one'
				} else {
					cl = 'color-five'
				}
				break
			case '1.25':
				cl = 'color-three'
				break
			case '1.75':
				cl = 'color-five'
				break
		}
	} else if (type == 'avg') {
		if (val <= 2.5) {
			cl = 'metrics-red'
		} else if (val > 2.5 && val <= 3.5) {
			cl = 'metrics-yellow'
		} else if (val > 3.5 && val <= 5) {
			cl = 'metrics-green'
		}
	}
	return cl
}
function dynamicRegisterColorByValue(val) {
	switch (val) {
		case '1': cl = 'color-five'; break;
		case '2': cl = 'color-four'; break;
		case '3': cl = 'color-three'; break;
		case '4': cl = 'color-two'; break;
		case '5': cl = 'color-one'; break;
		default: cl = ''; break;
	}
	return cl
}
function MTARegisterColorByValue(val) {
	switch (val) {
		case '1': cl = 'color-one'; break;
		case '2': cl = 'color-two'; break;
		case '3': cl = 'color-three'; break;
		case '4': cl = 'color-four'; break;
		case '5': cl = 'color-five'; break;
		default: cl = ''; break;
	}
	return cl
}
function BCPcolorByValue(val, type, reverse) {
	switch (val) {
		case '1': cl = 'color-five'; break;
		case '2': cl = 'color-four'; break;
		case '3': cl = 'color-three'; break;
		case '4': cl = 'color-two'; break;
		case '5': cl = 'color-one'; break;
		default: cl = 'color-five'; break;
	}
	return cl
}
// Validate Btn Color
function validate_publish_btn_color(form_id) {
	jQuery.post(object.ajaxurl + '?action=validate_form_submission', {
		form_id: form_id,
		security: object.ajax_nonce
	}, function(response) {
		if (response == true) {
			jQuery('.btn-publish').removeClass('btn-default btn-info').addClass('btn-info')
		} else {
			jQuery('.btn-publish').removeClass('btn-default btn-info').addClass('btn-default')
		}
	})
}
// Load dashbord scorecard
function load_dashboard_scorecard(element) {
	var postID = element.val();
	var areaID = element.find('option:selected').attr('areaid');
	jQuery.get(object.ajaxurl + '?action=dashboard_scorecard', {
		post_id: postID,
		area_id: areaID,
		security: object.ajax_nonce
	}, function(response) {
		jQuery('#ajax-scorecard-data').empty().html(response)
	})
}
function load_eva_dashboard_scorecard(postID) {
	jQuery.get(object.ajaxurl + '?action=dashboard_eva_reportcard', {
		post_id: postID,
		security: object.ajax_nonce
	}, function(response) {
		jQuery('#ajax-eva-data').empty().html(response)
	})
}
// Load dashbord scorecard
function load_dashboard_reportcard(postID) {
	jQuery.get(object.ajaxurl + '?action=dashboard_reportcard', {
		post_id: postID,
		security: object.ajax_nonce
	}, function(response) {
		jQuery('#ajax-scorecard-data').empty().html(response)
	})
}
// Load dashbord cloud scorecard
function load_dashboard_cloud_reportcard(postID) {
	jQuery.get(object.ajaxurl + '?action=dashboard_cloud_reportcard', {
		post_id: postID,
		security: object.ajax_nonce
	}, function(response) {
		jQuery('#ajax-scorecard-data').empty().html(response)
	})
}
// Load dashbord mta register status scorecard
function load_dashboard_mtar_statuscard(element) {
	var postID = element.val();
	var areaID = element.find('option:selected').attr('areaid');
	jQuery.get(object.ajaxurl + '?action=dashboard_mtar_statuscard', {
		post_id: postID,
		area_id: areaID,
		security: object.ajax_nonce
	}, function(response) {
		jQuery('#ajax-scorecard-data').empty().html(response)
	})
}
// Load dashbord mta register status scorecard
function load_dashboard_report_card_scorecard(element) {
	var postID = element.val();
	jQuery.get(object.ajaxurl + '?action=dashboard_report_card', {
		security: object.ajax_nonce
	}, function(response) {
		// console.log(response);
		jQuery('#ajax-scorecard-data').empty().html(response)
	})
}
// BIA Calc
function bia_calc(val) {
	var data = {}
	if (val >= 0 && val <= 20) {
		data.level = 'Non-essential'
		data.rto = '2 - 4 Weeks'
		data.color = 'color-five'
	} else if (val >= 21 && val <= 40) {
		data.level = 'Normal'
		data.rto = '7 Days'
		data.color = 'color-four'
	} else if (val >= 41 && val <= 60) {
		data.level = 'Important'
		data.rto = '3 Days'
		data.color = 'color-three'
	} else if (val >= 61 && val <= 80) {
		data.level = 'Urgent'
		data.rto = '24 Hours'
		data.color = 'color-two'
	} else if (val >= 80) {
		data.level = 'Critical'
		data.rto = '0 - 4 Hours'
		data.color = 'color-one'
	}
	return data
}
// BIA Risk Calc
function bia_risk_calc(obj) {
	var data = []
	if (Object.keys(obj).length == 1) {
		data.count = obj[0]
	} else if(Object.keys(obj).length == 2){
		data.count = Number(obj[0]) * Number(obj[1])
	} else {
		if (Number(obj[1]) - Number(obj[2]) > 1) {
			if (obj[0] == 0) { obj[0] = 1 }
			data.count = Number(obj[0]) * (Number(obj[1]) - Number(obj[2]))
		} else {
			data.count = obj[0]
		}
	}
	if (data.count >= 0 && data.count <= 3) {
		data.color = 'color-five'
	} else if (data.count >= 4 && data.count <= 8) {
		data.color = 'color-three'
	} else if (data.count >= 9 && data.count <= 16) {
		data.color = 'color-one'
	}
	return data
}
function bcp_risk_calc(obj) {
	var data = {count:0, avg:0};
	data.count = Number(obj[0]) * Number(obj[1]) * Number(obj[2]);
	if (data.count > 1) data.avg = Number(data.count / 3).toFixed(1);
	else data.count = 0;
	if (data.count <= 12) 							{ data.cls = 'color-five';  } 
	else if (data.count > 12 && data.count <= 26) 	{ data.cls = 'color-four';  } 
	else if (data.count > 26 && data.count <= 47) 	{ data.cls = 'color-three'; } 
	else if (data.count > 47 && data.count <= 99) 	{ data.cls = 'color-two';   } 
	else if (data.count >= 100) 					{ data.cls = 'color-one';   }
	return data
}
// BIA Risk Calc Avg
function bia_risk_calc_avg() {
	jQuery('.table-bia-risk').each(function() {
		var count = 0
		var total = 0
		var color
		var level
		jQuery(this).find('.risk').each(function() {
			count += 1
			total += Number(jQuery(this).html())
		})
		if (isNaN(avg)) {
			avg = (0).toFixed(1)
		}
		var avg = Math.round(total / count).toFixed(1)
		if (avg >= 0 && avg <= 3) {
			color = 'color-four'
			level = 'Low'
		} else if (avg >= 4 && avg <= 8) {
			color = 'color-three'
			level = 'Medium'
		} else if (avg >= 9 && avg <= 12) {
			color = 'color-two'
			level = 'High'
		} else if (avg >= 13 && avg <= 16) {
			color = 'color-one'
			level = 'Extreme'
		}
		jQuery(this).parents('.card').find('.total-risk').removeClass('color-one color-two color-three color-four').addClass(color).find('strong').html(level)
		jQuery(this).parents('.card').find('.total-risk-avg').removeClass('color-one color-two color-three color-four').addClass(color).find('span').html(avg)
		jQuery(this).parents('.card').find('.hidden-avg').val(avg)
	})
	Q4bUpstreamsSelectOptions();
	jQuery(document).on('change', '.Q4bUpstreams', function(event) {
		// var val = jQuery(this).val();
		Q4bUpstreamsSelectOptions();
	})
}

function bia_bcp_calc(element, register=false) {
	var data = {}
	var reverse = element.hasClass('reverse')
	var increment = element.hasClass('increment')
	var val = element.val()
	if (reverse) { val = String(Number(val) + 1) }
	if (increment && val == '0') { val = String(Number(val) + 1) }
	element.parent('td').removeClass('color-one color-two color-three color-four color-five').addClass(BCPcolorByValue(val, 'select', false))
	element.parents('tr').find('select').each(function(i, e) {
		var val = Number($(this).val())
		data[i] = val
	})
	var calc = bcp_risk_calc(data);
	if (register) element.parents('table').find('.bcprAvg').removeClass('color-one color-two color-three color-four color-five').addClass(calc.cls)
	else element.parents('tr').find('.bcp').html(calc.count).parent('td').removeClass().addClass('text-center ' + calc.cls);
}
function bia_bcp_calc_avg() {
	jQuery('.table-bia-bcp').each(function() {
		var count = 0
		var total = 0
		var color
		var level
		jQuery(this).find('.bcp').each(function() {
			count += 1
			total += Number(jQuery(this).html())
		})
		if (isNaN(avg)) { avg = (0).toFixed(1); }
		var avg = Math.round(total / count).toFixed(1)
		if (avg <= 12) 					{ color = 'color-five'; level = 'Very Low'; } 
		else if (avg > 12 && avg <= 26) { color = 'color-four'; level = 'Low'; } 
		else if (avg > 26 && avg <= 47) { color = 'color-three'; level = 'Moderate'; } 
		else if (avg > 47 && avg <= 99) { color = 'color-two'; level = 'High'; }
		else if (avg > 99 && avg <= 125){ color = 'color-one'; level = 'Very High'; }

		jQuery(this).parents('.card').find('.avgContainer').removeClass('color-one color-two color-three color-four color-five').addClass(color);
		jQuery(this).parents('.card').find('.total-bcp').find('strong').html(level)
		jQuery(this).parents('.card').find('.total-bcp-avg').find('span').html(avg)
		jQuery(this).parents('.card').find('.hidden-avg').val(avg)
	})
}
function Q4bUpstreamsSelectOptions() {
	var oldVals = [];
	// GET SELECTED VALUES
	jQuery('.Q4bUpstreams').each(function(element) {
		var selectedVal = jQuery(this).val();
		if (selectedVal != 0) oldVals.push(selectedVal);
	})
	// DISABLE SELECTED VALUES
	jQuery('.Q4bUpstreams').each(function(element) {
		var selectedVal = jQuery(this).val();
		jQuery(this).find('option').each(function(el) {
			var optionVal = jQuery(this).val();
			var isSelected = jQuery(this).attr('selected');
			if ((jQuery.inArray(optionVal, oldVals) !== -1) && !isSelected) {
				jQuery(this).attr('disabled', true);
			} else {
				jQuery(this).attr('disabled', false);
			}
		})
	})
}
// BIA Weight Calc
function bia_weight_to_multiply(weight=null) {
	if (weight) { return weight;}
	return 1;	
}
// SHOW UPSTREAM POPUP
jQuery(document).on('click', '.upstreamBtn', function(event) {
	event.preventDefault();
	var ID = '#'+jQuery(this).attr('id');
	var upstream = jQuery(ID +'_text').val();
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'upstream_options',
			'security': object.ajax_nonce,
			'upstream': upstream,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			// alert(response);
			jQuery('#modal-bia .modal-body').html(response);
			jQuery('#modal-bia .modal-footer .btn-primary').removeClass('saveDependenciesQ4b').addClass('saveDependencies');
			jQuery('#modal-bia').attr('rowid', ID).modal('show');
		},
		error: function(error) {
			alert('error');
		},
	})
})
// SHOW EXTERNAL DEPENDENCIES  POPUP
jQuery(document).on('click', '.dependencyBtn', function(event) {
	event.preventDefault();
	var button = jQuery(this);
	var ID = '#'+jQuery(this).attr('id');
	var dependency = jQuery(ID +'_text').val();
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'external_dependency_options',
			'security': object.ajax_nonce,
			'dependency': dependency,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			if (button.is('.cloud')) jQuery('#modal-bia .modal-title').html('Select Providers');
			jQuery('#modal-bia .modal-body').html(response);
			jQuery('#modal-bia').attr('rowid', ID).modal('show');
		},
		error: function(error) { alert('error'); },
	})
})
// SHOW EXTERNAL DEPENDENCIES  POPUP
jQuery(document).on('click', '.mobileAppsBtn', function(event) {
	event.preventDefault();
	var button = jQuery(this);
	var ID = '#'+jQuery(this).attr('id');
	var dependency = jQuery(ID +'_text').val();
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'mobile_apps_options',
			'security': object.ajax_nonce,
			'dependency': dependency,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			if (button.is('.cloud')) jQuery('#modal-bia .modal-title').html('Select Providers');
			jQuery('#modal-bia .modal-body').html(response);
			jQuery('#modal-bia').attr('rowid', ID).modal('show');
		},
		error: function(error) { alert('error'); },
	})
})
// SHOW UPSTREAM POPUP
jQuery(document).on('click', '.Q4bUpstreams', function(event) {
	event.preventDefault();
	var ID = '#'+jQuery(this).attr('id');
	var upstream = jQuery(ID +'_text').val();
	var allSelected = jQuery(this).parents('.Q4AUpstreams').attr('upstream');
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'upstream_options_Q4b',
			'security': object.ajax_nonce,
			'upstream': upstream,
			'selected': allSelected,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			// alert(response);
			jQuery('#modal-bia .modal-body').html(response);
			jQuery('#modal-bia .modal-footer .btn-primary').removeClass('saveDependencies').addClass('saveDependenciesQ4b');
			jQuery('#modal-bia').attr('rowid', ID).modal('show');
		},
		error: function(error) {
			alert('error');
		},
	})
})
jQuery(document).on('click', '.ITServiceCatalogue', function(event) {
    var upstream = $(this).attr('services');
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'upstream_options',
			'security': object.ajax_nonce,
			'upstream': upstream,
			'input': 0,
		},
		beforeSend: function() {},
		success: function(response) {
			// alert(response);
			jQuery('#modal-bia .modal-body').html(response);
			jQuery('#modal-bia').modal('show');
		},
		error: function(error) {
			alert('error');
		},
	})
})
// SHOW DESKTOP APPLICATION POPUP
jQuery(document).on('click', '.desktopBtn', function(event) {
	event.preventDefault();
	var button = jQuery(this);
	var ID = '#'+jQuery(this).attr('id');
	var dependency = jQuery(ID +'_text').val();
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'desktop_application_options',
			'security': object.ajax_nonce,
			'dependency': dependency,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			jQuery('#modal-bia .modal-body').html(response);
			jQuery('#modal-bia .modal-footer .btn-primary').removeClass('saveDependencies').addClass('saveDesktop');
			jQuery('#modal-bia').attr('rowid', ID).modal('show');
		},
		error: function(error) { alert('error'); },
	})
})
jQuery(document).on('click', '.saveDesktop', function(event) {
	event.preventDefault();
	var val = '';
	var ID = jQuery(this).parents('.modal').attr('rowid');
	jQuery('.upstream:checked').each(function(index) {
		val += jQuery(this).val() +',';
	});
	val = val.replace(/,$/g,'');
	jQuery(ID +'_text').val(val);
	if (val) jQuery(ID).addClass('active');
	else jQuery(ID).removeClass('active');
	jQuery('#modal-bia').modal('hide');
})
// SHOW DESKTOP APPLICATION POPUP
jQuery(document).on('click', '.desktopApplicationBtn', function(event) {
	event.preventDefault();
	var button = jQuery(this);
	var ID = '#'+jQuery(this).attr('id');
	var dependency = jQuery(ID +'_text').val();
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'desktop_application_options',
			'security': object.ajax_nonce,
			'dependency': dependency,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			jQuery('#modal-bia .modal-body').html(response);
			jQuery('#modal-bia .modal-footer .btn-primary').removeClass('saveDependencies').addClass('saveDesktopApplication');
			jQuery('#modal-bia').attr('rowid', ID).modal('show');
		},
		error: function(error) { alert('error'); },
	})
})
jQuery(document).on('click', '.saveDesktopApplication', function(event) {
	event.preventDefault();
	var val = '';
	var ID = jQuery(this).parents('.modal').attr('rowid');
	var cls = ID.replace('#upstream', '.Q4AUpstreams'); // Q4AUpstreams_1
	jQuery('.upstream:checked').each(function(index) {
		val += jQuery(this).val() +',';
	});
	val = val.replace(/,$/g,'');
	jQuery(ID +'_text').val(val);
	jQuery(cls).attr('upstream', val);
	if (val) jQuery(ID).addClass('active');
	else jQuery(ID).removeClass('active');
	jQuery('#modal-bia').modal('hide');
})
jQuery(document).on('click', '.saveDependencies', function(event) {
	event.preventDefault();
	var val = '';
	var Q4BID = '';
	var ID = jQuery(this).parents('.modal').attr('rowid');
	var Q4BClass = ID.replace('#upstream', '.Q4AUpstreams'); // Q4AUpstreams_1
	jQuery('.upstream:checked').each(function(index) {
		val += jQuery(this).val() +',';
	});
	val = val.replace(/,$/g,'');
	jQuery(ID +'_text').val(val);
	jQuery(Q4BClass).attr('upstream', val);
	if (val) jQuery(ID).addClass('active');
	else jQuery(ID).removeClass('active');
	jQuery('#modal-bia').modal('hide');
})
jQuery(document).on('click', '.saveDependenciesQ4b', function(event) {
	event.preventDefault();
	var val = '';
	var Q4bUpstreams = '';
	var ID = jQuery(this).parents('.modal').attr('rowid');
	jQuery('.upstream:checked').each(function(index) {
		val += jQuery(this).val() +',';
	});
	val = val.replace(/,$/g,'');
	jQuery(ID +'_text').val(val);
	// ADD ALL THINGS TOGETHER
	jQuery('.Q4bUpstreams').each(function(el) {
		var current = jQuery(this).find('input').val();
		var id = jQuery(this).find('input').attr('id');
		if (current) Q4bUpstreams += current +',';
	})
	jQuery('#Q4bUpstreams').val(Q4bUpstreams.substring(0,(Q4bUpstreams.length-1)));
	if (val) jQuery(ID).addClass('active');
	else jQuery(ID).removeClass('active');
	jQuery('#modal-bia').modal('hide');
})

// // BIA Q9 DEPENDENCIES (UPSTREAM DEPENDENCY)
// jQuery(document).on('click', '.oidUpstreamBtn', function(event) {
// 	event.preventDefault();
// 	var button = jQuery(this);
// 	var ID = '#'+jQuery(this).attr('id');
// 	var dependency = jQuery(ID +'_text').val();
// 	jQuery.ajax({
// 		url: object.ajaxurl,
// 		method : 'post',
// 		data: {
// 			'action': 'external_dependency_options',
// 			'security': object.ajax_nonce,
// 			'dependency': dependency,
// 			'input': 1,
// 		},
// 		beforeSend: function() {},
// 		success: function(response) {
// 			jQuery('#modal-bia .modal-body').html(response);
// 			jQuery('#modal-bia .modal-footer .btn-primary').removeClass('saveDependencies').addClass('save_OIDUpstreamBtn');
// 			jQuery('#modal-bia').attr('rowid', ID).modal('show');
// 		},
// 		error: function(error) { alert('error'); },
// 	})
// })
// jQuery(document).on('click', '.save_OIDUpstreamBtn', function(event) {
// 	event.preventDefault();
// 	var val = '';
// 	var ID = jQuery(this).parents('.modal').attr('rowid');
// 	jQuery('.upstream:checked').each(function(index) {
// 		val += jQuery(this).val() +',';
// 	});
// 	val = val.replace(/,$/g,'');
// 	jQuery(ID +'_text').val(val);
// 	if (val) jQuery(ID).addClass('active');
// 	else jQuery(ID).removeClass('active');
// 	jQuery('#modal-bia').modal('hide');
// })
// // BIA Q9 DEPENDENCIES (DOWNSTREAM DEPENDENCY)
// jQuery(document).on('click', '.oidDownstreamBtn', function(event) {
// 	event.preventDefault();
// 	var button = jQuery(this);
// 	var ID = '#'+jQuery(this).attr('id');
// 	var dependency = jQuery(ID +'_text').val();
// 	jQuery.ajax({
// 		url: object.ajaxurl,
// 		method : 'post',
// 		data: {
// 			'action': 'external_dependency_options',
// 			'security': object.ajax_nonce,
// 			'dependency': dependency,
// 			'input': 1,
// 		},
// 		beforeSend: function() {},
// 		success: function(response) {
// 			jQuery('#modal-bia .modal-body').html(response);
// 			jQuery('#modal-bia .modal-footer .btn-primary').removeClass('saveDependencies').addClass('save_OIDDownstreamBtn');
// 			jQuery('#modal-bia').attr('rowid', ID).modal('show');
// 		},
// 		error: function(error) { alert('error'); },
// 	})
// })
// jQuery(document).on('click', '.save_OIDDownstreamBtn', function(event) {
// 	event.preventDefault();
// 	var val = '';
// 	var ID = jQuery(this).parents('.modal').attr('rowid');
// 	jQuery('.upstream:checked').each(function(index) { val += jQuery(this).val() +','; });
// 	val = val.replace(/,$/g,'');
// 	jQuery(ID +'_text').val(val);
// 	if (val) jQuery(ID).addClass('active');
// 	else jQuery(ID).removeClass('active');
// 	jQuery('#modal-bia').modal('hide');
// })
// BCP
jQuery(document).on('click', '.assetsImpactedBtn', function(event) {
	event.preventDefault();
	var ID = '#'+jQuery(this).attr('id');
	var ai = jQuery(ID +'_text').val();
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'assets_impacted_options',
			'security': object.ajax_nonce,
			'ai': ai,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			jQuery('#BCPModal .modal-body').html(response);
			jQuery('#BCPModal').attr('rowid', ID).modal('show');
		},
		error: function(error) { alert('error'); },
	})
})
jQuery(document).on('click', '.saveAssetsImpacted', function(event) {
	event.preventDefault();
	var val = '';
	var ID = jQuery(this).parents('.modal').attr('rowid');
	// var Q4BClass = ID.replace('#upstream', '.Q4AUpstreams');
	var Q4BClass = '';
	jQuery('.upstream:checked').each(function(index) {
		val += jQuery(this).val() +',';
	});
	val = val.replace(/,$/g,'');
	jQuery(ID +'_text').val(val);
	jQuery(Q4BClass).attr('upstream', val);
	if (val) jQuery(ID).addClass('active');
	else jQuery(ID).removeClass('active');
	jQuery('#BCPModal').modal('hide');
})
jQuery(document).on('click', '.vulnerabilityBtn', function(event) {
	event.preventDefault();
	var ID = '#'+jQuery(this).attr('id');
	var ai = jQuery(ID +'_text').val();
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'vulnerability_options',
			'security': object.ajax_nonce,
			'ai': ai,
			'input': 1,
		},
		beforeSend: function() {},
		success: function(response) {
			jQuery('#BCPModal .modal-body').html(response);
			jQuery('#modal-bia .modal-footer .btn-primary').removeClass('saveAssetsImpacted').addClass('saveVulnerability');
			jQuery('#BCPModal').attr('rowid', ID).modal('show');
		},
		error: function(error) { alert('error'); },
	})
})
jQuery(document).on('click', '.saveVulnerability', function(event) {
	event.preventDefault();
	var val = '';
	var ID = jQuery(this).parents('.modal').attr('rowid');
	var Q4BClass = ID.replace('#upstream', '.Q4AUpstreams');
	// var Q4BClass = '';
	jQuery('.upstream:checked').each(function(index) {
		val += jQuery(this).val() +',';
	});
	val = val.replace(/,$/g,'');
	jQuery(ID +'_text').val(val);
	jQuery(Q4BClass).attr('upstream', val);
	if (val) jQuery(ID).addClass('active');
	else jQuery(ID).removeClass('active');
	jQuery('#BCPModal').modal('hide');
})
// BCPR
jQuery(document).on('click', '.BCPRAssetsBtn', function(event) {
	event.preventDefault();
	var values = jQuery(this).attr('data');
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'assets_impacted_options',
			'security': object.ajax_nonce,
			'ai': values,
			'input': 0,
		},
		beforeSend: function() {},
		success: function(response) {
			jQuery('#BCPRModal .modal-body').html(response);
			jQuery('#BCPRModal').modal('show');
		},
		error: function(error) { alert('error'); },
	})
})
jQuery(document).on('click', '.BCPRvulnerabilityBtn', function(event) {
	event.preventDefault();
	var values = jQuery(this).attr('data');
	jQuery.ajax({
		url: object.ajaxurl,
		method : 'post',
		data: {
			'action': 'vulnerability_options',
			'security': object.ajax_nonce,
			'ai': values,
			'input': 0,
		},
		beforeSend: function() {},
		success: function(response) {
			jQuery('#BCPRModal .modal-body').html(response);
			jQuery('#BCPRModal').modal('show');
		},
		error: function(error) { alert('error'); },
	})
	function add_loader(selector, align='left'){
		var data = '<div class="loader active" style="text-align:'+align+'"><span></span> <span></span> <span></span> <span></span> <span></span></div>';
		selector.html(data);
	}
	function remove_loader(selector){
		selector.find('.loader.active').remove();
	}
})