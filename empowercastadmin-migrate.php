<?php


function empowercast_admin_verify_url($url)
{
	$wp_remote_options = array();
	$wp_remote_options['user-agent'] = 'Podops EmpowerCast/'.EMPOWERCAST_VERSION;
	$wp_remote_options['httpversion'] = '1.1';
	$response = wp_remote_head( $url, $wp_remote_options );
	
	for( $x = 0; $x < 5; $x++ )
	{
		// Redirect 1-5
		if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
		{
			$headers = wp_remote_retrieve_headers( $response );
			$response = wp_remote_head( $headers['location'], $wp_remote_options );
		}
		else
		{
			break;// Either we had an error or the response code is no longer a redirect
		}
	}

	if ( is_wp_error( $response ) )
	{
		return array('error'=>$response->get_error_message() );
	}
	
	if( isset($response['response']['code']) && ($response['response']['code'] < 200 || $response['response']['code'] > 203) )
	{
		return array('error'=>'Error, HTTP '.$response['response']['code'] );
	}
	
	return array('error'=>false);
}

function empowercast_admin_migrate_get_files($clean=false, $exclude_podops=true)
{
		global $wpdb;
		
		$return = array();
		//$return['feeds_required'] = 0;
		$query = "SELECT p.ID, p.post_title, p.post_date, pm.meta_id, pm.post_id, pm.meta_key, pm.meta_value ";
		$query .= "FROM {$wpdb->posts} AS p ";
		$query .= "INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id ";
		$query .= "WHERE (pm.meta_key = 'enclosure' OR pm.meta_key LIKE '\_%:enclosure') ";
		$query .= "AND p.post_type != 'revision' ";
		$query .= "GROUP BY pm.meta_id ";
		$query .= "ORDER BY p.post_date DESC ";
		
		$results_data = $wpdb->get_results($query, ARRAY_A);
		if( $results_data )
		{
			foreach( $results_data as $null => $row )
			{
				$meta_id = $row['meta_id'];
				$EpisodeData = empowercast_get_enclosure_data($row['post_id'], 'podcast', $row['meta_value'], false); // Get the enclosure data with no redirect added
				
				if( $exclude_podops && strstr($EpisodeData['url'], 'content.podopshost.com') )
					continue; // Skip media hosted on podops in this case

                if( $exclude_podops && strstr($EpisodeData['url'], 'ins.podopshost.com') )
                    continue; // Skip media hosted on podops in this case

                if( $exclude_podops && strstr($EpisodeData['url'], 'protected.podopshost.com') )
                    continue; // Skip media hosted on podops in this case

				if( !$clean )
					$return[$meta_id] = $row;
				if( !$exclude_podops )
					$return[$meta_id]['on_podops'] = ( preg_match('/(ins|protected|content)\.podops\.com/i',$EpisodeData['url']) == 1 );
				$return[$meta_id]['src_url'] = $EpisodeData['url'];
			}
		}
		return $return;
}


function powepress_admin_migrate_add_urls($urls)
{
    $Settings = get_option('empowercast_general');
    $creds = get_option('empowercast_creds');
    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
    $auth = new EmpowerCastAuth();
	if( empty($Settings['podops_auth']) && !$creds )
	{
		empowercast_page_message_add_error( sprintf(__('You must have a podops Podcast Hosting account to continue.', 'empowercast')) .' '. '<a href="https://podopshost.com/services/podcast-hosting/" target="_blank">'. __('Learn More', 'empowercast') .'</a>', 'inline', false );
		return false;
	}
	
	$PostArgs = array('urls'=>$urls);
	
	$json_data = false;
	$api_url_array = empowercast_get_api_array();
    if (is_plugin_active('empowercast-hosting/empowercast-hosting.php')) {
        $website_detection_string = "?wp_podops_hosted=true";
    } else {
        $website_detection_string = "?wp_admin_url=" . urlencode(admin_url());
    }
    if ($creds) {
        $accessToken = empowercast_getAccessToken();
        $req_url = sprintf('/2/media/%s/migrate_add.json%s', urlencode($Settings['podops_program_keyword']), $website_detection_string);
        $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA')?'&'. EMPOWERCAST_PODOPS_API_QSA:'');
        $results = $auth->api($accessToken, $req_url, $PostArgs);
    } else {
        foreach ($api_url_array as $index => $api_url) {
            $req_url = sprintf('%s/media/%s/migrate_add.json%s', rtrim($api_url, '/'), urlencode($Settings['podops_program_keyword']), $website_detection_string);
            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');

            $json_data = empowercast_remote_fopen($req_url, $Settings['podops_auth'], $PostArgs);
            if (!$json_data && $api_url == 'https://api.podopshost.com/') { // Lets force cURL and see if that helps...
                $json_data = empowercast_remote_fopen($req_url, $Settings['podops_auth'], $PostArgs, 15, false, true);
            }
            if ($json_data != false)
                break;
        }
        $results = empowercast_json_decode($json_data);

        if (empty($results)) {
            $results = array();
            $results['error'] = __('Unknown error occurred decoding results from server.', 'empowercast');
        }
    }

    if( !empty($results['error']) )
    {
        $error = __('Podops Migrate Media Error', 'empowercast') .': '. $results['error'];
        empowercast_page_message_add_error($error);
        return false;
    } else if( empty($results) )
	{
		if( !empty($GLOBALS['g_empowercast_remote_errorno']) && $GLOBALS['g_empowercast_remote_errorno'] == 401 )
			$error =  __('Incorrect sign-in email address or password.', 'empowercast') .' '. __('Verify your account settings then try again.', 'empowercast');
		else if( !empty($GLOBALS['g_empowercast_remote_error']) )
			$error = $GLOBALS['g_empowercast_remote_error'];
		else
			$error = __('Authentication failed.', 'empowercast');
		empowercast_page_message_add_error($error);
		return false;
	}

    // unlikely to be necessary for most sites
    // i discovered in testing that this value needs cleared and won't automatically overwrite
    // in the event of someone doing another migration for a different program
    $prev_saved_val = get_option('podops_manage_media');
    if ($prev_saved_val) {
        delete_option('podops_manage_media');
    }

    if (!empty($results['publisher'])) {
        add_option('podops_manage_media', $results['publisher']);
    } else {
        add_option('podops_manage_media', 'https://publish.podopshost.com/');
    }

	return $results;
}


