<?php
    //Plan--put function empowercast_meta_box here.
    //In this function, set all settings then call methods from empowercastadmin-metabox.php for each tab/section
    //Functions in empowercastadmin-metabox should take the same two parameters as empowercast_meta_box
    //Plus maybe general settings and whatever other variables are initialized in empowercast_meta_box

require_once(EMPOWERCAST_ABSPATH .'/empowercast-metamarks.php');

function empowercast_admin_enqueue_scripts($hook) {
    if ( 'post-new.php' === $hook || 'post.php' === $hook) {
        if (defined('WP_DEBUG')) {
            if (WP_DEBUG) {
                wp_register_style('empowercast-episode-box', empowercast_get_root_url() . 'css/episode-box.css', array(), EMPOWERCAST_VERSION);
                wp_register_style('empowercast-grid', empowercast_get_root_url() . 'css/bootstrap-grid.css', array(), EMPOWERCAST_VERSION);
            } else {
                wp_register_style('empowercast-episode-box', empowercast_get_root_url() . 'css/episode-box.min.css', array(), EMPOWERCAST_VERSION);
                wp_register_style('empowercast-grid', empowercast_get_root_url() . 'css/bootstrap-grid.min.css', array(), EMPOWERCAST_VERSION);
            }
        } else {
            wp_register_style('empowercast-episode-box', empowercast_get_root_url() . 'css/episode-box.min.css', array(), EMPOWERCAST_VERSION);
            wp_register_style('empowercast-grid', empowercast_get_root_url() . 'css/bootstrap-grid.min.css', array(), EMPOWERCAST_VERSION);
        }
        wp_enqueue_style( 'empowercast-episode-box' );
        wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );
        wp_enqueue_style( 'empowercast-grid' );
    }
}
add_action('admin_enqueue_scripts', 'empowercast_admin_enqueue_scripts');

/**
 * Accept json files so that users can upload podcast chapters
 * @param $mime_types
 * @return mixed
 */
function empowercast_accept_json( $mime_types ) {
    $mime_types['json'] = 'text/plain'; // Adding .json extension
    return $mime_types;
}

$GeneralSettings = get_option('empowercast_general');

//Use the upload_mimes filter to accept json uploads if necessary
if (isset($GeneralSettings['empowercast_accept_json']) && $GeneralSettings['empowercast_accept_json']) {
    add_filter( 'upload_mimes', 'empowercast_accept_json', 100000, 1);
} else {
    remove_filter( 'upload_mimes', 'empowercast_accept_json', 100000);
}

