<?php
class EmpowercastOnboarding
{
    function __construct()
    {
        if (defined('WP_DEBUG')) {
            if (WP_DEBUG) {
                wp_enqueue_style('empowercast_onboarding_styles', plugin_dir_url(__FILE__) . 'css/onboarding.css', array(), EMPOWERCAST_VERSION);
            } else {
                wp_enqueue_style('empowercast_onboarding_styles', plugin_dir_url(__FILE__) . 'css/onboarding.min.css', array(), EMPOWERCAST_VERSION);
            }
        } else {
            wp_enqueue_style('empowercast_onboarding_styles', plugin_dir_url(__FILE__) . 'css/onboarding.min.css', array(), EMPOWERCAST_VERSION);
        }
        wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );
    }

    public function router($GET) {
        if(empty($GET['step'])) {
            include 'views/onboarding/start.php';
            marketing_footer(true);
        }
        else {
            switch ($GET['step']) {
                case 'nohost':
                    include 'views/onboarding/nohost.php';
                    break;
                case 'podopsSignin':
                    include 'views/onboarding/podops_signin.php';
                    break;
                case 'podopsSignup':
                    include 'views/onboarding/podops_signup.php';
                    break;
                case 'showBasics':
                    include 'views/onboarding/show_basics.php';
                    break;
                case 'wantStats':
                    include 'views/onboarding/want_stats.php';
                    break;
                default:
                    include 'views/onboarding/start.php';
                    break;
            }
        }
    }
}
