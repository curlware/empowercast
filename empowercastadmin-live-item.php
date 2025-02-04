<?php

if(!function_exists('add_action')){
    die("access denied.");
}


function empowercast_admin_live_item(){
    $General = empowercast_get_settings('empowercast_general');
    $hasChannels = isset($General['channels']) && $General['channels'] == 1;
    $hasCats = isset($General['cat_casting']) && $General['cat_casting'] == 1;
    $hasTax = isset($General['taxonomy_podcasting']) && $General['taxonomy_podcasting'] == 1;
    $hasPT = isset($General['posttype_podcasting']) && $General['posttype_podcasting'] == 1;

    $baseUrl = admin_url()."admin.php?page=empowercastadmin_basic&action=empowercast-editfeed&tab=live-item-tab&feed_slug=podcast";
    $channelDefaultURL = admin_url()."admin.php?page=empowercast/empowercastadmin_customfeeds.php&action=empowercast-editfeed&tab=live-item-tab&";
    $catDefaultURL = admin_url().'admin.php?page=empowercast/empowercastadmin_categoryfeeds.php&action=empowercast-editcategoryfeed&tab=live-item-tab&';
    $taxDefaultURL = admin_url().'admin.php?page=empowercast/empowercastadmin_taxonomyfeeds.php&action=empowercast-edittaxonomyfeed&tab=live-item-tab&';
    $ptDefaultURL = admin_url().'admin.php?page=empowercast/empowercastadmin_posttypefeeds.php&action=empowercast-editposttypefeed&tab=live-item-tab&';

    // If we have empowercast credentials, check if the account has been verified
    $creds = get_option('empowercast_creds');
    empowercast_check_credentials($creds);
    wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );
    ?>

    <script>
        let defaultChannelURL = '<?php echo $channelDefaultURL; ?>';
        let defaultCatURL = '<?php echo $catDefaultURL; ?>';
        let defaultTaxURL = '<?php echo $taxDefaultURL; ?>';
        let defaultPtURL = '<?php echo $ptDefaultURL; ?>';

        jQuery(document).ready(function() {
            jQuery("#channel-selector").change(function () {
                jQuery("#channel-link").attr("href", encodeURI(defaultChannelURL + jQuery("#channel-selector").val()))
            });

            jQuery("#cat-selector").change(function () {
                jQuery("#cat-link").attr("href", encodeURI(defaultCatURL + jQuery("#cat-selector").val()))
            });

            jQuery("#tax-selector").change(function () {
                jQuery("#tax-link").attr("href", encodeURI(defaultTaxURL + jQuery("#tax-selector").val()))
            });

            jQuery("#pt-selector").change(function () {
                jQuery("#pt-link").attr("href", encodeURI(defaultPtURL + jQuery("#pt-selector").val()))
            });
        });
    </script>

    <h2 class="pp-page-header"><?php echo __('Live Item Tag', 'empowercast'); ?></h2>
    <h3 class="pp-page-h3"><?php echo __('This is a new initiative as part of Podcasting 2.0. The Live Item tag is for those with a live component to your show, whether it be an audio stream or a video stream. It is important to know that only apps that designate the LIT function at NewPodcastApps.com support this.', 'empowercast'); ?></h3>
    <h3 class="pp-page-h3"><?php echo __('Going live is a bigger part of podcasting now. What has been developed by Podcasting 2.0 is the ability for you to show up as live in the supported apps. Giving those listeners the ability to be notified in supported podcasting apps that you are live to be able to listen or watch within the podcasting apps that support this.', 'empowercast'); ?></h3>

    <div class="pp-card-body">
        <p>
            <?php echo __('Live Item for base feed:', 'empowercast')?>
            <a href="<?php echo esc_attr($baseUrl); ?>">
                Click Here
            </a>
        </p>
        <?php if ($hasChannels) {
            $Feeds = array('podcast'=>__('Podcast', 'empowercast') );

            if(isset($General['custom_feeds']['podcast'])){
                $Feeds = $General['custom_feeds'];
            }

            if ($Feeds['podcast'] != __('Podcast', 'empowercast'))
                $Feeds['podcast'] = __('Podcast', 'empowercast');
        ?>
        <h2 class="pp-page-sub-header"><?php echo __('Podcast Channels', 'empowercast'); ?></h2>
        <table class="widefat fixed">
            <thead>
            <tr><td><?php echo __('Show Select', 'empowercast'); ?></td><td><?php echo __('Edit Link', 'empowercast'); ?></td></tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <select class="pp-settings-select" id="channel-selector">
                        <?php
                        $count = 0;
                        $defaultSlug = "podcast";
                        foreach($Feeds as $feed_slug => $feed_title) {
                            if ($count == 0) {
                                $defaultSlug = $feed_slug;
                                $count++;
                            }
                            ?>
                        <option value="feed_slug=<?php echo $feed_slug?>"><?php echo __($feed_title ? $feed_title : "Podcast", 'empowercast'); ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td><a id="channel-link" href="<?php echo esc_attr($channelDefaultURL . "feed_slug=$defaultSlug"); ?>"><?php echo __('Edit Live Settings', 'empowercast'); ?></a></td>
            </tr>
            </tbody>
        </table>
        <br />
        <?php } ?>

        <?php if ($hasCats) {
            $Feeds = array();
            if(isset($General['custom_cat_feeds'])){
                $Feeds = $General['custom_cat_feeds'];
            }

            if (count($Feeds) > 0) {
            ?>
            <h2 class="pp-page-sub-header"><?php echo __('Category Podcasts', 'empowercast'); ?></h2>
            <table class="widefat fixed">
                <thead>
                <tr><td><?php echo __('Categories', 'empowercast'); ?></td><td><?php echo __('Edit Link', 'empowercast'); ?></td></tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <select class="pp-settings-select" id="cat-selector">
                            <?php foreach($Feeds as $null => $cat_ID){
                                    if(empty($cat_ID)){
                                        continue;
                                    }
                                    $category = get_category_to_edit($cat_ID);
                                    if(is_wp_error($category)){
                                        // $cat_ID does not existing
                                        continue;
                                    }
                            ?>
                                <option value="cat=<?php echo $cat_ID?>"><?php echo __($category->slug, 'empowercast'); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><a id="cat-link" href="<?php echo esc_attr($catDefaultURL . "cat=".$Feeds[0]); ?>"><?php echo __('Edit Live Settings', 'empowercast'); ?></a></td>
                </tr>
                </tbody>
            </table>
            <br />
        <?php
            }
        } ?>

        <?php if ($hasTax) {
        $EmpowerCastTaxonomies = get_option('empowercast_taxonomy_podcasting');
        if(empty($EmpowerCastTaxonomies)){
            $EmpowerCastTaxonomies = array();
        }

        if (count($EmpowerCastTaxonomies) > 0) {
                ?>
                <h2 class="pp-page-sub-header"><?php echo __('Taxonomy Podcasts', 'empowercast'); ?></h2>
                <table class="widefat fixed">
                    <thead>
                    <tr><td><?php echo __('Term Name / Taxonomy', 'empowercast'); ?></td><td><?php echo __('Edit Link', 'empowercast'); ?></td></tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <select class="pp-settings-select" id="tax-selector">
                                <?php
                                $count = 0;
                                $defaultVals = array();
                                foreach($EmpowerCastTaxonomies as $tt_id => $null) {
                                    $taxonomy_type = '';
                                    $term_ID = '';

                                    global $wpdb;
                                    $term_info = $wpdb->get_results("SELECT term_id, taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = $tt_id",  ARRAY_A);
                                    if(!empty( $term_info[0]['term_id'])){
                                        $term_ID = $term_info[0]['term_id'];
                                        $taxonomy_type = $term_info[0]['taxonomy'];
                                    } else {
                                        continue; // we didn't find this taxonomy relationship
                                    }

                                    if ($count == 0) {
                                        $defaultVals['term_id'] = $term_ID;
                                        $defaultVals['taxonomy'] = $taxonomy_type;
                                        $defaultVals['ttid'] = $tt_id;
                                        $count += 1;
                                    }

                                    $term_object = get_term($term_ID, $taxonomy_type, OBJECT, 'edit');
                                    if(is_wp_error($term_object)){
                                        continue;
                                    }
                                    ?>
                                    <option value="term=<?php echo $term_ID?>&taxonomy=<?php echo $taxonomy_type?>&ttid=<?php echo $tt_id?>"><?php echo __($term_object->name.' / '.$taxonomy_type, 'empowercast'); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><a id="tax-link" href="<?php echo esc_attr($taxDefaultURL . "term=".$defaultVals['term_id']."&taxonomy=".$defaultVals['taxonomy']."&ttid=".$defaultVals['ttid']); ?>"><?php echo __('Edit Live Settings', 'empowercast'); ?></a></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <?php
            }
        } ?>

        <?php if ($hasPT) {
            $post_types = empowercast_admin_get_post_types(false);

            $ptCount = 0;
            $validPts = array();
            foreach($post_types as $null => $post_type) {
                $PostTypeSettingsArray = get_option('empowercast_posttype_' . $post_type);
                if (!$PostTypeSettingsArray) {
                    continue;
                }

                $validPts[] = $post_type;
                $ptCount++;
            }

            if ($ptCount > 0) {
                ?>
                <h2 class="pp-page-sub-header"><?php echo __('Post Type Podcasts', 'empowercast'); ?></h2>
                <table class="widefat fixed">
                    <thead>
                    <tr><td><?php echo __('Feed Name / Post Type', 'empowercast'); ?></td><td><?php echo __('Edit Link', 'empowercast'); ?></td></tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <select class="pp-settings-select" id="pt-selector">
                                <?php
                                $count = 0;
                                $defaultVals = array();
                                foreach($validPts as $post_type) {
                                    $PostTypeSettingsArray = get_option('empowercast_posttype_'. $post_type );

                                    foreach($PostTypeSettingsArray as $feed_slug => $PostTypeSettings) {
                                        $feed_title = ( !empty($PostTypeSettings['title']) ? $PostTypeSettings['title'] : '(blank)');

                                        if ($count == 0) {
                                            $defaultVals['feed_slug'] = $feed_slug;
                                            $defaultVals['post_type'] = $post_type;
                                            $count += 1;
                                        }
                                    ?>
                                    <option value="feed_slug=<?php echo $feed_slug; ?>&podcast_post_type=<?php echo $post_type; ?>"><?php echo __($feed_title.' / '.$post_type, 'empowercast'); ?></option>
                                <?php }
                                }
                                ?>
                            </select>
                        </td>
                        <td><a id="pt-link" href="<?php echo esc_attr($ptDefaultURL . "feed_slug=".$defaultVals['feed_slug']."&podcast_post_type=".$defaultVals['post_type']); ?>"><?php echo __('Edit Live Settings', 'empowercast'); ?></a></td>
                    </tr>
                    </tbody>
                </table>
                <?php
            }
        } ?>

    </div>
<?php } ?>
