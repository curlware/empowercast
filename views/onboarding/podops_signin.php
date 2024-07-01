<?php
    $Step = 1;
    require_once(EMPOWERCAST_ABSPATH .'/empowercastadmin-auth.class.php');
    $auth = new EmpowerCastAuth();
    add_thickbox();
    $General = empowercast_get_settings('empowercast_general');
    if (!isset($_REQUEST['_wpnonce'])) {
        empowercast_page_message_add_error(__('Invalid link', 'empowercast'));
        empowercast_page_message_print();
        exit;
    }
    if (wp_verify_nonce($_REQUEST['_wpnonce'], 'empowercast-link-podops')) {
        if (isset($_GET['podops_create'])) {
            $path = 'create';
            $actType = '&account_type=2';
        } else {
            $path = 'authorize';
            $actType = '';
        }

        if (!isset($_GET['code']) && !isset($_GET['error']) && !isset($_POST['Settings'])) {
            $result = $auth->getTemporaryCredentials();
            // Okay we got it!
            if ($result !== false && !empty($result['temp_client_id']) && !empty($result['temp_client_secret'])) {
                $state = md5(rand(0, 999999) . time());
                update_option('empowercast_temp_client', array('temp_client_id' => $result['temp_client_id'], 'temp_client_secret' => $result['temp_client_secret'], 'state' => $state));
                $from_string = '';
                if (isset($_GET['from'])) {
                    $from_string = "&from=" . $_GET['from'];
                    if ($_GET['from'] == 'empowercastadmin_basic') {
                        $tab_string = isset($_GET['tab']) ? "&tab=" . htmlspecialchars($_GET['tab']) : "";
                        $sidenav_tab_string = isset($_GET['sidenav-tab']) ? "&sidenav-tab=" . htmlspecialchars($_GET['sidenav-tab']) : "";
                        $from_string .= $tab_string;
                        $from_string .= $sidenav_tab_string;
                    }
                }
                $url_string = "admin.php?page={$_GET['page']}&step=podopsSignin{$from_string}" . (isset($_GET['podops_create']) ? '&podops_create=true' : '');
                $redirect_uri = add_query_arg('_wpnonce', $_REQUEST['_wpnonce'], admin_url($url_string));
                update_option('empowercast_podops_api_redirect_uri', $redirect_uri);
                echo '<script>window.location.href = "' . $auth->getApiUrl() . 'oauth2/' . $path . '?response_type=code&client_id=' . $result['temp_client_id'] . '&client_secret=' . $result['temp_client_secret'] . '&state=' . $state . '&redirect_uri=' . urlencode($redirect_uri) . $actType . '";</script>';
                exit;
            }
        } else if (isset($_GET['code']) || isset($_GET['error'])) {
            if (isset($_GET['error']) && $_GET['error'] == 'consent_required') {
                if (isset($_GET['from']) && $_GET['from'] == 'empowercastadmin_basic') {
                    $tab_string = isset($_GET['tab']) ? "&tab=" . htmlspecialchars($_GET['tab']) : "";
                    $sidenav_tab_string = isset($_GET['sidenav-tab']) ? "&sidenav-tab=" . htmlspecialchars($_GET['sidenav-tab']) : "";
                    echo '<script>window.location.href = "' . admin_url("admin.php?page=" . htmlspecialchars($_GET['from']) . "{$tab_string}{$sidenav_tab_string}") . '";</script>';
                    exit;
                } elseif (isset($_GET['from']) && $_GET['from'] == 'new_post') {
                    echo '<script>window.location.href = "' . admin_url('post-new.php') . '";</script>';
                    exit;
                } elseif (isset($_GET['from']) && $_GET['from'] == 'hosting_plugin') {
                    echo '<script>window.location.href = "' . admin_url('admin.php?page=empowercast-site-setup') . '";</script>';
                    exit;
                } else {
                    echo '<script>window.location.href = "' . admin_url("admin.php?page=" . htmlspecialchars($_GET['page']) . "&step=nohost") . '";</script>';
                    exit;
                }
            } elseif (empty($_GET['state']) || empty($_GET['code'])) {
                empowercast_page_message_add_error(__('An error occurred linking your account. Missing parameters.', 'empowercast'));
            }


            $creds = get_option('empowercast_creds');
            if (!$creds) {
                $tempClient = get_option('empowercast_temp_client');
                if ($_GET['state'] != $tempClient['state']) {
                    empowercast_page_message_add_error(__('An error occurred linking your account. State does not match.', 'empowercast'));
                    return false;
                }
                $redirectUri = get_option('empowercast_podops_api_redirect_uri');

                // Get the client ID for this installation
                $resultClient = $auth->issueClient($_GET['code'], $tempClient['temp_client_id'], $tempClient['temp_client_secret'], $redirectUri);
                if ($resultClient === false || empty($resultClient['client_id']) || empty($resultClient['client_secret'])) {
                    if (!empty($resultClient['error_description']))
                        empowercast_page_message_add_error($resultClient['error_description']);
                    else if (!empty($resultClient['error']))
                        empowercast_page_message_add_error($resultClient['error']);
                    else
                        empowercast_page_message_add_error(__('Error issuing client:', 'empowercast-network') . ' ' . $auth->GetLastError() . $auth->getDebugInfo() . print_r($resultClient, true));
                    empowercast_page_message_print();
                    exit;
                }

                // Get the access and refresh token for this client
                $resultTokens = $auth->getAccessTokenFromCode($_GET['code'], $resultClient['client_id'], $resultClient['client_secret'], $redirectUri);
                if ($resultTokens === false || empty($resultTokens['access_token']) || empty($resultTokens['refresh_token'])) {
                    if (!empty($resultTokens['error_description']))
                        empowercast_page_message_add_error($resultTokens['error_description']);
                    else if (!empty($resultTokens['error']))
                        empowercast_page_message_add_error($resultTokens['error']);
                    else
                        empowercast_page_message_add_error(__('Error retrieving access token:', 'empowercast-network') . ' ' . $auth->GetLastError());
                    empowercast_page_message_print();
                    exit;
                }

                $props = array();
                $props['code'] = $_GET['code'];
                $props['client_id'] = $resultClient['client_id'];
                $props['client_secret'] = $resultClient['client_secret'];
                $props['access_token'] = $resultTokens['access_token'];
                $props['access_expires'] = (time() + $resultTokens['expires_in'] - 10);
                $props['refresh_token'] = $resultTokens['refresh_token'];
                empowercast_save_settings($props, 'empowercast_creds');
            }

            $result = $auth->checkAccountVerified();
            if (isset($result['account_enabled']) && isset($result['account_confirmed'])) {
                if (!$result['account_enabled'] || !$result['account_confirmed']) {
                    $props['account_verified'] = false;
                    empowercast_save_settings($props, 'empowercast_creds');
                    empowercast_check_account_verified_popup(true);
                } else {
                    $props['account_verified'] = true;
                    empowercast_save_settings($props, 'empowercast_creds');
                    $Save = false;
                    $Close = false;
                    $Programs = array();
                    $ProgramHosting = array();
                    $json_data = false;
                    $results_programs = array();
                    $api_url_array = empowercast_get_api_array();
                    $accessToken = empowercast_getAccessToken();

                    $req_url = '/2/service/index.json?cache=' . md5( rand(0, 999) . time() );
                    $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
                    $results_programs = $auth->api($accessToken, $req_url);

                    if (!$results_programs || isset($results_programs['error'])) {
                        empowercast_page_message_add_error(__('Error accessing account: ', 'empowercast') . isset($results_programs['error']) ? $results_programs['error'] : $auth->getLastError());
                    } else {
                        foreach ($results_programs as $null => $row) {
                            $Programs[$row['program_keyword']] = $row['program_title'];
                        }
                    }
                    $from_string = '';
                    if (isset($_GET['from'])) {
                        $from_string = "&from=" . $_GET['from'];
                        if ($_GET['from'] == 'empowercastadmin_basic') {
                            $tab_string = isset($_GET['tab']) ? "&tab={$_GET['tab']}" : "";
                            $sidenav_tab_string = isset($_GET['sidenav-tab']) ? "&sidenav-tab={$_GET['sidenav-tab']}" : "";
                            $from_string .= $tab_string;
                            $from_string .= $sidenav_tab_string;
                        }
                    }
                    wp_enqueue_style('empowercast_onboarding_styles', EMPOWERCAST_ABSPATH . '/css/onboarding.css'); ?>


                    <script>
                        jQuery(document).ready(function() {
                            jQuery('.pp-show-option').click(function(e) {
                                console.log(e.currentTarget.id);
                                jQuery('#pp-show-selected-button').val(e.currentTarget.id);
                                jQuery('.pp-show-option').each(function(index, element) {
                                    if (jQuery(this).hasClass('pp-show-selected')) {
                                        jQuery(this).removeClass('pp-show-selected');
                                    }
                                });
                                jQuery(this).addClass('pp-show-selected');
                            });
                        });
                    </script>
                    <div class="pp_container">
                        <h2><?php echo __('Podops Account Connected', 'empowercast'); ?></h2>
                        <h5 style="margin: 0;"><?php echo __('You can now now able to upload episodes from within WordPress to Podops and view free stats from the WordPress dashboard.', 'empowercast'); ?></h5>
                        <h5 style="margin-top: 0;"><?php echo __('If this is the wrong Podops account,  visit settings to unlink this account.', 'empowercast'); ?></h5>
                        <hr class="pp_align-center"/>

                        <?php empowercast_page_message_print() ?>
                        <h2><?php echo __('Main Show', 'empowercast'); ?></h2>
                        <h5><?php echo __('You have multiple shows in your account. Please select which one you want to be your default show for this website.', 'empowercast'); ?></h5>
                        <div class="pp_flex-grid">
                            <div class="pp_col" style="margin: 0;">
                                <div>
                                    <form action="<?php echo esc_attr(add_query_arg('_wpnonce', $_REQUEST['_wpnonce'], admin_url("admin.php?page={$_GET['page']}&step=podopsSignin{$from_string}"))); ?>"
                                          method="post">
                                        <?php
                                        echo "<div class='pp-show-list'>";
                                        $first_program = false;
                                        foreach ($Programs as $value => $desc) {
                                            if (!$first_program) {
                                                $first_program = $value;
                                                echo "<div id='{$value}' class='pp-show-option pp-show-selected'><span>{$desc}</span></div>";
                                            } else {
                                                echo "<div id='{$value}' class='pp-show-option'><span>{$desc}</span></div>";
                                            }
                                        }
                                        echo '</div>';

                                        if (isset($_GET['from']) && $_GET['from'] == 'import') { ?>
                                            <div class="pp_form-group" style="text-align: center">
                                                <div class="pp_input-field-login" style="display: inline-block">
                                                    <input type="checkbox" id="podops_migrate_option_signin"
                                                           name="Settings[podops_migrate]">
                                                    <label for="podops_migrate_option_signin"><?php echo __('Migrate media from imported feed (only possible with a hosting account)', 'empowercast'); ?></label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="pp-show-list-submit-button">
                                            <button id="pp-show-selected-button" type='submit' name='Settings[podops_program_keyword]' value='<?php echo $first_program; ?>' class='pp_button show_button'>
                                                <span>Save</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                $props['account_verified'] = false;
                empowercast_save_settings($props, 'empowercast_creds');
                empowercast_page_message_add_error(__('Error verifying account: ', 'empowercast') . isset($result['error']) ? $result['error'] : $auth->getLastError());
                empowercast_page_message_print();
            }
            //var_dump($result);
            //exit;

        } else if (isset($_POST['Settings'])) {
            $SaveSettings = $_POST['Settings'];
            $SaveSettings = empowercast_stripslashes($SaveSettings);
            $Save = false;
            $Close = false;
            $Programs = array();
            $ProgramHosting = array();
            $json_data = false;
            $results_programs = array();
            $api_url_array = empowercast_get_api_array();
            $accessToken = empowercast_getAccessToken();
            $req_url = '/2/service/index.json?cache=' . md5( rand(0, 999) . time() );
            $req_url .= (defined('EMPOWERCAST_PODOPS_API_QSA') ? '?' . EMPOWERCAST_PODOPS_API_QSA : '');
            $results = $auth->api($accessToken, $req_url);
            if (isset($results['error'])) {
                $Error = $results['error'];
                if (strstr($Error, __('currently not available', 'empowercast'))) {
                    $Error = __('Unable to find podcasts for this account.', 'empowercast');
                    $Error .= '<br /><span style="font-weight: normal; font-size: 12px;">';
                    $Error .= 'Verify that the email address you enter here matches the email address you used when you listed your podcast on podopshost.com.</span>';
                } else if (preg_match('/No programs found.*media hosting/i', $results['error'])) {
                    $Error .= '<br/><span style="font-weight: normal; font-size: 12px;">';
                    $Error .= 'Service may take a few minutes to activate.</span>';
                }
            } else if (!is_array($results)) {
                $Error = $json_data;
            } else {
                // Get all the programs for this user...
                foreach ($results as $null => $row) {
                    $Programs[$row['program_keyword']] = $row['program_title'];
                    if ($row['hosting'] === true || $row['hosting'] == 'true')
                        $ProgramHosting[$row['program_keyword']] = true;
                    else
                        $ProgramHosting[$row['program_keyword']] = false;
                }

                if (count($Programs) > 0) {
                    global $wpdb;
                    $migrate_string = "";

                    if (isset($SaveSettings['podops_migrate']) && $SaveSettings['podops_migrate']) {
                        $migrate_string = "&migrate=true";
                        $query = "SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE \"%enclosure\"";
                        $results_data = $wpdb->get_results($query, ARRAY_A);
                        if ($results_data) {
                            if (empty($GLOBALS['pp_migrate_media_urls']))
                                $GLOBALS['pp_migrate_media_urls'] = array();
                            foreach ($results_data as $index => $row) {
                                list($url) = @explode("\n", $row['meta_value'], 2);
                                $url = trim($url);
                                $post_id = $row['post_id'];
                                $GLOBALS['pp_migrate_media_urls'][$post_id] = $url;
                            }
                            require_once(EMPOWERCAST_ABSPATH . '/empowercastadmin-migrate.php');

                            $update_option = true;
                            $QueuedFiles = get_option('empowercast_migrate_queued');
                            if (!is_array($QueuedFiles)) {
                                $QueuedFiles = array();
                                $update_option = false;
                            }

                            $add_urls = '';
                            foreach ($GLOBALS['pp_migrate_media_urls'] as $meta_id => $url) {
                                if (empty($QueuedFiles[$meta_id])) { // Add to the array if not already added
                                    $QueuedFiles[$meta_id] = $url;
                                    if (!empty($add_urls)) {
                                        $add_urls .= "\n";
                                    }
                                    $add_urls .= $url;
                                }
                            }
                            powepress_admin_migrate_add_urls($add_urls);
                        }
                    }


                    if (!empty($SaveSettings['podops_program_keyword'])) {
                        empowercast_add_podops_redirect($SaveSettings['podops_program_keyword']);
                        $SaveSettings['podops_hosting'] = $ProgramHosting[$SaveSettings['podops_program_keyword']];
                        if (!is_bool($SaveSettings['podops_hosting'])) {
                            if ($SaveSettings['podops_hosting'] === 'false' || empty($SaveSettings['podops_hosting']))
                                $SaveSettings['podops_hosting'] = false;
                        }

                        $Save = true;
                        $Close = true;
                    } else if (isset($SaveSettings['podops_program_keyword'])) // Present but empty
                    {
                        $Error = __('You must select a program to continue.', 'empowercast');
                    } else if (count($Programs) == 1) {
                        foreach ($Programs as $keyword => $title) {
                            break;
                        }

                        $SaveSettings['podops_program_keyword'] = $keyword;
                        $SaveSettings['podops_hosting'] = $ProgramHosting[$keyword];
                        if (!is_bool($SaveSettings['podops_hosting'])) {
                            if ($SaveSettings['podops_hosting'] === 'false' || empty($SaveSettings['podops_hosting']))
                                $SaveSettings['podops_hosting'] = false;
                        }
                        empowercast_add_podops_redirect($keyword);
                        $Close = true;
                        $Save = true;
                    } else {
                        $Step = 2;
                        $Settings['podops_username'] = $SaveSettings['podops_username'];
                    }
                } else {
                    $Error = __('No podcasts for this account are listed on podopshost.com.', 'empowercast');
                }
            }

            if (isset($Error)) {
                $Error .= '<p style="text-align: center;"><a href="https://podopshost.com/support/empowercast-documentation/services-stats/" target="_blank">' . __('Click Here For Help', 'empowercast') . '</a></p>';
            }
            if ($Save) {
                empowercast_save_settings($SaveSettings);
                if (isset($_GET['from']) && $_GET['from'] == 'empowercastadmin_basic') {
                    $tab_string = isset($_GET['tab']) ? "&tab=" . htmlspecialchars($_GET['tab']) : "";
                    $sidenav_tab_string = isset($_GET['sidenav-tab']) ? "&sidenav-tab=" . htmlspecialchars($_GET['sidenav-tab']) : "";
                    echo '<script>window.location.href = "' . admin_url("admin.php?page=" . htmlspecialchars($_GET['from']) . "{$tab_string}{$sidenav_tab_string}") . '";</script>';
                } elseif (isset($_GET['from']) && $_GET['from'] == 'new_post') {
                    echo '<script>window.location.href = "' . admin_url('post-new.php') . '";</script>';
                } elseif (isset($_GET['from']) && $_GET['from'] == 'hosting_plugin') {
                    echo '<script>window.location.href = "' . admin_url('admin.php?page=empowercast-site-setup') . '";</script>';
                }
                echo '<script>window.location.href = "' . admin_url("admin.php?page=empowercastadmin_basic$migrate_string") . '";</script>';
            }
            // Clear cached statistics
            delete_option('empowercast_stats');

            if (isset($Error))
                empowercast_page_message_add_notice($Error, 'inline', false);


        }
    } else {
        empowercast_page_message_add_error(__('Invalid link', 'empowercast'));
        empowercast_page_message_print();
        exit;
    }
?>