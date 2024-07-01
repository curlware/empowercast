<?php

class EmpowerCastNetworkApplication extends EmpowerCastNetworkShortCode
{
    function __construct()
    {
        parent::__construct('ppn-application');
    }

    function ppn_shortcode($attr, $contents)
    {
        require_once(WP_PLUGIN_DIR . '/empowercast/empowercastadmin.php');
        $props = array();

        if ( !empty($attr['auto-active']) ) {
            $props['auto-active'] = (bool)true;
        }

        if ( isset($attr['terms-url']) ) {
            $props['terms-url'] = (string)$attr['terms-url'];
        }

        if ( isset($attr['default-list']) ) {
            $props['default-list'] = (int)$attr['default-list'];
        }

        $props['network_general'] = get_option('network_general');
        $props['empowercast_network'] = get_option('empowercast_network');
        if (isset($props['empowercast_network']['list_id'])){
            $listID = $props['empowercast_network']['list_id'];
        }
        $networkID = get_option('empowercast_network_id');
        $networkTitle = get_option('empowercast_network_title');
        $props['empowercast_network']['network_id'] = $networkID;
        $props['empowercast_network']['network_title'] = $networkTitle;

        $post = false;
        $requestUrl = '/2/empowercast/network/'.$networkID.'/lists/';
        $results = $GLOBALS['ppn_object']->requestAPI($requestUrl, true, $post);

        $props['post'] = $post;
        $props['lists'] = $results;
        //$props['apiUrl'] = $apiUrl;
        if (!isset($null)){
            $null = null;
        }
        return EmpowerCastNetwork::getHTML('forms.php', $props, null, null);
    }
}

$GLOBALS['ppn_application'] = new EmpowerCastNetworkApplication();

