<?php

if( !function_exists('add_action') )
	die("access denied.");

define('EMPOWERCAST_FEED_HIGHLIGHTED', 'https://podopshost.com/podcast-insider/category/highlighted/feed/?order=ASC');
define('EMPOWERCAST_FEED_NEWS', 'https://podopshost.com/podcast-insider/feed/');

function empowercast_get_news($feed_url, $limit=10)
{
	include_once(ABSPATH . WPINC . '/feed.php');
	$rss = fetch_feed( $feed_url );
	
	// If feed doesn't work...
	if ( is_wp_error($rss) )
	{
		require_once( ABSPATH . WPINC . '/class-feed.php' );
		// Try fetching the feed using CURL directly...
		$content = empowercast_remote_fopen($feed_url, false, array(), 3, false, true);
		if( !$content ) {
			return false;
		}
		// Load the content in a fetch_feed object...
		$rss = new SimplePie();

		$rss->set_sanitize_class( 'WP_SimplePie_Sanitize_KSES' );
		// We must manually overwrite $feed->sanitize because SimplePie's
		// constructor sets it before we have a chance to set the sanitization class
		$rss->sanitize = new WP_SimplePie_Sanitize_KSES();

		$rss->set_cache_class( 'WP_Feed_Cache' );
		$rss->set_file_class( 'WP_SimplePie_File' );
		$rss->set_raw_data($content);
		$rss->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 12 * HOUR_IN_SECONDS, $feed_url ) );
		do_action_ref_array( 'wp_feed_options', array( &$rss, $feed_url ) );
		$rss->init();
		$rss->set_output_encoding( get_option( 'blog_charset' ) );
		$rss->handle_content_type();

		if ( $rss->error() )
			return false;
	}
	
	$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $limit ) );
	
	// If the feed was erroneously 
	if ( !$rss_items ) {
		$md5 = md5( $feed_url ); // This is from simple-pie, look at member variable ->cache_name_function
		delete_transient( 'feed_' . $md5 );
		delete_transient( 'feed_mod_' . $md5 );
		$rss->__destruct();
		unset($rss);
		$rss = fetch_feed( $feed_url );
		$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
		$rss->__destruct();
		unset($rss);
	}
	
	return $rss_items;
}

	
function empowercast_dashboard_head()
{
	echo "<script type=\"text/javascript\" src=\"". empowercast_get_root_url() ."player.min.js\"></script>\n";
?>
<style type="text/css">
#podops_stats_summary {
	
}
#podops_stats_summary label {
	width: 40%;
	max-width: 150px;
	float: left;
}
#podops_stats_summary h2 {
	font-size: 14px;
	margin: 0;
	padding: 0;
}
.podops_stats_ul {
	padding-left: 20px;
	margin-top: 5px;
	margin-bottom: 10px;
}
.podops_stats_ul li {
	list-style-type: none;
	margin: 0px;
	padding: 0px;
}
#podops_stats_media {
	display: none;
}
#podops_stats_media_show {
	text-align: right;
	font-size: 85%;
}
#podops_stats_media h4 {
	margin-bottom: 10px;
}
.podops_stats_title {
	margin-left: 10px;
}
.podops_stats_updated {
	font-size: 80%;
}
.empowercast-news-dashboard {
/*	background-image:url(http://images.podopshost.com/empowercast/podops_logo.png);
	background-repeat: no-repeat;
	background-position: top right; */
}
.empowercast-news-dashboard .empowercastNewsPlayer {
	margin-top: 5px;
}
</style>
<script type="text/javascript"><!--
jQuery(document).ready(function($) {
	jQuery('.empowercast-dashboard-notice').click( function(e) {
		e.preventDefault();
		var dash_id = jQuery(this).parents('.postbox').attr('id');
		jQuery( '#' + dash_id + '-hide' ).prop('checked', false).triggerHandler('click');
	
		jQuery.ajax( {
				type: 'POST',
				url: '<?php echo admin_url(); ?>admin-ajax.php', 
				data: { action: 'empowercast_dashboard_dismiss', dismiss_dash_id : dash_id, nonce: '<?php echo wp_create_nonce('empowercast-dashboard-dismiss'); ?>' },
				success: function(response) {
				}
			});
	});
});
// --></script>
<?php
}

