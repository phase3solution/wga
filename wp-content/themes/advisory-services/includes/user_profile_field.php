<?php
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );
function extra_user_profile_fields( $user ) { 
    $admin = wp_get_current_user();
    if (in_array('viewer', $user->roles) && array_intersect(['administrator', 'advisor'], $admin->roles)) {
        echo "<h3>Extra profile information</h3>";
        echo '<table class="form-table extraProfileInformation">';
            echo '<tr>';
                $showMedia = esc_attr( get_the_author_meta( 'mediaPage', $user->ID ));
                if ($showMedia) { $mediaPageyes = ' checked'; $mediaPageNo = ''; } 
                else { $mediaPageyes = ''; $mediaPageNo = ' checked'; }
                echo '<th><label for="mediaPage">Show Media page</label></th>';
                echo '<td>';
                echo '<label for="mediaPage-yes"><input type="radio" name="mediaPage" id="mediaPage-yes" value="1"'. $mediaPageyes .'> Yes</label>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<label for="mediaPage-no"><input type="radio" name="mediaPage" id="mediaPage-no" value="0"'. $mediaPageNo .'> No</label>';
                echo '</td>';
            echo '<tr>';
            echo '<tr>';
                $specialUser = esc_attr( get_the_author_meta( 'spuser', $user->ID ));
                if ($specialUser) { $spuserYes = ' checked'; $spuserNo = ''; } 
                else { $spuserYes = ''; $spuserNo = ' checked'; }
                echo '<th><label for="spuser">Special user</label></th>';
                echo '<td>';
                echo '<label for="spuser-yes"><input type="radio" name="spuser" id="spuser-yes" value="1"'. $spuserYes .'> Yes</label>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<label for="spuser-no"><input type="radio" name="spuser" id="spuser-no" value="0"'. $spuserNo .'> No</label>';
                echo '</td>';
            echo '<tr>';
            echo '<tr>';
                $specialBIAUser = esc_attr(get_the_author_meta( 'specialbiauser', $user->ID ));
                if ($specialBIAUser) { $specialbiauserYes = ' checked'; $specialbiauserNo = ''; } 
                else { $specialbiauserYes = ''; $specialbiauserNo = ' checked'; }
                echo '<th><label for="spuser">Special BIA user</label></th>';
                echo '<td>';
                echo '<label for="specialbiauser-yes"><input type="radio" name="specialbiauser" id="specialbiauser-yes" value="1"'. $specialbiauserYes .'> Yes</label>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<label for="specialbiauser-no"><input type="radio" name="specialbiauser" id="specialbiauser-no" value="0"'. $specialbiauserNo .'> No</label>';
                echo '</td>';
            echo '<tr>';
            echo '<tr>';
                $specialRiskUser = esc_attr(get_the_author_meta( 'specialriskuser', $user->ID ));
                if ($specialRiskUser) { $specialriskuserYes = ' checked'; $specialriskuserNo = ''; } 
                else { $specialriskuserYes = ''; $specialriskuserNo = ' checked'; }
                echo '<th><label for="spuser">Special Risk Assessment user</label></th>';
                echo '<td>';
                echo '<label for="specialriskuser-yes"><input type="radio" name="specialriskuser" id="specialriskuser-yes" value="1"'. $specialriskuserYes .'> Yes</label>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<label for="specialriskuser-no"><input type="radio" name="specialriskuser" id="specialriskuser-no" value="0"'. $specialriskuserNo .'> No</label>';
                echo '</td>';
            echo '<tr>';
            echo '<tr>';
                $specialMTAUser = esc_attr(get_the_author_meta( 'specialmtauser', $user->ID ));
                if ($specialMTAUser) { $specialmtauserYes = ' checked'; $specialmtauserNo = ''; } 
                else { $specialmtauserYes = ''; $specialmtauserNo = ' checked'; }
                echo '<th><label for="spuser">Special Risk MTA user</label></th>';
                echo '<td>';
                echo '<label for="specialmtauser-yes"><input type="radio" name="specialmtauser" id="specialmtauser-yes" value="1"'. $specialmtauserYes .'> Yes</label>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<label for="specialmtauser-no"><input type="radio" name="specialmtauser" id="specialmtauser-no" value="0"'. $specialmtauserNo .'> No</label>';
                echo '</td>';
            echo '<tr>';
            echo '<tr>';
                $specialIHCUser = esc_attr(get_the_author_meta( 'specialihcuser', $user->ID ));
                if ($specialIHCUser) { $specialihcuserYes = ' checked'; $specialihcuserNo = ''; } 
                else { $specialihcuserYes = ''; $specialihcuserNo = ' checked'; }
                echo '<th><label for="spuser">Special Risk IHC user</label></th>';
                echo '<td>';
                echo '<label for="specialihcuser-yes"><input type="radio" name="specialihcuser" id="specialihcuser-yes" value="1"'. $specialihcuserYes .'> Yes</label>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<label for="specialihcuser-no"><input type="radio" name="specialihcuser" id="specialihcuser-no" value="0"'. $specialihcuserNo .'> No</label>';
                echo '</td>';
            echo '<tr>';
            echo '<tr>';
                $specialSFIAUser = esc_attr(get_the_author_meta( 'specialsfiauser', $user->ID ));
                if ($specialSFIAUser) { $specialsfiauserYes = ' checked'; $specialsfiauserNo = ''; } 
                else { $specialsfiauserYes = ''; $specialsfiauserNo = ' checked'; }
                echo '<th><label for="spuser">Special SFIA user</label></th>';
                echo '<td>';
                echo '<label for="specialsfiauser-yes"><input type="radio" name="specialsfiauser" id="specialsfiauser-yes" value="1"'. $specialsfiauserYes .'> Yes</label>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<label for="specialsfiauser-no"><input type="radio" name="specialsfiauser" id="specialsfiauser-no" value="0"'. $specialsfiauserNo .'> No</label>';
                echo '</td>';
            echo '<tr>';
        echo '</table>'; 
    } ?>
<style>
    .imageWrapper{width: 200px;position: relative;margin-top: 15px;}
    .imagePreview{max-width: 100%;height: auto;}
    .removeBtn{position: absolute; top: -5px; right: -5px; height: 20px; width: 20px; border-radius: 50%; background: #000; color: #fff; text-align: center; font-size: 12px; font-weight: bold; line-height: 18px; cursor: pointer;}
    .form-table.extraProfileInformation{width: 345px;}
    .form-table.extraProfileInformation tr:hover{ background: #d5d5d575; }
    .form-table.extraProfileInformation th, .form-table.extraProfileInformation td { padding: 7px 10px 7px 0; }
    .form-table.extraProfileInformation th{ padding-left: 7px; }
</style>
<script>
    jQuery(document).ready( function( $ ) {
        // upload media image
        $('#userLogo_button').click(function(e) {
            e.preventDefault();
            var mainObj = $(this);
            var image = wp.media({
                title: 'Upload Image',
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open()
            .on('select', function(e){
                // This will return the selected image from the Media Uploader, the result is an object
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                // Output to the console uploaded_image
                //console.log(uploaded_image);
                var image_url = uploaded_image.toJSON().url;
                $('.imagePreview').prop("src",image_url);
                $('#userLogo').val( image_url );
                $('.imageWrapper').removeClass('hidden');
            });
        });
        // remove media image
        $(document).on( 'click', '.removeBtn', function () {
          $('#userLogo').val('');
            $('.imageWrapper').addClass('hidden');
        });
    });
</script>
    <?php
}
add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );
function save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) return false;
    if (isset($_POST['mediaPage'])) update_user_meta( $user_id, 'mediaPage', $_POST['mediaPage']);
    if (isset($_POST['userLogo'])) update_user_meta( $user_id, 'userLogo', $_POST['userLogo']);
    if (isset($_POST['userDashboard'])) update_user_meta( $user_id, 'userDashboard', $_POST['userDashboard']);
    if (isset($_POST['spuser'])) {
        $user = new WP_User( $user_id );
        if(count(array_intersect($user->roles, ['administrator', 'advisor'])) < 1){
            if (trim($_POST['spuser'])) $user->add_cap( 'upload_files' );
            else $user->remove_cap( 'upload_files' );
        }
        update_user_meta( $user_id, 'spuser', $_POST['spuser']);
        update_user_meta( $user_id, 'specialbiauser', $_POST['specialbiauser']);
        update_user_meta( $user_id, 'specialriskuser', $_POST['specialriskuser']);
        update_user_meta( $user_id, 'specialmtauser', $_POST['specialmtauser']);
        update_user_meta( $user_id, 'specialihcuser', $_POST['specialihcuser']);
        update_user_meta( $user_id, 'specialsfiauser', $_POST['specialsfiauser']);
    }
}