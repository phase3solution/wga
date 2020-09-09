<?php if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('login'));
}
global $sfia_premission;
if (is_singular(json_decode(ALL_POST_TYPES))) {
    if (isset($_GET['area']) && !empty($_GET['area'])) {
        if (!advisory_area_exists(get_the_ID(), advisory_id_from_string($_GET['area']))) {
            wp_safe_redirect(404);
        }
        if (!isset($_GET['view']) && !isset($_GET['edit'])) {
            if (!advisory_has_form_view_permission(get_the_ID())) {
                wp_safe_redirect(404);
            }
        }
        if (isset($_GET['view'])) {
            if ($_GET['view'] == 'true') {
                if (!advisory_has_survey_view_permission(get_the_ID())) {
                    wp_safe_redirect(404);
                }
            } else {
                wp_safe_redirect(404);
            }
        }
        if (isset($_GET['edit'])) {
            if ($_GET['edit'] == 'true') {
                if (isset($_GET['section'])) {
                    if (!advisory_has_dmm_view_permission(get_the_ID())) { wp_safe_redirect(404); }
                } else{
                    if (!advisory_has_survey_edit_permission(get_the_ID())) { wp_safe_redirect(404); }
                }
            } else {
                wp_safe_redirect(404);
            }
        }
    } else {
        wp_safe_redirect(404);
    }
}
if (is_page_template(['scorecard.php', 'page-scorecard.php'])) {
    if (!advisory_has_scorecard_view_permission(@$_GET['view'])) {
        wp_safe_redirect(404);
    }
}
if (is_page_template(['rr.php', 'page-rr.php'])) {
    if (!advisory_get_user_company_id()) {
        wp_safe_redirect(404);
    }
} 
$user_data = wp_get_current_user();
$sfia_premission = advisory_sfia_get_permission_level($user_data->ID);
$user_company_data = advisory_get_user_company();
$logo = IMAGE_DIR_URL.'logo.png';
if ($user_company_data) {
    $logo = $user_company_data ? companyLogo($user_company_data->term_id) : '';
    $companyData = get_term_meta($user_company_data->term_id, 'company_data', true);
    $dashboard = @$companyData['userDashboard'] == 'Dashboard B' ? 'B' : false;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700|Roboto:300,400,500,700,900" rel="stylesheet" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries-->
    <!--if lt IE 9
    script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js')
    script(src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js')
    -->
    <?php wp_head(); ?>
</head>
<body <?php body_class('sidebar-mini fixed'); ?>>
<div class="wrapper">
    <!-- Navbar-->
    <header class="main-header hidden-print">
        <a class="logo" href="<?php echo home_url('dashboard') ?>"><img style="max-width: 230px;" src="<?php echo $logo; ?>" alt="logo"></a>
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a class="sidebar-toggle" href="#" data-toggle="offcanvas"></a>
            <!-- Navbar Right Menu-->
            <div class="navbar-custom-menu">
                <?php
                if (current_user_can('viewer')) {
                    if (is_singular(array('prr', 'mta', 'ihc', 'itsm', 'cra', 'drm'))) {
                        $data = get_post_meta(get_the_ID(), 'form_opts', true);
                        echo '<span class="top-heading">' . $data['desc'] . ': ' . $user_company_data->name . '</span>';
                    } else if (is_page_template(['page-dashboard.php', 'dashboard.php'])) {
                        echo '<span class="top-heading">' . get_the_title() . ': ' . $user_company_data->name . '</span>';
                    } else {
                        echo '<span class="top-heading">' . $user_company_data->name . '</span>';
                    }
                } ?>
                <?php
                    global $user_switching;
                    $users = advisory_registered_users();
                    if (!empty($users) && !current_user_can('viewer')) {
                        echo '<span class="top-heading"><select class="switch-user"><option value="">Switch User</option>';
                        foreach ($users as $user_id => $user) {
                            $user = get_user_by('id', $user_id);
                            $link = user_switching::maybe_switch_url($user);
                            $company_data = advisory_get_user_company($user_id);
                            if ($link) {
                                $link = add_query_arg('redirect_to', home_url('dashboard'), $link);
                                echo '<option value="' . esc_url($link, $user) . '">' . $user->display_name . ':' . $company_data->name . '</option>';
                            }
                        }
                        echo '</select></span>';
                    }
                    if($user_switching->get_old_user()) {
                        $switch_back_url = $user_switching->switch_back_url($user_switching->get_old_user());
                        echo '<span class="top-heading"><a href="' . esc_url(add_query_arg(array('redirect_to' => home_url('dashboard')), $switch_back_url)) . '">Back To Admin</a></span>';
                    }
                ?>
                <ul class="top-nav">
                    <!-- User Menu-->
                    <li class="hidden-xs"><?php echo 'Welcome ' . $user_data->first_name . ' ' . $user_data->last_name ?></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <img class="img-circle" src="<?php echo advisory_get_user_avatar($user_data->ID) ?>" width="24" height="24" alt="avatar">
                            <span class="user-name"><?php echo $user_data->user_login ?></span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu settings-menu">
                            <li><a href="<?php echo home_url('profile') ?>"><i class="fa fa-user fa-lg"></i> Profile</a></li>
                            <li><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Side-Nav-->
    <aside class="main-sidebar hidden-print">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="<?php echo home_url('dashboard') ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/dashboard.png" alt=""><span>Dashboard</span></a></li>
                <?php 
                // echo '<li>TEST == '.$sfia_premission.'</li>';
                if (current_user_can('viewer') && advisory_get_user_company_id()) {
                    $forms = advisory_get_active_forms(advisory_get_user_company_id());
                    if (!empty($forms)) {
                        if ($user_switching->get_old_user()) {
                            foreach ($forms as $form) {
                                if (get_post_type($form) == 'csa') getActiveCSAMenu($form);
                                else if (get_post_type($form) == 'sfia') echo advisory_sfia_get_menu_items($form);
                                else if (get_post_type($form) == 'sfiar') echo advisory_sfiar_get_menu_items($form, $user_company_data->term_id);
                                else {
                                    $form_meta = get_post_meta($form, 'form_opts', true);
                                    echo '<li><a href="#"><img src="' . @$form_meta['icon'] .'" alt=""><span>'. advisory_get_form_name($form) . '</span></a>';
                                        if (!empty($form_meta['areas'])) {
                                            echo '<ul class="treeview-menu">';
                                            foreach ($form_meta['areas'] as $area) {
                                                echo '<li><a href="' . get_the_permalink($form) . '?area=' . advisory_id_from_string($area['name']) . '"><img src="' . (empty($area['icon_menu']) ? $form_meta['icon'] : $area['icon_menu']) .'"><span>' . $area['name'] . '</span></a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    echo '</li>';
                                }
                            }
                        } else {
                            foreach ($forms as $form) {
                                if (get_post_type($form) == 'csa') getActiveCSAMenu($form);
                                else if (get_post_type($form) == 'sfia') echo advisory_sfia_get_menu_items($form);
                                else if (get_post_type($form) == 'sfiar') echo advisory_sfiar_get_menu_items($form, $user_company_data->term_id);
                                else {
                                    $form_meta = get_post_meta($form, 'form_opts', true);
                                    echo '<li><a href="#"><img src="' . @$form_meta['icon'] .'" alt=""><span>' . advisory_get_form_name($form) . '</span></a>';
                                        if (!empty($form_meta['areas'])) {
                                            echo '<ul class="treeview-menu">';
                                            foreach ($form_meta['areas'] as $area) {
                                                echo '<li><a href="' . get_the_permalink($form) . '?view=true&area=' . advisory_id_from_string($area['name']) . '"><img src="' . (empty($area['icon_menu']) ? $form_meta['icon'] : $area['icon_menu']) .'"><span>' . $area['name'] . '</span></a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    echo '</li>';
                                }
                            }
                        }
                    }
                    // echo '<hr>'; echo '<style>.main-sidebar{height: auto !important; width: 100%; position: relative;}</style>';
                    // echo '<br><pre>'. print_r($forms, true) .'</pre>';
                    // All Registers
                    getArchivedMenuFor('risk', 'DR Risk Register', 'Risk Register', 'icon-rr.png', false);
                    getArchivedMenuFor('bcp', 'BCP Register', 'BCP Register', 'icon-rr.png', false);
                    getArchivedMenuFor('prr', 'Project Risk Register', 'Project Risk Register', 'icon-prr.png');
                    getDynamicRegisterMenuFor('drmrr', 'DRM Register', 'DRM Register', 'icon-drmrr.png');
                    getDynamicRegisterMenuFor('dmmr', 'DMM Register', 'DMM Register', 'icon-rrR.png');
                    if ($dashboard) getDynamicRegisterMenuFor('mtar', 'MTA Register', 'MTA Register', 'icon-ihcr.png');
                    else getDynamicRegisterMenuFor('ihcr', 'IHC Register', 'IHC Register', 'icon-ihcr.png');
                    if (empty($forms)) echo '<li><a class="text-center">' . __('', 'advisory') . '</a></li>';
                    if (get_the_author_meta( 'spuser', $user_data->ID )) {
                        echo '<hr>';
                        $pageLink = get_permalink(get_page_by_title('Editor'));
                        $pdfEdit = get_permalink(get_page_by_title('IHC PDF'));
                        if ($dashboard) {
                            echo '<li><a href="'. $pageLink .'?p=mta"><img src="'.P3_TEMPLATE_URI.'/images/menu-forms.png"> MTA Edit</a></li>';
                            echo '<hr>';
                            echo '<li><a href="'. $pdfEdit .'?p=mta"><img src="'.P3_TEMPLATE_URI.'/images/menu-forms.png"> MTA PDF</a></li>';
                        } else {
                            echo '<li><a href="'. $pageLink .'?p=ihc"><img src="'.P3_TEMPLATE_URI.'/images/menu-forms.png"> IHC Edit</a></li>';
                            echo '<hr>';
                            echo '<li><a href="'. $pdfEdit .'?p=ihc"><img src="'.P3_TEMPLATE_URI.'/images/menu-forms.png"> IHC PDF</a></li>';
                        }
                        // echo '<li><a href="'. $pdfEdit .'?p=bia"><img src="'.P3_TEMPLATE_URI.'/images/menu-forms.png"> BIA PDF</a></li>';
                    }
                } else {
                    echo '<li><a href="' . admin_url('upload.php') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-media.png"> Media</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=mta') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> MTA</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=ihc') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> IHC</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=itsm') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> ITSM</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=cra') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> Cloud Readiness</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=bia') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> BIA</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=risk') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> Risk</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=bcp') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> BCP Assessment</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=drm') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> DR Maturity</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=dmm') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> DMM Assessment</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=itcl') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> ITCL MTA</a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=csa') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> CSA </a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=sfia') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> SFIA </a></li>';
                    echo '<li><a href="' . admin_url('edit.php?post_type=sfiats') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-forms.png"> Technical Surveys </a></li>';
                    echo '<li><a href="' . admin_url('users.php') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-user.png"> Users</a></li>';
                    echo '<li><a href="' . admin_url('admin.php?page=p3s-options') .'"><img src="' .P3_TEMPLATE_URI.'/images/menu-settings.png"> Options</a></li>';
                } ?>
            </ul>
        </section>
    </aside>