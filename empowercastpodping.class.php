<?php


class EmpowerCastPodping
{
    /**
     * Podping constructor.
     */
    public function __construct(){}


    /**
     * Calls Podops API to pubilsh update to Podping
     * @param string $url URL of feed to update
     * @param string $program_keyword Podops program keyword
     * @throws Exception Throws exception if keyword is missing or if Podops API returns an error
     * @return bool
     */
    public function publish($url, $program_keyword = "", $reason=false) {

        $GeneralSettings = get_option('empowercast_general', array());
        if (!$program_keyword) {
            $program_keyword = $GeneralSettings['podops_program_keyword'];
        }
        if (!$program_keyword) {
            throw new Exception("Could not detect Podops program keyword." );
        }

        $post_array = array('auth_suffix' => $this->getAuthKey(), 'empowercast_version' => EMPOWERCAST_VERSION, 'feed_url' => $url);

        if ($reason)
            $post_array['reason'] = $reason;

        require_once('empowercastadmin-auth.class.php');
        $creds = get_option('empowercast_creds');
        $auth = new EmpowerCastAuth();
        if ($creds) {
            $accessToken = empowercast_getAccessToken();
            $req_url = sprintf('/2/episode/%s/podping-notification/?format=json&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword));
            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
            $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
            $podping_results = $auth->api($accessToken, $req_url, $post_array, false, 60 * 30);
        } else {
            if (empty($GeneralSettings['podops_auth'])) {
                throw new Exception("No Podops account linked." );
            }
            $api_url_array = empowercast_get_api_array();
            foreach ($api_url_array as $index => $api_url) {
                $req_url = sprintf('/episode/%s/podping-notification/?format=json&cache=' . md5(rand(0, 999) . time()), urlencode($program_keyword));
                $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '&' . EMPOWERCAST_PODOPS_API_QSA : '');
                $req_url .= (defined('EMPOWERCAST_PUBLISH_PROTECTED') ? '&protected=true' : '');
                $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $GeneralSettings['podops_auth'], $post_array, 60 * 30); // give this up to 30 minutes, though 3 seocnds to 20 seconds is all one should need.
                if (!$json_data && $api_url == 'https://api.podopshost.com/') { // Let's force cURL and see if that helps...
                    $json_data = empowercast_remote_fopen(rtrim($api_url, '/') . $req_url, $GeneralSettings['podops_auth'], $post_array, 60 * 30, false, true);
                }
                if ($json_data != false)
                    break;
            }
            $podping_results = empowercast_json_decode($json_data);
        }

        if (!$podping_results || !empty($podping_results['error'])) {
            $error = $auth->getLastError();
            if (!empty($podping_results['error'])) {
                $error = $podping_results['error'];
            }
            if ($error) {
                throw new Exception("Podping error: " . $error );
            } else {
                throw new Exception("Unknown podping error occurred.");
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getAuthKey()
    {
        $auth_key = get_option('empowercast_podping_authkey');

        // generate auth key only once for each empowercast install
        if (!$auth_key) {
            $feed_url = get_home_url() . '/feed/podcast';
            // start with crc32 of the feed url
            $auth_key = crc32($feed_url);
            $needed_characters = 22 - strlen($auth_key);
            $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            // fill in the rest of the 22 total characters randomly
            while ($needed_characters) {
                $auth_key .= $charset[rand(0, strlen($charset) - 1)];
                $needed_characters -= 1;
            }

            update_option('empowercast_podping_authkey', $auth_key);
        }
        return $auth_key;
    }
}