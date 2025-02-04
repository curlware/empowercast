<?php

// empowercastadmin-find-replace.php
$g_FindReplaceResults = array();
	
// Returns an array of enclosures with key of array = meta_id
function empowercastadmin_find_replace_get_enclosures($find_string){
    $Episodes = array();
    global $wpdb;

    $query = "SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE \"%enclosure\"";
    $results_data = $wpdb->get_results($query, ARRAY_A);

    foreach($results_data as $index=> $row){
        list($url) = @explode("\n", $row['meta_value'], 2);
        $url = trim($url);
        if($find_string == '' || strstr($url, $find_string)){
            $Episodes[ $row['meta_id'] ] = $row;
        }
    }

    return $Episodes;
}
	
function empowercastadmin_find_replace_update_meta($meta_id, $meta_value){
    global $wpdb;
    return $wpdb->update( $wpdb->postmeta, array('meta_value'=>$meta_value), array('meta_id'=>$meta_id) );
}
	
function empowercastadmin_find_replace_process(){
    $wp_remote_options = array();
    $wp_remote_options['user-agent'] = 'Podops EmpowerCast/'.EMPOWERCAST_VERSION;
    $wp_remote_options['httpversion'] = '1.1';

    global $g_FindReplaceResults;
    if(isset($_POST['FindReplace'])){
        $FindReplace = $_POST['FindReplace'];
        $FindReplace['step'] = intval($FindReplace['step']);
        if($FindReplace['step'] == 2 || $FindReplace['step'] == 3){
            $success_count = 0;
            $failed_count = 0;

            $FoundArray = empowercastadmin_find_replace_get_enclosures($FindReplace['find_string']);

            foreach($FoundArray as $meta_id=> $row){
                // empowercast_get_post_meta
                $meta_value = get_post_meta($row['post_id'], $row['meta_key'], true);
                $parts = explode("\n", $meta_value, 2);
                $other_meta_data = false;
                if( count($parts) == 2 )
                    list($old_url, $other_meta_data) = $parts;
                else
                    $old_url = trim($meta_value);

                $old_url = trim($old_url);
                //echo  $old_url;
                $g_FindReplaceResults[ $meta_id ] = $row;
                $g_FindReplaceResults[ $meta_id ]['old_url'] = $old_url;
                $g_FindReplaceResults[ $meta_id ]['find_readable'] = str_replace($FindReplace['find_string'],
                        sprintf('<span class="find_string strong">%s</span>', esc_attr($FindReplace['find_string'])), esc_attr($old_url) );
                $g_FindReplaceResults[ $meta_id ]['replace_readable'] = str_replace($FindReplace['find_string'],
                        sprintf('<span class="replace_string strong">%s</span>', esc_attr($FindReplace['replace_string']) ), esc_attr($old_url) );
                $new_url = str_replace($FindReplace['find_string'],$FindReplace['replace_string'], $old_url);
                $g_FindReplaceResults[ $meta_id ]['new_url'] = $new_url;

                if($FindReplace['step'] == 3 && $FindReplace['find_string'] != '') {
                    $good = true;
                    if(!empty($FindReplace['verify'])) {
                        $response = wp_remote_head( $new_url, $wp_remote_options );
                        // Redirect 1
                        if(!is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302)) {
                            $headers = wp_remote_retrieve_headers( $response );
                            $response = wp_remote_head( $headers['location'], $wp_remote_options );
                        }
                        // Redirect 2
                        if(!is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302)){
                            $headers = wp_remote_retrieve_headers( $response );
                            $response = wp_remote_head( $headers['location'], $wp_remote_options );
                        }
                        // Redirect 3
                        if(!is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302)){
                            $headers = wp_remote_retrieve_headers( $response );
                            $response = wp_remote_head( $headers['location'], $wp_remote_options );
                        }
                        // Redirect 4
                        if(!is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302)){
                            $headers = wp_remote_retrieve_headers( $response );
                            $response = wp_remote_head( $headers['location'], $wp_remote_options );
                        }
                        //$headers = wp_remote_retrieve_headers( $response );

                        //$response = @wp_remote_head( $new_url, $wp_remote_options );
                        if(is_wp_error( $response )){
                            $g_FindReplaceResults[ $meta_id ]['error'] = $response->get_error_message();
                            $good = false;
                        }

                        if($good && isset($response['response']['code']) && ($response['response']['code'] < 200 || $response['response']['code'] > 203)){
                            $g_FindReplaceResults[ $meta_id ]['error'] = 'Error, HTTP '.$response['response']['code'];
                            $good = false;
                        }
                    }

                    if($good) {
                        $DataUpdated = $new_url;
                        if($other_meta_data){
                            $DataUpdated .= "\n". $other_meta_data;
                        }
                        if(update_post_meta( $row['post_id'], $row['meta_key'], $DataUpdated)){
                            $success_count++;
                        } else {
                            $good = false;
                        }
                    }

                    if(!$good){
                        $failed_count++;
                    }
                    $g_FindReplaceResults[ $meta_id ]['success'] = $good;
                }
            }

            if($FindReplace['step'] == 3) {
                if($success_count > 0){
                    empowercast_page_message_add_notice(  sprintf(__('%d URLs updated successfully.', 'empowercast'), $success_count) );
                }
                if($failed_count > 0){
                    empowercast_page_message_add_error(  sprintf(__('%d URLs were not updated.', 'empowercast'), $failed_count) );
                }
                elseif( $FindReplace['find_string'] == '' ){
                    empowercast_page_message_add_notice(  __('Nothing specified to find.', 'empowercast') );
                }
            }
        }
    }

    empowercast_page_message_add_notice(  __('WARNING: Please backup your database before proceeding. Podops EmpowerCast is not responsible for any lost or damaged data resulting from this Find and Replace tool.', 'empowercast') );
}
	
