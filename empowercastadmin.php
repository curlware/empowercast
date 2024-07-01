<?php

if( !function_exists('add_action') )
	die("access denied.");

add_action ('wp_loaded', 'redirect_to_settings');

function redirect_to_settings() {
    $General = empowercast_get_settings('empowercast_general');
    $hasChannels = isset($General['channels']) && $General['channels'] == 1;
    $hasCats = isset($General['cat_casting']) && $General['cat_casting'] == 1;
    $hasTax = isset($General['taxonomy_podcasting']) && $General['taxonomy_podcasting'] == 1;
    $hasPT = isset($General['posttype_podcasting']) && $General['posttype_podcasting'] == 1;

    if (!$hasChannels && !$hasCats && !$hasTax && !$hasPT && isset($_GET['page']) && $_GET['page'] == 'empowercast/empowercastadmin_live_item.php') {
        wp_redirect(admin_url() . "admin.php?page=empowercastadmin_basic&tab=live-item-tab");
    }
}

function empowercast_esc_html($escape)
{
	if( is_array($escape) )
	{
		foreach( $escape as $index => $value ) {
			$escape[ $index ] = empowercast_esc_html($value);
		}
	}
	return esc_html($escape);
}

function empowercast_login_create_nonce() {
    return wp_create_nonce( 'empowercast-link-podops' );
}

/**
 * Returns array of languages supported by RevAI
 * @return array
 */
function empowercast_revai_languages(){
    $languages = array();

    $languages['ar'] = 'Arabic';
    $languages['bg'] = 'Bulgarian';
    $languages['ca'] = 'Catalan';
    $languages['hr'] = 'Croatian';
    $languages['cs'] = 'Czech';
    $languages['da'] = 'Danish';
    $languages['nl'] = 'Dutch';
    $languages['en'] = 'English';
    $languages['fa'] = 'Farsi';
    $languages['fi'] = 'Finnish';
    $languages['fr'] = 'French';
    $languages['de'] = 'German';
    $languages['el'] = 'Greek';
    $languages['he'] = 'Hebrew';
    $languages['hi'] = 'Hindi';
    $languages['hu'] = 'Hungarian';
    $languages['id'] = 'Indonesian';
    $languages['it'] = 'Italian';
    $languages['ja'] = 'Japanese';
    $languages['ko'] = 'Korean';
    $languages['lt'] = 'Lithuanian';
    $languages['lv'] = 'Latvian';
    $languages['ms'] = 'Malay';
    $languages['cmn'] = 'Mandarin';
    $languages['no'] = 'Norwegian';
    $languages['pl'] = 'Polish';
    $languages['pt'] = 'Portuguese';
    $languages['ro'] = 'Romanian';
    $languages['ru'] = 'Russian';
    $languages['sk'] = 'Slovak';
    $languages['sl'] = 'Slovenian';
    $languages['es'] = 'Spanish';
    $languages['sv'] = 'Swedish';
    $languages['ta'] = 'Tamil';
    $languages['te'] = 'Telugu';
    $languages['tr'] = 'Turkish';

    return $languages;
}

function empowercast_languages()
{
    // List copied from PodPress:
    $langs = array();
    $langs['af'] = __('Afrikaans', 'empowercast');
    $langs['sq'] = __('Albanian', 'empowercast');
    $langs['ar'] = __('Arabic', 'empowercast');
    $langs['ar-sa'] = __('Arabic (Saudi Arabia)', 'empowercast');
    $langs['ar-eg'] = __('Arabic (Egypt)', 'empowercast');
    $langs['ar-dz'] = __('Arabic (Algeria)', 'empowercast');
    $langs['ar-tn'] = __('Arabic (Tunisia)', 'empowercast');
    $langs['ar-ye'] = __('Arabic (Yemen)', 'empowercast');
    $langs['ar-jo'] = __('Arabic (Jordan)', 'empowercast');
    $langs['ar-kw'] = __('Arabic (Kuwait)', 'empowercast');
    $langs['ar-bh'] = __('Arabic (Bahrain)', 'empowercast');
    $langs['eu'] = __('Basque', 'empowercast');
    $langs['be'] = __('Belarusian', 'empowercast');
    $langs['bg'] = __('Bulgarian', 'empowercast');
    $langs['ca'] = __('Catalan', 'empowercast');
    $langs['zh-cn'] = __('Chinese (Simplified)', 'empowercast');
    $langs['zh-tw'] = __('Chinese (Traditional)', 'empowercast');
    $langs['hr'] = __('Croatian', 'empowercast');
    $langs['cs'] = __('Czech', 'empowercast');
    $langs['cr'] = __('Cree', 'empowercast');
    $langs['da'] = __('Danish', 'empowercast');
    $langs['nl'] = __('Dutch', 'empowercast');
    $langs['nl-be'] = __('Dutch (Belgium)', 'empowercast');
    $langs['nl-nl'] = __('Dutch (Netherlands)', 'empowercast');
    $langs['en'] = __('English', 'empowercast');
    $langs['en-au'] = __('English (Australia)', 'empowercast');
    $langs['en-bz'] = __('English (Belize)', 'empowercast');
    $langs['en-ca'] = __('English (Canada)', 'empowercast');
    $langs['en-ie'] = __('English (Ireland)', 'empowercast');
    $langs['en-jm'] = __('English (Jamaica)', 'empowercast');
    $langs['en-nz'] = __('English (New Zealand)', 'empowercast');
    $langs['en-ph'] = __('English (Phillipines)', 'empowercast');
    $langs['en-za'] = __('English (South Africa)', 'empowercast');
    $langs['en-tt'] = __('English (Trinidad)', 'empowercast');
    $langs['en-gb'] = __('English (United Kingdom)', 'empowercast');
    $langs['en-us'] = __('English (United States)', 'empowercast');
    $langs['en-zw'] = __('English (Zimbabwe)', 'empowercast');
    $langs['et'] = __('Estonian', 'empowercast');
    $langs['fo'] = __('Faeroese', 'empowercast');
    $langs['fi'] = __('Finnish', 'empowercast');
    $langs['fr'] = __('French', 'empowercast');
    $langs['fr-be'] = __('French (Belgium)', 'empowercast');
    $langs['fr-ca'] = __('French (Canada)', 'empowercast');
    $langs['fr-fr'] = __('French (France)', 'empowercast');
    $langs['fr-lu'] = __('French (Luxembourg)', 'empowercast');
    $langs['fr-mc'] = __('French (Monaco)', 'empowercast');
    $langs['fr-ch'] = __('French (Switzerland)', 'empowercast');
    $langs['gl'] = __('Galician', 'empowercast');
    $langs['gd'] = __('Gaelic', 'empowercast');
    $langs['de'] = __('German', 'empowercast');
    $langs['de-at'] = __('German (Austria)', 'empowercast');
    $langs['de-de'] = __('German (Germany)', 'empowercast');
    $langs['de-li'] = __('German (Liechtenstein)', 'empowercast');
    $langs['de-lu'] = __('German (Luxembourg)', 'empowercast');
    $langs['de-ch'] = __('German (Switzerland)', 'empowercast');
    $langs['el'] = __('Greek', 'empowercast');
    $langs['haw'] = __('Hawaiian', 'empowercast');
    $langs['he_IL'] = __('Hebrew', 'empowercast');
    $langs['hu'] = __('Hungarian', 'empowercast');
    $langs['is'] = __('Icelandic', 'empowercast');
    $langs['in'] = __('Indonesian', 'empowercast');
    $langs['ga'] = __('Irish', 'empowercast');
    $langs['it'] = __('Italian', 'empowercast');
    $langs['hi'] = __('Hindi', 'empowercast');
    $langs['it-it'] = __('Italian (Italy)', 'empowercast');
    $langs['it-ch'] = __('Italian (Switzerland)', 'empowercast');
    $langs['ja'] = __('Japanese', 'empowercast');
    $langs['ko'] = __('Korean', 'empowercast');
    $langs['mk'] = __('Macedonian', 'empowercast');
    $langs['no'] = __('Norwegian', 'empowercast');
    $langs['pa'] = __('Punjabi', 'empowercast');
    $langs['pl'] = __('Polish', 'empowercast');
    $langs['pt'] = __('Portuguese', 'empowercast');
    $langs['pt-br'] = __('Portuguese (Brazil)', 'empowercast');
    $langs['pt-pt'] = __('Portuguese (Portugal)', 'empowercast');
    $langs['ro'] = __('Romanian', 'empowercast');
    $langs['ro-mo'] = __('Romanian (Moldova)', 'empowercast');
    $langs['ro-ro'] = __('Romanian (Romania)', 'empowercast');
    $langs['ru'] = __('Russian', 'empowercast');
    $langs['ru-mo'] = __('Russian (Moldova)', 'empowercast');
    $langs['ru-ru'] = __('Russian (Russia)', 'empowercast');
    $langs['sr'] = __('Serbian', 'empowercast');
    $langs['sk'] = __('Slovak', 'empowercast');
    $langs['sl'] = __('Slovenian', 'empowercast');
    $langs['es'] = __('Spanish', 'empowercast');
    $langs['es-ar'] = __('Spanish (Argentina)', 'empowercast');
    $langs['es-bo'] = __('Spanish (Bolivia)', 'empowercast');
    $langs['es-cl'] = __('Spanish (Chile)', 'empowercast');
    $langs['es-co'] = __('Spanish (Colombia)', 'empowercast');
    $langs['es-cr'] = __('Spanish (Costa Rica)', 'empowercast');
    $langs['es-do'] = __('Spanish (Dominican Republic)', 'empowercast');
    $langs['es-ec'] = __('Spanish (Ecuador)', 'empowercast');
    $langs['es-sv'] = __('Spanish (El Salvador)', 'empowercast');
    $langs['es-gt'] = __('Spanish (Guatemala)', 'empowercast');
    $langs['es-hn'] = __('Spanish (Honduras)', 'empowercast');
    $langs['es-mx'] = __('Spanish (Mexico)', 'empowercast');
    $langs['es-ni'] = __('Spanish (Nicaragua)', 'empowercast');
    $langs['es-pa'] = __('Spanish (Panama)', 'empowercast');
    $langs['es-py'] = __('Spanish (Paraguay)', 'empowercast');
    $langs['es-pe'] = __('Spanish (Peru)', 'empowercast');
    $langs['es-pr'] = __('Spanish (Puerto Rico)', 'empowercast');
    $langs['es-es'] = __('Spanish (Spain)', 'empowercast');
    $langs['es-uy'] = __('Spanish (Uruguay)', 'empowercast');
    $langs['es-ve'] = __('Spanish (Venezuela)', 'empowercast');
    $langs['sv'] = __('Swedish', 'empowercast');
    $langs['sv-fi'] = __('Swedish (Finland)', 'empowercast');
    $langs['sv-se'] = __('Swedish (Sweden)', 'empowercast');
    $langs['sw'] = __('Swahili', 'empowercast');
    $langs['ta'] = __('Tamil', 'empowercast');
    $langs['th'] = __('Thai', 'empowercast');
    $langs['bo'] = __('Tibetan', 'empowercast');
    $langs['tr'] = __('Turkish', 'empowercast');
    $langs['uk'] = __('Ukranian', 'empowercast');
    $langs['ve'] = __('Venda', 'empowercast');
    $langs['vi'] = __('Vietnamese', 'empowercast');
    $langs['zu'] = __('Zulu', 'empowercast');
    $langs['fa'] = __('Persian', 'empowercast');
    $langs['fa-af'] = __('Persian (Afghanistan)', 'empowercast');

    return $langs;
}
	
function empowercast_page_message_add_error($msg, $classes='inline', $escape=true)
{
	global $g_empowercast_page_message;
	if( $escape )
		$g_empowercast_page_message .= '<div class="error empowercast-error '.$classes.'">'. esc_html($msg) . '</div>';
	else
		$g_empowercast_page_message .= '<div class="error empowercast-error '.$classes.'">'. ($msg) . '</div>';
}

function empowercast_page_message_add_notice($msg, $classes='inline', $escape=true)
{
	global $g_empowercast_page_message;
	// Always pre-pend, since jQuery will re-order with first as last.
	if( $escape )
		$g_empowercast_page_message = '<div class="updated fade empowercast-notice '.$classes.'">'. esc_html($msg) . '</div>' . $g_empowercast_page_message;
	else
		$g_empowercast_page_message = '<div class="updated fade empowercast-notice '.$classes.'">'. ($msg) . '</div>' . $g_empowercast_page_message;
}


function empowercast_getAccessToken()
{
    // Look at the creds and use the latest access token, if its not the latest refresh it...
    $creds = get_option('empowercast_creds', array());
    if( !empty($creds['access_token']) && !empty($creds['access_expires']) && $creds['access_expires'] > time() ) { // If access token did not expire
        return $creds['access_token'];
    }

    if( !empty($creds['refresh_token']) && !empty($creds['client_id']) && !empty($creds['client_secret']) ) {

        // Create new access token with refresh token here...
        $auth = new EmpowerCastAuth();
        $resultTokens = $auth->getAccessTokenFromRefreshToken($creds['refresh_token'], $creds['client_id'], $creds['client_secret']);

        if( !empty($resultTokens['access_token']) && !empty($resultTokens['expires_in']) ) {
            empowercast_save_settings( array('access_token'=>$resultTokens['access_token'], 'access_expires'=>( time() + $resultTokens['expires_in'] - 10 ) ), 'empowercast_creds');

            return $resultTokens['access_token'];
        } else {
            //if their refresh token is expired, sign them out so they can re-authenticate
            delete_option('empowercast_creds');
            empowercast_page_message_add_error(__('Your account has been logged out due to inactivity with Podops services.', 'empowercast'));
            empowercast_page_message_print();

        }
    }

    // If we failed to get credentials, return false
    return false;
}

function empowercast_page_message_print()
{
	global $g_empowercast_page_message;
	if( $g_empowercast_page_message )
		echo $g_empowercast_page_message;
	$g_empowercast_page_message = '';
}

function empowercast_admin_activate()
{
	$Settings = get_option('empowercast_general', array());
	if( empty($Settings) )
	{
		// If no settings exist, see if either PodPress or Podcasting plugins are enabled and import those settings...
		if( defined('PODCAST_VERSION') )
		{
			empowercast_admin_import_podpress_settings();
		}
		else if( isset($GLOBALS['podcasting_player_id']) || defined('PODCASTING_VERSION') )
		{
			empowercast_admin_import_podcasting_settings();
		}
	}
}