function empowercast_admin_migrate_get_status()
{
	$Settings = get_option('empowercast_general');
    $creds = get_option('empowercast_creds');
    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
    $auth = new EmpowerCastAuth();
	if( empty($Settings['podops_auth']) && !$creds )
	{
		empowercast_page_message_add_error( sprintf(__('You must have a podops Podcast Hosting account to continue.', 'empowercast')), 'inline', false );
		return false;
	}
	
	
	$json_data = false;
	$api_url_array = empowercast_get_api_array();
    if ($creds) {
        $accessToken = empowercast_getAccessToken();
        $req_url = sprintf('/2/media/%s/migrate_status.json?status=summary&simple=true', urlencode($Settings['podops_program_keyword']));
        $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA')?'?'. EMPOWERCAST_PODOPS_API_QSA:'');
        $results = $auth->api($accessToken, $req_url);
    } else {
        foreach ($api_url_array as $index => $api_url) {
            $req_url = sprintf('%s/media/%s/migrate_status.json?status=summary&simple=true', rtrim($api_url, '/'), $Settings['podops_program_keyword']);
            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
            $json_data = empowercast_remote_fopen($req_url, $Settings['podops_auth']);
            if ($json_data != false)
                break;
        }

        if (!$json_data) {
            $error = '';
            if (!empty($GLOBALS['g_empowercast_remote_errorno']) && $GLOBALS['g_empowercast_remote_errorno'] == 401)
                $error = __('Incorrect sign-in email address or password.', 'empowercast') . ' ' . __('Verify your account settings then try again.', 'empowercast');
            else if (!empty($GLOBALS['g_empowercast_remote_error']))
                $error = $GLOBALS['g_empowercast_remote_error'];
            else
                $error = __('Authentication failed.', 'empowercast');
            empowercast_page_message_add_error($error);
            return false;
        }
        //mail('cio', 'ok', $json_data);
        $results = empowercast_json_decode($json_data);

        if (empty($results)) {
            $results = array();
            $results['error'] = __('Unknown error occurred decoding results from server.', 'empowercast');
        }
    }
	
	if( !empty($results['error']) )
	{
		$error = __('Podops Migrate Media Error', 'empowercast') .': '. $results['error'];
		empowercast_page_message_add_error($error);
		return false;
	}
	
	return $results;
}


function empowercast_admin_migrate_get_migrated_by_status($status='migrated')
{
	$Settings = get_option('empowercast_general');
    $creds = get_option('empowercast_creds');
    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
    $auth = new EmpowerCastAuth();
	if( empty($Settings['podops_auth']) && !$creds )
	{
		empowercast_page_message_add_error( sprintf(__('You must have a podops Podcast Hosting account to continue.', 'empowercast')), 'inline', false );
		return false;
	}
	
	
	$json_data = false;
	$api_url_array = empowercast_get_api_array();
    if ($creds) {
        $accessToken = empowercast_getAccessToken();
        $req_url = sprintf('/2/media/%s/migrate_status.json?status=%s&limit=10000&simple=true', urlencode($Settings['podops_program_keyword']), urlencode($status));
        $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA')?'?'. EMPOWERCAST_PODOPS_API_QSA:'');
        $results = $auth->api($accessToken, $req_url);
    } else {
        foreach ($api_url_array as $index => $api_url) {
            $req_url = sprintf('%s/media/%s/migrate_status.json?status=%s&limit=10000&simple=true', rtrim($api_url, '/'), $Settings['podops_program_keyword'], urlencode($status));
            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
            $json_data = empowercast_remote_fopen($req_url, $Settings['podops_auth']);
            if ($json_data != false)
                break;
        }

        if (!$json_data) {
            if (!empty($GLOBALS['g_empowercast_remote_errorno']) && $GLOBALS['g_empowercast_remote_errorno'] == 401)
                $error = __('Incorrect sign-in email address or password.', 'empowercast') . ' ' . __('Verify your account settings then try again.', 'empowercast');
            else if (!empty($GLOBALS['g_empowercast_remote_error']))
                $error = '<p>' . $GLOBALS['g_empowercast_remote_error'];
            else
                $error = __('Authentication failed.', 'empowercast');
            empowercast_page_message_add_error($error);
            return false;
        }

        $results = empowercast_json_decode($json_data);
        if (empty($results)) {
            $error = __('Unknown error occurred decoding results from server.', 'empowercast');
            empowercast_page_message_add_error($error);
            return false;
        }
    }
	if( !empty($results['error']) )
	{
		$error = __('Podops Migrate Media Error', 'empowercast') .': '. $results['error'];
		empowercast_page_message_add_error($error);
		return false;
	}
	
	return $results;
}


