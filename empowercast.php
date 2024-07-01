<?php
/*
Plugin Name: EmpowerCast Podcast Plugin by PodOps
Plugin URI: https://thepodops.com/services/empowercast-plugin/
Description: <a href="https://thepodops.com/services/empowercast-plugin/" target="_blank">EmpowerCast Podcast</a> is the No. 1 Podcasting plugin for WordPress. Developed by PodOps; features include Simple and Advanced modes, multiple audio/video player options, subscribe to podcast tools, podcast SEO features, and more! Fully supports Apple Podcasts (previously iTunes), Google Podcasts, Spotify, and Podops Podcasting directories, as well as all podcast applications and clients.
Version: 1.0.0
Author: PodOps
Author URI: https://thepodops.com/
Requires at least: 3.6
Tested up to: 6.5
Text Domain: empowercast
Change Log:
	Please see readme.txt for detailed change log.

Contributors:
	Alimul Razi, CIO PodOps - Plugin founder, architect and lead developer
	See readme.txt for full list of contributors.

Credits:
	getID3(), License: GPL 2.0+ by James Heinrich <info [at] getid3.org> http://www.getid3.org
		Note: getid3.php analyze() function modified to prevent redundant filesize() function call.

Copyright 2008-2019 Podops (https://thepodops.com)

License: GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt)

	This project uses source that is GPL licensed.
*/


if( !function_exists('add_action') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// WP_PLUGIN_DIR (REMEMBER TO USE THIS DEFINE IF NEEDED)
define('EMPOWERCAST_VERSION', '1.0.0' );

// Translation support:
if ( !defined('EMPOWERCAST_ABSPATH') )
	define('EMPOWERCAST_ABSPATH', dirname(__FILE__) );



/////////////////////////////////////////////////////
// The following define options should be placed in your
// wp-config.php file so the setting is not disrupted when
// you upgrade the plugin.
/////////////////////////////////////////////////////

if( !defined('EMPOWERCAST_PODOPS_API_URL') )
	define('EMPOWERCAST_PODOPS_API_URL', 'http://api.thepodops.com/');

// Replace validator service with one that is more reliable here:
define('EMPOWERCAST_FEEDVALIDATOR_URL', 'https://castfeedvalidator.com/?url=');

// Display custom play image for quicktime media. Applies to on page player only.
//define('EMPOWERCAST_PLAY_IMAGE', 'http://www.thepodops.com/themes/podops/images/player/PlayerBadge150x50NoBorder.jpg');

if( !defined('EMPOWERCAST_CONTENT_ACTION_PRIORITY') )
	define('EMPOWERCAST_CONTENT_ACTION_PRIORITY', 10 );

// Added so administrators can customize what capability is needed for EmpowerCast
if( !defined('EMPOWERCAST_CAPABILITY_MANAGE_OPTIONS') )
	define('EMPOWERCAST_CAPABILITY_MANAGE_OPTIONS', 'manage_options');
if( !defined('EMPOWERCAST_CAPABILITY_EDIT_PAGES') )
	define('EMPOWERCAST_CAPABILITY_EDIT_PAGES', 'edit_pages');

// Define variables, advanced users could define these in their own wp-config.php so lets not try to re-define
if( !defined('EMPOWERCAST_LINK_SEPARATOR') )
	define('EMPOWERCAST_LINK_SEPARATOR', '|');
if( !defined('EMPOWERCAST_TEXT_SEPARATOR') )
	define('EMPOWERCAST_TEXT_SEPARATOR', ':');
if( !defined('EMPOWERCAST_PLAY_IMAGE') )
	define('EMPOWERCAST_PLAY_IMAGE', 'play_video_default.jpg');
if( !defined('PHP_EOL') )
	define('PHP_EOL', "\n"); // We need this variable defined for new lines.
if( defined('EMPOWERCAST_DEBUG') ) {
	if( !defined('PHP_EOL_WEB') ) {
		define('PHP_EOL_WEB', "\n"); // Helps with readability
	}
} else {
	if( !defined('PHP_EOL_WEB') ) {
		define('PHP_EOL_WEB', ''); // We don't necessarily need new lines for web output
	}
}

if( !defined('EMPOWERCAST_SUBSCRIBE') )
	define('EMPOWERCAST_SUBSCRIBE', true);
if(!defined('EMPOWERCAST_NEW_APPLE_CATEGORIES')) {
    define('EMPOWERCAST_NEW_APPLE_CATEGORIES', true);
}
// Set regular expression values for determining mobile devices
if( !defined('EMPOWERCAST_MOBILE_REGEX') )
	define('EMPOWERCAST_MOBILE_REGEX', 'iphone|ipod|ipad|aspen|android|blackberry|opera mini|webos|incognito|webmate|silk');

$empowercast_feed = NULL; // DO NOT CHANGE

function empowercast_content($content)
{
	global $post, $g_empowercast_excerpt_post_id;

	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return $content;

	if( empty($post->ID) || !is_object($post) )
		return $content;

	if( defined('EMPOWERCAST_DO_ENCLOSE_FIX') )
		$content = preg_replace('/\<!--.*added by EmpowerCast.*-->/im', '', $content );

	if( is_feed() )
		return $content; // We don't want to do anything to the feed

	if( function_exists('post_password_required') )
	{
		if( post_password_required($post) )
			return $content;
	}

	// EmpowerCast settings:
	$GeneralSettings = get_option('empowercast_general', array());

	// No player or links to add to content...
	if( !empty($GeneralSettings['disable_appearance']) )
		return $content;

	// check for themes/plugins where we know we need to do this...
	if( empty($GeneralSettings['player_aggressive']) )
	{
		if( !empty($GLOBALS['fb_ver']) && version_compare($GLOBALS['fb_ver'], '1.0',  '<=')	) {
			$GeneralSettings['player_aggressive'] = 1;
		}
		if( defined('JETPACK__VERSION') && version_compare(JETPACK__VERSION, '2.0',  '>=')	) {
			$GeneralSettings['player_aggressive'] = 1; // Jet pack still doesn't behave with EmpowerCast the_content
		}
		if( defined('WPSEO_VERSION') ) {
			$GeneralSettings['player_aggressive'] = 4;
		}
	}

	if( !empty($GeneralSettings['player_aggressive']) )
	{
		if( $GeneralSettings['player_aggressive'] == 4 )
		{
			$in_http_head = empowercast_in_wp_head();
			if( $in_http_head === true )
				return $content;
		}
		else if( $GeneralSettings['player_aggressive'] == 2 ) // If we do not have theme issues then lets keep this logic clean. and only display playes after the wp_head only
		{
			if( empty($GLOBALS['empowercast_wp_head_completed']) )
				return $content;
		}
		else // method 1 or 3...
		{
			if( strstr($content, '<!--empowercast_player-->') !== false )
				return $content; // The players were already added to the content

			if( $GeneralSettings['player_aggressive'] != 3 && $g_empowercast_excerpt_post_id > 0 )
				$g_empowercast_excerpt_post_id = 0; // Hack, set this to zero so it always goes past...

			if( $GeneralSettings['player_aggressive'] == 3 )
				$GeneralSettings['player_aggressive'] = 1; // remainder of the system will function as normal
		}
	}

	// Problem: If the_excerpt is used instead of the_content, both the_exerpt and the_content will be called here.
	// Important to note, get_the_excerpt will be called before the_content is called, so we add a simple little hack
	if( current_filter() == 'get_the_excerpt' )
	{
		$g_empowercast_excerpt_post_id = $post->ID;
		return $content; // We don't want to do anything to this content yet...
	}
	else if( current_filter() == 'the_content' && $g_empowercast_excerpt_post_id == $post->ID )
	{
		return $content; // We don't want to do anything to this excerpt content in this call either...
	}
	else if( class_exists('custom_post_widget') && empowercast_in_custom_post_widget() )
	{
		return $content; // Custom Post Widget compatibility
	}


	if( !isset($GeneralSettings['custom_feeds']) )
    $GeneralSettings['custom_feeds'] = array('podcast'=>'Default Podcast Feed');
	if( empty($GeneralSettings['custom_feeds']['podcast']) )
		$GeneralSettings['custom_feeds']['podcast'] = 'Default Podcast Feed';

	// Re-order so the default podcast episode is the top most...
	$Temp = $GeneralSettings['custom_feeds'];
	$GeneralSettings['custom_feeds'] = array();
	$GeneralSettings['custom_feeds']['podcast'] = 'Default Podcast Feed';
	foreach( $Temp as $feed_slug=> $feed_title )
	{
		if( $feed_slug == 'podcast' )
			continue;
		$GeneralSettings['custom_feeds'][ $feed_slug ] = $feed_title;
	}

	// Handle post type feeds....
	if( !empty($GeneralSettings['posttype_podcasting']) )
	{
		$post_type = get_query_var('post_type');
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type ); // get first element in array
		}

		// Get the feed slugs and titles for this post type
		$PostTypeSettingsArray = get_option('empowercast_posttype_'.$post_type, array());
		// Loop through this array of post type settings...
		if( !empty($PostTypeSettingsArray) )
		{
			switch($post_type)
			{
				case 'post':
				case 'page': {
					// Do nothing!, we want the default podcast to appear in these post types
				}; break;
				default: {
					if( !empty($post_type) && empty($PostTypeSettingsArray['podcast']) )
						unset($GeneralSettings['custom_feeds']['podcast']); // special case, we do not want an accidental podcast episode to appear in a custom post type if the feature is enabled
				}; break;
			}

			if (is_array($PostTypeSettingsArray)) {
                foreach ($PostTypeSettingsArray as $feed_slug => $postTypeSettings) {
                    if (!empty($postTypeSettings['title']))
                        $GeneralSettings['custom_feeds'][$feed_slug] = $postTypeSettings['title'];
                    else
                        $GeneralSettings['custom_feeds'][$feed_slug] = $feed_slug;
                }
            }
		}
	}

	if( !isset($GeneralSettings['display_player']) )
			$GeneralSettings['display_player'] = 1;
	if( !isset($GeneralSettings['player_function']) )
		$GeneralSettings['player_function'] = 1;
	if( !isset($GeneralSettings['podcast_link']) )
		$GeneralSettings['podcast_link'] = 1;

	// The blog owner doesn't want anything displayed, so don't bother wasting anymore CPU cycles
	if( $GeneralSettings['display_player'] == 0 )
		return $content;

	if( current_filter() == 'the_excerpt' && empty($GeneralSettings['display_player_excerpt']) )
		return $content; // We didn't want to modify this since the user didn't enable it for excerpts

	if( !empty($GeneralSettings['hide_player_more']) && strstr($content, 'class="more-link"') )
		return $content; // We do not want to add players and links if the read-more class found

	// Figure out which players are alerady in the body of the page...
	$ExcludePlayers = array();
	if( isset($GeneralSettings['disable_player']) )
		$ExcludePlayers = $GeneralSettings['disable_player']; // automatically disable the players configured

	if( !empty($GeneralSettings['process_podpress']) && strstr($content, '[display_podcast]') )
		return $content;

	if( preg_match_all('/(.?)\[(empowercast)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', $content, $matches) )
	{
		if( isset($matches[3]) && is_array($matches[3]) )
		{
		    foreach ($matches[3] as $key => $row) {
                $attributes = shortcode_parse_atts($row);
                if (isset($attributes['url'])) {
                    // not a problem...
                } else if (isset($attributes['feed'])) {
                    // we want to exclude this feed from the links aera...
                    $ExcludePlayers[$attributes['feed']] = true;
                } else {
                    // we don't want to include any players below...
                    $ExcludePlayers = $GeneralSettings['custom_feeds'];
                }
            }
		}
	}

	$new_content = '';
    if ( is_array($GeneralSettings['custom_feeds']) ) {
        // LOOP HERE TO DISPLAY EACH MEDIA TYPE
        foreach ($GeneralSettings['custom_feeds'] as $feed_slug => $feed_title) {
            // Get the enclosure data
            $EpisodeData = empowercast_get_enclosure_data($post->ID, $feed_slug);

            if (!$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast')
                $EpisodeData = empowercast_get_enclosure_data_podpress($post->ID);

            if (!$EpisodeData || !$EpisodeData['url'])
                continue;

            // Just in case, if there's no URL lets escape!
            if (!$EpisodeData['url'])
                continue;

            // If the player is not already inserted in the body of the post using the shortcode...
            //if( preg_match('/\[empowercast(.*)\]/is', $content) == 0 )
            if (!isset($ExcludePlayers[$feed_slug])) // If the player is not in our exclude list because it's already in the post body somewhere...
            {
                if (isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !empowercast_premium_content_authorized($feed_slug)) {
                    $new_content .= empowercast_premium_content_message($post->ID, $feed_slug, $EpisodeData);
                } else {
                    if ($GeneralSettings['player_function'] != 3 && $GeneralSettings['player_function'] != 0) // Play in new window only or disabled
                    {
                        do_action('wp_empowercast_player_scripts');
                        $AddDefaultPlayer = empty($EpisodeData['no_player']);

                        if ($EpisodeData && !empty($EpisodeData['embed'])) {
                            $new_content .= trim($EpisodeData['embed']);
                            if (!empty($GeneralSettings['embed_replace_player']))
                                $AddDefaultPlayer = false;
                        }

                        if ($AddDefaultPlayer) {
                            $image = '';
                            if (isset($EpisodeData['image']) && $EpisodeData['image'] != '')
                                $image = $EpisodeData['image'];

                            $new_content .= apply_filters('empowercast_player', '', empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData);
                        }
                    }

                    if (!isset($EpisodeData['no_links'])) {
                        do_action('wp_empowercast_player_scripts');
                        $new_content .= apply_filters('empowercast_player_links', '', empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData);
                        $new_content .= apply_filters('empowercast_player_subscribe_links', '', empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData);
                    }
                }
            }
        }
    }

	if( $new_content == '' )
		return $content;

	switch( $GeneralSettings['display_player'] )
	{
		case 1: { // Below posts
			return $content.$new_content.( !empty($GeneralSettings['player_aggressive']) && $GeneralSettings['player_aggressive'] == 1 ?'<!--empowercast_player-->':'');
		}; break;
		case 2: { // Above posts
			return ( !empty($GeneralSettings['player_aggressive']) && $GeneralSettings['player_aggressive'] == 1 ?'<!--empowercast_player-->':'').$new_content.$content;
		}; break;
	}
	return $content;
}//end function


add_filter('get_the_excerpt', 'empowercast_content', (EMPOWERCAST_CONTENT_ACTION_PRIORITY - 1) );
add_filter('the_content', 'empowercast_content', EMPOWERCAST_CONTENT_ACTION_PRIORITY);
if( !defined('EMPOWERCAST_NO_THE_EXCERPT') )
	add_filter('the_excerpt', 'empowercast_content', EMPOWERCAST_CONTENT_ACTION_PRIORITY);

/* Specail case fix Yoast bug which messes up the HTML */
function empowercast_yoast_gawp_fix($content)
{
	$content= preg_replace(
		array('/return empowercast\_pinw\(\"/', '/return empowercast\_embed\_winplayer\(\"/', '/return empowercast\_show\_embed\(\"/', '/return empowercast\_embed\_html5v\(\"/', '/return empowercast\_embed\_html5a\(\"/',  ),
		array('return empowercast_pinw(\'', 'return empowercast_embed_winplayer(\'', 'return empowercast_show_embed(\'', 'return empowercast_embed_html5v(\'', 'return empowercast_embed_html5a(\'' ),
		$content);

	return $content;
}

function empowercast_header()
{
	// EmpowerCast settings:
	$Empowercast = get_option('empowercast_general', array());
	if( !isset($Empowercast['custom_feeds']) )
    $Empowercast['custom_feeds'] = array('podcast'=>'Default Podcast Feed');

	if( empty($Empowercast['disable_appearance']) || $Empowercast['disable_appearance'] == false )
	{
		if( !isset($Empowercast['player_function']) || $Empowercast['player_function'] > 0 ) // Don't include the player in the header if it is not needed...
		{
			$EmpowercastPluginURL = empowercast_get_root_url();
?>
<script type="text/javascript"><!--
<?php
		$new_window_width = 420;
		$new_window_height = 240;

		if( isset($Empowercast['new_window_width']) && $Empowercast['new_window_width'] > 0 )
			$new_window_width = $Empowercast['new_window_width'];
		else if( isset($Empowercast['new_window_width']) )
			$new_window_width = 420;

		if( isset($Empowercast['new_window_height']) && $Empowercast['new_window_height'] > 0 )
			$new_window_height = $Empowercast['new_window_height'];
		else if( isset($Empowercast['new_window_height']) )
			$new_window_height = 240;

		if( empty($Empowercast['new_window_nofactor']) )
		{
			$new_window_width  += 40;
			$new_window_height += 80;
		}

?>
function empowercast_pinw(pinw_url){window.open(pinw_url, 'EmpowerCastPlayer','toolbar=0,status=0,resizable=1,width=<?php echo ($new_window_width); ?>,height=<?php echo ($new_window_height); ?>');	return false;}
//-->
</script>
<?php
		}
	}

	if( !empty($Empowercast['feed_links']) )
	{
		if( is_home() ) {
			$feed_slug = 'podcast';
			$href = get_feed_link($feed_slug);
			// Podcast default and channel feed settings
			$Settings = get_option('empowercast_feed_'. $feed_slug, array());

			if( empty($Settings) && $feed_slug == 'podcast' )
				$Settings = get_option('empowercast_feed', array()); // Get the main feed settings

			if( empty($Settings['title']) )
				$Settings['title'] = get_bloginfo_rss('name'); // Get blog title

			// Get the default podcast feed...
			echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( $Settings['title'] ) . '" href="' . esc_url( $href ) . '" />' . "\n";
		} else if( is_category() ) {

			$category_id = get_query_var('cat');
			if( $category_id ) {
				$Settings = get_option('empowercast_cat_feed_'.$category_id, array() );
				if( empty($Settings['title']) ) {
					$Settings['title'] = get_cat_name( $category_id ); // Get category title
					$Settings['title'] .= ' '. apply_filters( 'document_title_separator', '-' ) .' ';
					$Settings['title'] .= get_bloginfo_rss('name');
				}
				if( empty($Settings['title']) )	{
					$Settings['title'] = get_bloginfo_rss('name'); // Get blog title, best we can do
				}

				if( !empty($Settings['feed_redirect_url']) )
					$Settings['feed_url'] = $Settings['feed_redirect_url'];
				else if( !empty($General['cat_casting_podcast_feeds']) )
					$Settings['feed_url'] = get_category_feed_link($category_id, 'podcast');
				else
					$Settings['feed_url'] = get_category_feed_link( $category_id ); // Get category feed URL

				// Get the category podcast feed...
				echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( $Settings['title'] ) . '" href="' . esc_url( $Settings['feed_url'] ) . '" />' . "\n";
			}
		}
	}
}

add_action('wp_head', 'empowercast_header');

function empowercast_wp_head_completed()
{
	$GLOBALS['empowercast_wp_head_completed'] = true;
}

add_action('wp_head', 'empowercast_wp_head_completed', 100000);

function empowercast_exit_on_http_head($return)
{
	if( is_feed() )
	{
		// Set the content type for HTTP headers...
		header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

		// Needs authentication?
		$GeneralSettings = get_option('empowercast_general', array());
		if( !empty($GeneralSettings['premium_caps']) )
		{
		  $feed_slug = get_query_var('feed');
		  $FeedSettings = get_option('empowercast_feed_'.$feed_slug, array());
		  if( !empty($FeedSettings['premium']) )
		  {
			return false; // Let the logic further into EmpowerCast authenticate this HEAD request
		  }
		}
	}
	return $return;
}

add_filter('exit_on_http_head', 'empowercast_exit_on_http_head' );

function empowercast_rss2_ns()
{
	if( !empowercast_is_podcast_feed() )
		return;

	// Okay, lets add the namespace
	echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"'.PHP_EOL;

	// Add the Podcast Index namespace
    echo 'xmlns:podcast="https://podcastindex.org/namespace/1.0"'.PHP_EOL;

	if( !defined('EMPOWERCAST_RAWVOICE_RSS') || EMPOWERCAST_RAWVOICE_RSS != false )
	{
		echo 'xmlns:rawvoice="http://www.rawvoice.com/rawvoiceRssModule/"'.PHP_EOL;
	}
	if( !defined('EMPOWERCAST_GOOGLEPLAY_RSS') || EMPOWERCAST_GOOGLEPLAY_RSS != false )
	{
		echo 'xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"'.PHP_EOL;
		//echo 'xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0/play-podcasts.xsd"'.PHP_EOL;
	}
}

add_action('rss2_ns', 'empowercast_rss2_ns');
add_action('rss2_ns_empowercast', 'empowercast_rss2_ns');