function empowercast_admin_init()
{
	global $wp_rewrite;
	add_thickbox(); // we use the thckbox for some settings
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core'); // Now including the library at Google

	// Empowercast page
	if( isset($_GET['page']) && strstr($_GET['page'], 'empowercast' ) !== false )
	{
		wp_enqueue_script('jquery-ui-tabs');
        // If we have empowercast credentials, check if the account has been verified
        $creds = get_option('empowercast_creds');
        empowercast_check_credentials($creds);
		//wp_enqueue_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js');
		if( preg_match('/empowercastadmin_(mobile|audio|video)player/', $_GET['page']) )
		{
			wp_enqueue_style( 'wp-color-picker' );
		}
		
		if( preg_match('/empowercastadmin_migrate/', $_GET['page']) )
		{
			wp_enqueue_script('media-upload'); // For the readjustment of the thickbox only
		}
	}

	if( function_exists('empowercast_admin_jquery_init') )
		empowercast_admin_jquery_init();
	
	if( !current_user_can(EMPOWERCAST_CAPABILITY_MANAGE_OPTIONS) )
	{
		empowercast_page_message_add_error( __('You do not have sufficient permission to manage options.', 'empowercast') );
		return;
	}

	// Check for other podcasting plugin
	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
		empowercast_page_message_add_error( __('Another podcasting plugin has been detected, EmpowerCast is currently disabled.', 'empowercast') );
	
	global $wp_version;
	$VersionDiff = version_compare($wp_version, 3.6);
	if( $VersionDiff < 0 )
		empowercast_page_message_add_error( __('Podops EmpowerCast requires Wordpress version 3.6 or greater.', 'empowercast') );
	
	// Check for incompatible plugins:
	if( isset($GLOBALS['objWPOSFLV']) && is_object($GLOBALS['objWPOSFLV']) )
		empowercast_page_message_add_error( __('The WP OS FLV plugin is not compatible with Podops EmpowerCast.', 'empowercast') );
	
	// Security step, we must be in a empowercast/* page...
	if( isset($_GET['page']) && ( strstr($_GET['page'], 'empowercast/' ) !== false || strstr($_GET['page'], 'empowercastadmin_' ) !== false ) )
	{
        // Save settings here
		if( isset($_POST[ 'Feed' ]) || isset($_POST[ 'General' ])  )
		{
			check_admin_referer('empowercast-edit');

			// Save the posted value in the database
			$Feed = (isset($_POST['Feed'])?$_POST['Feed']:false);
			$General = (isset($_POST['General'])?$_POST['General']:false);
			$FeedSlug = (isset($_POST['feed_slug'])?esc_attr($_POST['feed_slug']):false);
			$Category = (isset($_POST['cat'])?intval($_POST['cat']):false);
			$term_taxonomy_id = (isset($_POST['ttid'])?intval($_POST['ttid']):false);
			$podcast_post_type = (isset($_POST['podcast_post_type'])?esc_attr($_POST['podcast_post_type']):false);
            $acceptable_extensions = ['jpg', 'jpeg', 'png'];
            $GeneralPrev = get_option('empowercast_general', array());

			// New iTunes image
			if( !empty($_POST['itunes_image_checkbox']) )
			{
				$temp = $_FILES['itunes_image_file']['tmp_name'];

                //Make sure the file extension is alright
                $name = $_FILES['itunes_image_file']['name'];
                $ext = substr($name, strrpos($name, '.') + 1);


                $UploadArray = wp_upload_dir();
                $upload_url = $UploadArray['baseurl'];
                if (empty($name) && !empty($Feed['itunes_image']) && strpos($Feed['itunes_image'], $upload_url) === false) {
                    // no uploaded file--URL pasted into field
                    $Feed['itunes_image'] = @media_sideload_image($Feed['itunes_image'], null, null, 'src');
                }
                if (!empty($_FILES['itunes_image_file']['tmp_name'])) {
                    if (!in_array(strtolower($ext), $acceptable_extensions)) {
                        unset($Feed['itunes_image']);
                        empowercast_page_message_add_error(__('Apple Podcasts Image has an invalid file type: ' . htmlspecialchars($ext), 'empowercast'));
                    } else {
                        // Check the image...
                        if (file_exists($temp)) {
                            $ImageData = @getimagesize($temp);

                            $rgb = true; // We assume it is RGB
                            if (defined('EMPOWERCAST_IMAGICK') && EMPOWERCAST_IMAGICK) {
                                if ($ImageData[2] == IMAGETYPE_PNG && extension_loaded('imagick')) {
                                    $image = new Imagick($temp);
                                    if ($image->getImageColorspace() != imagick::COLORSPACE_RGB) {
                                        $rgb = false;
                                    }
                                }
                            }

                            if (empty($ImageData['channels']))
                                $ImageData['channels'] = 3; // Assume it's ok if we cannot detect it.

                            if ($ImageData) {
                                if ($rgb && ($ImageData[2] == IMAGETYPE_JPEG || $ImageData[2] == IMAGETYPE_PNG) && $ImageData[0] == $ImageData[1] && $ImageData[0] >= 1400 && $ImageData[0] <= 3000 && $ImageData['channels'] == 3) // Just check that it is an image, the correct image type and that the image is square
                                {
                                    $upload_result = wp_handle_upload($_FILES['itunes_image_file'], array('action' => $_POST['action'], 'test_form' => false));
                                    if (is_array($upload_result) && isset($upload_result['error'])) {
                                        unset($Feed['itunes_image']);
                                        empowercast_page_message_add_error(__('Error saving image', 'empowercast') . ':   ' . htmlspecialchars($upload_result['error']));
                                    } elseif (is_array($upload_result) && isset($upload_result['url'])) {
                                        $Feed['itunes_image'] = $upload_result['url'];
                                    } else {
                                        unset($Feed['itunes_image']);
                                        empowercast_page_message_add_error(__('Error saving image', 'empowercast'));
                                    }
                                } else if ($ImageData['channels'] != 3 || $rgb == false) {
                                    unset($Feed['itunes_image']);
                                    empowercast_page_message_add_error(__('Invalid image', 'empowercast') . ':	' . htmlspecialchars($_FILES['itunes_image_file']['name']) . ' - ' . __('Image must be in RGB color space (CMYK is not supported).', 'empowercast'));
                                } else if ($ImageData[0] != $ImageData[1]) {
                                    unset($Feed['itunes_image']);
                                    empowercast_page_message_add_error(__('Invalid image', 'empowercast') . ':	' . htmlspecialchars($_FILES['itunes_image_file']['name']) . ' - ' . __('Image must be square, 1400 x 1400 is the required minimum size.', 'empowercast'));
                                } else if ($ImageData[0] != $ImageData[1] || $ImageData[0] < 1400) {
                                    unset($Feed['itunes_image']);
                                    empowercast_page_message_add_error(__('Invalid image', 'empowercast') . ':	' . htmlspecialchars($_FILES['itunes_image_file']['name']) . ' - ' . __('Image is too small, 1400 x 1400 is the required minimum size.', 'empowercast'));
                                } else if ($ImageData[0] != $ImageData[1] || $ImageData[0] > 3000) {
                                    unset($Feed['itunes_image']);
                                    empowercast_page_message_add_error(__('Invalid image', 'empowercast') . ':	' . htmlspecialchars($_FILES['itunes_image_file']['name']) . ' - ' . __('Image is too large, 3000 x 3000 is the maximum size allowed.', 'empowercast'));
                                } else {
                                    unset($Feed['itunes_image']);
                                    empowercast_page_message_add_error(__('Invalid image', 'empowercast') . ':	' . htmlspecialchars($_FILES['itunes_image_file']['name']));
                                }
                            } else {
                                unset($Feed['itunes_image']);
                                empowercast_page_message_add_error(__('Invalid image', 'empowercast') . ':	' . htmlspecialchars($_FILES['itunes_image_file']['name']));
                            }
                        }
                    }
                }
			}
			
			// New mp3 coverart image
			if( !empty($_POST['coverart_image_checkbox']) )
			{
				$temp = $_FILES['coverart_image_file']['tmp_name'];

                //Make sure the file extension is alright
                $name = $_FILES['coverart_image_file']['name'];
                $ext = substr($name, strrpos($name, '.') + 1);

                if (!in_array(strtolower($ext), $acceptable_extensions)) {
                    empowercast_page_message_add_error(__('Coverart Image has an invalid file type: ' . htmlspecialchars($ext), 'empowercast') );
                } else {
                    if (@getimagesize($temp))  // Just check that it is an image, we may add more to this later
                    {
                        $upload_result = wp_handle_upload($_FILES['coverart_image_file'], array('action' => $_POST['action'], 'test_form' => false));
                        if (is_array($upload_result) && isset($upload_result['error'])) {
                            empowercast_page_message_add_error(__('Error saving Coverart image', 'empowercast') . ':	' . $upload_result['error']);
                        } elseif (is_array($upload_result) && isset($upload_result['url'])) {
                            $General['tag_coverart'] = $upload_result['url'];
                        } else {
                            empowercast_page_message_add_error(__('Error saving Coverart image', 'empowercast'));
                        }
                    } else {
                        empowercast_page_message_add_error(__('Invalid Coverat image', 'empowercast') . ': ' . htmlspecialchars($_FILES['coverart_image_file']['name']));
                    }
                }
			}
			
			// New poster image
			if( !empty($_POST['poster_image_checkbox']) )
			{
				$temp = $_FILES['poster_image_file']['tmp_name'];

                //Make sure the file extension is alright
                $name = $_FILES['poster_image_file']['name'];
                $ext = substr($name, strrpos($name, '.') + 1);

                if (!in_array(strtolower($ext), $acceptable_extensions)) {
                    empowercast_page_message_add_error(__('Poster Image has an invalid file type: ' . htmlspecialchars($ext), 'empowercast') );
                } else {
                    if (@getimagesize($temp))  // Just check that it is an image, we may add more to this later
                    {
                        $upload_result = wp_handle_upload($_FILES['poster_image_file'], array('action' => $_POST['action'], 'test_form' => false));
                        if (is_array($upload_result) && isset($upload_result['error'])) {
                            empowercast_page_message_add_error(__('Error saving Poster image', 'empowercast') . ':	' . $upload_result['error']);
                        } elseif (is_array($upload_result) && isset($upload_result['url'])) {
                            $General['poster_image'] = $upload_result['url'];
                        } else {
                            empowercast_page_message_add_error(__('Error saving Poster image', 'empowercast'));
                        }
                    } else {
                        empowercast_page_message_add_error(__('Invalid poster image', 'empowercast') . ': ' . htmlspecialchars($_FILES['poster_image_file']['name']));
                    }
                }
			}
			
			
			// New audio play icon image
			if( !empty($_POST['audio_custom_play_button_checkbox']) )
			{
				$temp = $_FILES['audio_custom_play_button_file']['tmp_name'];

                //Make sure the file extension is alright
                $name = $_FILES['audio_custom_play_button_file']['name'];
                $ext = substr($name, strrpos($name, '.') + 1);

                if (!in_array(strtolower($ext), $acceptable_extensions)) {
                    empowercast_page_message_add_error(__('Custom audio play button Image has an invalid file type: ' . htmlspecialchars($ext), 'empowercast') );
                } else {
                    if (@getimagesize($temp))  // Just check that it is an image, we may add more to this later
                    {
                        $upload_result = wp_handle_upload($_FILES['audio_custom_play_button_file'], array('action' => $_POST['action'], 'test_form' => false));
                        if (is_array($upload_result) && isset($upload_result['error'])) {
                            empowercast_page_message_add_error(__('Error saving Play image', 'empowercast') . ':	' . $upload_result['error']);
                        } elseif (is_array($upload_result) && isset($upload_result['url'])) {
                            $General['audio_custom_play_button'] = $upload_result['url'];
                        } else {
                            empowercast_page_message_add_error(__('Error saving Play image', 'empowercast'));
                        }
                    } else {
                        empowercast_page_message_add_error(__('Invalid play icon image', 'empowercast') . ': ' . htmlspecialchars($_FILES['audio_custom_play_button_file']['name']));
                    }
                }
			}
			
			// New video play icon image empowercast-select-player
			if( !empty($_POST['video_custom_play_button_checkbox']) )
			{
				$temp = $_FILES['video_custom_play_button_file']['tmp_name'];

                //Make sure the file extension is alright
                $name = $_FILES['video_custom_play_button_file']['name'];
                $ext = substr($name, strrpos($name, '.') + 1);

                if (!in_array(strtolower($ext), $acceptable_extensions)) {
                    empowercast_page_message_add_error(__('Custom video play button Image has an invalid file type: ' . htmlspecialchars($ext), 'empowercast') );
                } else {
                    $imageInfo = @getimagesize($temp);
                    if ($imageInfo && $imageInfo[0] == $imageInfo[1] && $imageInfo[0] == 60)  // Just check that it is an image, we may add more to this later
                    {
                        $upload_result = wp_handle_upload($_FILES['video_custom_play_button_file'], array('action' => $_POST['action'], 'test_form' => false));
                        if (is_array($upload_result) && isset($upload_result['error'])) {
                            empowercast_page_message_add_error(__('Error saving Video Play icon image', 'empowercast') . ':	' . $upload_result['error']);
                        } elseif (is_array($upload_result) && isset($upload_result['url'])) {
                            $General['video_custom_play_button'] = $upload_result['url'];
                        } else {
                            empowercast_page_message_add_error(__('Error saving Video Play icon image', 'empowercast'));
                        }
                    } else if ($imageInfo) {
                        empowercast_page_message_add_error(__('Invalid play icon image size', 'empowercast') . ': ' . htmlspecialchars($_FILES['video_custom_play_button_file']['name']));
                    } else {
                        empowercast_page_message_add_error(__('Invalid play icon image', 'empowercast') . ': ' . htmlspecialchars($_FILES['video_custom_play_button_file']['name']));
                    }
                }
			}
			
			if( isset($_POST['UpdateDisablePlayer']) )
			{
				$player_feed_slug = $_POST['UpdateDisablePlayer'];
				$General['disable_player'] = array();
				if( isset($GeneralPrev['disable_player']) )
					$General['disable_player'] = $GeneralPrev['disable_player'];
				if( isset($_POST['DisablePlayerFor']) && !empty($_POST['DisablePlayerFor']) )
					$General['disable_player'][ $player_feed_slug ] = 1;
				else
					unset($General['disable_player'][ $player_feed_slug ]);
			}
			
			// Check to see if we need to update the feed title
			if( $FeedSlug && !$podcast_post_type )
			{
				$GeneralSettingsTemp = empowercast_get_settings('empowercast_general', false);
				if( !isset($GeneralSettingsTemp['custom_feeds'][$FeedSlug]) || $GeneralSettingsTemp['custom_feeds'][$FeedSlug] != $Feed['title'] )
				{
					if( !$General )
						$General = array();
					if( !empty($GeneralSettingsTemp['custom_feeds']) )
						$General['custom_feeds'] = $GeneralSettingsTemp['custom_feeds'];
					else
						$General['custom_feeds'] = array();
					$General['custom_feeds'][$FeedSlug] = $Feed['title'];
				}
			}
			
			// Update the settings in the database:
			if( $General )
			{
				if( !empty($_POST['action']) && $_POST['action'] == 'empowercast-save-settings' )
				{
					if( !isset($General['display_player_excerpt']) ) // If we are modifying appearance settings but this option was not checked...
						$General['display_player_excerpt'] = 0; // Set it to zero.
					
					//if( !isset($General['display_player_disable_mobile']) )
					//	$General['display_player_disable_mobile'] = 0;
					
					$General['disable_dashboard_stats'] = 0;
					if( !empty($_POST['DisableStatsInDashboard'] ) )
						$General['disable_dashboard_stats'] = 1;
					if( !isset($General['disable_dashboard_news'] ) )
						$General['disable_dashboard_news'] = 0;
					if( !isset($General['allow_feed_comments'] ) )
						$General['allow_feed_comments'] = 0;
						
					if( !isset($General['feed_links']) )
						$General['feed_links'] = 0;
                    if( !isset($General['suppress_unused_item_tags']) )
                        $General['suppress_unused_item_tags'] = 0;

                    if( !isset($General['skip_to_episode_settings']) )
                        $General['skip_to_episode_settings'] = 0;

					// Advanced Features
					if( !isset($General['player_options'] ) )
						$General['player_options'] = 0;
					if( !isset($General['cat_casting'] ) )
						$General['cat_casting'] = 0;
					if( !isset($General['channels'] ) )
						$General['channels'] = 0;
					if( !isset($General['taxonomy_podcasting'] ) )
						$General['taxonomy_podcasting'] = 0;
					if( !isset($General['posttype_podcasting'] ) )
						$General['posttype_podcasting'] = 0;
					if( !isset($General['playlist_player'] ) )
						$General['playlist_player'] = 0;
					if(!isset($General['empowercast_network']))
					    $General['empowercast_network'] = 0;
                    if(!isset($General['empowercast_accept_json']))
                        $General['empowercast_accept_json'] = 0;
                    if(!isset($General['pp_show_block_errors']))
                        $General['pp_show_block_errors'] = 0;

                    // Media Presentation Settings
					$PlayerSettings = array();
					if( !empty($_POST['PlayerSettings']) )
						$PlayerSettings = $_POST['PlayerSettings'];	
					if( empty($PlayerSettings['display_pinw']) )
						$PlayerSettings['display_pinw'] = 0;
					if( empty($PlayerSettings['display_media_player']) )
						$PlayerSettings['display_media_player'] = 0;
					if( empty($PlayerSettings['display_pinw']) ) $PlayerSettings['display_pinw'] = 0;
					if( empty($PlayerSettings['display_media_player']) ) $PlayerSettings['display_media_player'] = 0;
					
					$General['player_function'] = abs( $PlayerSettings['display_pinw'] - $PlayerSettings['display_media_player'] );
					$General['podcast_link'] = 0;
					if( !empty($PlayerSettings['display_download']) )
					{
						$General['podcast_link'] = 1;
						if( !empty($PlayerSettings['display_download_size']) )
						{
							$General['podcast_link'] = 2;
							if( !empty($PlayerSettings['display_download_duration']) )
								$General['podcast_link'] = 3;
						}
					}
					
					if( !isset($General['podcast_embed'] ) )
						$General['podcast_embed'] = 0;
					if( !isset($General['podcast_embed_in_feed'] ) )
						$General['podcast_embed_in_feed'] = 0;
					if( !isset($General['m4a'] ) )
						$General['m4a'] = '';
					if( !isset($General['new_window_nofactor'] ) )
						$General['new_window_nofactor'] = '';
						
					if( !isset($General['subscribe_links'] ) )
						$General['subscribe_links'] = false;	
					if( !isset($General['subscribe_feature_email'] ) )
						$General['subscribe_feature_email'] = false;

                    // for user role management
                    if (empty($General['use_caps']) && !empty($GeneralPrev['use_caps'])) { // turning user role management off
                        $users = array('administrator','editor', 'author', 'contributor', 'subscriber');
                        foreach( $users as $null => $user )
                        {
                            $role = get_role($user);
                            if( !empty($role) )
                            {
                                if( $role->has_cap('edit_podcast') )
                                    $role->remove_cap('edit_podcast');
                                if( $role->has_cap('view_podcast_stats') )
                                    $role->remove_cap('view_podcast_stats');
                            }
                        }
                        empowercast_page_message_add_notice( __('EmpowerCast Roles and Capabilities removed from WordPress Blog', 'empowercast') );
                    } else if (!empty($General['use_caps']) && empty($GeneralPrev['use_caps'])) { // turning user role management on
                        $users = array('administrator','editor', 'author'); // , 'contributor', 'subscriber');
                        foreach( $users as $null => $user )
                        {
                            $role = get_role($user);
                            if( !empty($role) )
                            {
                                if( !$role->has_cap('edit_podcast') )
                                    $role->add_cap('edit_podcast');
                                if( $user == 'administrator' && !$role->has_cap('view_podcast_stats') )
                                    $role->add_cap('view_podcast_stats');
                            }
                        }
                        empowercast_page_message_add_notice( __('EmpowerCast Roles and Capabilities added to WordPress Blog.', 'empowercast') );
                    }
				}
				else if( !empty($_POST['action']) && $_POST['action'] == 'empowercast-save-defaults' )
				{
					if( !isset($General['display_player_excerpt']) ) // If we are modifying appearance settings but this option was not checked...
						$General['display_player_excerpt'] = 0; // Set it to zero.
					$General['disable_dashboard_stats'] = 0;
					if( !empty($_POST['DisableStatsInDashboard'] ) )
						$General['disable_dashboard_stats'] = 1;
					
					// Advanced Mode options
					if( !isset($General['cat_casting'] ) )
						$General['cat_casting'] = 0;
					if( !isset($General['channels'] ) )
						$General['channels'] = 0;
					if( !isset($General['taxonomy_podcasting'] ) )
						$General['taxonomy_podcasting'] = 0;
					if( !isset($General['posttype_podcasting'] ) )
						$General['posttype_podcasting'] = 0;
                    if( !isset($General['playlist_player'] ) )
                        $General['playlist_player'] = 0;
                    if(!isset($General['empowercast_network']))
                        $General['empowercast_network'] = 0;
                    if(!isset($General['empowercast_accept_json']))
                        $General['empowercast_accept_json'] = 0;
                    if(!isset($General['pp_show_block_errors']))
                        $General['pp_show_block_errors'] = 0;
				}

				// seo settings
				if( !empty($_POST['action']) && $_POST['action'] == 'empowercast-save-settings' )
				{
					//$EmpowerCastSearch = $_POST['EmpowerCastSearch'];
					$EmpowerCastSearchToggle = $_POST['EmpowerCastSearchToggle'];
					if( empty($EmpowerCastSearchToggle['seo_feed_title']) )
						$General['seo_feed_title'] = 0;
				}

				// tags settings
				if( !empty($_POST['action']) && $_POST['action'] == 'empowercast-save-settings' )
				{
					if( !isset($General['write_tags']) ) // If we are modifying appearance settings but this option was not checked...
						$General['write_tags'] = 0; // Set it to zero.
						
					$TagValues = $_POST['TagValues'];
					$GeneralPosted = $_POST['General'];
					
					if( !empty($_POST['EmpowerCastTrackNumber']) ) {
						update_option('empowercast_track_number',  $_POST['EmpowerCastTrackNumber']);
					}
					// Set all the tag values...
					foreach( $GeneralPosted as $key => $value )
					{
						if( substr($key, 0, 4) == 'tag_' )
						{
							// Special case, we are uploading new coverart image
							if( !empty($_POST['coverart_image_checkbox']) && $key == 'tag_coverart' )
								continue;
								
							// Specail case, the track is saved in a separate column in the database.
							if( $key == 'tag_track' )
								continue; 
							
							if( !empty($value) )
								$General[$key] = $TagValues[$key];
							else
								$General[$key] = '';
						}
					}
					
					if( !empty($General['tag_coverart']) ) // $TagValues['tag_coverart'] != '' )
					{
						$GeneralSettingsTemp = empowercast_get_settings('empowercast_general', false);
						if( !empty($GeneralSettingsTemp['podops_hosting']) && $GeneralSettingsTemp['podops_hosting'] !== 'false' )
						{
                            require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
                            $auth = new EmpowerCastAuth();
							$json_data = false;
                            $api_url_array = empowercast_get_api_array();
                            $creds = get_option('empowercast_creds');
							if ($creds) {
                                $accessToken = empowercast_getAccessToken();
                                $req_url = sprintf('/2/media/%s/coverart.json?url=%s', $GeneralSettingsTemp['podops_program_keyword'], urlencode($TagValues['tag_coverart']));
                                $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA')?'?'. EMPOWERCAST_PODOPS_API_QSA:'');
                                $results = $auth->api($accessToken, $req_url, false, 'DELETE');
                            } else {
                                foreach ($api_url_array as $index => $api_url) {
                                    $req_url = sprintf('%s/media/%s/coverart.json?url=%s', rtrim($api_url, '/'), $GeneralSettingsTemp['podops_program_keyword'], urlencode($TagValues['tag_coverart']));
                                    $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
                                    $json_data = empowercast_remote_fopen($req_url, $GeneralSettingsTemp['podops_auth']);
                                    if (!$json_data && $api_url == 'https://api.podopshost.com/') { // Lets force cURL and see if that helps...
                                        $json_data = empowercast_remote_fopen($req_url, $GeneralSettingsTemp['podops_auth'], array(), 15, false, true);
                                    }
                                    if ($json_data != false)
                                        break;
                                }
                                // Lets try to cache the image onto Podops's Server...
                                $results = empowercast_json_decode($json_data);
                            }
								
							if( is_array($results) && !isset($results['error']) )
							{
								// Good!
								empowercast_page_message_add_notice( __('Coverart image updated successfully.', 'empowercast') );
							}
							else if( isset($results['error']) )
							{
								$error = __('Podops Hosting Error (updating coverart)', 'empowercast') .': '. $results['error'];
								empowercast_page_message_add_error($error);
							}
							else
							{
								$error = __('An error occurred updating the coverart with your Podops Services Account.', 'empowercast');
								empowercast_page_message_add_error($error);
							}
						}
						else
						{
							empowercast_page_message_add_error( __('Coverart Image was not uploaded to your Podops Services Account. It will NOT be added to your mp3s.', 'empowercast') );
						}
					}
				}
				
				if( !empty($_POST['action']) && $_POST['action'] == 'empowercast-save-videocommon' )
				{
					if( !isset($General['poster_play_image'] ) )
						$General['poster_play_image'] = 0;
					if( !isset($General['poster_image_audio'] ) )
						$General['poster_image_audio'] = 0;
				}
				
				// Wordpress adds slashes to everything, but since we're storing everything serialized, lets remove them...
				$General = empowercast_stripslashes($General);
				empowercast_save_settings($General);
			}
			
			if( $Feed )
			{
                $personNames = [];
                $personRoles = [];
                $personURLs = [];
                $linkURLs = [];
                $count = 1;
                while (true) {
                    if (!isset($_POST["role-$count-name"]))
                        break;

                    $name = htmlspecialchars($_POST["role-$count-name"]);

                    if ($name != "") {
                        $personNames[] = $name;
                        $personRoles[] = htmlspecialchars($_POST["role-$count-role"]);
                        $personURLs[] = htmlspecialchars($_POST["role-$count-personurl"]);
                        $linkURLs[] = htmlspecialchars($_POST["role-$count-linkurl"]);
                    }
                    $count += 1;
                }

                if (count($personNames) > 0) {
                    $Feed['person_names'] = $personNames;
                    $Feed['person_roles'] = $personRoles;
                    $Feed['person_urls'] = $personURLs;
                    $Feed['link_urls'] = $linkURLs;
                } else {
                    $Feed['person_names'] = [''];
                    $Feed['person_roles'] = [''];
                    $Feed['person_urls'] = [''];
                    $Feed['link_urls'] = [''];
                }

                // Experimental Features - Logic from experimental.php in pub to save shit
                $Feed['value_error'] = 'no';

                $valueErrorMsg = '';
                $valueError = false;
                $morePeople = true;
                $lightning = [];
                $splits = [];
                $pubKeys = [];
                $customKeys = [];
                $customValues = [];
                $count = 1;
                $validPeople = 0;
                while ($morePeople) {
                    if (isset($_POST["person-$count-pubkey"]))
                        $pubKey = $_POST["person-$count-pubkey"];
                    else
                        $pubKey = false;

                    if ($pubKey === false) {
                        $morePeople = false;
                    } else {
                        if ($pubKey != "" || $_POST["person-$count-split"] != "") {
                            $pubKeys[] = $pubKey;
                            $splits[] = $_POST["person-$count-split"];
                            $lightning[] = $_POST["person-$count-lightning"];
                            $customKeys[] = $_POST["person-$count-customkey"];
                            $customValues[] = $_POST["person-$count-customvalue"];
                            $validPeople += 1;
                        }
                    }
                    $count += 1;
                }

                foreach ($pubKeys as $pubKey) {
                    if ($pubKey == "") {
                        $Feed['value_error'] = 'yes';
                        $valueError = true;
                        $valueErrorMsg .= " PubKey is required for all recipients.";
                    }
                }

                $sum = 0;

                if (count($pubKeys) == 1)
                    $splits[0] = 100;

                foreach ($splits as $split) {
                    $sum += $split;
                }

                if (!$valueError) {
                    $Feed['value_error'] = "no";
                    $Feed['value_pubkey'] = $pubKeys;
                }

                $Feed['value_lightning'] = $lightning;
                $Feed['value_custom_key'] = $customKeys;
                $Feed['value_custom_value'] = $customValues;
                $Feed['value_split'] = $splits;

                if ($sum != 100 && $validPeople > 0) {
                    $Feed['value_error'] = "yes";
                    $valueErrorMsg .= " Split values should add up to 100.";
                }

                $Feed['value_error_message'] = $valueErrorMsg;

                $feed_url = "";
                if ($Category) {
                    if( !empty($General['cat_casting_podcast_feeds']) )
                        $feed_url = get_category_feed_link($Category, 'podcast');
                    else // Use the old link
                        $feed_url = get_category_feed_link($Category);
                } else if ($term_taxonomy_id) {
                    global $wpdb;
                    $term_info = $wpdb->get_results("SELECT term_id, taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = $term_taxonomy_id",  ARRAY_A);
                    $taxonomy_type = $term_info[0]['taxonomy'];
                    $feed_url = get_term_feed_link($term_taxonomy_id, $taxonomy_type, 'rss2');
                } else if ($podcast_post_type) {
                    $feed_url = get_post_type_archive_feed_link($podcast_post_type, $FeedSlug);
                } else // otherwise treat as a podcast channel
                {
                    $feed_url = get_feed_link($FeedSlug);
                }

                require_once('uuid5.class.php');
                if (!UUID::is_valid($Feed['live_item']['guid']) || $Feed['live_item']['status'] == 'Pending') {
                    $Feed['live_item']['guid'] = UUID::v5('ead4c236-bf58-58c6-a2c6-a6b28d128cb6', $Feed['live_item']['title'] . ' ' .rand(0, 1000000));
                    $Feed['live_item']['podping_status'] = -1;
                }

                $litEerror = false;
                update_option('lit_error', false);
                update_option('lit_error_msg', '');

                if ($Feed['live_item']['enabled'] == '1') {
                    if ($Feed['live_item']['start_date_time'] == "" || $Feed['live_item']['end_date_time'] == "" || $Feed['live_item']['title'] == "" || $Feed['live_item']['stream_link'] == "" || $Feed['live_item']['fallback_link'] == "") {
                        update_option('lit_error', true);
                        update_option('lit_error_msg', "Please fill out all required fields before enabling your live item.");
                        $litEerror = true;
                    }

                    // Ensure end date is after start date
                    $startDate = explode('T', $Feed['live_item']['start_date_time']);
                    $endDate = explode('T', $Feed['live_item']['end_date_time']);

                    $startUnix = strtotime($startDate[0] . ' ' . $startDate[1] . ' ' . htmlspecialchars($Feed['live_item']['timezone']));
                    $endUnix = strtotime($endDate[0] . ' ' . $endDate[1] . ' ' . htmlspecialchars($Feed['live_item']['timezone']));

                    if ($endUnix <= $startUnix) {
                        update_option('lit_error', true);
                        update_option('lit_error_msg', "Please choose an ending date and time which is after your starting date.");
                        $litEerror = true;
                    }

                    if ($startUnix <= time() && $Feed['live_item']['status'] == "Pending") {
                        update_option('lit_error', true);
                        update_option('lit_error_msg', 'Please choose a starting date and time in the future.');
                        $litEerror = true;
                    }

                    if ($litEerror) {
                        $Feed['live_item']['enabled'] = '0';
                    } else {
                        $Feed['live_item']['podping_status'] = intval($Feed['live_item']['podping_status']);
                        $reason = false;

                        if ($Feed['live_item']['status'] == 'Ended')
                            $reason = 'liveEnd';
                        elseif ($Feed['live_item']['status'] == 'Live')
                            $reason = 'live';

                        $creds = get_option('empowercast_creds');
                        $GeneralSettingsTemp = empowercast_get_settings('empowercast_general', false);
                        $pending = !$reason && $Feed['live_item']['podping_status'] != 0;
                        $live = $reason == 'live' && $Feed['live_item']['podping_status'] != 1;
                        $ended = $reason == 'liveEnd' && $Feed['live_item']['podping_status'] != 2;

                        if (($pending || $live || $ended) && (!empty($creds) || !empty($GeneralSettingsTemp['podops_auth']))) {
                            if (!(defined('EMPOWERCAST_DISABLE_PODPING') && EMPOWERCAST_DISABLE_PODPING)) {
                                require_once('empowercastpodping.class.php');
                                $Podping = new EmpowerCastPodping();
                                try {
                                    // because auth key is the same across the whole site and each feed url is being passed, it doesn't matter what program keyword we use to call the api
                                    $Podping->publish($feed_url, $GeneralSettingsTemp['podops_program_keyword'], $reason);
                                } catch (Exception $e) {
                                    if (defined('WP_DEBUG') && WP_DEBUG) {
                                        empowercast_add_error($e->getMessage());
                                    }
                                }

                                if ($reason == 'live')
                                    $Feed['live_item']['podping_status'] = 1;
                                else if ($reason == 'liveEnd')
                                    $Feed['live_item']['podping_status'] = 2;
                                else
                                    $Feed['live_item']['podping_status'] = 0;
                            }
                        }
                    }
                }

                if (isset($Feed['update_frequency'])) {
                    $updateFrequency = $Feed['update_frequency'];
                    $Feed['frequency'] = $updateFrequency == 1 ? 'Daily' : ($updateFrequency == 2 ? 'Weekly' : 'Monthly');

                    if ($updateFrequency == 2) { # Weekly
                        $selectedDayList = [];
                        for ($count=0; $count<7; $count++) {
                            $isChecked = isset($Feed['freq-day-'.$count]);

                            if ($isChecked)
                                $selectedDayList[] = $count;
                        }

                        $Feed['update_frequency_week'] = implode(',', $selectedDayList);
                    }
                }

                $blockAgree = $Feed['block'] ?? false;

                if (!isset($Feed['block_all']))
                    $Feed['block_all'] = 0;

                if ($blockAgree)
                    $Feed['block_list'] = implode(';', $Feed['block_list'] ?? []);

                $remoteItems = $Feed['remoteItems'] ?? [];

                if (!empty($remoteItems)) {
                    $newRemoteItems = [];
                    $podrollItems = $remoteItems['podroll'] ?? [];
                    $feedItems = $remoteItems['feed'] ?? [];

                    foreach ($podrollItems as $podcastGuid => $itemGuids) {
                        $newRemoteItems[] = array(
                            'feed_guid' => $podcastGuid,
                            'item_guid' => '',
                            'item_title' => $itemGuids[0],
                            'item_link' => $itemGuids['link'],
                            'podroll' => 1
                        );
                    }

                    foreach ($feedItems as $podcastGuid => $itemGuids) {
                        $previousItems = array();

                        foreach ($itemGuids as $itemGuid => $itemTitle) {
                            if (!in_array($itemGuid, $previousItems) && $itemGuid != 'link') {
                                $newRemoteItems[] = array(
                                    'feed_guid' => $podcastGuid,
                                    'item_guid' => $itemGuid != 'none' ? $itemGuid : '',
                                    'item_title' => $itemTitle,
                                    'item_link' => $itemGuids['link'],
                                    'podroll' => 0
                                );

                                $previousItems[] = $itemGuid;
                            }
                        }
                    }

                    $Feed['remote_items'] = $newRemoteItems;
                } else {
                    $Feed['remote_items'] = [];
                }
                unset($Feed['remoteItems']);

				if( !isset($_POST['ProtectContent']) && isset($Feed['premium']) )
					$Feed['premium'] = false;
				if( !isset($Feed['enhance_itunes_summary']) )
					$Feed['enhance_itunes_summary'] = false;
				if( !isset($Feed['itunes_author_post']) )
					$Feed['itunes_author_post'] = false;
                if( !isset($Feed['pp_enable_feed_lock']) )
                    $Feed['pp_enable_feed_lock'] = false;
                if( !isset($Feed['pp_enable_email']) )
                    $Feed['pp_enable_email'] = false;
                if( !isset($Feed['guid_override_check']) )
                    $Feed['guid_override_check'] = false;
					
				if( !isset($Feed['itunes_block']) )
					$Feed['itunes_block'] = false;
				if( !isset($Feed['itunes_complete']) )
					$Feed['itunes_complete'] = false;
				if( !isset($Feed['maximize_feed']) )
					$Feed['maximize_feed'] = false;
                if( !isset($Feed['unlock_podcast']) )
                    $Feed['unlock_podcast'] = false;
				if( !isset($Feed['donate_link']) )
					$Feed['donate_link'] = false;
				if( !isset($Feed['episode_itunes_image']) )
					$Feed['episode_itunes_image'] = false;


				$Feed = empowercast_stripslashes($Feed);
				if( $Category )
				{
					empowercast_save_settings($Feed, 'empowercast_cat_feed_'.$Category);
				}
				else if ( $term_taxonomy_id )
				{
					empowercast_save_settings($Feed, 'empowercast_taxonomy_'.$term_taxonomy_id);
				}
				else if( $podcast_post_type )
				{
					$PostTypeSettings = array();
					$PostTypeSettings[ $FeedSlug ] = $Feed;
					empowercast_save_settings($PostTypeSettings, 'empowercast_posttype_'.$podcast_post_type);
					empowercast_rebuild_posttype_podcasting();
				}
				else // otherwise treat as a podcast channel
				{
					if( $FeedSlug == false && get_option('empowercast_feed_podcast') ) // If the settings were moved to the podcast channels feature...
						empowercast_save_settings($Feed, 'empowercast_feed_podcast' ); // save a copy here if that is the case.
					
					empowercast_save_settings($Feed, 'empowercast_feed'.($FeedSlug?'_'.$FeedSlug:'') );
				}
			}
			
			if( isset($_POST['EpisodeBoxBGColor']) )
			{
				$GeneralSettingsTemp = get_option('empowercast_general', array());
				$SaveEpisdoeBoxBGColor['episode_box_background_color'] = array();
				if( isset($GeneralSettingsTemp['episode_box_background_color']) )
					$SaveEpisdoeBoxBGColor['episode_box_background_color'] = $GeneralSettingsTemp['episode_box_background_color']; //  copy previous settings
				
				foreach( $_POST['EpisodeBoxBGColor'] as $feed_slug_temp => $background_color ) {
					break;
				}
				$SaveEpisdoeBoxBGColor['episode_box_background_color'][ $feed_slug_temp ] = $background_color;
				empowercast_save_settings($SaveEpisdoeBoxBGColor);
			}

			if (isset($_POST['podops-login'])) {
                check_admin_referer('empowercast-edit');
                $pp_nonce = empowercast_login_create_nonce();
                $tab_string = isset($_POST['tab']) ? "&tab={$_POST['tab']}" : "";
                $sidenav_tab_string = isset($_POST['sidenav-tab']) ? "&sidenav-tab={$_POST['sidenav-tab']}" : "";
                header("Location: " . add_query_arg( '_wpnonce', $pp_nonce, admin_url("admin.php?page=empowercastadmin_onboarding.php&step=podopsSignin&from=empowercastadmin_basic{$tab_string}{$sidenav_tab_string}")));
            }

			// Anytime settings are saved lets flush the rewrite rules
			$wp_rewrite->flush_rules();
			// Settings saved successfully
			if( !empty($_POST['action']) )
			{
				switch( $_POST['action'] )
				{
					case 'empowercast-save-settings':
					case 'empowercast-save-defaults': {
						empowercast_page_message_add_notice( __('Podops EmpowerCast settings saved.', 'empowercast') );
					}; break;
					case 'empowercast-save-channel': {
						empowercast_page_message_add_notice( __('Podops EmpowerCast Channel settings saved.', 'empowercast') );
                        $_GET['action'] = 'empowercast-editfeed';
                        $_GET['feed_slug'] = $FeedSlug;
					}; break;
					case 'empowercast-save-category': {
						empowercast_page_message_add_notice( __('Podops EmpowerCast Category Podcasting  settings saved.', 'empowercast') );
                        $_GET['action'] = 'empowercast-editcategoryfeed';
                        $_GET['cat'] = $Category;
					}; break;
					case 'empowercast-save-ttid': {
						empowercast_page_message_add_notice( __('Podops EmpowerCast Taxonomy Podcasting settings saved.', 'empowercast') );
                        $_GET['action'] = 'empowercast-edittaxonomyfeed';
                        if (empty($_GET['term'])) {
                            $_GET['term'] = false;
                        }
                        $term_ID = intval( isset($_POST['term'])? $_POST['term'] : $_GET['term'] );
                        $_GET['term'] = $term_ID;
                        $_GET['ttid'] = $term_taxonomy_id;
					}; break;
					case 'empowercast-save-post_type': {
						empowercast_page_message_add_notice( __('Podops EmpowerCast Post Type Podcasting settings saved.', 'empowercast') );
                        $_GET['action'] = 'empowercast-editposttypefeed';
                        $_GET['feed_slug'] = $FeedSlug;
                        $_GET['podcast_post_type'] = $podcast_post_type;
					}; break;
					default: {
						empowercast_page_message_add_notice( __('Podops EmpowerCast settings saved.', 'empowercast') );
					}; break;
				}
			}
		}
		
		// Handle POST actions...
		if( isset($_POST['action'] ) )
		{
			switch($_POST['action'])
			{
				case 'empowercast-addfeed': {
					check_admin_referer('empowercast-add-feed');
					
					$Settings = get_option('empowercast_general', array());
					$key = sanitize_title($_POST['feed_slug']);
					$value = $_POST['feed_name'];
					$value = empowercast_stripslashes($value);
					
					/*
					if( isset($Settings['custom_feeds'][ $key ]) && empty($_POST['overwrite']) )
					{
						empowercast_page_message_add_error( sprintf(__('Feed slug "%s" already exists.'), $key) );
					} else */
					if( $key == '' )
					{
						empowercast_page_message_add_error( sprintf(__('Feed slug "%s" is not valid.', 'empowercast'), esc_html($_POST['feed_slug']) ) );
					}
					else if( in_array($key, $wp_rewrite->feeds)  && !isset($Settings['custom_feeds'][ $key ]) ) // If it is a system feed or feed created by something else
					{
						empowercast_page_message_add_error( sprintf(__('Feed slug "%s" is not available.', 'empowercast'), esc_html($key) ) );
					}
					else
					{
						$Settings['custom_feeds'][ $key ] = $value;
						empowercast_save_settings($Settings);
						
						add_feed($key, 'empowercast_do_podcast_feed'); // Before we flush the rewrite rules we need to add the new custom feed...
						$wp_rewrite->flush_rules();
						
						empowercast_page_message_add_notice( sprintf(__('Podcast Feed "%s" added, please configure your new feed now.', 'empowercast'), esc_html($value) ) );
						$_GET['action'] = 'empowercast-editfeed';
						$_GET['feed_slug'] = $key;
					}
				}; break;
				case 'empowercast-addtaxonomyfeed': {
					if( !empty($_POST['cancel']) )
						unset($_POST['taxonomy']);
					
					if( empty($_POST['add_podcasting']) )
						break; // We do not handle this situation
				}
				case 'empowercast-addcategoryfeed': {
				
					check_admin_referer('empowercast-add-taxonomy-feed');
					
					
					
				
					$taxonomy_type = ( isset($_POST['taxonomy'])? $_POST['taxonomy'] : $_GET['taxonomy'] );
					$term_ID = intval( isset($_POST['term'])? $_POST['term'] : $_GET['term'] );
					
					
					$term_object = get_term( $term_ID, $taxonomy_type, OBJECT, 'edit');
					
					if( empty($term_ID) )
					{
						if( $taxonomy_type == 'category' )
							empowercast_page_message_add_error( __('You must select a category to continue.', 'empowercast') );
						else
							empowercast_page_message_add_error( __('You must select a term to continue.', 'empowercast') );
					}
					else if( $term_object == false )
					{
						empowercast_page_message_add_error( __('Error obtaining term information.', 'empowercast') );
					}
					else if( $taxonomy_type == 'category' )
					{
						$Settings = get_option('empowercast_general', array());
						if( empty($Settings['custom_cat_feeds']) )
							$Settings['custom_cat_feeds'] = array();
						
						if( !in_array($term_ID, $Settings['custom_cat_feeds']) )
						{
							$Settings['custom_cat_feeds'][] = $term_ID;
							empowercast_save_settings($Settings);
						}
					
						empowercast_page_message_add_notice( __('Please configure your category podcast feed now.', 'empowercast') );
						
						$_GET['action'] = 'empowercast-editcategoryfeed';
						$_GET['cat'] = $term_ID;
					}
					else
					{
						
			
						//$term_info = term_exists($term_ID, $taxonomy_type);
						$tt_id = $term_object->term_taxonomy_id;
						
						if( !$tt_id )
						{
						
						}
						else
						{
							$Settings = get_option('empowercast_taxonomy_podcasting', array());
			
							if( !isset($Settings[ $tt_id ])  )
							{
								$Settings[ $tt_id ] = true;
								empowercast_save_settings($Settings, 'empowercast_taxonomy_podcasting'); // add the feed to the taxonomy podcasting list
							}
						
							empowercast_page_message_add_notice( __('Please configure your taxonomy podcast now.', 'empowercast') );
							
							$_GET['action'] = 'empowercast-edittaxonomyfeed';
							$_GET['term'] = $term_ID;
							$_GET['ttid'] = $tt_id;
						}
					}
				}; break;
				case 'empowercast-addposttypefeed': {
					
					
					check_admin_referer('empowercast-add-posttype-feed');
					//die('ok 2');
					
					$Settings = get_option('empowercast_general', array());
					$feed_slug = sanitize_title($_POST['feed_slug']);
					$post_type = $_POST['podcast_post_type'];
					$post_type = empowercast_stripslashes($post_type);
					$feed_title = $_POST['feed_title'];
					$feed_title = empowercast_stripslashes($feed_title);
					
					
					
					/*
					if( isset($Settings['custom_feeds'][ $key ]) && empty($_POST['overwrite']) )
					{
						empowercast_page_message_add_error( sprintf(__('Feed slug "%s" already exists.'), $key) );
					} else */
					if( empty($feed_slug) )
					{
						empowercast_page_message_add_error( sprintf(__('Feed slug "%s" is not valid.', 'empowercast'), esc_html($_POST['feed_slug']) ) );
					}
					else if( empty($post_type) )
					{
						empowercast_page_message_add_error( __('Post Type is invalid.', 'empowercast') );
					}
					// TODO:
					//else if( in_array($feed_slug, $wp_rewrite->feeds)  && !isset($Settings['custom_feeds'][ $key ]) ) // If it is a system feed or feed created by something else
					//{
					//	empowercast_page_message_add_error( sprintf(__('Feed slug "%s" is not available.', 'empowercast'), $key) );
					//}
					else
					{
						$ExistingSettings = empowercast_get_settings('empowercast_posttype_'. $post_type);
						if( !empty($ExistingSettings[ $feed_slug ]) )
						{
							empowercast_page_message_add_error( sprintf(__('Feed slug "%s" already exists.', 'empowercast'), $_POST['feed_slug']) );
						}
						else
						{
							$NewSettings = array();
							$NewSettings[ $feed_slug ]['title'] = $feed_title;
							empowercast_save_settings($NewSettings, 'empowercast_posttype_'. $post_type);
							
							
							add_feed($feed_slug, 'empowercast_do_podcast_feed'); // Before we flush the rewrite rules we need to add the new custom feed...
							$wp_rewrite->flush_rules();
							
							empowercast_page_message_add_notice( sprintf(__('Podcast "%s" added, please configure your new podcast.', 'empowercast'), $feed_title) );
							$_GET['action'] = 'empowercast-editposttypefeed';
							$_GET['feed_slug'] = $feed_slug;
							$_GET['podcast_post_type'] = $post_type;
						}
					}
				}; break;
				case 'empowercast-ping-sites': {
					check_admin_referer('empowercast-ping-sites');
					
					require_once( EMPOWERCAST_ABSPATH . '/empowercastadmin-ping-sites.php');
					empowercastadmin_ping_sites_process();
					
					$_GET['action'] = 'empowercast-ping-sites';
				}; break;
				case 'empowercast-find-replace': {
					check_admin_referer('empowercast-find-replace');
					
					require_once( EMPOWERCAST_ABSPATH . '/empowercastadmin-find-replace.php');
					empowercastadmin_find_replace_process();
					
					$_GET['action'] = 'empowercast-find-replace';
				}; break;
				case 'empowercast-importpodpress': {
					check_admin_referer('empowercast-import-podpress');
					
					require_once( EMPOWERCAST_ABSPATH . '/empowercastadmin-podpress.php');
					empowercastadmin_podpress_do_import();
					
					$_GET['action'] = 'empowercast-podpress-epiosdes';
				}; break;
				case 'empowercast-importmt': {
					check_admin_referer('empowercast-import-mt');
					
					require_once( EMPOWERCAST_ABSPATH . '/empowercastadmin-mt.php');
					empowercastadmin_mt_do_import();
					
					$_GET['action'] = 'empowercast-mt-epiosdes';
				}; break;
				case 'deletepodpressdata': {
					check_admin_referer('empowercast-delete-podpress-data');
					
					require_once( EMPOWERCAST_ABSPATH . '/empowercastadmin-podpress.php');
					empowercastadmin_podpress_delete_data();
					
				}; break;
				case 'empowercast-category-settings': {
					// Save here!
					check_admin_referer('empowercast-category-settings');
					
					if( isset($_POST['cat_casting_podcast_feeds']) && isset($_POST['cat_casting_strict']) )
					{
						$Save = array('cat_casting_podcast_feeds'=>$_POST['cat_casting_podcast_feeds'], 'cat_casting_strict'=>$_POST['cat_casting_strict']);
						empowercast_save_settings($Save);
						empowercast_page_message_add_notice( __('Settings saved successfully.', 'empowercast') );
					}
					
				}; break;
			}
		}
		
		// Handle GET actions...
		if( isset($_GET['action'] ) )
		{
			switch( $_GET['action'] )
			{
                case 'empowercast-sync-progad': {
                    function buildRedirect($Redirects) {
                        $redirect_result = '';
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

                                    if( !strstr($redirect_result, $RedirectClean) ) // If the redirect is not already added...
                                        $redirect_result = $RedirectClean . $redirect_result;
                                }
                            }
                        }
                        return 'https://' . $redirect_result;
                    }
                    // grab the redirect url prefixes for each feed slug and make an array
                    $General = empowercast_get_settings('empowercast_general');
                    // append general redirects to each other (starting with redirect1)
                    $Redirects = array('redirect0'=>'', 'redirect1'=>'', 'redirect2'=>'', 'redirect3'=>'');
                    if( !empty($General['redirect1']) )
                        $Redirects['redirect1'] = $General['redirect1'];
                    if( !empty($General['redirect2']) )
                        $Redirects['redirect2'] = $General['redirect2'];
                    if( !empty($General['redirect3']) )
                        $Redirects['redirect3'] = $General['redirect3'];

                    // add to redirect array with key 'enclosure'
                    $main_redirect = buildRedirect($Redirects);
                    $redirect_array = array('enclosure' => $main_redirect);

                    // then append custom feed redirects to beginning of main feed redirect with _slug:enclosure for each custom feed
                    // channels
                    if (!empty($General['custom_feeds'])) {
                        foreach ($General['custom_feeds'] as $slug => $title) {
                            $Feed = get_option('empowercast_feed_' . $slug, array());
                            if (!empty($Feed['redirect'])) {
                                $Redirects['redirect0'] = $Feed['redirect'];
                                $redirect_array += array('_' . $slug . ':enclosure' => buildRedirect($Redirects));
                                $Redirects['redirect0'] = '';
                            } else {
                                $redirect_array += array('_' . $slug . ':enclosure' => $main_redirect);
                                // default stats redirect
                            }
                        }
                    }

                    // categories
                    if (!empty($General['custom_cat_feeds'])) {
                        foreach ($General['custom_cat_feeds'] as $idx => $id) {
                            $category = get_category($id);
                            // $category['slug']
                            $Feed = get_option('empowercast_cat_feed_' . $id, array());
                            if (!empty($Feed['redirect'])) {
                                $Redirects['redirect0'] = $Feed['redirect'];
                                $redirect_array += array('_' . $category->slug . ':enclosure' => buildRedirect($Redirects));
                                $Redirects['redirect0'] = '';
                            } else {
                                $redirect_array += array('_' . $category->slug . ':enclosure' => $main_redirect);
                                // default stats redirect
                            }
                        }
                    }

                    // taxonomies
                    $EmpowerCastTaxonomies = get_option('empowercast_taxonomy_podcasting', array());
                    if (!empty($EmpowerCastTaxonomies)) {
                        foreach ($EmpowerCastTaxonomies as $tt_id => $null) {

                            $taxonomy_type = '';
                            $term_ID = '';

                            global $wpdb;
                            $term_info = $wpdb->get_results("SELECT term_id, taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = $tt_id", ARRAY_A);
                            if (!empty($term_info[0]['term_id'])) {
                                $term_ID = $term_info[0]['term_id'];
                                $taxonomy_type = $term_info[0]['taxonomy'];
                            } else {
                                continue; // we didn't find this taxonomy relationship
                            }

                            $Feed = empowercast_get_settings('empowercast_taxonomy_' . $tt_id);
                            $term_object = get_term( $term_ID, $taxonomy_type, OBJECT, 'edit');
                            if (!empty($Feed['redirect'])) {
                                $Redirects['redirect0'] = $Feed['redirect'];
                                $redirect_array += array('_' . $term_object->slug . ':enclosure' => buildRedirect($Redirects));
                                $Redirects['redirect0'] = '';
                            } else {
                                $redirect_array += array('_' . $term_object->slug . ':enclosure' => $main_redirect);
                                // default stats redirect
                            }
                        }
                    }

                    // post types
                    $post_types = empowercast_admin_get_post_types(false);
                    if (!empty($post_types)) {
                        foreach ($post_types as $null => $post_type) {
                            $PostTypeSettingsArray = get_option('empowercast_posttype_' . $post_type, array());
                            if (empty($PostTypeSettingsArray))
                                continue;

                            foreach ($PostTypeSettingsArray as $feed_slug => $Feed) {
                                if (!empty($Feed['redirect'])) {
                                    $Redirects['redirect0'] = $Feed['redirect'];
                                    $redirect_array += array('_' . $feed_slug . ':enclosure' => buildRedirect($Redirects));
                                    $Redirects['redirect0'] = '';
                                } else {
                                    $redirect_array += array('_' . $feed_slug . ':enclosure' => $main_redirect);
                                    // default stats redirect
                                }
                            }
                        }
                    }

                    // figure out which shows we are enabling/disabling
                    $progad_error = '';
                    $progad_enable_urls = array();
                    $progad_disable_urls = array();
                    $auth = new EmpowerCastAuth();
                    $accessToken = empowercast_getAccessToken();
                    $req_url = sprintf('/2/media/prog_ad_status.json?cache=' . md5(rand(0, 999) . time()));
                    $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                    $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                    $progad_enabled_shows = $auth->api($accessToken, $req_url, array(), false, 60 * 30);
                    if (!$progad_enabled_shows) {
                        $progad_error = $auth->getLastError();
                    }
                    $past_shows_with_progad = get_option('pp_programmatic_enabled_shows');
                    if (!empty($past_shows_with_progad) && !empty($progad_enabled_shows['programs'])) {
                        $shows_to_enable = array_diff($progad_enabled_shows['programs'], $past_shows_with_progad);
                        $shows_to_disable = array_diff($past_shows_with_progad, $progad_enabled_shows['programs']);
                    } elseif (!empty($past_shows_with_progad) && empty($progad_enabled_shows['programs'])) {
                        $shows_to_disable = $past_shows_with_progad;
                    } elseif (!empty($progad_enabled_shows['programs']) && empty($past_shows_with_progad)) {
                        $shows_to_enable = $progad_enabled_shows['programs'];
                    }
                    update_option('pp_programmatic_enabled_shows', $progad_enabled_shows['programs']);

                    // use the API to get associated URLs for all URLs in any program whose ads were just enabled
                    if (!empty($shows_to_enable)) {
                        foreach ($shows_to_enable as $idx => $keyword) {
                            $req_url = sprintf('/2/media/' . $keyword . '/prog_ad_urls.json?cache=' . md5(rand(0, 999) . time()));
                            if (defined('EMPOWERCAST_PROGRAMMATIC_FIX')) {
                                $req_url .= '&pp_first_release_fix=true';
                            }
                            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                            $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                            $result_prog = $auth->api($accessToken, $req_url, array(), false, 60 * 30);
                            if (isset($result_prog['urls']) && is_array($result_prog['urls'])) {
                                foreach ($result_prog['urls'] as $i => $url_pair) {
                                    // add the redirect to the key before adding this pair
                                    $progad_enable_urls += $url_pair;
                                }
                            } elseif (isset($result_prog['message']) && $result_prog['message'] == 'no media') {
                                // no error--continue
                            }
                            else {
                                $progad_error = $auth->getLastError();
                            }
                        }
                    }

                    // use the API to get associated URLs for all URLs in any program whose ads were just disabled
                    if (!empty($shows_to_disable)) {
                        foreach ($shows_to_disable as $idx => $keyword) {
                            $req_url = sprintf('/2/media/' . $keyword . '/prog_ad_urls.json?disable=true&cache=' . md5(rand(0, 999) . time()));
                            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                            $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                            $result_prog = $auth->api($accessToken, $req_url, array(), false, 60 * 30);
                            $progad_error = $auth->getLastError();
                            if (isset($result_prog['urls']) && is_array($result_prog['urls'])) {
                                foreach ($result_prog['urls'] as $i => $url_pair) {
                                    // add the redirect to the key before adding this pair
                                    $progad_disable_urls += $url_pair;
                                }
                            } elseif (isset($result_prog['message']) && $result_prog['message'] == 'no media') {
                                // no error--continue
                            }
                            else {
                                $progad_error = $auth->getLastError();
                            }
                        }
                    }

                    // query the wordpress database to match up the URLs that we need to update
                    global $wpdb;
                    $query = "SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE \"%enclosure\"";
                    $results_data = $wpdb->get_results($query, ARRAY_A);
                    foreach ($results_data as $idx => $data) {
                        $meta_parts = explode("\n", $data['meta_value']);

                        if (strpos($meta_parts[0], 'ins.podopshost.com')) {
                            $parts_array = explode('ins.podopshost.com', $meta_parts[0]);
                        } else if (strpos($meta_parts[0], 'content3.podops.biz')) {
                            $parts_array = explode('content3.podops.biz', $meta_parts[0]);
                        } else if (strpos($meta_parts[0], 'mc.podopshost.com')) {
                            $parts_array = explode('mc.podopshost.com', $meta_parts[0]);
                        } elseif (strpos($meta_parts[0], 'content.podopshost.com')) {
                            $parts_array = explode('content.podopshost.com', $meta_parts[0]);
                        } else {
                            // not Podops hosted
                            continue;
                        }
                        $url_without_prefix = $parts_array[1];
                        $parts_drop_qs = explode('?', $url_without_prefix);
                        if (!empty($progad_enable_urls) && array_key_exists($parts_drop_qs[0], $progad_enable_urls)) {
                            // now, if they have a redirect for the feed that this url is in, we need to replace the https://media.podopshost.com/{keyword}/ with those redirects
                            $progad_url_with_pp_redirect = preg_replace('#https://media.podopshost.com/(.*)/#U', $redirect_array[$data['meta_key']], $progad_enable_urls[$parts_drop_qs[0]]);
                            // replace the url in the meta_parts array, implode it back together, and update the program meta
                            $meta_parts[0] = $progad_url_with_pp_redirect;
                            $new_meta_value = implode("\n", $meta_parts);
                            update_post_meta($data['post_id'], $data['meta_key'], $new_meta_value);
                        } else if (!empty($progad_disable_urls) && array_key_exists($parts_drop_qs[0], $progad_disable_urls)) {
                            $hosting_url_with_pp_redirect = preg_replace('#http(s?)://#U', $redirect_array[$data['meta_key']], $progad_disable_urls[$parts_drop_qs[0]]);
                            // replace the url in the meta_parts array, implode it back together, and update the program meta
                            $meta_parts[0] = $hosting_url_with_pp_redirect;
                            $new_meta_value = implode("\n", $meta_parts);
                            update_post_meta($data['post_id'], $data['meta_key'], $new_meta_value);
                        }
                    }
                    if ($progad_error) {
                        empowercast_add_error("Error syncing Programmatic Advertising Settings: " . $progad_error);
                    } else {
                        empowercast_page_message_add_notice("Successfully synced Programmatic Advertising Settings from Podops.", 'inline', false);
                    }
                }; break;
                case 'empowercast_dismiss': {
                    update_option('empowercast_' . $_GET['notice'] . '_notice_dismissed', 'true');
                    header('Location: ' . admin_url('admin.php?page=empowercastadmin_basic'));
                } break;
				case 'empowercast-enable-categorypodcasting': {
					check_admin_referer('empowercast-enable-categorypodcasting');
					
					$Settings = get_option('empowercast_general', array());
					$Settings['cat_casting'] = 1;
					empowercast_save_settings($Settings);
					
					wp_redirect('edit-tags.php?taxonomy=category&message=3');
					exit;
					
				}; break;
				case 'empowercast-addcategoryfeed': {
					check_admin_referer('empowercast-add-taxonomy-feed');
					$cat_ID = intval($_GET['cat']);
					
					$Settings = get_option('empowercast_general', array());
					$category = get_category($cat_ID);
					if( $category == false )
					{
						empowercast_page_message_add_error( __('Error obtaining category information.', 'empowercast') );
					}
					else
					{
						if( empty($Settings['custom_cat_feeds']) || !is_array($Settings['custom_cat_feeds']) )
							$Settings['custom_cat_feeds'] = array();
						
						if( !in_array($cat_ID, $Settings['custom_cat_feeds']) )
						{
							$Settings['custom_cat_feeds'][] = $cat_ID;
							empowercast_save_settings($Settings);
						}
					
						empowercast_page_message_add_notice( __('Please configure your category podcast feed now.', 'empowercast') );
						
						$_GET['action'] = 'empowercast-editcategoryfeed';
						$_GET['cat'] = $cat_ID;
					}
				}; break;
				case 'empowercast-delete-feed': {
					$delete_slug = $_GET['feed_slug'];
					$force_deletion = !empty($_GET['force']);
					check_admin_referer('empowercast-delete-feed-'.$delete_slug);
					
					$Episodes = empowercast_admin_episodes_per_feed($delete_slug);
					
					if( false && $delete_slug == 'podcast' && $force_deletion == false ) // Feature disabled, you can now delete podcast specific settings
					{
						empowercast_page_message_add_error( __('Cannot delete default podcast feed.', 'empowercast') );
					}
					else if( $delete_slug != 'podcast' && $Episodes > 0 && $force_deletion == false )
					{
						empowercast_page_message_add_error( sprintf(__('Cannot delete feed. Feed contains %d episode(s).', 'empowercast'), $Episodes) );
					}
					else
					{
						$Settings = get_option('empowercast_general', array());
						unset($Settings['custom_feeds'][ $delete_slug ]);
						empowercast_save_settings($Settings); // Delete the feed from the general settings
						delete_option('empowercast_feed_'.$delete_slug); // Delete the actual feed settings
						
						// Now we need to update the rewrite cso the cached rules are up to date
						if ( in_array($delete_slug, $wp_rewrite->feeds))
						{
							$index = array_search($delete_slug, $wp_rewrite->feeds);
							if( $index !== false )
								unset($wp_rewrite->feeds[$index]); // Remove the old feed
						}
					
						// Remove feed function hook
						$hook = 'do_feed_' . $delete_slug;
						remove_action($hook, $hook, 10, 1); // This may not be necessary
						$wp_rewrite->flush_rules(); // This is definitely necessary
						
						empowercast_page_message_add_notice( __('Feed deleted successfully.', 'empowercast') );
					}
				}; break;
				case 'empowercast-delete-category-feed': {
					$cat_ID = intval($_GET['cat']);
					check_admin_referer('empowercast-delete-category-feed-'.$cat_ID);
					
					$Settings = get_option('empowercast_general', array());
					$key = array_search($cat_ID, $Settings['custom_cat_feeds']);
					if( $key !== false )
					{
						unset( $Settings['custom_cat_feeds'][$key] );
						empowercast_save_settings($Settings); // Delete the feed from the general settings
					}
					delete_option('empowercast_cat_feed_'.$cat_ID); // Delete the actual feed settings
					
					empowercast_page_message_add_notice( __('Removed podcast settings for category feed successfully.', 'empowercast') );
				}; break;
				case 'empowercast-delete-taxonomy-feed': {
					$tt_ID = intval($_GET['ttid']);
					check_admin_referer('empowercast-delete-taxonomy-feed-'.$tt_ID);
					
					$Settings = get_option('empowercast_taxonomy_podcasting', array());
					if( !empty($Settings[ $tt_ID ]) )
					{
						unset( $Settings[ $tt_ID ] );
						empowercast_save_settings($Settings, 'empowercast_taxonomy_podcasting'); // Delete the feed from the general settings
					}
					delete_option('empowercast_taxonomy_'.$tt_ID); // Delete the actual feed settings
					
					empowercast_page_message_add_notice( __('Removed podcast settings for term successfully.', 'empowercast') );
				}; break;
				case 'empowercast-delete-posttype-feed': {
				
					// check admin referer prevents xss
					$feed_slug = esc_attr($_GET['feed_slug']);
					$post_type = esc_attr($_GET['podcast_post_type']);
					check_admin_referer('empowercast-delete-posttype-feed-'.$post_type .'_'.$feed_slug);
			
					$Settings = get_option('empowercast_posttype_'.$post_type, array());
					if( !empty($Settings[ $feed_slug ]) )
					{
						unset( $Settings[ $feed_slug ] );
						update_option('empowercast_posttype_'.$post_type,  $Settings);
						//empowercast_save_settings($Settings, 'empowercast_posttype_'.$post_type); // Delete the feed from the general settings
					}
							
					empowercast_page_message_add_notice( __('Removed podcast settings for post type successfully.', 'empowercast') );
				}; break;
				case 'empowercast-podpress-settings': {
					check_admin_referer('empowercast-podpress-settings');
					
					// Import settings here..
					if( empowercast_admin_import_podpress_settings() )
						empowercast_page_message_add_notice( __('Podpress settings imported successfully.', 'empowercast') );
					else
						empowercast_page_message_add_error( __('No Podpress settings found.', 'empowercast') );
					
				}; break;
				case 'empowercast-podcasting-settings': {
					check_admin_referer('empowercast-podcasting-settings');
					
					// Import settings here..
					if( empowercast_admin_import_podcasting_settings() )
						empowercast_page_message_add_notice( __('Settings imported from the plugin "Podcasting" successfully.', 'empowercast') );
					else
						empowercast_page_message_add_error( __('No settings found for the plugin "Podcasting".', 'empowercast') );
					
				}; break;
				case 'empowercast-add-feed-caps': {
					check_admin_referer('empowercast-add-feed-caps');
					
					$ps_role = get_role('premium_subscriber');
					if( empty($ps_role) )
					{
						add_role('premium_subscriber', __('Premium Subscriber', 'empowercast'));
						$ps_role = get_role('premium_subscriber');
						$ps_role->add_cap('read');
						$ps_role->add_cap('premium_content');
					}
					
					$users = array('administrator','editor', 'author'); // , 'contributor', 'subscriber');
					foreach( $users as $null => $user )
					{
						$role = get_role($user);
						if( !empty($role) )
						{
							if( !$role->has_cap('premium_content') )
								$role->add_cap('premium_content');
						}
					}
					
					$General = array('premium_caps'=>true);
					empowercast_save_settings($General);
					empowercast_page_message_add_notice( __('Podcast Password Protection Capabilities for Custom Channel Feeds added successfully.', 'empowercast') );
					
				}; break;
				case 'empowercast-remove-feed-caps': {
					check_admin_referer('empowercast-remove-feed-caps');
					
					$users = array('administrator','editor', 'author', 'contributor', 'subscriber', 'premium_subscriber', 'empowercast');
					foreach( $users as $null => $user )
					{
						$role = get_role($user);
						if( !empty($role) )
						{
							if( $role->has_cap('premium_content') )
								$role->remove_cap('premium_content');
						}
					}
					
					remove_role('premium_subscriber');
					
					$General = array('premium_caps'=>false);
					empowercast_save_settings($General);
					empowercast_page_message_add_notice( __('Podcast Password Protection Capabilities for Custom Channel Feeds removed successfully.', 'empowercast') );
					
				}; break;
				case 'empowercast-clear-update_plugins': {
					check_admin_referer('empowercast-clear-update_plugins');
					
					delete_option('update_plugins'); // OLD method
					delete_option('_site_transient_update_plugins'); // New method
					empowercast_page_message_add_notice( sprintf( __('Plugins Update Cache cleared successfully. You may now to go the %s page to see the latest plugin versions.', 'empowercast'), '<a href="'. admin_url() .'plugins.php" title="'.  __('Manage Plugins', 'empowercast') .'">'.  __('Manage Plugins', 'empowercast') .'</a>'), 'inline', false );
					
				}; break;
				case 'empowercast-ios11-fields': {
					check_admin_referer('empowercast-ios11-fields');
					
					$General = array('ios11_fields'=> (!empty($_REQUEST['variation'])? $_REQUEST['variation'] : '0') );
					empowercast_save_settings($General);
					empowercast_page_message_add_notice( 'iOS 11 program level fields changed.' );
				}; break;
			}
		}
		
		if( isset($_REQUEST['action']) )
		{
			switch( $_REQUEST['action'] )
			{
				case 'empowercast-migrate-media': {
					
					require_once( EMPOWERCAST_ABSPATH . '/empowercastadmin-migrate.php');
					empowercast_admin_migrate_request();
				
				}; break;
			}
		}

        // check for ongoing migration, if one exisits, call the function to display
        $migrationInprogress = get_option('empowercast_migrate_queued', array());
        $migrationComplete = get_option('empowercast_migrate_completed', array());
        $migrationStatus = get_option('empowercast_migrate_status', array());
        $migrationResults = get_option('empowercast_migrate_results', array());
        $oneMonthAgo = strtotime('-1 month', current_time('timestamp'));

        // disable the cron task if:
        // - have have the wp-option 'empowercast_migrate_completed', they have completed all three migration steps
        // - the migration is over a month old
        // - they do not have the migration complete option but have the results option (older migrations)
        if (!empty($migrationComplete) || (!empty($migrationStatus) && $migrationStatus['updated_timestamp'] < $oneMonthAgo) || (empty($migrationComplete) && !empty($migrationResults))) {
            // unregister cron task if it still exists
            $timestamp = wp_next_scheduled( 'empowercast_admin_migration_hook' );
            wp_unschedule_event( $timestamp, 'empowercast_admin_migration_hook' );
        } elseif (!empty($migrationInprogress) && $_GET['page'] != 'empowercast/empowercastadmin_migrate.php') {
            // handles in progress, and update episode directive
            empowercast_admin_migration_notice();
        }
    }
	
	// Handle edit from category page
	if( isset($_POST['from_categories']) )
	{
		wp_redirect('edit-tags.php?taxonomy=category&message=3');
		exit;
	}
	
	add_filter( 'plugin_row_meta', 'empowercast_plugin_row_meta', 10, 2);
	
	// Hnadle player settings
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-player.php');
	empowercast_admin_players_init();

	// Handle notices
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-notifications.php');
}