function empowercast_admin_find_replace(){
    $FindReplaceResults = array();

    if(isset($_POST['FindReplace'])){
        $FindReplace = $_POST['FindReplace'];
        $FindReplace['step'] = intval( $FindReplace['step'] );
    } else {
        $FindReplace = array();
        $FindReplace['find_string'] = '';
        $FindReplace['replace_string'] = '';
        $FindReplace['step'] = 1;
    }

    if($FindReplace['step'] == 2){
        $FindReplace['verify'] = true;
    }

    if($FindReplace['step'] == 2 || $FindReplace['step'] == 3) {
        $FindReplaceResults = empowercastadmin_find_replace_get_results();
    }
    ?>

    <script type="text/javascript">
    function VerifyCheck(obj){
        if(!obj.checked && !confirm('<?php echo __('WARNING: Verification prevents changes if the URL entered is invalid.\n\nAre you sure you do not want to verify the URLs?', 'empowercast'); ?>')){
            obj.checked = true;
        }
    }

    function ConfirmReplace(){
        if(confirm('<?php echo __('WARNING: You are about to make permanent changes to your database.\n\nAre you sure you wish to continue?', 'empowercast'); ?>')){
            jQuery('#replace_step').val('3');
            return true;
        }
        return false;
    }
</script>

    <style>
        .find_string {
            background-color: #CFE2F3; /* lt blue */
            padding: 1px;
        }
        .replace_string {
            background-color: #FCE5CD; /* orange */
            padding: 1px;
        }
        .strong {
            font-style:italic;
        }
        dd {
            margin: 2px 2px 2px 10px;
        }
        dt {
            margin: 2px 2px 2px 10px;
        }
    </style>

    <input type="hidden" name="action" value="empowercast-find-replace" />
    <input type="hidden" name="FindReplace[step]" value="<?php echo esc_attr($FindReplace['step']); ?>" id="replace_step" />

    <div class="pp-row">
        <a class="pp-page-back-link" href="admin.php?page=empowercast/empowercastadmin_tools.php"><span>&#8592; EmpowerCast Tools</span></a>
    </div>
    <div class="pp-card-body">

        <div class="pp-row pp-tools-row">
            <h2 class="pp-page-sub-header">Find and Replace Episode URLs</h2>
        </div>

        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">
                Find and replace complete or partial segments of media URLs. Useful if you move your media to a new website or service.
                We recommend using the WP-DB-Backup plugin to backup your database before using this Find and Replace tool.
            </p>
        </div>

        <div class="pp-row pp-tools-row" style="margin-bottom: 30px;">
            <h3 style="margin-right: 30px;">Find Episode URL</h3>
            <input class="find-and-replace-input" type="text" id="find_string" name="FindReplace[find_string]" value="<?php echo esc_attr($FindReplace['find_string']); ?>" maxlength="255" placeholder="MyOldSite.com/">
        </div>
        <div class="pp-row pp-tools-row">
            <h3 style="margin-right: 65px;">Replace with</h3>
            <input class="find-and-replace-input" type="text" id="replace_string" name="FindReplace[replace_string]" value="<?php echo esc_attr($FindReplace['replace_string']); ?>" maxlength="255" placeholder="MyEmpowerCastSite.com/">
        </div>

        <div class="pp-row pp-tools-row">
            <input style="margin: 30px 0 30px 0;" type="submit" name="Submit" id="empowercast_save_button" class="empowercast_save_button_other pp-tools-button" value="<?php echo __('FIND AND PREVIEW CHANGES', 'empowercast'); ?>" onclick="jQuery('#replace_step').val('2');"/>
        </div>

        <?php if($FindReplace['step'] == 2 || $FindReplace['step'] == 3){ ?>
            <hr>
            <div class="pp-row pp-tools-row">
                <h3><?php echo ($FindReplace['step'] == 2 ? __('Preview Changes', 'empowercast') : __('Change Results', 'empowercast')); ?></h3>
            </div>

            <div class="pp-row pp-tools-row">
                <p class="pp-tools-text"><?php echo sprintf( __('Found %d results with "%s"', 'empowercast'), count($FindReplaceResults), "<span class=\"find_string strong\">". esc_attr($FindReplace['find_string']). "</span>"); ?></p>
            </div>

            <ol class="preview-changes-list">
                <?php
                    $rowCount = 1;
                    foreach($FindReplaceResults as $meta_id=> $row){
                        $post_view_link = '<a href="' . get_permalink($row['post_id']) . '" target="_blank">' . get_the_title($row['post_id']) . '</a>';
                        $post_edit_link = '<a href="' . get_edit_post_link($row['post_id']) . '" target="_blank">' . __('Edit Post', 'empowercast') . '</a>';
                        ?>
                        <div class="pp-row pp-tools-row preview-changes-row <?php echo ($rowCount % 2 == 0 ? 'colored-row' : ''); ?>">
                            <li style="margin-left: 20px;">
                                <?php
                                    if($FindReplace['step'] == 3){
                                        echo '<div>';
                                            empowercastadmin_find_replace_status($row['success']);
                                            echo ' &nbsp; ';
                                            if(!empty($row['error'])){
                                                echo $row['error'];
                                            }
                                        echo '</div>';
                                    }
                                ?>
                                Post: <strong><?php echo $post_view_link; ?></strong>
                                <span style="font-size: 90%;">(<?php echo $post_edit_link; ?>)</span>
                                <dl>
                                    <dt>
                                        <?php echo __('Found', 'empowercast') .': '. $row['find_readable']; ?>
                                    </dt>
                                    <dd>
                                        <?php echo __('Replace', 'empowercast') .': '. $row['replace_readable']; ?>
                                        (<a href="<?php echo esc_attr($row['new_url']); ?>" target="_blank"><?php echo __('test link', 'empowercast'); ?></a>)
                                    </dd>
                                </dl>
                            </li>
                        </div>
                    <?php
                    $rowCount++;
                    } ?>
            </ol>
        <?php } ?>

        <?php if($FindReplace['step'] == 2 && count($FindReplaceResults) > 0){ ?>
            <div class="pp-row pp-tools-row">
                <input style="margin: 30px 0 30px 0;" type="submit" name="Submit" id="empowercast_save_button" class="empowercast_save_button_other pp-tools-button" value="<?php echo __('COMMIT CHANGES', 'empowercast'); ?>" onclick="return ConfirmReplace()" />
                <input style="margin: 40px 10px 0 20px;" type="checkbox" name="FindReplace[verify]" value="1" <?php if(!empty($FindReplace['verify'])) echo 'checked'; ?> onchange="return VerifyCheck(this)"/>
                <strong style="margin: 38px 10px 0 0;"><?php echo __('Verify URLs', 'empowercast'); ?></strong>
                <p style="margin: 36px 10px 0 0;">(Does not change URL if invalid)</p>
                <a style="margin: 37px 10px 0 10px;" class="pp-page-back-link" href="admin.php?page=empowercast/empowercastadmin_tools.php">CANCEL REPLACEMENT</a>
            </div>
        <?php } elseif ($FindReplace['step'] == 3 || ($FindReplace['step'] == 2 && count($FindReplaceResults) == 0)){ ?>
            <p class="submit">
                <strong><a href="<?php echo admin_url('admin.php?page=empowercast/empowercastadmin_tools.php'); ?>"><?php echo __('EmpowerCast Tools', 'empowercast'); ?></a></strong>
            </p>
        <?php } ?>

        <div class="pp-row pp-tools-row">
            <p style="margin-bottom: 0;" class="pp-tools-text"><?php echo sprintf( __('We recommend using the %s plugin to backup your database before using this Find and Replace tool.', 'empowercast'), '<a href="http://wordpress.org/extend/plugins/wp-db-backup/" target="_blank">'. __('WP-DB-Backup', 'empowercast') .'</a>' ); ?></p>
        </div>
    </div>
<?php }
	
function empowercastadmin_find_replace_status($success=true){
    $img = 'yes.png';
    $color = '#458045';
    $text = __('Success', 'empowercast');
    if($success == false){ // Failed takes precedence over warning
        $img = 'no.png';
        $color = '#CC0000';
        $text = __('Failed', 'empowercast');
    }
    ?>

    <img src="<?php echo admin_url(); ?>/images/<?php echo $img; ?>" style="vertical-align:text-top;"/>
    <strong style="color:<?php echo $color; ?>;"><?php echo $text; ?></strong>
<?php }
	
function empowercastadmin_find_replace_get_results(){
    global $g_FindReplaceResults;
    if(!is_array($g_FindReplaceResults)){
        return array();
    }
    return $g_FindReplaceResults;
}

?>