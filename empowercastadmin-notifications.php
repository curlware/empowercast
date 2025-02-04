<?php
	// empowercastadmin-notifications.php
	
	// Notice message manager for EmpowerCast
	
	// Inspired by the notifications in Yoast's SEO
	// Also inspired from https://premium.wpmudev.org/blog/adding-admin-notices/
	// For the sake of keeping things simple, we will only include this class when we need to display notifications
	
class EmpowerCast_Notification {
	
	private $settings = array();
	
	public function __construct($content, $settings = array() )
	{
		$defaults = array();
		$defaults['content'] = '';
		$defaults['type'] = 'updated';
		$defaults['id'] = '';
		$this->settings = wp_parse_args( $settings, $defaults );
		if( !empty($content) )
			$this->settings['content'] = $content;
		if( empty($this->settings['content']) )
			$this->settings['id'] = crc32($this->settings['content']); // Not ideal, but with no ID we need some unique value
	}
	
	public function get_notification_as_string()
	{
		$html = '<div class="empowercast-notice notice is-dismissible ' . esc_attr( $this->settings['type'] ) . '" id="empowercast-notice-'. esc_attr( $this->settings['id'] ) .'">'; 
		$html .= wpautop( $this->settings['content'] );
			
		if( version_compare($GLOBALS['wp_version'], 4.2, '<' ) ) {
			$html .= '<p>&nbsp; <a style="float:right;" href="#" class="notice-dismiss-link">'. __('Dismiss', 'empowercast') .'</a></p>';
		}
		$html .= '</div>' . PHP_EOL;
		return $html;
	}
	
};

class EmpowerCast_Notification_Manager {

	private $notifications = array();
	private $dismissedNotifications = array();
	
	public function __construct()
	{
		$this->dismissedNotifications = get_option('empowercast_dismissed_notices');
		add_action( 'all_admin_notices', array( $this, 'all_admin_notices' ) );
		add_action('wp_ajax_empowercast_notice_dismiss', array($this, 'wp_ajax_empowercast_notice_dismiss') );
		add_action('admin_head', array($this, 'admin_head') );
	}

	// this will print the notification as a normal string, rather than a bar at the top of the page
	public function print_one_notice($id, $ignore_dismiss = false) {
	    echo "<div class='pp-sidebar-notification'>";
	    if (!empty($this->notifications[$id])) {
	        echo str_replace(array('empowercast-notice ', 'notice ', 'updated'), array('', '', ''), $this->notifications[$id]->get_notification_as_string());
        } else if ($ignore_dismiss) {
            echo str_replace(array('empowercast-notice ', 'notice ', 'updated'), array('', '', ''), $this->dismissedNotifications[$id]);
        }
	    echo "</div>";
    }
	
	public function all_admin_notices()
	{
		foreach( $this->notifications as $key => $notification )
		{
			echo $notification->get_notification_as_string();
		}
	}
	
	public function wp_ajax_empowercast_notice_dismiss()
	{
        // Check for nonce security
        if (!isset($_POST['nonce'])) {
            exit;
        }
        if ( ! wp_verify_nonce( $_POST['nonce'], 'empowercast-notice-dismiss' ) ) {
            exit;
        }
		$dismiss_notice_id = $_POST['dismiss_notice_id'];
		preg_match('/^empowercast-notice-(.*)$/i', $dismiss_notice_id, $match );
		if( empty($match[1]) )
			die('-1');
			
		$DismissedNotifications = get_option('empowercast_dismissed_notices');
		if( !is_array($DismissedNotifications) )
			$DismissedNotifications = array();
		$DismissedNotifications[ $match[1] ] = $this->notifications[$match[1]]->get_notification_as_string();
		update_option('empowercast_dismissed_notices',  $DismissedNotifications);
		die('1');
	}
	
	function admin_head()
	{
		if( count($this->notifications) > 0 ) // If there are notices to print, then lets also put in the ajax to clear them
		{
			if( version_compare($GLOBALS['wp_version'], 4.2, '>=' ) ) {
?>
<script type="text/javascript"><!--

jQuery(document).ready( function() {
	
	jQuery(document).on( 'click', '.empowercast-notice .notice-dismiss', function() {
	
		var dismissId = jQuery(this).closest('.empowercast-notice').attr('id');
		jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action:"empowercast_notice_dismiss", dismiss_notice_id: dismissId, nonce: '<?php echo wp_create_nonce('empowercast-notice-dismiss'); ?>' });
			});
});

--></script>
<?php
			}
			else
			{
?>
<script type="text/javascript"><!--

jQuery(document).ready( function() {
	
	jQuery(document).on( 'click', '.empowercast-notice .notice-dismiss-link', function(e) {
		e.preventDefault();
		var dismissId = jQuery(this).closest('.empowercast-notice').attr('id');
		jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action:"empowercast_notice_dismiss", dismiss_notice_id: dismissId, nonce: '<?php echo wp_create_nonce('empowercast-notice-dismiss'); ?>' });
		jQuery(this).closest('.empowercast-notice').hide(); // Hide the div now we dismissed it
	});
});

--></script>
<?php
			}
?>
<style>
.empowercast-notice a {
	text-decoration: underline;
}
</style>	
<?php
		}
	}
	
	public function dismissed_status($notification_id)
	{
		if( !empty($this->dismissedNotifications[ $notification_id ]) )
			return true;
		return false;
	}
	
	public function add($notification_id, $notification_content)
	{
		if( !$this->dismissed_status($notification_id) ) {
			$this->notifications[$notification_id] = new EmpowerCast_Notification($notification_content, array('id'=>$notification_id)  );
		}
	}

};

function empowercastadmin_notifications_appropriate()
{
	// Any empowercast page
	if( preg_match('/wp-admin\/.*empowercast/', $_SERVER['REQUEST_URI']) )
		return true;
		
	// Dashboard is acceptable:
	if( preg_match('/wp-admin\/(index\.php)?$/', $_SERVER['REQUEST_URI']) )
		return true;
	
	// edit posts (pages, post types, etc...)
	if( preg_match('/wp-admin\/edit\.php/', $_SERVER['REQUEST_URI']) )
		return true;
		
	// managing plugins
	if( preg_match('/wp-admin\/plugins\.php/', $_SERVER['REQUEST_URI']) )
		return true;

    // managing themes
    if( preg_match('/wp-admin\/themes\.php/', $_SERVER['REQUEST_URI']) )
        return true;
	
	// Required so we can dismiss notices
	if( preg_match('/wp-admin\/admin-ajax\.php/', $_SERVER['REQUEST_URI']) )
		return true;
		
	return false;
}



// eof