<?php

if(!function_exists('add_action')){
    die("access denied.");
}

function empowercast_admin_customfeeds_columns($data=array()){
	$data['name'] = __('Category Name', 'empowercast');
	$data['feed-slug'] = __('Slug', 'empowercast');
	$data['url'] = __('Feed URL', 'empowercast');
	return $data;
}

add_filter('manage_empowercastadmin_categoryfeeds_columns', 'empowercast_admin_customfeeds_columns');

function empowercast_admin_categoryfeeds(){
	$General = empowercast_get_settings('empowercast_general');

    // If we have empowercast credentials, check if the account has been verified
    $creds = get_option('empowercast_creds');
    empowercast_check_credentials($creds);
    wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );
    ?>

    <h2 class="pp-page-header"><?php echo __('Category Podcasting', 'empowercast'); ?></h2>
    <h3 class="pp-page-h3"><?php echo __('Category Podcasting adds custom podcast settings to specific blog category feeds, allowing you to organize episodes by topic.', 'empowercast'); ?></h3>

    <div class="pp-card-body">
        <div class="pp-row pp-tools-row">
            <div class="pp-col-50">
                <div class="pp-row">
                    <h2 class="pp-page-sub-header">Add Podcast Category</h2>
                </div>

                <div class="form-field form-required">
                    <h3><label for="feed_name"><?php echo __('Select Category', 'empowercast') ?></label></h3>
                    <?php wp_dropdown_categories(array('class'=>'category-list', 'show_option_none'=>__('Select Category', 'empowercast'), 'orderby'=>'name', 'hide_empty'=>0, 'hierarchical'=>1, 'name'=>'term', 'id'=>'term_id')); ?>
                </div>

                <p class="submit"><input style="background-color: white; color: #4e93d9; border: 1px solid #4e93d9" type="submit" class="empowercast_save_button_other pp-tools-button" name="submit" value="<?php echo __('ADD SETTINGS TO CATEGORY FEED', 'empowercast'); ?>"/></p>
            </div>

            <div class="pp-col-50">
                <div class="pp-row">
                    <h2 class="pp-page-sub-header">Why would I use Podcast Categories?</h2>
                </div>

                <ul style="list-style: unset; padding-left: 20px;">
                    <li>
                        <h3 style="font-weight: 400;">You have a podcast that covers two topics that sometimes share same posts and sometimes do not. Use your main podcast feed as a combined feed of both topics 	and use category feeds to distribute topic specific episodes.</h3>
                    </li>
                     <li>
                        <h3 style="font-weight: 400;">You want to use categories to keep episodes separate from each other. Each category can be used to distribute separate podcasts with the main podcast feed combining all categories to provide a network feed</h3>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pp-row pp-tools-row" style="margin-top: 20px;">
            <table class="widefat fixed">
	            <thead>
	                <tr>
                        <?php print_column_headers('empowercastadmin_categoryfeeds'); ?>
	                </tr>
	            </thead>

	            <tfoot>
	                <tr>
                        <?php print_column_headers('empowercastadmin_categoryfeeds', false); ?>
	                </tr>
	            </tfoot>

	            <tbody>
                    <?php
                        $Feeds = array();
                        if(isset($General['custom_cat_feeds'])){
                            $Feeds = $General['custom_cat_feeds'];
                        }

                        $count = 0;
                        foreach($Feeds as $null => $cat_ID){
                            if(empty($cat_ID)){
                                continue;
                            }
                            $category = get_category_to_edit($cat_ID);
                            if(is_wp_error($category)){
                                // $cat_ID does not existing
                                continue;
                            }

                            $columns = empowercast_admin_customfeeds_columns();
                            $hidden = array();

                            if($count % 2 == 0){
                                echo '<tr class="alternate">';
                            } else {
                                echo '<tr>';
                            }

                            $edit_link = admin_url('admin.php?page='. empowercast_admin_get_page() .'&amp;action=empowercast-editcategoryfeed&amp;cat=') . $cat_ID;

                            $feed_title = $category->name;

                            if(!empty($General['cat_casting_podcast_feeds'])){
                                $url = get_category_feed_link($cat_ID, 'podcast');
                            } else {
                                $url = get_category_feed_link($cat_ID);
                            }

                            $short_url = str_replace('http://', '', $url);
                            $short_url = str_replace('www.', '', $short_url);
                            if(strlen($short_url) > 35){
                                $short_url = substr($short_url, 0, 32).'...';
                            }

                            foreach($columns as $column_name=>$column_display_name){
                                $class = "class=\"column-$column_name\"";

                                switch($column_name){
                                    case 'feed-slug': {
                                        echo "<td $class>{$category->slug}";
                                        echo "</td>";

                                    } break;

                                    case 'name': {
                                        echo '<td '.$class.'><strong><a class="row-title" href="'.$edit_link.'" title="' . esc_attr(sprintf(__('Edit "%s"', 'empowercast'), $feed_title)) . '">'. esc_html($feed_title) .'</a></strong><br />';
                                        $actions = array();
                                        $actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit', 'empowercast') . '</a>';
                                        $actions['remove'] = "<a class='submitdelete' href='". admin_url() . wp_nonce_url("admin.php?page=". empowercast_admin_get_page() ."&amp;action=empowercast-delete-category-feed&amp;cat=$cat_ID", 'empowercast-delete-category-feed-' . $cat_ID) . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to remove podcast settings for category feed '%s'\n  'Cancel' to stop, 'OK' to delete.", 'empowercast'), esc_html($feed_title) )) . "') ) { return true;}return false;\">" . __('Remove', 'empowercast') . "</a>";
                                        $action_count = count($actions);
                                        $i = 0;
                                        echo '<div class="row-actions">';
                                        foreach($actions as $action => $linkaction){
                                            ++$i;
                                            ($i == $action_count) ? $sep = '' : $sep = ' | ';
                                            echo '<span class="'.$action.'">'.$linkaction.$sep .'</span>';
                                        }
                                        echo '</div>';
                                        echo '</td>';

                                    } break;

                                    case 'url': {
                                        echo "<td $class><a href='$url' title='". esc_attr(sprintf(__('Visit %s', 'empowercast'), $feed_title))."' target=\"_blank\">$short_url</a>";
                                            echo '<div class="row-actions">';
                                                if(defined('EMPOWERCAST_FEEDVALIDATOR_URL')){
                                                    echo '<span class="'.$action .'"><a href="'. EMPOWERCAST_FEEDVALIDATOR_URL . urlencode( str_replace('&amp;', '&', $url) ) .'" target="_blank">' . __('Validate Feed', 'empowercast') . '</a></span>';
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
        </div>

        <input type="hidden" name="action" value="empowercast-addcategoryfeed" />
        <input type="hidden" name="taxonomy" value="category" />
        <?php wp_nonce_field('empowercast-add-taxonomy-feed'); ?>

        </form> <!-- closing form started on another page -->

        <br>
        <div class="pp-row pp-tools-row">
            <form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page='. empowercast_admin_get_page() ) ?>">
                <input type="hidden" name="action" value="empowercast-category-settings" />
                <?php wp_nonce_field('empowercast-category-settings'); ?>

                <div class="pp-row" style="width: 100%;">
                    <h2 class="pp-page-sub-header">Category Podcast Settings</h2>
                </div>

                <h3 style="width: 100%;">Strict Categories</h3>
                <p><input type="hidden" name="cat_casting_strict" value="0" />
                <label><input type="checkbox" name="cat_casting_strict" value="1" <?php echo ( !empty($General['cat_casting_strict']) ?'checked ':''); ?>/>
                <?php echo __('Select a specific category to each episode for statistics tracking and subscription links.', 'empowercast'); ?></label></p>

                <h3 style="width: 100%;">Podcast Only Feeds</h3>
                <p><input type="hidden" name="cat_casting_podcast_feeds" value="0" />
                <label><input type="checkbox" name="cat_casting_podcast_feeds" value="1" <?php echo ( !empty($General['cat_casting_podcast_feeds']) ?'checked ':''); ?>/>
                <?php echo __('Enable to separate blog posts from podcast episodes.', 'empowercast'); ?></label></p>

                <div class="pp-row" style="width: 100%;">
                    <p style="margin: 0;"><input type="submit" class="empowercast_save_button_other pp-tools-button" name="submit" value="<?php echo __('Save Settings', 'empowercast'); ?>" /></p>
                </div>
            </form>
        </div>
    </div>

    <br>

    <style>
        .pp-col-50 { width: 50%; }
        .column-url { width: 40%; }
        .column-name { width: 30%; }
        .column-feed-slug { width: 15%; }
        .column-episode-count { width: 15%; }
        .category-list { width: 100%; }
    </style>

<?php } ?>