// Handle POST/GET page requests here
function empowercast_admin_migrate_request()
{
	if( !empty($_GET['migrate_step']) )
	{
		switch( $_GET['migrate_step'] )
		{
			case 1: {
				$GLOBALS['empowercast_migrate_stats'] = empowercast_admin_extension_counts();
			}; break;
			
		}
	}
	
	if( !empty($_POST['migrate_action']) )
	{
		check_admin_referer('empowercast-migrate-media');
		
		switch($_POST['migrate_action'])
		{
			case 'queue_episodes': {
				
				if( !empty($_POST['Migrate']) )
				{
					empowercast_admin_queue_files($_POST['Migrate']);
						
					// Else error message handled in function called above
				}
			}; break;
			case 'update_episodes': { // <input type="hidden" name="migrate_action" value="update_episodes" />
				
				$MigrateResultsPrevious = get_option('empowercast_migrate_results');
				$add_option = false;
				if( $MigrateResultsPrevious == false )
					$add_option = true;
				unset($MigrateResultsPrevious); // Free up the memory
				
				//$URLs = empowercast_admin_migrate_get_migrated_by_status('completed');
				$URLs = empowercast_admin_migrate_get_migrated_by_status('all');
				if( !empty($URLs) )
				{
					$URLs['updated_timestamp'] = current_time( 'timestamp' );
					
					if( $add_option )
						add_option('empowercast_migrate_results', $URLs, '', 'no'); // Make sure it is not auto loaded
					else 
						update_option('empowercast_migrate_results', $URLs);
					
					if( !empty($URLs['results']) )
					{
						$update_option = true;
						$CompletedResults = get_option('empowercast_migrate_completed');
						if( $CompletedResults == false )
							$update_option = false;
						if( empty($CompletedResults['completed_count']) )
							$CompletedResults['completed_count'] = 0;
						if( empty($CompletedResults['error_count']) )
							$CompletedResults['error_count'] = 0;
						if( empty($GLOBALS['g_empowercast_verify_failed_count']) )
							$GLOBALS['g_empowercast_verify_failed_count'] = 0;
						if( empty($GLOBALS['g_empowercast_already_migrated']) )
							$GLOBALS['g_empowercast_already_migrated'] = 0;
						if( empty($GLOBALS['g_empowercast_total_files_found']) )
							$GLOBALS['g_empowercast_total_files_found'] = 0;
						if( empty($GLOBALS['g_empowercast_update_errors']) )
							$GLOBALS['g_empowercast_update_errors'] = 0;
						$QueuedEpisodes = get_option('empowercast_migrate_queued'); // Array of key meta_id => URL value pairs
						
						$FoundCount = 0;
						if( !empty($QueuedEpisodes) )
						{
							foreach( $URLs['results'] as $index => $row )
							{
								if( $row['status'] != 'completed' ) // Not migrated
									continue;
								
								$source_url = $row['source_url'];
								$new_url = $row['new_url'];
								$found = array_keys($QueuedEpisodes, $source_url);
								
								if( empty($found) )
								{
									continue; // Nothing found here
								}
								
								$FoundCount++;
								$GLOBALS['g_empowercast_total_files_found']++;
								
								foreach( $found as $null => $meta_id )
								{
									// Get the post meta
									$meta_object = get_metadata_by_mid('post', $meta_id);
									if( !is_object($meta_object) )
										continue; // Weird
										
									$meta_data = $meta_object->meta_value;
									
									$parts = explode("\n", $meta_data, 2);
									$other_meta_data = false;
									if( count($parts) == 2 )
										list($current_url, $other_meta_data) = $parts;
									else
										$current_url = trim($meta_data);
									
									$current_url = trim($current_url);
									
									// We already migrated this one, or it was modified anyway
									if( $source_url != $current_url )
									{
										//echo "$source_url != $current_url ";
										$GLOBALS['g_empowercast_already_migrated']++;
										continue;
									}
									
									// Verify the URL:
									if( !empty($_POST['EmpowerCastVerifyURLs']) )
									{
										$verified= empowercast_admin_verify_url($new_url);
										if( !empty($verified['error']) )
										{
											// TODO: Handle the error here...
											$GLOBALS['g_empowercast_verify_failed_count']++;
											continue;
										}
									}
									
									$new_meta_data = $new_url;
									if( $other_meta_data )
										$new_meta_data .= "\n". $other_meta_data;
								
									// save the new URL
									if( update_metadata_by_mid( 'post', $meta_id, $new_meta_data) )
									{
										$CompletedResults['completed_count']++;
										$CompletedResults['results'][ $meta_id ] = $new_url;
									}
									else
									{
										$CompletedResults['error_count']++;
										$GLOBALS['g_empowercast_update_errors']++;
									}
								}
							}
							
							if( $CompletedResults['completed_count'] > 0 )
							{
								if( $update_option )
									update_option('empowercast_migrate_completed', $CompletedResults);
								else
									add_option('empowercast_migrate_completed', $CompletedResults, '', 'no'); // Make sure we are not preloading 
								empowercast_page_message_add_notice( sprintf(__('Episodes updated successfully.', 'empowercast')) );
								return;
							}
							
							empowercast_page_message_add_notice( sprintf(__('No Episodes updated. Please see results.', 'empowercast')) );
							return;
						}
					}
					else
					{
						empowercast_page_message_add_notice(  sprintf(__('No episodes updated.', 'empowercast')) );
					}
				}

                // delete the cron task that check for the migration status
                $timestamp = wp_next_scheduled( 'empowercast_admin_migration_hook' );
                wp_unschedule_event( $timestamp, 'empowercast_admin_migration_hook' );
			}; break;
		}
	}
	
	if( !empty($_GET['migrate_action']) )
	{
		check_admin_referer('empowercast-migrate-media');
		
		switch($_GET['migrate_action'])
		{
			case 'reset_migrate_media': {
				delete_option('empowercast_migrate_completed');
				delete_option('empowercast_migrate_queued');
				delete_option('empowercast_migrate_status');
				delete_option('empowercast_migrate_results');
				empowercast_page_message_add_notice(  sprintf(__('Media migration reset successfully.', 'empowercast')) );
			}; break;
		}
	}
}

function empowercast_admin_extension_counts()
{
	$files = empowercast_admin_migrate_get_files(true, false);
	$extensions = array(); // 'podops'=>0, 'mp3'=>0, 'm4a'=>0, 'mp4'=>0, 'm4v'=>0, '*'=>0 );
	foreach( $files as $meta_id => $row )
	{
		$extension = '*';
			
		$parts = pathinfo($row['src_url']);
		if (empty($parts['extension'])) {
		    continue;
        }
		if (strpos($parts['extension'], '?') !== false) {
		    $ext_query_string = explode('?', $parts['extension']);
		    $parts['extension'] = $ext_query_string[0];
        }
		if( preg_match('/(mp3|m4a|mp4|m4v|mov)/i', $parts['extension']) )
			$extension = strtolower($parts['extension']);
			
		if( !empty($row['on_podops']) )
			$extension = 'podops';
			
		if( empty($extensions[ $extension ]) )
			$extensions[ $extension ] = 0;
		
		$extensions[ $extension ]++;
	}
	return $extensions;
}

function empowercast_admin_queue_files($extensions=array() )
{
	$add_urls = '';
	$extensions_preg_match = '';
	foreach( $extensions as $extension => $null )
	{
		if( $extension == '*' )
		{
			$extensions_preg_match = '.*';
			break; // Lets just match everything
		}
		if( !empty($extensions_preg_match) )
			$extensions_preg_match .= '|';
		$extensions_preg_match .= preg_quote($extension);
	}
	
	if( empty($extensions_preg_match) )
	{
		// No files specified, no error message needed
		return;
	}
	
	$files = empowercast_admin_migrate_get_files(true, true); // Keep the URLs clean, excude podops media URLs
	
	$QueuedFiles = array();
	$Update = false;
	$update_option = true;
	$PastResults = get_option('empowercast_migrate_queued');
	if( $PastResults == false )
		$update_option = false;
	if( is_array($PastResults) )
		$QueuedFiles = $PastResults;
	$AddedCount = 0;;
	$AlreadyAddedCount = 0;
	
	foreach( $files as $meta_id => $row )
	{
		$parts = pathinfo($row['src_url']);
        if (empty($parts['extension'])) {
            continue;
        }
        if (strpos($parts['extension'], '?') !== false) {
            $ext_query_string = explode('?', $parts['extension']);
            $parts['extension'] = $ext_query_string[0];
        }
		if( preg_match('/('.$extensions_preg_match.')/i', $parts['extension']) )
		{
			if( !empty($QueuedFiles[ $meta_id ]) && $QueuedFiles[ $meta_id ] == $row['src_url'] )
			{
				$AlreadyAddedCount++;
				continue; // Already queued
			}
			
			$QueuedFiles[ $meta_id ] = $row['src_url'];
			if( !empty($add_urls ) )
				$add_urls .= "\n";
			$add_urls .= $row['src_url'];
			$Update = true;
			$AddedCount++;
		}
	}
	
	if( $Update )
	{
		// Make API CALL to add files to queue here!
		$UpdateResults = powepress_admin_migrate_add_urls( $add_urls );
	
		if( empty($UpdateResults) )
			$Update = false;
	}
	
	if( $Update )
	{
		// IF the API call was successful, lets save the list locally
		if( $update_option )
			update_option('empowercast_migrate_queued', $QueuedFiles);
		else
			add_option('empowercast_migrate_queued', $QueuedFiles, '', 'no');
		empowercast_page_message_add_notice( sprintf(__('%d media files added to migration queue.', 'empowercast'), $AddedCount) );
	}
	
	if( $AlreadyAddedCount  > 0 )
	{
		empowercast_page_message_add_notice( sprintf(__('%d media files were already added to migration queue.', 'empowercast'), $AlreadyAddedCount) );
	}



    if( !wp_next_scheduled('empowercast_admin_migration_hook')) {
        add_action( 'empowercast_admin_migration_hook', 'empowercast_admin_migration_cron' );
        wp_schedule_event(time(), 'hourly', 'empowercast_admin_migration_hook');
    }
}