function empowercast_rss2_head()
{
    // disable php notices inside feeds
    error_reporting(0);
	global $empowercast_feed;

	if( !empowercast_is_podcast_feed() )
		return; // Not a feed we manage

	$feed_slug = get_query_var( 'feed' );
	$cat_ID = get_query_var('cat');

	$Feed = get_option('empowercast_feed', array()); // Get the main feed settings
    $General = get_option('empowercast_general', array());

    $feed_url = "";
    if( !empty($empowercast_feed['category']) )
	{
		$CustomFeed = get_option('empowercast_cat_feed_'.$empowercast_feed['category'], array()); // Get the custom podcast feed settings saved in the database
		if( !empty($CustomFeed) )
			$Feed = empowercast_merge_empty_feed_settings($CustomFeed, $Feed);

        if( !empty($General['cat_casting_podcast_feeds']) )
            $feed_url = get_category_feed_link($empowercast_feed['category'], 'podcast');
        else // Use the old link
            $feed_url = get_category_feed_link($empowercast_feed['category']);
	}
	else if( !empty($empowercast_feed['term_taxonomy_id']) )
	{
		$CustomFeed = get_option('empowercast_taxonomy_'.$empowercast_feed['term_taxonomy_id'], array()); // Get the taxonomy podcast settings saved in the database
		if( !empty($CustomFeed) )
			$Feed = empowercast_merge_empty_feed_settings($CustomFeed, $Feed);

        global $wpdb;
        $term_info = $wpdb->get_results("SELECT term_id, taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = " . $empowercast_feed['term_taxonomy_id'],  ARRAY_A);
        $taxonomy_type = $term_info[0]['taxonomy'];
        $feed_url = get_term_feed_link($empowercast_feed['term_taxonomy_id'], $taxonomy_type, 'rss2');
	}
	else if( !empty($empowercast_feed['post_type']) )
	{
		$PostTypeSettingsArray = get_option('empowercast_posttype_'.$empowercast_feed['post_type'], array()); // Get the post type podcast feed settings saved in the database
		if( !empty($PostTypeSettingsArray[ $feed_slug ]) )
		{
			$CustomFeed = $PostTypeSettingsArray[ $feed_slug ];
			$Feed = empowercast_merge_empty_feed_settings($CustomFeed, $Feed, ($feed_slug == 'podcast') );
		}

        $feed_url = get_post_type_archive_feed_link($empowercast_feed['post_type'], $feed_slug);
    }
	else if( empowercast_is_custom_podcast_feed() ) // If we're handling a custom podcast feed...
	{
		$CustomFeed = get_option('empowercast_feed_'.$feed_slug, array()); // Get the custom podcast feed settings saved in the database
		$Feed = empowercast_merge_empty_feed_settings($CustomFeed, $Feed, ($feed_slug == 'podcast') );
        $feed_url = get_feed_link($feed_slug);
	}

	if( !isset($Feed['url']) || trim($Feed['url']) == '' )
	{
		if( is_category() )
			$Feed['url'] = get_category_link($cat_ID);
		else {

			$blogHomepage = get_option('page_for_posts');
			if( !empty($blogHomepage) ) {
				$Feed['url'] = get_permalink( $blogHomepage );
			}

			if( empty($Feed['url']) )
				$Feed['url'] = get_bloginfo('url');
		}
	}

	$General = get_option('empowercast_general', array());

	$feedComment = apply_filters('empowercast_feed_comment', '');
	$feedComment = trim($feedComment);
	if( !empty($feedComment) )
		echo $feedComment.' ';


	// Websub!
	if(!(defined('EMPOWERCAST_DISABLE_WEBSUB') && EMPOWERCAST_DISABLE_WEBSUB )) {
        echo "\t<atom:link rel=\"hub\" href=\"https://pubsubhubbub.appspot.com/\" />" . PHP_EOL;
    }

	// Podcast Index Locked Tag
    if (!empty($Feed['pp_enable_feed_lock'])) {
        echo "\t<podcast:locked>";
        if (!empty($Feed['unlock_podcast'])) {
            echo "no";
        } else {
            echo "yes";
        }
        echo "</podcast:locked>" . PHP_EOL;
    }

	// add the itunes:new-feed-url tag to feed
	if( empowercast_is_custom_podcast_feed() )
	{
		if( !empty($Feed['itunes_new_feed_url']) )
		{
			$Feed['itunes_new_feed_url'] = str_replace('&amp;', '&', $Feed['itunes_new_feed_url']);
			echo "\t<itunes:new-feed-url>". htmlspecialchars(trim($Feed['itunes_new_feed_url'])) .'</itunes:new-feed-url>'.PHP_EOL;
		}
	}
	else if( !empty($Feed['itunes_new_feed_url']) && ($feed_slug == 'feed' || $feed_slug == 'rss2') ) // If it is the default feed (We don't wnat to apply this to category or tag feeds
	{
		$Feed['itunes_new_feed_url'] = str_replace('&amp;', '&', $Feed['itunes_new_feed_url']);
		echo "\t<itunes:new-feed-url>". htmlspecialchars(trim($Feed['itunes_new_feed_url'])) .'</itunes:new-feed-url>'.PHP_EOL;
	}

	if( !empty($General['itunes_cdata']) ) {
		if( !empty($Feed['itunes_summary']) )
			echo "\t".'<itunes:summary><![CDATA['. empowercast_format_itunes_value( $Feed['itunes_summary'], 'summary', true ) .']]></itunes:summary>'.PHP_EOL;
		else
			echo "\t".'<itunes:summary><![CDATA['. empowercast_format_itunes_value( get_bloginfo('description'), 'summary', true ) .']]></itunes:summary>'.PHP_EOL;
	} else {
		if( !empty($Feed['itunes_summary']) )
			echo "\t".'<itunes:summary>'. empowercast_format_itunes_value( $Feed['itunes_summary'], 'summary' ) .'</itunes:summary>'.PHP_EOL;
		else
			echo "\t".'<itunes:summary>'.  empowercast_format_itunes_value( get_bloginfo('description'), 'summary' ) .'</itunes:summary>'.PHP_EOL;
	}

	if( !empty($empowercast_feed['itunes_talent_name']) )
		echo "\t<itunes:author>" . esc_html($empowercast_feed['itunes_talent_name']) . '</itunes:author>'.PHP_EOL;

	if( !empty($empowercast_feed['explicit']) && $empowercast_feed['explicit'] != 'no' )
		echo "\t".'<itunes:explicit>' . $empowercast_feed['explicit'] . '</itunes:explicit>'.PHP_EOL;

	if( !empty($Feed['itunes_block']) )
		echo "\t<itunes:block>yes</itunes:block>".PHP_EOL;

	if( !empty($Feed['itunes_complete']) )
		echo "\t<itunes:complete>yes</itunes:complete>".PHP_EOL;

	if( !empty($Feed['itunes_image']) )
	{
		echo "\t".'<itunes:image href="' . esc_html( empowercast_url_in_feed(str_replace(' ', '+', $Feed['itunes_image'])), 'double') . '" />'.PHP_EOL;
	}
	else
	{
		echo "\t".'<itunes:image href="' . empowercast_url_in_feed(empowercast_get_root_url()) . 'itunes_default.jpg" />'.PHP_EOL;
	}

	if( !empty($Feed['itunes_type']) ) {
		echo "\t".'<itunes:type>'. esc_html($Feed['itunes_type']) .'</itunes:type>'.PHP_EOL;
	}

    echo "\t".'<itunes:owner>'.PHP_EOL;
    echo "\t\t".'<itunes:name>' . esc_html($empowercast_feed['itunes_talent_name']) . '</itunes:name>'.PHP_EOL;
	if( !empty($Feed['email']) && (!isset($Feed['pp_enable_email']) || $Feed['pp_enable_email'] == 1))
	{
		echo "\t\t".'<itunes:email>' . esc_html($Feed['email']) . '</itunes:email>'.PHP_EOL;
	}
    echo "\t".'</itunes:owner>'.PHP_EOL;

	if( !empty($Feed['copyright']) )
	{
		// In case the user entered the copyright html version or the copyright UTF-8 or ASCII symbol or just (c)
		$Feed['copyright'] = str_replace(array('&copy;', '(c)', '(C)', chr(194) . chr(169), chr(169) ), '&#xA9;', $Feed['copyright']);
		echo "\t".'<copyright>'. esc_html($Feed['copyright']) . '</copyright>'.PHP_EOL;
        echo "\t".'<podcast:license>'. esc_html($Feed['copyright']) . '</podcast:license>'.PHP_EOL;
	}

    if ( !empty($Feed['txt_tag']) )
    {
        echo "\t".'<podcast:txt>'. esc_html($Feed['txt_tag']) . '</podcast:txt>'.PHP_EOL;
    }

    echo "\t".'<podcast:medium>'. esc_html($Feed['medium'] ?? 'podcast') . '</podcast:medium>'.PHP_EOL;

    if( !empty($Feed['itunes_subtitle']) )
		echo "\t".'<itunes:subtitle>' . empowercast_format_itunes_value($Feed['itunes_subtitle'], 'subtitle') . '</itunes:subtitle>'.PHP_EOL;
	else
		echo "\t".'<itunes:subtitle>'.  empowercast_format_itunes_value( get_bloginfo('description'), 'subtitle') .'</itunes:subtitle>'.PHP_EOL;
	$podcast_title_safe = '';
	if( version_compare($GLOBALS['wp_version'], 4.4, '<' ) ) {
		$podcast_title_safe .= get_bloginfo_rss('name');
	}
	$podcast_title_safe .= get_wp_title_rss();
	if( empty($General['disable_rss_image']) )
	{
		if(!empty($Feed['itunes_image']) )
		{
			$rss_image = $Feed['itunes_image'];

			echo "\t". '<image>' .PHP_EOL;
			echo "\t\t".'<title>' . $podcast_title_safe . '</title>'.PHP_EOL;
			echo "\t\t".'<url>' . esc_html( str_replace(' ', '+', $rss_image)) . '</url>'.PHP_EOL;
			echo "\t\t".'<link>'. $Feed['url'] . '</link>' . PHP_EOL;
			echo "\t".'</image>' . PHP_EOL;
		}
		else // Use the default image
		{
			echo "\t". '<image>' .PHP_EOL;
			echo "\t\t".'<title>' . $podcast_title_safe . '</title>'.PHP_EOL;
			echo "\t\t".'<url>' . empowercast_get_root_url() . 'rss_default.jpg</url>'.PHP_EOL;
			echo "\t\t".'<link>'. $Feed['url'] . '</link>' . PHP_EOL;
			echo "\t".'</image>' . PHP_EOL;
		}
	}

	// Handle iTunes categories
	$Cat1 = false; $Cat2 = false; $Cat3 = false; $SubCat1 = false; $SubCat2 = false; $SubCat3 = false;
	if(defined('EMPOWERCAST_NEW_APPLE_CATEGORIES') && EMPOWERCAST_NEW_APPLE_CATEGORIES == true) {
	    $Categories = empowercast_apple_categories();
        for ($i = 1; $i <= 3; $i++) {
            if(!empty($Feed['itunes_cat_'.$i]) && empty($Feed['apple_cat_'.$i])) {
                $mappings = array('01-00' => '01-00', '01-01' => '01-02', '01-02' => '01-03', '01-03' => '01-04', '01-04' => '01-01',
                    '01-05' => '01-05', '01-06' => '01-06', '02-00' => '02-00', '02-01' => '12-01', '02-02' => '02-01', '02-03' => '02-03',
                    '02-04' => '02-00', '02-05' => '02-00', '03-00' => '03-00', '04-00' => '04-00', '04-01' => '04-00', '04-02' => '04-00',
                    '04-03' => '09-01', '04-04' => '04-03', '04-05' => '04-00', '05-00' => '10-00', '05-01' => '10-02', '05-02' => '10-03',
                    '05-03' => '10-06', '05-04' => '10-05', '05-05' => '10-05', '06-00' => '06-00', '06-01' => '06-00', '06-02' => '06-00',
                    '06-03' => '06-00', '06-04' => '06-00', '07-00' => '07-00', '07-01' => '07-01', '07-02' => '07-00', '07-03' => '04-04',
                    '07-04' => '07-06', '08-00' => '09-00', '09-00' => '11-00', '11-00' => '13-00', '11-01' => '13-01', '11-02' => '13-02',
                    '11-03' => '13-03', '11-04' => '13-04', '11-05' => '13-05', '11-06' => '13-06', '11-07' => '13-07', '12-00' => '14-00',
                    '12-01' => '07-03', '12-02' => '14-06', '12-03' => '14-09', '13-00' => '15-00', '13-01' => '08-00', '13-02' => '15-02',
                    '13-03' => '15-03', '13-04' => '15-04', '14-00' => '16-00', '14-01' => '16-00', '14-02' => '16-00', '14-03' => '16-15',
                    '14-04' => '16-00', '15-00' => '17-00', '15-01' => '17-00', '15-02' => '12-07', '15-03' => '17-00', '15-04' => '17-00', '16-00' => '19-00');
                $Feed['apple_cat_'. $i] = $mappings[$Feed['itunes_cat_'.$i]];

            }
        }
        if (!empty($Feed['apple_cat_1']))
            list($Cat1, $SubCat1) = explode('-', $Feed['apple_cat_1']);
        if (!empty($Feed['apple_cat_2']))
            list($Cat2, $SubCat2) = explode('-', $Feed['apple_cat_2']);
        if (!empty($Feed['apple_cat_3']))
            list($Cat3, $SubCat3) = explode('-', $Feed['apple_cat_3']);
        $googleplay_category_mapping = array(
            '01-00'   => '01-00',
            '02-00'   => '02-00',
            '03-00'   => '03-00',
            '04-00'   => '04-00',
            '05-00'   => '13-00',
            '06-00'   => '06-00',
            '07-00'   => '07-00',
            '08-00'   => '13-00',
            '09-00'   => '08-00',
            '10-00'   => '05-00',
            '11-00'   => '09-00',
            '12-00'   => '10-00',
            '13-00'   => '11-00',
            '14-00'   => '12-00',
            '15-00'   => '13-00',
            '16-00'   => '14-00',
            '17-00'   => '15-00',
            '18-00'   => '13-00',
            '19-00'   => '16-00',
        );
    }
	else {
        $Categories = empowercast_itunes_categories();
        if (!empty($Feed['itunes_cat_1']))
            list($Cat1, $SubCat1) = explode('-', $Feed['itunes_cat_1']);
        if (!empty($Feed['itunes_cat_2']))
            list($Cat2, $SubCat2) = explode('-', $Feed['itunes_cat_2']);
        if (!empty($Feed['itunes_cat_3']))
            list($Cat3, $SubCat3) = explode('-', $Feed['itunes_cat_3']);
        $googleplay_category_mapping = array(
            '01-00'   => '01-00',
            '02-00'   => '02-00',
            '03-00'   => '03-00',
            '04-00'   => '04-00',
            '05-00'   => '05-00',
            '06-00'   => '06-00',
            '07-00'   => '07-00',
            '08-00'   => '08-00',
            '09-00'   => '09-00',
            '10-00'   => '10-00',
            '11-00'   => '11-00',
            '12-00'   => '12-00',
            '13-00'   => '13-00',
            '14-00'   => '14-00',
            '15-00'   => '15-00',
            '16-00'   => '16-00',
        );
    }

    $googleplay_categories = empowercast_googleplay_categories();

	if( $Cat1 )
	{
		$CatDesc = $Categories[$Cat1.'-00'];
		$SubCatDesc = $Categories[$Cat1.'-'.$SubCat1];
		echo "\t".'<itunes:category text="'. esc_attr($CatDesc);
		if( $SubCat1 != '00' ) {
            echo '">' . PHP_EOL . "\t\t" . '<itunes:category text="' . esc_attr($SubCatDesc) . '" />' . PHP_EOL;
            // End this category set
            echo "\t".'</itunes:category>'.PHP_EOL;
        } else {
            echo '" />'.PHP_EOL;
        }


        //Get the googleplay category and put it in the feed
        $gplay_category = $googleplay_categories[$googleplay_category_mapping[$Cat1.'-00']];
        echo "\t",'<googleplay:category text="'. esc_attr($gplay_category). '"/>'.PHP_EOL;
	}

	if( $Cat2 )
	{
		$CatDesc = $Categories[$Cat2.'-00'];
		$SubCatDesc = $Categories[$Cat2.'-'.$SubCat2];

        echo "\t".'<itunes:category text="'. esc_attr($CatDesc);
        if( $SubCat2 != '00' ) {
            echo '">' . PHP_EOL . "\t\t" . '<itunes:category text="' . esc_attr($SubCatDesc) . '" />' . PHP_EOL;
            // End this category set
            echo "\t".'</itunes:category>'.PHP_EOL;
        } else {
            echo '" />'.PHP_EOL;
        }
    }

	if( $Cat3 )
	{
		$CatDesc = $Categories[$Cat3.'-00'];
		$SubCatDesc = $Categories[$Cat3.'-'.$SubCat3];

        echo "\t".'<itunes:category text="'. esc_attr($CatDesc);
        if( $SubCat3 != '00' ) {
            echo '">' . PHP_EOL . "\t\t" . '<itunes:category text="' . esc_attr($SubCatDesc) . '" />' . PHP_EOL;
            // End this category set
            echo "\t".'</itunes:category>'.PHP_EOL;
        } else {
            echo '" />'.PHP_EOL;
        }
	}
	// End Handle iTunes categories

	// RawVoice RSS Tags
	if( !defined('EMPOWERCAST_RAWVOICE_RSS') || EMPOWERCAST_RAWVOICE_RSS != false )
	{
		if( !empty($Feed['parental_rating']) )
			echo "\t<rawvoice:rating>". $Feed['parental_rating'] ."</rawvoice:rating>".PHP_EOL;
		if( !empty($Feed['location']) ) {
            echo "\t<rawvoice:location>" . htmlspecialchars($Feed['location']) . "</rawvoice:location>" . PHP_EOL;
            echo "\t<podcast:location";
            if( !empty($Feed['pci_geo']) ) {
                echo " geo=\"" . htmlspecialchars($Feed['pci_geo']) . "\"";
            }
            if( !empty($Feed['pci_osm']) ) {
                echo " osm=\"" . htmlspecialchars($Feed['pci_osm']) . "\"";
            }
            echo ">" . htmlspecialchars($Feed['location']) . "</podcast:location>" . PHP_EOL;
        }

        if (isset($Feed['update_frequency'])) {
            $rrule = 'FREQ=' . ($Feed['update_frequency'] == '1' ? 'DAILY' : ($Feed['update_frequency'] == '2' ? 'WEEKLY' : 'MONTHLY'));
            $updateFrequency = 'Daily';

            if ($Feed['update_frequency'] == 2) {
                $days = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];
                $selectedDays = explode(',', $Feed['update_frequency_week']);
                $byDays = [];
                foreach ($selectedDays as $day) {
                    $byDays[] = $days[(int) $day];
                }

                if (!empty($byDays))
                    $rrule .= ';BYDAY='.implode(',', $byDays);

                $updateFrequency = 'Weekly';

                if (count($selectedDays) == 2)
                    $updateFrequency = 'Biweekly';
            } else if ($Feed['update_frequency'] == 3) {
                $rrule .= ';INTERVAL=' . $Feed['update_frequency_month'];
                $updateFrequency = 'Monthly';

                if ($Feed['update_frequency_month'] == 2)
                    $updateFrequency = 'Bimonthly';
            }

            echo "\t<podcast:updateFrequency rrule=\"$rrule\">$updateFrequency</podcast:updateFrequency>\n";
        }

        if (isset($Feed['block'])) {
            if (isset($Feed['block_all']) && $Feed['block_all'] != 0) {
                echo "\t<podcast:block>yes</podcast:block>\n";
                echo "\t<itunes:block>yes</itunes:block>\n";
                echo "\t<googleplay:block>yes</googleplay:block>\n";
            } else {
                // Block individuals
                $blockList = explode(';', $Feed['block_list']);

                foreach ($blockList as $block) {
                    if ($block != '') {
                        echo "\t<podcast:block id=\"$block\">yes</podcast:block>\n";
                        if ($block == 'apple')
                            echo "\t<itunes:block>yes</itunes:block>\n";
                        if ($block == 'google')
                            echo "\t<googleplay:block>yes</googleplay:block>\n";
                    }
                }
            }
        }

        if (isset($Feed['remote_items']) && !empty($Feed['remote_items'])) {
            $existingRemoteItems = $Feed['remote_items'];
            $existingPodrollItems = [];
            $existingFeedItems = [];

            foreach ($existingRemoteItems as $remoteItem) {
                if ($remoteItem['podroll'] == 1)
                    $existingPodrollItems[] = $remoteItem;
                else
                    $existingFeedItems[] = $remoteItem;
            }

            if (!empty($existingPodrollItems)) {
                echo "\t<podcast:podroll>\n";

                foreach ($existingPodrollItems as $remoteItem) {
                    $feedGuid = $remoteItem['feed_guid'];
                    echo "\t\t<podcast:remoteItem feedGuid=\"$feedGuid\" />\n";
                }
                echo "\t</podcast:podroll>\n";
            }

            foreach ($existingFeedItems as $remoteItem) {
                $feedGuid = $remoteItem['feed_guid'];
                $attrStr = "feedGuid=\"$feedGuid\"";

                if ($remoteItem['item_guid'] != '') {
                    $itemGuid = $remoteItem['item_guid'];
                    $attrStr .= " itemGuid=\"$itemGuid\"";
                }

                echo "\t<podcast:remoteItem $attrStr />\n";
            }
        }

        if (!empty($Feed['value_pubkey']) && !empty($Feed['value_split'])) {
            echo "\t".'<podcast:value type="lightning" method="keysend" suggested="0.00000005000">'."\n";
            for ($i=0; $i < count($Feed['value_pubkey']); $i++) {
                if ($Feed['value_pubkey'][$i] == "")
                    continue;

                $attrStr = 'type="node" split="'.$Feed['value_split'][$i].'" address="'.$Feed['value_pubkey'][$i].'"';

                if ($Feed['value_lightning'][$i] != "")
                    $attrStr .= ' name="'.$Feed['value_lightning'][$i].'"';

                if ($Feed['value_custom_key'][$i] != "")
                    $attrStr .= ' customKey="'.$Feed['value_custom_key'][$i].'"';

                if ($Feed['value_custom_value'][$i] != "")
                    $attrStr .= ' customValue="'.$Feed['value_custom_value'][$i].'"';

                echo "\t\t"."<podcast:valueRecipient $attrStr/>\n";
            }

            $podopsAttrs = array(
                "name" =>  "podops@getalby.com",
                "type" => "node",
                "split" => 3,
                "address" => "030a58b8653d32b99200a2334cfe913e51dc7d155aa0116c176657a4f1722677a3",
                "customKey" => "696969",
                "customValue" => "qAHJuqKLmMhTNFualcIj",
                "fee" => "true"
            );

            $podopsAttrStr = "";
            foreach ($podopsAttrs as $key => $value) {
                $podopsAttrStr .= ' '.$key.'="'.$value.'"';
            }
            echo "\t\t"."<podcast:valueRecipient $podopsAttrStr/>\n";

            echo "\t".'</podcast:value>'."\n";
        }
		if( !empty($Feed['frequency']) )
			echo "\t<rawvoice:frequency>". htmlspecialchars($Feed['frequency']) ."</rawvoice:frequency>".PHP_EOL;
		if( !empty($Feed['donate_link']) && !empty($Feed['donate_url']) ) {
            echo "\t<rawvoice:donate href=\"" . htmlspecialchars($Feed['donate_url']) . "\">" . htmlspecialchars((empty($Feed['donate_label']) ? '' : $Feed['donate_label'])) . "</rawvoice:donate>" . PHP_EOL;
            echo "\t<podcast:funding url=\"" . htmlspecialchars($Feed['donate_url']) . "\">" . htmlspecialchars((empty($Feed['donate_label']) ? '' : $Feed['donate_label'])) . "</podcast:funding>" . PHP_EOL;
        }
        if ( !empty($Feed['person_names']) ) {
            $personNames = $Feed['person_names'];
            $personRoles = $Feed['person_roles'];
            $personURLs = $Feed['person_urls'];
            $linkURLs = $Feed['link_urls'];

            for ($i=0; $i < count($personNames); $i++) {
                $name = $personNames[$i];
                if ($name == "")
                    continue;

                $attrStr = '';
                if ($personRoles[$i] != "")
                    $attrStr = ' role="' . $personRoles[$i] . '"';

                if ($personURLs[$i] != "")
                    $attrStr .= ' img="' . htmlspecialchars($personURLs[$i]) . '"';

                if ($linkURLs[$i] != "")
                    $attrStr .= ' href="' . htmlspecialchars($linkURLs[$i]) . '"';

                echo "\t<podcast:person$attrStr>$name</podcast:person>" . PHP_EOL;
            }
        }

        echo "\t<podcast:podping usesPodping=\"true\" />" . PHP_EOL;

        require_once('uuid5.class.php');
        // This will be the same every time, but moved this logic into here to expedite removal of duplicate GUIDs
        $guidFeedURL = str_replace("http://", "", str_replace("https://", "", $feed_url));
        $guidFeedURL = rtrim($guidFeedURL,"/");
        $guid = UUID::v5('ead4c236-bf58-58c6-a2c6-a6b28d128cb6', $guidFeedURL);
        if (UUID::is_valid($guid)) {
            $Feed['podcast_guid'] = $guid;
        }
        if (!empty($Feed['guid_override_check']) && !empty($Feed['guid_override']))
            echo "\t<podcast:guid>".$Feed['guid_override']."</podcast:guid>" . PHP_EOL;
        elseif (isset($Feed['podcast_guid']) && UUID::is_valid($Feed['podcast_guid']) && $guidFeedURL != '')
            echo "\t<podcast:guid>".$Feed['podcast_guid']."</podcast:guid>" . PHP_EOL;

        if (isset($Feed['live_item']) && $Feed['live_item']['enabled'] == '1' && UUID::is_valid($Feed['live_item']['guid'])) {
            $liveItem = $Feed['live_item'];
            $tzName = timezone_name_from_abbr($liveItem['timezone']);

            $status = strtolower($liveItem['status']);
            $startArr = explode('T', $liveItem['start_date_time']);
            $startDate = new DateTime($startArr[0] . ' ' . $startArr[1], new DateTimeZone($tzName));
            $start = $startDate->format('c');

            $endArr = explode('T', $liveItem['end_date_time']);
            $endDate = new DateTime($endArr[0] . ' ' . $endArr[1], new DateTimeZone($tzName));
            $end = $endDate->format('c');
            echo "\t<podcast:liveItem status=\"$status\" start=\"$start\" end=\"$end\">" . PHP_EOL;
            echo "\t\t<title>".$liveItem['title']."</title>" . PHP_EOL;
            echo "\t\t<guid isPermalink=\"false\">".$liveItem['guid']."</guid>" . PHP_EOL;

            if ($liveItem['description'] != "")
                echo "\t\t<description>".$liveItem['description']."</description>" . PHP_EOL;

            if ($liveItem['coverart_link'] != "")
                echo "\t\t<podcast:images srcset=\"".$liveItem['cover_art']." 1400w\" />" . PHP_EOL;


            $EnclosureAttr = 'url="'.$liveItem['stream_link'].'" ';
            $EnclosureAttr .= 'length="5242880" ';
            $EnclosureAttr .= 'type="'.$liveItem['stream_type'].'"';

            echo "\t\t<enclosure $EnclosureAttr />" . PHP_EOL;
            echo "\t\t<podcast:alternateEnclosure type=\"".$liveItem['stream_type']."\" length=\"5242880\">" . PHP_EOL;
            echo "\t\t\t<podcast:source uri=\"".$liveItem['stream_link']."\" />" . PHP_EOL;
            echo "\t\t</podcast:alternateEnclosure>" . PHP_EOL;


            if ($liveItem['episode_link'] != "")
                echo "\t\t<link>".$liveItem['episode_link']."</link>" . PHP_EOL;

            echo "\t\t<podcast:contentLink href=\"".$liveItem['fallback_link']."\">Listen Live!</podcast:contentLink>" . PHP_EOL;
            echo "\t\t<podcast:timezone>".$liveItem['timezone']."</podcast:timezone>" . PHP_EOL;
            echo "\t</podcast:liveItem>" . PHP_EOL;
        }

		if( !empty($Feed['itunes_url']) || !empty($Feed['podops_url']) || !empty($Feed['tunein_url']) || !empty($Feed['spotify_url']) ) {
			echo "\t<rawvoice:subscribe feed=\"";
			self_link();
			echo '"';

			// Subscribe page // empty($FeedSettings['subscribe_page_link_href']) && empty($FeedSettings['subscribe_page_link_id'])
			if( !empty($Feed['subscribe_page_link_id']) ) {
				$link = get_page_link($Feed['subscribe_page_link_id']);
				if( !empty($link) ) {
					echo " html=\"". htmlspecialchars( $link ) .'"';
				}
			} else if( !empty($Feed['subscribe_page_link_href']) ) {
				echo " html=\"". htmlspecialchars( $Feed['subscribe_page_link_href'] ) .'"';
			}

			if( !empty($Feed['itunes_url']) )
				echo " itunes=\"". htmlspecialchars( $Feed['itunes_url'] ) .'"';
			if( !empty($Feed['podops_url']) )
				echo " podops=\"". htmlspecialchars( $Feed['podops_url'] ) .'"';
			if( !empty($Feed['tunein_url']) )
				echo " tunein=\"". htmlspecialchars( $Feed['tunein_url'] ) .'"';
			if( !empty($Feed['spotify_url']) )
				echo " spotify=\"". htmlspecialchars( $Feed['spotify_url'] ) .'"';
            if( !empty($Feed['amazon_url']) )
                echo " amazon_music=\"". htmlspecialchars( $Feed['amazon_url'] ) .'"';
            if( !empty($Feed['pcindex_url']) )
                echo " pcindex=\"". htmlspecialchars( $Feed['pcindex_url'] ) .'"';
            if( !empty($Feed['iheart_url']) )
                echo " iheart=\"". htmlspecialchars( $Feed['iheart_url'] ) .'"';
            if( !empty($Feed['pandora_url']) )
                echo " pandora=\"". htmlspecialchars( $Feed['pandora_url'] ) .'"';
            if( !empty($Feed['deezer_url']) )
                echo " deezer=\"". htmlspecialchars( $Feed['deezer_url'] ) .'"';
            if( !empty($Feed['jiosaavn_url']) )
                echo " jiosaavn=\"". htmlspecialchars( $Feed['jiosaavn_url'] ) .'"';
            if( !empty($Feed['podchaser_url']) )
                echo " podchaser=\"". htmlspecialchars( $Feed['podchaser_url'] ) .'"';
            if( !empty($Feed['gaana_url']) )
                echo " gaana=\"". htmlspecialchars( $Feed['gaana_url'] ) .'"';
            if( !empty($Feed['anghami_url']) )
                echo " anghami=\"". htmlspecialchars( $Feed['anghami_url'] ) .'"';
            if( !empty($Feed['youtube_url']) )
                echo " youtube=\"". htmlspecialchars( $Feed['youtube_url'] ) .'"';
			echo "></rawvoice:subscribe>".PHP_EOL;
		}
	}
}

