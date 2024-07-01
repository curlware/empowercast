<?php
	// empowercast-feed-auth.php
	
	function empowercast_feed_auth($feed_slug)
	{
		// See if a filter exists to perform the authentication...
		$authenticated = apply_filters('empowercast_feed_auth', false, 'channel', $feed_slug);
		if( !$authenticated )
		{
			$FeedSettings = get_option('empowercast_feed_'.$feed_slug);
			
			/*
			 * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
			 * For this workaround to work, add this line to your .htaccess file:
			 * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
			 */
			// Workaround for HTTP Authentication with PHP running as CGI
			if ( !isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
				$ha = base64_decode( substr($_SERVER['HTTP_AUTHORIZATION'],6) ); // Chop off 'basic ' from the beginning of the value
				if( strstr($ha, ':') ) { // Colon found, lets split it for user:password
					list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $ha);
				}
				unset($ha);
			}
			
			if( !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ) {
				empowercast_feed_auth_basic( $FeedSettings['title'] );
				exit;
			}
				
			$user = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];

			if (!is_null($user) && strlen($user) > 1) {
                if (strpos($user, '@', 1) !== false) {
                    $userObjID = get_user_by('email', $user); // Get the user by email
                    if (!is_wp_error($userObjID))
                        $user = $userObjID->user_login; // Use the user's login (not email) to authenticate
                }
            }
			
			$userObj = wp_authenticate($user, $password);
			
			if( !is_wp_error($userObj) )
			{
				// Check capability...
				if( $userObj->has_cap( $FeedSettings['premium'] ) )
					return; // Nice, let us continue...
				empowercast_feed_auth_basic( $FeedSettings['title'], __('Access Denied', 'empowercast') );
				exit;
			}
			
			// If we made it this far, then there was a wp_authenticate error...
			empowercast_feed_auth_basic( $FeedSettings['title'], $userObj );
			exit;
		}
	}
	
	function empowercast_feed_auth_basic($realm_name, $error = false )
	{
		if( !defined('EMPOWERCAST_FEED_AUTH_PRINT_WP_ERRORS') && is_wp_error($error) ) {
			$error = __('Unauthorized', 'empowercast');
		}
		
		if( empty($error) ) {
			$error = __('Unauthorized', 'empowercast');
		}
		
		$error_heading = (is_wp_error($error)? __('Unauthorized', 'empowercast') : $error);
		$error_message = (is_wp_error($error)?$error->get_error_message(): htmlspecialchars($error) );
		
		if( !$error )
			$error = __('Unauthorized', 'empowercast');
		header('HTTP/1.0 401 Unauthorized');
		header('Content-Type: text/html');
		header('WWW-Authenticate: Basic realm="'. str_replace('"', '', $realm_name).'"');
		
		echo '<!DOCTYPE html>'; // HTML5!
		echo "\n";
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="robots" content="noindex" />
	<title><?php echo htmlspecialchars($error_heading); ?></title>
</head>
<body>
	<p><?php echo $error_message; ?></p>
</body>
</html>
<?php
		exit;
	}
	
// eof