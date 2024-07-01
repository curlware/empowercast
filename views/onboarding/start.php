<?php
$Settings = get_option('empowercast_general');
if (!isset($Settings['pp_onboarding_incomplete'])) {
    empowercast_save_settings(array('pp_onboarding_incomplete' => 1, 'empowercast_general'));
}
$pp_nonce = empowercast_login_create_nonce();
?>
<div class="wrap">
    <div class="pp_container">
        <div class="onboarding_header">
            <div class="onboarding-logo-container">
                <img id="podops-logo-onboarding" src="<?php echo empowercast_get_root_url(); ?>images/EmpowerCast_white.svg" alt="" />
            </div>
            <div style="display: inline-block;">
                <h4 style="margin: 0;"><?php echo __('Welcome to EmpowerCast','empowercast'); ?></h4>
                <h5 style="margin: 0;"><?php echo __('Let\'s get started by connecting your podcast. ','empowercast'); ?> <a href="<?php echo esc_attr(add_query_arg( '_wpnonce', $pp_nonce, admin_url("admin.php?page=empowercastadmin_onboarding.php&step=podopsSignin&from=new_post")));?>">Connect Podops Account</a></h5>
            </div>
        </div>
        <hr  class="pp_align-center" />
        <section id="two" class="pp_wrapper">
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="custom-card">
                        <div class="card-body">
                            <a href="<?php echo admin_url("admin.php?page=" . htmlspecialchars($_GET['page']) . "&step=showBasics"); ?>">
                                <button type="button" class="pp_button"><span style="font-size: 16px;font-weight:bold;"><i class="fa-solid fa-microphone-lines"></i><?php echo __('Create Podcast','empowercast'); ?></span></button>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="custom-card">
                        <div class="card-body">
                            <a href="<?php echo $_GET['page'] == 'empowercastadmin_basic' ? admin_url("admin.php?import=empowercast-rss-podcast&from=onboarding") : admin_url("admin.php?import=empowercast-rss-podcast&from=gs"); ?>">
                                <button type="button" class="pp_button" ><span style=" font-size: 16px;font-weight:bold;"><i class="fa-solid fa-rss"></i><?php echo __('Import RSS Feed','empowercast'); ?></span></button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="card custom-card" style="border-radius:10px;">
                        <div class="card-body">
                            <h4 class="card-title fw-bold text-theme">Total Subscriber</h4>
                            <div class="card-text p-2">
                                <i class="fa fa-users card-icon" aria-hidden="true" style="color:#b52e84;font-size:40px;"></i>&nbsp;
                                <span class="fs-1 px-5 text-theme fw-bold" style="padding-left:20px;color:#380c56;font-size:40px;">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="card custom-card"  style="border-radius:10px;">
                        <div class="card-body">
                            <h4 class="card-title fw-bold text-theme">Total Podcast</h4>
                            <div class="card-text p-2">
                                <i class="fa fa-podcast card-icon" aria-hidden="true" style="color:#b52e84;font-size:40px;"></i>
                                <span class="fs-1 px-5 text-theme fw-bold" style="padding-left:20px; color:#380c56;font-size:40px;"> 1</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="card custom-card"  style="border-radius:10px;">
                        <div class="card-body">
                            <h4 class="card-title fw-bold text-theme">Total Sponsors</h4>
                            <div class="card-text p-2">
                                <i class="fa fa-handshake card-icon" aria-hidden="true" style="color:#b52e84;font-size:40px;"></i>
                                <span class="fs-1 px-5 text-theme fw-bold" style="color:#380c56;font-size:40px;">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="card custom-card"  style="border-radius:10px;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-theme">Gratuity Earned</h5>
                            <div class="card-text p-2">
                                <i class="fa-solid fa-dollar-sign card-icon" style="color:#b52e84;font-size:40px;"></i>
                                <span class="fs-1 px-5 text-theme fw-bold"  style="padding-left:20px;color:#380c56;font-size:40px;">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section id="four" class="pp_wrapper">
            <div class="row">
                <div class="col-12 mb-3">
                    <a href="https://podopsstudio.com" target="_blank">
                        <img src="https://podopshost.com/marketing/podops-tri-brand.png" alt="tri brand banner" width="100%" class="rounded">
                    </a>
                </div>
            </div>
        </div>
        