add_action('rss2_head', 'empowercast_rss2_head');
add_action('rss2_head_empowercast', 'empowercast_rss2_head');

function empowercast_rss2_item()
{
	global $post, $empowercast_feed;

	// disable php notices inside feeds
    error_reporting(0);

	// are we processing a feed that empowercast should handle
	if( !empowercast_is_podcast_feed() )
		return;

	if( function_exists('post_password_required') )
	{
		if( post_password_required($post) )
			return;
	}

	// Check and see if we're working with a podcast episode
	$custom_enclosure = false;
	if( empowercast_is_custom_podcast_feed() && get_query_var('feed') !== 'podcast' && !is_category() && !is_tax() && !is_tag() )
	{
		$EpisodeData = empowercast_get_enclosure_data($post->ID, get_query_var('feed') );
		$custom_enclosure = true;
	}
	else
	{
		$EpisodeData = empowercast_get_enclosure_data($post->ID, 'podcast');
		if( !$EpisodeData && !empty($empowercast_feed['process_podpress']) )
		{
			$EpisodeData = empowercast_get_enclosure_data_podpress($post->ID);
			$custom_enclosure = true;
		}
	}

	// No episode data to include
	if( empty($EpisodeData) || empty($EpisodeData['url']) || $EpisodeData['url'] == 'no' )
		return;

	// If enclosure not added, check to see why...
	if( defined('EMPOWERCAST_ENCLOSURE_FIX') && EMPOWERCAST_ENCLOSURE_FIX && !$custom_enclosure && $GLOBALS['empowercast_rss_enclosure_post_id'] != $post->ID )
	{
		$enclosure_in_wp = apply_filters('rss_enclosure', '<enclosure url="' . trim(htmlspecialchars($EpisodeData['url']) . '" length="' . $EpisodeData['size'] . '" type="' . $EpisodeData['type'] . '" />' . "\n") );
		if( !$enclosure_in_wp )
			$custom_enclosure = true;
	}

	// Lets print the enclosure tag
	if( $custom_enclosure ) // We need to add the enclosure tag here...
	{
		if( empty($EpisodeData['size']) )
			$EpisodeData['size'] = 5242880; // Use the dummy 5MB size since we don't have a size to quote

        // encode htmlspecialchars if necessary
        $decoded = htmlspecialchars_decode($EpisodeData['url']);
        if (strlen($decoded) != strlen($EpisodeData['url'])) {
            // already encoded
            $media_url = $EpisodeData['url'];
        } else {
            // might need encoded/no risk of double encoding
            $media_url = htmlspecialchars($EpisodeData['url']);
        }

		echo "\t". sprintf('<enclosure url="%s" length="%d" type="%s" />%s',
			empowercast_url_in_feed(trim($media_url)),
			trim($EpisodeData['size']),
			trim($EpisodeData['type']),
			PHP_EOL);
	}

    if (!empty($EpisodeData['alternate_enclosure'])) {
        foreach ($EpisodeData['alternate_enclosure'] as $alternate_enclosure) {
            $size_string = "";
            $type_string = "";
            if (!empty($alternate_enclosure['size'])) {
                $size_string = 'length="' . trim($alternate_enclosure['size']) . '"';
            }

            $youtube_regexp = "/^https?:\/\/(?:www\.)?(?:youtube.com|youtu.be)\/(?:watch\?(?=.*v=([\w\-]+))(?:\S+)?|([\w\-]+))$/i";

            if (preg_match($youtube_regexp, $alternate_enclosure['url']))
                $alternate_enclosure['type'] = 'video/youtube';

            if (!empty($alternate_enclosure['type'])) {
                $type_string = 'type="' . trim($alternate_enclosure['type']) . '"';
            }
            echo "\t" . sprintf('<podcast:alternateEnclosure %s %s>%s',
                $size_string,
                $type_string,
                PHP_EOL);
            echo "\t\t" . sprintf('<podcast:source uri="%s" />%s',
                empowercast_url_in_feed(trim($alternate_enclosure['url'])),
                PHP_EOL);
            echo "\t" . sprintf('</podcast:alternateEnclosure>%s', PHP_EOL);
        }
    }

	$author = $empowercast_feed['itunes_talent_name'];
	if( isset($empowercast_feed['itunes_author_post']) )
		$author = get_the_author();

	$explicit = $empowercast_feed['explicit'];
	$summary = '';
	$subtitle = '';
	$block = false;
	$cc = false;

    if( isset( $EpisodeData['summary'] )  && strlen($EpisodeData['summary']) > 1 )
		$summary = $EpisodeData['summary'];
	if( isset( $EpisodeData['subtitle'] )  && strlen($EpisodeData['subtitle']) > 1 )
		$subtitle = $EpisodeData['subtitle'];
	if( isset( $EpisodeData['explicit'] ) && is_numeric($EpisodeData['explicit']) )
	{
        // switching from 'not set' 'yes' 'clean' to 'true' 'false'--for backwards compatibility, 'not set' will now be 'false'
        $explicit_array = array("false", "true", "false");
		$explicit = $explicit_array[$EpisodeData['explicit']];
	}

	if( !empty( $EpisodeData['author'] ) )
		$author = $EpisodeData['author'];
	if( !empty( $EpisodeData['block'] ) )
		$block = 'yes';
	if( !empty( $EpisodeData['cc'] ) )
		$cc = 'yes';

	$General = get_option('empowercast_general', array());
	$summary_cdata = ( !empty( $General['itunes_cdata'] ) ? true : false );

	if ( empty ($General['suppress_unused_item_tags']) || !$General['suppress_unused_item_tags']) {
        if (empty($subtitle)) {
            $subtitle = empowercast_get_the_exerpt(false, !empty($General['feed_action_hook']));
        }

        // If no summary specified and we have enhanced summary enabled...
        if (empty($summary) && !empty($empowercast_feed['enhance_itunes_summary'])) {
            $summary = empowercast_enhanced_itunes_summary(!empty($General['feed_action_hook']));
            if (!empty($summary)) {
                $summary_cdata = true; // Always use CDATA for enhanced iTunes summary
            }
        }

        if (empty($summary)) { // Backwards compatibility with PodPress, the excerpt is used as the itunes summary if set
            $summary = empowercast_get_the_exerpt(true, !empty($General['feed_action_hook'])); // Will call empowercast_get_the_content(true) if the excerpt does not exist
        }

        if (!empty($subtitle)) {
            echo "\t<itunes:subtitle>" . empowercast_format_itunes_value($subtitle, 'subtitle') . '</itunes:subtitle>' . PHP_EOL;
        }

        if (!empty($summary)) {
            if ($summary_cdata) {
                echo "\t\t<itunes:summary><![CDATA[" . empowercast_format_itunes_value($summary, 'summary', true) . ']]></itunes:summary>' . PHP_EOL;
            } else {
                echo "\t\t<itunes:summary>" . empowercast_format_itunes_value($summary, 'summary') . '</itunes:summary>' . PHP_EOL;
            }
        }

        if( !empty($author) ) {
            echo "\t\t<itunes:author>" . esc_html($author) . '</itunes:author>'.PHP_EOL;
        }
    }

    // itunes episode image
	if( !empty( $EpisodeData['itunes_image']) ) {
		echo "\t\t".'<itunes:image href="' . esc_attr( empowercast_url_in_feed(str_replace(' ', '+', $EpisodeData['itunes_image'])), 'double') . '" />'.PHP_EOL;
	} else if( !empty($empowercast_feed['itunes_image']) ) {
		echo "\t\t".'<itunes:image href="' . esc_attr( empowercast_url_in_feed(str_replace(' ', '+', $empowercast_feed['itunes_image'])), 'double') . '" />'.PHP_EOL;
	}

	if( !empty($EpisodeData['season']) ) {
		echo "\t\t".'<itunes:season>'. esc_html($EpisodeData['season']) .'</itunes:season>'.PHP_EOL;
        echo "\t\t".'<podcast:season>'. esc_html($EpisodeData['season']) .'</podcast:season>'.PHP_EOL;
    }

	if( !empty($EpisodeData['episode_no']) ) {
		echo "\t\t".'<itunes:episode>'. esc_html(floor($EpisodeData['episode_no'])) .'</itunes:episode>'.PHP_EOL;
        echo "\t\t".'<podcast:episode>'. esc_html(floor($EpisodeData['episode_no'])) .'</podcast:episode>'.PHP_EOL;
    }

	if( !empty($EpisodeData['episode_title']) ) {
		echo "\t\t".'<itunes:title>'. esc_html($EpisodeData['episode_title']) .'</itunes:title>'.PHP_EOL;
	}

	if(empty($EpisodeData['episode_type'])) {
        $EpisodeData['episode_type'] = 'full';
	}
    echo "\t\t".'<itunes:episodeType>'. esc_html($EpisodeData['episode_type']) .'</itunes:episodeType>'.PHP_EOL;

    if( !empty($explicit) && $explicit != 'no' ) {
		echo "\t\t<itunes:explicit>" . $explicit . '</itunes:explicit>'.PHP_EOL;
	}

	if( !empty($EpisodeData['duration']) && preg_match('/^(\d{1,2}:){0,2}\d{1,2}$/i', ltrim($EpisodeData['duration'], '0:') ) ) { // Include duration if it is valid
		echo "\t\t<itunes:duration>" . ltrim($EpisodeData['duration'], '0:') . '</itunes:duration>'.PHP_EOL;
	}

	if( $block && $block == 'yes' ) {
		echo "\t\t<itunes:block>yes</itunes:block>".PHP_EOL;
	}

    if ( empty ($General['suppress_unused_item_tags']) || !$General['suppress_unused_item_tags']) {
        if ($cc && $cc == 'yes') {
            echo "\t\t<itunes:isClosedCaptioned>yes</itunes:isClosedCaptioned>" . PHP_EOL;
        }
    }

	if( !empty($empowercast_feed['itunes_feature']) ) { // We are using the itunes:order option to feature a specific episode.
		// Skip inserting the order tag
	} else {
		if( isset( $EpisodeData['order'] ) && is_numeric( $EpisodeData['order'] ) )
			echo "\t\t<itunes:order>". $EpisodeData['order'] ."</itunes:order>".PHP_EOL;
	}

	// Podcast index tags:
    if (!empty($EpisodeData['pci_transcript']) && !empty($EpisodeData['pci_transcript_url'])) {
        echo "\t\t<podcast:transcript url=\"" . $EpisodeData['pci_transcript_url'] . "\"";
        $transcript_type = empowercast_get_contenttype($EpisodeData['pci_transcript_url']);
        if (!empty($EpisodeData['pci_transcript_language'])) {
            echo " language=\"" . $EpisodeData['pci_transcript_language'] . "\"";
        }
        if (!empty($transcript_type)) {
            echo " type=\"" . $transcript_type . "\" rel=\"captions\" />".PHP_EOL;
        } else {
            echo " type=\"text/plain\" rel=\"captions\" />".PHP_EOL;
        }
    }
    if (!empty($EpisodeData['pci_chapters']) && !empty($EpisodeData['pci_chapters_url'])) {
        echo "\t\t<podcast:chapters url=\"" . $EpisodeData['pci_chapters_url'] . "\" type=\"application/json+chapters\" />".PHP_EOL;
    }
    if (!empty($EpisodeData['social_interact_uri'])) {
        if (!empty($EpisodeData['social_interact_accountid']))
            echo "\t\t<podcast:socialInteract uri=\"" . esc_attr($EpisodeData['social_interact_uri']) . "\" protocol=\"".$EpisodeData['social_interact_protocol']."\" accountId=\"".esc_attr($EpisodeData['social_interact_accountid'])."\" />".PHP_EOL;
        else
            echo "\t\t<podcast:socialInteract uri=\"" . esc_attr($EpisodeData['social_interact_uri']) . "\" protocol=\"".$EpisodeData['social_interact_protocol']."\" />".PHP_EOL;
    }
    if( isset( $EpisodeData['copyright'] ) && strlen($EpisodeData['copyright']) > 1 )
        echo "\t\t".'<podcast:license>'. esc_html($EpisodeData['copyright']) . '</podcast:license>'.PHP_EOL;

	// Google Play tags:
	if( empty($empowercast_feed['feed_maximizer_on']) ) { // These tags for the most part replicate what is in the itunes tags, so lets not include them when we want to maximize the feed
		if( !empty( $EpisodeData['gp_desc'] ) ) {
			echo "\t\t<googleplay:description>". empowercast_format_itunes_value($EpisodeData['gp_desc'], 'summary') ."</googleplay:description>".PHP_EOL;
		}

		if( !empty( $EpisodeData['gp_explicit'] ) ) {
			echo "\t\t<googleplay:explicit>yes</googleplay:explicit>".PHP_EOL;
		}
	}

	if( !empty( $EpisodeData['gp_block'] ) ) {
		echo "\t\t<googleplay:block>yes</googleplay:block>".PHP_EOL;
	}

    if ( !empty($EpisodeData['person_names']) ) {
        $personNames = $EpisodeData['person_names'];
        $personRoles = $EpisodeData['person_roles'];
        $personURLs = $EpisodeData['person_urls'];
        $linkURLs = $EpisodeData['link_urls'];

        for ($i=0; $i < count($personNames); $i++) {
            $name = $personNames[$i];
            if ($name == "")
                continue;

            $attrStr = '';
            if ($personRoles[$i] != "")
                $attrStr = ' role="' . $personRoles[$i] . '"';

            if ($personURLs[$i] != "")
                $attrStr .= ' img="' . htmlspecialchars($personURLs[$i]) . '"';

            if ($linkURLs[$i] != "")
                $attrStr .= ' href="' . htmlspecialchars($linkURLs[$i]) . '"';

            echo "\t\t<podcast:person$attrStr>$name</podcast:person>" . PHP_EOL;
        }
    }

    if ( !empty($EpisodeData['soundbite_starts']) ) {
        $soundbiteStarts = $EpisodeData['soundbite_starts'];
        $soundbiteDurations = $EpisodeData['soundbite_durations'];
        $soundbiteTitles = $EpisodeData['soundbite_titles'];

        for ($i=0; $i < count($soundbiteStarts); $i++) {
            $start = $soundbiteStarts[$i];
            $float_start = (float) $start;
            $is_string_float = ( strval($float_start) == $start );

            if ($start == "" || !$is_string_float)
                continue;

            $duration = $soundbiteDurations[$i];
            $float_duration = (float) $duration;
            $is_string_float = ( strval($float_duration) == $duration );
            if ($duration == "" || !$is_string_float)
                continue;

            $attrStr = '';
            $attrStr = ' startTime="' . $start . '"';
            $attrStr .= ' duration="' . $duration . '"';
            $title = $soundbiteTitles[$i];
            echo "\t\t<podcast:soundbite$attrStr>".esc_html($title)."</podcast:soundbite>" . PHP_EOL;
        }
    }

    if( !empty($EpisodeData['location']) ) {
        echo "\t\t<podcast:location";
        if( !empty($EpisodeData['pci_geo']) ) {
            echo " geo=\"" . htmlspecialchars($EpisodeData['pci_geo']) . "\"";
        }
        if( !empty($EpisodeData['pci_osm']) ) {
            echo " osm=\"" . htmlspecialchars($EpisodeData['pci_osm']) . "\"";
        }
        echo ">" . htmlspecialchars($EpisodeData['location']) . "</podcast:location>" . PHP_EOL;
    }

    if (!empty($EpisodeData['value_pubkey']) && !empty($EpisodeData['value_split']) || (isset($EpisodeData['vts_order']) && !empty($EpisodeData['vts_order']))) {
        $lightning = $EpisodeData['value_lightning'];
        $splits = $EpisodeData['value_split'];
        $pubKeys = $EpisodeData['value_pubkey'];
        $customKeys = $EpisodeData['value_custom_key'];
        $customValues = $EpisodeData['value_custom_value'];

        echo "\t\t".'<podcast:value type="lightning" method="keysend" suggested="0.00000005000">'.PHP_EOL;
        for ($i=0; $i < count($pubKeys); $i++) {
            if ($pubKeys[$i] == "")
                continue;

            $attrStr = 'type="node" split="'.$splits[$i].'" address="'.$pubKeys[$i].'"';

            if ($lightning[$i] != "")
                $attrStr .= ' name="'.$lightning[$i].'"';

            if ($customKeys[$i] != "")
                $attrStr .= ' customKey="'.$customKeys[$i].'"';

            if ($customValues[$i] != "")
                $attrStr .= ' customValue="'.$customValues[$i].'"';

            echo "\t\t\t"."<podcast:valueRecipient $attrStr/>\n";
        }

        $podopsAttrs = array(
            "name" =>  "podops@getalby.com",
            "type" => "node",
            "split" => 3,
            "address" => "030a58b8653d32b99200a2334cfe913e51dc7d155aa0116c176657a4f1722677a3",
            "customKey" => "696969",
            "customValue" => "qAHJuqKLmMhTNFualcIj",
            "fee" => "true"
        );

        $podopsAttrStr = "";
        foreach ($podopsAttrs as $key => $value) {
            $podopsAttrStr .= ' '.$key.'="'.$value.'"';
        }
        echo "\t\t\t"."<podcast:valueRecipient $podopsAttrStr/>\n";

        $feed_slug = get_query_var('feed');
        // if this is a blog feed, we need to access the settings associated to the podcast feed
        if (is_category() && $feed_slug == 'feed') {
            $feed_slug = 'podcast';
        }
        if (isset($EpisodeData['vts_order']) && !empty($EpisodeData['vts_order'])) {
            $valueTimeSplits = get_option('vts_'.$feed_slug.'_'.get_the_ID());
            echo "\n";

            foreach ($EpisodeData['vts_order'] as $vts_id) {
                $timeSplit = $valueTimeSplits[$vts_id];
                if ($timeSplit['duration'] == 0)
                    continue;

                $vtsAttrs = array(
                    'startTime' => $timeSplit['start_time'],
                    'duration' => $timeSplit['duration']
                );

                if ($timeSplit['recipient'] == 0)
                    $vtsAttrs['remotePercentage'] = $timeSplit['remote_percent'];

                $vtsAttrStr = "";
                foreach ($vtsAttrs as $key => $value) {
                    $vtsAttrStr .= ' '.$key.'="'.$value.'"';
                }

                echo "\t\t\t"."<podcast:valueTimeSplit $vtsAttrStr>\n";

                if ($timeSplit['recipient'] == 0) {
                    $remoteItem = $timeSplit['remote_item'];

                    $attrs = array('feedGuid' => $remoteItem['feed_guid']);

                    if ($remoteItem['item_guid'] != 'none')
                        $attrs['itemGuid'] = $remoteItem['item_guid'];

                    $attrStr = "";
                    foreach ($attrs as $key => $value) {
                        $attrStr .= ' '.$key.'="'.$value.'"';
                    }

                    echo "\t\t\t\t"."<podcast:remoteItem $attrStr/>\n";
                } else {
                    $valueRecipients = $timeSplit['value_recipients'];

                    foreach ($valueRecipients as $valueRecipient) {
                        $attrs = array(
                            "type" => "node",
                            "split" => $valueRecipient['split'],
                            "address" => $valueRecipient['pubkey'],
                        );

                        if ($valueRecipient['lightning_address'] != "")
                            $attrs['name'] = $valueRecipient['lightning_address'];

                        if ($valueRecipient['custom_key'] != "")
                            $attrs["customKey"] = $valueRecipient['custom_key'];

                        if ($valueRecipient['custom_value'] != "")
                            $attrs["customValue"] = $valueRecipient['custom_value'];

                        $attrStr = "";
                        foreach ($attrs as $key => $value) {
                            $attrStr .= ' '.$key.'="'.$value.'"';
                        }

                        echo "\t\t\t\t"."<podcast:valueRecipient $attrStr/>\n";
                    }
                }

                echo "\t\t\t"."</podcast:valueTimeSplit>\n";
            }
        }

        echo "\t\t".'</podcast:value>'.PHP_EOL;
    }

	// RawVoice RSS Tags
	if( empty($empowercast_feed['feed_maximizer_on']) )
	{
		if( !defined('EMPOWERCAST_RAWVOICE_RSS') || EMPOWERCAST_RAWVOICE_RSS != false )
		{
			if( !empty($EpisodeData['ishd']) )
				echo "\t\t<rawvoice:isHD>yes</rawvoice:isHD>".PHP_EOL;;
			if( !empty($EpisodeData['image']) )
				echo "\t\t<rawvoice:poster url=\"". $EpisodeData['image'] ."\" />".PHP_EOL;
			if( !empty($EpisodeData['embed']) )
				echo "\t\t<rawvoice:embed>". htmlspecialchars($EpisodeData['embed']) ."</rawvoice:embed>".PHP_EOL;
			else if( !empty($empowercast_feed['podcast_embed_in_feed']) && function_exists('empowercast_generate_embed') )
			{
				$player = empowercastplayer_embedable($EpisodeData['url'], $EpisodeData);
				$embed_content = '';

				if( $player )
					$embed_content = empowercast_generate_embed($player, $EpisodeData);
				if( $embed_content )
					echo "\t\t<rawvoice:embed>". htmlspecialchars( $embed_content ) ."</rawvoice:embed>".PHP_EOL;
			}

			if( !empty($EpisodeData['webm_src']) )
			{
				echo "\t\t<rawvoice:webm src=\"". $EpisodeData['webm_src'] ."\"";
				if( $EpisodeData['webm_length'] )
					echo " length=\"". $EpisodeData['webm_length'] ."\"";
				echo " type=\"video/webm\" />".PHP_EOL;
			}

			$GeneralSettings = get_option('empowercast_general', array());

            require_once(EMPOWERCAST_ABSPATH .'/empowercast-metamarks.php');
            empowercast_metamarks_print_rss2($EpisodeData);
		}
	}
}

