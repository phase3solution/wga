jQuery(function() {
	"use strict";
	//Activate tooltips
	jQuery("[data-toggle='tooltip']").tooltip();
	jQuery("[data-toggle='utility-menu']").click(function() {
		jQuery(this).next().slideToggle(300);
		jQuery(this).toggleClass('open');
		return false;
	});
	// Login Page Flipbox control
	jQuery('#toFlip').click(function() {
		loginFlip();
		return false;
	});
	jQuery('#noFlip').click(function() {
		loginFlip();
		return false;
	});
	// Navbar height : Using slimscroll for sidebar
	if (jQuery('body').hasClass('fixed') || jQuery('body').hasClass('only-sidebar')) {
		jQuery('.sidebar').slimScroll({
			height: (jQuery(window).height() - jQuery(".main-header").height()) + "px",
			color: "rgba(0,0,0,0.8)",
			size: "3px"
		});
	} else {
		var docHeight = jQuery(document).height();
		jQuery('.main-sidebar').height(docHeight);
	}
});
// Sidenav prototypes
jQuery.pushMenu = {
	activate: function(toggleBtn) {
		//Enable sidebar toggle
		jQuery(toggleBtn).on('click', function(e) {
			e.preventDefault();
			//Enable sidebar push menu
			if (jQuery(window).width() > (767)) {
				if (jQuery("body").hasClass('sidebar-collapse')) {
					jQuery("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
				} else {
					jQuery("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
				}
			}
			//Handle sidebar push menu for small screens
			else {
				if (jQuery("body").hasClass('sidebar-open')) {
					jQuery("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
				} else {
					jQuery("body").addClass('sidebar-open').trigger('expanded.pushMenu');
				}
			}
			if (jQuery('body').hasClass('fixed') && jQuery('body').hasClass('sidebar-mini') && jQuery('body').hasClass('sidebar-collapse')) {
				jQuery('.sidebar').css("overflow", "visible");
				jQuery('.main-sidebar').find(".slimScrollDiv").css("overflow", "visible");
			}
			if (jQuery('body').hasClass('only-sidebar')) {
				jQuery('.sidebar').css("overflow", "visible");
				jQuery('.main-sidebar').find(".slimScrollDiv").css("overflow", "visible");
			};
		});
		jQuery(".content-wrapper").click(function() {
			//Enable hide menu when clicking on the content-wrapper on small screens
			if (jQuery(window).width() <= (767) && jQuery("body").hasClass("sidebar-open")) {
				jQuery("body").removeClass('sidebar-open');
			}
		});
	}
};
jQuery.tree = function(menu) {
	var _this = this;
	var animationSpeed = 200;
	jQuery(document).on('click', menu + ' li a', function(e) {
		//Get the clicked link and the next element
		var $this = jQuery(this);
		var checkElement = $this.next();
		//Check if the next element is a menu and is visible
		if ((checkElement.is('.treeview-menu')) && (checkElement.is(':visible'))) {
			//Close the menu
			checkElement.slideUp(animationSpeed, function() {
				checkElement.removeClass('menu-open');
				//Fix the layout in case the sidebar stretches over the height of the window
				//_this.layout.fix();
			});
			checkElement.parent("li").removeClass("active");
		}
		//If the menu is not visible
		else if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
			//Get the parent menu
			var parent = $this.parents('ul').first();
			//Close all open menus within the parent
			var ul = parent.find('ul:visible').slideUp(animationSpeed);
			//Remove the menu-open class from the parent
			ul.removeClass('menu-open');
			//Get the parent li
			var parent_li = $this.parent("li");
			//Open the target menu and add the menu-open class
			checkElement.slideDown(animationSpeed, function() {
				//Add the class active to the parent li
				checkElement.addClass('menu-open');
				parent.find('li.active').removeClass('active');
				parent_li.addClass('active');
			});
		}
		//if this isn't a link, prevent the page from being redirected
		if (checkElement.is('.treeview-menu')) {
			e.preventDefault();
		}
	});
};
// Activate sidenav treemenu 
jQuery.tree('.sidebar');
jQuery.pushMenu.activate("[data-toggle='offcanvas']");
function loginFlip() {
	jQuery('.login-box').toggleClass('flipped');
}
// Button Loading Plugin
jQuery.fn.loadingBtn = function(options) {
	var settings = jQuery.extend({
		text: "Loading"
	}, options);
	this.html('<span class="btn-spinner"></span> ' + settings.text + '');
	this.addClass("disabled");
};
jQuery.fn.loadingBtnComplete = function(options) {
	var settings = jQuery.extend({
		html: "submit"
	}, options);
	this.html(settings.html);
	this.removeClass("disabled");
};