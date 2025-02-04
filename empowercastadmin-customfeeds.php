<?php

if(!function_exists('add_action')){
    die("access denied.");
}

function empowercast_admin_customfeeds_columns($data=array()){
	$data['name'] = __('Name', 'empowercast');
	$data['feed-slug'] = __('Slug', 'empowercast');
	$data['episode-count'] = __('Episodes', 'empowercast');
	$data['url'] = __('URL', 'empowercast');
	return $data;
}

add_filter('manage_empowercastadmin_customfeeds_columns', 'empowercast_admin_customfeeds_columns');

function empowercast_admin_customfeeds(){
	$General = empowercast_get_settings('empowercast_general');

    // If we have empowercast credentials, check if the account has been verified
    $creds = get_option('empowercast_creds');
    empowercast_check_credentials($creds);
    wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );
    ?>

    <h2 class="pp-page-header"><?php echo __('Custom Podcast Channels', 'empowercast'); ?></h2>
    <h3 class="pp-page-h3"><?php echo __('Custom podcast channels allow you to associate multiple media files and/or formats to one blog post.', 'empowercast'); ?></h3>

    <div class="pp-card-body">
        <div class="pp-row pp-tools-row">
            <div class="pp-col-50">
                <div class="pp-row">
                    <h2 class="pp-page-sub-header">Add Podcast Channel</h2>
                </div>

                <div class="form-field form-required">
                    <h3><label for="feed_name">Channel Name</label></h3>
                    <input name="feed_name" id="feed_name" type="text" value="" size="40" style="width: 92%;"/>
                    <p><?php echo __('The name is used for use within the administration area only.', 'empowercast'); ?></p>
                </div>

                <div class="form-field">
                    <h3><label for="feed_slug">Feed Slug</label></h3>
                    <div class="pp-row">
                        <h4 style="margin: 0; padding-top: 10px;">MyPodcast.com/</h4>
                        <input name="feed_slug" id="feed_slug" type="text" value="" size="40" style="width: 80%;"/>

                    </div>
                    <p><?php echo __('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'empowercast'); ?></p>
                </div>
            </div>
            <div class="pp-col-50">
                <div class="pp-row">
                    <h2 class="pp-page-sub-header">Why would I use Channels?</h2>
                </div>

                <ul style="list-style: unset; padding-left: 20px;">
                    <li>
                        <h3 style="font-weight: 400;">You want to distribute both an mp3 and an ogg version of your podcast. Use the default podcast channel for your mp3 media and create a custom channel for your ogg media.</h3>
                    </li>
                     <li>
                        <h3 style="font-weight: 400;">You have a video podcast with multiple file formats. Use the default podcast channel for the main media that you want to appear on your blog (e.g. m4v). Create additional channels for the remaining formats (e.g. wmv, mov, mpeg).</h3>
                    </li>
                     <li>
                        <h3 style="font-weight: 400;">You create two versions of your podcast, a 20 minute summary and a full 2 hour episode. Use the default channel for your 20 minute summary episodes and create a new custom channel for your full length episodes.</h3>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pp-row pp-tools-row" style="margin-top: 20px; width: 50%;">
             <?php if(!empty($General['premium_caps'])){ ?>
                <p style="margin: 0;"><strong><a href="<?php echo admin_url() . wp_nonce_url("admin.php?page=empowercast/empowercastadmin_customfeeds.php&amp;action=empowercast-remove-feed-caps", 'empowercast-remove-feed-caps'); ?>"><?php echo __('Remove Password Protection Capabilities for Control of Which Users can Access Your Podcasts', 'empowercast'); ?></a></strong>  (<?php echo __('Also kown as Premium Content', 'empowercast'); ?>)</p>

                <p>
                    <?php echo __('Password protection capabilities for custom podcast channel feeds lets you control who can listen and view your
                    podcast. This feature allows you to password-protect custom podcast channels by adding a new role called "Premium
                    Subscriber." Only users with the "Premium Subscriber" role have access to your password protected custom podcast
                    channels. Due to this feature\'s complexity, it is not supported by Podops.com.', 'empowercast'); ?>
                </p>
            <?php } else { ?>
                <p style="margin: 0;"><strong><a href="<?php echo admin_url() . wp_nonce_url("admin.php?page=empowercast/empowercastadmin_customfeeds.php&amp;action=empowercast-add-feed-caps", 'empowercast-add-feed-caps'); ?>"><?php echo __('Add Password Protection Capabilities for Control of Which Users can Access Your Podcasts', 'empowercast'); ?></a></strong> (<?php echo __('Also known as Premium Content', 'empowercast'); ?>)</p>
                <p>
                    <?php echo __('Adding password protection capabilities for custom podcast channel feeds lets you control who can listen and view your
                    podcast. This feature allows you to password-protect custom podcast channels by adding a new role called "Premium
                    Subscriber." Only users with the "Premium Subscriber" role have access to your password protected custom podcast
                    channels. Due to this feature\'s complexity, it is not supported by Podops.com.', 'empowercast'); ?>
                </p>
            <?php } ?>
        </div>

        <div class="pp-row pp-tools-row">
            <p class="submit"><input type="submit" class="empowercast_save_button_other pp-tools-button" name="submit" value="<?php echo __('Add Podcast Channel', 'empowercast'); ?>" /></p>
        </div>

        <table class="widefat fixed">
            <thead>
                <tr><?php print_column_headers('empowercastadmin_customfeeds'); ?></tr>
            </thead>

            <tbody>
                <?php
                $Feeds = array('podcast'=>__('Podcast', 'empowercast') );

                if(isset($General['custom_feeds']['podcast'])){
                    $Feeds = $General['custom_feeds'];
                } elseif( is_array($General['custom_feeds'])){
                    $Feeds += $General['custom_feeds'];
                }

                asort($Feeds, SORT_STRING); // Sort feeds
                $count = 0;

                foreach($Feeds as $feed_slug => $feed_title){
                    $feed_slug = esc_attr($feed_slug); // Precaution
                    $episode_total = empowercast_admin_episodes_per_feed($feed_slug);
                    $columns = empowercast_admin_customfeeds_columns();
                    $hidden = array();

                    if($feed_slug == 'podcast'){
                        $feed_title = __('Podcast', 'empowercast');
                    }

                    if($count % 2 == 0){
                        echo '<tr class="alternate">';
                    } else {
                        echo '<tr>';
                    }

                    foreach($columns as $column_name=>$column_display_name){
                        $class = "class=\"column-$column_name\"";
                        $edit_link = admin_url('admin.php?page='. empowercast_admin_get_page() .'&amp;action=empowercast-editfeed&amp;feed_slug=') . $feed_slug;
                        $url = get_feed_link($feed_slug);
                        $short_url = str_replace('http://', '', $url);
                        $short_url = str_replace('www.', '', $short_url);


                        if(strlen($short_url) > 35){
                            $short_url = substr($short_url, 0, 32).'...';
                        }

                        switch($column_name){
                            case 'feed-slug': {
                                echo "<td $class>$feed_slug";
                                echo "</td>";
                            } break;

                            case 'name': {

                                echo '<td '.$class.'><strong><a class="row-title" href="'.$edit_link.'" title="' . esc_attr(sprintf(__('Edit "%s"', 'empowercast'), $feed_title)) . '">'. esc_html($feed_title) .'</a></strong>'. ( $feed_slug == 'podcast' ?' ('. __('default channel', 'empowercast') .')':'').'<br />';
                                $actions = array();
                                $actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit', 'empowercast') . '</a>';
                                $actions['delete'] = "<a class='submitdelete' href='". admin_url() . wp_nonce_url("admin.php?page=". empowercast_admin_get_page() ."&amp;action=empowercast-delete-feed&amp;feed_slug=$feed_slug", 'empowercast-delete-feed-' . $feed_slug) . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to delete feed '%s'\n  'Cancel' to stop, 'OK' to delete.", 'empowercast'), esc_attr($feed_title) )) . "') ) { return true;}return false;\">" . __('Delete', 'empowercast') . "</a>";
                                if(!isset($General['custom_feeds'][ $feed_slug ])){
                                    unset($actions['delete']);
                                }
                                $action_count = count($actions);
                                $i = 0;
                                echo '<div class="row-actions">';
                                foreach ($actions as $action => $linkaction){
                                    ++$i;
                                    ($i == $action_count) ? $sep = '' : $sep = ' | ';
                                    echo '<span class="'.$action.'">'.$linkaction.$sep .'</span>';
                                }
                                echo '</div>';
                                echo '</td>';
                            } break;

                            case 'url': {
                                echo "<td $class><a href='$url' title='". esc_attr(sprintf(__('Visit %s', 'empowercast'), $feed_title))."' target=\"_blank\">". esc_html($short_url) ."</a>";
                                    echo '<div class="row-actions">';
                                    if(defined('EMPOWERCAST_FEEDVALIDATOR_URL')){
                                        echo '<span class="'.$action .'"><a href="'. EMPOWERCAST_FEEDVALIDATOR_URL . urlencode($url) .'" target="_blank">' . __('Validate Feed', 'empowercast') . '</a></span>';
                                    }
                                    echo '</div>';
                                echo "</td>";

                            } break;

                            case 'episode-count': {

                                echo "<td $class>$episode_total";
                                echo "</td>";

                            } break;

                            default: {
                            } break;
                        }
                    }

                    echo "\n    </tr>\n";
                    $count++;
                }
            ?>
            </tbody>
    </table>

        <?php if(!isset($General['custom_feeds'][ $feed_slug ])) { ?>
            <p style="margin-top: 20px;"><?php echo sprintf( __('Note: The default channel "Podcast" is currently using global EmpowerCast settings. Click %s to customize the default "Podcast" channel.', 'empowercast'),
            '<a href="'. admin_url('admin.php?page='. empowercast_admin_get_page() .'&amp;action=empowercast-editfeed&amp;feed_slug=podcast') .'">'. __('Edit', 'empowercast') .'</a>'); ?>
            </p>
        <?php } ?>
    </div>

    <div id="ajax-response"></div>
    <input type="hidden" name="action" value="empowercast-addfeed"/>
<?php } ?>

<style>
    .pp-col-50 { width: 50%; }
    .column-url { width: 40%; }
    .column-name { width: 30%; }
    .column-feed-slug { width: 15%; }
    .column-episode-count { width: 15%; }
</style>