add_filter('rss2_item', 'empowercast_rss2_item');
add_filter('rss2_item_empowercast', 'empowercast_rss2_item');

/*
This filter is only necessary for feeds that are not specifically for podcasting, e.g. a category feed that did not have category podcasting added to it
*/
function empowercast_filter_rss_enclosure($content)
{
	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') ) {
		return $content; // Another podcasting plugin is enabled...
	}

	if( empowercast_is_custom_podcast_feed() && get_query_var('feed') !== 'podcast' && !is_category() && !is_tag() && !is_tax() )
		return ''; // We will handle this enclosure in the empowercast_rss2_item() function

	$match_count = preg_match('/\surl="([^"]*)"/', $content, $matches); // No URL found, weird
	if( count($matches) != 2)
		return $content;

	// Original Media URL
	$OrigURL = $matches[1];

	if( substr($OrigURL, 0, 5) != 'http:' && substr($OrigURL, 0, 6) != 'https:' )
		return ''; // The URL value is invalid

	global $post, $empowercast_rss_enclosure_post_id;
	if( empty($empowercast_rss_enclosure_post_id) )
		$empowercast_rss_enclosure_post_id = -1;

	if( $empowercast_rss_enclosure_post_id == $post->ID )
		return ''; // we've already included one enclosure, lets not allow anymore
	$empowercast_rss_enclosure_post_id = $post->ID;

	$EpisodeData = empowercast_get_enclosure_data($post->ID);

	// Modified Media URL
	$ModifiedURL = empowercast_url_in_feed($EpisodeData['url']); // empowercast_add_redirect_url($OrigURL);

	// Check that the content type is a valid one...
	$match_count = preg_match('/\stype="([^"]*)"/', $content, $matches);
	if( count($matches) > 1 && strstr($matches[1], '/') == false )
	{
		$ContentType = empowercast_get_contenttype($ModifiedURL);
		$content = str_replace("type=\"{$matches[1]}\"", "type=\"$ContentType\"", $content);
	}

	// Check that the content length is a digit greater that zero
	$match_count = preg_match('/\slength="([^"]*)"/', $content, $matches);
	if( count($matches) > 1 && empty($matches[1]) )
	{
		$content = str_replace("length=\"{$matches[1]}\"", "length=\"5242880\"", $content);
	}

    // encode htmlspecialchars if necessary
    $decoded = htmlspecialchars_decode($ModifiedURL);
    if (strlen($decoded) == strlen($EpisodeData['url'])) {
        // might need encoded/no risk of double encoding
        $ModifiedURL = htmlspecialchars($ModifiedURL);
    }

	// Replace the original url with the modified one...
	if( $OrigURL != $ModifiedURL )
		return str_replace($OrigURL, $ModifiedURL, $content);
	return $content;
}


add_filter('rss_enclosure', 'empowercast_filter_rss_enclosure', 11);

function empowercast_bloginfo_rss($content, $field = '')
{
	$new_value = '';
	if( empowercast_is_custom_podcast_feed() )
	{
		if( is_category() ) {
			$Feed = get_option('empowercast_cat_feed_'.get_query_var('cat'), array() );
		}
		else if( is_tax() || is_tag() ) {
			global $empowercast_feed;
			if( !empty($empowercast_feed['term_taxonomy_id']) )
				$Feed = get_option('empowercast_taxonomy_'.$empowercast_feed['term_taxonomy_id'], array() );
		}
		else
		{
			global $empowercast_feed;

			if( !empty($empowercast_feed['post_type']) )
			{
				$feed_slug = get_query_var('feed');
				$PostTypeSettingsArray = get_option('empowercast_posttype_'.$empowercast_feed['post_type'], array() );
				if( !empty($PostTypeSettingsArray[ $feed_slug ]) )
					$Feed = $PostTypeSettingsArray[ $feed_slug ];
			}
			else
			{
				$Feed = get_option('empowercast_feed_'.get_query_var('feed'), array() );
				if( empty($Feed) && get_query_var('feed') === 'podcast' )
					$Feed = get_option('empowercast_feed', array());
			}
		}

		if( !empty($Feed) )
		{
			switch( $field )
			{
				case 'description': {
					if( !empty($Feed['description']) )
						$new_value = $Feed['description'];
					else if( is_category() )
					{
						$category = get_category( get_query_var('cat') );
						if( $category->description )
							$new_value = $category->description;
					}
				}; break;
				case 'url': {
					// If the website URL is set for this podcast then lets use it...
					if( !empty($Feed['url']) )
						return trim($Feed['url']);

					if( is_category() ) {
						return get_category_link( get_query_var('cat') );
					} else {
						$urlTemp = '';
						$blogHomepage = get_option('page_for_posts');
						if( !empty($blogHomepage) ) {
							$urlTemp = get_permalink( $blogHomepage );
						}

						if( empty($urlTemp) )
							$urlTemp = get_bloginfo('url');
						if( !empty($urlTemp) )
							return $urlTemp;
					}
				}; break;
				case 'name': { // As of wp 4.4+ title is handled by get_the_title_rss completely.
					if( !empty($Feed['title']) )
						$new_value = $Feed['title'];
				}; break;
				case 'language': {
					// Get the feed language
					$lang = '';
					if( isset($Feed['rss_language']) && $Feed['rss_language'] != '' )
						$lang = $Feed['rss_language'];
					if( strlen($lang) == 5 )
						$lang = substr($lang,0,3) .  strtoupper( substr($lang, 3) ); // Format example: en-US for English, United States
					if( !empty($lang) )
						return $lang;
				}; break;
			}
		}
	}

	if( !empty($new_value) )
	{
		$new_value = wptexturize($new_value);
		$new_value = convert_chars($new_value);
		$new_value = esc_html($new_value);
		//$new_value = convert_chars($new_value);
		return $new_value;
	}

	return $content;
}

add_filter('get_bloginfo_rss', 'empowercast_bloginfo_rss', 10, 2);


function empowercast_wp_title_rss($title)
{
	if( version_compare($GLOBALS['wp_version'], 4.4, '>=' ) )
	{
		if( empowercast_is_custom_podcast_feed() )
		{
			if( is_category() ) {
				$Feed = get_option('empowercast_cat_feed_'.get_query_var('cat'), array() );
			}
			else if( is_tax() || is_tag() ) {
				global $empowercast_feed;
				if( !empty($empowercast_feed['term_taxonomy_id']) )
					$Feed = get_option('empowercast_taxonomy_'.$empowercast_feed['term_taxonomy_id'], array() );
			}
			else
			{
				global $empowercast_feed;

				if( !empty($empowercast_feed['post_type']) )
				{
					$feed_slug = get_query_var('feed');
					if( !empty($feed_slug) ) {
						$PostTypeSettingsArray = get_option('empowercast_posttype_'.$empowercast_feed['post_type'], array() );
						if( !empty($PostTypeSettingsArray[ $feed_slug ]) )
							$Feed = $PostTypeSettingsArray[ $feed_slug ];
					}
				}
				else
				{
					$feed_slug = get_query_var('feed');
					$Feed = false;
					if( !empty($feed_slug) ) {
						$Feed = get_option('empowercast_feed_'.get_query_var('feed') );
					}
					if( empty($Feed) && get_query_var('feed') === 'podcast' )
						$Feed = get_option('empowercast_feed');
				}
			}

			if( !empty($Feed) )
			{
				if( !empty($Feed['title']) )
					return esc_html( $Feed['title'] );
			}
		}
	}
	else
	{
		if( empowercast_is_custom_podcast_feed() )
		{
			if( is_category() )
			{
				$Feed = get_option('empowercast_cat_feed_'.get_query_var('cat') );
				if( $Feed && isset($Feed['title']) && $Feed['title'] != '' )
					return ''; // We alrady did a custom title, lets not add the category to it...
			}
			else
			{
				return ''; // It is not a category, lets not mess with our beautiful title then
			}
		}
	}

	return $title;
}

add_filter('get_wp_title_rss', 'empowercast_wp_title_rss');

function empowercast_the_title_rss($title)
{
	$new_title = $title;
	$GeneralSettings = get_option('empowercast_general');
	// If it is a custom podcast channel...
	if( !empty($GeneralSettings['seo_feed_title']) )
	{
		$feed_slug = 'podcast';
		// IF custom post type or channel, use that feed slug...
		if( get_query_var('feed') !== 'podcast' && !is_category() && !is_tax() && !is_tag() )
			$feed_slug = get_query_var('feed');

		// Get the episode specific title...
		$EpisodeData = empowercast_get_enclosure_data(get_the_ID(), $feed_slug);
		if( !empty($EpisodeData['feed_title']) )
		{
			$feed_title = ent2ncr( $EpisodeData['feed_title'] );
			$feed_title = strip_tags( $feed_title );
			$feed_title = esc_html( $feed_title );

			//switch( $GeneralSettings['custom_feed_title'] )
			switch( $GeneralSettings['seo_feed_title'] )
			{
				case 1: { // Replaces title
					$new_title = $feed_title;
				}; break;
				case 2: { // Prefixes title
					$new_title = $feed_title . ' ' . $title;
				}; break;
				case 3: { // Postfixes title
					$new_title = $title . ' ' . $feed_title;
				}; break;
			}
		}
	}

	return $new_title;
}

add_filter('the_title_rss', 'empowercast_the_title_rss', 11);


function empowercast_feed_content_type($content_type = '', $feedslug = '')
{
	switch( $feedslug )
	{
		case 'rss':
		case 'rss2':
		case 'atom':
		case 'rdf': {
			// Do nothing, let WordPress take care of these
		}; break;
		case 'podcast': {
			// This one is ours!
			$content_type = 'application/rss+xml';
		}; break;
		default: { // Check for the custom podcast feeds
			$GeneralSettings = get_option('empowercast_general');
			if( !empty($GeneralSettings['custom_feeds'][ $feedslug ]) )
			{
				$content_type = 'application/rss+xml';
			}
			else if( !empty($GeneralSettings['posttype_podcasting']) )
			{
				// We need to look up these settings...
				$FeedSlugPostTypesArray = get_option('empowercast_posttype-podcasting');
				if( is_array($FeedSlugPostTypesArray) && !empty($FeedSlugPostTypesArray[ $feedslug ]) )
				{
					$content_type = 'application/rss+xml';
				}
			}
		}
	}

	return $content_type;
}

add_filter( 'feed_content_type', 'empowercast_feed_content_type', 10, 2 );

function wpse_152316_wp_audio_extensions( $ext )
{
    remove_filter( current_filter(), __FUNCTION__ );
    $ext[] = '';
    return $ext;
}

/**
 * Allow unrecognized audio sources hosted on trusted hosts that use query strings on their podcast media.
 *
 * @see http://wordpress.stackexchange.com/a/152352/26350
 */

add_filter( 'wp_audio_shortcode_override',
    function( $html, $atts )
    {
        if (isset($atts['src'])) {
            $trusted_hosts_use_qstrings = array('traffic.libsyn.com', 'cdn.simplecast.com', 'buzzsprout.com', 'audioboom.com', 'mc.thepodops.com');
            foreach ($trusted_hosts_use_qstrings as $host) {
                if (strpos($atts['src'], $host) !== false) {
                    add_filter('wp_audio_extensions', 'wpse_152316_wp_audio_extensions');
                }
            }
        }
        return $html;
    }
    , PHP_INT_MAX, 2 );

// Following code only works for WP 3.3 or older. WP 3.4+ now uses the get_locale setting, so we have to override directly in the get_bloginfo_rss functoin.
if( version_compare($GLOBALS['wp_version'], '3.4', '<') )
{
	function empowercast_rss_language($value)
	{
		if( empowercast_is_custom_podcast_feed() )
		{
			global $empowercast_feed;
			if( $empowercast_feed && isset($empowercast_feed['rss_language']) && $empowercast_feed['rss_language'] != '' )
				$value = $empowercast_feed['rss_language'];
		}
		return $value;
	}

	add_filter('option_rss_language', 'empowercast_rss_language');
}

//filter to ensure that guid doesn't come up blank
function empowercast_the_guid($guid) {
    global $post;
	
	// Simple case, what is in the DB is better than an empty value
	if( empty($guid) && !empty($post->guid) ) {
		return $post->guid;
	}
	
	if( !empty($post->guid) ) {
		if( preg_match('/^https?:\/\//i', $post->guid, $matches) == false ) {
			$empowercastGuid = get_post_meta($post->ID, '_empowercast_guid', true);
			if( !empty($empowercastGuid) )
				return $empowercastGuid;
		}
	}
    
    return $guid;
}

function empowercast_do_podcast_feed($for_comments=false)
{
	global $wp_query, $empowercast_feed;

	empowercast_is_podcast_feed(); // Loads the feed settings if not already loaded...

	$GeneralSettings = get_option('empowercast_general');
	if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] )
	{
		$feed_slug = get_query_var('feed');

		if( $feed_slug != 'podcast' )
		{
			$FeedSettings = get_option('empowercast_feed_'.$feed_slug);
			if( !empty($FeedSettings['premium']) )
			{
				require_once( EMPOWERCAST_ABSPATH.'/empowercast-feed-auth.php');
				empowercast_feed_auth( $feed_slug );
			}
		}
	}

	// Use the template to gurantee future WordPress behavior
	if( defined('EMPOWERCAST_FEED_TEMPLATE') ) {
		load_template( EMPOWERCAST_FEED_TEMPLATE );
	} else {
		load_template( EMPOWERCAST_ABSPATH . '/feed-podcast.php' );
	}
}

function empowercast_template_redirect()
{
	if( is_feed() && empowercast_is_custom_podcast_feed() )
	{
		if ( defined('WPSEO_VERSION') && version_compare(WPSEO_VERSION, '7.7',  '>=') && class_exists( 'WPSEO_Frontend' ) ) {
			$wpseo_frontend = WPSEO_Frontend::get_instance();
			if( !empty($wpseo_frontend) ) {
				remove_action( 'template_redirect', array( $wpseo_frontend, 'noindex_feed' ) );
			}
		}

		remove_action('template_redirect', 'ol_feed_redirect'); // Remove this action so feedsmith doesn't redirect
		global $empowercast_feed;
		if( !isset($empowercast_feed['feed_redirect_url']) )
			$empowercast_feed['feed_redirect_url'] = '';
		$redirect_value = ( !empty($_GET['redirect'])? $_GET['redirect'] : false );
        $user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		if( is_array($empowercast_feed) && trim($empowercast_feed['feed_redirect_url']) != '' && !preg_match("/feedburner|feedsqueezer|feedvalidator/i", $user_agent ) && $redirect_value != 'no' )
		{
			if (function_exists('status_header'))
				status_header( 301 );
			header("Location: " . trim($empowercast_feed['feed_redirect_url']));
			header("HTTP/1.1 301 Moved Permanently");
			exit();
		}
	}
}

add_action('template_redirect', 'empowercast_template_redirect', 0);


function empowercast_rewrite_rules_array($array)
{
	global $wp_rewrite;
	$settings = get_option('empowercast_general');

	$podcast_feeds = array('podcast'=>true);
	if( isset($settings['custom_feeds']) && is_array($settings['custom_feeds']) )
		$podcast_feeds = array_merge($settings['custom_feeds'], $podcast_feeds );

	$merged_slugs = '';
	foreach( $podcast_feeds as $feed_slug=> $feed_title )
	{
		if( $merged_slugs != '' )
			$merged_slugs .= '|';
		$merged_slugs .= $feed_slug;
	}

	// $wp_rewrite->index most likely index.php
	$new_array[ 'feed/('.$merged_slugs.')/?$' ] = $wp_rewrite->index. '?feed='. $wp_rewrite->preg_index(1);

	// If feature is not enabled, use the default permalinks
	if( empty($settings['permalink_feeds_only']) )
		return array_merge($new_array, $array);

	global $wpdb;
	reset($podcast_feeds);
	foreach( $podcast_feeds as $feed_slug=> $feed_title )
	{
		$page_name_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = '".$feed_slug."'");
		if( $page_name_id )
		{
			$new_array[ $feed_slug.'/?$' ] = $wp_rewrite->index. '?pagename='. $feed_slug.'&page_id='.$page_name_id;
			unset($podcast_feeds[ $feed_slug ]);
			continue;
		}

		$category = get_category_by_slug($feed_slug);
		if( $category )
		{
			$new_array[ $feed_slug.'/?$' ] = $wp_rewrite->index. '?cat='. $category->term_id; // category_name='. $feed_slug .'&
			unset($podcast_feeds[ $feed_slug ]);
		}
	}

	if( count($podcast_feeds) > 0 )
	{
		reset($podcast_feeds);
		$remaining_slugs = '';
		foreach( $podcast_feeds as $feed_slug=> $feed_title )
		{
			if( $remaining_slugs != '' )
				$remaining_slugs .= '|';
			$remaining_slugs .= $feed_slug;
		}

		$new_array[ '('.$remaining_slugs.')/?$' ] = $wp_rewrite->index. '?pagename='. $wp_rewrite->preg_index(1);
	}

	return array_merge($new_array, $array);
}

add_filter('rewrite_rules_array', 'empowercast_rewrite_rules_array');


function empowercast_pre_transient_rewrite_rules($return_rules)
{
	global $wp_rewrite;
	$GeneralSettings = get_option('empowercast_general');
	if( !in_array('podcast', $wp_rewrite->feeds) )
		$wp_rewrite->feeds[] = 'podcast';

	if( $GeneralSettings && isset($GeneralSettings['custom_feeds']) && is_array($GeneralSettings['custom_feeds']) )
	{
		foreach( $GeneralSettings['custom_feeds'] as $feed_slug=> $null )
		{
			if( !in_array($feed_slug, $wp_rewrite->feeds) )
				$wp_rewrite->feeds[] = $feed_slug;
		}
	}

	return $return_rules;
}

add_filter('pre_transient_rewrite_rules', 'empowercast_pre_transient_rewrite_rules');

