<?php
// empowercastadmin-ping-sites.php
	
function empowercastadmin_diagnostics_process(){
    global $empowercast_diags;
    $empowercast_diags = array();

    // First, see if the user has cURL and/or allow_url_fopen enabled...
    $empowercast_diags['detecting_media'] = array();
    $empowercast_diags['detecting_media']['success'] = true;
    $empowercast_diags['detecting_media']['warning'] = false;
    $empowercast_diags['detecting_media']['allow_url_fopen'] = (ini_get( 'allow_url_fopen' ) != false); // fopen
    $empowercast_diags['detecting_media']['curl'] = function_exists( 'curl_init' ); // cURL
    $empowercast_diags['detecting_media']['message2'] = ''; // if ( !ini_get('open_basedir') )
    $empowercast_diags['detecting_media']['message3'] = ''; // ssl checks

    // Testing:
    //$empowercast_diags['detecting_media']['allow_url_fopen'] = false;
    //$empowercast_diags['detecting_media']['curl'] = false;

    if($empowercast_diags['detecting_media']['curl']) {
        $empowercast_diags['detecting_media']['message'] = __('Your web server supports the PHP cURL library.', 'empowercast');
        if($empowercast_diags['detecting_media']['allow_url_fopen']){
            $empowercast_diags['detecting_media']['message'] .= ' '. __('Your web server is also configured with the php.ini setting \'allow_url_fopen\' enabled, but the cURL library takes precedence.', 'empowercast');
        }

        if(ini_get('open_basedir')){
            $empowercast_diags['detecting_media']['warning'] = true;
            $empowercast_diags['detecting_media']['message2'] = __('Warning: The php.ini setting \'open_basedir\' will prevent the cURL library from following redirects in URLs.', 'empowercast');
        }
    }
    elseif($empowercast_diags['detecting_media']['allow_url_fopen']){
        $empowercast_diags['detecting_media']['message'] = __('Your web server is configured with the php.ini setting \'allow_url_fopen\' enabled.', 'empowercast');
    } else {
        $empowercast_diags['detecting_media']['success'] = false;
        $empowercast_diags['detecting_media']['message'] = __('Your server must either have the php.ini setting \'allow_url_fopen\' enabled or have the PHP cURL library installed in order to detect media information.', 'empowercast');
    }

    // OpenSSL or curl SSL is required
    $empowercast_diags['detecting_media']['openssl'] = extension_loaded('openssl');
    $empowercast_diags['detecting_media']['curl_ssl'] = false;
    if(function_exists('curl_version')){
        $curl_info = curl_version();
        $empowercast_diags['detecting_media']['curl_ssl'] = ($curl_info['features'] & CURL_VERSION_SSL );
    }

    if($empowercast_diags['detecting_media']['openssl'] == false && $empowercast_diags['detecting_media']['curl_ssl'] == false){
        $empowercast_diags['detecting_media']['warning'] = true;
        $empowercast_diags['detecting_media']['message3'] = __('WARNING: Your server should support SSL either openssl or curl_ssl.', 'empowercast');
    }

    // testing:
    //$empowercast_diags['pinging_itunes']['openssl'] = false;
    //$empowercast_diags['pinging_itunes']['curl_ssl'] = false;

    // Third, see if the uploads/empowercast folder is writable
    $UploadArray = wp_upload_dir();
    $empowercast_diags['uploading_artwork'] = array();
    $empowercast_diags['uploading_artwork']['success'] = false;
    $empowercast_diags['uploading_artwork']['file_uploads'] = ini_get( 'file_uploads' );
    $empowercast_diags['uploading_artwork']['writable'] = false;
    $empowercast_diags['uploading_artwork']['upload_path'] = '';
    $empowercast_diags['uploading_artwork']['message'] = '';

    // Testing:
    //$UploadArray['error'] = 'WordPres broke';
    //$empowercast_diags['uploading_artwork']['file_uploads'] = false;
    //$UploadArray['error'] = true;

    if($empowercast_diags['uploading_artwork']['file_uploads'] == false){
        $empowercast_diags['uploading_artwork']['message'] = __('Your server requires the php.ini setting \'file_uploads\' enabled in order to upload podcast artwork.', 'empowercast');
    }
    elseif($UploadArray['error'] === false){
        $empowercast_diags['uploading_artwork']['upload_path'] = $UploadArray['basedir'] . '/empowercast/';

        if(!is_dir($empowercast_diags['uploading_artwork']['upload_path']) && ! wp_mkdir_p( rtrim($empowercast_diags['uploading_artwork']['upload_path'], '/'))){
            $empowercast_diags['uploading_artwork']['message'] = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?', 'empowercast'), rtrim($empowercast_diags['uploading_artwork']['upload_path'], '/') );
        } else {
            $empowercast_diags['uploading_artwork']['writable'] = empowercastadmin_diagnostics_is_writable($empowercast_diags['uploading_artwork']['upload_path']);
            if($empowercast_diags['uploading_artwork']['writable'] == false){
                $empowercast_diags['uploading_artwork']['message'] = sprintf(__('EmpowerCast is unable to write to the %s directory.', 'empowercast'), $empowercast_diags['uploading_artwork']['upload_path']);
            } else {
                $empowercast_diags['uploading_artwork']['success'] = true;
                $empowercast_diags['uploading_artwork']['message'] = __('You are able to upload and save artwork images for your podcasts.', 'empowercast');
            }
        }
    } else {
        if(strlen($UploadArray['error']) > 2){
            $empowercast_diags['uploading_artwork']['message'] = $UploadArray['error'];
        } else {
            $empowercast_diags['uploading_artwork']['message'] = __('An error occurred obtaining the uploads directory from WordPress.', 'empowercast');
        }
    }

    // Fourth, see if we have enough memory and we're running an appropriate version of PHP
    $empowercast_diags['system_info'] = array();
    $empowercast_diags['system_info']['warning'] = false;
    $empowercast_diags['system_info']['success'] = true;
    $empowercast_diags['system_info']['php_version'] = phpversion();
    $empowercast_diags['system_info']['php_cgi'] = (function_exists('php_sapi_name') && preg_match('/cgi/i', php_sapi_name())? true : false );
    $empowercast_diags['system_info']['memory_limit'] = (int) ini_get('memory_limit');
    $empowercast_diags['system_info']['temp_directory'] = get_temp_dir(); // Function available since WP2.5+

    // testing:
    //$empowercast_diags['system_info']['memory_limit'] = -1;
    //$empowercast_diags['system_info']['memory_limit'] = 0;
    //$empowercast_diags['system_info']['memory_limit'] = 16;

    if($empowercast_diags['system_info']['memory_limit'] == 0){
        if(version_compare($empowercast_diags['system_info']['php_version'], '5.2') > 0){
            $empowercast_diags['system_info']['memory_limit'] = 128;
        } elseif(version_compare($empowercast_diags['system_info']['php_version'], '5.2') == 0){
            $empowercast_diags['system_info']['memory_limit'] = 16;
        } else {
            $empowercast_diags['system_info']['memory_limit'] = 8;
        }
    }

    $empowercast_diags['system_info']['memory_used'] = 0;

    if(version_compare($empowercast_diags['system_info']['php_version'], '7.0') > -1){
        $empowercast_diags['system_info']['message'] = sprintf( __('Your version of PHP (%s) is OK!', 'empowercast'), $empowercast_diags['system_info']['php_version']);
    } elseif(version_compare($empowercast_diags['system_info']['php_version'], '5.4') > -1){
        $empowercast_diags['system_info']['message'] = sprintf( __('Your version of PHP (%s) is OK, though PHP 7.0 or newer is recommended.', 'empowercast'), $empowercast_diags['system_info']['php_version'] );
    } else {
        $empowercast_diags['system_info']['message'] = sprintf( __('Your version of PHP (%s) will work, but PHP 7.0 or newer is recommended.', 'empowercast'), $empowercast_diags['system_info']['php_version'] );
    }

    $used = 0;
    $total = $empowercast_diags['system_info']['memory_limit'];

    if($total == -1){
        $empowercast_diags['system_info']['message2'] = __('Your scripts have no limit to the amount of memory they can use.', 'empowercast');
        $used = (function_exists('memory_get_peak_usage')? memory_get_peak_usage() : ( function_exists('memory_get_usage') ? memory_get_usage() : 0 ) );
        if($used){
            $empowercast_diags['system_info']['memory_used'] = round($used / 1024 / 1024, 2);
        }
    } elseif(function_exists('memory_get_peak_usage')){
        $used = round(memory_get_peak_usage() / 1024 / 1024, 2);
        $empowercast_diags['system_info']['memory_used'] = $used;
        $percent = ($used/$total)*100;
        $empowercast_diags['system_info']['message2'] = sprintf(__('You are using %d%% (%.01fM of %.01dM) of available memory.', 'empowercast'), $percent, $used, $total);
    } elseif(function_exists('memory_get_usage')){
        $used = round(memory_get_usage() / 1024 / 1024, 2);
        $empowercast_diags['system_info']['memory_used'] = $used;
        $percent = ($used/$total)*100;
        $empowercast_diags['system_info']['message2'] = sprintf(__('You are using %d%% (%.01fM of %dM) of available memory. Versions of PHP 5.2 or newer will give you a more accurate total of memory usage.', 'empowercast'), $percent, $used, $total);
    } else {
        $empowercast_diags['system_info']['message2'] = sprintf(__('Your scripts have a total of %dM.', 'empowercast'), $total );
    }

    if($total > 0 && ($used + 4) > $total){
        $empowercast_diags['system_info']['warning'] = true;
        $empowercast_diags['system_info']['message2'] = __('Warning:', 'empowercast') .' '. $empowercast_diags['system_info']['message2'];
        $empowercast_diags['system_info']['message2'] .= ' ';
        $empowercast_diags['system_info']['message2'] .= sprintf(__('We recommend that you have at least %dM (4M more that what is currently used) or more memory to accomodate all of your installed plugins.', 'empowercast'), ceil($used)+4 );
    }

    if(empty($empowercast_diags['system_info']['temp_directory'])){
        $empowercast_diags['system_info']['success'] = false;
        $empowercast_diags['system_info']['message3'] =  __('Error:', 'empowercast') .' '. __('No temporary directory available.', 'empowercast');
    } elseif(is_dir($empowercast_diags['system_info']['temp_directory']) && is_writable($empowercast_diags['system_info']['temp_directory'])){
        $empowercast_diags['system_info']['message3'] = sprintf(__('Temporary directory %s is writable.', 'empowercast'), $empowercast_diags['system_info']['temp_directory']);
    } else {
        $empowercast_diags['system_info']['success'] = false;
        $empowercast_diags['system_info']['message3'] = __('Error:', 'empowercast') .' '. sprintf(__('Temporary directory %s is not writable.', 'empowercast'), $empowercast_diags['system_info']['temp_directory']);
    }

    if(empty($empowercast_diags['system_info']['php_cgi'])){
        $empowercast_diags['system_info']['message4'] = '';
    } else {
        $empowercast_diags['system_info']['message4'] = __('Warning:', 'empowercast') .' '. __('PHP running in CGI mode.', 'empowercast');
    }

    $user_info = wp_get_current_user();
    if(!empty($user_info->user_email) && isset($_GET['Submit'])){
        empowercastadmin_diagnostics_email($user_info->user_email);
        empowercast_page_message_add_notice(sprintf(__('Diagnostic results sent to %s.', 'empowercast'), $user_info->user_email));
    }
}
	