function empowercast_admin_migrate_step1()
{
	// Use check_admin_referer('empowercast-migrate-media');  when handling this post request
?>
<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page=empowercast/empowercastadmin_migrate.php'); ?>">
<?php wp_nonce_field('empowercast-migrate-media'); ?>
<input type="hidden" name="action" value="empowercast-migrate-media" />
<input type="hidden" name="migrate_action" value="queue_episodes" />
<h2><?php echo __('Migrate Media to your Podops Podcast Media Hosting Account', 'empowercast'); ?></h2>

    <div id="empowercast_steps" class="pp-migrate-container">
        <div class="pp-migrate-container-heading">
            <h1><div class="empowercast-step-blue">1</div><?php echo __('Select Media to Migrate', 'empowercast'); ?></h1>
        </div>
        <div class="pp-migrate-content">

<ul>
<?php 
	if( count($GLOBALS['empowercast_migrate_stats']) == 0 )
	{
	?>
	<li>
	<?php echo __('No media found to migrate', 'empowercast'); ?>
	</li>
	<?php
	}
	$types = array('mp3', 'm4a', 'mp4', 'm4v', 'mov', '*', 'podops');
	foreach( $types as $null => $extension )
	{
		if( empty($GLOBALS['empowercast_migrate_stats'][$extension]) )
			continue;
		$count = $GLOBALS['empowercast_migrate_stats'][$extension];
		$checked = ' checked';
		switch( $extension )
		{
			case 'mp3': $label = __('mp3 audio files', 'empowercast'); break;
			case 'm4a': $label = __('m4a audio files', 'empowercast'); break;
			case 'mp4': $label = __('mp4 video files', 'empowercast'); break;
			case 'm4v': $label = __('m4v video files', 'empowercast'); break;
			case 'mov': $label = __('mov video files', 'empowercast'); break;
			case 'podops': $label = __('media hosted by Podops', 'empowercast'); break;
			default: $label = __('Other media formats', 'empowercast'); $checked = '';
		}
		
	?>
	<li>
	<?php if( $extension == 'podops' ) { ?>
        <h4><input type="checkbox" name="NULL[<?php echo $extension; ?>]" value="0" disabled /><?php echo $label; ?>
	<?php } else { ?>
        <h4><input type="checkbox" name="Migrate[<?php echo $extension; ?>]" value="1" <?php echo $checked; ?> />  <?php echo $label; ?>
	<?php } ?>
	<?php echo sprintf( __('(%d found)', 'empowercast'), $count); ?></h4>
	</li>
<?php } ?>
</ul>
<?php
	if( count($GLOBALS['empowercast_migrate_stats']) )
?><p class="submit"><button type="submit" class="pp_button" name="Submit"><span><?php echo __('Request Migration', 'empowercast'); ?></span></button></p><?php
?>
        </div></div>
</form>

<p style="margin-bottom: 40px;">&#8592;  <a href="<?php echo admin_url( 'admin.php?page=empowercast/empowercastadmin_migrate.php'); ?>"><?php echo __('Migrate Media', 'empowercast'); ?></a></p>
<?php
}

function empowercast_admin_migrate_find_in_results(&$results, $src_url)
{
	$found = false;
	foreach( $results as $index => $row )
	{
		if( $row['source_url'] == $src_url )
		{
			$found = $row;
			break;
		}
	}
	reset($results);
	
	return $found;
}