function delete_post_refresh_player($postId) {
    $post_status = get_post_status($postId);
    $post_meta = get_post_meta($postId);

    if ($post_status == 'publish') {
        foreach ($post_meta as $key => $value) {
            if (preg_match('/enclosure/i', $key) && count($value) > 0) {
                $EnclosureData = explode("\n", $value[0], 4);
                $mediaURL = $EnclosureData[0];
                $GeneralSettingsTemp = empowercast_get_settings('empowercast_general', false);
                if( !empty($mediaURL) && !empty($GeneralSettingsTemp['podops_hosting']) && $GeneralSettingsTemp['podops_hosting'] !== 'false' ) {
                    require_once(EMPOWERCAST_ABSPATH . '/empowercastadmin-auth.class.php');
                    $auth = new EmpowerCastAuth();
                    $json_data = false;
                    $creds = get_option('empowercast_creds');
                    if ($creds) {
                        $post_array = array('media_url' => urlencode($mediaURL));
                        $accessToken = empowercast_getAccessToken();
                        $req_url = sprintf('/2/episode/%s/delete/', $GeneralSettingsTemp['podops_program_keyword']);
                        $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                        $results = $auth->api($accessToken, $req_url, $post_array, false, 60 * 30);
                    }
                }
            }
        }
    }
}

add_action('admin_init', 'empowercast_admin_init');
add_action('wp_trash_post', 'delete_post_refresh_player');

function empowercast_admin_notices()
{
	$errors = get_option('empowercast_errors');
	if( !empty($errors) )
	{
		if( !delete_option('empowercast_errors') ) {
			// If for some reason we cannot delete this record, maybe we can at least update it with a blank value...
			update_option('empowercast_errors', '');
		}
		
		// Clear the SG cachepress plugin:
		if (function_exists('sg_cachepress_purge_cache')) { sg_cachepress_purge_cache(); }
		
		foreach( $errors as $null => $error )
		{
?>
<div class="updated"><p style="line-height: 125%;"><strong><?php echo $error; ?></strong></p></div>
<?php
		}
	}
}

add_action( 'admin_notices', 'empowercast_admin_notices' );

function empowercast_save_settings($SettingsNew=false, $field = 'empowercast_general' )
{
	if(  $field == 'empowercast_taxonomy_podcasting' || $field == 'empowercast_itunes_featured' ) { // No merging settings for these fields...
		update_option($field,  $SettingsNew);
		return;
	}
	// Save general settings
	if( $SettingsNew )
	{
		$Settings = get_option($field);
		if( !is_array($Settings) )
			$Settings = array();
		foreach( $SettingsNew as $key => $value ) {
			$Settings[$key] = $value;
		}
		if( $field == 'empowercast_general' && !isset($Settings['timestamp']) )
			$Settings['timestamp'] = time();
			
		// Special case fields, if they are empty, we can delete them., this will keep the Settings array uncluttered
		if( isset($Settings['feed_links']) && $Settings['feed_links'] == 0 ) // If set to default value, no need to save it in the database
			unset($Settings['feed_links']);
        if( isset($Settings['suppress_unused_item_tags']) && $Settings['suppress_unused_item_tags'] == 0 ) // If set to default value, no need to save it in the database
            unset($Settings['suppress_unused_item_tags']);

		// We can unset settings that are set to their defaults to save database size...
		if( $field == 'empowercast_general' )
		{
		    if( isset($SettingsNew['new_episode_box_flag'])) {
                /* Switch the settings over to the actual field name (to fix FCGI mode problem with older versions of PHP.
                if (isset($SettingsNew['ebititle'])) {
                    if ($SettingsNew['ebititle'] == 'false') {
                        $Settings['new_episode_box_itunes_title'] = 2;
                    } else {
                        $Settings['new_episode_box_itunes_title'] = 1;
                        $SettingsNew['new_episode_box_itunes_title'] = 1;
                    }
                    unset($Settings['ebititle']);
                }

                if (isset($SettingsNew['ebinst'])) {
                    if ($SettingsNew['ebinst'] == 'false') {
                        $Settings['new_episode_box_itunes_nst'] = 2;
                    } else {
                        $Settings['new_episode_box_itunes_nst'] = 1;
                        $SettingsNew['new_episode_box_itunes_nst'] = 1;
                    }
                    unset($Settings['ebinst']);
                }*/

                if (!isset($SettingsNew['new_episode_box_embed']))
                    $Settings['new_episode_box_embed'] = 2;
                if (!isset($SettingsNew['new_embed_replace_player']))
                    $Settings['new_embed_replace_player'] = 2;
                if (!isset($SettingsNew['new_episode_box_no_player']))
                    $Settings['new_episode_box_no_player'] = 2;
                if (!isset($SettingsNew['new_episode_box_no_links']))
                    $Settings['new_episode_box_no_links'] = 2;
                if (!isset($SettingsNew['new_episode_box_no_player_and_links']))
                    $Settings['new_episode_box_no_player_and_links'] = 2;
                if (!isset($SettingsNew['new_episode_box_cover_image']))
                    $Settings['new_episode_box_cover_image'] = 2;
                if (!isset($SettingsNew['new_episode_box_player_size']))
                    $Settings['new_episode_box_player_size'] = 2;
                if (!isset($SettingsNew['new_episode_box_subtitle']))
                    $Settings['new_episode_box_subtitle'] = 2;
                if (!isset($SettingsNew['new_episode_box_summary']))
                    $Settings['new_episode_box_summary'] = 2;
                if (!isset($SettingsNew['new_episode_box_author']))
                    $Settings['new_episode_box_author'] = 2;
                if (!isset($SettingsNew['new_episode_box_explicit']))
                    $Settings['new_episode_box_explicit'] = 2;
                if (!isset($SettingsNew['new_episode_box_pci']))
                    $Settings['new_episode_box_pci'] = 2;
                if (!isset($SettingsNew['new_episode_box_block']))
                    $Settings['new_episode_box_block'] = 2;
                if (!isset($SettingsNew['new_episode_box_itunes_image']))
                    $Settings['new_episode_box_itunes_image'] = 2;
                if (!isset($SettingsNew['new_episode_box_order']))
                    $Settings['new_episode_box_order'] = 2;
                if (!isset($SettingsNew['new_episode_box_itunes_title']))
                    $Settings['new_episode_box_itunes_title'] = 2;
                if (!isset($SettingsNew['new_episode_box_itunes_nst']))
                    $Settings['new_episode_box_itunes_nst'] = 2;
                if (!isset($SettingsNew['new_episode_box_gp_explicit']))
                    $Settings['new_episode_box_gp_explicit'] = 2;
                if (!isset($SettingsNew['new_episode_box_feature_in_itunes']))
                    $Settings['new_episode_box_feature_in_itunes'] = 2;
            } elseif(isset($SettingsNew['pp-gen-settings-tabs'])) {
                if (!isset($SettingsNew['skip_to_episode_settings']) || empty($SettingsNew['skip_to_episode_settings']))
                    unset($Settings['skip_to_episode_settings']);
                if (!isset($SettingsNew['display_player_excerpt']) || empty($SettingsNew['display_player_excerpt']))
                    unset($Settings['display_player_excerpt']);
                if (!isset($SettingsNew['hide_player_more']) || empty($SettingsNew['hide_player_more']))
                    unset($Settings['hide_player_more']);
                if (!isset($SettingsNew['podcast_embed']) || empty($SettingsNew['podcast_embed']))
                    unset($Settings['podcast_embed']);
                if (!isset($SettingsNew['subscribe_links']) || empty($SettingsNew['subscribe_links']))
                    unset($Settings['subscribe_links']);
                if (!isset($SettingsNew['new_window_no_factor']) || empty($SettingsNew['new_window_no_factor']))
                    unset($Settings['new_window_no_factor']);
            } elseif( isset($SettingsNew['empowercast_bplayer_settings'])) {
		        unset($Settings['empowercast_bplayer_settings']);
                if (!isset($SettingsNew['new_episode_box_itunes_image']) || empty($SettingsNew['new_episode_box_itunes_image']))
                    $Settings['new_episode_box_itunes_image'] = 2;
                if (isset($SettingsNew['bp_episode_image']) && empty($SettingsNew['bp_episode_image']))
                    unset($Settings['bp_episode_image']);
            }


			if( isset($Settings['videojs_css_class']) && empty($Settings['videojs_css_class']) )
				unset($Settings['videojs_css_class']);
			if( isset($Settings['cat_casting']) && empty($Settings['cat_casting']) )
                unset($Settings['cat_casting']);
			if( isset($Settings['posttype_podcasting']) && empty($Settings['posttype_podcasting']) )
				unset($Settings['posttype_podcasting']);
			if( isset($Settings['taxonomy_podcasting']) && empty($Settings['taxonomy_podcasting']) )
				unset($Settings['taxonomy_podcasting']);
			if( isset($Settings['playlist_player']) && empty($Settings['playlist_player']) )
				unset($Settings['playlist_player']);	
			if( isset($Settings['seo_feed_title']) && empty($Settings['seo_feed_title']) )
				unset($Settings['seo_feed_title']);
			if( isset($Settings['subscribe_feature_email']) && empty($Settings['subscribe_feature_email']) )
				unset($Settings['subscribe_feature_email']);
			if( isset($Settings['poster_image_video']) && empty($Settings['poster_image_video']) )
				unset($Settings['poster_image_video']);
			if( isset($Settings['poster_image_audio']) && empty($Settings['poster_image_audio']) )
				unset($Settings['poster_image_audio']);
			if( isset($Settings['itunes_image_audio']) && empty($Settings['itunes_image_audio']) )
				unset($Settings['itunes_image_audio']);
            if( isset($Settings['network_mode']) && empty($Settings['network_mode']) )
				unset($Settings['network_mode']);
            if( isset($Settings['use_caps']) && empty($Settings['use_caps']) )
				unset($Settings['use_caps']);
		}
		else // Feed or player settings...
		{
			if( isset($Settings['itunes_block'] ) && $Settings['itunes_block'] == 0 )
				unset($Settings['itunes_block']);
			if( isset($Settings['itunes_complete'] ) && $Settings['itunes_complete'] == 0 )
				unset($Settings['itunes_complete']);
			if( isset($Settings['maximize_feed'] ) && $Settings['maximize_feed'] == 0 )
				unset($Settings['maximize_feed']);
            if( isset($Settings['unlock_podcast'] ) && $Settings['unlock_podcast'] == 0 )
                unset($Settings['unlock_podcast']);
			if( isset($Settings['donate_link'] ) && $Settings['donate_link'] == 0 )
				unset($Settings['donate_link']);
			if( empty($Settings['donate_url']) )
				unset($Settings['donate_url']);
			if( empty($Settings['donate_label']) )
				unset($Settings['donate_label']);
			if( isset($Settings['allow_feed_comments'] ) && $Settings['allow_feed_comments'] == 0 )
				unset($Settings['allow_feed_comments']);	
			if( empty($Settings['episode_itunes_image']) )
				unset($Settings['episode_itunes_image']);
		}

        if(!empty($Settings)){
            if(isset($Settings['player'])){
                if($Settings['player'] == 'podopsmodern'){
                    if(!empty($_POST)){
                        if(isset($_POST['ModernPlayer']['progress']) && isset($_POST['ModernPlayer']['border']) && isset($_POST['mode'])){
                            if($_POST['mode'] == 'Light' || $_POST['mode'] == 'Dark'){
                                if(preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i', $_POST['ModernPlayer']['progress']) && preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i', $_POST['ModernPlayer']['border'])){
                                    $updatedPlayerSettings = array('mode' => $_POST['mode'], 'border' => $_POST['ModernPlayer']['border'], 'progress' => $_POST['ModernPlayer']['progress']);
                                    update_option('empowercast_bplayer',  json_encode($updatedPlayerSettings));
                                }
                            }
                        }
                    }
                }
            }
        }

		update_option($field,  $Settings);
	}
}

function empowercast_get_settings($field, $for_editing=true)
{
	$Settings = get_option($field, array());
	if( $for_editing )
		$Settings = empowercast_htmlspecialchars($Settings);
	return $Settings;
}

function empowercast_htmlspecialchars($data)
{
	if( !$data )
		return $data;
	if( is_array($data) )
	{
		foreach( $data as $key => $value )
		{
			if( $key == 'itunes_summary' )
				continue; // Skip this one as we escape it in the form.
			if( is_array($value) )
				$data[$key] = empowercast_htmlspecialchars($value);
			else
				$data[$key] = htmlspecialchars($value);
		}
		reset($data);
	}
	return $data;
}

function empowercast_stripslashes($data)
{
	if( !$data )
		return $data;
	
	if( !is_array($data) )
		return stripslashes($data);
	
	foreach( $data as $key => $value )
	{
		if( is_array($value) )
			$data[$key] = empowercast_stripslashes($value);
		else
			$data[$key] = stripslashes($value);
	}
	reset($data);
	return $data;
}

function empowercast_admin_get_post_types($capability_type = 'post')
{
	if( !function_exists('get_post_types') || !function_exists('get_post_type_object') )
		return array($capability_type);
		
	$return = array();
	$post_types = get_post_types();
	foreach( $post_types as $index => $post_type )
	{
		if( $post_type == 'redirect_rule' || $post_type == 'attachment' || $post_type == 'nav_menu_item' || $post_type == 'revision' || $post_type == 'action' )
			continue;
		if( $capability_type !== false )
		{
			$object = get_post_type_object($post_type);
			if( $object && $object->capability_type == $capability_type )
				$return[] = $post_type;
		}
		else
		{
			$return[] = $post_type;
		}
	}
	return $return;
}

/* Rebuild empowercast_posttype_podcasting field*/
function empowercast_rebuild_posttype_podcasting()
{
	// Loop through all the posttype podcasting settings, save them into a field
	// array( feed-slugs => array('posttype1'=>'post type 1 title', 'posttype2'=>post type 2 title', ...) );
	$post_types = get_post_types();
	$FeedSlugPostTypeArray = array();
	foreach( $post_types as $index => $post_type )
	{
		$PostTypeSettingsArray = get_option('empowercast_posttype_'. $post_type, array() );
		if( empty($PostTypeSettingsArray ))
			continue;
		
		foreach( $PostTypeSettingsArray as $feed_slug => $PostTypeSettings )
		{
			$FeedSlugPostTypeArray[ $feed_slug ][ $post_type ] = ( empty($PostTypeSettings['title'])? $feed_slug : $PostTypeSettings['title'] );
		}
	}
	update_option('empowercast_posttype-podcasting', $FeedSlugPostTypeArray);
}