function empowercastadmin_diagnostics_email($email){
    global $empowercast_diags, $wpmu_version, $wp_version, $empowercast_diag_message;
    $SettingsGeneral = get_option('empowercast_general');

    // First we need some basic information about the blog...
    $message = __('Blog Title:', 'empowercast') .' '. get_bloginfo('name') . "<br />\n";
    $message .= __('Blog URL:', 'empowercast') .' '. get_bloginfo('url') . "<br />\n";
    $message .= __('WordPress Version:', 'empowercast') .' '. $wp_version . "<br />\n";
    if(!empty($wpmu_version)){
        $message .= __('WordPress MU Version:', 'empowercast') .' '. $wpmu_version . "<br />\n";
    }
    $message .= __('System:', 'empowercast') .' '. $_SERVER['SERVER_SOFTWARE'] . "<br />\n";
    $message .= __('Open basedir:', 'empowercast') .' '. ini_get('open_basedir') ."<br />\n";

    // Crutial EmpowerCast Settings
    $message .= "<br />\n";
    $message .= '<strong>'. __('Important EmpowerCast Settings', 'empowercast') ."</strong><br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('EmpowerCast version:', 'empowercast') .' '. EMPOWERCAST_VERSION ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('episode box file size/duration fields:', 'empowercast') .' '. ( empty($SettingsGeneral['episode_box_mode']) ?__('yes', 'empowercast'): ($SettingsGeneral['episode_box_mode']==1?__('no', 'empowercast'):__('yes', 'empowercast')) ) ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('Podcasting capability:', 'empowercast') .' '. ( empty($SettingsGeneral['use_caps'])?__('Disabled (default)', 'empowercast'): __('Enabled', 'empowercast')) ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('Feed capability:', 'empowercast') .' '. ( empty($SettingsGeneral['feed_caps'])?__('Disabled (default)', 'empowercast'): __('Enabled', 'empowercast')) ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('Category Podcasting:', 'empowercast') .' '. ( empty($SettingsGeneral['cat_casting']) ?__('Disabled (default)', 'empowercast'): __('Enabled', 'empowercast')) ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('Podcast Channels:', 'empowercast') .' '. ( empty($SettingsGeneral['channels']) ?__('Disabled (default)', 'empowercast'): __('Enabled', 'empowercast')) ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('Additional Player Options:', 'empowercast') .' '. ( empty($SettingsGeneral['player_options'])?__('Disabled (default)', 'empowercast'): __('Enabled', 'empowercast')) ."<br />\n";

    // Detecting Media Information
    $message .= "<br />\n";
    $message .= '<strong>'.__('Detecting Media Information', 'empowercast') ."</strong><br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('success:', 'empowercast') .' '. ($empowercast_diags['detecting_media']['success']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('warning:', 'empowercast') .' '. ($empowercast_diags['detecting_media']['warning']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('allow_url_fopen:', 'empowercast') .' '. ($empowercast_diags['detecting_media']['allow_url_fopen']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('curl:', 'empowercast') .' '. ($empowercast_diags['detecting_media']['curl']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('curl_ssl:', 'empowercast') .' '. ($empowercast_diags['detecting_media']['curl_ssl']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('openssl:', 'empowercast') .' '. ($empowercast_diags['detecting_media']['openssl']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('message:', 'empowercast') .' '. $empowercast_diags['detecting_media']['message'] ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('message 2:', 'empowercast') .' '. $empowercast_diags['detecting_media']['message2'] ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('message 3:', 'empowercast') .' '. $empowercast_diags['detecting_media']['message3'] ."<br />\n";

    // Uploading Artwork
    $message .= "<br />\n";
    $message .= '<strong>'.__('Uploading Artwork', 'empowercast') ."</strong><br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('success:', 'empowercast') .' '. ($empowercast_diags['uploading_artwork']['success']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('file_uploads:', 'empowercast') .' '. ($empowercast_diags['uploading_artwork']['file_uploads']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('writable:', 'empowercast') .' '. ($empowercast_diags['uploading_artwork']['writable']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('upload_path:', 'empowercast') .' '. $empowercast_diags['uploading_artwork']['upload_path'] ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('message:', 'empowercast') .' '. $empowercast_diags['uploading_artwork']['message'] ."<br />\n";

    // System Information
    $message .= "<br />\n";
    $message .= '<strong>'.__('System Information', 'empowercast') ."</strong><br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('success:', 'empowercast') .' '. ($empowercast_diags['system_info']['success']?'true':'false') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('warning:', 'empowercast') .' '. ($empowercast_diags['system_info']['warning']?'yes':'no') ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('php_version:', 'empowercast') .' '. $empowercast_diags['system_info']['php_version'] ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('memory_limit:', 'empowercast') .' '. $empowercast_diags['system_info']['memory_limit'] ."M\n";
    $message .= " &nbsp; \t &nbsp; ". __('memory_used:', 'empowercast') .' '. sprintf('%.01fM',$empowercast_diags['system_info']['memory_used']) ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('temp directory:', 'empowercast') .' '. $empowercast_diags['system_info']['temp_directory'] ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('message:', 'empowercast') .' '. $empowercast_diags['system_info']['message'] ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('message 2:', 'empowercast') .' '. $empowercast_diags['system_info']['message2'] ."<br />\n";
    $message .= " &nbsp; \t &nbsp; ". __('message 3:', 'empowercast') .' '. $empowercast_diags['system_info']['message3'] ."<br />\n";
    if(!empty($empowercast_diags['system_info']['message4'])){
        $message .= " &nbsp; \t &nbsp; ". __('message 4:', 'empowercast') .' '. $empowercast_diags['system_info']['message4'] ."<br />\n";
    }

    // We are always sending the active plugins list
    if(true){
        $current_plugins = get_option('active_plugins');
        $message .= "<br />\n";
        $message .= '<strong>'.__('Active Plugins', 'empowercast') ."</strong><br />\n";
        foreach($current_plugins as $null=> $plugin_path){
            $plugin_data = get_plugin_data( rtrim(WP_PLUGIN_DIR, '/\\'). '/'. rtrim($plugin_path, '\\/'), false, false ); //Do not apply markup/translate as it'll be cached.
            $message .= " &nbsp; \t &nbsp; " . __('Title:', 'empowercast') .' '. $plugin_data['Title']. "<br />\n";
            $message .= " &nbsp; \t &nbsp; " . __('Relative Path:', 'empowercast') .' '. $plugin_path. "<br />\n";
            $message .= " &nbsp; \t &nbsp; " . __('Version:', 'empowercast') .' '. $plugin_data['Version']. "<br />\n";
            $message .= " &nbsp; \t &nbsp; " . __('Web Site:', 'empowercast') .' '. $plugin_data['PluginURI']. "<br />\n";
            $message .= "<br />\n";
        }
    }

    // Now lets loop through each section of diagnostics
    $user_info = wp_get_current_user();
    $from_email = $user_info->user_email;
    $from_name = $user_info->user_nicename;
    $headers = 'From: "'.$from_name.'" <'.$from_email.'>'."\n"
        .'Reply-To: "'.$from_name.'" <'.$from_email.'>'."\n"
        .'Return-Path: "'.$from_name.'" <'.$from_email.'>'."\n";

    if(!empty($_GET['support'])){
        $from_name = 'Podops Support';
        $from_email = 'support@podopshost.com';
        $headers .= 'CC: "'.$from_name.'" <'.$from_email.'>'."\n";
    }

    if(!empty($_GET['additional']) && !empty($_GET['additional_email'])){
        $from_email = htmlspecialchars($_GET['additional_email']);
        $headers .= 'CC: <'.$from_email.'>'."\n";
    }

    $headers .= "Content-Type: text/html\n";

    @wp_mail($email, sprintf(__('Podops EmpowerCast diagnostic results for %s', 'empowercast'), get_bloginfo('name')), $message, $headers);
    $empowercast_diag_message = $message;
}
	