function empowercast_admin_migrate_step2($QueuedResults, $MigrateStatus, $CompletedResults)
{
	$update_episodes = false;
	$count = count($QueuedResults);
	$MigrateResults = get_option('empowercast_migrate_results');
	// allow a refresh every three minutes (was previously every thirty, but now we're allowing them to check details while migration is in progress, so we'll need to update more often)
	if( empty($MigrateResults['updated_timestamp']) || $MigrateResults['updated_timestamp']  < ( current_time( 'timestamp') - (60*3) ) )
	{
		$update_option = true;
		if( $MigrateResults == false )
			$update_option = false;
			
		$MigrateResults = empowercast_admin_migrate_get_migrated_by_status('all');
		$MigrateResults['updated_timestamp'] = current_time( 'timestamp');
		if( $update_option )
			update_option('empowercast_migrate_results', $MigrateResults );
		else
			add_option('empowercast_migrate_results', $MigrateResults, '', 'no');
	}
	
	$CompletedResults = get_option('empowercast_migrate_completed');
?>
<h2><?php echo __('Migrate Media to your Podops Podcast Media Hosting Account', 'empowercast'); ?></h2>


<div id="empowercast_single_step">
	<h3><?php echo __('List of requested media', 'empowercast'); ?></h3>
</div>
<!-- <p><?php echo sprintf( __('%d files requested', 'empowercast'), $count); ?></p> -->
<?php if( !empty($MigrateStatus['queued']) && false ) { ?><p><?php echo sprintf( __('%d files in queue', 'empowercast'), $MigrateStatus['queued']); ?></p><?php } ?>
<?php if( !empty($MigrateStatus['completed']) && false ) { ?><p><?php echo sprintf( __('%d migrated files available', 'empowercast'), $MigrateStatus['completed']); ?></p><?php } ?>
<?php if( !empty($MigrateStatus['skipped']) && false ) { ?><p><?php echo sprintf( __('%d skipped', 'empowercast'), $MigrateStatus['skipped']); ?></p><?php } ?>
<?php if( !empty($MigrateStatus['failed']) && false ) { ?><p><?php echo sprintf( __('%d failed', 'empowercast'), $MigrateStatus['failed']); ?></p><?php } ?>
<?php if( !empty($CompletedResults['completed_count']) && false ) { ?><p><?php echo sprintf( __('%d episodes updated', 'empowercast'), $CompletedResults['completed_count']); ?></p><?php } ?>
<style type="text/css">
table.empowercast-migration-table {
	min-width: 80%;
}
table.empowercast-migration-table th {
	text-align: left;
}
table.empowercast-migration-table td {
	padding-left: 6px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    max-width: 400px;
}
table.empowercast-migration-table tr:hover {
	background-color: #DDDDDD;
}
.empowercast-migrate-e,
.empowercast-migrate-s {
	width: 18%;
}
</style>
<table class="empowercast-migration-table">
 <tr>
	<th class="empowercast-migrate-f"><?php echo __('File', 'empowercast'); ?></th>
	<th class="empowercast-migrate-s"><?php echo __('Migration Status', 'empowercast'); ?></th>
	<th class="empowercast-migrate-e"><?php echo __('Episode Updated', 'empowercast'); ?></th>
 </tr>
<?php
	foreach( $QueuedResults as $meta_id => $url )
	{
		$status = __('Requested', 'empowercast');
		$updated = '-';
		$file = basename($url);
		
		if( !empty($CompletedResults['results'][$meta_id]) )
		{
			$found = array('status'=>'completed', 'new_url'=>$CompletedResults['results'][$meta_id]);
			$updated = __('Yes', 'empowercast');
		}
		else
		{
			$found = empowercast_admin_migrate_find_in_results($MigrateResults['results'], $url );
		}
		
		if( !empty($found['status']) )
		{
			switch($found['status'])
			{
				case 'completed': { 
					$status = __('Completed', 'empowercast');
					if( empty($CompletedResults['results'][$meta_id]) )
					{
						$updated = __('No', 'empowercast');
						$update_episodes = true;
					}
				}; break;
				case 'skipped': $status = __('Skipped', 'empowercast'); break;
				case 'error': $status = __('Error', 'empowercast'); break;
			}
		}
?>
 <tr>
	<td><?php echo htmlspecialchars($file); ?> &nbsp;</td>
	<td><?php echo $status; ?></td>
	<td><?php echo $updated; ?></td>
 </tr>
<?php
	}
?>
</table>


<?php
}

function empowercast_admin_migrate_step3($MigrateStatus, $CompletedResults)
{
	// $MigrateStatus['completed']
?>
<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page=empowercast/empowercastadmin_migrate.php'); ?>">
<?php wp_nonce_field('empowercast-migrate-media'); ?>
<input type="hidden" name="action" value="empowercast-migrate-media" />
<input type="hidden" name="migrate_action" value="update_episodes" />
<h2><?php echo __('Migrate Media to your Podops Podcast Media Hosting Account', 'empowercast'); ?></h2>

<div id="empowercast_single_step">
	<h3><?php echo __('Step 3', 'empowercast'); ?> - <?php echo __('Update your Episodes', 'empowercast'); ?></h3>
</div>


<?php if( !empty($MigrateStatus['completed']) ) { ?><p><?php echo sprintf( __('%d migrated files available', 'empowercast'), $MigrateStatus['completed']); ?></p><?php } ?>
<?php if( !empty($CompletedResults['completed_count']) ) { ?><p><?php echo sprintf( __('%d episodes updated', 'empowercast'), $CompletedResults['completed_count']); ?></p><?php } ?>
<p><?php echo __('', 'empowercast'); ?></p>

<p style="margin: 30px 0;"><?php echo __('We recommend backing up your database before proceeding.', 'empowercast'); ?></p>


<p class="submit">
	<input type="submit" name="Submit" id="empowercast_save_button" class="button-primary button-podops" value="<?php echo __('Update Episodes', 'empowercast'); ?>" onclick="" />
	&nbsp;
	<input type="checkbox" name="EmpowerCastVerifyURLs" value="1" checked />
	<strong><?php echo __('Verify URLs', 'empowercast'); ?></strong>
		(<?php echo __('Does not change URL if invalid', 'empowercast'); ?>)</p>
</p>


</form>
<p style="margin-bottom: 40px;">&#8592;  <a href="<?php echo admin_url( 'admin.php?page=empowercast/empowercastadmin_migrate.php'); ?>"><?php echo __('Migrate Media', 'empowercast'); ?></a></p>
<?php
}


