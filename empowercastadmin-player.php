<?php
	// EmpowerCast Player administration
	
	
// Handle post processing here for the players page.
function empowercast_admin_players_init()
{
	wp_enqueue_style('wp-mediaelement');
	wp_enqueue_script( 'wp-mediaelement' );
	
	$Settings = false; // Important, never remove this
	$Step = 1;
	
	$action = (isset($_GET['action'])?$_GET['action']: (isset($_POST['action'])?$_POST['action']:false) );
	//$type = (isset($_GET['type'])?$_GET['type']: (isset($_POST['type'])?$_POST['type']:'audio') );
	
	if( !$action )
		return;
		
	switch($action)
	{
		case 'empowercast-select-player': {
			$SaveSettings = array();
			if( isset($_POST['Player']) )
				$SaveSettings = $_POST['Player'];
			if( isset($_POST['VideoPlayer']) )
				$SaveSettings += $_POST['VideoPlayer'];

            $messageText = 'Player activated successfully.';
            if(isset($_POST['Submit'])){
                if($_POST['Submit'] == 'Save Changes'){
                    $messageText = 'Podops Player settings saved successfully.';
                }
            }

			empowercast_save_settings($SaveSettings, 'empowercast_general');
			empowercast_page_message_add_notice( __($messageText, 'empowercast') );

		} break;
		case 'empowercast_bplayer':{ // podops player
			$SaveSettings = !empty($_POST['BBPlayer']) ? $_POST['BBPlayer'] : array();
            $GenSettings = $_POST['General'];
            $ModernPlayerSettings = !empty($_POST['ModernPlayer']) ? $_POST['ModernPlayer'] : array();

            if(empty($SaveSettings)){
                $SaveSettings = $ModernPlayerSettings;
            }

			empowercast_save_settings($SaveSettings, 'empowercast_bplayer');
            empowercast_save_settings($GenSettings, 'empowercast_general');
			empowercast_page_message_add_notice( __('Podops Player settings saved successfully.', 'empowercast') );
		} break;
	}
}