function empowercast_admin_menu()
{
	$Empowercast = get_option('empowercast_general', array());
	
	if( defined('PODCAST_VERSION') || isset($GLOBALS['podcasting_player_id']) || isset($GLOBALS['podcast_channel_active']) || defined('PODCASTING_VERSION') )
	{
		// CRAP
	}
	else if( empty($Empowercast['use_caps']) || current_user_can('edit_podcast') )
	{ // Otherwise we're using a version of wordpress that is not supported.
		
		require_once( EMPOWERCAST_ABSPATH .'/views/episode-box.php');
		$FeedSlugPostTypesArray = array();
		if( !empty($Empowercast['posttype_podcasting']) )
		{
			$FeedSlugPostTypesArray = get_option('empowercast_posttype-podcasting');
				if( empty($FeedSlugPostTypesArray) )
					$FeedSlugPostTypesArray = array();
		}
		
		if( !defined('EMPOWERCAST_POST_TYPES') )
		{
			$post_types = array('post'); // Only apply to default posts if post type podcasting is disabled
			if( empty($Empowercast['posttype_podcasting']) )
				$post_types = empowercast_admin_get_post_types('post'); // Get pages by capability type
		}
		else
		{
			$post_type_string = str_replace(' ', '',EMPOWERCAST_POST_TYPES); // Get all the spaces out
			$post_types = explode(',', $post_type_string);
		}
		
		if( !empty($Empowercast['posttype_podcasting']) )
		{
			add_meta_box('empowercast-podcast', __('Podcast Episode (default)', 'empowercast'), 'empowercast_meta_box', 'post', 'normal'); // Default podcast box for post type 'post'
			
			$FeedSlugPostTypesArray = get_option('empowercast_posttype-podcasting');
			if( empty($FeedSlugPostTypesArray) )
				$FeedSlugPostTypesArray = array();

			foreach( $FeedSlugPostTypesArray as $feed_slug => $FeedSlugPostTypes )
			{
				foreach( $FeedSlugPostTypes as $post_type => $type_title )
				{
					if ( $feed_slug != 'podcast' || $post_type != 'post' ) // No the default podcast feed
					{
						$feed_title = $type_title;
						if( empty($feed_title) )
							$feed_title = $feed_slug;
							//echo (" $feed_slug ");
						add_meta_box('empowercast-'.$feed_slug,  __('Podcast Episode', 'empowercast') .': '.$feed_title, 'empowercast_meta_box', $post_type, 'normal');
					}
				}
			}
		}
		
		if( isset($Empowercast['custom_feeds']) )
		{
			$FeedDefaultPodcast = get_option('empowercast_feed_podcast', array());
			
			foreach( $post_types as $null => $post_type )
			{
				// Make sure this post type can edit the default podcast channel...
				if( !empty($FeedDefaultPodcast['custom_post_type']) && $FeedDefaultPodcast['custom_post_type'] != $post_type )
					continue;
					
				if( empty($FeedSlugPostTypesArray[ 'podcast' ][ $post_type ]) )
					add_meta_box('empowercast-podcast', __('Podcast Episode (default)', 'empowercast'), 'empowercast_meta_box', $post_type, 'normal');
			}
			
			foreach( $Empowercast['custom_feeds'] as $feed_slug => $feed_title )
			{
				if( $feed_slug == 'podcast' )
					continue;
				
				$FeedCustom = get_option('empowercast_feed_'.$feed_slug, array());
				$feed_slug = esc_attr($feed_slug);
				
						
				reset($post_types);
				foreach( $post_types as $null => $post_type )
				{
					// Make sure this post type can edit the default podcast channel...
					if( !empty($FeedCustom['custom_post_type']) && $FeedCustom['custom_post_type'] != $post_type )
						continue;
					
					if( empty($FeedSlugPostTypesArray[ $feed_slug ][ $post_type ]) )
						add_meta_box('empowercast-'.$feed_slug, __('Podcast Episode for Custom Channel', 'empowercast') .': '. esc_attr($feed_title), 'empowercast_meta_box', $post_type, 'normal');
				}
			}
			reset($Empowercast['custom_feeds']);
		}
		else // This handles all podcast post types and default  'post'. if post type podcasting enabled. 
		{
			reset($post_types);
			foreach( $post_types as $null => $post_type )
			{
				if( empty($FeedSlugPostTypesArray[ 'podcast' ][ $post_type ]) )
					add_meta_box('empowercast-podcast', __('Podcast Episode', 'empowercast'), 'empowercast_meta_box', $post_type, 'normal');
			}
		}
		
		// For custom compatibility type set:
		if( isset($Empowercast['custom_feeds']) && defined('EMPOWERCAST_CUSTOM_CAPABILITY_TYPE') )
		{
			$post_types = empowercast_admin_get_post_types( EMPOWERCAST_CUSTOM_CAPABILITY_TYPE );
			if( !empty($post_types) )
			{
				foreach( $Empowercast['custom_feeds'] as $feed_slug => $feed_title )
				{
					if( $feed_slug == 'podcast' )
						continue;
					
					$FeedCustom = get_option('empowercast_feed_'.$feed_slug, array());
							
					reset($post_types);
					foreach( $post_types as $null => $post_type )
					{
						if( !empty($FeedCustom['custom_post_type']) && $FeedCustom['custom_post_type'] != $post_type )
							continue;
						
						if( empty($FeedSlugPostTypesArray[ $feed_slug ][ $post_type ]) )
							add_meta_box('empowercast-'.$feed_slug, __('Podcast Episode for Custom Channel', 'empowercast') .': '.$feed_title, 'empowercast_meta_box', $post_type, 'normal');
					}
				}
				reset($Empowercast['custom_feeds']);
			}
		}
	}
	
	if( current_user_can(EMPOWERCAST_CAPABILITY_MANAGE_OPTIONS) ) {
        $Empowercast = empowercast_default_settings($Empowercast, 'basic');

        if (isset($_GET['page']) && strstr($_GET['page'], 'empowercast') !== false && isset($_POST['General'])) {
            $ToBeSaved = $_POST['General'];

            if (isset($ToBeSaved['channels']))
                $Empowercast['channels'] = $ToBeSaved['channels'];
            if (isset($ToBeSaved['cat_casting']))
                $Empowercast['cat_casting'] = $ToBeSaved['cat_casting'];
            if (isset($ToBeSaved['taxonomy_podcasting']))
                $Empowercast['taxonomy_podcasting'] = $ToBeSaved['taxonomy_podcasting'];
            if (isset($ToBeSaved['posttype_podcasting']))
                $Empowercast['posttype_podcasting'] = $ToBeSaved['posttype_podcasting'];
            if (isset($ToBeSaved['podpress_stats']))
                $Empowercast['podpress_stats'] = $ToBeSaved['podpress_stats'];
            if (isset($ToBeSaved['podops_hosting']))
                $Empowercast['podops_hosting'] = $ToBeSaved['podops_hosting'];
        }
        $Settings = get_option('empowercast_general', array());
        if (!empty($_GET['skip_onboarding'])) {
            $Settings['pp_onboarding_incomplete'] = 0;
            empowercast_save_settings($Settings);
        }
        if ((isset($Settings['pp_onboarding_incomplete']) && $Settings['pp_onboarding_incomplete'] == 1) && (isset($Settings['timestamp']) && $Settings['timestamp'] > 1576972800)) {
            $parent_slug = 'empowercastadmin_onboarding.php';
            $parent_slug = apply_filters('empowercast_submenu_parent_slug', $parent_slug);
            add_menu_page(__('EmpowerCast', 'empowercast'), __('EmpowerCast', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercastadmin_onboarding.php', 'empowercast_admin_page_onboarding', empowercast_get_root_url() . 'images/EmpowerCast_white.png');
            add_submenu_page($parent_slug, __('Get Started', 'empowercast'), __('Get Started', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercastadmin_onboarding.php', 'empowercast_admin_page_onboarding');
            add_submenu_page($parent_slug, __('EmpowerCast Settings', 'empowercast'), __('Settings', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercastadmin_basic', 'empowercast_admin_page_basic');
        } else {
            $parent_slug = 'empowercastadmin_basic';
            $parent_slug = apply_filters('empowercast_submenu_parent_slug', $parent_slug);
            add_menu_page(__('EmpowerCast', 'empowercast'), __('EmpowerCast', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercastadmin_basic', 'empowercast_admin_page_basic', empowercast_get_root_url() . 'images/EmpowerCast_white.png');
            add_submenu_page($parent_slug, __('EmpowerCast Settings', 'empowercast'), __('Settings', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercastadmin_basic', 'empowercast_admin_page_basic');
            add_submenu_page($parent_slug, __('Get Started', 'empowercast'), __('Get Started', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercastadmin_onboarding.php', 'empowercast_admin_page_onboarding');

        }
        add_options_page(__('EmpowerCast', 'empowercast'), __('EmpowerCast', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercastadmin_basic', 'empowercast_admin_page_basic');

        add_submenu_page($parent_slug, __('Live Item Tag.', 'empowercast'), __('Live Item', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_live_item.php', 'empowercast_admin_page_live_item');

        add_submenu_page($parent_slug, __('Import podcast feed from SoundCloud, LibSyn, PodBean or other podcast service.', 'empowercast'), __('Import Podcast', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_import_feed.php', 'empowercast_admin_page_import_feed');
        add_submenu_page($parent_slug, __('Migrate media files to Podops Podcast Media Hosting with only a few clicks.', 'empowercast'), __('Migrate Media', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_migrate.php', 'empowercast_admin_page_migrate');

        add_submenu_page($parent_slug, __('EmpowerCast Audio Player Options', 'empowercast'), __('Audio Player', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_player.php', 'empowercast_admin_page_players');
        add_submenu_page($parent_slug, __('EmpowerCast Video Player Options', 'empowercast'), __('Video Player', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_videoplayer.php', 'empowercast_admin_page_videoplayers');
        if (!empty($Empowercast['channels']))
            add_submenu_page($parent_slug, __('EmpowerCast Custom Podcast Channels', 'empowercast'), __('Podcast Channels', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_customfeeds.php', 'empowercast_admin_page_customfeeds');
        if (!empty($Empowercast['cat_casting']))
            add_submenu_page($parent_slug, __('EmpowerCast Category Podcasting', 'empowercast'), __('Category Podcasting', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_categoryfeeds.php', 'empowercast_admin_page_categoryfeeds');
        if (defined('EMPOWERCAST_TAXONOMY_PODCASTING') || !empty($Empowercast['taxonomy_podcasting']))
            add_submenu_page($parent_slug, __('EmpowerCast Taxonomy Podcasting', 'empowercast'), __('Taxonomy Podcasting', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_taxonomyfeeds.php', 'empowercast_admin_page_taxonomyfeeds');
        if (defined('EMPOWERCAST_POSTTYPE_PODCASTING') || !empty($Empowercast['posttype_podcasting']))
            add_submenu_page($parent_slug, __('EmpowerCast Post Type Podcasting', 'empowercast'), __('Post Type Podcasting', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_posttypefeeds.php', 'empowercast_admin_page_posttypefeeds');
        if (!empty($Empowercast['podpress_stats']))
            add_submenu_page($parent_slug, __('PodPress Stats', 'empowercast'), __('PodPress Stats', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_podpress-stats.php', 'empowercast_admin_page_podpress_stats');
        if (!empty($Empowercast['empowercast_network'])) {
            add_submenu_page( $parent_slug,  __('EmpowerCast Network', 'empowercast'), __('EmpowerCast Network', 'empowercast'), 'manage_options', 'network-plugin', 'network_plugin' );
        }
        //if( !empty($Empowercast['podops_hosting']) &&  $Empowercast['podops_hosting'] !== 'false' )

        add_submenu_page($parent_slug, __('EmpowerCast Tools', 'empowercast'), __('Tools', 'empowercast'), EMPOWERCAST_CAPABILITY_EDIT_PAGES, 'empowercast/empowercastadmin_tools.php', 'empowercast_admin_page_tools');
    }
}


add_action('admin_menu', 'empowercast_admin_menu');


function network_plugin() {
    if (isset($GLOBALS['ppn_object'])) {
        $GLOBALS['ppn_object']->display_plugin();
    }
}

// Save episode information
function empowercast_edit_post($post_ID, $post)
{
	if ( !current_user_can('edit_post', $post_ID) )
		return $post_ID;
		
	$GeneralSettings = get_option('empowercast_general', array());

	if( isset($GeneralSettings['auto_enclose']) && $GeneralSettings['auto_enclose'] )
	{
		empowercast_do_enclose($post->post_content, $post_ID, ($GeneralSettings['auto_enclose']==2) );
	}

	// If the field limit is exceeded, WordPress won't send an error so we need to, as this prevents publishing
	if( count($_POST, COUNT_RECURSIVE) > (ini_get('max_input_vars') -100 ) ) {
        // we want to display the warning message
        $error = "EmpowerCast Warning: you may be exceeding your fields limit, a server setting that limits how many fields your pages can contain. Your current limit is ";
        $error .= ini_get('max_input_vars') . " <a href='https://podopshost.com/support/empowercast-documentation/warning-messages-explained/'>Learn more</a>";
        empowercast_add_error($error);
    }

    $Episodes = ( isset($_POST['Empowercast'])? $_POST['Empowercast'] : false);
	if( $Episodes )
	{
	    $success_array = array();
		foreach( $Episodes as $feed_slug => $Empowercast )
		{
		    $error = '';
			$field = 'enclosure';
            if (!preg_match('/^[a-z0-9]+(?:(?:-|_)+[a-z0-9]+)*$/', $feed_slug)) {
                empowercast_add_error('Invalid feed slug ' . htmlspecialchars($feed_slug));
                continue;
            }
			if( $feed_slug != 'podcast' )
				$field = '_'.$feed_slug.':enclosure';
			
			if( !empty($Empowercast['remove_podcast']) )
			{
				delete_post_meta( $post_ID, $field);
				
				if( $feed_slug == 'podcast' ) // Clean up the old data
					delete_post_meta( $post_ID, 'itunes:duration');
			}
			else if( !empty($Empowercast['change_podcast']) || !empty($Empowercast['new_podcast']) )
			{
				// No URL specified, then it's not really a podcast--display a warning
                // check some basic episode data and save if it's there
				if( empty($Empowercast['url']) && ( !empty($Empowercast['itunes_image']) || !empty($Empowercast['episode_title']) || !empty($Empowercast['feed_title']) || !empty($Empowercast['summary']) || !empty($Empowercast['subtitle'])) ) {
				    $Empowercast['url'] = '';
				    $error = __('WARNING: This post contains podcast data but no podcast sound file. ', 'empowercast');
				    if ($feed_slug != 'podcast') {
				        $error .= __('Episode published in feed ', 'empowercast') . $feed_slug . ' ';
                    } else {
				        $error .= __('This episode ', 'empowercast');
                    }
				    $error .= __('will not be included in any podcast feed.', 'empowercast');
				    empowercast_add_error($error);

                } else if( empty($Empowercast['url']) && empty($Empowercast['itunes_image']) && empty($Empowercast['episode_title']) && empty($Empowercast['feed_title']) && empty($Empowercast['summary']) && empty($Empowercast['subtitle']) ) {
				    continue;
                }

                    // Initialize the important variables:
                $MediaURL = $Empowercast['url'];
				if( !empty($GeneralSettings['default_url']) && strpos($MediaURL, 'http://') !== 0 && strpos($MediaURL, 'https://') !== 0 && empty($Empowercast['hosting']) ) // If the url entered does not start with a http:// or https://
				{
					if( !empty($MediaURL) )
						$MediaURL = rtrim($GeneralSettings['default_url'], '/') .'/'. ltrim($MediaURL, '/');
				}


				
				$FileSize = '';
				$ContentType = '';
				$Duration = false;
				if( $Empowercast['set_duration'] == 0 )
					$Duration = ''; // allow the duration to be detected

				// Get the content type based on the file extension, first we have to remove query string if it exists
				$UrlParts = parse_url($Empowercast['url']);
				if( $UrlParts['path'] )
				{
					// using functions that already exist in WordPress when possible:
					$ContentType = empowercast_get_contenttype($UrlParts['path']);
				}

				if( !$ContentType && !empty($Empowercast['url']) )
				{
					$error = __('Error', 'empowercast') ." [{$Empowercast['url']}]: " .__('Unable to determine content type of media (e.g. audio/mpeg). Verify file extension is correct and try again.', 'empowercast');
					empowercast_add_error($error);
				}

				//Set the duration specified by the user
				if( $Empowercast['set_duration'] == 1 ) // specify duration
				{
					$Duration = sprintf('%02d:%02d:%02d', $Empowercast['duration_hh'], $Empowercast['duration_mm'], $Empowercast['duration_ss'] );
				}
				
				//Set the file size specified by the user
				if( $Empowercast['set_size'] == 1 ) // specify file size
				{
					$FileSize = $Empowercast['size'];
				}

				if( $Empowercast['set_size'] == 0 || $Empowercast['set_duration'] == 0 )
				{
					if( !empty($Empowercast['hosting']) && !empty($Empowercast['url']) )
					{
						if( $Empowercast['set_size'] == 0 || $Empowercast['set_duration'] == 0 )
						{
							$MediaInfo = empowercast_get_media_info($Empowercast['url'], $Empowercast['program_keyword']);
							if( !isset($MediaInfo['error']) )
							{
								if( $Empowercast['set_size'] == 0 )
									$FileSize = $MediaInfo['length'];
								if( $Empowercast['set_duration'] == 0 && !empty($MediaInfo['duration']) )
									$Duration = empowercast_readable_duration($MediaInfo['duration'], true);
							}
							else
							{
							    // Even when this error is thrown, we still detect filesize and duration fine as far as i can tell
                                // Leaving this in for a few updates in case it causes some unforeseen issues
                                // Will likely want to delete this block for good August 2023
								//$error = __('Error', 'empowercast') ." ({$Empowercast['url']}): {$MediaInfo['error']}";
								//empowercast_add_error($error);
							}
						}
					}
					else
					{
                        if (!empty($Empowercast['url']) ) {
                            $media_hostname = $UrlParts['host'];
                            if (in_array($media_hostname, array('0.0.0.0', '127.0.0.1', 'localhost', '[::]', '0x7f000001/', '0xc0a80014/')) || filter_var($media_hostname, FILTER_VALIDATE_IP) || !preg_match('/^[a-zA-Z.\-\d]+$/i', $media_hostname)) {
                                // they have already seen the invalid url message on verify--no media check!
                            } else {
                                if (empty($Empowercast['set_duration']))
                                    $MediaInfo = empowercast_get_media_info_local($MediaURL, $ContentType, 0, '');
                                else
                                    $MediaInfo = empowercast_get_media_info_local($MediaURL, $ContentType, 0, $Duration);

                                if (isset($MediaInfo['error'])) {
                                    $error = __('Error', 'empowercast') . " (<a href=\"$MediaURL\" target=\"_blank\">{$MediaURL}</a>): {$MediaInfo['error']}";
                                    empowercast_add_error($error);
                                } else if (empty($MediaInfo['length'])) {
                                    $error = __('Error', 'empowercast') . " (<a href=\"$MediaURL\" target=\"_blank\">{$MediaURL}</a>): " . __('Unable to obtain size of media.', 'empowercast');
                                    empowercast_add_error($error);
                                } else {
                                    // Detect the duration
                                    if (empty($Empowercast['set_duration']) && !empty($MediaInfo['duration']))
                                        $Duration = empowercast_readable_duration($MediaInfo['duration'], true); // Fix so it looks better when viewed for editing

                                    // Detect the file size
                                    if (empty($Empowercast['set_size']) && $MediaInfo['length'] > 0)
                                        $FileSize = $MediaInfo['length'];
                                }
                            }
                        }
					}
				}

				// If we made if this far, we have the content type and file size...
                if (!empty($MediaURL)) {
                    $EnclosureData = $MediaURL . "\n" . $FileSize . "\n" . $ContentType;
                } else {
                    $EnclosureData = "no\n0\n0";
                }

                if (!empty($Empowercast['alternate_enclosure'])) {
                    $AltEnclosureData = array();
                    foreach ($Empowercast['alternate_enclosure'] as $alt_idx => $alt_enclosure_data) {
                        // Initialize the important variables:
                        $MediaURL = $alt_enclosure_data['url'];
                        if( !empty($GeneralSettings['default_url']) && strpos($MediaURL, 'http://') !== 0 && strpos($MediaURL, 'https://') !== 0 && empty($Empowercast['hosting']) ) // If the url entered does not start with a http:// or https://
                        {
                            if( !empty($MediaURL) )
                                $MediaURL = rtrim($GeneralSettings['default_url'], '/') .'/'. ltrim($MediaURL, '/');
                        }

                        $FileSize = '';
                        $ContentType = '';
                        $Duration = false;
                        if( $Empowercast['set_duration'] == 0 )
                            $Duration = ''; // allow the duration to be detected

                        // Get the content type based on the file extension, first we have to remove query string if it exists
                        $ContentType = false;
                        $UrlParts = parse_url($alt_enclosure_data['url']);
                        if( $UrlParts['path'] )
                        {
                            // using functions that already exist in WordPress when possible:
                            $ContentType = empowercast_get_contenttype($UrlParts['path']);
                        }

                        //Set the file size specified by the user
                        $FileSize = false;
                        if( !empty($alt_enclosure_data['size']) ) // specify file size
                        {
                            $FileSize = $alt_enclosure_data['size'];
                        } else if( !empty($alt_enclosure_data['hosting']) && !empty($alt_enclosure_data['url']) ) {
                            $MediaInfo = empowercast_get_media_info($alt_enclosure_data['url'], $Empowercast['program_keyword']);
                            if( !isset($MediaInfo['error']) )
                            {
                                $FileSize = $MediaInfo['length'];
                            }
                        }

                        $youtube_regexp = "/^https?:\/\/(?:www\.)?(?:youtube.com|youtu.be)\/(?:watch\?(?=.*v=([\w\-]+))(?:\S+)?|([\w\-]+))$/i";

                        if (preg_match($youtube_regexp, $MediaURL))
                            $ContentType = 'video/youtube';

                        if (!empty($MediaURL)) {
                            $AltEnclosureData[] = array('url' => $MediaURL, 'size' => $FileSize, 'type' => $ContentType, 'hosting' => $alt_enclosure_data['hosting']);
                        }
                    }
                }


                $ToSerialize = array();
                if (!empty($AltEnclosureData)) {
                    $ToSerialize['alternate_enclosure'] = $AltEnclosureData;
                }
                $valueError = false;
                $valueErrorMsg = "";
                $lightning = [];
                $splits = [];
                $pubKeys = [];
                $customKeys = [];
                $customValues = [];
                $count = 1;
                $morePeople = true;
                $validPeople  = 0;
                while ($morePeople) {
                    if (isset($Empowercast["ep-person-$count-pubkey"]))
                        $pubKey = $Empowercast["ep-person-$count-pubkey"];
                    else
                        $pubKey = false;

                    if ($pubKey === false) {
                        $morePeople = false;
                    } else {
                        if ($pubKey != "" || $Empowercast["ep-person-$count-split"] != "") {
                            $pubKeys[] = $pubKey;
                            $splits[] = $Empowercast["ep-person-$count-split"];
                            $lightning[] = $Empowercast["ep-person-$count-lightning"];
                            $customKeys[] = $Empowercast["ep-person-$count-customkey"];
                            $customValues[] = $Empowercast["ep-person-$count-customvalue"];
                            $validPeople += 1;
                        }
                    }
                    $count += 1;
                }

                foreach ($pubKeys as $pubKey) {
                    if ($pubKey == "") {
                        $valueError = true;
                        $valueErrorMsg .= " PubKey is required for all recipients.";
                    }
                }

                $sum = count($splits) > 0 ? 0 : 100;

                if (count($pubKeys) == 1)
                    $splits[0] = 100;

                foreach ($splits as $split) {
                    $sum += $split;
                }

                if (!$valueError) {
                    $ToSerialize['value_pubkey'] = $pubKeys;
                }

                $ToSerialize['value_lightning'] = $lightning;
                $ToSerialize['value_custom_key'] = $customKeys;
                $ToSerialize['value_custom_value'] = $customValues;
                $ToSerialize['value_split'] = $splits;

                if ($sum != 100 && $validPeople > 0) {
                    $valueError = true;
                    $valueErrorMsg .= " Split values should add up to 100.";
                }

                if ($valueError) {
                    empowercast_add_error(__($valueErrorMsg, 'empowercast'));
                }

				if( !empty($Empowercast['hosting']) )
					$ToSerialize['hosting'] = 1;

                $personNames = [];
                $personRoles = [];
                $personURLs = [];
                $linkURLs = [];
                $count = 1;
                while (true) {
                    if (!isset($Empowercast["role-$count-name"]))
                        break;

                    $name = $Empowercast["role-$count-name"];

                    if ($name != "") {
                        $personNames[] = $name;
                        $personRoles[] = $Empowercast["role-$count-role"];
                        $personURLs[] = $Empowercast["role-$count-personurl"];
                        $linkURLs[] = $Empowercast["role-$count-linkurl"];
                    }
                    $count += 1;
                }

                if (count($personNames) > 0) {
                    $ToSerialize['person_names'] = $personNames;
                    $ToSerialize['person_roles'] = $personRoles;
                    $ToSerialize['person_urls'] = $personURLs;
                    $ToSerialize['link_urls'] = $linkURLs;
                } else {
                    $ToSerialize['person_names'] = [''];
                    $ToSerialize['person_roles'] = [''];
                    $ToSerialize['person_urls'] = [''];
                    $ToSerialize['link_urls'] = [''];
                }

                $soundbiteStarts = [];
                $soundbiteDurations = [];
                $soundbiteTitles = [];
                $count = 1;
                while (true) {
                    if (!isset($Empowercast["soundbite-$count-start"]))
                        break;

                    $start = $Empowercast["soundbite-$count-start"];

                    if ($start != "" && $Empowercast["soundbite-$count-duration"] != "" && $Empowercast["soundbite-$count-duration"] != "00:00:00") {
                        $startParsed = explode(':', $start);
                        $startSecs = intval($startParsed[0]) * 60 * 60 + intval($startParsed[1]) * 60 + intval($startParsed[2]);

                        $durParsed = explode(':', $Empowercast["soundbite-$count-duration"]);
                        $durSecs = intval($durParsed[0]) * 60 * 60 + intval($durParsed[1]) * 60 + intval($durParsed[2]);

                        $soundbiteStarts[] = $startSecs;
                        $soundbiteDurations[] = $durSecs;
                        $soundbiteTitles[] = $Empowercast["soundbite-$count-title"];
                    }
                    $count += 1;
                }

                if (count($soundbiteStarts) > 0) {
                    $ToSerialize['soundbite_starts'] = $soundbiteStarts;
                    $ToSerialize['soundbite_durations'] = $soundbiteDurations;
                    $ToSerialize['soundbite_titles'] = $soundbiteTitles;
                } else {
                    $ToSerialize['soundbite_starts'] = [''];
                    $ToSerialize['soundbite_durations'] = [''];
                    $ToSerialize['soundbite_titles'] = [''];
                }

                if (!empty($Empowercast['location'])) {
                    $ToSerialize['location'] = $Empowercast['location'];

                    if (!empty($Empowercast['pci_geo']))
                        $ToSerialize['pci_geo'] = $Empowercast['pci_geo'];

                    if (!empty($Empowercast['pci_osm']))
                        $ToSerialize['pci_osm'] = $Empowercast['pci_osm'];
                }

                if (isset($Empowercast['vts'])) {
                    $existingVtsInfo = get_option('vts_'.$feed_slug.'_'.$post_ID, array());
                    $newVtsInfo = [];

                    foreach ($Empowercast['vts'] as $vts_id => $vts_info) {
                        $startParsed = explode(':', $vts_info['start_time']);
                        $startSecs = intval($startParsed[0]) * 60 * 60 + intval($startParsed[1]) * 60 + intval($startParsed[2]);

                        $durationParsed = explode(':', $vts_info['duration']);
                        $durationSeconds = intval($durationParsed[0]) * 60 * 60 + intval($durationParsed[1]) * 60 + intval($durationParsed[2]);

                        $newVtsInfo[$vts_id] = $existingVtsInfo[$vts_id];
                        $newVtsInfo[$vts_id]['start_time'] = $startSecs;
                        $newVtsInfo[$vts_id]['duration'] = $durationSeconds;
                        $newVtsInfo[$vts_id]['vts_id'] = $vts_id;

                        if ($newVtsInfo[$vts_id]['recipient'] == 1) {
                            $valueRecipients = $newVtsInfo[$vts_id]['value_recipients'];
                            $splitSum = 0;

                            foreach ($valueRecipients as $valueRecipient) {
                                $splitSum += intval($valueRecipient['split']);
                            }

                            $newValueRecipients = [];

                            foreach ($valueRecipients as $valueRecipient) {
                                $newSplit = $valueRecipient['split'] / $splitSum * 100;
                                $newValueRecipient = $valueRecipient;
                                $newValueRecipient['split'] = $newSplit;
                                $newValueRecipients[] = $newValueRecipient;
                            }
                        }
                    }

                    foreach ($newVtsInfo as $vts_key => $vts_info) {
                        if (!isset($vts_info['recipient']))
                            unset($newVtsInfo[$vts_key]);

                        if (!isset($vts_info['start_time']))
                            unset($newVtsInfo[$vts_key]);

                        if (!isset($vts_info['duration']))
                            unset($newVtsInfo[$vts_key]);
                    }

                    update_option('vts_'.$feed_slug.'_'.$post_ID, $newVtsInfo);

                    usort($newVtsInfo, function($a, $b) {
                        return $a['start_time'] < $b['start_time'] ? -1 : 1;
                    });

                    $vtsOrder = array();
                    foreach ($newVtsInfo as $vts_key => $vts_info) {
                        $vtsOrder[] = $vts_info['vts_id'];
                    }

                    $ToSerialize['vts_order'] = $vtsOrder;
                }

                if (!empty($Empowercast['social_interact_uri'])) {
                    $ToSerialize['social_interact_uri'] = $Empowercast['social_interact_uri'];
                    $ToSerialize['social_interact_protocol'] = $Empowercast['social_interact_protocol'];

                    if (!empty($Empowercast['social_interact_accountid']))
                        $ToSerialize['social_interact_accountid'] = $Empowercast['social_interact_accountid'];
                }

                if (!empty($Empowercast['copyright'])) {
                    $ToSerialize['copyright'] = $Empowercast['copyright'];
                }
					
				// iTunes duration
				if( $Duration && ltrim($Duration, '0:') != '' ) // If all the zeroz and : are trimmed from the front and you're left with an empty value then don't save it.
					$ToSerialize['duration'] = $Duration; // regular expression '/^(\d{1,2}\:)?\d{1,2}\:\d\d$/i' (examples: 1:23, 12:34, 1:23:45, 12:34:56)

                if (isset($Empowercast['set_duration'])) {
                    $ToSerialize['set_duration'] = $Empowercast['set_duration'];
                }
                if (isset($Empowercast['set_size'])) {
                    $ToSerialize['set_size'] = $Empowercast['set_size'];
                }
                // iTunes Subtitle
				if( isset($Empowercast['subtitle']) && trim($Empowercast['subtitle']) != '' ) 
					$ToSerialize['subtitle'] = stripslashes($Empowercast['subtitle']);
				// iTunes Summary
				if( isset($Empowercast['summary']) && trim($Empowercast['summary']) != '' ) 
					$ToSerialize['summary'] = stripslashes($Empowercast['summary']);
				// Google Play Description
				if( isset($Empowercast['gp_desc']) && trim($Empowercast['gp_desc']) != '' ) 
					$ToSerialize['gp_desc'] = stripslashes($Empowercast['gp_desc']);
				// iTunes keywords (Deprecated by Apple)
				if( isset($Empowercast['keywords']) && trim($Empowercast['keywords']) != '' ) 
					$ToSerialize['keywords'] = stripslashes($Empowercast['keywords']);
				// iTunes Author
				if( isset($Empowercast['author']) && trim($Empowercast['author']) != '' ) 
					$ToSerialize['author'] = stripslashes($Empowercast['author']);
				// iTunes Explicit
				if( isset($Empowercast['explicit']) && trim($Empowercast['explicit']) != '' ) 
					$ToSerialize['explicit'] = stripslashes($Empowercast['explicit']);
				// Google Play Explicit
				if( isset($Empowercast['gp_explicit']) && trim($Empowercast['gp_explicit']) == '1' )
					$ToSerialize['gp_explicit'] = stripslashes($Empowercast['gp_explicit']);
				// iTunes CC
				if( isset($Empowercast['cc']) && trim($Empowercast['cc']) != '' ) 
					$ToSerialize['cc'] = stripslashes($Empowercast['cc']);
				// iTunes Episode image
				if( isset($Empowercast['itunes_image']) && trim($Empowercast['itunes_image']) != '' ) 
					$ToSerialize['itunes_image'] = stripslashes($Empowercast['itunes_image']);

				if( isset($Empowercast['episode_title']) && trim($Empowercast['episode_title']) != '' ) 
					$ToSerialize['episode_title'] = stripslashes($Empowercast['episode_title']);
				if( isset($Empowercast['episode_no']) && trim($Empowercast['episode_no']) != '' ) {
				    if (intval($Empowercast['episode_no']) < 0) {
                        $Empowercast['episode_no'] = -1 * intval($Empowercast['episode_no']);
                    }
                    $ToSerialize['episode_no'] = stripslashes(floor($Empowercast['episode_no']));
                }
				if( isset($Empowercast['season']) && trim($Empowercast['season']) != '' ) {
                    if (intval($Empowercast['season']) < 0) {
                        $Empowercast['season'] = -1 * intval($Empowercast['season']);
                    }
                    $ToSerialize['season'] = stripslashes($Empowercast['season']);
                }
				if( isset($Empowercast['episode_type']) && trim($Empowercast['episode_type']) != '' ) 
					$ToSerialize['episode_type'] = stripslashes($Empowercast['episode_type']);
                else
                    $ToSerialize['episode_type'] = 'full';
			
				// order
				if( isset($Empowercast['order']) && trim($Empowercast['order']) != '' ) 
					$ToSerialize['order'] = stripslashes($Empowercast['order']);
				// always
				if( isset($Empowercast['always']) && trim($Empowercast['always']) != '' ) 
					$ToSerialize['always'] = stripslashes($Empowercast['always']);
				// iTunes Block
				if( isset($Empowercast['block']) && $Empowercast['block'] == '1' ) 
					$ToSerialize['block'] = 1;
				// Google Play Block
				if( isset($Empowercast['gp_block']) && $Empowercast['gp_block'] == '1' ) 
					$ToSerialize['gp_block'] = 1;
				// Player Embed
				if( isset($Empowercast['embed']) && trim($Empowercast['embed']) != '' )
					$ToSerialize['embed'] = stripslashes($Empowercast['embed']); // we have to strip slahes if they are present befure we serialize the data
				if( isset($Empowercast['image']) && trim($Empowercast['image']) != '' )
					$ToSerialize['image'] = stripslashes($Empowercast['image']);
				if( isset($Empowercast['no_player']) && $Empowercast['no_player'] )
					$ToSerialize['no_player'] = 1;
				if( isset($Empowercast['no_links']) && $Empowercast['no_links'] )
					$ToSerialize['no_links'] = 1;
				if( isset($Empowercast['ishd']) && $Empowercast['ishd'] )
					$ToSerialize['ishd'] = 1;
                if( isset($Empowercast['program_keyword']) && !empty($Empowercast['program_keyword']) )
                    $ToSerialize['program_keyword'] = $Empowercast['program_keyword'];
				if( isset($Empowercast['width']) && trim($Empowercast['width']) )
					$ToSerialize['width'] =stripslashes( trim($Empowercast['width']));
				if( isset($Empowercast['height']) && trim($Empowercast['height']) )
					$ToSerialize['height'] = stripslashes(trim($Empowercast['height']));
				if( !empty($Empowercast['feed_title']) && trim($Empowercast['feed_title']) )
					$ToSerialize['feed_title'] = stripslashes(trim($Empowercast['feed_title']));
				if( !empty($Empowercast['category']) )
					$ToSerialize['category'] = stripslashes($Empowercast['category']);
                if( isset($Empowercast['transcript']['upload']) && $Empowercast['transcript']['upload'] ) {
                    $ToSerialize['pci_transcript'] = 1;
                    if (isset($Empowercast['pci_transcript_url']) && trim($Empowercast['pci_transcript_url']) != '') {
                        if (strpos($Empowercast['pci_transcript_url'], 'http') !== 0) {
                            empowercast_add_error(__('Transcript Error: Transcript should be a link, starting with http.', 'empowercast'));
                        } else {
                            $ToSerialize['pci_transcript_url'] = stripslashes($Empowercast['pci_transcript_url']);
                        }
                    }
                    if (isset($Empowercast['pci_transcript_language']) && trim($Empowercast['pci_transcript_language']) != '') {
                        $ToSerialize['pci_transcript_language'] = stripslashes($Empowercast['pci_transcript_language']);
                    }
                }
                if( isset($Empowercast['transcript']['generate']) && $Empowercast['transcript']['generate'] ) {
                    $ToSerialize['pci_transcript'] = 1;
                    if (isset($Empowercast['pci_transcript_language']) && trim($Empowercast['pci_transcript_language']) != '') {
                        $ToSerialize['pci_transcript_language'] = stripslashes($Empowercast['pci_transcript_language']);
                    }
                }
                $chapterURL = '';
                if (!empty($Empowercast['chapters']['edit'])) {
                    if (!empty($Empowercast['chapters']['upload'])) {
                        if (isset($Empowercast['pci_chapters_url']) && trim($Empowercast['pci_chapters_url']) != '') {
                            $ToSerialize['pci_chapters'] = 1;
                            $ToSerialize['pci_chapters_url'] = stripslashes($Empowercast['pci_chapters_url']);
                            $chapterURL = $ToSerialize['pci_chapters_url'];
                        }
                    }
                    if (!empty($Empowercast['chapters']['manual'])) {
                        $ToSerialize['pci_chapters'] = 1;
                        $jsonChapters = array(
                            'version' => '1.2.0',
                            'chapters' => []
                        );

                        $uploadPath = wp_upload_dir()['basedir'] . '/' . $feed_slug . '/' . intval($_POST['post_ID']);
                        $uploadURL = wp_upload_dir()['baseurl'] . '/' . $feed_slug . '/' . intval($_POST['post_ID']);
                        if (!is_dir($uploadPath)) {
                            wp_mkdir_p($uploadPath);
                            wp_mkdir_p($uploadPath . "/images");
                        }


                        $ToSerialize['pci_chapters_manual'] = 1;


                        $chapterStarts = $_POST[$feed_slug."-starts"];
                        $chapterTitles = $_POST[$feed_slug."-titles"];
                        $chapterURLs = $_POST[$feed_slug."-urls"];
                        $chapterIms = $_POST[$feed_slug."-images"] ?? [];
                        $existingIms = $_POST[$feed_slug."-existingIms"];
                        $removeIms = $_POST[$feed_slug."-removeExisting"];

                        $numChapters = count($chapterStarts);
                        $tempChapters = array();

                        for ($i = 0; $i < $numChapters; $i++) {
                            if ($chapterStarts[$i] == "" || $chapterTitles[$i] == "") {
                                continue;
                            }

                            $startParsed = explode(':', $chapterStarts[$i]);
                            $title = $chapterTitles[$i];
                            $url = $chapterURLs[$i];
                            $fileName = basename($chapterIms[$i] ?? '');
                            $fileURL = $chapterIms[$i] ?? '';
                            $existingIm = $existingIms[$i];
                            $removeIm = $removeIms[$i];

                            $startSecs = intval($startParsed[0]) * 60 * 60 + intval($startParsed[1]) * 60 + intval($startParsed[2]);
                            $tempChapters["$startSecs"] = array(
                                'title' => $title,
                                'url' => $url,
                                'img' => array(
                                    'existing_file' => $existingIm,
                                    'existing_file_name' => basename($existingIm),
                                    'remove_existing' => $removeIm == '1',
                                    'new_file_name' => $fileName,
                                    'new_file' => $fileURL
                                )
                            );
                        }

                        ksort($tempChapters);

                        $uploadPath = wp_upload_dir()['basedir'] . '/' . $feed_slug . '/' . intval($_POST['post_ID']);
                        $uploadURL = wp_upload_dir()['baseurl'] . '/' . $feed_slug . '/' . intval($_POST['post_ID']);
                        if (!is_dir($uploadPath)) {
                            wp_mkdir_p($uploadPath);
                            wp_mkdir_p($uploadPath . "/images");
                        }

                        foreach ($tempChapters as $startTime => $chapterInfo) {
                            $chapter = array(
                                'startTime' => intval($startTime),
                                'title' => $chapterInfo['title'],
                            );

                            if (!empty($chapterInfo['url'])) {
                                $chapter['url'] = $chapterInfo['url'];
                            }

                            $img = $chapterInfo['img'];

                            if ($img['existing_file'] != '' && $img['new_file_name'] == '') {
                                $chapter['img'] = $img['existing_file'];
                            } elseif ($img['new_file_name'] != '') {
                                // first, check if the image is hosted on this site and if so, access it directly rather than over http
                                if (strpos($img['new_file'], wp_upload_dir()['baseurl']) !== false) {
                                    $image_req_url = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $img['new_file']);
                                } else {
                                    $image_req_url = $img['new_file'];
                                }
                                file_put_contents($uploadPath . "/images/" . $img['new_file_name'], file_get_contents($image_req_url));
                                $chapter['img'] = $uploadURL . "/images/" . $img['new_file_name'];
                            }

                            if ($img['existing_file'] != '' && $img['remove_existing']) {
                                if ($img['new_file_name'] == '') {
                                    unset($chapter['img']);
                                }
                                unlink($uploadPath . "/images/" . $img['existing_file_name']);
                            }

                            $jsonChapters['chapters'][] = $chapter;
                        }

                        $json = json_encode($jsonChapters);

                        $fname = "temp_chapters.json";
                        $tempFile = tempnam(sys_get_temp_dir(), $fname);
                        $file = fopen($tempFile, 'w');
                        fwrite($file, $json);
                        fclose($file);

                        file_put_contents($uploadPath . "/chapters.json", file_get_contents($tempFile));
                        unlink($tempFile);

                        $chapterURL = $uploadURL . "/chapters.json";

                        $ToSerialize['pci_chapters_url'] = $chapterURL;

                    }
                } else if (!empty($Empowercast['pci_chapters_url'])) {
                    // if the transcript has not been changed, carry it through
                    $ToSerialize['pci_chapters'] = 1;
                    $ToSerialize['pci_chapters_manual'] = !empty($Empowercast['chapters']['manual']) ? 1 : 0;
                    $ToSerialize['pci_chapters_url'] = stripslashes($Empowercast['pci_chapters_url']);
                    $chapterURL = $ToSerialize['pci_chapters_url'];
                } else {
                    $chapterURL = '';
                }
                if( isset($Empowercast['pci_soundbites']) && $Empowercast['pci_soundbites'] )
                    $ToSerialize['pci_soundbites'] = 1;
					
				if( isset($Empowercast['no_player_and_links']) && $Empowercast['no_player_and_links'] )
				{
					$ToSerialize['no_player'] = 1;
					$ToSerialize['no_links'] = 1;
				}

				if (isset($Empowercast['podcast_id'])) {
				    $ToSerialize['podcast_id'] = $Empowercast['podcast_id'];
                }
				
				// WebM Support:
				if( !empty($Empowercast['webm_src']) )
				{
					$WebMSrc = $Empowercast['webm_src'];
					if( !empty($GeneralSettings['default_url']) && strpos($WebMSrc, 'http://') !== 0 ) // && $Empowercast['hosting'] != 1 ) // If the url entered does not start with a http://
					{
						$WebMSrc = rtrim($GeneralSettings['default_url'], '/') .'/'. ltrim($WebMSrc, '/');
					}
					$ToSerialize['webm_src'] = $WebMSrc;
					
					$MediaInfo = empowercast_get_media_info_local($WebMSrc, 'video/webm', 0, '');
					if( isset($MediaInfo['error']) )
					{
						$error = __('Error', 'empowercast') ." ({$WebMSrc}): {$MediaInfo['error']}";
						empowercast_add_error($error);
					}
					else if( empty($MediaInfo['length']) )
					{
						$error = __('Error', 'empowercast') ." ({$WebMSrc}): ". __('Unable to obtain size of media.', 'empowercast');
						empowercast_add_error($error);
					}
					else
					{
						$ToSerialize['webm_length'] = $MediaInfo['length'];
					}
				}
				
				if( $Empowercast['set_duration'] == -1 )
					unset($ToSerialize['duration']);
				if( count($ToSerialize) > 0 ) // Lets add the serialized data
					$EnclosureData .= "\n".serialize( $ToSerialize );

				if( !empty($Empowercast['new_podcast']) )
				{
					add_post_meta($post_ID, $field, $EnclosureData, true);
				}
				else
				{
					update_post_meta($post_ID, $field, $EnclosureData);
				}
				
				if( !empty($ToSerialize['category']) )
				{
					$Categories = wp_get_post_categories($post_ID);
					if( !in_array($ToSerialize['category'], $Categories) )
					{
						$AddCategories = array($ToSerialize['category']);
						wp_set_post_categories($post_ID, $AddCategories, true);
					}
				}

                if (!empty($error)) {
                    $success_array[$feed_slug] = $error;
                } else {
                    $success_array[$feed_slug] = true;
                }
            }
		} // Loop through posted episodes...
		
		// Check for EmpowercastFeature for each channel...
		if( isset($_POST['EmpowercastFeature']) )
		{
			$FeatureEpisodes = empowercast_get_settings('empowercast_itunes_featured');
			if( empty($FeatureEpisodes) && !is_array($FeatureEpisodes) )
				$FeatureEpisodes = array();
			
			$EmpowercastFeature = $_POST['EmpowercastFeature'];
			foreach( $EmpowercastFeature as $feed_slug => $set_featured )
			{
				if( !empty($set_featured) )
					$FeatureEpisodes[ $feed_slug ] = $post_ID;
				else
					unset($FeatureEpisodes[ $feed_slug ]);
			}
			
			empowercast_save_settings( $FeatureEpisodes, 'empowercast_itunes_featured');
		}

        require_once(EMPOWERCAST_ABSPATH .'/empowercast-metamarks.php');
        empowercast_metabox_save($post_ID);
	}
    // Anytime the post is marked published, private or scheduled for the future we need to make sure we're making the media available for hosting
    if( $post->post_status == 'publish' || $post->post_status == 'private' || $post->post_status == 'future' )
    {
        if( !empty($GeneralSettings['podops_hosting']) &&  $GeneralSettings['podops_hosting'] !== 'false' )
            empowercast_process_hosting($post_ID, $post->post_title, $chapterURL); // Call anytime blog post is in the published state
    }
    //WebSub and podping implementation
    if($post->post_status == 'publish') {
        $feedUrls = array(); //feed urls that have been updated by this post and that the hub should be notified about.
        $postType = get_post_type($post_ID);

        if (!empty($GeneralSettings['cat_casting'])) {
            foreach (wp_get_post_categories($post_ID) as $id) {
                if (!empty($GeneralSettings['cat_casting_podcast_feeds'])) {
                    array_push($feedUrls, get_category_feed_link($id, 'podcast'));
                } else {
                    array_push($feedUrls, get_category_feed_link($id));
                }
            }
        }
        foreach (get_post_meta($post_ID) as $key => $value) {
            if ($key === 'enclosure') {
                //main feed updated
                array_push($feedUrls, get_feed_link('podcast'));
            }
            else if (!empty($GeneralSettings['channels']) && $postType == 'post' && preg_match("/_(\w{1,}):enclosure/i", $key, $matches) == 1) {
                array_push($feedUrls, get_feed_link($matches[1]));
            }
        }
        if (!empty($GeneralSettings['posttype_podcasting'])) {
            if ($postType != "page" || $postType != "post") {
                $PostTypeSettings = get_option('empowercast_posttype_' . $postType);
                if (!empty($PostTypeSettings)) {
                    foreach ($PostTypeSettings as $feed_slug => $setting) {
                        array_push($feedUrls, get_post_type_archive_feed_link($postType, $feed_slug));
                    }
                }
            }
        }
        if (!(defined('EMPOWERCAST_DISABLE_WEBSUB') && EMPOWERCAST_DISABLE_WEBSUB )) {
            require_once( 'class.empowercastwebsub.php' );
            $Websub = new EmpowerCastWebSub();
            foreach ($feedUrls as $url) {
                try {
                    $Websub->publish($url);
                } catch (Exception $e) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        empowercast_add_error($e->getMessage());
                    }
                }
            }
        }

        // podping uses the Podops API, so an account needs to be linked
        $creds = get_option('empowercast_creds');
        if (!empty($MediaURL) && (!empty($creds) || !empty($GeneralSettings['podops_auth']))) {
            if (!(defined('EMPOWERCAST_DISABLE_PODPING') && EMPOWERCAST_DISABLE_PODPING )) {
                require_once( 'empowercastpodping.class.php' );
                $Podping = new EmpowerCastPodping();
                foreach ($feedUrls as $url) {
                    try {
                        // because auth key is the same across the whole site and each feed url is being passed, it doesn't matter what program keyword we use to call the api
                        $Podping->publish($url, $GeneralSettings['podops_program_keyword']);
                    } catch (Exception $e) {
                        if (defined('WP_DEBUG') && WP_DEBUG) {
                            empowercast_add_error($e->getMessage());
                        }
                    }
                }
            }

        }
    }

    // cookies to either reload the block on success, or display an error on error
    if (!empty($success_array) && (!isset($GeneralSettings['pp_show_block_errors']) || $GeneralSettings['pp_show_block_errors'])) {
        // if successful, success = true. otherwise, success = a string that contains the error
        foreach($success_array as $feed_slug => $success) {
            if ($success === true) {
                setcookie('pp_event_' . $feed_slug . '_' . $post_ID . '_success', "success", 0, '/');
            } else {
                setcookie( 'pp_event_' . $feed_slug . '_' . $post_ID . '_add_notice',  $success, 0, '/' );
            }
        }
    }

    // And we're done!
	return $post_ID;
}

add_action('save_post', 'empowercast_edit_post', 10, 2);

if( defined('EMPOWERCAST_DO_ENCLOSE_FIX') )
{
	function empowercast_insert_post_data($data, $postarr)
	{
		// If we added or modified a podcast episode, then we need to re-add/remove the embedded hidden link...
		if( isset($_POST['Empowercast']['podcast']) && $postarr['post_type'] == 'post' )
		{
			// First, remove the previous comment if one exists in the post body.
			$data['post_content'] = preg_replace('/\<!--.*added by EmpowerCast.*-->/im', '', $data['post_content']);
			
			$Empowercast = $_POST['Empowercast']['podcast'];
			if( !empty($Empowercast['remove_podcast']) )
			{
				// Do nothing
			}
			else if( !empty($Empowercast['change_podcast']) || !empty($Empowercast['new_podcast']) )
			{
				$MediaURL = $Empowercast['url'];
				if( strpos($MediaURL, 'http://') !== 0 && strpos($MediaURL, 'https://') !== 0 && empty($Empowercast['hosting']) ) // If the url entered does not start with a http:// or https://
				{
					// Only glitch here is if the media url had an error, and if that's the case then there are other issues the user needs to worry about.
					$GeneralSettings = get_option('empowercast_general');
					if( $GeneralSettings && isset($GeneralSettings['default_url']) )
						$MediaURL = rtrim($GeneralSettings['default_url'], '/') .'/'. ltrim($MediaURL, '/');
				}
					
				$data['post_content'] .= "<!-- DO NOT DELETE href=\"$MediaURL\" added by EmpowerCast to fix WordPress 2.8+ bug -->";
			}
			else
			{
				$EncloseData = empowercast_get_enclosure_data($postarr['ID']);
				if( $EncloseData && $EncloseData['url'] )
					$data['post_content'] .= "<!-- DO NOT DELETE href=\"{$EncloseData['url']}\" added by EmpowerCast to fix WordPress 2.8+ bug -->";
			}
		}
		
		return $data;
	}
	add_filter('wp_insert_post_data', 'empowercast_insert_post_data',1,2);
}

// Do the iTunes pinging here...
function empowercast_publish_post($post_id)
{
	// Delete scheduled _encloseme requests...
	global $wpdb;
	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_encloseme' ");
	
	$GeneralSettings = get_option('empowercast_general');
	if( isset($GeneralSettings['auto_enclose']) && $GeneralSettings['auto_enclose'] )
	{
		$post = get_post($post_id);
		empowercast_do_enclose($post->post_content, $post_id, ($GeneralSettings['auto_enclose']==2) );
	}
}

add_action('publish_post', 'empowercast_publish_post');

// Admin page, html meta header
function empowercast_admin_head()
{
	global $parent_file, $hook_suffix;
	$page_name = '';
	if ( isset($parent_file) && !empty($parent_file) )
	{
		$page_name = substr($parent_file, 0, -4);
		$page_name = preg_replace('/(\?.*)$/', '', $page_name); // Hack required for WP 3.0
		$page_name = str_replace(array('.php', '-new', '-add'), '', $page_name); // Hack required for WP 3.0
	}
	else
	{
		$page_name = str_replace(array('.php', '-new', '-add'), '', $hook_suffix);
	}
	
	// Empowercast page
	if( isset($_GET['page']) && strstr($_GET['page'], 'empowercast' ) !== false )
	{
		empowercast_wp_print_styles();
		echo "<script type=\"text/javascript\" src=\"". empowercast_get_root_url() ."player.min.js\"></script>\n";
?>
<script type="text/javascript"><!--
function empowercast_show_field(id, show) {
	if( document.getElementById(id).nodeName == "SPAN" )
	 document.getElementById(id).style.display = (show?"inline":"none");
 else
	 document.getElementById(id).style.display = (show?"block":"none");
}
function empowercast_new_feed_url_prompt() {
	var Msg = '<?php echo __('WARNING: Changes made here are permanent. If the New Feed URL entered is incorrect, you will lose subscribers and will no longer be able to update your listing in the iTunes Store.\n\nDO NOT MODIFY THIS SETTING UNLESS YOU ABSOLUTELY KNOW WHAT YOU ARE DOING.\n\nAre you sure you want to continue?', 'empowercast'); ?>';
	if( confirm(Msg) ) {
		empowercast_show_field('new_feed_url_step_1', false);
		empowercast_show_field('new_feed_url_step_2', true);
	}
	return false;
}


function empowercast_create_subscribe_page()
{
	// This function is only called once!
	jQuery.ajax( {
		type: 'POST',
		url: '<?php echo admin_url(); ?>admin-ajax.php', 
		data: { action: 'empowercast_create_subscribe_page', nonce: '<?php echo wp_create_nonce('empowercast-create-subscribe-page'); ?>' },
		timeout: (30 * 1000),
		success: function(response) {
			
			response = response.trim();
			<?php
			if( defined('EMPOWERCAST_AJAX_DEBUG') )
				echo "\t\t\t\talert(response);\n";
			?>
			// This logic will parse beyond warning messages generated by the server that we don't know about
			
			var foundAt = response.indexOf('PAGE-OK');
			if( foundAt > 0 )
			{
				response = response.substring( foundAt );
			}
			
			var Parts = response.split("\n", 5);
			
			if( Parts[0] == 'PAGE-OK' )
			{
				jQuery('#subscribe_page_link_id').append('<option value="' + Parts[1] + '" selected>' + Parts[3] + '</option>');
				jQuery('#subscribe_page_link_or').hide();
				jQuery('#empowercast_create_subscribe_page').hide();
			}
			else if( Parts[0] == 'PAGE-ERROR' )
			{
				alert( Parts[1] );
			}
			else
			{
				alert(  '<?php echo __('Unknown error occurred creating subscribe page.', 'empowercast'); ?>' );
			}
		},
		error: function(objAJAXRequest, strError) {
			
			alert(  '<?php echo __('Unknown ajax error occurred creating subscribe page.', 'empowercast'); ?>' );
			
			var errorMsg = "HTTP " +objAJAXRequest.statusText;
			if ( objAJAXRequest.responseText ) {
				errorMsg += ', '+ objAJAXRequest.responseText.replace( /<.[^<>]*?>/g, '' );
			}
		}
	});
}

/* Save tab position */
jQuery(document).ready(function($) {
	
	if( jQuery("#empowercast_settings_page").length > 0 )
	{
        <?php if (!empty($_POST['tab'])) { ?>
        document.getElementById("<?php echo esc_js($_POST['tab']); ?>").click();
        <?php } elseif (!empty($_GET['tab'])) { ?>
        document.getElementById("<?php echo esc_js($_GET['tab']); ?>").click();
        <?php }
        if (!empty($_POST['sidenav-tab'])) { ?>
        document.getElementById("<?php echo esc_js($_POST['sidenav-tab']); ?>").click();
        <?php }  elseif (!empty($_GET['sidenav-tab'])) { ?>
        document.getElementById("<?php echo esc_js($_GET['sidenav-tab']); ?>").click();
        <?php }?>
        jQuery('form').submit(function() {
            let selectedTemp = jQuery('.tablinks.active:first');
            jQuery('#save_tab_pos').val(selectedTemp.attr('id'));
            let selectedSide = jQuery('.pp-tabcontent.active .pp-sidenav-tablinks.active:first');
            if (selectedSide) {
                jQuery("#save_sidenav_pos").val(selectedSide.attr('id'));
            }
        });
	}
	
	jQuery('#empowercast_create_subscribe_page').click( function(e) {
		e.preventDefault();
		empowercast_create_subscribe_page();
		return false;
	});
	jQuery('#subscribe_page_link_id').change( function(e) {
		if( jQuery('#subscribe_page_link_id').val().length > 0 )
			jQuery('#subscribe_page_link_or').css('display', 'none');
		else
			jQuery('#subscribe_page_link_or').css('display', 'inline-block');
	});
	
	jQuery('.empowercast-parental-rating-tip').click( function(event) {
		event.preventDefault();
		jQuery('.empowercast-parental-rating-tip-p').css('display', 'none');
		jQuery('#'+this.id +'_p').css('display', 'block');
	});
	jQuery('.activate-player').click( function(event) {
		event.preventDefault();
		var PlayerName = this.id.replace(/(activate_)(.*)$/, "$2");
		if( !PlayerName )
			return;

		if(typeof jQuery.prop === 'function') {
            jQuery('.player-type-input').prop('disabled', true);
			jQuery('#player_'+PlayerName).prop('disabled', false);
            jQuery('#player_'+PlayerName).removeAttr('disabled');
		} else {
            jQuery('.player-type-input').attr('disabled', true);
			jQuery('#player_'+PlayerName).removeAttr('disabled');
		}
		jQuery(this).closest("form").submit();
	});
	jQuery('.goto-artwork-tab').click( function(event) {
		event.preventDefault();
		// TODO:
		
	});
});


//-->
</script>
        <?php
        if (defined('WP_DEBUG') && WP_DEBUG) {
            if (WP_DEBUG) {
                wp_register_style('empowercast-admin-style', empowercast_get_root_url() . 'css/admin.css', array(), EMPOWERCAST_VERSION);
                wp_register_style('empowercast-grid', empowercast_get_root_url() . 'css/bootstrap-grid.css', array(), EMPOWERCAST_VERSION);
            } else {
                wp_register_style('empowercast-admin-style', empowercast_get_root_url() . 'css/admin.min.css', array(), EMPOWERCAST_VERSION);
                wp_register_style('empowercast-grid', empowercast_get_root_url() . 'css/bootstrap-grid.min.css', array(), EMPOWERCAST_VERSION);
            }
        } else {
            wp_register_style('empowercast-admin-style', empowercast_get_root_url() . 'css/admin.min.css', array(), EMPOWERCAST_VERSION);
            wp_register_style('empowercast-grid', empowercast_get_root_url() . 'css/bootstrap-grid.min.css', array(), EMPOWERCAST_VERSION);
        }
        wp_enqueue_style( 'empowercast-admin-style' );
        wp_enqueue_style( 'empowercast-grid' );
    }
	else if( $page_name == 'edit' || $page_name == 'edit-pages' ) // || $page_name == '' ) // we don't know the page, we better include our CSS just in case
	{
?>
<style type="text/css">
.empowercast_podcast_box {
	
}
.empowercast_podcast_box label {
	width: 120px;
	font-weight: bold;
	font-size: 110%;
	display: inline;
	position: absolute;
	top: 0;
	left: 0;
}
.empowercast_podcast_box .empowercast_row {
	margin-top: 10px;
	margin-bottom: 10px;
	position: relative;
}
.empowercast_podcast_box .empowercast_row_content {
	margin-left: 120px;
}
.empowercast_podcast_box  .error,
.empowercast_podcast_box  .warning,
.empowercast_podcast_box  .success {
	margin-top: 10px;
	margin-bottom: 10px;
	padding: 5px;
	font-size: 12px;
	border-width: 1px;
	border-style: solid;
	font-weight: bold;
	text-align: center;
	-moz-border-radius: 3px;
	-khtml-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	position: relative;
}
.empowercast_podcast_box  .warning {
	color: #8a6d3b;
	border-color: #faebcc;
	background-color: #fcf8e3;
}
.empowercast_podcast_box  .error {
	color: #a94442;
	border-color: #ebccd1;
	background-color: #f2dede;
}
.empowercast_podcast_box  .success {
	color: #3c763d;
	border-color: #d6e9c6;
	background-color: #dff0d8;
}
.empowercast_podcast_box  .success a.close {
	position: absolute;
	top: 2px;
	right: 2px;
	text-align: right;
	color: #993366;
	text-decoration: none;
}
.empowercast_podcast_box  .updated {
	margin-top: 10px;
	margin-bottom: 10px;
	padding: 5px;
	font-size: 12px;
	border-width: 1px;
	border-style: solid;
	font-weight: bold;
	text-align: center;
}

</style>
<script language="javascript"><!--

g_empowercast_last_selected_channel = '';

function empowercast_check_url(url, DestDiv)
{
	if( empowercast_check_url.arguments.length > 1 )
		DestDiv = empowercast_check_url.arguments[1];

	let Div = jQuery( '#'+DestDiv );
    Div.addClass("pp-error");
    Div.removeClass("updated");
    // allow query strings from trusted hosts
    var validChars = ':0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/-_.:';
    if (url.includes('traffic.libsyn.com') || url.includes('cdn.simplecast.com') || url.includes('buzzsprout.com') || url.includes('audioboom.com')) {
        validChars = ':0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/-_.?=&:';

    }
    // art19 urls have commas and %s
    if (url.includes('art19.com')) {
        validChars = ':0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/-_.?=&,%:';
    }
    // for podops's programmatic ads, we include %s to encode the feed url in the query string
    if (url.includes('mc.podopshost.com')) {
        validChars = ':0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/-_.?=&%;+:';
    }
    let FeedSlug = DestDiv.replace("empowercast_warning_", "");

	for( var x = 0; x < url.length; x++ )
	{
		if( validChars.indexOf( url.charAt(x) ) == -1 )
		{
            Div.html('<?php echo esc_js( __('Media URL contains characters that may cause problems for some clients. For maximum compatibility, only use letters, numbers, dash - and underscore _ characters only.', 'empowercast')); ?>');
            Div.css('display', 'block');
            jQuery('#empowercast_fail_'+FeedSlug).css("display", 'inline-block');
            jQuery( '#empowercast_url_show_'+FeedSlug ).css('background-color', '#FFF3CD');
            jQuery( '#empowercast_url_show_'+FeedSlug ).css('background-color');
			return false;
		}
	
		if( x == 5 )
			validChars = validChars.substring(1); // remove the colon, should no longer appear in URLs
	}

	Div.css('display', 'none');
	return true;
}


function empowercast_get_media_info(FeedSlug)
{
	if( jQuery('#empowercast_check_'+FeedSlug).css("display") != "none" )
		return; // Another process is already running

    jQuery('#empowercast_fail_'+FeedSlug).css("display", 'none');
	jQuery( '#empowercast_success_'+FeedSlug ).css('display', 'none');
	//jQuery( '#empowercast_warning_'+FeedSlug ).text('');
	jQuery( '#empowercast_warning_'+FeedSlug ).css('display', 'none');
	jQuery( '#empowercast_warning_'+FeedSlug ).addClass("pp-error");
	jQuery( '#empowercast_warning_'+FeedSlug ).removeClass("updated");
	
	var Value = jQuery('#empowercast_url_display_'+FeedSlug).val();
	var Hosting = jQuery('#empowercast_hosting_'+FeedSlug).val();
	var program_keyword = jQuery('#empowercast_program_keyword_'+FeedSlug).val();
	if( Value )
	{
		if( empowercast_check_url(Value, 'empowercast_warning_'+FeedSlug ) )
		{
			jQuery('#empowercast_check_'+FeedSlug).css("display", 'inline-block');
			jQuery.ajax( {
				type: 'POST',
				url: '<?php echo admin_url(); ?>admin-ajax.php',
				data: { action: 'empowercast_media_info', media_url : Value, feed_slug : encodeURIComponent(FeedSlug), hosting: Hosting, program_keyword: program_keyword, nonce: '<?php echo wp_create_nonce('empowercast-media-info'); ?>' },
				timeout: (30 * 1000),
				success: function(response) {

					response = response.trim();
					// This logic will parse beyond warning messages generated by the server that we don't know about
					var foundAt = response.indexOf('VERIFY-OK');
					if( foundAt > 0 )
					{
						response = response.substring( foundAt );
					}
					console.log(response);
					var Parts = response.split("\n", 5);

					var FinishFeedSlug = Parts[1];
					
					jQuery('#empowercast_check_'+FeedSlug).css("display", 'none');
					
					if( FeedSlug == FinishFeedSlug && Parts[0] == 'VERIFY-OK' )
					{
						// only set file size if auto detect is selected
                        var sizeChecked = jQuery('#empowercast_set_size_0_'+FeedSlug).attr('checked');
                        if(typeof jQuery.prop === 'function') {
                            sizeChecked = jQuery('#empowercast_set_size_0_'+FeedSlug).prop('checked');
                        }
						if (sizeChecked && Parts[2])
						{

                            jQuery('#empowercast_size_'+FeedSlug).val( Parts[2] );
                        }

                        // only set duration if auto detect is selected
                        var durationChecked = jQuery('#empowercast_set_duration_0_'+FeedSlug).attr('checked');
                        if(typeof jQuery.prop === 'function') {
                            durationChecked = jQuery('#empowercast_set_duration_0_'+FeedSlug).prop('checked');
                        }
						if( durationChecked && Parts[3] )
						{
							
							var Duration = Parts[3].split(':');
							jQuery('#empowercast_duration_hh_'+FeedSlug).val( Duration[0] );
							jQuery('#empowercast_duration_mm_'+FeedSlug).val( Duration[1] );
							jQuery('#empowercast_duration_ss_'+FeedSlug).val( Duration[2] );
						}
						else if( durationChecked )
						{
							jQuery('#empowercast_duration_hh_'+FeedSlug).val( '' );
							jQuery('#empowercast_duration_mm_'+FeedSlug).val( '' );
							jQuery('#empowercast_duration_ss_'+FeedSlug).val( '' );
						}

						if (Parts[4]) {
                            self.parent.document.getElementById('empowercast_url_' + FeedSlug).value = Parts[4];
                        }
						
						if( Parts.length > 5 && Parts[5] != '' )
						{
							jQuery( '#empowercast_warning_'+FeedSlug ).html( Parts[5] );
							jQuery( '#empowercast_warning_'+FeedSlug ).css('display', 'block');
							jQuery( '#empowercast_warning_'+FeedSlug ).addClass("updated");
							jQuery( '#empowercast_warning_'+FeedSlug ).removeClass("error");
						<?php
						if( defined('EMPOWERCAST_AJAX_DEBUG') )
							echo "\t\t\t\tjQuery( '#empowercast_warning_'+FeedSlug ).append( '<br/>Complete Response: '+ response);\n";
						?>
						}
						else
						{
							jQuery( '#empowercast_success_'+FeedSlug ).css('display', 'inline-block');
                            jQuery( '#empowercast_url_show_'+FeedSlug ).css('background-color', '#c1f2b8');
							// setTimeout( function() { jQuery( '#empowercast_success_'+FeedSlug ).fadeOut(1000); }, 10000 );
							<?php
						if( defined('EMPOWERCAST_AJAX_DEBUG') )
							echo "\t\t\t\tjQuery( '#empowercast_warning_'+FeedSlug ).append( '<br/>Complete Response: '+ response);\n";
						?>
						}
					}
					else
					{
						var Parts = response.split("\n", 5);
						if( Parts.length > 5 )
						{
							var server_error = response.replace(/\n/g, "<br \/>");
							jQuery( '#empowercast_warning_'+FeedSlug ).html( '<div style="text-align: left;">Server Error:</div><div style="text-align: left; font-weight: normal;">' + server_error +'<\/div>' );
						}
						else if( Parts[1] )
							jQuery( '#empowercast_warning_'+FeedSlug ).html( Parts[1] );
						else
							jQuery( '#empowercast_warning_'+FeedSlug ).text( '<?php echo __('Unknown error occurred while checking Media URL. You can still publish this episode, but will need to enter filesize and duration manually.', 'empowercast'); ?>' );

						<?php
						if( defined('EMPOWERCAST_AJAX_DEBUG') )
							echo "\t\t\t\tjQuery( '#empowercast_warning_'+FeedSlug ).append( '<br/>Complete Response: '+ response);\n";
						?>
						jQuery( '#empowercast_warning_'+FeedSlug ).css('display', 'block');
                        jQuery('#empowercast_fail_'+FeedSlug).css("display", 'inline-block');
                        jQuery( '#empowercast_url_show_'+FeedSlug ).css('background-color', '#eba2a2');
					}
				},
				error: function(objAJAXRequest, textStatus, errorThrown) {
					
					var errorCode = objAJAXRequest.status;
					var errorMsg = objAJAXRequest.statusText;
					var responseClean = '';
					if ( objAJAXRequest.responseText ) {
						responseClean = objAJAXRequest.responseText.replace( /<.[^<>]*?>/g, '' );
					}
					
					jQuery('#empowercast_check_'+FeedSlug).css("display", 'none');
                    jQuery( '#empowercast_warning_'+FeedSlug ).css('display', 'block');
                    jQuery('#empowercast_fail_'+FeedSlug).css("display", 'inline-block');
                    jQuery( '#empowercast_url_show_'+FeedSlug ).css('background-color', '#eba2a2');

					if( textStatus == 'timeout' ) {
						jQuery( '#empowercast_warning_'+FeedSlug ).text( '<?php echo __('Operation timed out.', 'empowercast'); ?>' );
					}
					else if( textStatus == 'error' ) {
						jQuery( '#empowercast_warning_'+FeedSlug ).html( errorCode +' - '+ errorThrown +'<br />');
					}
					else if( textStatus == 'abort' ) {
						jQuery( '#empowercast_warning_'+FeedSlug ).text( '<?php echo __('Operation aborted.', 'empowercast'); ?>' );
					}
					else if( textStatus == 'parsererror' ) {
						jQuery( '#empowercast_warning_'+FeedSlug ).text( '<?php echo __('Parse error occurred.', 'empowercast'); ?>' );
					}
					else if( textStatus != null ) {
						jQuery( '#empowercast_warning_'+FeedSlug ).text( '<?php echo __('AJAX Error', 'empowercast') .': '; ?>'+textStatus );
					}
					else if( errorMsg ) {
						jQuery( '#empowercast_warning_'+FeedSlug ).text( +errorMsg );
					}
					else {
						jQuery( '#empowercast_warning_'+FeedSlug ).text( '<?php echo __('AJAX Error', 'empowercast') .': '. __('Unknown', 'empowercast'); ?>' );
					}
					
					if( textStatus != 'error' && errorThrown ) { // If we have an error thrown, lets append it to the error message
						jQuery('#empowercast_warning_'+FeedSlug).append('<br/>'+errorThrown);
					}

					<?php
					if( defined('EMPOWERCAST_AJAX_DEBUG') ) {
						echo "\t\t\tif( objAJAXRequest.responseText ) {\n";
						echo "\t\t\t\tjQuery('#empowercast_warning_'+FeedSlug).text( jQuery('#empowercast_warning_'+FeedSlug).text() +' - Response: '+ objAJAXRequest.responseText);\n";
						echo "\t\t\t}\n";
					}
					?>
				}
			});
		}
	}
}

function empowercast_update_for_video(media_url, FeedSlug)
{
	if (media_url.search(/\.(mp4|m4v|ogg|ogv|webm)$/) > -1)
	{
		jQuery('#empowercast_ishd_'+ FeedSlug +'_span').css('display','inline');
	}
	else
	{
		jQuery('#empowercast_ishd_'+ FeedSlug +'_span').css('display','none');
		jQuery('#empowercast_ishd_'+ FeedSlug +'_span').removeAttr('checked');
		if(typeof jQuery.removeProp === 'function') {
			jQuery('#empowercast_ishd_'+ FeedSlug +'_span').removeProp('checked');
		}
	}
	
		
	if (media_url.search(/\.(mp4|m4v)$/) > -1)
	{
		jQuery('#empowercast_webm_'+ FeedSlug ).css('display', 'block');
	}
	else
	{
		jQuery('#empowercast_webm_'+ FeedSlug ).css('display', 'none');
	}
}

function empowercast_remove_hosting(FeedSlug)
{
	if( confirm('<?php echo __('Are you sure you want to remove this media file?', 'empowercast'); ?>') )
	{
		jQuery( '#empowercast_url_display_'+FeedSlug ).attr("readOnly", false);
		jQuery( '#empowercast_url_'+FeedSlug ).val('');
		jQuery( '#empowercast_hosting_'+FeedSlug ).val(0);
		jQuery( '#empowercast_hosting_note_'+FeedSlug ).css('display', 'none');
		empowercast_update_for_video('', FeedSlug);
	}
}

var pp_upload_image_button_funct = false;

jQuery(document).ready(function($) {
	
	jQuery('.empowercast-url').change(function() {
	
		var FeedSlug = this.id.replace(/(empowercast_url_)(.*)$/, "$2");
		if( !FeedSlug )
			return;
		
		var media_url = jQuery(this).val();
		empowercast_check_url(media_url,'empowercast_warning_'+FeedSlug)
		empowercast_update_for_video(media_url, FeedSlug);
	});
	
	jQuery('.empowercast-image-browser').click(function(e) {
		e.preventDefault();
		g_empowercast_last_selected_channel = this.id.replace(/(empowercast_image_browser_)(.*)$/, "$2");
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&amp;post_id=0', false);

		if( pp_upload_image_button_funct == false )
			pp_upload_image_button_funct = window.send_to_editor;
		
		window.send_to_editor = function(html)
		{
			url = jQuery('img', html).attr('src');
			if (url === undefined) {
				url = jQuery(html).attr('src');
			}
			jQuery('#empowercast_image_'+g_empowercast_last_selected_channel).val( url );
			g_empowercast_last_selected_channel = '';
			tb_remove();
			window.send_to_editor = pp_upload_image_button_funct;
			pp_upload_image_button_funct = false;
		}
		return false;
	});
	jQuery('.empowercast-itunes-image-browser').click(function(e) {
        console.log('here');
		e.preventDefault();
		g_empowercast_last_selected_channel = this.id.replace(/(empowercast_itunes_image_browser_)(.*)$/, "$2");
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&amp;post_id=0', false);

		if( pp_upload_image_button_funct == false )
			pp_upload_image_button_funct = window.send_to_editor;
		
		window.send_to_editor = function(html)
		{
			url = jQuery('img', html).attr('src');
			if (url === undefined) {
				url = jQuery(html).attr('src');
			}
			jQuery('#empowercast_itunes_image_'+g_empowercast_last_selected_channel).val( url );
			g_empowercast_last_selected_channel = '';
			tb_remove();
			window.send_to_editor = pp_upload_image_button_funct;
			pp_upload_image_button_funct = false;
		}
		return false;
	});
	jQuery('#insert-media-button').click( function(e) {
		if( pp_upload_image_button_funct != false )
		{
			window.send_to_editor = pp_upload_image_button_funct;
			pp_upload_image_button_funct = false;
		}
	});
	jQuery('.empowercast-embed').change( function() {
		// if there is a value in the embed box, but there is no value in the url box, then we need to display a warning...
		var FeedSlug = this.id.replace(/(empowercast_embed_)(.*)$/, "$2");
		if( !FeedSlug )
			return;
		
		var MediaURL = jQuery('#empowercast_url_'+FeedSlug).val();
		if( !MediaURL )
		{
			jQuery('#empowercast_warning_'+FeedSlug ).text( '<?php echo __('You must enter a Media URL in order to save Media Embed.', 'empowercast'); ?>');
			jQuery('#empowercast_warning_'+FeedSlug ).css('display', 'block');
			jQuery('#empowercast_url_'+FeedSlug).focus();
		}
	});
});

function empowercast_send_to_poster_image(url)
{
	if( g_empowercast_last_selected_channel )
	{
		jQuery('#empowercast_image_'+g_empowercast_last_selected_channel).val( url );
		g_empowercast_last_selected_channel = '';
	}
	tb_remove();
}
//-->
</script>
<?php
	}
	else if( $page_name == 'index' )
	{
		// Print this line for debugging when looking for other pages to include header data for
		//echo "<!-- WP Page Name: $page_name; Hook Suffix: $hook_suffix -->\n";

        if (defined('WP_DEBUG') && WP_DEBUG) {
            if (WP_DEBUG) {
                wp_register_style('empowercast-dashboard', empowercast_get_root_url() . 'css/dashboard.css', array(), EMPOWERCAST_VERSION);
            } else {
                wp_register_style('empowercast-dashboard', empowercast_get_root_url() . 'css/dashboard.min.css', array(), EMPOWERCAST_VERSION);
            }
        } else {
            wp_register_style('empowercast-dashboard', empowercast_get_root_url() . 'css/dashboard.min.css', array(), EMPOWERCAST_VERSION);
        }
        wp_enqueue_style( 'empowercast-dashboard' );
	}
}

add_action('admin_head', 'empowercast_admin_head');

function empowercast_check_account_verified_popup($no_signout_link = false) {
    $link_action_url = admin_url('admin.php?action=empowercast-jquery-account-verify');
    $link_action = 'empowercast-jquery-account-verify';
    $url = wp_nonce_url($link_action_url, $link_action);
    $url = str_replace("&amp;", "&", $url);
    if ($no_signout_link) {
        $no_signout = 'true';
    } else {
        $no_signout = 'false';
    }
    $url = $url . '&no_signout_link=' . $no_signout;
    echo "<input type='hidden' id='verify-account-url' value='" . $url . "'/>";
}

function empowercast_check_credentials($creds) {
    if (isset($creds['client_id'])) {
        if (!isset($creds['account_verified']) || !$creds['account_verified']) {
            empowercast_check_account_verified_popup($_GET['page'] == 'empowercastadmin_onboarding.php');
        }
    }
}

function empowercast_media_info_ajax()
{
    // Check for nonce security
    if (!isset($_POST['nonce'])) {
        exit;
    }
    if ( ! wp_verify_nonce( $_POST['nonce'], 'empowercast-media-info' ) ) {
        exit;
    }
    $feed_slug = htmlspecialchars($_POST['feed_slug']);
	$hosting = $_POST['hosting'];
	$size = 0;
	$duration = '';
	$GeneralSettings = get_option('empowercast_general');
	if (empty($GeneralSettings['podops_hosting'])) {
        $hosting = 0;
    }
	if (!$hosting && preg_match('#^(.+)://#', $_POST['media_url'])) {
        $media_url = filter_var($_POST['media_url'], FILTER_VALIDATE_URL);
    } else {
        $media_url = $_POST['media_url'];
    }
	$program_keyword = false;
	if( !empty($_POST['program_keyword']) ) {
		$program_keyword = $_POST['program_keyword'];
	} else if( !empty($GeneralSettings['podops_program_keyword']) ) {
		$program_keyword = $GeneralSettings['podops_program_keyword'];
	}

    if (!empty($program_keyword) && !preg_match('/[A-Za-z0-9\_]+/', $program_keyword)) {
        exit;
    }
	if( strpos($media_url, 'http://') !== 0 && strpos($media_url, 'https://') !== 0 && $hosting != 1 ) // If the url entered does not start with a http:// or https://
	{
		$media_url = rtrim($GeneralSettings['default_url'], '/') .'/'. $media_url;
	}
	// for media creek urls, just verify the content.podopshost.com version
    $orig_url = false;
    if (strpos($media_url, 'mc.podopshost.com') !== false) {
        $orig_url = $media_url;
        //remove query string
        $media_url_array = explode('?', $media_url);
        $media_url = $media_url_array[0];
        // replace media host
        $media_url = str_replace('mc.podopshost.com', 'content.podopshost.com', $media_url);
    }

    $ContentType = false;
	$UrlParts = parse_url($media_url);
	if( !empty($UrlParts['path']) )
	{
		// using functions that already exist in WordPress when possible:
		$ContentType = empowercast_get_contenttype($UrlParts['path'], false);
	}

    if (!$hosting) {
        $media_hostname = $UrlParts['host'];
        if (in_array($media_hostname, array('0.0.0.0', '127.0.0.1', 'localhost', '[::]', '0x7f000001/', '0xc0a80014/')) || filter_var($media_hostname, FILTER_VALIDATE_IP) || !preg_match('/^[a-zA-Z.\-\d]+$/i', $media_hostname)) {
            $error = __('Invalid url. Please ensure that your url is formatted correctly, e.g https://example.com/filename.mp3. You can still publish this episode, but will need to enter filesize and duration manually.', 'empowercast');
            echo "$feed_slug\n";
            echo $error;
            exit;
        }
    } else {
        // hosting flag means unpublished--use filename only!
        if (substr($media_url, 0, 4) == 'http') {
            $file_array = explode('/', $media_url);
            $file = $file_array[count($file_array) - 1];
            // remove query string
            $file_array = explode('?', $file);
            $media_url = $file_array[0];
        }

    }
	if( !$ContentType )
	{
		$error = __('Unable to determine content type of media (e.g. audio/mpeg). Verify file extension is correct and try again.', 'empowercast');
		echo "$feed_slug\n";
		echo $error;
		exit;
	}
	
	// Get media info here...
	if( $hosting )
		$MediaInfo = empowercast_get_media_info($media_url, $program_keyword );
	else
		$MediaInfo = empowercast_get_media_info_local($media_url, '', 0, '', true);

    //If the file is unpublished, check to make sure the user has hosting space
    if( strpos($media_url, 'http://') !== 0 && strpos($media_url, 'https://') !== 0) {
        if (isset($MediaInfo['space_remaining']) && !$MediaInfo['space_remaining']) {
            if (empty($MediaInfo['error'])) {
                $MediaInfo['error'] = __('Not enough storage space remaining to publish this file.', 'empowercast');
            } else {
                $MediaInfo['error'] .= ' ' . __('Not enough storage space remaining to publish this file.', 'empowercast');
            }
        }
    }

    echo "$feed_slug\n";
    if( !empty($MediaInfo['error']) ) {
        echo $MediaInfo['error'];
        if( preg_match('/^https?\:\/\//i', $media_url) )
            echo '<br />'. sprintf('Test: %s', "<a href=\"" . esc_attr($media_url) . "\" target=\"_blank\">" . htmlspecialchars($media_url) . "</a>");
    }
    echo "\n";

	if( !isset($MediaInfo['error']) )
	{
        //substr('', 'abc');
        echo "VERIFY-OK\n";
        echo "$feed_slug\n"; // swap positions
        if (!empty($MediaInfo['length'])) {
            echo "{$MediaInfo['length']}\n";
        } else {
            echo "\n";
        }
        if (!empty($MediaInfo['duration'])) {
            echo empowercast_readable_duration($MediaInfo['duration'], true) . "\n";
        } else {
            echo "\n";
        }
        echo $orig_url ? htmlspecialchars($orig_url) : htmlspecialchars($MediaInfo['enclosure_url']) . "\n";
        if (isset($MediaInfo['warnings']))
            echo $MediaInfo['warnings'];

        echo "\n"; // make sure this line is ended
        exit;
	}

	exit;
}
 
add_action('wp_ajax_empowercast_media_info', 'empowercast_media_info_ajax');

function empowercast_metamarks_addrow_ajax()
{
    // Check for nonce security
    if (!isset($_POST['nonce'])) {
        exit;
    }
    if ( ! wp_verify_nonce( $_POST['nonce'], 'empowercast-metamarks-addrow' ) ) {
        exit;
    }
	require_once(EMPOWERCAST_ABSPATH .'/views/episode-box.php');
	empowercast_metamarks_addrow();
}
add_action('wp_ajax_empowercast_metamarks_addrow', 'empowercast_metamarks_addrow_ajax');

function empowercast_dashboard_dismiss_ajax()
{
    // Check for nonce security
    if (!isset($_POST['nonce'])) {
        exit;
    }
    if ( ! wp_verify_nonce( $_POST['nonce'], 'empowercast-dashboard-dismiss' ) ) {
        exit;
    }
	require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-dashboard.php');
	empowercast_dashboard_dismiss();
}
add_action('wp_ajax_empowercast_dashboard_dismiss', 'empowercast_dashboard_dismiss_ajax');


function empowercast_create_subscribe_page()
{
    // Check for nonce security
    if (!isset($_POST['nonce'])) {
        exit;
    }
    if ( ! wp_verify_nonce( $_POST['nonce'], 'empowercast-create-subscribe-page' ) ) {
        exit;
    }
	$ajax = ( defined('DOING_AJAX') && DOING_AJAX ); // Now we can use this function without an ajax call! :)
	
	$template_url = 'http://plugins.svn.wordpress.org/empowercast/assets/subscribe_template/';
	$languages = array();
	$language = get_option( 'WPLANG' );
	if( !empty($language) ) {
		switch( $language )
		{
			// Template translated for the following languages
			case 'en_AU':
			case 'en_CA':
			case 'en_GB':
			case 'en_NZ':
			case 'en_ZA': {
				$languages[] = $language; // Placed at the top of array
			}; break;
		}
	}
	$languages[] = 'en_US'; // fallback to the en_US version

	$template_content = false;
	foreach( $languages as $index => $lang )
	{
		$template_content = empowercast_remote_fopen( $template_url . $lang . '.txt' );
		if( empty($template_content) ) { // Lets force cURL and see if that helps...
			$template_content = empowercast_remote_fopen($template_url . $lang . '.txt', false, array(), 15, false, true);
		}
		if( !empty($template_content) ) // We found a translation!
			break;
	}
	
	if( empty($template_content) )
	{
		if( $ajax )
		{
			echo "PAGE-ERROR\n";
			echo __('Error occurred downloading subscribe page template.', 'empowercast');
			exit;
		}
		return false;
	}

	// delete heading stuff from template
    $template_array = explode('[empowercast_subscribe]', $template_content);
	$template_content = '[empowercast_subscribe]' . $template_array[1];
	
	// Create page here...
	global $user_ID;
	$page['post_type']    = 'page';
	$page['post_content'] = $template_content;
	$page['post_parent']  = 0;
	$page['post_author']  = $user_ID;
	$page['post_status']  = 'publish';
	$page['post_title']   = __('Subscribe to Podcast', 'empowercast');
	
	$pageid = wp_insert_post ($page);
	if ($pageid == 0)
	{
		if( $ajax ) {
			echo "PAGE-ERROR\n";
			echo __('Error occurred creating subscribe page.', 'empowercast');
			exit;
		}
		return false;
	}

	// Save to settings...
	$Save = array('subscribe_page_link_id'=>$pageid );
	empowercast_save_settings($Save, 'empowercast_feed_podcast');
	
	// send back the page URL and Page ID
	if( $ajax ) {
		echo "PAGE-OK\n";
		echo "$pageid\n";
		echo get_page_link($pageid). "\n";
		echo $page['post_title']. "\n";
		exit;
	}
	return true;
}
add_action('wp_ajax_empowercast_create_subscribe_page', 'empowercast_create_subscribe_page');

function empowercast_cat_row_actions($actions, $object)
{
	$General = get_option('empowercast_general');
	
	
	// New 3.0+ tag in taxonomy check
	if( !empty($General['tag_casting']) && !empty($object->taxonomy) && $object->taxonomy == 'tag' )
	{
		// TODO:
	}
	
	// Otherwise from here on in, we're working with a category or nothing at all.
	if( empty($General['cat_casting']) )
		return $actions;
	
	// 3.0 category in taxonomy check
	if( !empty($object->taxonomy) && $object->taxonomy != 'category' )
		return $actions;
		
	$cat_id = (isset($object->term_id)?$object->term_id : $object->cat_ID);
	
	if( empty($cat_id) )
		return $actions;
	
	if( isset($General['custom_cat_feeds']) && is_array($General['custom_cat_feeds']) && in_array($cat_id, $General['custom_cat_feeds']) )
	{
		$edit_link = admin_url('admin.php?page=empowercast/empowercastadmin_categoryfeeds.php&amp;from_categories=1&amp;action=empowercast-editcategoryfeed&amp;cat=') . $cat_id;
		$actions['empowercast'] = '<a href="' . $edit_link . '" title="'. __('Edit Podops EmpowerCast Podcast Settings', 'empowercast') .'">' . str_replace(' ', '&nbsp;', __('Podcast Settings', 'empowercast')) . '</a>';
	}
	else
	{
		$edit_link = admin_url() . wp_nonce_url("admin.php?page=empowercast/empowercastadmin_categoryfeeds.php&amp;from_categories=1&amp;action=empowercast-addcategoryfeed&amp;taxonomy=category&amp;cat=".$cat_id, 'empowercast-add-taxonomy-feed');
		$actions['empowercast'] = '<a href="' . $edit_link . '" title="'. __('Add Podops EmpowerCast Podcasting Settings', 'empowercast') .'">' . str_replace(' ', '&nbsp;', __('Add Podcasting', 'empowercast')) . '</a>';
	}
	return $actions;
}

add_filter('cat_row_actions', 'empowercast_cat_row_actions', 1,2);
add_filter('tag_row_actions', 'empowercast_cat_row_actions', 1,2);

// Handles category and all other taxonomy terms
function empowercast_delete_term($term_id, $tt_id, $taxonomy)
{
	if( $taxonomy == 'category' )
	{
		$Settings = get_option('empowercast_general');
		if( isset($Settings['custom_cat_feeds']) )
		{
			$key = array_search($term_id, $Settings['custom_cat_feeds']);
			if( $key !== false )
			{
				unset( $Settings['custom_cat_feeds'][$key] );
				empowercast_save_settings($Settings); // Delete the feed from the general settings
			}
		}
		delete_option('empowercast_cat_feed_'.$term_id); // Delete the actual feed settings
	}
	else // All other taxonomies handled here
	{
		$Settings = get_option('empowercast_taxonomy_podcasting');
		
		if( isset($Settings[ $tt_id ])  )
		{
			unset( $Settings[ $tt_id ] );
			empowercast_save_settings($Settings); // Delete the feed from the general settings
		}
		delete_option('empowercast_taxonomy_'.$tt_id); // Delete the actual feed settings
	}
}

add_action('delete_term', 'empowercast_delete_term', 10, 3);


function empowercast_edit_category_form($cat)
{
	if( empty($cat) || !isset( $cat->cat_ID ) )
	{
?>
<div>
<?php
		$General = get_option('empowercast_general');
		if( !isset($General['cat_casting']) || $General['cat_casting'] == 0 )
		{
			$enable_link = admin_url() . wp_nonce_url('edit-tags.php?taxonomy=category&action=empowercast-enable-categorypodcasting', 'empowercast-enable-categorypodcasting');
?>
	<h2><?php echo __('EmpowerCast Category Podcasting'); ?></h2>
	<p><a href="<?php echo $enable_link; ?>" title="<?php echo __('Enable Category Podcasting', 'empowercast'); ?>"><?php echo __('Enable Category Podcasting', 'empowercast'); ?></a> <?php echo __('if you would like to add specific podcasting settings to your blog categories.', 'empowercast'); ?></p>
<?php
		}
		else
		{
?>
	<h2><?php echo __('EmpowerCast Category Podcasting', 'empowercast'); ?></h2>
	<p><?php echo __('EmpowerCast Category Podcasting is enabled. Select \'Add Podcasting\' to add podcasting settings. Select <u>Podcast Settings</u> to edit existing podcast settings.', 'empowercast'); ?></p>
<?php
		}
?>
</div>
<?php
	}
}
add_action('category_edit_form', 'empowercast_edit_category_form');

// Admin page, header
function empowercast_admin_page_header($page=false, $nonce_field = 'empowercast-edit', $page_type='')
{
	if( !$page )
		$page = 'empowercastadmin_basic';
?>
<div class="wrap" id="empowercast_settings">
<?php
	if( $nonce_field )
	{
?>
<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page='. urlencode($page) ) ?>">
<?php
		wp_nonce_field($nonce_field);
	}
	if( !empty($page_type) )
		echo '<input type="hidden" name="page_type" value="'. $page_type .'" />';
			
	empowercast_page_message_print();
}

// Admin page, footer
function empowercast_admin_page_footer($SaveButton=true, $form=true)
{
	if( $SaveButton ) { ?>
<h2 style="margin-bottom: 0;"><b><?php echo __('Looking for Support?','empowercast'); ?></b></h2>
<p class="submit" style="margin-top: 0; margin-left: 2em;">
    <a title="<?php echo esc_attr(__('Podops Services Info', 'empowercast')); ?>"
       href="https://podopshost.com/services/podcast-hosting/"
       target="_blank"><?php echo __('Learn more about Podops Support Options', 'empowercast'); ?></a>
</p>
<p class="submit">
<strong><i><?php echo empowercast_review_message(); ?></i></strong>
</p>
<?php } ?>
<p style="font-size: 85%; text-align: center; padding-bottom: 35px; width: 100%;">
	<a href="https://podopshost.com/services/empowercast-plugin/" title="Podops EmpowerCast" target="_blank"><?php echo __('Podops EmpowerCast', 'empowercast'); ?></a> <?php echo EMPOWERCAST_VERSION; ?> &#8212;
	<a href="https://podopshost.com/services/podcast-hosting/" target="_blank" title="<?php echo __('Podops EmpowerCast and related Resources', 'empowercast'); ?>"><?php echo __('Resources', 'empowercast'); ?></a> |
	<a href="https://podopshost.com/support/" target="_blank" title="<?php echo __('Podops Support', 'empowercast'); ?>"><?php echo __('Support', 'empowercast'); ?></a>
</p>
<?php if( $form ) { ?>
</form><?php } ?>
</div>
<?php 
}

// Admin page, footer
function empowercast_settings_save_button($blue = false, $float_left = false)
{
    if ($float_left) {
        $float_string = '';
    } else {
        $float_string = ' pp-save-button-container-right';
    }
    if ($blue) {
        $class = "empowercast_save_button_other";
    } else {
        $class = "empowercast_save_button";
    }?>

    <div class="pp-save-button-container<?php echo $float_string; ?>">
        <input type="submit" name="Submit" class="<?php echo $class; ?>" value="<?php echo __('Save Changes', 'empowercast') ?>" />
    </div>
    <?php
}

// Admin page, advanced mode: basic settings
function empowercast_admin_page_basic()
{
	empowercast_admin_page_header();
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-basic.php');
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-editfeed.php');
	empowercast_admin_basic();
	empowercast_admin_page_footer(true);
}

// Admin page, advanced mode: basic settings
function empowercast_admin_page_players()
{
	empowercast_admin_page_header('empowercast/empowercastadmin_player.php');
	require_once( EMPOWERCAST_ABSPATH.'/empowercastadmin-player-page.php');
	empowercast_admin_players('audio');
	empowercast_admin_page_footer(true);
}

function empowercast_admin_page_videoplayers()
{
	empowercast_admin_page_header('empowercast/empowercastadmin_videoplayer.php');
	require_once( EMPOWERCAST_ABSPATH.'/empowercastadmin-player-page.php');
	empowercast_admin_players('video');
	empowercast_admin_page_footer(true);
}

function empowercast_admin_page_mobileplayers()
{
	empowercast_admin_page_header('empowercast/empowercastadmin_mobileplayer.php');
	require_once( EMPOWERCAST_ABSPATH.'/empowercastadmin-player-page.php');
	empowercast_admin_players('mobile');
    empowercast_settings_save_button(true);
	empowercast_admin_page_footer(true);
}

// Admin page, advanced mode: feed settings
function empowercast_admin_page_podpress_stats()
{
	empowercast_admin_page_header('empowercast/empowercastadmin_podpress-stats.php');
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-podpress-stats.php');
	empowercast_admin_podpress_stats();
	empowercast_admin_page_footer(false);
}

// Migrate
function empowercast_admin_page_migrate()
{
	empowercast_admin_page_header('empowercast/empowercastadmin_migrate.php');
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-migrate.php');
	empowercast_admin_migrate();
	empowercast_admin_page_footer(false);
}

function empowercast_admin_page_import_feed()
{
	empowercast_admin_page_header('empowercast/empowercastadmin_import_feed.php');
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-import-feed.php');
	empowercast_admin_import_feed();
	empowercast_admin_page_footer(false);
}

function marketing_footer($button = false) {
    // only display if not signed in
    $page = htmlspecialchars($_GET['page']);
    $creds = get_option('empowercast_creds');
    if (!$creds) {
        $pp_nonce = empowercast_login_create_nonce();
        echo '<div class="empowercast-podops-hosting-marketing">';
        echo '<img class="hosting-marketing-podops-logo" src="' . empowercast_get_root_url() . 'images/Podops2017Logo-LOWERCASE.svg" alt="Podops Podcasting" />';
        echo '<div class="hosting-marketing-text-container" ';
        if ($button) {
            echo 'style="width: 55%">';
        } else {
            echo 'style="width: 70%">';
        }
        echo '<h4 class="hosting-marketing-title">Publish everywhere from this website</h4>';
        echo '<h5 class="hosting-marketing-text">You want podcast hosting that\'s easy to use with full control. ';
        if ($button) {
            echo 'Try Podops hosting ';
        } else {
            echo '<a class="hosting-marketing-text" href="' . esc_attr(add_query_arg( '_wpnonce', $pp_nonce, admin_url("admin.php?page={$page}&step=podopsSignup&onboarding_type=stats"))) . '" target="_blank">Try Podops hosting</a> ';
        }
        echo 'for free and enjoy additional publishing features with EmpowerCast.</h4></div>';
        if ($button) {
            echo '<a class="hosting-marketing-link-button" href="' . esc_attr(add_query_arg( '_wpnonce', $pp_nonce, admin_url("admin.php?page={$page}&step=podopsSignup&onboarding_type=stats"))) . '" target="_blank">';
            echo '<button class="hosting-marketing-button">TRY PODOPS HOSTING FOR FREE</button>';
            echo '</a>';
        }
        echo '</div>';
    }
}

function empowercast_admin_page_onboarding() {
    empowercast_admin_page_header('empowercast/empowercastadmin_onboarding.php', false);
    require_once( EMPOWERCAST_ABSPATH .'/class.empowercast_onboarding.php');
    $onboardinClass = new EmpowercastOnboarding();
    $onboardinClass->router($_GET);
    empowercast_admin_page_footer(false, false);
}


// Admin page, advanced mode: custom feeds
function empowercast_admin_page_customfeeds()
{
	$Action = (!empty($_GET['action'])? $_GET['action'] : false);

    if (!$Action) {
        $Action = (!empty($_POST['action'])? $_POST['action'] : false);
    }

    switch( $Action )
	{
		case 'empowercast-editfeed' : {
			empowercast_admin_page_header('empowercast/empowercastadmin_customfeeds.php');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-editfeed.php');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-basic.php');
			$feed_slug = esc_attr($_GET['feed_slug']);
			empowercast_admin_editfeed('channel', $feed_slug);
			empowercast_admin_page_footer();
		}; break;
		default: {
			empowercast_admin_page_header('empowercast/empowercastadmin_customfeeds.php', 'empowercast-add-feed');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-customfeeds.php');
			empowercast_admin_customfeeds();
			empowercast_admin_page_footer(false);
		};
	}
}

function empowercast_admin_page_live_item()
{
    $Action = (!empty($_GET['action'])? $_GET['action'] : false);
    switch( $Action )
    {
        case 'empowercast-editfeed' : {
            empowercast_admin_page_header('empowercast/empowercastadmin_customfeeds.php');
            require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-editfeed.php');
            require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-basic.php');
            $feed_slug = esc_attr($_GET['feed_slug']);
            empowercast_admin_editfeed('channel', $feed_slug);
            empowercast_admin_page_footer();
        }; break;
        default: {
            empowercast_admin_page_header('empowercast/empowercastadmin_live_item.php', 'empowercast-live-item');
            require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-live-item.php');
            empowercast_admin_live_item();
            empowercast_admin_page_footer(false);
        };
    }
}

// Category feeds
function empowercast_admin_page_categoryfeeds()
{
	$Action = (!empty($_GET['action'])? $_GET['action'] : false);
	switch( $Action )
	{
		case 'empowercast-editcategoryfeed' : {
			empowercast_admin_page_header('empowercast/empowercastadmin_categoryfeeds.php');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-editfeed.php');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-basic.php');
			empowercast_admin_editfeed('category', intval($_GET['cat']) );
			empowercast_admin_page_footer();
		}; break;
		default: {
			empowercast_admin_page_header('empowercast/empowercastadmin_categoryfeeds.php', 'empowercast-add-categoryfeed');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-categoryfeeds.php');
			empowercast_admin_categoryfeeds();
			empowercast_admin_page_footer(false);
		};
	}
}

// Taxonomy Feeds
function empowercast_admin_page_taxonomyfeeds()
{
	$Action = (!empty($_GET['action'])? $_GET['action'] : false);
	switch( $Action )
	{
		case 'empowercast-edittaxonomyfeed' : {
			if( !empty($_GET['ttid']) )
			{
				empowercast_admin_page_header('empowercast/empowercastadmin_taxonomyfeeds.php');
				require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-editfeed.php');
				require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-basic.php');
				empowercast_admin_editfeed('ttid', intval($_GET['ttid']));
				empowercast_admin_page_footer();
			}
		}; break;
		default: {
			empowercast_admin_page_header('empowercast/empowercastadmin_taxonomyfeeds.php', 'empowercast-add-taxonomyfeed');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-taxonomyfeeds.php');
			empowercast_admin_taxonomyfeeds();
			empowercast_admin_page_footer(false);
		};
	}
}

// Custom Post Type Feeds
function empowercast_admin_page_posttypefeeds()
{
	
	$Action = (!empty($_GET['action'])? $_GET['action'] : false);
	switch( $Action )
	{
		case 'empowercast-editposttypefeed' : {
			if( !empty($_GET['podcast_post_type']) && !empty($_GET['feed_slug']) ) {
				
				empowercast_admin_page_header('empowercast/empowercastadmin_posttypefeeds.php');
				require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-editfeed.php');
				require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-basic.php');
				$post_type = esc_attr( $_GET['podcast_post_type'] );
				$feed_slug = esc_attr( $_GET['feed_slug'] );
				empowercast_admin_editfeed('post_type', $post_type, $feed_slug);
				empowercast_admin_page_footer();
				
			}
		} break; 
		default: {
			empowercast_admin_page_header('empowercast/empowercastadmin_posttypefeeds.php', 'empowercast-add-posttypefeed');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-posttypefeeds.php');
			empowercast_admin_posttypefeeds();
			empowercast_admin_page_footer(false);
		};
	}
}

// Admin page, advanced mode: tools
function empowercast_admin_page_tools()
{
	$Action = (!empty($_GET['action'])? $_GET['action'] : false);
	if ($Action == 'empowercast-network-mode-off') {
        $GeneralSettings = get_option('empowercast_general');
        $GeneralSettings['network_mode'] = 0;
        empowercast_save_settings($GeneralSettings);
        $Action = '';
    } elseif ($Action == 'empowercast-network-mode-on') {
        $GeneralSettings = get_option('empowercast_general');
	    $GeneralSettings['network_mode'] = 1;
        empowercast_save_settings($GeneralSettings);
        $Action = '';
    }
	switch( $Action )
	{
		case 'empowercast-podpress-epiosdes' : {
			empowercast_admin_page_header('empowercast/empowercastadmin_tools.php', 'empowercast-import-podpress');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-podpress.php');
			empowercast_admin_podpress();
			empowercast_admin_page_footer(false);
		}; break;
		case 'empowercast-mt-epiosdes': {
			empowercast_admin_page_header('empowercast/empowercastadmin_tools.php', 'empowercast-import-mt');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-mt.php');
			empowercast_admin_mt();
			empowercast_admin_page_footer(false);
		}; break;
		case 'empowercast-ping-sites': {
			empowercast_admin_page_header('empowercast/empowercastadmin_tools.php', 'empowercast-ping-sites');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-ping-sites.php');
			empowercast_admin_ping_sites();
			empowercast_admin_page_footer(false);
		}; break;
		case 'empowercast-find-replace': {
			empowercast_admin_page_header('empowercast/empowercastadmin_tools.php', 'empowercast-find-replace');
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-find-replace.php');
			empowercast_admin_find_replace();
			empowercast_admin_page_footer(false);
		}; break;
		case 'empowercast-diagnostics': {
			empowercast_admin_page_header('empowercast/empowercastadmin_tools.php', false);
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-diagnostics.php');
			empowercastadmin_diagnostics();
			empowercast_admin_page_footer(false, false);
		}; break;
		default: {
			empowercast_admin_page_header('empowercast/empowercastadmin_tools.php', false);
			require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-tools.php');
			empowercast_admin_tools();
			empowercast_admin_page_footer(false, false);
		};
	}
}

function empowercast_podpress_episodes_exist()
{
	global $wpdb;
	$query = "SELECT post_id ";
	$query .= "FROM {$wpdb->postmeta} ";
	$query .= "WHERE meta_key LIKE '%podPressMedia' ";
	$query .= "LIMIT 0, 1";
	$results = $wpdb->get_results($query, ARRAY_A);
	if( count($results) )
		return true;
	return false;
}

function empowercast_podpress_stats_exist()
{
	global $wpdb;
	// First, see if the table exists...
	$query = "SHOW TABLES LIKE '{$wpdb->prefix}podpress_statcounts'";
	$wpdb->hide_errors();
	$results = $wpdb->get_results($query, ARRAY_A);
	$wpdb->show_errors();
	if( count($results) == 0 )
		return false;
	
	// Now see if a record exists...
	$query = "SELECT `media` ";
	$query .= "FROM {$wpdb->prefix}podpress_statcounts ";
	$query .= "LIMIT 1";
	$results = $wpdb->get_results($query, ARRAY_A);
	if( count($results) )
		return true;
	return false;
}

/*
// Helper functions:
*/
function empowercast_remote_fopen($url, $basic_auth = false, $post_args = array(), $timeout = 15, $custom_request = false, $force_curl=false )
{
	unset($GLOBALS['g_empowercast_remote_error']);
	unset($GLOBALS['g_empowercast_remote_errorno']);
	
	if( ($force_curl || (defined('EMPOWERCAST_CURL') && EMPOWERCAST_CURL) ) && function_exists( 'curl_init' ) )
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		
		if ( version_compare( PHP_VERSION, '5.3.0') < 0 )
		{
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Follow location redirection
			curl_setopt($curl, CURLOPT_MAXREDIRS, 12); // Location redirection limit
		}
		else if ( !ini_get('open_basedir') )
		{
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Follow location redirection
			curl_setopt($curl, CURLOPT_MAXREDIRS, 12); // Location redirection limit
		}
		else
		{
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($curl, CURLOPT_MAXREDIRS, 0 );
		}

		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2 ); // Connect time out
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); // The maximum number of seconds to execute.
		curl_setopt($curl, CURLOPT_USERAGENT, 'Podops EmpowerCast/'.EMPOWERCAST_VERSION);
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		if( preg_match('/^https:\/\//i', $url) != 0 )
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2 );
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true );
			curl_setopt($curl, CURLOPT_CAINFO, ABSPATH . WPINC . '/certificates/ca-bundle.crt');
		}
		// HTTP Authentication
		if( $basic_auth )
		{
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$basic_auth) );
		}
		// HTTP Post:
		if( count($post_args) > 0 )
		{
			$post_query = '';
			foreach( $post_args as $name => $value )
			{
				if( $post_query != '' )
					$post_query .= '&';
				$post_query .= $name;
				$post_query .= '=';
				$post_query .= urlencode($value);
			}
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_query);
		}
		else if( $custom_request )
		{
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $custom_request);
		}
		
		$content = curl_exec($curl);
		$error = curl_errno($curl);
		$error_msg = curl_error($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		
		curl_close($curl);
		if( $error )
		{
			$GLOBALS['g_empowercast_remote_error'] = $error_msg;
			$GLOBALS['g_empowercast_remote_errorno'] = $http_code;
			//echo 'error: '.$content;
			
			$decoded = json_decode($content);
			if( !empty($decoded) )
				return $content; // We can still return the error from the server at least
			return false;
		}
		else if( $http_code > 399 )
		{
			//echo '40x';
			$GLOBALS['g_empowercast_remote_error'] = "HTTP $http_code";
			$GLOBALS['g_empowercast_remote_errorno'] = $http_code;
			switch( $http_code )
			{
				case 400: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Bad Request", 'empowercast'); break;
				case 401: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Unauthorized (Check that your username and password are correct)", 'empowercast'); break;
				case 402: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Payment Required", 'empowercast'); break;
				case 403: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Forbidden", 'empowercast'); break;
				case 404: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Not Found", 'empowercast'); break;
			}
			
			$decoded = json_decode($content);
			if( !empty($decoded) )
				return $content; // We can still return the error from the server at least
			return false;
		}
		return $content;
	}
	
	if( $force_curl )
		return false; // Do not continue, we wanted to use cURL
	
	$options = array();
	$options['timeout'] = $timeout;
	$options['user-agent'] = 'Podops EmpowerCast/'.EMPOWERCAST_VERSION;
	if( $basic_auth )
		$options['headers']['Authorization'] = 'Basic '.$basic_auth;
	
	if( count($post_args) > 0 )
	{
		$options['body'] = $post_args;
		$response = wp_remote_post( $url, $options );
	}
	else if($custom_request) {
	    $options['method'] = $custom_request;
	    $response = wp_remote_request($url,$options);
    }
	else
	{
		$response = wp_remote_get( $url, $options );
	}
	
	if ( is_wp_error( $response ) )
	{
		$GLOBALS['g_empowercast_remote_errorno'] = $response->get_error_code();
		$GLOBALS['g_empowercast_remote_error'] = $response->get_error_message();
		return false;
	}
	
	if( isset($response['response']['code']) && $response['response']['code'] > 399 )
	{
		$GLOBALS['g_empowercast_remote_error'] = "HTTP ".$response['response']['code'];
		$GLOBALS['g_empowercast_remote_errorno'] = $response['response']['code'];
		switch( $response['response']['code'] )
		{
			case 400: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Bad Request", 'empowercast'); break;
			case 401: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Unauthorized (Check that your username and password are correct)", 'empowercast'); break;
			case 402: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Payment Required", 'empowercast'); break;
			case 403: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Forbidden", 'empowercast'); break;
			case 404: $GLOBALS['g_empowercast_remote_error'] .= ' '. __("Not Found", 'empowercast'); break;
			default: $GLOBALS['g_empowercast_remote_error'] .= ' '.$response['response']['message'];
		}
	}

	return $response['body'];
}

// Process any episodes for the specified post that have been marked for hosting and that do not have full URLs...
function empowercast_process_hosting($post_ID, $post_title, $chapter_url)
{
	$errors = array();
	$Settings = get_option('empowercast_general');
    $creds = get_option('empowercast_creds');
    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
    $auth = new EmpowerCastAuth();

    $CustomFeeds = array();
	if( !empty($Settings['custom_feeds']) && is_array($Settings['custom_feeds']) )
		$CustomFeeds = $Settings['custom_feeds'];
	if( !isset($CustomFeeds['podcast']) )
		$CustomFeeds['podcast'] = 'podcast';
		
	
	if( !empty($Settings['posttype_podcasting']) )
	{
		$FeedSlugPostTypesArray = get_option('empowercast_posttype-podcasting');
		foreach( $FeedSlugPostTypesArray as $feed_slug => $null )
		{
			if( empty($CustomFeeds[$feed_slug]) )
				$CustomFeeds[$feed_slug] = $feed_slug;
		}
	}

    $api_url_array = empowercast_get_api_array();
	foreach( $CustomFeeds as $feed_slug => $null )
	{
		$field = 'enclosure';
		if( $feed_slug != 'podcast' )
			$field = '_'.$feed_slug.':enclosure';
		$EnclosureData = get_post_meta($post_ID, $field, true);
		$post_guid = get_the_guid($post_ID);
		$post_time = get_post_time('U', false, $post_ID);

		if( $EnclosureData )
		{
			/*
			// Old Logic, replaced with below $MetaParts so no notices appear
			list($EnclosureURL, $EnclosureSize, $EnclosureType, $Serialized) = explode("\n", $EnclosureData, 4);
			$EnclosureURL = trim($EnclosureURL);
			$EnclosureType = trim($EnclosureType);
			$EnclosureSize = trim($EnclosureSize);
			$EpisodeData = unserialize($Serialized);
			*/
			$MetaParts = explode("\n", $EnclosureData, 4);
			$EnclosureURL = '';
			if( count($MetaParts) > 0 )
				$EnclosureURL = trim($MetaParts[0]);
			
			$EnclosureSize = '';
			if( count($MetaParts) > 1 )
				$EnclosureSize = trim($MetaParts[1]);
			$EnclosureType = '';
			if( count($MetaParts) > 2 )
				$EnclosureType = trim($MetaParts[2]);
				
			$EpisodeData = false;
			if( count($MetaParts) > 3 )
				$EpisodeData = unserialize($MetaParts[3]);
				
			if( $EnclosureType == '' )
			{
				$error = __('Podops Hosting Error (publish)', 'empowercast') .': '. __('Error occurred obtaining enclosure content type.', 'empowercast');
				empowercast_add_error($error);
			}

            // only pass EPISODE level image
            if ($EpisodeData) {
                $episode_art = $EpisodeData['image'] ?? '';
            }

			if( $EpisodeData && (!empty($EpisodeData['hosting']) ||
                    !empty($_POST['Empowercast'][$feed_slug]['transcript']['edit']) ||
                    !empty($_POST['Empowercast'][$feed_slug]['chapters']['edit'])))
			{
				$program_keyword = (!empty($EpisodeData['program_keyword']) ? $EpisodeData['program_keyword'] : $Settings['podops_program_keyword'] );
				$error = false;
				if (!empty($EpisodeData['hosting'])) {
                    // First we need to get media information...
                    // If we are working with an Mp3, we can write id3 tags and get the info returned...
                    if (($EnclosureType == 'audio/mpg' || $EnclosureType == 'audio/mpeg') && !empty($Settings['write_tags'])) {
                        $results = empowercast_write_tags($EnclosureURL, $post_title, $program_keyword);
                        // if error, try inputting just the file name in case it's unpublished
                        if ((strpos($EnclosureURL, 'ins.podopshost.com') || strpos($EnclosureURL, 'content.podopshost.com') || strpos($EnclosureURL, 'content3.podops.biz') || strpos($EnclosureURL, 'protected.podopshost.com') || strpos($EnclosureURL, 'mc.podopshost.com')) && (isset($results['error']) || !is_array($results) || !$results)) {
                            $EnclosureUrLArray = explode('/', $EnclosureURL);
                            $EnclosureURL = end($EnclosureUrLArray);
                            $EnclosureUrlQSArray = explode('?', $EnclosureURL);
                            $EnclosureURL = $EnclosureUrlQSArray[0];
                            $results = empowercast_write_tags($EnclosureURL, $post_title, $program_keyword);
                        }
                    } else {
                        // order of logic below is reversed due to changes in empowercast_get_media_info to properly verify media in drafts
                        // first, try as if the file is unpublished
                        $EnclosureFullURL = $EnclosureURL;
                        $EnclosureUrLArray = explode('/', $EnclosureURL);
                        $EnclosureURL = end($EnclosureUrLArray);
                        $EnclosureUrlQSArray = explode('?', $EnclosureURL);
                        $EnclosureURL = $EnclosureUrlQSArray[0];
                        $results = empowercast_get_media_info($EnclosureURL, $program_keyword);

                        // if error, try the full URL
                        if ((isset($results['error']) || !is_array($results))) {
                            $EnclosureURL = $EnclosureFullURL;
                            $results = empowercast_get_media_info($EnclosureURL, $program_keyword);
                        }
                    }

                    if (is_array($results) && !isset($results['error'])) {
                        if (isset($results['duration']) && $results['duration'])
                            $EpisodeData['duration'] = $results['duration'];
                        if (isset($results['content-type']) && $results['content-type'])
                            $EnclosureType = $results['content-type'];
                        if (isset($results['length']) && $results['length'])
                            $EnclosureSize = $results['length'];
                    } else if (isset($results['error'])) {
                        $error = __('Podops Hosting Error (media info)', 'empowercast') . ': ' . $results['error'];
                        empowercast_add_error($error);
                    } else {
                        $error = sprintf(__('Podops Hosting Error (media info): An error occurred publishing media %s.', 'empowercast'), $EnclosureURL);
                        $error .= ' ';
                        $rand_id = rand(100, 2000);
                        $error .= '<a href="#" onclick="document.getElementById(\'empowercast_error_' . $rand_id . '\');this.style.display=\'none\';return false;">' . __('Display Error', 'empowercast') . '</a>';
                        empowercast_add_error($error);
                    }

                    if ($error == false) {
                        $post_vars = array('episode_art' => $episode_art, 'podcast_post_date' => $post_time, 'podcast_title' => $post_title, 'podcast_subtitle' => isset($EpisodeData['subtitle']) ? $EpisodeData['subtitle'] : '');
                        if (!empty($EpisodeData['alternate_enclosure'])) {
                            $post_vars['alternate_enclosures'] = array();
                            foreach ($EpisodeData['alternate_enclosure'] as $alternate_enclosure) {
                                if (!empty($alternate_enclosure['hosting'])) {
                                    $post_vars['alternate_enclosures'][] = $alternate_enclosure;
                                    // write id3 tags if necessary
                                    if (($alternate_enclosure['type'] == 'audio/mpg' || $alternate_enclosure['type'] == 'audio/mpeg') && !empty($Settings['write_tags'])) {
                                        $alt_enclosure_tag_results = empowercast_write_tags($alternate_enclosure['url'], $post_title, $program_keyword);
                                        if (isset($alt_enclosure_tag_results['error'])) {
                                            $error = __('Podops Hosting Error (alternate enclosure)', 'empowercast') . ': ' . $alt_enclosure_tag_results['error'];
                                            empowercast_add_error($error);
                                        }
                                    }
                                }
                            }
                        }

                        // Extend the max execution time here
                        @set_time_limit(60 * 20); // give it 20 minutes just in case
                        $json_data = false;
                        if ($creds) {
                            $accessToken = empowercast_getAccessToken();
                            $req_url = sprintf('/2/media/%s/%s?format=json&publish=true&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword), urlencode($EnclosureURL));
                            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                            $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                            $results = $auth->api($accessToken, $req_url, $post_vars, false, 60 * 30, true, true);
                        } else {
                            foreach ($api_url_array as $index => $api_url) {
                                $req_url = sprintf('%s/media/%s/%s?format=json&publish=true&cache=' . md5(rand(0, 999) . time()), rtrim($api_url, '/'), urlencode($program_keyword), urlencode($EnclosureURL));
                                $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
                                $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                                $json_data = empowercast_remote_fopen($req_url, $Settings['podops_auth'], $post_vars, 60 * 30); // give this up to 30 minutes, though 3 seocnds to 20 seconds is all one should need.
                                if (!$json_data && $api_url == 'https://api.podopshost.com/') { // Lets force cURL and see if that helps...
                                    $json_data = empowercast_remote_fopen($req_url, $Settings['podops_auth'], $post_vars, 60 * 30, false, true);
                                }
                                if ($json_data != false)
                                    break;
                            }

                            $results = empowercast_json_decode($json_data);
                        }

                        if (is_array($results) && !isset($results['error'])) {
                            $EnclosureURL = $results['media_url'];
                            if (strtolower(substr($results['media_url'], 0, 4)) != 'http') {
                                $error = __('Podops Hosting Error (publish): Please re-upload media file and re-publish post', 'empowercast');
                                empowercast_add_error($error);
                            }
                            unset($EpisodeData['hosting']); // we need to remove the flag since we're now using the correct FULL url

                            // save podcast id
                            if (!empty($results['podcast_id'])) {
                                $EpisodeData['podcast_id'] = $results['podcast_id'];
                            }

                            // update alternate enclosures
                            if (!empty($results['alternate_enclosures']))
                            {
                                foreach ($EpisodeData['alternate_enclosure'] as $idx => $alternate_enclosure) {
                                    if (array_key_exists($alternate_enclosure['url'], $results['alternate_enclosures'])) {
                                        $EpisodeData['alternate_enclosure'][$idx]['url'] = $results['alternate_enclosures'][$alternate_enclosure['url']];
                                    }
                                }
                            }
                            $EnclosureData = $EnclosureURL . "\n" . $EnclosureSize . "\n" . $EnclosureType . "\n" . serialize($EpisodeData);
                            update_post_meta($post_ID, $field, $EnclosureData);
                        } else if (isset($results['error'])) {
                            $error = __('Podops Hosting Error (publish)', 'empowercast') . ': ' . $results['error'];
                            empowercast_add_error($error);
                        } else {
                            $rand_id = rand(100, 2000);
                            $error = __('Podops Hosting Error (publish)', 'empowercast') . ': ' . sprintf(__('An error occurred publishing media \'%s\'.', 'empowercast'), $EnclosureURL);
                            $error .= ' <a href="#" onclick="document.getElementById(\'empowercast_error_' . $rand_id . '\').style.display=\'block\';this.style.display=\'none\';return false;">' . __('Display Error', 'empowercast') . '</a>';
                            $error .= '<div id="empowercast_error_' . $rand_id . '" style="display: none;">';
                            if (!empty($json_data))
                                $error .= $json_data;
                            else if (!empty($GLOBALS['g_empowercast_remote_error']))
                                $error .= htmlspecialchars($GLOBALS['g_empowercast_remote_error']);
                            else
                                $error .= __('Unknown error occurred.', 'empowercast');
                            $error .= '</div>';
                            empowercast_add_error($error);
                        }
                    }
                }

                // find podcast_id and start query string for either API call (transcripts or chapters)
                $podcastId = false;
                if (!empty($results['podcast_id'])) {
                    $EpisodeData['podcast_id'] = $results['podcast_id'];
                    $podcastId = $results['podcast_id'];
                } else {
                    // try using one saved in the postmeta
                    $postmeta = get_post_meta($post_ID, $field, true);
                    if (!empty($postmeta['podcast_id'])) {
                        $podcastId = $postmeta['podcast_id'];
                    }
                }

                if ($podcastId) {
                    $podcastSearchAnd = "&podcast_id=" . intval($podcastId);
                } else {
                    // pass in a media_url if we don't have a podcast id
                    $podcastSearchAnd = "&media_url=" . urlencode($EnclosureURL);
                }

                // if media is hosted by podops, we need to send the url for the corresponsing player in order to clear the cache
                $podops_hosted_media = true;
                if (strpos($EnclosureURL, 'http://') !== false || strpos($EnclosureURL, 'https://') !== false) {
                    $podops_hosted_media = false;
                    if (strpos($EnclosureURL, 'content.podopshost.com') !== false) {
                        $podops_hosted_media = true;
                    }
                    if (strpos($EnclosureURL, 'ins.podopshost.com') !== false) {
                        $podops_hosted_media = true;
                    }
                    if (strpos($EnclosureURL, 'mc.podopshost.com') !== false) {
                        $podops_hosted_media = true;
                    }
                }
                if (!empty($Settings['podops_hosting']) && $podops_hosted_media) {
                    $EnclosureUrLArray = explode('/', $EnclosureURL);
                    $EnclosureURLQS = end($EnclosureUrLArray);
                    $EnclosureUrlQSArray = explode('?', $EnclosureURLQS);
                    $EnclosureFilename = $EnclosureUrlQSArray[0];
                    if (!empty($EpisodeData['podcast_id'])) {
                        $purgeUrl = 'https://player.podopshost.com/?podcast_id=' . intval($EpisodeData['podcast_id']);
                        $purgeUrl .= '&media_url=' . urlencode($EnclosureURL);
                        if (!empty($Settings['player']) && $Settings['player'] == 'podopsmodern') {
                            $purgeUrl .= '&modern=1';
                        }
                    } else {
                        $purgeUrl = 'https://player.podopshost.com/?media_url=' . urlencode($EnclosureURL);
                        if (!empty($Settings['player']) && $Settings['player'] == 'podopsmodern') {
                            $purgeUrl .= '&modern=1';
                        }
                        if (!empty($EpisodeData['id'])) {
                            // Get permalink URL
                            $permalink = get_permalink($EpisodeData['id']);
                            if (!empty($permalink))
                                $purgeUrl .= '&podcast_link=' . urlencode($permalink);
                        }
                        if (!empty($EpisodeData['itunes_image'])) {
                            if (isset($Settings['bp_episode_image']) && $Settings['bp_episode_image'] != false)
                                $purgeUrl .= '&artwork_url=' . urlencode($EpisodeData['itunes_image']);
                        }
                    }
                    $podcastSearchAnd .= "&purge_url=" . urlencode($purgeUrl);
                }

                // new transcript call here
                // only for hosting customers and Podops hosted media
                // transcript url is already saved; hosting call will replace it with a hosted version
                if (!empty($_POST['Empowercast'][$feed_slug]['transcript']['edit'])) {
                    if (!empty($Settings['podops_hosting']) && $podops_hosted_media) {
                        if ((!empty($_POST['Empowercast'][$feed_slug]['transcript']['generate']) || (!empty($_POST['Empowercast'][$feed_slug]['transcript']['upload']) && !empty($_POST['Empowercast'][$feed_slug]['pci_transcript_url'])))) {
                            if (!empty($_POST['Empowercast'][$feed_slug]['pci_transcript_url']) && !empty($_POST['Empowercast'][$feed_slug]['transcript']['upload']))
                                $podcastSearchAnd .= '&transcript_url=' . urlencode($_POST['Empowercast'][$feed_slug]['pci_transcript_url']);

                            if (!empty($_POST['Empowercast'][$feed_slug]['pci_transcript_language'])) {
                                $podcastSearchAnd .= '&language=' . $_POST['Empowercast'][$feed_slug]['pci_transcript_language'];
                            }
                            if ($creds) {
                                $accessToken = empowercast_getAccessToken();
                                $req_url = sprintf('/2/media/%s/%s?format=json&transcript=true%s&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword), urlencode($EnclosureFilename), $podcastSearchAnd);
                                $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                                $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                                $transcriptResults = $auth->api($accessToken, $req_url, false, false, 60 * 30);

                            } else {
                                foreach ($api_url_array as $index => $api_url) {
                                    $req_url = sprintf('/media/%s/%s?format=json&transcript=true%s&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword), urlencode($EnclosureFilename), $podcastSearchAnd);
                                    $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
                                    $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                                    $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $Settings['podops_auth'], array(), 60 * 30); // give this up to 30 minutes, though 3 seocnds to 20 seconds is all one should need.
                                    if (!$json_data && $api_url == 'https://api.podopshost.com/') { // Let's force cURL and see if that helps...
                                        $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $Settings['podops_auth'], array(), 60 * 30, false, true);
                                    }
                                    if ($json_data != false)
                                        break;
                                }
                                $transcriptResults = empowercast_json_decode($json_data);
                            }

                            // Take the temp transcription file and add to the feed
                            if (!empty($transcriptResults['temp_transcription_file'])) {
                                $EpisodeData["pci_transcript_url"] = $transcriptResults['temp_transcription_file'];
                                $EpisodeData["pci_transcript"] = 1;
                                $EnclosureData = $EnclosureURL . "\n" . $EnclosureSize . "\n" . $EnclosureType . "\n" . serialize($EpisodeData);
                                update_post_meta($post_ID, $field, $EnclosureData);
                            }

                            // Check for transcription error
                            if (!empty($transcriptResults['insufficient_transcription_storage'])) {
                                $error = 'Your episode was published without a transcript because you have reached your transcription limit. 
                                Limits are calculated based on transcripts generated for your total media published/replaced for the month.';

                                // Once free trial is over, we will add:
                                // To increase your transcription limit, upgrade to a hosting plan with more storage. <a href="">Change Plan</a>

                                $error = __($error, 'empowercast');
                                empowercast_add_error($error);
                            }

                            if (isset($transcriptResults['error'])) {
                                empowercast_add_error(__('Error generating transcript: ', 'empowercast') . $transcriptResults['error']);
                            } else if (empty($transcriptResults) || !is_array($transcriptResults) || empty($transcriptResults['temp_transcription_file'])) {
                                empowercast_add_error(__('Error generating transcript', 'empowercast'));
                            }

                            // check for notices from api call
                            if (!empty($transcriptResults['message'])) {
                                empowercast_add_error($transcriptResults['message']);
                            }

                        }
                    }
                }
                if (!empty($_POST['Empowercast'][$feed_slug]['chapters']['edit'])) {
                    if (!empty($Settings['podops_hosting']) && $podops_hosted_media && !empty($chapter_url)) {
                        $podcastSearchAnd .= "&chapters_url=" . urlencode($chapter_url);
                        if ($creds) {
                            $accessToken = empowercast_getAccessToken();
                            $req_url = sprintf('/2/media/%s/%s?format=json&chapters=true%s&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword), urlencode($EnclosureFilename), $podcastSearchAnd);
                            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                            $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                            $chaptersResults = $auth->api($accessToken, $req_url, false, false, 60 * 30);

                        } else {
                            foreach ($api_url_array as $index => $api_url) {
                                $req_url = sprintf('/media/%s/%s?format=json&transcript=true%s&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword), urlencode($EnclosureFilename), $podcastSearchAnd);
                                $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
                                $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                                $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $Settings['podops_auth'], array(), 60 * 30); // give this up to 30 minutes, though 3 seocnds to 20 seconds is all one should need.
                                if (!$json_data && $api_url == 'https://api.podopshost.com/') { // Let's force cURL and see if that helps...
                                    $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $Settings['podops_auth'], array(), 60 * 30, false, true);
                                }
                                if ($json_data != false)
                                    break;
                            }
                            $chaptersResults = empowercast_json_decode($json_data);
                        }

                        // Take the new chapters url and add to the feed
                        if (!empty($chaptersResults['chapters_url'])) {
                            $EpisodeData["pci_chapters_url"] = $chaptersResults['chapters_url'];
                            // may as well try to detect podcast_id here too
                            if (empty($EpisodeData['podcast_id']) && !empty($chaptersResults['podcast_id'])) {
                                $EpisodeData['podcast_id'] = $chaptersResults;
                            }
                            $EnclosureData = $EnclosureURL . "\n" . $EnclosureSize . "\n" . $EnclosureType . "\n" . serialize($EpisodeData);
                            update_post_meta($post_ID, $field, $EnclosureData);
                        }

                        // check for notices from api call
                        if (!empty($chaptersResults['message'])) {
                            empowercast_add_error($chaptersResults['message']);
                        }
                        if (!empty($chaptersResults['error'])) {
                            empowercast_add_error($chaptersResults['error']);
                        }
                    }
                }
            }
			if (empty($program_keyword)) {
                $program_keyword = (!empty($EpisodeData['program_keyword']) ? $EpisodeData['program_keyword'] : $Settings['podops_program_keyword'] );
            }
            // Push the title to Podops
            $post_array = array('title' => $post_title, 'media_url' => $EnclosureURL);
            if (!empty($podcastId)) {
                $post_array = array('title' => $post_title, 'podcast_id' => $podcastId, 'media_url' => $EnclosureURL);
            }
            $post_array['podcast_post_date'] = $post_time;
            $post_array['episode_art'] = $episode_art;
            if ($creds) {
                $accessToken = empowercast_getAccessToken();
                $req_url = sprintf('/2/episode/%s/update-title/?format=json&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword));
                $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                $title_results = $auth->api($accessToken, $req_url, $post_array, false, 60 * 30);

            } else {
                foreach ($api_url_array as $index => $api_url) {
                    $req_url = sprintf('/episode/%s/update-title/?format=json&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword));
                    $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
                    $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                    $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $Settings['podops_auth'], $post_array, 60 * 30); // give this up to 30 minutes, though 3 seocnds to 20 seconds is all one should need.
                    if (!$json_data && $api_url == 'https://api.podopshost.com/') { // Let's force cURL and see if that helps...
                        $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $Settings['podops_auth'], $post_array, 60 * 30, false, true);
                    }
                    if ($json_data != false)
                        break;
                }
                $title_results = empowercast_json_decode($json_data);
            }
		}
	}
}

function empowercast_json_decode($value)
{
	return json_decode($value, true);
}

// Import podpress settings
function empowercast_admin_import_podpress_settings()
{
	// First pull in the Podpress settings
	$PodpressData = get_option('podPress_config');
	if( !$PodpressData )
		return false;
	
	$General = get_option('empowercast_general');
	if( !$General)
		$General = array();
	$General['process_podpress'] = 1;
	$General['display_player'] = 1;
	$General['player_function'] = 1;
	$General['podcast_link'] = 1;
	// Lets try to copy settings from podpress
	$General['default_url'] = $PodpressData['mediaWebPath'];
	if( substr($General['default_url'], 0, -1) != '/' )
		$General['default_url'] .= '/'; // Add the trailing slash, donno it's not there...
	
	// Insert the podops redirect
	if( isset($PodpressData['statBluBrryProgramKeyword']) && strlen($PodpressData['statBluBrryProgramKeyword']) > 2 )
	{
		$General['redirect1'] = 'https://media.podopshost.com/'.$PodpressData['statBluBrryProgramKeyword'].'/';
	}
	
	// Insert the Podtrac redirect
	if( $PodpressData['enable3rdPartyStats'] == 'PodTrac' )
	{
		if( $General['redirect1'] )
			$General['redirect2'] = 'http://www.podtrac.com/pts/redirect.mp3/';
		else
			$General['redirect1'] = 'http://www.podtrac.com/pts/redirect.mp3/';
	}
	
	if( $PodpressData['contentDownload'] == 'enabled' )
		$General['podcast_link'] = 1;
	else
		$General['podcast_link'] = 0;
	
	if( $PodpressData['contentPlayer'] == 'both' )
		$General['player_function'] = 1;
	else if( $PodpressData['contentPlayer'] == 'inline' )
		$General['player_function'] = 2;
	else if( $PodpressData['contentPlayer'] == 'popup' )
		$General['player_function'] = 3;
	else
		$General['player_function'] = 0;
		
	if( $PodpressData['contentPlayer'] == 'start' )
		$General['display_player'] = 2;
	else
		$General['display_player'] = 1;
	
	// save these imported general settings
	empowercast_save_settings($General, 'empowercast_general');

	$FeedSettings = get_option('empowercast_feed');
	
	if( !$FeedSettings ) // If no feed settings, lets set defaults or copy from podpress.
		$FeedSettings = array();
		
	$FeedSettings['apply_to'] = 1; // Default, apply to all the rss2 feeds
	
	$FeedSettings['itunes_image'] = $PodpressData['iTunes']['image'];
	if( strstr($FeedSettings['itunes_image'], 'powered_by_podpress') )
		$FeedSettings['itunes_image'] = ''; // We're not using podpress anymore
	
	$FeedSettings['itunes_summary'] = $PodpressData['iTunes']['summary'];
	$FeedSettings['itunes_talent_name'] = $PodpressData['iTunes']['author'];
	$FeedSettings['itunes_subtitle'] = $PodpressData['iTunes']['subtitle'];
	$FeedSettings['copyright'] = $PodpressData['rss_copyright'];

    // Categories are tricky...
	$iTunesCategories = empowercast_itunes_categories(true);
	for( $x = 0; $x < 3; $x++ )
	{	
		if( isset($PodpressData['iTunes']['category'][$x]) )
		{
			$CatDesc = str_replace(':', ' > ', $PodpressData['iTunes']['category'][$x]);
			$CatKey = array_search($CatDesc, $iTunesCategories);
			if( $CatKey )
				$FeedSettings['itunes_cat_'.($x+1)] = $CatKey;
		}
	}
	
	if( $PodpressData['iTunes']['explicit'] == 'No' )
		$FeedSettings['itunes_explicit'] = 0;
	else if( $PodpressData['iTunes']['explicit'] == 'Yes' )
		$FeedSettings['itunes_explicit'] = 1;
	else if( $PodpressData['iTunes']['explicit'] == 'Clean' )
		$FeedSettings['itunes_explicit'] = 2;
		
	if( !empty($PodpressData['iTunes']['FeedID']) )
		$FeedSettings['itunes_url'] = 'http://phobos.apple.com/WebObjects/MZStore.woa/wa/viewPodcast?id='. $PodpressData['iTunes']['FeedID'];

	// Lastly, lets try to get the RSS image from the database
	$RSSImage = get_option('rss_image');
	if( $RSSImage )
		$FeedSettings['itunes_image'] = $RSSImage;
	if( strstr($FeedSettings['itunes_image'], 'powered_by_podpress') )
		$FeedSettings['itunes_image'] = ''; // We're not using podpress anymore
	$AdminEmail = get_option('admin_email');
	if( $AdminEmail )
		$FeedSettings['email'] = $AdminEmail;
		
	// save these imported feed settings
	empowercast_save_settings($FeedSettings, 'empowercast_feed');
	return true;
}

// Import plugin Podcasting settings
function empowercast_admin_import_podcasting_settings()
{
	$Changes = false;
	
	$General = get_option('empowercast_general');
	if( !$General)
	{
		$General = array();
		$Changes = true;
		$General['process_podpress'] = 0;
		$General['display_player'] = 1;
		$General['player_function'] = 1;
		$General['podcast_link'] = 1;
	}

	$pod_player_location = get_option('pod_player_location');
	if( $pod_player_location == 'top' ) // display player below posts is default in EmpowerCast
	{
		$General['display_player'] = 2; // display above posts
		$Changes = true;
	}
	
	$pod_audio_width = get_option('pod_audio_width');
	if( is_int( (int)$pod_audio_width) && $pod_audio_width > 100 ) // audio player width
	{
		$General['player_width_audio'] = $pod_audio_width;
		$Changes = true;
	}
	
	$pod_player_width = get_option('pod_player_width');
	if( is_int( (int)$pod_player_width) && $pod_player_width > 100 ) // video player width
	{
		$General['player_width'] = $pod_player_width;
		$Changes = true;
	}
	
	$pod_player_height = get_option('pod_player_height');
	if( is_int( (int)$pod_player_height) && $pod_player_height > 100 ) // video player width
	{
		$General['player_height'] = $pod_player_height;
		$Changes = true;
	}
	
	if( $Changes == true )
	{
		// save these imported general settings
		empowercast_save_settings($General, 'empowercast_general');
	}
	
	$FeedChanges = false;
	// Feed settings:
	$FeedSettings = get_option('empowercast_feed');
	
	if( !$FeedSettings ) // If no feed settings, lets set defaults or copy from podpress.
	{
		$FeedSettings = array();
		$FeedChanges = true;
	}
	
	$pod_itunes_summary = get_option('pod_itunes_summary');
	if( $pod_itunes_summary )
	{
		$FeedSettings['itunes_summary'] = stripslashes($pod_itunes_summary);
		$FeedChanges = true;
	}
	
	$pod_itunes_image = get_option('pod_itunes_image');
	if( $pod_itunes_image ) 
	{
		$FeedSettings['itunes_image'] = $pod_itunes_image;
		$FeedChanges = true;
	}
	
	$iTunesCategories = empowercast_itunes_categories(true);
	for( $x = 1; $x <= 3; $x++ )
	{
		$pod_itunes_cat = get_option('pod_itunes_cat'.$x);
		$find = str_replace('&amp;', '&', $pod_itunes_cat);
		$CatDesc = str_replace('||', ' > ', $find);
		$CatKey = array_search($CatDesc, $iTunesCategories);
		if( $CatKey )
		{
			$FeedSettings['itunes_cat_'.$x] = $CatKey;
			$FeedChanges = true;
		}
	}
	
	$pod_itunes_ownername = get_option('pod_itunes_ownername');
	if( $pod_itunes_ownername ) 
	{
		$FeedSettings['itunes_talent_name'] = stripslashes($pod_itunes_ownername);
		$FeedChanges = true;
	}
	
	$pod_itunes_owneremail = get_option('pod_itunes_owneremail');
	if( $pod_itunes_owneremail ) 
	{
		$FeedSettings['email'] = $pod_itunes_owneremail;
		$FeedChanges = true;
	}
	
	$rss_language = get_option('rss_language');
	if( $rss_language ) 
	{
		$FeedSettings['rss_language'] = $rss_language;
		$FeedChanges = true;
	}
	
	$pod_tagline = get_option('pod_tagline');
	if( $pod_tagline ) 
	{
		$FeedSettings['itunes_subtitle'] = stripslashes($pod_tagline);
		$FeedChanges = true;
	}
	
	$pod_itunes_explicit = get_option('pod_itunes_explicit');
	if( $pod_itunes_explicit == 'yes'  ) 
	{
		$FeedSettings['itunes_explicit'] = 1;
		$FeedChanges = true;
	}
	else if( $pod_itunes_explicit == 'clean'  ) 
	{
		$FeedSettings['itunes_explicit'] = 2;
		$FeedChanges = true;
	}
	
	if( $FeedChanges )
	{
		// save these imported feed settings
		empowercast_save_settings($FeedSettings, 'empowercast_feed');
	}
	
	return ($Changes||$FeedChanges);
}

function empowercast_admin_episodes_not_hosted() {
    // no need to specify feed slug--this is for use during onboarding, before additional feeds are set up
    $field = 'enclosure';
    global $wpdb;
    if ($results = $wpdb->get_results("SELECT COUNT(pm.post_id) AS episodes_total FROM $wpdb->posts AS p INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID WHERE pm.meta_key = '$field' AND p.post_status <> 'auto-draft' AND p.post_status <> 'trash' AND p.post_status <> 'inherit' AND pm.meta_value NOT LIKE '%content.podopshost.com%' ", ARRAY_A)) {
        if (count($results)) {
            foreach ($results as $key => $row) {
                if ($row['episodes_total'])
                    return $row['episodes_total'];
            }
        }
    }
    return 0;
}

function empowercast_admin_episodes_per_feed($feed_slug, $post_type='post')
{
	$field = 'enclosure';
	if( $feed_slug != 'podcast' )
		$field = '_'. $feed_slug .':enclosure';
	global $wpdb;
	if ( $results = $wpdb->get_results("SELECT COUNT(pm.post_id) AS episodes_total FROM $wpdb->posts AS p INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID WHERE pm.meta_key = '$field' AND p.post_status <> 'auto-draft' AND p.post_status <> 'trash' AND p.post_status <> 'inherit' ", ARRAY_A) ) {
        if( count($results) )
		{
			foreach( $results as $key => $row ) {
				break;
			}
			if( $row['episodes_total'] )
				return $row['episodes_total'];
		}
	}
	return 0;
}

// Set the default settings basedon the section user is in.
function empowercast_default_settings($Settings, $Section='basic')
{
	// Set the default settings if the setting does not exist...
	switch($Section)
	{
		case 'basic': {
			// Nothing needs to be pre-set in the basic settings area
			
			if( !isset($Settings['player_options'] ) )
			{
				$Settings['player_options'] = 0;
				if( isset($Settings['player']) && $Settings['player'] != '' && $Settings['player'] != 'default' )
					$Settings['player_options'] = 1;
			}
			
			if( !isset($Settings['cat_casting'] ) )
			{
				$Settings['cat_casting'] = 0;
				//if( isset($Settings['custom_cat_feeds']) && count($Settings['custom_cat_feeds']) > 0 )
				//	$Settings['cat_casting'] = 1;
			}
			
			if( !isset($Settings['channels'] ) )
				$Settings['channels'] = 0;
			if( isset($Settings['custom_feeds']) && count($Settings['custom_feeds']) > 0 ) // They can't delete this until they remove all the channels
				$Settings['channels'] = 1;
					
		}; break;
		case 'editfeed': {
			if( !isset($Settings['apply_to']) )
				$Settings['apply_to'] = 1; // Make sure settings are applied to all feeds by default
			//if( !isset($Settings['enhance_itunes_summary']) )
			//	$Settings['enhance_itunes_summary'] = 1;
		}; // Let this fall through to the custom feed settings
		case 'editfeed_custom': {
			if( !isset($Settings['enhance_itunes_summary']) )
				$Settings['enhance_itunes_summary'] = 0;
		}; break;
		case 'appearance': {
			if( !isset($Settings['display_player']) )
				$Settings['display_player'] = 1;
			if( !isset($Settings['player_function']) )
				$Settings['player_function'] = 1;
			if( !isset($Settings['podcast_link']) )
				$Settings['podcast_link'] = 1;
			if( !isset($Settings['display_player_excerpt']) )
					$Settings['display_player_excerpt'] = 0;
			//if( !isset($Settings['display_player_disable_mobile']) )
			//		$Settings['display_player_disable_mobile'] = 0;
			
			// Play in page obsolete, switching here:
			if( $Settings['player_function'] == 5 )
				$Settings['player_function'] = 1;
			else if( $Settings['player_function'] == 4 )
				$Settings['player_function'] = 2;
			if(!isset($Settings['subscribe_widget_shape'])) {
                $Settings['subscribe_widget_shape'] = 'squared';
            }
			if(!isset($Settings['subscribe_no_important_styling'])) {
			    $Settings['subscribe_no_important_styling'] = 'include';
            }
		}; break;
	}
	
	return $Settings;
}

function empowercast_write_tags($file, $post_title, $program_keyword = false)
{
	// Use the Podops API to write ID3 tags. to the media...
	
	$Settings = get_option('empowercast_general');
    $creds = get_option('empowercast_creds');
    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
    $auth = new EmpowerCastAuth();
	if( empty($program_keyword) && !empty($Settings['podops_program_keyword']) ) {
		$program_keyword = $Settings['podops_program_keyword'];
	}

	$PostArgs = array();
	$Fields = array('title','artist','album','genre','year','track','composer','copyright','url');
	foreach( $Fields as $null => $field )
	{
		if( !empty($Settings[ 'tag_'.$field ]) )
		{
			if( $field == 'track' )
			{
				$TrackNumber = get_option('empowercast_track_number');
				if( empty($TrackNumber) )
					$TrackNumber = 1;
				$PostArgs[ $field ] = $TrackNumber;
				update_option('empowercast_track_number', ($TrackNumber+1) );
			}
			else
			{
				$PostArgs[ $field ] = $Settings[ 'tag_'.$field ];
			}
		}
		else
		{
			switch($field)
			{
				case 'title': {
					$PostArgs['title'] = $post_title;
				}; break;
				case 'album': {
					$PostArgs['album'] = get_bloginfo('name');
				}; break;
				case 'genre': {
					$PostArgs['genre'] = 'Podcast';
				}; break;
				case 'year': {
					$PostArgs['year'] = date('Y');
				}; break;
				case 'artist':
				case 'composer': {
					if( !empty($Settings['itunes_talent_name']) )
						$PostArgs[ $field ] = $Settings['itunes_talent_name'];
				}; break;
				case 'copyright': {
					if( !empty($Settings['itunes_talent_name']) )
						$PostArgs['copyright'] = '(c) '.$Settings['itunes_talent_name'];
				}; break;
				case 'url': {
					$PostArgs['url'] = get_bloginfo('url');
				}; break;
			}
		}
	}
							
	// Get meta info via API
    $Results = false;
	$content = false;
	$api_url_array = empowercast_get_api_array();
    if ($creds) {
        $accessToken = empowercast_getAccessToken();
        $req_url = sprintf('/2/media/%s/%s?format=json&id3=true&cache=' . md5( rand(0, 999) . time() ) , urlencode($program_keyword), urlencode($file));
        $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA')?'?'. EMPOWERCAST_PODOPS_API_QSA:'');
        $Results = $auth->api($accessToken, $req_url, $PostArgs);
        //$Results['error'] = print_r($Results, true);
    } else {
        foreach ($api_url_array as $index => $api_url) {
            $req_url = sprintf('%s/media/%s/%s?format=json&id3=true&cache=' . md5( rand(0, 999) . time() ), rtrim($api_url, '/'), urlencode($program_keyword), urlencode($file));
            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
            $content = empowercast_remote_fopen($req_url, $Settings['podops_auth'], $PostArgs);
            if (!$content && $api_url == 'https://api.podopshost.com/') { // Lets force cURL and see if that helps...
                $content = empowercast_remote_fopen($req_url, $Settings['podops_auth'], $PostArgs, 15, false, true);
            }
            if ($content != false)
                break;
        }

        if ($content) {
            $Results = empowercast_json_decode($content);
        }
    }
    if ($Results && is_array($Results))
        return $Results;
	
	return array('error'=>__('Error occurred writing MP3 ID3 Tags.', 'empowercast') );
}

function empowercast_get_media_info($file, $program_Keyword = false)
{
    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
    $auth = new EmpowerCastAuth();
	$Settings = get_option('empowercast_general');
    $creds = get_option('empowercast_creds');
	if( empty($program_Keyword) && !empty($Settings['podops_program_keyword']) ) {
		$program_Keyword = $Settings['podops_program_keyword'];
	}
    // for media creek urls, just verify the content.podopshost.com version
	if (strpos($file, 'mc.podopshost.com') !== false) {
        //remove query string
        $file_array = explode('?', $file);
        $file = $file_array[0];
        // replace media host
        $file = str_replace('mc.podopshost.com', 'content.podopshost.com', $file);
    }
	$content = false;
    $Results = array();
    $full_url = true;
    $api_url_array = empowercast_get_api_array();
    if (substr($file, 0, 4) != 'http') {
        // filename only, remove query string
        $file_array = explode('?', $file);
        $file = $file_array[0];
        $full_url = false;
    }
    if ($creds) {
        $accessToken = empowercast_getAccessToken();
        $req_url = sprintf('/2/media/%s/%s?format=json&info=true', urlencode($program_Keyword), urlencode($file));
        $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA')?'?'. EMPOWERCAST_PODOPS_API_QSA:'');
        $Results = $auth->api($accessToken, $req_url, false);
    } else {
        foreach ($api_url_array as $index => $api_url) {
            $req_url = sprintf('%s/media/%s/%s?format=json&info=true', rtrim($api_url, '/'), urlencode($program_Keyword), urlencode($file));
            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
            $content = empowercast_remote_fopen($req_url, $Settings['podops_auth']);
            if (!$content && $api_url == 'https://api.podopshost.com/') { // Lets force cURL and see if that helps...
                $content = empowercast_remote_fopen($req_url, $Settings['podops_auth'], array(), 15, false, true);
            }

            if ($content != false)
                break;
        }

        if ($content) {
            $Results = empowercast_json_decode($content);
        }
    }

    if ($Results && is_array($Results) && empty($Results['error']))
        return $Results;

	return array('error'=>__('Error occurred obtaining media information.', 'empowercast') );
}

// Call this function when there is no enclosure currently detected for the post but users set the option to auto-add first media file linked within post option is checked.
function empowercast_do_enclose( $content, $post_ID, $use_last_media_link = false )
{
	$ltrs = '\w';
	$gunk = '/#~:.?+=&%@!\-';
	$punc = '.:?\-';
	$any = $ltrs . $gunk . $punc;

	preg_match_all( "{\b http : [$any] +? (?= [$punc] * [^$any] | $)}x", $content, $post_links_temp );
	
	if( $use_last_media_link )
		$post_links_temp[0] = array_reverse($post_links_temp[0]);
	
	$enclosure = false;
	foreach ( (array) $post_links_temp[0] as $link_test ) {
		$test = parse_url( $link_test );
		// Wordpress also acecpts query strings, which doesn't matter to us what's more important is taht the request ends with a file extension.
		// get the file extension at the end of the request:
		if( preg_match('/\.([a-z0-9]{2,7})$/i', $link_test, $matches) )
		{
			// see if the file extension is one of the supported media types...
			$content_type = empowercast_get_contenttype('test.'.$matches[1], false); // we want to strictly use the content types known for media, so pass false for second argument
			if( $content_type )
			{
				$enclosure = $link_test;
				$MediaInfo = empowercast_get_media_info_local($link_test, $content_type);
				if( !isset($MediaInfo['error']) && !empty($MediaInfo['length']) )
				{
					// Insert enclosure here:
					$EnclosureData = $link_test . "\n" . $MediaInfo['length'] . "\n". $content_type;
					if( !empty($MediaInfo['duration']) )
						$EnclosureData .= "\n".serialize( array('duration'=>$MediaInfo['duration']) );
					add_post_meta($post_ID, 'enclosure', $EnclosureData, true);
					break; // We don't wnat to insert anymore enclosures, this was it!
				}
			}
		}
	}
}

function empowercast_get_episode_count($feed_slug, $post_type = 'post')
{
	global $wpdb;
	$custom_field = 'enclosure';
	if( $feed_slug != 'podcast' )
		$custom_field = '_'. $feed_slug .':enclosure';
		
	$query = "SELECT COUNT( * ) AS num_posts FROM {$wpdb->posts} ";
	$query .= "INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ";
	$query .= "WHERE {$wpdb->postmeta}.meta_key = '". $custom_field ."' AND post_type = %s AND post_status = 'publish' ";
	
	$results = $wpdb->get_results( $wpdb->prepare( $query, $post_type ), ARRAY_A );
	if( !empty($results[0]['num_posts']) )
	{
		return $results[0]['num_posts'];
	}
	return 0;
}

function empowercast_get_media_info_local($media_file, $content_type='', $file_size=0, $duration='', $return_warnings=false)
{
	$error_msg = '';
	$warning_msg = '';
	if( $content_type == '' )
		$content_type = empowercast_get_contenttype($media_file);
		
	if( isset($GLOBALS['objWPOSFLV']) && is_object($GLOBALS['objWPOSFLV']) )
		return array('error'=>__('The WP OS FLV plugin is not compatible with Podops EmpowerCast.', 'empowercast') );

	$get_duration_info = ( ($content_type == 'audio/mpeg' || $content_type == 'audio/x-m4a' || $content_type == 'video/x-m4v' || $content_type == 'video/mp4' || $content_type == 'audio/ogg' ) && $duration === '');
	// Lets use the mp3info class:
	require_once( EMPOWERCAST_ABSPATH .'/mp3info.class.php');
	$Mp3Info = new Mp3Info();

	if( $get_duration_info )
	{
		if( preg_match('/video/i', $content_type) )
		{
			if( defined('EMPOWERCAST_DOWNLOAD_BYTE_LIMIT_VIDEO') )
				$Mp3Info->SetDownloadBytesLimit(EMPOWERCAST_DOWNLOAD_BYTE_LIMIT_VIDEO);
		}
		else
		{
			if( defined('EMPOWERCAST_DOWNLOAD_BYTE_LIMIT') )
				$Mp3Info->SetDownloadBytesLimit(EMPOWERCAST_DOWNLOAD_BYTE_LIMIT);
		}
	}

	$Mp3Data = $Mp3Info->GetMp3Info($media_file, !$get_duration_info);
	if( $Mp3Data )
	{
		if( $Mp3Info->GetRedirectCount() > 5 )
		{
			// Add a warning that the redirect count exceeded 5, which may prevent some podcatchers from downloading the media.
			$warning = sprintf( __('Warning, the Media URL %s contains %d redirects.', 'empowercast'), $media_file, $Mp3Info->GetRedirectCount() );
			$warning .=	' [<a href="https://podopshost.com/support/empowercast-documentation/warning-messages-explained/" title="'. __('EmpowerCast Warnings Explained', 'empowercast') .'" target="_blank">'. __('EmpowerCast Warnings Explained') .'</a>]';
			if( $return_warnings )
				$warning_msg .= $warning;
			else
				empowercast_add_error( $warning );
		}

		if( $file_size == 0 )
			$file_size = $Mp3Info->GetContentLength();

		if( $get_duration_info )
		{
			$playtime_string = ( !empty($Mp3Data['playtime_string']) ? $Mp3Data['playtime_string'] : '');
			$duration = empowercast_readable_duration($playtime_string, true); // Fix so it looks better when viewed for editing
		}

		$GeneralSettings = get_option('empowercast_general');
		if( empty($GeneralSettings['hide_warnings']) && count( $Mp3Info->GetWarnings() ) > 0 )
		{
			$Warnings = $Mp3Info->GetWarnings();
			foreach( $Warnings as $null => $warning )
			{
				$warning = sprintf( __('Warning, Media URL %s', 'empowercast'), $media_file) .': '. $warning  .' [<a href="https://podopshost.com/support/empowercast-documentation/warning-messages-explained/" target="_blank">'. __('EmpowerCast Warnings Explained', 'empowercast') .'</a>]';
				if( $return_warnings )
					$warning_msg .= $warning;
				else
					empowercast_add_error( $warning );
			}
		}
	}
	else
	{
		if( $Mp3Info->GetError() != '' )
			return array('error'=>$Mp3Info->GetError() );
		else
			return array('error'=>__('Error occurred obtaining media information.', 'empowercast') );
	}

	if( $file_size == 0 )
		return array('error'=>__('Error occurred obtaining media file size.', 'empowercast') );

	if( $return_warnings && $warning_msg != '' )
		return array('content-type'=>$content_type, 'length'=>$file_size, 'duration'=>$duration, 'warnings'=>$warning_msg, 'enclosure_url' => $media_file);
	return array('content-type'=>$content_type, 'length'=>$file_size, 'duration'=>$duration, 'enclosure_url' => $media_file);
}

function empowercast_add_error($error)
{
	$Errors = get_option('empowercast_errors');
	if( !is_array($Errors) )
		$Errors = array();
	$Errors[] = $error;
	update_option('empowercast_errors',  $Errors);
}
	
function empowercast_print_options($options,$selected=null, $return=false)
{
	reset($options);
	if( $return )
	{
		$html = '';
		foreach( $options as $key=> $value )
		{
			$html .= '<option value="'. esc_attr($key) .'"'. ( ($selected !== null && strcmp($selected, $key) == 0 )?' selected':'') .'>';
			$html .= htmlspecialchars($value);
			$html .= "</option>\n";
		}
		
		return $html;
	}
	foreach( $options as $key=> $value )
	{
		echo '<option value="'. esc_attr($key) .'"'. ( ($selected !== null && strcmp($selected, $key) == 0 )?' selected':'') .'>';
		echo htmlspecialchars($value);
		echo "</option>\n";
	}
}

/*
Help Link
2.0 beta
*/
function empowercast_help_link($link, $title = false )
{
	if( $title == '' )
		$title = __('Learn More', 'empowercast');
	
	return ' [<a href="'. $link .'" title="'. htmlspecialchars($title) .'" target="_blank">'. htmlspecialchars($title) .'</a>] ';
}

$g_SupportUploads = null;
function empowercastadmin_support_uploads()
{
	global $g_SupportUploads;
	if( $g_SupportUploads != null )
		return $g_SupportUploads;
	
	$g_SupportUploads = false;
	$UploadArray = wp_upload_dir();
	if( false === $UploadArray['error'] )
	{
		$upload_path =  $UploadArray['basedir'].'/empowercast/';
		
		if( !file_exists($upload_path) )
			$g_SupportUploads = @wp_mkdir_p( rtrim($upload_path, '/') );
		else
			$g_SupportUploads = true;
	}	
	return $g_SupportUploads;
}

function empowercastadmin_new($span = false, $additional_style='')
{
    $style = 'color: #CC0000; font-weight: bold; background-color: white; padding: 2px; border-radius: 4px;';
	if( !empty($additional_style) )
		$style .= $additional_style;
	if ($span) {
        return '<span style="'.$style.'">'. __('New!', 'empowercast') .'</span>';
    }
	return '<sup style="'.$style.'">'. __('New!', 'empowercast') .'</sup>';
}

function empowercastadmin_updated($updated_message)
{
	return '<div style="margin: 5px;"><sup style="color: #CC0000; font-weight: bold; font-size: 85%;">'. $updated_message .'</sup></div>';
}

function empowercastadmin_notice($updated_message)
{
	return '<sup style="color: #CC0000; font-weight: bold; font-size: 105%;">'. htmlspecialchars($updated_message) .'</sup>';
}

function empowercastadmin_community_news($items=4, $pp_settings=false)
{
	require_once( EMPOWERCAST_ABSPATH. '/empowercast-player.php'); // Include, if not included already
	$rss_items = empowercast_get_news(EMPOWERCAST_FEED_NEWS, $items);
	echo '<div class="empowercast-news-dashboard">';	
	echo '<ul>';

	if ( !$rss_items )
	{
		echo '<li>'. __('Error occurred retrieving news.' , 'empowercast') .'</li>';
	}
	else
	{
		$first_item = true;
		foreach( $rss_items as $null=> $item)
		{
            preg_match_all('/<img.*?src="(.*?)"/', $item->get_content(), $matches);
			if (!isset($matches[1][0])) {
			    $img_url = empowercast_get_root_url() . 'images/pts_cover.jpg';
            } else {
                $img_url = $matches[1][0];
            }
            $enclosure = $item->get_enclosure();
			echo '<li class="pp-news-item">';
			echo "<img class='pp-news-image' src=\"$img_url\" alt=\"No image\" /><div class='pp-news-details-container'>";
			echo '<a class="rsswidget" href="'.esc_url( $item->get_permalink(), $protocolls=null, 'display' ).'" target="_blank">'. esc_html( $item->get_title() ) .'</a>';
			echo ' <div class="rss-date">'. $item->get_date('F j, Y') .'</div>';
			echo '<div class="rssSummary">'. esc_html( empowercast_feed_text_limit( strip_tags( $item->get_description() ), 150 ) );
            echo '<a href="' . esc_url($item->get_permalink(), $protocolls = null, 'display') . '" target="_blank">' . __('Read more', 'empowercast') . '</a>';
			echo '</div>';
			if( $enclosure && !empty($enclosure->link) )
			{
				$poster_image = '';
				$poster_tag = $item->get_item_tags('http://www.rawvoice.com/rawvoiceRssModule/', 'poster');
				if( $poster_tag && !empty($poster_tag[0]['attribs']['']['url']) )
					$poster_image = $item->sanitize($poster_tag[0]['attribs']['']['url'], SIMPLEPIE_CONSTRUCT_TEXT);
				
				$embed = '';
				$embed_tag = $item->get_item_tags('http://www.rawvoice.com/rawvoiceRssModule/', 'embed');
				if( $embed_tag && !empty($embed_tag[0]['data']) )
					$embed = $embed_tag[0]['data'];
				
				
				// Only show an episode with the latest item
				if( $first_item && $embed )
				{
					if( preg_match('/width="(\d{1,4})"/i', $embed, $matches ) && count($matches) > 1 )
					{
						$max_width = $matches[1];
						$embed = preg_replace('/width="/i', 'style="max-width: '.$max_width.'px;" width="', $embed );
					}
					$embed = preg_replace('/width="(\d{1,4})"/i', 'width="100%"', $embed );
					
					echo '<div class="empowercastNewsPlayer">';
					echo $embed;
					echo '</div>';
				}
				else if( $first_item )
				{
					$EpisodeData = array();
					$EpisodeData['type'] = $enclosure->type;
					$EpisodeData['duration'] = $enclosure->duration;
					$EpisodeData['poster'] = $poster_image;
					$EpisodeData['width'] = '100%';
					$EpisodeData['custom_play_button'] = empowercast_get_root_url() . 'play_audio.png';
					$ext = empowercastplayer_get_extension($enclosure->link);
					switch($ext)
					{
						case 'mp4':
						case 'm4v':
						case 'webm': {
							echo '<div class="empowercastNewsPlayer empowercastadmin-mejs-video">';
								echo empowercastplayer_build_mediaelementvideo($enclosure->link, $EpisodeData);
							echo '</div>';
						}; break;
						case 'mp3':
						case 'm4a': {
							echo '<div class="empowercastNewsPlayer">';
								echo empowercastplayer_build_mediaelementaudio($enclosure->link, $EpisodeData);
							echo '</div>';
						}; break;
					}
				}
				
					echo '<div style="clear: both;"></div>';
			}
			echo '</div>';
			echo '</li>';
			//$first_item = false;
		}
	}						

	echo '</ul>';
	echo '<br class="clear"/>';
	echo '<div style="text-align:center;"><b>';
	echo  __('MORE ARTICLES AT', 'empowercast');
	echo '&nbsp;';
	echo '<em><a href="https://podopshost.com/podcast-insider/">'. __('PODCAST INSIDER', 'empowercast') .'</a></em>';

	echo '</b></div>';
	echo '</div>';
}

function empowercastadmin_community_highlighted($items=8)
{
	require_once( EMPOWERCAST_ABSPATH. '/empowercast-player.php'); // Include, if not included already
	$rss_items = empowercast_get_news(EMPOWERCAST_FEED_HIGHLIGHTED, $items);
	echo '<div class="empowercast-highlighted-dashboard">';	
	echo '<ul>';

	if ( !$rss_items )
	{
		echo '<li>'. __('Error occurred retrieving highlighted items.' , 'empowercast') .'</li>';
	}
	else
	{
		foreach( $rss_items as $null=> $item)
		{
			echo '<li>';
			echo '<a class="rsswidget" href="'.esc_url( $item->get_permalink(), $protocolls=null, 'display' ).'" target="_blank">'. esc_html( $item->get_title() ) .'</a>';
			//echo ' <span class="rss-date">'. $item->get_date('F j, Y') .'</span>';
			echo '<div class="rssSummary">'. esc_html( empowercast_feed_text_limit( strip_tags( $item->get_description() ), 150 ) ).'</div>';
			echo '</li>';
		}
	}						

	echo '</ul>';
	echo '</div>';
}

function empowercast_admin_plugin_action_links( $links, $file )
{
	if( preg_match('/empowercast\.php$/', $file)  )
		array_push( $links, '<a href="'. admin_url("admin.php?page=empowercastadmin_basic")  .'">'. __('Settings', 'empowercast') .'</a>');
	return $links;
}
add_filter( 'plugin_action_links', 'empowercast_admin_plugin_action_links', 10, 2 );

function empowercast_plugin_row_meta( $links, $file ) {
	
	if ( strpos( $file, 'empowercast.php' ) !== false ) {
	
		$new_links = array();
		$new_links[] = empowercast_get_documentation_link();
		//$new_links[] = '<a href="https://podopshost.com/support/empowercast-documentation/empowercast-documentation/" target="_blank">' . __( 'Support', 'empowercast' ) . '</a>';
		$new_links[] = empowercast_get_review_link();
		
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}

function empowercast_admin_get_page()
{
	if( !empty($_REQUEST['page']) )
		return htmlspecialchars($_REQUEST['page']);
	return 'empowercastadmin_basic';
}

function empowercast_review_message($type=0)
{
	if( $type == 1 )
		return sprintf(__('If you appreciate EmpowerCast and the features provided, we would greatly appreciate it if you could <a href="%s" target="_blank">leave a review on WordPress.org</a>.', 'empowercast'), 'https://wordpress.org/support/plugin/empowercast/reviews/?rate=5#new-post');
	
	return sprintf(__('Fan of EmpowerCast? Please show your appreciation by <a href="%s" target="_blank">leaving a review</a>.', 'empowercast'), 'https://wordpress.org/support/plugin/empowercast/reviews/?rate=5#new-post');
}

function empowercast_get_review_link()
{
	return '<a href="https://wordpress.org/support/view/plugin-reviews/empowercast?rate=5#postform" target="_blank">' . __( 'Write a review', 'empowercast' ) . '</a>';
}

function empowercast_get_documentation_link()
{
	return '<a href="https://podopshost.com/support/empowercast-documentation/empowercast-documentation/" target="_blank">' . __( 'Documentation', 'empowercast' ) . '</a>';
}



require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-jquery.php');
// Only include the dashboard when appropriate.
require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-dashboard.php');

if( defined('WP_LOAD_IMPORTERS') ) {
	require_once( EMPOWERCAST_ABSPATH .'/empowercastadmin-rss-import.php');
}