/**
 * Prints Podops Stats Widget to the WordPress dashboard using the stats widget class
 */
function empowercast_dashboard_stats_content()
{
    require_once('empowercastadmin-stats-widget.class.php');
    $widget = new EmpowerCastStatsWidget();
    $widget->empowercast_print_stats_widget();
}

function empowercast_dashboard_news_content()
{
	$Settings = get_option('empowercast_general');
		
	empowercastadmin_community_news();
}

function empowercast_dashboard_notice_message($notice_id, $message)
{
	echo $message;
	// Add link to remove this notice.
	echo '<p><a href="#" id="empowercast_dashboard_notice_'. $notice_id .'_dismiss" class="empowercast-dashboard-notice">'. __('Dismiss', 'empowercast')  .'</a></p>';
}


function empowercast_feed_text_limit( $text, $limit, $finish = '&hellip;') {
	if( strlen( $text ) > $limit ) {
			//$text = (function_exists('mb_substr')?mb_substr($text, 0, $limit):substr($text, 0, $limit) );
			$text = substr( $text, 0, $limit );
		$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
		$text .= $finish;
	}
	return $text;
}

function empowercast_dashboard_setup()
{
	if( !function_exists('wp_add_dashboard_widget') )
		return; // We are not in the dashboard!
		
	if( !empty($_GET['empowercastdash']) && $_GET['empowercastdash'] == 1 )
		return;
	
	$Settings = get_option('empowercast_general');
	$StatsDashboard = true;
	$NewsDashboard = true;
	
	if( !empty($Settings['disable_dashboard_stats']) )
		$StatsDashboard = false; // Lets not do anything to the dashboard for EmpowerCast Statistics
	
	if( !empty($Settings['disable_dashboard_news']) )
		$NewsDashboard = false; // Lets not do anything to the dashboard for EmpowerCast News
		
	if( !empty($Settings['use_caps']) && !current_user_can('view_podcast_stats') )
		$StatsDashboard = false;
	
	$user = wp_get_current_user();
	
	if( !empty($_GET['empowercastdash']) && $_GET['empowercastdash'] == 2 ) {
		return;
	}
	
	//if( $NewsDashboard )
	wp_add_dashboard_widget( 'empowercast_dashboard_news', __( 'Podcast Insider by Podops', 'empowercast'), 'empowercast_dashboard_news_content' );
	
	if( !empty($_GET['empowercastdash']) && $_GET['empowercastdash'] == 3 ) {
		return;
	}
	
	if( $StatsDashboard )
		wp_add_dashboard_widget( 'empowercast_dashboard_stats', __( 'Podops Podcast Statistics', 'empowercast'), 'empowercast_dashboard_stats_content' );

	if( !empty($_GET['empowercastdash']) && $_GET['empowercastdash'] == 4 ) {
		return;
	}
	
	if( !empty( $user ) ) {
		$user_options = get_user_option('empowercast_user');
		if( empty($user_options) || empty($user_options['dashboard_installed']) || $user_options['dashboard_installed'] < 2 )
		{
			if( !is_array($user_options) )
				$user_options = array();
			
			if( !empty($_GET['empowercastdash']) && $_GET['empowercastdash'] == 5 ) {
				return;
			}
			
			// First time we've seen this setting, so must be first time we've added the widgets, lets stack them at the top for convenience.
			empowercastadmin_add_dashboard_widgets($user->ID);
			$user_options['dashboard_installed'] = 2; // version of EmpowerCast
			update_user_option($user->ID, "empowercast_user", $user_options, true);
		}
		else
		{
			if( !empty($_GET['empowercastdash']) && $_GET['empowercastdash'] == 6 ) {
				return;
			}
			
			empowercastadmin_add_dashboard_widgets(false);
		}
	}
}