function empowercast_meta_box($object, $box)
{
    $FeedSlug = esc_attr(str_replace('empowercast-', '', $box['id']));
    $DurationHH = '';
    $DurationMM = '';
    $DurationSS = '';
    $EnclosureURL = '';
    $EnclosureLength = '';
    $Embed = '';
    $CoverImage = '';
    $iTunesDuration = false;
    $iTunesKeywords = '';
    $iTunesSubtitle = '';
    $iTunesSummary = '';
    $GooglePlayDesc = '';
    $GooglePlayExplicit = '';
    $GooglePlayBlock = '';
    $iTunesAuthor = '';
    $iTunesExplicit = '';
    $iTunesCC = false;
    $iTunesOrder = false;
    $FeedAlways = false;
    $PCITranscript = false;
    $PCITranscriptURL = '';
    $PCIChapters = false;
    $PCIChaptersManual = false;
    $PCIChaptersURL = '';
    $PCISoundbites = false;
    $iTunesBlock = false;
    $NoPlayer = false;
    $NoLinks = false;
    $IsHD = false;
    $IsVideo = false;
    $Width = false;
    $Height = false;
    $FeedTitle = '';
    $PodcastCategory = '';
    $GeneralSettings = get_option('empowercast_general');
    if (!isset($GeneralSettings['set_size']))
        $GeneralSettings['set_size'] = 0;
    if (!isset($GeneralSettings['set_duration']))
        $GeneralSettings['set_duration'] = 0;
    if (!isset($GeneralSettings['episode_box_embed']))
        $GeneralSettings['episode_box_embed'] = 0;
    if ((!empty($GeneralSettings['podops_hosting']) && $GeneralSettings['podops_hosting'] === 'false') || empty($GeneralSettings['podops_hosting']))
        $GeneralSettings['podops_hosting'] = false;
    $ExtraData = array();


    if ($object->ID) {

        if ($FeedSlug == 'podcast')
            $enclosureArray = get_post_meta($object->ID, 'enclosure', true);
        else
            $enclosureArray = get_post_meta($object->ID, '_' . $FeedSlug . ':enclosure', true);

        $EnclosureURL = '';
        $EnclosureLength = '';
        $EnclosureType = '';
        $EnclosureSerialized = false;
        if ($enclosureArray) {
            // list($EnclosureURL, $EnclosureLength, $EnclosureType, $EnclosureSerialized) =  explode("\n", $enclosureArray, 4);
            $MetaParts = explode("\n", $enclosureArray, 4);
            if (count($MetaParts) > 0)
                $EnclosureURL = trim($MetaParts[0]) == 'no' ? '' : $MetaParts[0];
            if (count($MetaParts) > 1)
                $EnclosureLength = $MetaParts[1];
            if (count($MetaParts) > 2)
                $EnclosureType = $MetaParts[2];
            if (count($MetaParts) > 3)
                $EnclosureSerialized = $MetaParts[3];
        }

        $EnclosureURL = trim($EnclosureURL);
        $EnclosureLength = trim($EnclosureLength);
        $EnclosureType = trim($EnclosureType);

        if ($EnclosureSerialized) {
            $ExtraData = @unserialize($EnclosureSerialized);
            if ($ExtraData) {
                if (isset($ExtraData['duration']))
                    $iTunesDuration = $ExtraData['duration'];
                else if (isset($ExtraData['length'])) // Podcasting plugin support
                    $iTunesDuration = $ExtraData['length'];
                if (isset($ExtraData['embed']))
                    $Embed = $ExtraData['embed'];
                if (isset($ExtraData['keywords']))
                    $iTunesKeywords = $ExtraData['keywords'];
                if (isset($ExtraData['subtitle']))
                    $iTunesSubtitle = $ExtraData['subtitle'];
                if (isset($ExtraData['summary']))
                    $iTunesSummary = $ExtraData['summary'];
                if (isset($ExtraData['gp_desc']))
                    $GooglePlayDesc = $ExtraData['gp_desc'];
                if (isset($ExtraData['gp_explicit']))
                    $GooglePlayExplicit = $ExtraData['gp_explicit'];
                if (isset($ExtraData['gp_block']))
                    $GooglePlayBlock = $ExtraData['gp_block'];
                if (isset($ExtraData['author']))
                    $iTunesAuthor = $ExtraData['author'];
                if (isset($ExtraData['no_player']))
                    $NoPlayer = $ExtraData['no_player'];
                if (isset($ExtraData['no_links']))
                    $NoLinks = $ExtraData['no_links'];
                if (isset($ExtraData['explicit']))
                    $iTunesExplicit = $ExtraData['explicit'];
                if (isset($ExtraData['cc']))
                    $iTunesCC = $ExtraData['cc'];
                if (isset($ExtraData['order']))
                    $iTunesOrder = $ExtraData['order'];
                if (isset($ExtraData['always']))
                    $FeedAlways = $ExtraData['always'];
                if (isset($ExtraData['block']))
                    $iTunesBlock = $ExtraData['block'];
                if (isset($ExtraData['image']))
                    $CoverImage = $ExtraData['image'];
                if (isset($ExtraData['ishd']))
                    $IsHD = $ExtraData['ishd'];
                if (isset($ExtraData['height']))
                    $Height = $ExtraData['height'];
                if (isset($ExtraData['width']))
                    $Width = $ExtraData['width'];
                if (isset($ExtraData['feed_title']))
                    $FeedTitle = $ExtraData['feed_title'];
                if (!isset($ExtraData['itunes_image']))
                    $ExtraData['itunes_image'] = "";
                if (isset($ExtraData['pci_transcript']))
                    $PCITranscript = $ExtraData['pci_transcript'];
                if (isset($ExtraData['pci_transcript_url']))
                    $PCITranscriptURL = $ExtraData['pci_transcript_url'];
                if (isset($ExtraData['pci_chapters']))
                    $PCIChapters = $ExtraData['pci_chapters'];
                if (isset($ExtraData['pci_chapters_url']))
                    $PCIChaptersURL = $ExtraData['pci_chapters_url'];
                if (isset($ExtraData['pci_chapters_manual']))
                    $PCIChaptersManual = $ExtraData['pci_chapters_manual'];
                if (isset($ExtraData['pci_soundbites']))
                    $PCISoundbites = $ExtraData['pci_soundbites'];
            }
        }

        if( defined('EMPOWERCAST_AUTO_DETECT_ONCE') && EMPOWERCAST_AUTO_DETECT_ONCE != false )
        {
            if( $EnclosureLength )
                $GeneralSettings['set_size'] = 1; // specify
            if( $iTunesDuration )
                $GeneralSettings['set_duration'] = 1; // specify
        }

        if( $FeedSlug == 'podcast' && !$iTunesDuration ) // Get the iTunes duration the old way (very old way)
            $iTunesDuration = get_post_meta($object->ID, 'itunes:duration', true);

        if( $iTunesDuration )
        {
            $iTunesDuration = empowercast_readable_duration($iTunesDuration, true);
            list($DurationHH, $DurationMM, $DurationSS) = explode(':', $iTunesDuration);
            if( ltrim($DurationHH, '0') == 0 )
                $DurationHH = '';
            if( $DurationHH == '' && ltrim($DurationMM, '0') == 0 )
                $DurationMM = '';
            if( $DurationHH == '' && $DurationMM == '' && ltrim($DurationSS, '0') == 0 )
                $DurationSS = '';
        }

        // Check for HD Video formats
        if( preg_match('/\.(mp4|m4v|webm|ogg|ogv)$/i', $EnclosureURL ) )
        {
            $IsVideo = true;
        }

    } // if ($object->ID)
    $seo_feed_title = !empty($GeneralSettings['seo_feed_title']);

    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-metabox.php');
    global $current_screen;
    $current_screen = get_current_screen();
    if( (method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor()) ||  ( function_exists('is_gutenberg_page')) && is_gutenberg_page() ) {
        $editor = "";
    } else {
        $editor = "classic-editor";
    }

    if ($EnclosureURL) {
        $style = "display: block";
    } else {
        $style = "display: none";
    }

    echo "<div id=\"empowercast_podcast_box_$FeedSlug\" class=\"$editor\">";
    // if no enclosure url AND no other podcast metadata, this is a branch new post
    if (!$EnclosureURL && empty($ExtraData['itunes_image']) && empty($ExtraData['category']) && empty($ExtraData['episode_title']) && empty($ExtraData['feed_title']) && empty($ExtraData['summary']) && empty($ExtraData['subtitle'])) {
        echo '<input type="hidden" name="Empowercast['. $FeedSlug .'][new_podcast]" value="1" />'.PHP_EOL;
    } else {
        echo "<div>";
        echo "<input style=\"display:none\" type=\"checkbox\" name=\"Empowercast[$FeedSlug][change_podcast]\"";
        echo "id=\"empowercast_change_$FeedSlug\" value=\"1\" checked/>";
        echo "</div>";
    }
    episode_box_top($EnclosureURL, $FeedSlug, $ExtraData, $GeneralSettings, $EnclosureLength, $DurationHH, $DurationMM, $DurationSS, $PCITranscriptURL);
    echo "<div id=\"tab-container-$FeedSlug\" style=\"$style\">";
    echo "<div class=\"pp-tab\">";
    $titles = array("info" => esc_attr(__("Episode Info", "empowercast")), "artwork" => esc_attr(__("Episode Artwork", "empowercast")), "website" => esc_attr(__("Website Display", "empowercast")), "advanced" => esc_attr(__("Advanced", "empowercast")), 'chapters' => esc_attr(__("Chapter Builder", "empowercast")), 'vts' => esc_attr(__("Value Time Splits", "empowercast")));
    echo "<button class=\"tablinks active\" id=\"0$FeedSlug\" title='{$titles['info']}' onclick=\"empowercast_openTab(event, 'seo-$FeedSlug')\" id=\"defaultOpen-$FeedSlug\">" . esc_html(__('Episode Info', 'empowercast')) . "</button>";
    echo "<button class=\"tablinks\" id=\"1$FeedSlug\" title='{$titles['artwork']}' onclick=\"empowercast_openTab(event, 'artwork-$FeedSlug')\">" . esc_html(__('Episode Artwork', 'empowercast')) . "</button>";
    echo "<button class=\"tablinks\" id=\"2$FeedSlug\" title='{$titles['website']}' onclick=\"empowercast_openTab(event, 'display-$FeedSlug')\">" . esc_html(__('Website Display', 'empowercast')) . "</button>";
    echo "<button class=\"tablinks\" id=\"3$FeedSlug\" title='{$titles['advanced']}' onclick=\"empowercast_openTab(event, 'notes-$FeedSlug')\">" . esc_html(__('Advanced', 'empowercast')) . "</button>";
    echo "<button class=\"tablinks\" id=\"4$FeedSlug\" title='{$titles['chapters']}' onclick=\"empowercast_openTab(event, 'chapters-$FeedSlug')\">" . esc_html(__('Chapter Builder', 'empowercast')) . "</button>";
    echo "<button class=\"tablinks\" id=\"5$FeedSlug\" title='{$titles['vts']}' onclick=\"empowercast_openTab(event, 'vts-$FeedSlug')\">" . esc_html(__('Value Time Splits', 'empowercast')) . "</button>";
    echo "</div>";
    seo_tab($FeedSlug, $ExtraData, $iTunesExplicit, $seo_feed_title, $GeneralSettings, $iTunesSubtitle, $iTunesSummary, $iTunesAuthor, $iTunesOrder, $iTunesBlock, $object);
    artwork_tab($FeedSlug, $ExtraData, $object, $CoverImage, $GeneralSettings);
    display_tab($FeedSlug, $IsVideo, $NoPlayer, $NoLinks, $Width, $Height, $Embed, $GeneralSettings);
    notes_tab($FeedSlug, $object, $GeneralSettings, $PCITranscript, $PCITranscriptURL, $PCIChapters, $PCIChaptersURL, $PCISoundbites, $ExtraData);
    chapters_tab($EnclosureURL, $FeedSlug, $object, $GeneralSettings, $PCITranscript, $PCITranscriptURL, $PCIChapters, $PCIChaptersManual, $PCIChaptersURL, $PCISoundbites, $ExtraData);
    vts_tab($FeedSlug, $object, $GeneralSettings, $PCITranscript, $PCITranscriptURL, $PCIChapters, $PCIChaptersManual, $PCIChaptersURL, $PCISoundbites, $ExtraData);

    echo "</div>";
    echo "</div>";
    if ($EnclosureURL) {
        echo "<script type=\"text/javascript\">";
        echo "jQuery(document).ready(function($) {";
        echo "empowercast_verifyMedia({id: 'verify-button-$FeedSlug'});";
        echo "});";
        echo "</script>";
    } else {
        echo "<script type=\"text/javascript\">";
        echo "jQuery(document).ready(function($) {";
        echo "verify_interval = setInterval(function() { empowercast_verifyButtonColor('$FeedSlug'); })";
        echo "});";
        echo "</script>";
    }
    if( !empty($GeneralSettings['episode_box_background_color'][$FeedSlug]) ) {
        echo "<script type=\"text/javascript\">";
        echo "jQuery(document).ready(function($) {";
        $color = $GeneralSettings['episode_box_background_color'][$FeedSlug];
        echo "jQuery('#empowercast-$FeedSlug h2.hndle').css( {'width' : '97%' });";
        echo "jQuery('#empowercast-$FeedSlug h2.hndle').css( {'background-color' : '$color' });";
	    echo "jQuery('#empowercast-$FeedSlug h2.hndle').css( {'background-image' : '-moz-linear-gradient(center top , $color, $color' });";
        echo "jQuery('#empowercast-$FeedSlug button.handlediv').css( {'border-bottom' : '1px solid #e2e4e7' });";
        echo "jQuery('#empowercast-$FeedSlug button.handlediv').css( {'height' : '50px' });";
        echo "jQuery('#empowercast-$FeedSlug button.handlediv').css( {'background-color' : '$color' });";
        echo "jQuery('#empowercast-$FeedSlug button.handlediv').css( {'background-image' : '-moz-linear-gradient(center top , $color, $color' });";
        echo "});";
        echo "</script>";
    }
    if( !empty($GeneralSettings['skip_to_episode_settings']) ) {
        echo "<script type=\"text/javascript\">";
        echo "jQuery(document).ready(function($) {";
        echo "empowercast_skipToEpisodeSettings(\"$FeedSlug\");";
        echo "});";
        echo "</script>";
    }
} // function


