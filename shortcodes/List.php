<?php


class EmpowerCastNetworkList extends EmpowerCastNetworkShortCode
{
    function __construct()
    {
        parent::__construct('ppn-list');
    }

    function ppn_shortcode($attr, $contents)
    {
        plugins_url('/empowercast/empowercast.php', '__FILE__');
        plugins_url('/empowercast/empowercastadmin-jquery.php');
        require_once(WP_PLUGIN_DIR . '/empowercast/empowercastadmin.php');

        $list_id = $attr['id'];

        $props = [];
        $props['style'] = 'simple';
        if (isset($attr['style']) && $attr['style'] == 'detailed') {
            $props['style'] = $attr['style'];
        }

        $page = get_query_var('paged');
        if ($page > 1) {
            $props['paged'] = $page;
        } else {
            $props['start'] = 1;
            $props['paged'] = 1;
        }

        $props['show-paging'] = true;

        $pathParts = explode('/', htmlspecialchars($_SERVER['REQUEST_URI']));
        $props['link-to'] = "featured/" . $pathParts[1];

        $apiArray = empowercast_get_api_array();
        $apiUrl = $apiArray[0];

        $networkInfo = get_option('empowercast_network');
        $networkId = get_option('empowercast_network_id');
        $networkTitle = get_option('empowercast_network_title');
        $networkInfo['network_id'] = $networkId;
        $networkInfo['network_title'] = $networkTitle;

        $props['network_general'] = get_option('network_general');
        $props['network_map'] = get_option('empowercast_network_map');
        if (isset($networkInfo['network_id'])) {
            $networkId = $networkInfo['network_id'];
        }
        if (isset($networkInfo['network_title'])) {
            $networkTitle = $networkInfo['network_title'];
        }
        if (!isset($null)) {
            $null = null;
        }
        $post = false;
        if (!empty($networkInfo['network_id'])) {
            if (!empty($list_id) && $list_id != 'all') {
                $networkInfo['list_id'] = $list_id;
                $results = EmpowercastNetworkDataBus::getSpecificListInNetwork($apiUrl, array('post' => $post), $networkInfo, false);
                $props['list_title'] = $results['list_info']['list_title'];
                $props['list_desc'] = $results['list_info']['list_description'];
                unset($props['results']['list_title']);
                unset($props['results']['list_description']);
                $props['results'] = $results;
            } else if (!empty($list_id) && $list_id == 'all') {
                $results = EmpowercastNetworkDataBus::getProgramsInNetwork($apiUrl, array('post' => $post), $networkInfo, true);
                $props['results']['programs'] = $results;
                $props['list_title'] = 'ABC';
            }

            $props['network_title'] = $networkTitle;
            $props['network_id'] = $networkId;
            $props['post'] = $post;
            $props['apiUrl'] = $apiUrl;
            $temp = null;
            if (!empty($props['results']['programs'])) {
                foreach ($props['results']['programs'] as $program) {
                    if (($list_id == 'all') || (isset($program['checked']) && $program['checked'] == true)) {
                        $program['link'] = '#';
                        if (isset($props['network_map']['p-' . $program['program_id']])) {
                            $postId = $props['network_map']['p-' . $program['program_id']];
                            if (get_post_status($postId) == 'publish') {
                                $program['link'] = get_post_permalink($postId);
                            } else {
                                $program['link'] = '#';
                            }
                        }
                        $temp[] = $program;
                    }
                }
            }
            $props['results'] = $temp;
            if (empty($props['list_title'])) {
                return EmpowerCastNetwork::getHTML('no-list-results.php', $props, null, null);
            } else if (empty($props['results'])) {
                return EmpowerCastNetwork::getHTML('list-no-programs.php', $props, null, null);
            } else {
                return EmpowerCastNetwork::getHTML('lists-results.php', $props, null, null);
            }
        } else {
            return EmpowerCastNetwork::getHTML('invalid-network.php', $props, null, null);
        }
    }
}

$GLOBALS['ppn-list'] = new EmpowerCastNetworkList();