function empowercast_admin_migrate()
{
    add_thickbox();
	$General = empowercast_get_settings('empowercast_general');
	$files = empowercast_admin_migrate_get_files();

	// styles for create episode and PP settings buttons
    if (defined('WP_DEBUG')) {
        if (WP_DEBUG) {
            wp_enqueue_style('empowercast_onboarding_styles', plugin_dir_url(__FILE__) . 'css/onboarding.css', array(), EMPOWERCAST_VERSION);
        } else {
            wp_enqueue_style('empowercast_onboarding_styles', plugin_dir_url(__FILE__) . 'css/onboarding.min.css', array(), EMPOWERCAST_VERSION);
        }
    } else {
        wp_enqueue_style('empowercast_onboarding_styles', plugin_dir_url(__FILE__) . 'css/onboarding.min.css', array(), EMPOWERCAST_VERSION);
    }

	if( !empty($_REQUEST['migrate_step']) && $_REQUEST['migrate_step'] == 1 )
	{
		empowercast_admin_migrate_step1();
		return;
	}
	
	$Step = 0;
	$RequestedCount = 0;
	$PodopsQueuedCount = 0;
	$MigratedCount = 0;
	$FailedCount = 0;
	$SkippedCount = 0;
	$QueuedResults = get_option('empowercast_migrate_queued');
	if( is_array($QueuedResults) )
	{
		$RequestedCount = count($QueuedResults);
		if( $RequestedCount  > 0 )
			$Step = 1;
	}

	$MigrateStatus = false;
	if( $Step >= 1 || !empty($_GET['refresh_migrate_status']) )
	{
		$MigrateStatus = get_option('empowercast_migrate_status');
		if( empty($MigrateStatus) || $MigrateStatus['updated_timestamp'] < current_time('timestamp')-(60*30) || !empty($_GET['refresh_migrate_status']) ) // Check every 30 minutes
		{
			$update_option = true;
			if( $MigrateStatus == false )
				$update_option = false;
			
			$MigrateStatus = empowercast_admin_migrate_get_status();
			if( is_array($MigrateStatus) )
			{
				$MigrateStatus['updated_timestamp'] = current_time( 'timestamp' );
				if( $update_option )
					update_option('empowercast_migrate_status', $MigrateStatus);
				else
					add_option('empowercast_migrate_status', $MigrateStatus, '', 'no' );
			}
		}
	}
	
	if( !empty($MigrateStatus['completed']) )
	{
		$Step = 3;
		$MigratedCount = $MigrateStatus['completed'];
	}
	if( !empty($MigrateStatus['failed']) )
	{
		$FailedCount = $MigrateStatus['failed'];
	}
	if( !empty($MigrateStatus['skipped']) )
	{
		$SkippedCount = $MigrateStatus['skipped'];
	}
	if( !empty($MigrateStatus['queued']) )
	{
		$PodopsQueuedCount = $MigrateStatus['queued'];
	}

	$CompletedResults = get_option('empowercast_migrate_completed');
	
	if( !empty($_REQUEST['migrate_step']) && $_REQUEST['migrate_step'] == 2 && $Step > 0 )
	{
		empowercast_admin_migrate_step2($QueuedResults, $MigrateStatus, $CompletedResults);
		return;
	}

	if( !empty($_REQUEST['migrate_step']) && $_REQUEST['migrate_step'] == '3' && $Step == 3 )
	{
		empowercast_admin_migrate_step3($MigrateStatus, $CompletedResults);
		return;
	}

	// If we have empowercast credentials, check if the account has been verified
    $creds = get_option('empowercast_creds');
    empowercast_check_credentials($creds);
    wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );

	if( !empty($CompletedResults['completed_count']) )
	{
		$CompletedCount = $CompletedResults['completed_count'];
	}
	
?>
    <div id="pp-migrate-page">
<?php empowercast_page_message_print(); ?>
<?php
	if( !empty($GLOBALS['g_empowercast_verify_failed_count']) )
	{
		echo '<p>';
		echo sprintf(__('%d urls failed verification.', 'empowercast'), $GLOBALS['g_empowercast_verify_failed_count']);
		echo '</p>';
	}
						
	if( !empty($GLOBALS['g_empowercast_total_files_found']) )
	{
		echo '<p>';
		echo sprintf(__('%d migrated files found on this site.', 'empowercast'), $GLOBALS['g_empowercast_total_files_found']);
		echo '</p>';
	}
	
	if( !empty($GLOBALS['g_empowercast_already_migrated']) )
	{
		echo '<p>';
		echo sprintf(__('%d episodes already updated with new URLs.', 'empowercast'), $GLOBALS['g_empowercast_already_migrated']);
		echo '</p>';
	}
	
	if( !empty($GLOBALS['g_empowercast_update_errors']) )
	{
		echo '<p>';
		echo sprintf(__('%d update errors.', 'empowercast'), $GLOBALS['g_empowercast_update_errors']);
		echo '</p>';
	}