function empowercast_init()
{
	$GeneralSettings = get_option('empowercast_general');


    if( empty($GeneralSettings['disable_appearance']) || $GeneralSettings['disable_appearance'] == false )
	{
		require_once( EMPOWERCAST_ABSPATH.'/empowercast-player.php');
		empowercastplayer_init($GeneralSettings);
	}

	// Enable the playlist feature for EmpowerCast
	if( !empty($GeneralSettings['playlist_player']) ) // Either not set or set on
	{
		require_once(EMPOWERCAST_ABSPATH.'/empowercast-playlist.php');
	}

	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return false; // Another podcasting plugin is enabled...

	// If we are to process podpress data..
	if( !empty($GeneralSettings['process_podpress']) )
	{
		empowercast_podpress_redirect_check();
	}

	// Add the podcast feeds;
	if( !defined('EMPOWERCAST_NO_PODCAST_FEED') )
	{
		add_feed('podcast', 'empowercast_do_podcast_feed');
	}

	if( $GeneralSettings && isset($GeneralSettings['custom_feeds']) && is_array($GeneralSettings['custom_feeds']) )
	{
		foreach( $GeneralSettings['custom_feeds'] as $feed_slug=> $feed_title )
		{
			if( $feed_slug != 'podcast' )
				add_feed($feed_slug, 'empowercast_do_podcast_feed');
		}
	}

	if( !empty($GeneralSettings['posttype_podcasting']) )
	{
		// Loop through the posttype podcasting settings and set the feeds for the custom post type slugs...
		global $wp_rewrite;


		$FeedSlugPostTypesArray = get_option('empowercast_posttype-podcasting'); // Changed field slightly so it does not conflict with a post type "podcasting"
		if( $FeedSlugPostTypesArray === false )
		{
			// Simple one-time fix...
			$FeedSlugPostTypesArray = get_option('empowercast_posttype_podcasting');
			if( empty($FeedSlugPostTypesArray) )
				$FeedSlugPostTypesArray = array();
			update_option('empowercast_posttype-podcasting', $FeedSlugPostTypesArray);
			if( !array_key_exists('title', $FeedSlugPostTypesArray) ) // AS long as it doesn't have post type specific settings...
				delete_option('empowercast_posttype_podcasting');
		}

		if( empty($FeedSlugPostTypesArray) )
		{
			$FeedSlugPostTypesArray = array();
		}
		foreach( $FeedSlugPostTypesArray as $feed_slug=> $FeedSlugPostTypes )
		{
			if ( !in_array($feed_slug, $wp_rewrite->feeds) ) // we need to add this feed name
			{
				add_feed($feed_slug, 'empowercast_do_podcast_feed');
				foreach( $FeedSlugPostTypes as $post_type_slug=> $title )
				{
					add_rewrite_rule( '/'. $post_type_slug .'/feed/'. $feed_slug .'/?$', 'index.php?post_type='. $post_type_slug .'&feed='.$feed_slug, 'top' ); // capture the post type feeds
					add_rewrite_rule( '/'. $post_type_slug .'/feed/'. $feed_slug .'/?$', 'index.php?post_type='. $post_type_slug .'&feed='.$feed_slug, 'bottom' ); // capture the post type feeds
				}
			}
		}
	}

	if( defined('GAWP_VERSION') )
	{
		add_filter('the_content', 'empowercast_yoast_gawp_fix', 120 );
	}

	if( !empty($GeneralSettings['subscribe_links']) )
	{
		// 2 Subscribe page shortocde [empowercast_subscribe feedslug="podcast"]
		// 3 Subscribe sidebar widget: iTunes, RSS
		add_filter('empowercast_player_subscribe_links', 'empowercastplayer_link_subscribe_pre', 1, 3);
		add_filter('empowercast_player_subscribe_links', 'empowercastplayer_link_subscribe_post', 1000, 3);
	}
	wp_register_style(
			'empowercast-subscribe-style',
			empowercast_get_root_url() . 'css/subscribe.css',
			array(),
			'20141021',
			'all' );

	if( !empty($GeneralSettings['rss_emoji']) ) {
		if( has_filter('the_content_feed',   'wp_staticize_emoji') ) {
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' ); // Remove the emoji images
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			//add_filter( 'the_content_feed', 'wp_encode_emoji' ); // Convert an emoji to &#x1Fxxx;
			//add_filter( 'get_wp_title_rss', 'wp_encode_emoji' );
		}
	}

    if( !defined('EMPOWERCAST_NO_REMOVE_WP_HEAD') ) {
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    add_filter( 'the_guid', 'empowercast_the_guid', 11 );



    if (!isset($GeneralSettings)) {
        $GeneralSettings = get_option('empowercast_general');
    }

    if (!empty($GeneralSettings['empowercast_network'])) {
        require_once( EMPOWERCAST_ABSPATH .'/empowercast-network.php');
        if (class_exists('EmpowerCastNetwork')) {
            $GLOBALS['ppn_object'] = new EmpowerCastNetwork('empowercastadmin_basic');
            $GLOBALS['ppn_object']->setDisplay();

            if (is_admin()) {
                if (defined('WP_DEBUG')) {
                    if (WP_DEBUG) {
                        wp_register_style('empowercast-network', empowercast_get_root_url() . 'css/style.css', array(), EMPOWERCAST_VERSION);
                    } else {
                        wp_register_style('empowercast-network', empowercast_get_root_url() . 'css/style.min.css', array(), EMPOWERCAST_VERSION);
                    }
                }
                else {
                    wp_register_style('empowercast-network', empowercast_get_root_url() . 'css/style.min.css', array(), EMPOWERCAST_VERSION);
                }

                wp_enqueue_style('empowercast-network');
                //wp_enqueue_script('empowercast-network', $this::empowercast_network_plugin_url() . 'js/admin.js', array('jquery'));
            }

            $plugin_url = plugin_dir_url(__FILE__);
            //wp_enqueue_style('style', $plugin_url . 'css/stylesheet.css');

            if (defined('WP_DEBUG')) {
                if (WP_DEBUG) {
                    wp_enqueue_style('style', $plugin_url . 'css/blueprint.css', array(), EMPOWERCAST_VERSION);
                } else {
                    wp_enqueue_style('style', $plugin_url . 'css/blueprint.min.css', array(), EMPOWERCAST_VERSION);
                }
            } else {
                wp_enqueue_style('style', $plugin_url . 'css/blueprint.min.css', array(), EMPOWERCAST_VERSION);
            }
        }
    }

}

add_action('init', 'empowercast_init', -100); // We need to add the feeds before other plugins start screwing with them

function empowercast_init_block() {
    if (function_exists('register_block_type')) {
        // register block(s)
        register_block_type(__DIR__ . '/blocks/player-block/build', array('render_callback' => function ($attributes, $content, $block) {
            $return = '';
            $GeneralSettings = get_option('empowercast_general');

            // first, dropdown to select feed if necessary
            $is_backend = defined('REST_REQUEST') && REST_REQUEST == true && filter_input(INPUT_GET, 'context', FILTER_SANITIZE_SPECIAL_CHARS) == 'edit';
            if ($is_backend && !empty($GeneralSettings['custom_feeds']) && !empty($attributes['id'])) {
                $return .= "<select id='select-feed-{$attributes['id']}' disabled>";
                if (empty($attributes['feed_slug'])) {
                    $return .= '<option value="" class="pp-block-select">Channel: No selection</option>';
                } else {
                    $return .= '<option value="" class="pp-block-select">Channel: No selection</option>';
                }
                if ($attributes['feed_slug'] == 'podcast') {
                    $return .= '<option value="podcast" class="pp-block-select" selected>Channel: Main Feed</option>';
                } else {
                    $return .= '<option value="podcast" class="pp-block-select">Channel: Main Feed</option>';
                }
                foreach ($GeneralSettings['custom_feeds'] as $slug => $title) {
                    if (!empty($attributes['feed_slug']) && $attributes['feed_slug'] == $slug) {
                        $return .= '<option value="' . $slug . '" class="pp-block-select" selected>' . 'Channel: ' . $title . '</p>';
                    } else {
                        $return .= '<option value="' . $slug . '" class="pp-block-select">' . 'Channel: ' . $title . '</p>';
                    }
                }
                $return .= "</select>";
            }

            // print shortcode on public side
            if (!$is_backend) {
                if (!empty($attributes['feed_slug'])) {
                    return '[empowercast channel="' . $attributes['feed_slug'] . '"]';
                }
                return '';
            }

            if (!empty($attributes['feed_slug'])) {
                // for editor, generate html from the shortcode and send it
                $return .= "<div>";
                $return .= "<p class='pp-block-error-{$attributes['id']}'></p><div class='pp-block-sample'>";
                $return .= do_shortcode('[empowercast sample="1" channel="' . $attributes['feed_slug'] . '"]');
                $return .= "</div></div>";
            }

            // randomly, sometimes, when we add the clientId on the frontend, the block does not re-render and just returns empty
            if (empty($return)) {
                $return = "<p class='alert alert-danger'>" . __("Something went wrong. Please delete this block and restart it.", "empowercast") . "</p>";
            }
            return $return;
        }, 'attributes' => array(
            'updated' => array(
                'type' => 'boolean',
                'default' => false,
            ),
            'feed_slug' => array(
                'type' => 'string',
                'default' => empty($GeneralSettings['custom_feeds']) ? 'podcast' : '',
            ),
            'id' => array(
                'type' => 'string',
                'default' => '',
            ),
        )));
    }
}

add_action('init', 'empowercast_init_block', 100); // We need to add this AFTER everything initializes


function empowercast_wp_print_styles()
{
	$Settings = get_option('empowercast_general');

	if( !empty($Settings['audio_player_max_width']) )
	{
		echo '<style type="text/css">'."\n";
		if( is_numeric($Settings['audio_player_max_width']) )
			$Settings['audio_player_max_width'] .= 'px';
		echo '.empowercast_player .wp-audio-shortcode { max-width: '.$Settings['audio_player_max_width'].'; }'."\n";
		echo '</style>'."\n";
	}
}

add_action('wp_print_styles', 'empowercast_wp_print_styles');

function empowercast_request($qv)
{
	if( !empty($qv['feed']) )
	{
		$podcast_feed_slug = false;
		if( $qv['feed'] == 'podcast' ) {
			$GeneralSettings = get_option('empowercast_general');
			if( empty($GeneralSettings['posttype_podcasting']) )
				$podcast_feed_slug = 'podcast';
		} else if( $qv['feed'] == 'rss' || $qv['feed'] == 'rss2' || $qv['feed'] == 'atom' || $qv['feed'] == 'rdf' || $qv['feed'] == 'feed' ) { //  'feed', 'rdf', 'rss', 'rss2', 'atom'
			// Skip
		} else {
			$GeneralSettings = get_option('empowercast_general');
			if( empty($GeneralSettings['posttype_podcasting']) && isset($GeneralSettings['custom_feeds']) && is_array($GeneralSettings['custom_feeds']) && !empty($GeneralSettings['custom_feeds'][ $qv['feed'] ] ) )
				$podcast_feed_slug = $qv['feed'];


		}

		if( $podcast_feed_slug )
		{
			if( !defined('EMPOWERCAST_POSTTYPE_MIXING') && $qv['feed'] == 'podcast' ) {
				$qv['post_type'] = 'post';
			} else {
				$qv['post_type'] = get_post_types( array('public'=> true, 'capability_type'=>'post') );
				if( !empty($qv['post_type']['attachment']) )
					unset($qv['post_type']['attachment']);
			}

			$FeedCustom = get_option('empowercast_feed_'.$podcast_feed_slug); // Get custom feed specific settings
			// See if the user set a custom post type only...
			if( !empty($FeedCustom) && !empty( $FeedCustom['custom_post_type']) )
				$qv['post_type'] = $FeedCustom['custom_post_type'];
		}
	}
	return $qv;
}

add_filter('request', 'empowercast_request');


function empowercast_plugins_loaded()
{
	// Translation support loaded:
	load_plugin_textdomain('empowercast', // domain / keyword name of plugin
		EMPOWERCAST_ABSPATH .'/languages', // Absolute path
		basename(EMPOWERCAST_ABSPATH).'/languages' ); // relative path in plugins folder

/*
####
# Defines that effect translation defined now:
####
*/
	// Set specific play and download labels for your installation of EmpowerCast
	if( !defined('EMPOWERCAST_LINKS_TEXT') )
		define('EMPOWERCAST_LINKS_TEXT', __('Podcast', 'empowercast') );
	if( !defined('EMPOWERCAST_DURATION_TEXT') )
		define('EMPOWERCAST_DURATION_TEXT', __('Duration', 'empowercast') );
	if( !defined('EMPOWERCAST_PLAY_IN_NEW_WINDOW_TEXT') )
		define('EMPOWERCAST_PLAY_IN_NEW_WINDOW_TEXT', __('Play in new window', 'empowercast') );
	if( !defined('EMPOWERCAST_DOWNLOAD_TEXT') )
		define('EMPOWERCAST_DOWNLOAD_TEXT', __('Download', 'empowercast') );
	if( !defined('EMPOWERCAST_PLAY_TEXT') )
		define('EMPOWERCAST_PLAY_TEXT', __('Play', 'empowercast') );
	if( !defined('EMPOWERCAST_EMBED_TEXT') )
		define('EMPOWERCAST_EMBED_TEXT', __('Embed', 'empowercast') );
	if( !defined('EMPOWERCAST_READ_TEXT') )
		define('EMPOWERCAST_READ_TEXT', __('Read', 'empowercast') );
}
add_action('plugins_loaded', 'empowercast_plugins_loaded');


function empowercast_w3tc_can_print_comment($settings)
{
	 return false;
}

// Disable minifying if W3TC is enabled
function empowercast_w3tc_minify_enable($enable)
{
	if( is_feed() )
		return false;
	return $enable;
}

// Load the general feed settings for feeds handled by empowercast
function empowercast_load_general_feed_settings()
{
	global $wp_query;
	global $empowercast_feed;

	if( $empowercast_feed !== false ) // If it is not false (either NULL or an array) then we already looked these settings up
	{
		$empowercast_feed = false;

		// Get the empowercast settings
		$GeneralSettings = get_option('empowercast_general');
		if( !isset($GeneralSettings['custom_feeds']['podcast']) )
			$GeneralSettings['custom_feeds']['podcast'] = 'Podcast Feed'; // Fixes scenario where the user never configured the custom default podcast feed.
		if( empty($GeneralSettings['default_url']) )
			$GeneralSettings['default_url'] = '';

		if( $GeneralSettings )
		{
			$FeedSettingsBasic = get_option('empowercast_feed'); // Get overall feed settings
			if( is_feed() && defined( 'WPCACHEHOME' ) && empty($GeneralSettings['allow_feed_comments']) )
			{
				global $wp_super_cache_comments;
				$wp_super_cache_comments = 0;
			}

			if( is_feed() && defined('W3TC') && empty($GeneralSettings['allow_feed_comments']) )
			{
				add_filter( 'w3tc_can_print_comment', 'empowercast_w3tc_can_print_comment', 10, 1 );
			}

			if( is_feed() && defined('W3TC') )
			{
				add_filter( 'w3tc_minify_enable', 'empowercast_w3tc_minify_enable');
			}

			// If we're in advanced mode and we're dealing with a category feed we're extending, lets work with it...
			if( is_category() && isset($GeneralSettings['custom_cat_feeds']) && is_array($GeneralSettings['custom_cat_feeds']) && in_array( get_query_var('cat'), $GeneralSettings['custom_cat_feeds']) )
			{
				$cat_ID = get_query_var('cat');
				$FeedCustom = get_option('empowercast_cat_feed_'.$cat_ID); // Get custom feed specific settings
				$Feed = empowercast_merge_empty_feed_settings($FeedCustom, $FeedSettingsBasic);

				$empowercast_feed = array();
				if( !empty($GeneralSettings['feed_accel']) )
					$empowercast_feed['feed_accel'] = true;
				$empowercast_feed['is_custom'] = true;
				$empowercast_feed['category'] = $cat_ID;
				$empowercast_feed['process_podpress'] = !empty($GeneralSettings['process_podpress']); // Category feeds could originate from Podpress
				$empowercast_feed['rss_language'] = ''; // default, let WordPress set the language
				$empowercast_feed['default_url'] = '';
				if( !empty($GeneralSettings['default_url']) )
					$empowercast_feed['default_url'] = rtrim($GeneralSettings['default_url'], '/') .'/';
				// switching from 'not set' 'yes' 'clean' to 'true' 'false'--for backwards compatibility, 'not set' will now be 'false'
				$explicit_array = array("false", "true", "false");
				$empowercast_feed['explicit'] = $explicit_array[$Feed['itunes_explicit']];
				if( !empty($Feed['itunes_talent_name']) )
					$empowercast_feed['itunes_talent_name'] = $Feed['itunes_talent_name'];
				else
					$empowercast_feed['itunes_talent_name'] = get_wp_title_rss();
				$empowercast_feed['enhance_itunes_summary'] = $Feed['enhance_itunes_summary'];
				if( !empty($GeneralSettings['seo_itunes']) )
					$empowercast_feed['enhance_itunes_summary'] = 1;
				$empowercast_feed['posts_per_rss'] = false;
				if( !empty($Feed['posts_per_rss']) && is_numeric($Feed['posts_per_rss']) && $Feed['posts_per_rss'] > 0 )
					$empowercast_feed['posts_per_rss'] = $Feed['posts_per_rss'];
				$empowercast_feed['feed_redirect_url'] = '';
				if( !empty($Feed['feed_redirect_url']) )
					$empowercast_feed['feed_redirect_url'] = $Feed['feed_redirect_url'];
				if( $Feed['itunes_author_post'] == true )
					$empowercast_feed['itunes_author_post'] = true;
				if( $Feed['rss_language'] != '' )
					$empowercast_feed['rss_language'] = $Feed['rss_language'];

				if( !empty($GeneralSettings['podcast_embed_in_feed']) )
					$empowercast_feed['podcast_embed_in_feed'] = true;
				if( !empty($Feed['maximize_feed']) )
					$empowercast_feed['maximize_feed'] = true;
                if( !empty($Feed['unlock_podcast']) )
                    $empowercast_feed['unlock_podcast'] = true;
				if( !empty($Feed['episode_itunes_image']) && !empty($Feed['itunes_image']) )
					$empowercast_feed['itunes_image'] = $Feed['itunes_image'];
				return;
			}
			else if( ( defined('EMPOWERCAST_TAXONOMY_PODCASTING') || !empty($GeneralSettings['taxonomy_podcasting']) ) && ( is_tag() || is_tax() ) )
			{
				// We need to get the term_id and the tax_id (tt_id)
				$term_slug = get_query_var('term');
				$taxonomy = get_query_var('taxonomy');

				if( empty($term_slug) && empty($taxonomy) ) // Handle situation where tag is the taxonomy we're working with
				{
					$term_slug = get_query_var('tag');
					if( !empty($term_slug) )
						$taxonomy = 'post_tag';
				}

				$term = false;
				if( !empty($term_slug) && !empty($taxonomy) )
				{
					$term = term_exists($term_slug, $taxonomy);
				}

				if( !empty($term['term_taxonomy_id']) )
				{
					$FeedCustom = get_option('empowercast_taxonomy_'.$term['term_taxonomy_id'] ); // Get custom feed specific settings
					if( $FeedCustom )
					{
						$Feed = empowercast_merge_empty_feed_settings($FeedCustom, $FeedSettingsBasic);

						$empowercast_feed = array();
						if( !empty($GeneralSettings['feed_accel']) )
							$empowercast_feed['feed_accel'] = true;
						$empowercast_feed['is_custom'] = true;
						$empowercast_feed['term_taxonomy_id'] = $term['term_taxonomy_id'];
						$empowercast_feed['process_podpress'] = false; // Taxonomy feeds will not originate from Podpress
						$empowercast_feed['rss_language'] = ''; // default, let WordPress set the language
						$empowercast_feed['default_url'] = rtrim($GeneralSettings['default_url'], '/') .'/';
                        // switching from 'not set' 'yes' 'clean' to 'true' 'false'--for backwards compatibility, 'not set' will now be 'false'
                        $explicit_array = array("false", "true", "false");
						$empowercast_feed['explicit'] = $explicit_array[$Feed['itunes_explicit']];
						if( !empty($Feed['itunes_talent_name']) )
							$empowercast_feed['itunes_talent_name'] = $Feed['itunes_talent_name'];
						else
							$empowercast_feed['itunes_talent_name'] = get_wp_title_rss();
						$empowercast_feed['enhance_itunes_summary'] = $Feed['enhance_itunes_summary'];
						if( !empty($GeneralSettings['seo_itunes']) )
							$empowercast_feed['enhance_itunes_summary'] = 1;
						$empowercast_feed['posts_per_rss'] = false;
						if( !empty($Feed['posts_per_rss']) && is_numeric($Feed['posts_per_rss']) && $Feed['posts_per_rss'] > 0 )
							$empowercast_feed['posts_per_rss'] = $Feed['posts_per_rss'];
						if( $Feed['feed_redirect_url'] != '' )
							$empowercast_feed['feed_redirect_url'] = $Feed['feed_redirect_url'];
						if( $Feed['itunes_author_post'] == true )
							$empowercast_feed['itunes_author_post'] = true;
						if( $Feed['rss_language'] != '' )
							$empowercast_feed['rss_language'] = $Feed['rss_language'];

						if( !empty($GeneralSettings['podcast_embed_in_feed']) )
							$empowercast_feed['podcast_embed_in_feed'] = true;
						if( !empty($Feed['maximize_feed']) )
							$empowercast_feed['maximize_feed'] = true;
                        if( !empty($Feed['unlock_podcast']) )
                            $empowercast_feed['unlock_podcast'] = true;
						if( !empty($Feed['episode_itunes_image']) && !empty($Feed['itunes_image']) )
							$empowercast_feed['itunes_image'] = $Feed['itunes_image'];
						return;
					}
				}
			}

			$feed_slug = get_query_var('feed');
			// Are we dealing with a custom podcast channel or a custom post type podcast feed...
			if( !empty($GeneralSettings['posttype_podcasting']) || isset($GeneralSettings['custom_feeds'][ $feed_slug ]) )
			{
				$Feed = false;
				if( !empty($GeneralSettings['posttype_podcasting']) )
				{
					$post_type = get_query_var('post_type');

					if( !empty($post_type) )
					{
						if ( is_array( $post_type ) ) {
							$post_type = reset( $post_type ); // get first element in array
						}

						// Get the settings for this podcast post type
						$PostTypeSettingsArray = get_option('empowercast_posttype_'. $post_type);
						if( !empty($PostTypeSettingsArray[ $feed_slug ]) )
						{
							$FeedCustom = $PostTypeSettingsArray[ $feed_slug ];
							$Feed = empowercast_merge_empty_feed_settings($FeedCustom, $FeedSettingsBasic);
							$Feed['post_type'] = $post_type;
						}
					}
				}
				if( empty($Feed) && isset($GeneralSettings['custom_feeds'][ $feed_slug ]) )
				{
					$FeedCustom = get_option('empowercast_feed_'.$feed_slug); // Get custom feed specific settings
					$Feed = empowercast_merge_empty_feed_settings($FeedCustom, $FeedSettingsBasic, ($feed_slug == 'podcast') );
				}

				if( $Feed )
				{
					$empowercast_feed = array();
					if( !empty($GeneralSettings['feed_accel']) )
						$empowercast_feed['feed_accel'] = true;
					$empowercast_feed['is_custom'] = true;
					$empowercast_feed['feed-slug'] = $feed_slug;
					if( !empty($Feed['post_type']) )
						$empowercast_feed['post_type'] = $Feed['post_type'];
					$empowercast_feed['process_podpress'] = ($feed_slug=='podcast'? !empty($GeneralSettings['process_podpress']): false); // We don't touch podpress data for custom feeds
					$empowercast_feed['rss_language'] = ''; // RSS language should be set by WordPress by default
					$empowercast_feed['default_url'] = '';
					if( !empty($empowercast_feed['default_url']) )
						$empowercast_feed['default_url'] = rtrim($GeneralSettings['default_url'], '/') .'/';
                    // switching from 'not set' 'yes' 'clean' to 'true' 'false'--for backwards compatibility, 'not set' will now be 'false'
                    $explicit = array("false", "true", "false");
					$empowercast_feed['explicit'] ='false';
					if( !empty($Feed['itunes_explicit']) )
						$empowercast_feed['explicit'] = $explicit[ $Feed['itunes_explicit'] ];
					if( !empty($Feed['itunes_talent_name']) )
						$empowercast_feed['itunes_talent_name'] = $Feed['itunes_talent_name'];
					else
						$empowercast_feed['itunes_talent_name'] = get_wp_title_rss();
					$empowercast_feed['enhance_itunes_summary'] = $Feed['enhance_itunes_summary'];
					if( !empty($GeneralSettings['seo_itunes']) )
						$empowercast_feed['enhance_itunes_summary'] = 1;
					$empowercast_feed['posts_per_rss'] = false;
					if( !empty($Feed['posts_per_rss']) && is_numeric($Feed['posts_per_rss']) && $Feed['posts_per_rss'] > 0 )
						$empowercast_feed['posts_per_rss'] = $Feed['posts_per_rss'];
					if( !empty($Feed['feed_redirect_url']) )
						$empowercast_feed['feed_redirect_url'] = $Feed['feed_redirect_url'];
					if( !empty($Feed['itunes_author_post'] ) )
						$empowercast_feed['itunes_author_post'] = true;
					if( !empty($Feed['rss_language']) )
						$empowercast_feed['rss_language'] = $Feed['rss_language'];
					if( !empty($GeneralSettings['podcast_embed_in_feed']) )
						$empowercast_feed['podcast_embed_in_feed'] = true;
					if( !empty($Feed['maximize_feed']) )
						$empowercast_feed['maximize_feed'] = true;
                    if( !empty($Feed['unlock_podcast']) )
                        $empowercast_feed['unlock_podcast'] = true;
					if( !empty($Feed['episode_itunes_image']) && !empty($Feed['itunes_image']) )
						$empowercast_feed['itunes_image'] = $Feed['itunes_image'];
					return;
				}
			}

			if( !isset($FeedSettingsBasic['apply_to']) )
				$FeedSettingsBasic['apply_to'] = 1;

			// We fell this far,we must be in simple mode or the user never saved customized their custom feed settings
			switch( $FeedSettingsBasic['apply_to'] )
			{
				case 0: // enhance only the podcast feed added by EmpowerCast, with the logic above this code should never be reached but it is added for readability.
				{
					if( $feed_slug != 'podcast' )
						break;
				} // important: no break here!
				case 2: // RSS2 Main feed and podcast feed added by EmpowerCast only
				{
					if( $feed_slug != 'feed' && $feed_slug != 'rss2' && $feed_slug != 'podcast' )
						break; // We're only adding podcasts to the rss2 feed in this situation

					if( $wp_query->is_category ) // don't touch the category feeds...
						break;

					if( $wp_query->is_tag ) // don't touch the tag feeds...
						break;

					if( $wp_query->is_comment_feed ) // don't touch the comments feeds...
						break;
				} // important: no break here!
				case 1: // All feeds
				{
					$empowercast_feed = array(); // Only store what's needed for each feed item
					if( !empty($GeneralSettings['feed_accel']) )
						$empowercast_feed['feed_accel'] = true;
					$empowercast_feed['is_custom'] = false; // ($feed_slug == 'podcast'?true:false);
					$empowercast_feed['feed-slug'] = $feed_slug;
					$empowercast_feed['process_podpress'] = !empty($GeneralSettings['process_podpress']); // We don't touch podpress data for custom feeds
					$empowercast_feed['default_url'] = '';
					if( !empty($GeneralSettings['default_url']) )
						$empowercast_feed['default_url'] = rtrim($GeneralSettings['default_url'], '/') .'/';
                    // switching from 'not set' 'yes' 'clean' to 'true' 'false'--for backwards compatibility, 'not set' will now be 'false'
                    $explicit = array("false", "true", "false");
					$empowercast_feed['explicit'] = 'false';
					if( !empty($FeedSettingsBasic['itunes_explicit']) )
						$empowercast_feed['explicit'] = $explicit[$FeedSettingsBasic['itunes_explicit']];
					if( !empty($FeedSettingsBasic['itunes_talent_name']) )
						$empowercast_feed['itunes_talent_name'] = $FeedSettingsBasic['itunes_talent_name'];
					else
						$empowercast_feed['itunes_talent_name'] = get_wp_title_rss();
					$empowercast_feed['enhance_itunes_summary'] = 0;
					if( isset($FeedSettingsBasic['enhance_itunes_summary']) )
						$empowercast_feed['enhance_itunes_summary'] = $FeedSettingsBasic['enhance_itunes_summary'];
					if( !empty($GeneralSettings['seo_itunes']) )
						$empowercast_feed['enhance_itunes_summary'] = 1;
					$empowercast_feed['posts_per_rss'] = false;
					if( !empty($FeedSettingsBasic['posts_per_rss']) && is_numeric($FeedSettingsBasic['posts_per_rss']) && $FeedSettingsBasic['posts_per_rss'] > 0 )
						$empowercast_feed['posts_per_rss'] = $FeedSettingsBasic['posts_per_rss'];
					if( !empty($FeedSettingsBasic['itunes_author_post']) )
						$empowercast_feed['itunes_author_post'] = true;
					$empowercast_feed['rss_language'] = ''; // Cannot set the language setting in simple mode
					if( !empty($GeneralSettings['podcast_embed_in_feed']) )
						$empowercast_feed['podcast_embed_in_feed'] = true;
					if( !empty($FeedSettingsBasic['episode_itunes_image']) && !empty($FeedSettingsBasic['itunes_image']) )
						$empowercast_feed['itunes_image'] = $FeedSettingsBasic['itunes_image'];

				}; break;
				// All other cases we let fall through
			}
		}
	}
}

// Returns true of the feed should be treated as a podcast feed
function empowercast_is_podcast_feed()
{
	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return false; // Another podcasting plugin is enabled...

	global $empowercast_feed;
	if( $empowercast_feed !== false && !is_array($empowercast_feed) )
		empowercast_load_general_feed_settings();
	if( $empowercast_feed === false )
		return false;
	return true;
}

// Returns true if the feed is a custom feed added by EmpowerCast
function empowercast_is_custom_podcast_feed()
{
	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return false; // Another podcasting plugin is enabled...

	global $empowercast_feed;
	if( $empowercast_feed !== false && !is_array($empowercast_feed) )
		empowercast_load_general_feed_settings();
	if( $empowercast_feed === false )
		return false;
	return $empowercast_feed['is_custom'];
}

function empowercast_posts_fields($cols)
{
	if( !is_feed() )
		return $cols;

	if( is_category() || is_tag() || is_tax() ) {
		if( get_query_var('feed') !== 'podcast' )
			return $cols;
	}

	if( empowercast_is_custom_podcast_feed() || get_query_var('feed') === 'podcast' )
	{
		if( !empty($GLOBALS['empowercast_feed']['feed_accel']) )
		{
			$feed_slug = get_query_var('feed');
			global $wpdb;
			$cols .= ", pp_{$wpdb->postmeta}.meta_value AS podcast_meta_value ";
		}
	}

  return $cols;
}
//$fields = apply_filters_ref_array( 'posts_fields', array( $fields, &$this ) );
add_filter('posts_fields', 'empowercast_posts_fields' );

function empowercast_posts_join($join)
{
	if( !is_feed() )
		return $join;

	if( is_category() || is_tag() || is_tax() ) {
		if( get_query_var('feed') !== 'podcast' )
			return $join;
	}

	if( empowercast_is_custom_podcast_feed() || get_query_var('feed') === 'podcast' )
	{
		global $wpdb;
		$join .= " INNER JOIN {$wpdb->postmeta} AS pp_{$wpdb->postmeta} ";
		$join .= " ON {$wpdb->posts}.ID = pp_{$wpdb->postmeta}.post_id ";
	}

  return $join;
}

add_filter('posts_join', 'empowercast_posts_join' );

function empowercast_posts_where($where)
{
	if( !is_feed() )
		return $where;
	if( is_category() || is_tag() || is_tax() ) {
		if( get_query_var('feed') !== 'podcast' )
			return $where;
	}

	if( empowercast_is_custom_podcast_feed() || get_query_var('feed') === 'podcast' )
	{
		global $wpdb, $empowercast_feed;
		$where .= " AND (";

		if( empowercast_is_custom_podcast_feed() && get_query_var('feed') !== 'podcast' )
			$where .= " pp_{$wpdb->postmeta}.meta_key = '_". get_query_var('feed') .":enclosure' AND pp_{$wpdb->postmeta}.meta_value NOT LIKE 'no%' ";
		else
			$where .= " pp_{$wpdb->postmeta}.meta_key = 'enclosure' AND pp_{$wpdb->postmeta}.meta_value NOT LIKE 'no%' ";

		// Include Podpress data if exists...
		if( !empty($empowercast_feed['process_podpress']) && get_query_var('feed') === 'podcast' )
			$where .= " OR pp_{$wpdb->postmeta}.meta_key = 'podPressMedia' OR pp_{$wpdb->postmeta}.meta_key = '_podPressMedia' ";

		$where .= ") ";
	}
	return $where;
}

add_filter('posts_where', 'empowercast_posts_where' );

// Add the groupby needed for enclosures only
function empowercast_posts_groupby($groupby)
{
	if( !is_feed() )
		return $groupby;

	if( is_category() || is_tag() || is_tax() ) {
		if( get_query_var('feed') !== 'podcast' )
			return $groupby;
	}

	if( empowercast_is_custom_podcast_feed() || get_query_var('feed') === 'podcast' )
	{
		global $wpdb;
		$groupby = " {$wpdb->posts}.ID ";
	}
	return $groupby;
}
add_filter('posts_groupby', 'empowercast_posts_groupby');

function empowercast_post_limits($limits)
{
	if( !is_feed() )
		return $limits;

	if( empowercast_is_custom_podcast_feed() || get_query_var('feed') === 'podcast' )
	{
		global $empowercast_feed;
		if( !empty($empowercast_feed['posts_per_rss']) && preg_match('/^(\d)+$/', trim($empowercast_feed['posts_per_rss'])) )
			$limits = "LIMIT 0, {$empowercast_feed['posts_per_rss']}";
	}
	return $limits;
}
add_filter('post_limits', 'empowercast_post_limits');


function empowercast_do_all_pings()
{
	global $wpdb;
	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_encloseme' ");

	// Now call the WordPress do_all_pings()...
	do_all_pings();
	remove_action('do_pings', 'do_all_pings');
}

remove_action('do_pings', 'do_all_pings');
add_action('do_pings', 'empowercast_do_all_pings', 1, 1);

/*
Helper functions:
*/
function empowercast_podpress_redirect_check()
{
	if( preg_match('/podpress_trac\/([^\/]+)\/([^\/]+)\/([^\/]+)\/(.*)$/', $_SERVER['REQUEST_URI'], $matches) )
	{
		$post_id = $matches[2];
		$mediaNum = $matches[3];
		//$filename = $matches[4];
		//$method = $matches[1];

		if( is_numeric($post_id) && is_numeric($mediaNum))
		{
			$EpisodeData = empowercast_get_enclosure_data_podpress($post_id, $mediaNum);
			if( $EpisodeData && isset($EpisodeData['url']) )
			{
				if( strpos($EpisodeData['url'], 'http://' ) !== 0 && strpos($EpisodeData['url'], 'https://' ) !== 0 )
				{
					die('Error occurred obtaining the URL for the requested media file.');
					exit;
				}

				$EnclosureURL = str_replace(' ', '%20', $EpisodeData['url']);
				header('Location: '.$EnclosureURL, true, 302);
				header('Content-Length: 0');
				exit;
			}
			// Let the WordPress 404 page load as normal
		}
	}
}

function the_empowercast_content()
{
	echo get_the_empowercast_content();
}

function get_the_empowercast_content()
{
	global $post;

	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		return '';

	if( function_exists('post_password_required') )
	{
		if( post_password_required($post) )
			return '';
	}

	// EmpowerCast settings:
	$GeneralSettings = get_option('empowercast_general');

	// No player or links to add to content...
	if( !empty($GeneralSettings['disable_appearance']) )
		return $content;

	if( !isset($GeneralSettings['custom_feeds']) )
    $GeneralSettings['custom_feeds'] = array('podcast'=>'Default Podcast Feed');

	// Re-order so the default podcast episode is the top most...
	$Temp = $GeneralSettings['custom_feeds'];
	$GeneralSettings['custom_feeds'] = array();
	$GeneralSettings['custom_feeds']['podcast'] = 'Default Podcast Feed';

	if (is_array($Temp)){
        foreach ($Temp as $feed_slug => $feed_title) {
            if ($feed_slug == 'podcast')
                continue;
            $GeneralSettings['custom_feeds'][$feed_slug] = $feed_title;
        }
    }
	// Handle post type feeds....
	if( !empty($GeneralSettings['posttype_podcasting']) )
	{
		$post_type = get_query_var('post_type');
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type ); // get first element in array
		}

		// Get the feed slugs and titles for this post type
		$PostTypeSettingsArray = get_option('empowercast_posttype_'.$post_type);
		// Loop through this array of post type settings...
		if( !empty($PostTypeSettingsArray) )
		{
			switch($post_type)
			{
				case 'post':
				case 'page': {
					// Do nothing!, we want the default podcast to appear in these post types
				}; break;
				default: {
					if( !empty($post_type) && empty($PostTypeSettingsArray['podcast']) )
						unset($GeneralSettings['custom_feeds']['podcast']); // special case, we do not want an accidental podcast episode to appear in a custom post type if the feature is enabled
				}; break;
			}

            if (is_array($PostTypeSettingsArray)) {
                foreach ($PostTypeSettingsArray as $feed_slug => $postTypeSettings) {
                    if (!empty($postTypeSettings['title']))
                        $GeneralSettings['custom_feeds'][$feed_slug] = $postTypeSettings['title'];
                    else
                        $GeneralSettings['custom_feeds'][$feed_slug] = $feed_slug;
                }
            }
		}
	}

	if( !isset($GeneralSettings['display_player']) )
			$GeneralSettings['display_player'] = 1;
	if( !isset($GeneralSettings['player_function']) )
		$GeneralSettings['player_function'] = 1;
	if( !isset($GeneralSettings['podcast_link']) )
		$GeneralSettings['podcast_link'] = 1;

	// Figure out which players are alerady in the body of the page...
	$ExcludePlayers = array();
	if( isset($GeneralSettings['disable_player']) )
		$ExcludePlayers = $GeneralSettings['disable_player']; // automatically disable the players configured

	// LOOP HERE TO DISPLAY EACH MEDIA TYPE
	$new_content = '';
	foreach( $GeneralSettings['custom_feeds'] as $feed_slug=> $feed_title )
	{
		// Get the enclosure data
		$EpisodeData = empowercast_get_enclosure_data($post->ID, $feed_slug);

		if( !$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast' )
			$EpisodeData = empowercast_get_enclosure_data_podpress($post->ID);

		if( !$EpisodeData || !$EpisodeData['url'] )
			continue;

		// Just in case, if there's no URL lets escape!
		if( !$EpisodeData['url'] )
			continue;

		// If the player is not already inserted in the body of the post using the shortcode...
		//if( preg_match('/\[empowercast(.*)\]/is', $content) == 0 )
		if( !isset($ExcludePlayers[ $feed_slug ]) ) // If the player is not in our exclude list because it's already in the post body somewhere...
		{
			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !empowercast_premium_content_authorized($feed_slug) )
			{
				$new_content .=  empowercast_premium_content_message($post->ID, $feed_slug, $EpisodeData);
			}
			else
			{
				if( $GeneralSettings['player_function'] != 3 && $GeneralSettings['player_function'] != 0 ) // Play in new window only or disabled
				{
					do_action('wp_empowercast_player_scripts');
					$AddDefaultPlayer = empty($EpisodeData['no_player']);

					if( $EpisodeData && !empty($EpisodeData['embed']) )
					{
						$new_content .=  trim($EpisodeData['embed']);
						if( !empty($GeneralSettings['embed_replace_player']) )
							$AddDefaultPlayer = false;
					}

					if( $AddDefaultPlayer )
					{
						$image = '';
						$width = '';
						$height = '';
						if( isset($EpisodeData['image']) && $EpisodeData['image'] != '' )
							$image = $EpisodeData['image'];
						if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
							$width = $EpisodeData['width'];
						if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
							$height = $EpisodeData['height'];

						$new_content .= apply_filters('empowercast_player', '', empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
					}
				}

				if( !isset($EpisodeData['no_links']) )
				{
					do_action('wp_empowercast_player_scripts');
					$new_content .= apply_filters('empowercast_player_links', '',  empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
					$new_content .= apply_filters('empowercast_player_subscribe_links', '',  empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
				}
			}
		}
	}

	return $new_content;
}



// Adds content types that are missing from the default wp_check_filetype function
function empowercast_get_contenttype($file, $use_wp_check_filetype = true)
{
	$parts = pathinfo($file);
	if( !empty($parts['extension']) )
	{
		switch( strtolower($parts['extension']) )
		{
			// Audio formats
			case 'mp3': // most common
			case 'mpga':
			case 'mp2':
			case 'mp2a':
			case 'm2a':
			case 'm3a':
				return 'audio/mpeg';
			case 'm4a':
				return 'audio/x-m4a';
			case 'm4b': // Audio book format
				return 'audio/m4b';
			case 'm4r': // iPhone ringtone format
				return 'audio/m4r';
			// OGG Internet content types as set forth by rfc5334 (http://tools.ietf.org/html/rfc5334)
			case 'opus':
			case 'oga':
			case 'spx':
				return 'audio/ogg';
			case 'wma':
				return 'audio/x-ms-wma';
			case 'wax':
				return 'audio/x-ms-wax';
			case 'ra':
			case 'ram':
				return 'audio/x-pn-realaudio';
			case 'mp4a':
				return 'audio/mp4';
            case 'aac':
                return 'audio/aac';

			// Video formats
			case 'm4v':
				return 'video/x-m4v';
			case 'mpeg':
			case 'mpg':
			case 'mpe':
			case 'm1v':
			case 'm2v':
				return 'video/mpeg';
			case 'mp4':
			case 'mp4v':
			case 'mpg4':
				return 'video/mp4';
			case 'asf':
			case 'asx':
				return 'video/x-ms-asf';
			case 'wmx':
				return 'video/x-ms-wmx';
			case 'avi':
				return 'video/x-msvideo';
			case 'wmv':
				return 'video/x-ms-wmv'; // Check this
			case 'flv':
				return 'video/x-flv';
			case 'mov':
			case 'qt':
				return 'video/quicktime';
			case 'divx':
				return 'video/divx';
			case '3gp':
				return 'video/3gpp';
			case 'webm':
				return 'video/webm';
			case 'ogg': {
				if( !defined('EMPOWERCAST_OGG_VIDEO') )
					return 'audio/ogg';
			} // Let this fall through as ogg/video
			case 'ogv':
				return 'video/ogg';

			// rarely used
			case 'mid':
			case 'midi':
				return 'audio/midi';
			case 'wav':
				return 'audio/wav';
			case 'aa':
				return 'audio/audible';
			case 'pdf':
				return 'application/pdf';
			case 'torrent':
				return 'application/x-bittorrent';
			case 'swf':
				return 'application/x-shockwave-flash';
			case 'ogx':
				return 'application/ogg';

			// Most recently added by Apple:
			case 'epub':
				return 'document/x-epub';

			// Content type for transcript files
            case 'srt':
                return 'application/srt';
            case 'json':
                return 'application/json';
            case 'html':
                return 'text/html';

			default: // Let it fall through
		}
	}

	// Last case let wordpress detect it:
	if( $use_wp_check_filetype )
	{
		$FileType = wp_check_filetype($file);
		if( $FileType && isset($FileType['type']) )
			return $FileType['type'];
	}
	return '';
}


function empowercast_itunes_categories($PrefixSubCategories = false)
{
	$temp = array();
	$temp['01-00'] = 'Arts';
		$temp['01-01'] = 'Design';
		$temp['01-02'] = 'Fashion & Beauty';
		$temp['01-03'] = 'Food';
		$temp['01-04'] = 'Literature';
		$temp['01-05'] = 'Performing Arts';
		$temp['01-06'] = 'Visual Arts';

	$temp['02-00'] = 'Business';
		$temp['02-01'] = 'Business News';
		$temp['02-02'] = 'Careers';
		$temp['02-03'] = 'Investing';
		$temp['02-04'] = 'Management & Marketing';
		$temp['02-05'] = 'Shopping';

	$temp['03-00'] = 'Comedy';

	$temp['04-00'] = 'Education';
		$temp['04-01'] = 'Education Technology';
		$temp['04-02'] = 'Higher Education';
		$temp['04-03'] = 'K-12';
		$temp['04-04'] = 'Language Courses';
		$temp['04-05'] = 'Training';

	$temp['05-00'] = 'Games & Hobbies';
		$temp['05-01'] = 'Automotive';
		$temp['05-02'] = 'Aviation';
		$temp['05-03'] = 'Hobbies';
		$temp['05-04'] = 'Other Games';
		$temp['05-05'] = 'Video Games';

	$temp['06-00'] = 'Government & Organizations';
		$temp['06-01'] = 'Local';
		$temp['06-02'] = 'National';
		$temp['06-03'] = 'Non-Profit';
		$temp['06-04'] = 'Regional';

	$temp['07-00'] = 'Health';
		$temp['07-01'] = 'Alternative Health';
		$temp['07-02'] = 'Fitness & Nutrition';
		$temp['07-03'] = 'Self-Help';
		$temp['07-04'] = 'Sexuality';

	$temp['08-00'] = 'Kids & Family';

	$temp['09-00'] = 'Music';

	$temp['10-00'] = 'News & Politics';

	$temp['11-00'] = 'Religion & Spirituality';
		$temp['11-01'] = 'Buddhism';
		$temp['11-02'] = 'Christianity';
		$temp['11-03'] = 'Hinduism';
		$temp['11-04'] = 'Islam';
		$temp['11-05'] = 'Judaism';
		$temp['11-06'] = 'Other';
		$temp['11-07'] = 'Spirituality';

	$temp['12-00'] = 'Science & Medicine';
		$temp['12-01'] = 'Medicine';
		$temp['12-02'] = 'Natural Sciences';
		$temp['12-03'] = 'Social Sciences';

	$temp['13-00'] = 'Society & Culture';
		$temp['13-01'] = 'History';
		$temp['13-02'] = 'Personal Journals';
		$temp['13-03'] = 'Philosophy';
		$temp['13-04'] = 'Places & Travel';

	$temp['14-00'] = 'Sports & Recreation';
		$temp['14-01'] = 'Amateur';
		$temp['14-02'] = 'College & High School';
		$temp['14-03'] = 'Outdoor';
		$temp['14-04'] = 'Professional';

	$temp['15-00'] = 'Technology';
		$temp['15-01'] = 'Gadgets';
		$temp['15-02'] = 'Tech News';
		$temp['15-03'] = 'Podcasting';
		$temp['15-04'] = 'Software How-To';

	$temp['16-00'] = 'TV & Film';

	if( $PrefixSubCategories )
	{
		foreach( $temp as $key=> $val )
		{
			$parts = explode('-', $key);
			$cat = $parts[0];
			$subcat = $parts[1];

			if( $subcat != '00' )
				$temp[$key] = $temp[$cat.'-00'].' > '.$val;
		}
		reset($temp);
	}

	return $temp;
}

/**
 * Categories for 2019+ Apple Podcast directory
 */
function empowercast_apple_categories($PrefixSubCategories = false) {
    $temp = array();
    $temp['01-00'] = 'Arts';
    $temp['01-01'] = 'Books';
    $temp['01-02'] = 'Design';
    $temp['01-03'] = 'Fashion & Beauty';
    $temp['01-04'] = 'Food';
    $temp['01-05'] = 'Performing Arts';
    $temp['01-06'] = 'Visual Arts';

    $temp['02-00'] = 'Business';
    $temp['02-01'] = 'Careers';
    $temp['02-02'] = 'Entrepreneurship';
    $temp['02-03'] = 'Investing';
    $temp['02-04'] = 'Management';
    $temp['02-05'] = 'Marketing';
    $temp['02-06'] = 'Non-Profit';

    $temp['03-00'] = 'Comedy';
    $temp['03-01'] = 'Comedy Interviews';
    $temp['03-02'] = 'Improv';
    $temp['03-03'] = 'Stand-Up';

    $temp['04-00'] = 'Education';
    $temp['04-01'] = 'Courses';
    $temp['04-02'] = 'How To';
    $temp['04-03'] = 'Language Learning';
    $temp['04-04'] = 'Self-Improvement';

    $temp['05-00'] = 'Fiction';
    $temp['05-01'] = 'Comedy Fiction';
    $temp['05-02'] = 'Drama';
    $temp['05-03'] = 'Science Fiction';

    $temp['06-00'] = 'Government';

    $temp['07-00'] = 'Health & Fitness';
    $temp['07-01'] = 'Alternative Health';
    $temp['07-02'] = 'Fitness';
    $temp['07-03'] = 'Medicine';
    $temp['07-04'] = 'Mental Health';
    $temp['07-05'] = 'Nutrition';
    $temp['07-06'] = 'Sexuality';

    $temp['08-00'] = 'History';

    $temp['09-00'] = 'Kids & Family';
    $temp['09-01'] = 'Education for Kids';
    $temp['09-02'] = 'Parenting';
    $temp['09-03'] = 'Pets & Animals';
    $temp['09-04'] = 'Stories for Kids';

    $temp['10-00'] = 'Leisure';
    $temp['10-01'] = 'Animation & Manga';
    $temp['10-02'] = 'Automotive';
    $temp['10-03'] = 'Aviation';
    $temp['10-04'] = 'Crafts';
    $temp['10-05'] = 'Games';
    $temp['10-06'] = 'Hobbies';
    $temp['10-07'] = 'Home & Garden';
    $temp['10-08'] = 'Video Games';

    $temp['11-00'] = 'Music';
    $temp['11-01'] = 'Music Commentary';
    $temp['11-02'] = 'Music History';
    $temp['11-03'] = 'Music Interviews';

    $temp['12-00'] = 'News';
    $temp['12-01'] = 'Business News';
    $temp['12-02'] = 'Daily News';
    $temp['12-03'] = 'Entertainment News';
    $temp['12-04'] = 'News Commentary';
    $temp['12-05'] = 'Politics';
    $temp['12-06'] = 'Sports News';
    $temp['12-07'] = 'Tech News';

    $temp['13-00'] = 'Religion & Spirituality';
    $temp['13-01'] = 'Buddhism';
    $temp['13-02'] = 'Christianity';
    $temp['13-03'] = 'Hinduism';
    $temp['13-04'] = 'Islam';
    $temp['13-05'] = 'Judaism';
    $temp['13-06'] = 'Religion';
    $temp['13-07'] = 'Spirituality';

    $temp['14-00'] = 'Science';
    $temp['14-01'] = 'Astronomy';
    $temp['14-02'] = 'Chemistry';
    $temp['14-03'] = 'Earth Sciences';
    $temp['14-04'] = 'Life Sciences';
    $temp['14-05'] = 'Mathematics';
    $temp['14-06'] = 'Natural Sciences';
    $temp['14-07'] = 'Nature';
    $temp['14-08'] = 'Physics';
    $temp['14-09'] = 'Social Sciences';

    $temp['15-00'] = 'Society & Culture';
    $temp['15-01'] = 'Documentary';
    $temp['15-02'] = 'Personal Journals';
    $temp['15-03'] = 'Philosophy';
    $temp['15-04'] = 'Places & Travel';
    $temp['15-06'] = 'Relationships';

    $temp['16-00'] = 'Sports';
    $temp['16-01'] = 'Baseball';
    $temp['16-02'] = 'Basketball';
    $temp['16-03'] = 'Cricket';
    $temp['16-04'] = 'Fantasy Sports';
    $temp['16-05'] = 'Football';
    $temp['16-06'] = 'Golf';
    $temp['16-07'] = 'Hockey';
    $temp['16-08'] = 'Rugby';
    $temp['16-09'] = 'Running';
    $temp['16-10'] = 'Soccer';
    $temp['16-11'] = 'Swimming';
    $temp['16-12'] = 'Tennis';
    $temp['16-13'] = 'Volleyball';
    $temp['16-15'] = 'Wilderness';
    $temp['16-16'] = 'Wrestling';

    $temp['17-00'] = 'Technology';

    $temp['18-00'] = 'True Crime';

    $temp['19-00'] = 'TV & Film';
    $temp['19-01'] = 'After Shows';
    $temp['19-02'] = 'Film History';
    $temp['19-03'] = 'Film Interviews';
    $temp['19-04'] = 'Film Reviews';
    $temp['19-05'] = 'TV Reviews';

    if( $PrefixSubCategories )
    {
        foreach( $temp as $key=> $val )
        {
            $parts = explode('-', $key);
            $cat = $parts[0];
            $subcat = $parts[1];

            if( $subcat != '00' )
                $temp[$key] = $temp[$cat.'-00'].' > '.$val;
        }
        reset($temp);
    }

    return $temp;
}

function empowercast_googleplay_categories()
{
	$temp = array();
	$temp['01-00'] = 'Arts';
	$temp['02-00'] = 'Business';
	$temp['03-00'] = 'Comedy';
	$temp['04-00'] = 'Education';
	$temp['05-00'] = 'Games & Hobbies';
	$temp['06-00'] = 'Government & Organizations';
	$temp['07-00'] = 'Health';
	$temp['08-00'] = 'Kids & Family';
	$temp['09-00'] = 'Music';
	$temp['10-00'] = 'News & Politics';
	$temp['11-00'] = 'Religion & Spirituality';
	$temp['12-00'] = 'Science & Medicine';
	$temp['13-00'] = 'Society & Culture';
	$temp['14-00'] = 'Sports & Recreation';
	$temp['15-00'] = 'Technology';
	$temp['16-00'] = 'TV & Film';

	return $temp;
}

function empowercast_get_root_url()
{
	/*
	// OLD CODE:
	$empowercast_dirname = basename( EMPOWERCAST_ABSPATH );
	return WP_PLUGIN_URL . '/'. $empowercast_dirname .'/';
	*/
	$local_path = __FILE__;
	if( DIRECTORY_SEPARATOR == '\\' ) { // Win32 fix
		$local_path = basename(dirname(__FILE__)) .'/'. basename(__FILE__);
	}
	$plugin_url = plugins_url('', $local_path);
	return $plugin_url . '/';
}

function empowercast_get_the_exerpt($for_summary = false, $no_filters = false, $post_id = false)
{
	if( $no_filters ) {
		if( $post_id > 0 ) {
			$post = get_post($post_id);
			$subtitle = $post->post_excerpt;
			if ( $subtitle == '') {

				$subtitle = $post->post_content;
				$shortcodesTemp = $GLOBALS['shortcode_tags'];
				$GLOBALS['shortcode_tags']['skipto'] = 'empowercast_shortcode_skipto';
				$subtitle = do_shortcode($subtitle);
				$GLOBALS['shortcode_tags'] = $shortcodesTemp;

				$subtitle = strip_shortcodes( $subtitle );
				$subtitle = str_replace(']]>', ']]&gt;', $subtitle);
				$subtitle = strip_tags($subtitle);
			}
		}
		else if( is_object($GLOBALS['post']) )
		{
			$subtitle = $GLOBALS['post']->post_excerpt;
			if ( $subtitle == '') {

				$subtitle = $GLOBALS['post']->post_content;

				$shortcodesTemp = $GLOBALS['shortcode_tags'];
				$GLOBALS['shortcode_tags']['skipto'] = 'empowercast_shortcode_skipto';
				$subtitle = do_shortcode($subtitle);
				$GLOBALS['shortcode_tags'] = $shortcodesTemp;

				$subtitle = strip_shortcodes( $subtitle );
				$subtitle = str_replace(']]>', ']]&gt;', $subtitle);
				$subtitle = strip_tags($subtitle);
			}
		}
	} else {
		$subtitle = get_the_excerpt();
	}

	$subtitle = trim( strip_tags( $subtitle ) );
	if( !empty($subtitle) )
		return $subtitle;
	return empowercast_get_the_content( $for_summary, $no_filters );
}

function empowercast_get_the_content($for_summary = true, $no_filters = false, $no_strip_tags = false) {
	if( $no_filters ) {
		global $post;
		$content_no_html = $post->post_content;

		$shortcodesTemp = $GLOBALS['shortcode_tags'];
		$GLOBALS['shortcode_tags']['skipto'] = 'empowercast_shortcode_skipto';
		$content_no_html = do_shortcode($content_no_html);
		$GLOBALS['shortcode_tags'] = $shortcodesTemp;

		//$content_no_html = strip_shortcodes( $content_no_html );
		$content_no_html = str_replace(']]>', ']]&gt;', $content_no_html);
		$content_no_html = wp_staticize_emoji( _oembed_filter_feed_content( $content_no_html ) );
	} else {
		$content_no_html = get_the_content();
	}

	$content_no_html = strip_shortcodes( $content_no_html );
	if( $no_strip_tags )
		return $content_no_html;

	if( $for_summary ) {
		return trim( strip_tags($content_no_html, '<a><p><br><ul><li>') );
	}
	return trim( strip_tags($content_no_html) );
}

function empowercast_enhanced_itunes_summary($no_filters = false)
{
	if( $no_filters ) {
		$summary = empowercast_get_the_content(false, true, true);
	} else {
		$summary = apply_filters( 'the_content', empowercast_get_the_content(false, $no_filters, true) );
	}
	$summary = str_replace("<li>", '<li>* ', $summary); // Make sure our bullet lists stay nicely formatted.
	$summary = strip_tags($summary, '<a>'); // We can leave a tags for itunes:summary, this will also strip CDATA tags
	return $summary;
}

function empowercast_url_in_feed($url) {
	if( defined('EMPOWERCAST_FEEDS_FORCE_HTTP') && is_feed() ) {
		if( preg_match('/^https:\/\/(.*)$/', $url, $matches) ) {
			return 'http://'.$matches[1];
		}
	}
	else if( defined('EMPOWERCAST_FEEDS_FORCE_HTTPS') && is_feed() ) {
		if( preg_match('/^http:\/\/(.*)$/', $url, $matches) ) {
			return 'https://'.$matches[1];
		}
	}
	return $url;
}

function empowercast_format_itunes_value($value, $tag, $cdata=false)
{
	if( $tag == 'summary' )
		$value = nl2br($value); // Does not remove existing br tags if present.

	if( $cdata ) {
		$value = str_replace(']]>', ']]&gt;', $value);
		return empowercast_trim_itunes_value($value, $tag);
	}

	if( !defined('EMPOWERCAST_DISABLE_ITUNES_UTF8') || EMPOWERCAST_DISABLE_ITUNES_UTF8 == false ) // If not defined or it is false
	{
		global $wpdb;
		switch( $wpdb->charset )
		{
			case 'utf8': break;
			case 'utf8mb3': break;
			case 'utf8mb4': break;
			default: {

				// preg_match fails when it encounters invalid UTF8 in $string
				if ( 1 !== @preg_match( '/^./us', $value ) ) {
					$value = utf8_encode($value); // If it is not, convert to UTF-8 then decode it...
				}
			}
		}
	}

	// Code added to solve issue with KimiliFlashEmbed plugin and also remove the shortcode for the WP Audio Player
	// 99.9% of the time this code will not be necessary
	$value = preg_replace("/\[(kml_(flash|swf)embed|audio\:)\b(.*?)(?:(\/))?(\]|$)/isu", '', $value);
	$value = @html_entity_decode($value, ENT_COMPAT, 'UTF-8'); // Remove any additional entities such as &nbsp;
	$value = preg_replace( '/&amp;/ui' , '&', $value); // Precaution in case it didn't get removed from function above.

	return esc_html( empowercast_trim_itunes_value($value, $tag) );
}

function empowercast_trim_itunes_value($value, $tag = 'summary')
{
	$value = trim($value); // First we need to trim the string
	$length = (function_exists('mb_strlen')?mb_strlen($value):strlen($value) );
	$trim_at = false;
	$remove_new_lines = false;

	switch($tag)
	{
		case 'description':
		case 'summary': {
			// 4000 character limit
			if( $length > 4000 )
				$trim_at = 3997; // 3 less characters so we can add a dot dot dot
		}; break;
		case 'subtitle':
		case 'author':
		case 'name':
		default: {
			$remove_new_lines = true;
			// 255 character limit
			if( $length > 255 )
				$trim_at = 252; // Allow 3 dots to be added after the trim
		};
	}

	if( $trim_at )
	{
		// Start trimming
		$value = (function_exists('mb_substr')?mb_substr($value, 0, $trim_at):substr($value, 0, $trim_at) );
		$clean_break = false;
		if( preg_match('/(.*[,\n.\?!])[^,\n.\?!]/isu', $value, $matches) ) // pattern modifiers: case (i)nsensitive, entire (s)tring and (u)nicode
		{
			if( isset( $matches[1]) )
			{
				$detected_eof_pos = (function_exists('mb_strlen')?mb_strlen($matches[1]):strlen($matches[1]) );
				// Look back at most 50 characters...
				if( $detected_eof_pos > 3950 || ($detected_eof_pos > 205 && $detected_eof_pos < 255 ) )
				{
					$value = $matches[1];
					$clean_break = true;
				}
				// Otherwise we want to continue with the same value we started with...
			}
		}

		if( $clean_break == false && $tag = 'subtitle' ) // Subtitle we want to add a ... at the end
		{
			if( $trim_at )
				$value = (function_exists('mb_substr')?mb_substr($value, 0, $trim_at):substr($value, 0, $trim_at) ). '...';
		}
	}

	if( $remove_new_lines )
		$value = str_replace( array("\r\n\r\n", "\n", "\r", "\t","-  "), array(' - ',' ', '', ' ', ''), $value );

	return $value;
}

function empowercast_add_redirect_url($MediaURL, $EpisodeData = false) // $channel = 'podcast')
{
	if( preg_match('/^https?:\/\//i', $MediaURL) == 0 )
		return $MediaURL; // If the user is hosting media not via http (e.g. ftp) then we can't handle the redirect

	if( !is_array($EpisodeData) )
	{
		$feed_slug = '';
		if( is_string($EpisodeData) && !empty($EpisodeData) ) {
			$feed_slug = $EpisodeData;
		}

		$EpisodeData = array();
		if( !empty($feed_slug) )
			$EpisodeData['feed'] = $EpisodeData;
	}

	if( empty($EpisodeData['feed']) )
		$EpisodeData['feed'] = 'podcast';

	$NewURL = apply_filters( 'empowercast_redirect_url',  $MediaURL, $EpisodeData );

	$URLScheme = ( (preg_match('/^https:\/\//i', $NewURL) != 0 ) ? 'https://':'http://');

	$GeneralSettings = get_option('empowercast_general');
	$Redirects = array('redirect0'=>'', 'redirect1'=>'', 'redirect2'=>'', 'redirect3'=>'');
	if( !empty($GeneralSettings['redirect1']) )
		$Redirects['redirect1'] = $GeneralSettings['redirect1'];
	if( !empty($GeneralSettings['redirect2']) )
		$Redirects['redirect2'] = $GeneralSettings['redirect2'];
	if( !empty($GeneralSettings['redirect3']) )
		$Redirects['redirect3'] = $GeneralSettings['redirect3'];

	if( !empty($GeneralSettings['cat_casting']) ) { // If category podcasting...

		if( !empty($EpisodeData['category']) ) {

			$FeedCatSettings = get_option('empowercast_cat_feed_'.$EpisodeData['category'] );
			if( !empty($FeedCatSettings['redirect']) ) {
				$Redirects['redirect0'] = $FeedCatSettings['redirect'];
				$Redirects['redirect1'] = '';
				$Redirects['redirect2'] = '';
				$Redirects['redirect3'] = '';
			}
			if( !empty($FeedCatSettings['redirect2']) ) {
				$Redirects['redirect1'] = $FeedCatSettings['redirect2'];
			}
		} else { // Use the old way

			if( is_category() ) { // Special case where we want to track the category separately
				$FeedCatSettings = get_option('empowercast_cat_feed_'.get_query_var('cat') );
				if( $FeedCatSettings && !empty($FeedCatSettings['redirect']) ) {
					$Redirects['redirect0'] = $FeedCatSettings['redirect'];
					$Redirects['redirect1'] = '';
					$Redirects['redirect2'] = '';
					$Redirects['redirect3'] = '';
					if( !empty($FeedCatSettings['redirect2']) ) {
						$Redirects['redirect1'] = $FeedCatSettings['redirect2'];
					}
				}
			} else if( is_single() ) {
				$categories = wp_get_post_categories( get_the_ID() );
				if( count($categories) == 1 ) { // See if only one category is associated with this post
					foreach( $categories as $null=> $cat_id ) {
						break;
					}
					$FeedCatSettings = get_option('empowercast_cat_feed_'.$cat_id );
					if( $FeedCatSettings && !empty($FeedCatSettings['redirect']) ) {
						$Redirects['redirect0'] = $FeedCatSettings['redirect'];
						$Redirects['redirect1'] = '';
						$Redirects['redirect2'] = '';
						$Redirects['redirect3'] = '';
						if( !empty($FeedCatSettings['redirect2']) ) {
							$Redirects['redirect1'] = $FeedCatSettings['redirect2'];
						}
					}
				}
			}
		}
	}

	//custom_feeds
	if( !empty($GeneralSettings['channels']) ) {

		$FeedSettings = get_option('empowercast_feed_'. $EpisodeData['feed']);
		if( !empty($FeedSettings['redirect']) )
		{
			// Override the redirect
			$Redirects['redirect0'] = $FeedSettings['redirect'];
			$Redirects['redirect1'] = '';
			$Redirects['redirect2'] = '';
			$Redirects['redirect3'] = '';
		}
		if( !empty($FeedSettings['redirect2']) ) {
			$Redirects['redirect1'] = $FeedSettings['redirect2'];
		}
	}

	if( !empty($GeneralSettings['posttype_podcasting']) )  // Post Type Podcasting
	{
		$post_type = get_post_type();
		switch($post_type) {
			case 'post':
			case 'page': {
				// Do nothing!, we want the default podcast and channels to appear in these post types
			}; break;
			default: {
				$PostTypeSettingsArray = get_option('empowercast_posttype_'.$post_type);

				// We found a post type statsitics tracking
				if( !empty($PostTypeSettingsArray[ $EpisodeData['feed'] ]['redirect']) )
				{
					$Redirects['redirect0'] = $PostTypeSettingsArray[ $EpisodeData['feed'] ]['redirect'];
					$Redirects['redirect1'] = '';
					$Redirects['redirect2'] = '';
					$Redirects['redirect3'] = '';
				}
				if( !empty($PostTypeSettingsArray[ $EpisodeData['feed'] ]['redirect2']) ) {
					$Redirects['redirect1'] = $PostTypeSettingsArray[ $EpisodeData['feed'] ]['redirect2'];
				}
			};
		}
	}

	if( version_compare($GLOBALS['wp_version'], 4.5, '>=' ) )
	{
		if( !empty($GeneralSettings['taxonomy_podcasting']) )  // Taxonomy Podcasting
		{
			$EmpowerCastTaxonomies = get_option('empowercast_taxonomy_podcasting');
			if( !empty($EmpowerCastTaxonomies) )
			{
                foreach ($EmpowerCastTaxonomies as $key => $value) {
                    $ttid_found = $key;

                    $TaxonomySettings = get_option('empowercast_taxonomy_'.$ttid_found);
                    // Found it???
                    if( !empty($TaxonomySettings['redirect']) )
                    {
                        $Redirects['redirect0'] = $TaxonomySettings['redirect'];
                        $Redirects['redirect1'] = '';
                        $Redirects['redirect2'] = '';
                        $Redirects['redirect3'] = '';
                        break;
                    }
                }
			}
		}
	}

	// Allow other apps to update the redirects
	$Redirects = apply_filters('empowercast_redirects', $Redirects, $EpisodeData);

	for( $x = 3; $x >= 0; $x-- )
	{
		$key = sprintf('redirect%d', $x);
		if( !empty($Redirects[ $key ]) )
		{
			if( preg_match('/^https?:\/\/(.*)$/', trim($Redirects[ $key ]) , $matches ) == 0 )
				continue;

			$RedirectClean = $matches[1];
			if( substr($RedirectClean, -1, 1) != '/' ) // Rediercts need to end with a slash /.
				$RedirectClean .= '/';

			if( !empty($RedirectClean) )
			{
				if( strpos($RedirectClean, '/') == 0 ) // Not a valid redirect URL
					continue;

				if( !strstr($NewURL, $RedirectClean) ) // If the redirect is not already added...
					$NewURL = $URLScheme. $RedirectClean . str_replace($URLScheme, '', $NewURL);
			}
		}
	}

	return $NewURL;
}

function empowercast_add_flag_to_redirect_url($MediaURL, $Flag)
{
	// First strip any previous flags...
	return $MediaURL;
}

/*
Code contributed from upekshapriya on the Podops Forums
*/
function empowercast_byte_size($ppbytes)
{
	$ppbytes = intval($ppbytes);
	if( empty($ppbytes) )
		return '';
	$ppsize = intval($ppbytes) / 1024;
	if($ppsize < 1024)
	{
		$ppsize = number_format($ppsize, 1);
		$ppsize .= 'KB';
	}
	else
	{
		if($ppsize / 1024 < 1024)
		{
			$ppsize = number_format($ppsize / 1024, 1);
			$ppsize .= 'MB';
		}
		else if ($ppsize / 1024 / 1024 < 1024)
		{
		$ppsize = number_format($ppsize / 1024 / 1024, 1);
		$ppsize .= 'GB';
		}
	}
	return $ppsize;
}

// Merges settings from feed settings page to empty custom feed settings
function empowercast_merge_empty_feed_settings($CustomFeedSettings, $FeedSettings, $DefaultPodcastFeed = false)
{
	unset($FeedSettings['apply_to']);
	// Remove settings from main $FeedSettings that should not be copied to custom feed.
	if( !$DefaultPodcastFeed )
	{
		unset($FeedSettings['itunes_new_feed_url']);
		unset($FeedSettings['feed_redirect_url']);
		unset($FeedSettings['itunes_complete']);
		unset($FeedSettings['itunes_block']);
		unset($FeedSettings['maximize_feed']);
        unset($FeedSettings['live_item']);
    }

	// If the setting is not already set, set the enhnaced itunes setting if they have PHP5+ on by default
	if( !isset($FeedSettings['enhance_itunes_summary']) )
		$FeedSettings['enhance_itunes_summary'] = 0;

	if( !$CustomFeedSettings )
		return $FeedSettings; // If the $CustomFeedSettings is false

    if (is_array($CustomFeedSettings)) {
        foreach ($CustomFeedSettings as $key => $value) {
            if ($value !== '' || !isset($FeedSettings[$key]))
                $FeedSettings[$key] = $value;
        }
    }

	return $FeedSettings;
}

function empowercast_readable_duration($duration, $include_hour=false)
{
	$seconds = 0;
	$parts = explode(':', $duration);
	if( count($parts) == 3 )
		$seconds = $parts[2] + ($parts[1]*60) + ($parts[0]*60*60);
	else if ( count($parts) == 2 )
		$seconds = $parts[1] + ($parts[0]*60);
	else
		$seconds = $parts[0];

	$hours = 0;
	$minutes = 0;
	if( $seconds >= (60*60) )
	{
		$hours = floor( $seconds /(60*60) );
		$seconds -= (60*60*$hours);
	}
	if( $seconds >= (60) )
	{
		$minutes = floor( $seconds /(60) );
		$seconds -= (60*$minutes);
	}

	if( $hours || $include_hour ) // X:XX:XX (readable)
		return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);

	return sprintf('%d:%02d', $minutes, $seconds); // X:XX or 0:XX (readable)
}

// Duratoin in form of seconds (parses hh:mm:ss)
function empowercast_raw_duration($duration)
{
	$duration = trim($duration);
	$Parts = explode(':',$duration);
	if( empty($Parts) )
		return $duration;

	if( count($Parts) == 3 )
		return ((intval($Parts[0])*60*60) + (intval($Parts[1])*60) + intval($Parts[2]));
	else if( count($Parts) == 2 )
		return ((intval($Parts[0])*60) + intval($Parts[1]));
	//else if( count($Parts) == 1 )
	//	return ($Parts[0]);

	// We never found any colons, so we assume duration is seconds
	return $duration;
}

// For grabbing data from Podpress data stored serialized, the strings for some values can sometimes get corrupted, so we fix it...

function empowercast_repair_serialize($string)
{
	if( @unserialize($string) )
		return $string; // Nothing to repair...

	$string = preg_replace_callback('/(s:(\d+):"([^"]*)")/',
		'empowercast_repair_serialize_callback',
		$string);

	if( substr($string, 0, 2) == 's:' ) // Sometimes the serialized data is double serialized, so we need to re-serialize the outside string
	{
		$string = preg_replace_callback('/(s:(\d+):"(.*)"(;))$/',
			'empowercast_repair_serialize_callback',
			$string);
	}

	return $string;
}

function empowercast_repair_serialize_callback($matches)
{
	if( strlen($matches[3]) == $matches[2] )
		return $matches[0];
	return sprintf('s:%d:"%s"', strlen($matches[3]), $matches[3]) . (!empty($matches[4])?';':'');
}

function empowercast_base64_encode($value)
{
	return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}
/*
	empowercast_get_post_meta()
	Safe function to retrieve corrupted PodPress data from the database
	@post_id - post id to retrieve post meta for
	@key - key to retrieve post meta for
*/
function empowercast_get_post_meta($post_id, $key)
{
	$pp_meta_cache = wp_cache_get($post_id, 'post_meta');
	if ( !$pp_meta_cache ) {
		update_postmeta_cache($post_id);
		$pp_meta_cache = wp_cache_get($post_id, 'post_meta');
	}

	$meta = false;
	if ( isset($pp_meta_cache[$key]) )
		$meta = $pp_meta_cache[$key][0];

	if ( is_serialized( $meta ) ) // Logic used up but not including WordPress 2.8, new logic doesn't make sure if unserialized failed or not
	{
		if ( false !== ( $gm = @unserialize( $meta ) ) )
			return $meta;
	}

	return $meta;
}

function empowercast_get_enclosure($post_id, $feed_slug = 'podcast')
{
	$Data = empowercast_get_enclosure_data($post_id, $feed_slug);
	if( $Data )
		return $Data['url'];
	return false;
}

function empowercast_get_enclosure_data($post_id, $feed_slug = 'podcast', $raw_data = false, $add_redirect=true)
{
	global $post;
	if( false != $raw_data )
		$MetaData = $raw_data;
	else
	{
		if( !empty($post->podcast_meta_value) && $post->ID == $post_id) // See if we got the meta data from the initial query...
		{
			// Make sure this is not serialized data from PodPress...
			$partsTest = explode("\n", $post->podcast_meta_value, 4);
			if( count($partsTest) > 2 ) {
				$PodcastData = empowercast_get_enclosure_data($post_id, $feed_slug, $post->podcast_meta_value, true);
				return $PodcastData;
			}
		}

		if( 'podcast' == $feed_slug || '' == $feed_slug )
			$MetaData = get_post_meta($post_id, 'enclosure', true);
		else
			$MetaData = get_post_meta($post_id, '_'. $feed_slug .':enclosure', true);
	}
	if( empty($MetaData) )
		return false;

	$MetaParts = explode("\n", $MetaData, 4);

	$Serialized = false;
	$Data = array();
	$Data['id'] = $post_id;
	$Data['feed'] = $feed_slug;
	$Data['url'] = '';
	$Data['duration'] = '';
	$Data['size'] = '';
	$Data['type'] = '';
	$Data['width'] = '';
	$Data['height'] = '';

	if( count($MetaParts) > 0 )
		$Data['url'] = trim($MetaParts[0]);
	if( count($MetaParts) > 1 )
		$Data['size'] = trim($MetaParts[1]);
	if( count($MetaParts) > 2 )
		$Data['type'] = trim($MetaParts[2]);
	if( count($MetaParts) > 3 )
		$Serialized = $MetaParts[3];

	if ($MetaParts[0] == 'no') {
	    return false;
    }

	if( $Serialized )
	{
		$ExtraData = @unserialize($Serialized);
		if( $ExtraData && is_array($ExtraData) )
		{
			foreach( $ExtraData as $key=> $value ) {

				// Make sure specific fields are not overwritten...
				switch( $key ) {
					case 'id':
					case 'feed':
					case 'url':
					case 'size':
					case 'type': break;
					default: $Data[ $key ] = $value;
				}
			}

			if( isset($Data['length']) ) // Setting from the "Podcasting" plugin...
				$Data['duration'] = empowercast_readable_duration($Data['length'], true);

			if( !empty($Data['webm_src']) )
			{
				$Data['webm_src'] = trim($Data['webm_src']);
			}


			if( strpos($MetaParts[0], 'http://') !== 0 && !empty($Data['hosting']) ) // if the URL is not set (just file name) and we're a hosting customer...
			{
				$post_status = get_post_status($post_id);
				switch( $post_status )
				{
					case 'pending':
					case 'draft':
					case 'auto-draft': {
						// Determine if audio or video, then set the demo episode here...
						$Data['url'] = 'http://media.thepodops.com/podops/content.thepodops.com/podops/preview.mp3'; // audio
						if( strstr($Data['type'], 'video') )
							$Data['url'] = 'http://media.thepodops.com/podops/content.thepodops.com/podops/preview.mp4'; // video
					}; break;
				}
			}
		}
	}

	// If the URL is using Podops hosting, then lets pump it up to https...
	if( is_ssl() && preg_match('/^http:\/\/(.*\/content\.podops\.com\/.*)$/i', $Data['url'], $matches) )
	{
		$Data['url'] = 'https://'. $matches[1];
	}

	// Check that the content type is a valid one...
	if( strstr($Data['type'], '/') == false )
		$Data['type'] = empowercast_get_contenttype($Data['url']);

	// Do redirect filter here...
	if( $add_redirect && !empty($Data['url']) )
		$Data['url'] = empowercast_add_redirect_url( $Data['url'], $Data );

	if( $add_redirect && !empty($Data['webm_src']) )
		$Data['webm_src'] = empowercast_add_redirect_url( $Data['webm_src'], $Data );

	return apply_filters('empowercast_get_enclosure_data', $Data);
}

function empowercast_get_enclosure_data_podpress($post_id, $mediaNum = 0, $include_premium = false)
{
	$podPressMedia = empowercast_get_post_meta($post_id, 'podPressMedia');
	if( !$podPressMedia )
		$podPressMedia = empowercast_get_post_meta($post_id, '_podPressMedia'); // handles latest verions of PodPress
	if( $podPressMedia )
	{

		if( !is_array($podPressMedia) )
		{
			// Sometimes the stored data gets messed up, we can fix it here:
			$podPressMedia = empowercast_repair_serialize($podPressMedia);
			$podPressMedia = @unserialize($podPressMedia);
		}

		// Do it a second time in case it is double serialized
		if( !is_array($podPressMedia) )
		{
			// Sometimes the stored data gets messed up, we can fix it here:
			$podPressMedia = empowercast_repair_serialize($podPressMedia);
			$podPressMedia = @unserialize($podPressMedia);
		}

		if( is_array($podPressMedia) && isset($podPressMedia[$mediaNum]) && isset($podPressMedia[$mediaNum]['URI']) )
		{
			if( $include_premium == false && isset($podPressMedia[$mediaNum]['premium_only']) && ($podPressMedia[$mediaNum]['premium_only'] == 'on' || $podPressMedia[$mediaNum]['premium_only'] == true) )
				return false;

			$Data = array();
			$Data['id'] = $post_id;
			$Data['feed'] = 'podcast';
			$Data['duration'] = 0;
			$Data['url'] = '';
			$Data['size'] = 0;
			$Data['type'] = '';
			$Data['width'] = '';
			$Data['height'] = '';

			$Data['url'] = $podPressMedia[$mediaNum]['URI'];
			if( isset($podPressMedia[$mediaNum]['size']) )
				$Data['size'] = $podPressMedia[$mediaNum]['size'];
			if( isset($PodPressSettings[$mediaNum]['duration']) )
				$Data['duration'] = $podPressMedia[$mediaNum]['duration'];
			if( isset($PodPressSettings[$mediaNum]['previewImage']) )
				$Data['image'] = $podPressMedia[$mediaNum]['previewImage'];

			if( strpos($Data['url'], 'http://' ) !== 0 && strpos($Data['url'], 'https://' ) !== 0 )
			{
				$PodPressSettings = get_option('podPress_config');
				if( $PodPressSettings && isset($PodPressSettings['mediaWebPath']) )
					$Data['url'] = rtrim($PodpressSettings['mediaWebPath'], '/') . '/' . ltrim($Data['url'], '/');
				unset($PodPressSettings);
			}

			if( strpos($Data['url'], 'http://' ) !== 0 && strpos($Data['url'], 'https://' ) !== 0 )
			{
				$Settings = get_option('empowercast_general');
				if( $Settings && isset($Settings['default_url']) )
					$Data['url'] = rtrim($Settings['default_url'], '/') . '/' . ltrim($Data['url'], '/');
			}

			if( strpos($Data['url'], 'http://' ) !== 0 && strpos($Data['url'], 'https://' ) !== 0 )
				return false;

			$Data['type'] = empowercast_get_contenttype($Data['url']); // Detect the content type
			$Data['url'] = empowercast_add_redirect_url($Data['url'], $Data); // Add redirects to Media URL

			return apply_filters('empowercast_get_enclosure_data', $Data);
		}
	}
	return false;
}

function empowercast_get_apple_id($url, $strict=false)
{
	if( $strict )
	{
		$results = preg_match('/apple\.com\/.*\/id(\d+)/i', $url, $matches);
		if( !$results )
			$results = preg_match('/apple\.com\/.*id\=(\d+)/i', $url, $matches);
		if( $results )
			return $matches[1];
		return 0;
	}
	$results = preg_match('/\/id(\d+)/i', $url, $matches);
	if( !$results )
		$results = preg_match('/id\=(\d+)/i', $url, $matches);
	if( $results )
		return $matches[1];
	return 0;
}


function the_empowercast_all_players($slug = false, $no_link=false)
{
	echo get_the_empowercast_all_players($slug, $no_link);
}

function get_the_empowercast_all_players($slug = false, $no_link=false)
{
	$return = '';
	//Use this function to insert the Empowercast player anywhere in the page.
	//Made by Nicolas Bouliane (http://nicolasbouliane.com/)

	/*We're going to use the Loop to retrieve the latest post with the 'enclosure' custom key set
	//then interpret it and manually launch empowercastplayer_build with the URL contained within
	//that data.*/

	//Let's reset the Loop to make sure we look through all posts
	rewind_posts();

	// Get the list of podcast channel slug names...
	$GeneralSettings = get_option('empowercast_general');

	// No player or links to add to content...
	if( !empty($GeneralSettings['disable_appearance']) )
		return $return;

	$ChannelSlugs = array('podcast');
	if( $slug == false )
	{
		if( isset($GeneralSettings['custom_feeds']['podcast']) )
			$ChannelSlugs = array(); // Reset the array so it is added from the list in specified order
		foreach( $GeneralSettings['custom_feeds'] as $feed_slug=> $null )
			$ChannelSlugs[] = $feed_slug;
	}
	else if( is_array($slug) )
	{
		$ChannelSlugs = $slug;
	}
	else
	{
		$ChannelSlugs = array($slug);
	}

	// Loop through the posts
	while( have_posts() )
	{
		the_post();

		foreach( $ChannelSlugs as $null=> $feed_slug )
		{
			// Do we follow the global settings to disable a player?
			if( isset($GeneralSettings['disable_player']) && isset($GeneralSettings['disable_player'][$feed_slug]) && $slug == false )
				continue;

			$EpisodeData = empowercast_get_enclosure_data(get_the_ID(), $feed_slug);
			if( !$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast' )
				$EpisodeData = empowercast_get_enclosure_data_podpress(get_the_ID());

			if( !$EpisodeData )
				continue;

			$AddDefaultPlayer = true;
			if( !empty($EpisodeData['embed']) )
			{
				$return .= $EpisodeData['embed'];
				if( !empty($GeneralSettings['embed_replace_player']) )
					$AddDefaultPlayer = false;
			}

			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !empowercast_premium_content_authorized($feed_slug) )
			{
				$return .= empowercast_premium_content_message(get_the_ID(), $feed_slug, $EpisodeData);
				continue;
			}

			if( !isset($EpisodeData['no_player']) && $AddDefaultPlayer )
			{
				do_action('wp_empowercast_player_scripts');
				$return .= apply_filters('empowercast_player', '', empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
			if( !isset($EpisodeData['no_links']) && $no_link == false )
			{
				do_action('wp_empowercast_player_scripts');
				$return .= apply_filters('empowercast_player_links', '',  empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
				$return .= apply_filters('empowercast_player_subscribe_links', '',  empowercast_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
		}
		reset($ChannelSlugs);
	}

	return $return;
}

function empowercast_premium_content_authorized_filter($default, $feed_slug)
{
	if( $feed_slug != 'podcast' )
	{
		$FeedSettings = get_option('empowercast_feed_'. $feed_slug);
		if( isset($FeedSettings['premium']) && $FeedSettings['premium'] != '' )
			return current_user_can($FeedSettings['premium']);
	}

	$post_type = get_query_var('post_type');
	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type ); // get first element in array
	}

	if( $post_type != 'post' )
	{
		$GeneralSettings = get_option('empowercast_general');
		if( !empty($GeneralSettings['posttype_podcasting']) ) // Custom Post Types
		{
			// Get the feed slugs and titles for this post type
			$PostTypeSettingsArray = get_option('empowercast_posttype_'.$post_type);
			if( !empty($PostTypeSettingsArray[$feed_slug]['premium']) )
				return current_user_can($PostTypeSettingsArray[$feed_slug]['premium']);
		}
	}

	return $default;
}
add_filter('empowercast_premium_content_authorized', 'empowercast_premium_content_authorized_filter', 10, 2);

function empowercast_premium_content_authorized($feed_slug)
{
	return apply_filters('empowercast_premium_content_authorized', true, $feed_slug );
}

function empowercast_premium_content_message($post_id, $feed_slug, $EpisodeData = false)
{
	if( !$EpisodeData && $post_id )
		$EpisodeData = empowercast_get_enclosure_data($post_id, $feed_slug);

	if( !$EpisodeData )
		return '';
	$FeedSettings = get_option('empowercast_feed_'.$feed_slug);
	$post_type = get_query_var('post_type');
	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type ); // get first element in array
	}

	if( $post_type != 'post' )
	{
		$GeneralSettings = get_option('empowercast_general');
		if( !empty($GeneralSettings['posttype_podcasting']) ) // Custom Post Types
		{
			// Get the feed slugs and titles for this post type
			$PostTypeSettingsArray = get_option('empowercast_posttype_'.$post_type);
			if( !empty($PostTypeSettingsArray[$feed_slug]['premium']) )
			{
				$FeedSettings = $PostTypeSettingsArray[$feed_slug];
			}
		}
	}

	$extension = 'unknown';
	$parts = pathinfo($EpisodeData['url']);
	if( $parts && isset($parts['extension']) )
		$extension  = strtolower($parts['extension']);

	if( isset($FeedSettings['premium_label']) && $FeedSettings['premium_label'] != '' ) // User has a custom label
		return '<p class="empowercast_links empowercast_links_'. $extension .'">'. $FeedSettings['premium_label'] . '</p>'.PHP_EOL_WEB;

	return '<p class="empowercast_links empowercast_links_'. $extension .'">'. htmlspecialchars($FeedSettings['title']) .': <a href="'. get_bloginfo('url') .'/wp-login.php" title="Protected Content">(Protected Content)</a></p>'.PHP_EOL_WEB;
}

function empowercast_is_mobile_client()
{
	_deprecated_function( __FUNCTION__, '7.0' );
	return false;
}

function empowercast_get_api_array()
{
	$return = array();
	if( strstr(EMPOWERCAST_PODOPS_API_URL, 'http://api.thepodops.com') == false ) // If not the default
	{
		$return = explode(';', EMPOWERCAST_PODOPS_API_URL);
	}
	else
	{
		$return[] = 'https://api.thepodops.com/'; // Use secure URL first when possible
		$return[] = 'https://api.podops.net/';
	}

	return $return;
}


function empowercast_in_wp_head()
{
	$e = new Exception();
	$trace = $e->getTrace();

	if( !empty($trace) ) {
		foreach( $trace as $index=> $call ) {
			if( isset($call['function']) ) {
				// Which calls should we not add the player and links...
				switch( $call['function'] ) {
					case 'wp_head': return true; break;
				}
			}
		}
	}
	return false;
}

function empowercast_in_custom_post_widget()
{
	if( !class_exists('custom_post_widget') )
		return false;

	$e = new Exception();
	$trace = $e->getTrace();

	if( !empty($trace) ) {

		foreach( $trace as $index=> $call ) {
			if( isset($call['function']) ) {
				// Which calls should we not add the player and links...
				switch( $call['function'] ) {
					case 'custom_post_widget_shortcode': return true; break;
				}
			}
		}
	}
	return false;
}

function empowercast_admin_migration_notice() {
    $QueuedResults = get_option('empowercast_migrate_queued');
    $Status = get_option('empowercast_migrate_status');
    $completed = false;

    // we have successfully migrated all media, or there is no more media to migrate
    if (count($QueuedResults) == $Status['completed'] || ($Status['queued'] == 0 && $Status['downloading'] == 0)) {
        $completed = true;
    }

    $alert_link = $root_url = ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ) ) . htmlspecialchars($_SERVER['HTTP_HOST']) . "/wp-admin/admin.php?page=empowercast/empowercastadmin_migrate.php";
    $alert_class = 'empowercast-notice notice is-dismissible ';
    if ($completed) {
        $alert_class .= ' notice-success ';
        $alert_message = 'Your migration has completed. ';
        $alert_link = $root_url . "&action=empowercast-migrate-media&migrate_step=3";;
        $alert_link_message = " to update your episodes.";
    } else {
        $alert_class .= ' notice-info ';
        $alert_message = 'Your migration is in progress. ';
        $alert_link = $root_url . "&action=empowercast-migrate-media&refresh_migrate_status=1";
        $alert_link_message = " to check the status of your migration.";
    }


    $html = "<p class='alertMessage'>$alert_message<a href='$alert_link'>Click here</a>$alert_link_message</p>"
        . '<p>&nbsp; <a style="float:right;" href="#" class="notice-dismiss-link"></a></p>' . PHP_EOL;
    empowercast_page_message_add_notice($html, 'inline', false);
}
/*
End Helper Functions
*/

