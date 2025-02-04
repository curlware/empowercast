<?php
// empowercastadmin-mt.php
	
if( !function_exists('add_action') )
	die("access denied.");
	
	function empowercast_get_mt_episodes()
	{
		global $wpdb;
		
		$return = array();
		$return['feeds_required'] = 0;
		
		$query = "SELECT p.ID, p.post_title, p.post_date, p.post_content ";
		$query .= "FROM {$wpdb->posts} AS p ";
		$query .= "WHERE 1 ";
		$query .= "AND p.post_type != 'revision' ";
		$query .= "ORDER BY p.post_date DESC ";
		
		$results_data = $wpdb->get_results($query, ARRAY_A); // This could return a lot of data...
		if( $results_data )
		{
			foreach( $results_data as $null => $row )
			{
				$Media = empowercastadmin_mt_media_from_content($row['post_content']);
				if( count($Media) == 0 )
					continue;
				
				if( $return['feeds_required'] < count( $Media ) )
					$return['feeds_required'] = count( $Media );
				$return[ $row['ID'] ] = array();
				$return[ $row['ID'] ]['post_title'] = $row['post_title'];
				$return[ $row['ID'] ]['post_date'] = $row['post_date'];
				foreach( $Media as $index => $url )
					$return[ $row['ID'] ]['enclosures'][$index]['url'] = $url;
			}
		}
		return $return;
	}
	
	function empowercastadmin_mt_is_media($url)
	{
		global $g_import_mt_extensions;
		
		$partsURL = @parse_url( trim($url) );
		if( empty($partsURL['path']) )
			return false;
		$filename = substr($partsURL['path'], strrpos($partsURL['path'], '/')+1 );
		$partsFile = pathinfo($filename);
				
		if( empty($partsFile['extension']) )
			return false;
		
		if( isset($_GET['include_only_ext']) && trim($_GET['include_only_ext']) != '' )
		{
			$extensions = strtolower(preg_replace("/\s/", '', $_GET['include_only_ext']));
			$ext_array = explode(',', trim($extensions, ',') );
			if( in_array( strtolower($partsFile['extension']), $ext_array ) )
			{
				if( !isset($g_import_mt_extensions[ strtolower($partsFile['extension']) ]) )
					$g_import_mt_extensions[ strtolower($partsFile['extension']) ] = true;
				return true;
			}
			return false;
		}
		
		switch( strtolower($partsFile['extension']) )
		{
			// Audio formats
			case 'mp3': // most common
			case 'mpga':
			case 'mp2':
			case 'mp2a':
			case 'm2a':
			case 'm3a':
			case 'm4a':
			case 'ogg':
			case 'wma':
			case 'wax':
			case 'ra':
			case 'ram':
			case 'mp4a':
				
			// Video formats
			case 'm4v':
			case 'mpeg':
			case 'mpg':
			case 'mpe':
			case 'm1v':
			case 'm2v':
			case 'mp4':
			case 'mp4v':
			case 'mpg4':
			case 'asf':
			case 'asx':
			case 'wmx':
			case 'avi':
			case 'wmv':
			case 'flv':
			case 'mov':
			case 'qt':
			case 'divx':
			case '3gp':
			
			// rarely used
			case 'mid':
			case 'midi':
			case 'wav':
			case 'aa':
			case 'torrent':
			{
				if( !isset($g_import_mt_extensions[ strtolower($partsFile['extension']) ]) )
					$g_import_mt_extensions[ strtolower($partsFile['extension']) ] = true;
				return true;
			}
		}

		return false;
	}
	
	function empowercastadmin_mt_media_from_content($content)
	{
		$return = array();
		//$content = '<a href="http://www.test.com/test1.mp3">test1<a/> http://www.test.com/test2.mp3 http://www.test.com/test2.exe <a href="http://www.test.com/test3.mp3"></a>';
		$ltrs = '\w';
		$gunk = '/#~:.?+=&%@!\-';
		$punc = '.:?\-';
		$any = $ltrs . $gunk . $punc;

		preg_match_all( "{\b http : [$any] +? (?= [$punc] * [^$any] | $)}x", $content, $post_links_temp );
		foreach( $post_links_temp[0] as $null => $url )
		{
			if( empowercastadmin_mt_is_media($url) )
			{
				if( !in_array($url, $return) )
					$return[] = $url;
			}
		}
		
		return $return;
	}
	
	function empowercastadmin_mt_do_import()
	{
		$wp_remote_options = array();
		$wp_remote_options['user-agent'] = 'Podops EmpowerCast/'.EMPOWERCAST_VERSION;
		$wp_remote_options['httpversion'] = '1.1';
		
		$Import = ( isset($_POST['Import']) ? $_POST['Import'] : array() );
		$Media = ( isset($_POST['Media']) ? $_POST['Media'] : array() );
		$Titles = ( isset($_POST['Titles']) ? $_POST['Titles'] : array() );
		
		@set_time_limit(60 + (10* count($Import)) );
		$DetectDuration = ( !empty($_POST['DetectDuration']) ? $_POST['DetectDuration'] : 0 );
		
		if( $DetectDuration )
		{
			require_once(EMPOWERCAST_ABSPATH.'/mp3info.class.php');
			$Mp3Info = new Mp3Info();
			if( defined('EMPOWERCAST_DOWNLOAD_BYTE_LIMIT') )
				$Mp3Info->SetDownloadBytesLimit(EMPOWERCAST_DOWNLOAD_BYTE_LIMIT);
		}
		
		foreach( $Import as $post_id => $episode_feeds )
		{
			foreach( $episode_feeds as $media_index => $feed_slug )
			{
				if( $feed_slug == '' )
					continue; // User decoded not to import this one..
				$url = $Media[$post_id][$media_index];
				//$headers = wp_remote_head($url, array('httpversion' => 1.1));
				//$response = wp_remote_request($url, $options);
				$response = wp_remote_head( htmlspecialchars($url), array('httpversion' => 1.1) );
				// Redirect 1
				if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
				{
					$headers = wp_remote_retrieve_headers( $response );
					$response = wp_remote_head( $headers['location'], $wp_remote_options );
				}
				// Redirect 2
				if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
				{
					$headers = wp_remote_retrieve_headers( $response );
					$response = wp_remote_head( $headers['location'], $wp_remote_options );
				}
				// Redirect 3
				if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
				{
					$headers = wp_remote_retrieve_headers( $response );
					$response = wp_remote_head( $headers['location'], $wp_remote_options );
				}
				// Redirect 4
				if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
				{
					$headers = wp_remote_retrieve_headers( $response );
					$response = wp_remote_head( $headers['location'], $wp_remote_options );
				}
				$headers = wp_remote_retrieve_headers( $response );

				if ( is_wp_error( $response ) )
				{
					empowercastadmin_mt_import_log($Titles[ $post_id ], $url, $feed_slug, 'A system error occurred.');
				}
				else if( $headers && $response['response']['code'] >= 200 && $response['response']['code'] < 300 )
				{
					
					$EpisodeData = array();
					$EpisodeData['url'] = $url;
					if( $headers['content-length'] )
						$EpisodeData['size'] = (int) $headers['content-length'];
					else
						$EpisodeData['size'] = 0;
						
					$EpisodeData['type'] = empowercast_get_contenttype($EpisodeData['url']);
					$EpisodeData['duration'] = false;
					if( $EpisodeData['type'] == 'audio/mpeg' && $DetectDuration )
					{
						$Mp3Data = $Mp3Info->GetMp3Info($EpisodeData['url']);
						
						if( $Mp3Data )
						{
							$Duration = $Mp3Data['playtime_string'];
							$EpisodeData['duration'] = empowercast_readable_duration($Duration, true); // Fix so it looks better when viewed for editing
						}
					}
					
					$EnclosureData = $EpisodeData['url'] . "\n" . $EpisodeData['size'] . "\n". $EpisodeData['type'];
					if( $EpisodeData['duration'] )
						$EnclosureData .= "\n".serialize( array('duration'=>$EpisodeData['duration']) );
					
					// Save it here...
					if( $feed_slug == 'podcast' )
						add_post_meta($post_id, 'enclosure', $EnclosureData, true);
					else
						add_post_meta($post_id, '_'. $feed_slug .':enclosure', $EnclosureData, true);
					
					empowercastadmin_mt_import_log($Titles[ $post_id ], $EpisodeData['url'], $feed_slug);
				}
				else
				{
					empowercastadmin_mt_import_log($Titles[ $post_id ], $url, $feed_slug, __('HTTP return code', 'empowercast')  .' '. $response['response']['code'] .'.');
				}
			}
		}
	}
	
	function empowercastadmin_mt_import_log($post_title, $episode_url, $feed_slug, $error = false)
	{
		global $g_mt_import_log, $g_mt_import_count, $g_mt_error_count;
		$filename = substr($episode_url, strrpos($episode_url, '/')+1);
		$g_mt_import_log .= '<p style="font-weight: normal; margin-top: 2px; margin-bottom: 2px; margin-left: 20px;">';
		if( $error )
		{
			$g_mt_import_log .= '<div class="error-mt">';
			$g_mt_import_log .= sprintf( __('Error importing %s for blog post %s:', 'empowercast'),
					'"<a href="'. $episode_url .'">'. htmlspecialchars($filename) .'</a>"',
					'<em>'. htmlspecialchars($post_title) .'</em>' );
			$g_mt_import_log .= ' '.$error;
			$g_mt_import_log .= "</div>\n";
		}
		else
		{
			$g_mt_import_log .= sprintf( __('Episode %s for blog post %s imported to feed %s.', 'empowercast'),
					'"<a href="'. $episode_url .'">'. htmlspecialchars($filename) .'</a>"',
					'<em>'. htmlspecialchars($post_title) .'</em>',
					'<em>'. htmlspecialchars($feed_slug) .'</em>'					);
			$g_mt_import_log .= "\n";
		}
		
		$g_mt_import_log .= '</p>';
		if( $error )
		{
			if( $g_mt_error_count )
				$g_mt_error_count++;
			else
				$g_mt_error_count = 1;
		}
		else
		{
			if( $g_mt_import_count )
				$g_mt_import_count++;
			else
				$g_mt_import_count = 1;
		}
	}
	
	function empowercastadmin_mt_import_print_log()
	{
		global $g_mt_import_log, $g_mt_import_count, $g_mt_error_count;
		if( !$g_mt_import_log )
			return;
		echo '<div style="" class="updated empowercast-notice">';
		echo '<h3 style="margin-top: 2px; margin-bottom: 2px;">Import Log</h3>';
		$DetectDuration = ( !empty($_POST['DetectDuration']) ?$_POST['DetectDuration']:0);
		if( $DetectDuration )
		{
			echo '<p style="font-weight: normal;">'. __('Duration of each mp3 detected.', 'empowercast') .'</p>';
		}
		
		echo $g_mt_import_log;
		$g_mt_import_log='';
		echo '<p style="font-weight: normal;">'. sprintf( __('Imported %d episode(s).', 'empowercast'), $g_mt_import_count ) .'</p>';
		if( $g_mt_error_count )
			echo '<p style="font-weight: normal;">'. sprintf( __('Found %d error(s).', 'empowercast'), $g_mt_error_count ) .'</p>';
		echo '</div>';
	}
	
	function empowercastadmin_importmt_columns($data=array())
	{
		$Settings = empowercast_get_settings('empowercast_general', false);
		$data['post-title'] = __('Episode Title', 'empowercast');
		$data['post-date'] = __('Date', 'empowercast');
		
		$data['feed-podcast'] = __('Feed: (podcast)', 'empowercast');
		
		if( !empty($Settings['custom_feeds']) && is_array($Settings['custom_feeds']) )
		{
			foreach( $Settings['custom_feeds'] as $feed_slug => $value )
			{
				if( $feed_slug != 'podcast' )
					$data['feed-'.$feed_slug] = __('Feed', 'empowercast')  .': ('.$feed_slug.')';
			}
		}
		$data['exclude'] = '<a href="#" onclick="no_import_all();return false;">'. __('No Import', 'empowercast') .'</a>';
		
		return $data;
	}
	
	add_filter('manage_empowercastadmin_importmt_columns', 'empowercastadmin_importmt_columns');
	
	function empowercast_admin_mt()
	{
		$results = empowercast_get_mt_episodes();
		$Settings = empowercast_get_settings('empowercast_general', false);
		if( !isset($Settings['custom_feeds']['podcast']) && !empty($Settings['custom_feeds']) )
			$Settings['custom_feeds'] = array_merge( array('podcast'=> __('Podcast Feed (default)', 'empowercast') ), $Settings['custom_feeds'] );
		else if( empty($Settings['custom_feeds']) )
			$Settings['custom_feeds'] = array('podcast'=> __('Podcast Feed (default)', 'empowercast'));
			
		if( $results )
		{
			if( $results['feeds_required'] > count($Settings['custom_feeds']) )
			{
				empowercast_page_message_add_error( sprintf(__('We found blog posts that have as many as %d media files. You may need to create %d more Custom Feeds in order to import all of the media.', 'empowercast'), $results['feeds_required'], $results['feeds_required'] - count($Settings['custom_feeds']) ) );
				empowercast_page_message_print();
			}
		}
		
		empowercastadmin_mt_import_print_log();
		
?>
<style type="text/css">
.column-exclude {
	width: 80px;
}
.column-post-date {
	width: 80px;
}
.error-mt {
	background-color: #ffebe8;
	border-color: #c00;
	border-width: 1px;
	border-style: solid;
	padding: 0 0.6em;
	margin: 5px 15px 2px;
	-moz-border-radius: 3px;
	-khtml-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
}
label {
	float: left;
	width: 160px;
}
</style>
<script language="javascript"><!--

function check_radio_selection(obj, PostID, FileIndex)
{
	if( obj.value == '' ) // Users can select the no feed option as much as they want
		return true;
	
	var Field = obj.id;
	while( Field.charAt( Field.length-1 ) >= "0" &&  Field.charAt( Field.length-1 ) <= "9" ) // ( chr < "0" ) || ( chr > "9" )
	{
		Field = Field.substring(0, Field.length-1);
	}
	
	var Pos = 0;
	var CheckObj = document.getElementsByName( "Import["+PostID+"]["+Pos+"]" );
	while( CheckObj )
	{
		if( CheckObj.length == 0 )
			break;
			
		if( Pos != FileIndex )
		{
			for (var i = 0; i < CheckObj.length; i++)
			{
				if (CheckObj[i].type == 'radio' && CheckObj[i].checked && CheckObj[i].value == obj.value )
				{
					alert("<?php echo __('Sorry, you may only select one media file per post per feed.', 'empowercast'); ?>");
					return false;
				}
			}
		}
		Pos++;
		var CheckObj = document.getElementsByName( "Import["+PostID+"]["+Pos+"]" );
	}
	
	return true;
}

function no_import_all()
{
	if( !confirm('<?php echo __('Select "No Import" option for all media files?', 'empowercast'); ?>') )
		return;
		
	var Inputs = document.getElementsByTagName('input');
	for (var i = 0; i < Inputs.length; i++)
	{
		var Elem = Inputs[i];
		if( Elem.type == 'radio' && Elem.value == '' )
			Elem.checked = true;
	}
}

function select_all(index,value)
{
	var NoImport = [];
	var Inputs = document.getElementsByTagName('input');
	for (var i = 0; i < Inputs.length; i++)
	{
		var Elem = Inputs[i];
		if( Elem.type == 'radio' && Elem.value == value )
		{
			ElemIndex = Elem.id.substring( Elem.id.lastIndexOf('_')+1);
			if( ElemIndex == index )
				Elem.checked = true;
			else if( Elem.checked && Elem.value != '' )
				NoImport.push( Elem.id );
		}
	}
	for (var i = 0; i < Inputs.length; i++)
	{
		var Elem = Inputs[i];
		if( Elem.type == 'radio' && Elem.value == '' )
		{
			for (var j = 0; j < NoImport.length; j++)
			{
				if( NoImport[j] == Elem.id )
					Elem.checked = true;
			}
		}
	}
}
//-->
</script>
<h2><?php echo __('Import Episodes', 'empowercast'); ?></h2>
<?php

if( count($results) == 0 || count($results) == 1 )
{
?>	
	<p><?php echo __('No episodes found to import.', 'empowercast'); ?></p>
<?php
}
else
{
?>
<input type="hidden" name="action" value="empowercast-importmt" />
<p><?php echo __('Select the media file under each feed for each episode you wish to import.', 'empowercast'); ?></p>
<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php
		print_column_headers('empowercastadmin_importmt');
?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php
		print_column_headers('empowercastadmin_importmt', false);
?>
	</tr>
	</tfoot>
	<tbody>
<?php
	
	$StrandedEpisodes = 0;
	$ImportableEpisodes = 0;
	$MaxFileIndex = 1;
	
	$count = 0;
	foreach( $results as $post_id => $import_data )
	{
		$edit_link = get_edit_post_link( $post_id );
		if( $post_id == 'feeds_required' )
			continue;
		
		$columns = empowercastadmin_importmt_columns();
		
		$CurrentEnclosures = array();
		
		if( is_array($Settings['custom_feeds']) )
		{
			foreach( $Settings['custom_feeds'] as $feed_slug => $value )
			{
				if( $feed_slug == 'podcast' )
					$enclosure_data = get_post_meta($post_id, 'enclosure', true);
				else
					$enclosure_data = get_post_meta($post_id, '_'. $feed_slug .':enclosure', true);
				if( !$enclosure_data )
					continue;
					
				@list($EnclosureURL, $EnclosureSize, $EnclosureType, $Serialized) = @explode("\n", $enclosure_data);
				if( $EnclosureURL )
				{
					$CurrentEnclosures[ $feed_slug ] = array();
					$CurrentEnclosures[ $feed_slug ]['url'] = trim($EnclosureURL);
					$CurrentEnclosures[ $feed_slug ]['imported'] = false;
				}
				
				$found = false;
				foreach( $import_data['enclosures'] as $episode_index => $episode_data )
				{
					if( $episode_data['url'] == $CurrentEnclosures[ $feed_slug ]['url'] )
					{
						$import_data['enclosures'][$episode_index]['imported'] = true;
						$CurrentEnclosures[ $feed_slug ]['imported'] = true;
						$found  = true;
						break;
					}
				}
				reset($import_data['enclosures']);
				if( $found == false )
				{
					// Add it to the media file list, prepend it...
					$is_enclosure_data = array();
					$is_enclosure_data['url'] = $CurrentEnclosures[ $feed_slug ]['url'];
					$is_enclosure_data['imported'] = true;
					
					array_push($import_data['enclosures'], $is_enclosure_data);
					$CurrentEnclosures[ $feed_slug ]['imported'] = true;
					$CurrentEnclosures[ $feed_slug ]['present'] = true;
				}
			}
			reset($Settings['custom_feeds']);
		}
		
		$feed_title = '';
		if( $feed_slug == 'podcast' )
			$feed_title = __('Podcast Feed (default)', 'empowercast');
		$feed_title = esc_html($feed_title);
		if( $count % 2 == 0 )
			echo '<tr valign="middle" class="alternate">';
		else
			echo '<tr valign="middle">';
			
		$CheckedEpisodes = array(); // key = file_index, value = feed-slug
		
		$feed_index = 0;
		foreach($columns as $column_name=>$column_display_name)
		{
			$class = "class=\"column-$column_name\"";
			
			switch($column_name)
			{
				case 'post-title': {
					
					echo '<td '.$class.'><strong>';
					if ( current_user_can( 'edit_post', $post_id ) )
					{
					?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr(sprintf(__('Edit "%s"', 'empowercast'), $import_data['post_title'])); ?>"><?php echo esc_attr($import_data['post_title']); ?></a><?php
					}
					else
					{
						echo $import_data['post_title'];
					}
					
					
					echo '</strong><br />';
					echo '<div style="margin-left: 10px;">';
					$index = 1;
					foreach( $import_data['enclosures'] as $episode_index => $episode_data )
					{
						if( $index > $MaxFileIndex )
							$MaxFileIndex = $index;
						$Parts = parse_url($episode_data['url']);
						$filename = substr($Parts['path'], strrpos($Parts['path'], '/')+1 );
						if( $filename == '' )
							$filename = $episode_data['url'];
						echo "File&nbsp;$index:&nbsp;";
						
						echo '<input type="hidden" name="Media['.$post_id.']['.$episode_index.']" value="'. htmlspecialchars($episode_data['url']) .'" />';
						
						
						if( empty($episode_data['imported']) )
						{
							echo '<span style="color: #CC0000; font-weight: bold; cursor:pointer;" onclick="alert(\'File: '. htmlspecialchars($filename) .'\nURL: '. htmlspecialchars($episode_data['url']) .'\')">';
							$StrandedEpisodes++;
						}
						else
						{
							echo '<span style="color: green; font-weight: bold; cursor:pointer;" onclick="alert(\'File: '. htmlspecialchars($filename) .'\nURL: '. htmlspecialchars($episode_data['url']) .'\')">';
						}
						
						if( empty($episode_data['imported']) )
							echo '*';
						echo htmlspecialchars($filename);
						echo '</span>';
							
						echo '<br/>';
						$index++;
					}
					
					if( $index > 1 ) // Do we need the post title?
						echo '<input type="hidden" name="Titles['.$post_id.']" value="'. htmlspecialchars($import_data['post_title']) .'" />';
					
					reset($import_data['enclosures']);
					
					echo '</div>';
					echo '</td>';
				
				}; break;
				case 'post-date': {
					echo "<td $class>";
					$timestamp = strtotime($import_data['post_date']);
					echo date('Y/m/d', $timestamp);
					echo "</td>";
				}; break;
				case 'feed-slug': {
					
					echo "<td $class>$feed_slug";
					echo "</td>";
					
				}; break;
				
				default: {
				
					echo "<td $class>";
					$feed_slug = substr($column_name, 5);
					if( $column_name == 'exclude' )
						$feed_slug = '';
					$enclosure_data = false;
					$EnclosureURL = '';
					
					echo '<div class="">&nbsp;<br />';
					if( isset($CurrentEnclosures[$feed_slug]) && $CurrentEnclosures[$feed_slug]['imported'] )
					{
						$index = 1;
						foreach( $import_data['enclosures'] as $episode_index => $episode_data )
						{
							echo "File $index: ";
							if( $CurrentEnclosures[$feed_slug]['url'] == $episode_data['url'] )
							{
								if( !empty($CurrentEnclosures[$feed_slug]['present']) )
									echo '<strong style="color: green;">'.  __('present', 'empowercast') .'</strong>';
								else
									echo '<strong style="color: green;">'.  __('imported', 'empowercast') .'</strong>';
							}
							else
								echo 'X';
							echo "<br/>\n";
							$index++;
						}
						reset($import_data['enclosures']);
					}
					else
					{
						$index = 1;
						foreach( $import_data['enclosures'] as $episode_index => $episode_data )
						{
							echo "File&nbsp;$index:&nbsp;";
							if( !empty($episode_data['imported']) )
							{
									echo '&nbsp;X';
							}
							else
							{
								$checked = '';
								if( !isset($CheckedEpisodes[ $episode_index ]) && !in_array($feed_slug, $CheckedEpisodes) )
								{
									$checked = 'checked';
									$CheckedEpisodes[ $episode_index ] = $feed_slug;
								}
								if( !isset($CheckedEpisodes[ $episode_index ]) && $feed_slug == '' )
								{
									$checked = 'checked';
								}
								
								
								echo '<input type="radio" id="import_'. $post_id .'_'. $episode_index .'" name="Import['.$post_id.']['.$episode_index.']" value="'.$feed_slug.'" '. $checked .' onclick="return check_radio_selection(this, '.$post_id.', '.$episode_index.')" />';
							}
							echo '<br/>';
							$index++;
						}
						reset($import_data['enclosures']);
					}
					
					echo '</div>';
					
					
					echo "</td>";
					$feed_index++;
				};	break;
			}
		}
		echo "\n    </tr>\n";
		$count++;
	}
?>
	</tbody>
</table>

<p><?php
	echo sprintf( __('Importable episodes highlighted in %s with asterisks *.', 'empowercast'),
		'<span style="color: #CC0000; font-weight: bold;">'. __('red', 'empowercast') .'</span>' );
?>
</p>
<p style="margin-bottom: 0; padding-bottom: 0;"><?php echo __('Select Only:', 'empowercast'); ?></p>
<?php
					if( $results['feeds_required'] < 1 )
				$results['feeds_required'] = 1;
			
			for( $number = 0; $number < $MaxFileIndex; $number++ )
			{
?>
<p style="margin: 0 0 0 40px; padding: 0;">
 <?php echo __('File', 'empowercast'); ?> <?php echo ($number+1); ?>:
<?php
				foreach( $Settings['custom_feeds'] as $feed_slug => $feed_title )
				{
					echo '<a href="javascript:void()" onclick="select_all('. $number .',\''. $feed_slug .'\');return false;">'. htmlspecialchars($feed_title) .'</a> | ';
				}
				reset($Settings['custom_feeds']);
?>
<a href="javascript:void()" onclick="select_all(<?php echo $number; ?>,'');return false;"><?php echo __('No Import', 'empowercast'); ?></a>
</p>
<?php
			}
?>
<p><?php echo __('Types of media found:', 'empowercast'); ?> 
<?php
	$comma = false;
	global $g_import_mt_extensions;
	foreach( $g_import_mt_extensions as $ext => $null )
	{
		if( $comma )
			echo ', ';
		$comma = true;
		echo $ext;
	}
?>
</p>



<?php
}
	
	if( $StrandedEpisodes )
	{
		reset($results);
?>
<p><?php
	echo sprintf( __('There are %d media files that can be imported with a total of %d blog post podcast episodes.', 'empowercast'),
		$StrandedEpisodes,
		(count($results) -1) );

?>
</p>
<?php
	}
?>
<p class="submit">
<input type="submit" name="Submit" id="empowercast_import_button" class="button-primary button-podops" value="Import Episodes" onclick="return confirm('Import selected episodes, are you sure?');" />
 &nbsp; <input type="checkbox" name="DetectDuration" value="1" /> <?php echo __('Detect duration for mp3 media. (expect script to take a while with this option)', 'empowercast'); ?></p>
</p>
</form>
<hr />
<form enctype="enctype" method="get" action="<?php echo admin_url('admin.php') ?>">
<input type="hidden" name="page" value="empowercast/empowercastadmin_tools.php" />
<input type="hidden" name="action" value="empowercast-mt-epiosdes" />
<h2><?php echo __('Filter Results', 'empowercast'); ?></h2>
<p><label><?php echo __('Include Only', 'empowercast'); ?></label><input type="text" name="include_only_ext" value="<?php if( !empty($_GET['include_only_ext']) ) echo htmlspecialchars($_GET['include_only_ext']); ?>" style="width: 240px;" />
(<?php echo __('leave blank for all media', 'empowercast'); ?>) <br />
<label>&nbsp;</label><?php echo __('Specify the file extensions to include separated by commas (e.g. mp3, m4v).', 'empowercast'); ?>
</p>
<p class="submit">
<input type="submit" name="Submit" class="button-primary button-podops" value="<?php echo __('Filter Episodes', 'empowercast'); ?>" />
</p>
	<!-- start footer -->
<?php
	}

?>