function empowercastadmin_diagnostics_is_writable($dir){
    // Make sure we can create a file in the specified directory...
    if(is_dir($dir)){
        return is_writable($dir);
    }
    return false;
}
	
function empowercastadmin_diagnostics_status($success=true, $warning=false){
    $img = 'yes.png';
    $color = '#0A8822';
    $text = __('Success', 'empowercast');
    if($success == false){ // Failed takes precedence over warning
        $img = 'no.png';
        $color = '#CC0000';
        $text = __('Failed', 'empowercast');
    } elseif($warning){
        $img = '../../../wp-includes/images/smilies/icon_exclaim.gif';
        $color = '#D98500';
        $text = __('Warning', 'empowercast');
    }
    ?>

<!--    <img src="--><?php //echo admin_url(); ?><!--/images/--><?php //echo $img; ?><!--" style="vertical-align:text-top;" />-->
    <strong style="color:<?php echo $color; ?>;"><?php echo $text; ?></strong>

<?php }
	
function empowercastadmin_diagnostics(){
    global $empowercast_diags, $empowercast_diag_message;
    $GeneralSettings = get_option('empowercast_general');

    if(empty($empowercast_diags)){
        empowercastadmin_diagnostics_process();
        empowercast_page_message_print();
    }
    ?>

    <div class="pp-row">
        <a class="pp-page-back-link" href="admin.php?page=empowercast/empowercastadmin_tools.php"><span>&#8592; EmpowerCast Tools</span></a>
    </div>
    <div class="pp-card-body">
        <div class="pp-row pp-tools-row">
            <h2 class="pp-page-sub-header">EmpowerCast Diagnostics</h2>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">
                The Diagnostics page checks to see if your server is configured to support all the available features in Podops EmpowerCast.
            </p>
        </div>
        <div class="pp-row pp-tools-row">
            <h3>Detecting Media Information</h3>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">
                The following test checks to see if your web server can make connections with other web servers to obtain file size and media duration information.
                The test checks to see if either the PHP cURL library is installed or the php.ini setting ‘allow_url_fopen’ enabled.
            </p>
        </div>
        <div class="diagnostics-message">
            <div class="pp-row">
                <h3><?php empowercastadmin_diagnostics_status($empowercast_diags['detecting_media']['success'],$empowercast_diags['detecting_media']['warning']); ?></h3>
            </div>
            <div class="pp-row">
                <p class="pp-tools-text" style="margin-bottom: 5px;"><?php echo htmlspecialchars($empowercast_diags['detecting_media']['message']); ?></p>
            </div>
            <?php if($empowercast_diags['detecting_media']['message2']){ ?>
                <div class="pp-row">
                    <p class="pp-tools-text" style="margin-bottom: 5px;"><?php echo htmlspecialchars($empowercast_diags['detecting_media']['message2']); ?></p>
                </div>
            <?php } ?>
            <?php if($empowercast_diags['detecting_media']['message3']){ ?>
                <div class="pp-row">
                    <p class="pp-tools-text" style="margin-bottom: 5px;"><?php echo htmlspecialchars($empowercast_diags['detecting_media']['message3']); ?></p>
                </div>
            <?php } ?>
            <?php if($empowercast_diags['detecting_media']['success']){ ?>
                <div class="pp-row">
                    <p class="pp-tools-text" style="margin-bottom: 5px;">If you are still having problems detecting media information, check with your web hosting provider if there is a firewall blocking your server.</p>
                </div>
            <?php } else { ?>
                <div class="pp-row">
                    <p class="pp-tools-text" style="margin-bottom: 5px;">Contact your web hosting provider with the information above.</p>
                </div>
            <?php } ?>
            <div class="pp-row">
                <ul>
                    <li>
                        <ul>
                            <li style="font-size: 100%;"><p>allow_url_fopen: <?php echo ($empowercast_diags['detecting_media']['allow_url_fopen'] ? 'true' : 'false'); ?></p></li>
                            <li style="font-size: 100%;"><p>curl: <?php echo ($empowercast_diags['detecting_media']['curl'] ? 'true' : 'false'); ?></p></li>
                            <li style="font-size: 100%;"><p>curl_ssl: <?php echo ($empowercast_diags['detecting_media']['curl_ssl'] ? 'true' : 'false'); ?></p></li>
                            <li style="font-size: 100%;"><p>openssl: <?php echo ($empowercast_diags['detecting_media']['openssl'] ? 'true' : 'false'); ?></p></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="pp-row pp-tools-row">
            <h3>Uploading Artwork</h3>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">The following test checks your version of PHP, memory usage and temporary directory access.</p>
        </div>
        <div class="diagnostics-message">
            <div class="pp-row">
                <h3><?php empowercastadmin_diagnostics_status($empowercast_diags['uploading_artwork']['success']); ?></h3>
            </div>

            <div class="pp-row">
                <p class="pp-tools-text" style="margin-bottom: 15px;"><?php echo htmlspecialchars($empowercast_diags['uploading_artwork']['message']); ?></p>
            </div>
        </div>
        <hr>
        <div class="pp-row pp-tools-row">
            <h3>System Information</h3>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">The following test checks your version of PHP, memory usage and temporary directory access.</p>
        </div>
        <div class="diagnostics-message">
            <div class="pp-row">
                <h3><?php empowercastadmin_diagnostics_status($empowercast_diags['system_info']['success'], ($empowercast_diags['system_info']['warning'] || $empowercast_diags['system_info']['php_cgi'])); ?></h3>
            </div>
            <div class="pp-row">
                <p class="pp-tools-text" style="margin-bottom: 15px;">
                    <?php echo htmlspecialchars(sprintf(__('WordPress Version %s'), $GLOBALS['wp_version'])); ?>
                    <?php echo htmlspecialchars($empowercast_diags['system_info']['message']); ?>
                    <?php echo htmlspecialchars($empowercast_diags['system_info']['message2']); ?>
                    <?php echo htmlspecialchars($empowercast_diags['system_info']['message3']); ?>

                    <?php if(!empty($empowercast_diags['system_info']['php_cgi']) ) { ?>
                        <?php echo __('Warning:', 'empowercast') .' '. __('PHP running in CGI mode.', 'empowercast'); ?>
                    <?php }

                    if($empowercast_diags['system_info']['warning']) { ?>
                        <?php echo __('Contact your web hosting provider to inquire how to increase the PHP memory limit on your web server.', 'empowercast'); ?>
                    <?php } ?>
                </p>
            </div>
        </div>
        <hr>
        <div class="pp-row pp-tools-row">
            <h3>Email Results</h3>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">Send the results above to the selected email addresses.</p>
        </div>

        <form enctype="multipart/form-data" method="get" action="<?php echo admin_url('admin.php'); ?>">
            <input type="hidden" name="action" value="empowercast-diagnostics" />
            <input type="hidden" name="page" value="empowercast/empowercastadmin_tools.php" />

            <?php // Print nonce
                wp_nonce_field('empowercast-diagnostics');
            ?>

            <div class="pp-row pp-tools-row" style="margin-bottom: 15px;">
                <input style="margin-top: 3px; margin-right: 15px;" type="checkbox" name="CC" value="1" checked disabled/>
                <p style="margin: 0;">Send to <?php $user_info = wp_get_current_user(); echo $user_info->user_email; ?></p>
            </div>

            <div class="pp-row pp-tools-row" style="margin-bottom: 15px;">
                <input style="margin-top: 3px; margin-right: 15px;" type="checkbox" name="support" value="1" checked/>
                <p style="margin: 0;">CC: support@podopshosting.com</p>
            </div>

            <div class="pp-row pp-tools-row" style="margin-bottom: 5px;">
                <input style="margin-top: 3px; margin-right: 15px;" type="checkbox" name="additional" value="1"/>
                <p style="margin: 0;">CC: additional email address</p>
            </div>

            <div class="pp-row pp-tools-row">
               <input type="text" name="additional_email" value="" style="width: 30%; padding: 0 10px 0 10px; margin-left: 30px;"/>
            </div>

            <div class="pp-row pp-tools-row" style="display: none;">
                <input style="margin-top: 3px; margin-right: 15px;" type="checkbox" name="ap" value="1" checked/>
                <p style="margin: 0;"><?php echo __('Include list of active plugins in diagnostics results.', 'empowercast') ?></p>
            </div>

            <div class="pp-row pp-tools-row" style="margin-top: 30px;">
                <input style="margin: 0 0 30px 0;" class="empowercast_save_button_other pp-tools-button" type="submit" name="Submit" id="empowercast_save_button" value="Send Results">
            </div>
        </form>
    </div>
<?php } ?>