// Are we in the admin?
if( is_admin() )
{
	require_once(EMPOWERCAST_ABSPATH.'/empowercastadmin.php');
	register_activation_hook( __FILE__, 'empowercast_admin_activate' );
}

if( defined('EMPOWERCAST_SUBSCRIBE') && EMPOWERCAST_SUBSCRIBE )
{
	require_once(EMPOWERCAST_ABSPATH.'/empowercast-subscribe.php');
}

// For testing purposes in development
if( defined('EMPOWERCAST_NEW_CODE') && EMPOWERCAST_NEW_CODE && file_exists(EMPOWERCAST_ABSPATH.'/empowercast-new-code.php') )
{
	require_once(EMPOWERCAST_ABSPATH.'/empowercast-new-code.php');
}

if( defined('EMPOWERCAST_PREMIUM_GROUPS_PLUGIN') ) {

	function empowercast_pre_get_posts($query) {
		if( $query->is_feed() && empowercast_is_custom_podcast_feed() && method_exists('Groups_Post_Access', 'posts_where') )
		{
			$feed_slug = get_query_var('feed');

			if( $feed_slug != 'podcast' )
			{
				$FeedSettings = get_option('empowercast_feed_'.$feed_slug);
				if( !empty($FeedSettings['premium']) )
				{
					if( has_filter('posts_where', 'Groups_Post_Access::posts_where') )
					{
						remove_filter('posts_where', 'Groups_Post_Access::posts_where');
					}
				}
			}
		}
	}
	add_filter('pre_get_posts', 'empowercast_pre_get_posts');
}

// eof