?>

        <h1 style="margin-bottom: 1em;"><b><?php echo __('MIGRATION', 'empowercast'); ?></b></h1>

    <?php

    if (empty($RequestedCount) && empty($PodopsQueuedCount) && empty($MigratedCount) && empty($CompletedCount)) {

        $GLOBALS['empowercast_migrate_stats'] = empowercast_admin_extension_counts();

    $pp_nonce = empowercast_login_create_nonce();
    ?>

        <div id="pp-migration-marketing" class="pp-migrate-container">
            <div class="pp-migrate-content">
                <h4><b><?php echo __('Transfer media files to your Podops hosting account', 'empowercast'); ?></b></h4>
                <div style="margin-top: 1em;">
                    <img src="<?php echo empowercast_get_root_url(); ?>images/onboarding/hosting_icon.png" alt="" class="pp-migration-image" />
                    <h4 class="pp-migrate-subtitle"><?php echo __('This makes it easy to publish episodes right from your own site. Hosting customers enjoy integrated publishing features, such as stats with EmpowerCast.', 'empowercast'); ?>
                        <?php // If not hosting
                        if( empty($General['podops_hosting']) || $General['podops_hosting'] === 'false' ) { ?>
                            <br />
                            <a href="<?php echo esc_attr(add_query_arg( '_wpnonce', $pp_nonce, admin_url("admin.php?page=empowercastadmin_onboarding.php&step=podopsSignup&onboarding_type=hosting"))); ?>">
                                <?php echo __('Try Podops hosting FREE', 'empowercast'); ?>
                            </a>
                        <?php } ?>
                    </h4>
                </div>
            </div>
        </div>

        <div id="empowercast_steps" class="pp-migrate-container">
            <div class="pp-migrate-container-heading">
                <h1><div class="empowercast-step-blue">1</div><?php echo __('Migrate Media', 'empowercast'); ?></h1>
            </div>
            <div class="pp_flex-grid pp-migrate-content">
                <div class="empowercast-step pp_col" id="powerpreess_step_1a">
                    <?php
                    $file_count = 0;
                    foreach($GLOBALS['empowercast_migrate_stats'] as $ext => $count) {
                        $file_count += $count;
                    }?>
                    <h1>
                        <?php echo $file_count . ' ' . __('episode files found', 'empowercast'); ?>
                    </h1>

                    <div style="margin-top: 1em;">
                        <a href="<?php echo admin_url("admin.php?page=empowercast/empowercastadmin_migrate.php&amp;action=empowercast-migrate-media&amp;migrate_step=1"); ?>">
                            <button type="button" class="pp_button"><span><?php echo __('START MIGRATION', 'empowercast'); ?></span></button>
                        </a>
                    </div>

                </div>
                <div class="empowercast-step pp_col divider-left" id="powerpreess_step_1b">
                    <h2><b><?php echo __('What is this?', 'empowercast'); ?></b></h2>
                    <p><?php echo __('We locate the files we are able to transfer to your new account.', 'empowercast'); ?></p>
                    <br />
                    <p class="migrate-status-red"><?php echo __('Please complete migration before deleting the source of your files.', 'empowercast'); ?></p>
                </div>
            </div>
        </div>
    <?php }
    if(  !empty($PodopsQueuedCount) && empty($CompletedCount)  ) { ?>

    <div id="pp-migration-marketing" class="pp-migrate-container">
        <div class="pp-migrate-content">
            <h4><b><?php echo __('Looking for private or premium hosting?', 'empowercast'); ?></b></h4>
            <div style="margin-top: 1em;">
                <img src="<?php echo empowercast_get_root_url(); ?>images/onboarding/pip_logo_rbg.png" alt="" class="pp-migration-image" />
                <h4 class="pp-migrate-subtitle"><?php echo __('Our private internal podcasting option makes it simple to offer secure and restricted content. Available for desktop and app streaming only.', 'empowercast'); ?>
                        <br />
                        <a href="https://podopshost.com/services/private-internal-podcasting/">
                            <?php echo __('Learn more here', 'empowercast'); ?>
                        </a>
                </h4>
            </div>
        </div>
    </div>

    <div id="empowercast_steps" class="pp-migrate-container">
        <div class="pp-migrate-container-heading">
            <h1><div class="empowercast-step-blue">2</div><?php echo __('Migration Status', 'empowercast'); ?></h1>
        </div>
        <div class="pp_flex-grid pp-migrate-content">
            <div class="empowercast-step pp_col" style="text-align: center;">
                <img alt="Step 2" src="<?php echo empowercast_get_root_url() . 'images/onboarding/migration_bird.png' ?>"/>
                <img class="loading" alt="Migrating media..." src="<?php echo empowercast_get_root_url() . 'images/onboarding/loading_bar.gif' ?>"/>
                <h4 class="migrate-status-yellow">
                    <?php echo __('Migrating...', 'empowercast'); ?>
                    </h4>
                <br />
                <h2 class="migrate-status-blue"><?php echo sprintf(__('%d files migrated', 'empowercast'), $MigratedCount); ?></h2>
                <div class="pp_flex-grid" style="justify-content: space-around">
                <?php if ($PodopsQueuedCount) { ?><h4>
                         <?php echo sprintf(__('%d files in queue', 'empowercast'), $PodopsQueuedCount); ?></h4> <?php } ?>
                <?php if ($FailedCount) { ?><h4 class="migrate-status-red">
                    <?php echo sprintf(__('%d files failed', 'empowercast'), $FailedCount); ?></h4><?php } ?>
                <?php if ($SkippedCount) { ?><h4 class="migrate-status-yellow">
                    <?php echo sprintf(__('%d files skipped', 'empowercast'), $SkippedCount); ?></h4><?php } ?>
                </div>

            </div>
            <div class="empowercast-step pp_col divider-left" id="powerpreess_step_1b">
                <h2><b><?php echo __('How long will this take?', 'empowercast'); ?></b></h2>
                <p><?php echo __('Times may vary, dependent on the number of files. We will notify you once the migration is complete. You can leave this page and check back later.', 'empowercast'); ?></p>
                <div class="pp_flex-grid" style="justify-content: space-around">
                <h4>
                    <a href="<?php echo admin_url("admin.php?page=empowercast/empowercastadmin_migrate.php&amp;action=empowercast-migrate-media&amp;refresh_migrate_status=1"); ?>">
                        <?php echo __('Refresh Page', 'empowercast'); ?>
                    </a>
                </h4>
                <h4>
                    <a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=empowercast-jquery-migrate-queue", 'empowercast-jquery-migrate-queue'); ?>&migrate_step=2&KeepThis=true&TB_iframe=true&modal=false" class="thickbox">
                        <?php echo __('Migration Status Details', 'empowercast'); ?>
                    </a>
                </h4>
                </div>
                <br />
                <p class="migrate-status-red"><?php echo __('Remember, complete the migration before deleting the source of your files.', 'empowercast'); ?></p>
            </div>
        </div>
    </div>
        <style>
            #wpfooter {
                position: relative;
            }
        </style>
        <div id="pp-create-episode" class="pp-migrate-container">
            <div class="pp-migrate-content">
                <section id="one" class="pp_wrapper">
                    <div class="pp_inner">

                        <div class="pp_flex-grid">

                            <div class="pp_col">
                                <div style="border: none; margin-bottom: 0;padding-left: 8px;">
                                    <div>
                                        <div class="pp_button-container" style="margin-bottom: 1em;float: left;">
                                            <a href="<?php echo admin_url('post-new.php') ?>"><button type="button" class="pp_button"><span><?php echo __('Create a new episode', 'empowercast'); ?></span></button></a>
                                        </div>
                                        <div class="btn-caption-container">
                                            <p style="width: 100%; margin-bottom: 1ch; float: left;"><?php echo __('Release a new episode or blog post.', 'empowercast'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pp_col divider-left" style="padding-left: 2.5em;">
                                <div style="border: none; margin-bottom: 0;">
                                    <div>
                                        <div class="pp_button-container" style="margin-bottom: 1em;float: left;">
                                            <a href="<?php echo admin_url('admin.php?page=empowercastadmin_basic') ?>">
                                                <button type="button" class="pp_button"><span><?php echo __('Go to settings', 'empowercast'); ?></span></button>
                                            </a>
                                        </div>
                                        <div class="btn-caption-container">
                                            <p style="float: left;"><?php echo __('Continue with EmpowerCast options for your feed and website.', 'empowercast'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php
    }

    if( empty($PodopsQueuedCount) && !empty($MigratedCount) && empty($CompletedCount) )
    {  ?>

        <div id="pp-migration-marketing" class="pp-migrate-container">
            <div class="pp-migrate-content">
                <h4><b><?php echo __('Advanced Statistics provide unparalleled insight into your show', 'empowercast'); ?></b></h4>
                <div style="margin-top: 1em;">
                    <img src="<?php echo empowercast_get_root_url(); ?>images/onboarding/podops_stats.png" alt="" class="pp-migration-image" />
                    <h4 class="pp-migrate-subtitle"><?php echo __('Discover your listener retention data! Find out how long your audience is listening to your episodes on a regular basis. Included in any hosting plan.', 'empowercast'); ?>
                        <br />
                        <a href="https://secure.podopshost.com/checkout/manage-subscriptions/">
                            <?php echo __('Upgrade to hosting here', 'empowercast'); ?>
                        </a>
                    </h4>
                </div>
            </div>
        </div>


        <div id="empowercast_steps" class="pp-migrate-container">
            <div class="pp-migrate-container-heading">
                <h1><div class="empowercast-step-blue">3</div><?php echo __('Almost Done', 'empowercast'); ?></h1>
            </div>
            <div class="pp_flex-grid pp-migrate-content">
                <div class="empowercast-step pp_col">
                    <h1><?php echo sprintf(__('%d files migrated', 'empowercast'), $MigratedCount); ?></h1>
                    <h4><a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=empowercast-jquery-migrate-queue", 'empowercast-jquery-migrate-queue'); ?>&migrate_step=2&KeepThis=true&TB_iframe=true&modal=false" class="thickbox"><?php echo __('View Results', 'empowercast'); ?></a></h4>
                    <br />
                    <?php if ($FailedCount) { ?><h4 class="migrate-status-red">
                        <?php echo sprintf(__('%d files failed', 'empowercast'), $FailedCount); ?></h4><?php } ?>
                    <form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page=empowercast/empowercastadmin_migrate.php'); ?>">
                        <?php wp_nonce_field('empowercast-migrate-media'); ?>
                        <input type="hidden" name="action" value="empowercast-migrate-media" />
                        <input type="hidden" name="migrate_action" value="update_episodes" />
                        <input type="hidden" name="EmpowerCastVerifyURLs" value="1" />
                        <div class="pp_button-container" style="margin-top: 1em; float: left;">
                            <b><button type="submit" class="pp_button"><span><?php echo __('UPDATE EPISODES', 'empowercast'); ?></span></button></b>
                        </div>
                    </form>
                </div>
                <div class="empowercast-step pp_col divider-left" id="powerpreess_step_1b">
                    <h2><b><?php echo __('What is this?', 'empowercast'); ?></b></h2>
                    <p><?php echo __('This is your last chance to check all of your files have been migrated. If you don’t see any issues, you’re ready to update your episodes with your new Podops media.', 'empowercast'); ?></p>
                </div>

            </div>
        </div>
        <?php } ?>

    <?php if (!empty($CompletedCount) ) {
        $publisher_migration_url = get_option('podops_manage_media');
        if (!$publisher_migration_url) {
            $publisher_migration_url = 'https://publish.podopshost.com/';
        }
        ?>
        <div id="empowercast_steps" class="pp-migrate-container">
            <div class="pp-migrate-container-heading">
                <h1><div class="empowercast-step-blue">4</div><?php echo __('Migration Finished', 'empowercast'); ?></h1>
            </div>
            <div class="pp_flex-grid pp-migrate-content">
                <div class="empowercast-step pp_col">
                    <h1><?php echo sprintf(__('%d files migrated', 'empowercast'), $MigratedCount); ?></h1>
                    <h4>
                        <a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=empowercast-jquery-migrate-queue", 'empowercast-jquery-migrate-queue'); ?>&migrate_step=2&KeepThis=true&TB_iframe=true&modal=false" class="thickbox">
                            <?php echo __('View Migrated List', 'empowercast'); ?>
                        </a>
                    </h4>
                    <?php if ($FailedCount) { ?><br /><h4 class="migrate-status-red">
                        <?php echo sprintf(__('%d files failed', 'empowercast'), $FailedCount); ?></h4><?php } ?>

                    <div class="pp_button-container" style="margin-top: 1em; float: left;">
                        <a href="<?php echo $publisher_migration_url; ?>" target="_blank">
                            <b><button type="button" class="pp_button"><span><?php echo __('GO TO MEDIA LIBRARY', 'empowercast'); ?></span></button></b>
                        </a>
                    </div>
                </div>
                <div class="empowercast-step pp_col divider-left" id="powerpreess_step_1b">
                    <h2><b><?php echo __('Files Missing?', 'empowercast'); ?></b></h2>
                    <p><?php echo __('This is your last chance to check all of your files have been migrated. If you don’t see any issues, you’re ready to update your episodes with your new Podops media.', 'empowercast'); ?></p>
                    <br />
                    <p class="migrate-status-red"><?php echo __('Remember, complete the migration before deleting the source of your files.', 'empowercast'); ?></p>
                    <h4>
                        <b><a href="<?php echo admin_url(); echo wp_nonce_url( "admin.php?page=empowercast/empowercastadmin_migrate.php&amp;migrate_action=reset_migrate_media&amp;action=empowercast-migrate-media", 'empowercast-migrate-media'); ?>" onclick="return confirm('<?php echo esc_js(__('Reset migration, are you sure?','empowercast')); ?>');"><?php echo __('RETRY MIGRATION', 'empowercast'); ?></a></b>
                    </h4>
                </div>

            </div>
        </div>
        <style>
            #wpfooter {
                position: relative;
            }
        </style>
        <div id="pp-create-episode" class="pp-migrate-container">
            <div class="pp-migrate-content">
                <section id="one" class="pp_wrapper">
                    <div class="pp_inner">

                        <div class="pp_flex-grid">

                            <div class="pp_col">
                                <div style="border: none; margin-bottom: 0;padding-left: 8px;">
                                    <div>
                                        <div class="pp_button-container" style="margin-bottom: 1em;float: left;">
                                            <a href="<?php echo admin_url('post-new.php') ?>"><button type="button" class="pp_button"><span><?php echo __('Create a new episode', 'empowercast'); ?></span></button></a>
                                        </div>
                                        <div class="btn-caption-container">
                                            <p style="width: 100%; margin-bottom: 1ch; float: left;"><?php echo __('Release a new episode or blog post.', 'empowercast'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pp_col divider-left" style="padding-left: 2.5em;">
                                <div style="border: none; margin-bottom: 0;">
                                    <div>
                                        <div class="pp_button-container" style="margin-bottom: 1em;float: left;">
                                            <a href="<?php echo admin_url('admin.php?page=empowercastadmin_basic') ?>">
                                                <button type="button" class="pp_button"><span><?php echo __('Go to settings', 'empowercast'); ?></span></button>
                                            </a>
                                        </div>
                                        <div class="btn-caption-container">
                                            <p style="float: left;"><?php echo __('Continue with EmpowerCast options for your feed and website.', 'empowercast'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php } ?>
	<div class="clear"></div>
    </div>
    <div class="clear"></div>
    </div>
<?php
}

/**
 * This function, called hourly by cron, will get the migration status and update the database accordingly.
 *
 * @return void
 */
function empowercast_admin_migration_cron() {
    // do logic to get correct counts and update wp options
    $MigrateStatus = get_option('empowercast_migrate_status');
    if( empty($MigrateStatus) || $MigrateStatus['updated_timestamp'] < current_time('timestamp')-(60*30) || !empty($_GET['refresh_migrate_status']) ) // Check every 30 minutes
    {
        $update_option = $MigrateStatus == false ? false : true;

        $MigrateStatus = empowercast_admin_migrate_get_status();
        if( is_array($MigrateStatus) )
        {
            $MigrateStatus['updated_timestamp'] = current_time( 'timestamp' );
            if( $update_option )
                update_option('empowercast_migrate_status', $MigrateStatus);
            else
                add_option('empowercast_migrate_status', $MigrateStatus, '', 'no' );
        }
    }
}