function empowercastadmin_add_dashboard_notice_widget($user_id, $notice_id)
{
	$user_options = get_user_option('meta-box-order_dashboard', $user_id);
	if( $user_options )
	{
		$save = false;
		if( !preg_match('/empowercast_dashboard_notice_'.$notice_id.'/', $user_options['normal']) && !preg_match('/empowercast_dashboard_notice_'.$notice_id.'/', $user_options['side']) && !preg_match('/empowercast_dashboard_notice_'.$notice_id.'/', $user_options['column3']) && !preg_match('/empowercast_dashboard_notice_'.$notice_id.'/', $user_options['column4']) )
		{	
			$save = true;
			$user_options['normal'] = 'empowercast_dashboard_notice_'.$notice_id.','.$user_options['normal'];
		}
		
		if( $save )
		{
			update_user_option($user_id, "meta-box-order_dashboard", $user_options, true);
		}
	}
}

function empowercastadmin_add_dashboard_widgets( $check_user_id = false)
{
	// Only re-order the empowercast widgets if they aren't already on the dashboard:
	if( $check_user_id )
	{
		$user_options = get_user_option('meta-box-order_dashboard', $check_user_id);
		if( $user_options )
		{
			$save = false;
			if( !preg_match('/empowercast_dashboard_stats/', $user_options['normal']) && !preg_match('/empowercast_dashboard_stats/', $user_options['side']) && !preg_match('/empowercast_dashboard_stats/', $user_options['column3']) && !preg_match('/empowercast_dashboard_stats/', $user_options['column4']) )
			{	
				$save = true;
				if( !empty($user_options['side']) )
					$user_options['side'] = 'empowercast_dashboard_stats,'.$user_options['side'];
				else
					$user_options['normal'] = 'empowercast_dashboard_stats,'.$user_options['normal'];
			}
			
			if( !preg_match('/empowercast_dashboard_news/', $user_options['normal']) && !preg_match('/empowercast_dashboard_news/', $user_options['side']) && !preg_match('/empowercast_dashboard_news/', $user_options['column3']) && !preg_match('/empowercast_dashboard_news/', $user_options['column4']) )
			{	
				$save = true;
				$user_options['normal'] = 'empowercast_dashboard_news,'.$user_options['normal'];
			}
			
			if( $save )
			{
				update_user_option($check_user_id, "meta-box-order_dashboard", $user_options, true);
			}
		}
	}
	
	// Reorder for all future users
	global $wp_meta_boxes;
	$dashboard_current = $wp_meta_boxes['dashboard']['normal']['core'];
	
	$dashboard_empowercast = array();
	for( $i = 0; $i < 20; $i++ )
	{
		if( isset( $dashboard_current['empowercast_dashboard_notice_' . $i] ) )
		{
			$dashboard_empowercast['empowercast_dashboard_notice_' . $i] = $dashboard_current['empowercast_dashboard_notice_' . $i];
			unset($dashboard_current['empowercast_dashboard_notice_' . $i]);
		}
	}
	
	if( isset( $dashboard_current['empowercast_dashboard_news'] ) )
	{
		$dashboard_empowercast['empowercast_dashboard_news'] = $dashboard_current['empowercast_dashboard_news'];
		unset($dashboard_current['empowercast_dashboard_news']);
	}
	
	if( isset( $dashboard_current['empowercast_dashboard_stats'] ) )
	{
		$dashboard_empowercast['empowercast_dashboard_stats'] = $dashboard_current['empowercast_dashboard_stats'];
		unset($dashboard_current['empowercast_dashboard_stats']);
	}
	
	if( count($dashboard_empowercast) > 0 )
	{
		$wp_meta_boxes['dashboard']['normal']['core'] = array_merge($dashboard_empowercast, $dashboard_current);
	}
}
	 
add_action('admin_head-index.php', 'empowercast_dashboard_head');
add_action('wp_dashboard_setup', 'empowercast_dashboard_setup');

function empowercast_dashboard_dismiss()  // Called by AJAX call
{
	$dismiss_dash_id = $_POST['dismiss_dash_id'];
	preg_match('/^empowercast_dashboard_notice_(.*)$/i', $dismiss_dash_id, $match );
	if( empty($match[1]) )
		exit;
	$DismissedNotices = get_option('empowercast_dismissed_notices');
	if( !is_array($DismissedNotices) )
		$DismissedNotices = array();
	$DismissedNotices[ $match[1] ] = 1;
	update_option('empowercast_dismissed_notices',  $DismissedNotices);
	echo 'ok';
	exit;
}

?>