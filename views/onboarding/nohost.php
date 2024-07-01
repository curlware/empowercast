<?php
$GeneralSettings = empowercast_get_settings('empowercast_general');
$creds = get_option('empowercast_creds');
$page = htmlspecialchars($_GET['page']);
if ((isset($GeneralSettings['podops_auth']) && $GeneralSettings['podops_auth'] != null) || $creds) {
    $next_page = admin_url("admin.php?page=empowercastadmin_basic");
} else {
    $next_page = admin_url("admin.php?page={$page}&step=wantStats");
}
if (isset($_GET['from']) && $_GET['from'] == 'import') {
    $querystring_import = "&from=import";
} else {
    $querystring_import = "";
}
$pp_nonce = empowercast_login_create_nonce();
?>
<style>
    li {
        font-size: 14px;
    }
    li::marker {
        color: #1976d2;
    }
    ul {
        text-align: left;
    }
    a {
        text-decoration: none;
        color:#1976d2;
    }
    img {
        vertical-align: text-bottom;
    }
</style>
<div class="wrap">
    <div class="pp_container">
        <img style="display: inline-block; height: 5em; margin-right: 1em;" src="<?php echo empowercast_get_root_url(); ?>images/onboarding/hosting_icon.png" />
        <div style="display: inline-block; width: 90%; float: right;">
            <h3><?php echo __('Host with Podops', 'empowercast'); ?></h3>
            <h5>
                <span>
                <?php echo __('Don’t know what a podcast host is?', 'empowercast'); ?>
                <a href="https://podopshost.com/manual/internet-media-hosting/">
                    <?php echo __('Learn more', 'empowercast'); ?></a>
                </span>
            </h5>
        </div>
        <hr  class="pp_align-center" />

        <section id="one" class="pp_wrapper" style="margin-top:25px;">
            <div class="pp_inner">

                <div class="pp_flex-grid">
                    <div class="pp_col" style="margin-top: -1px;">
                        <div class="pp_box pp_service-container" style="border: none;">
                            <div class="center" style="padding-left: 3em; padding-top: 1em;">
                                <img src="<?php echo empowercast_get_root_url(); ?>images/onboarding/podops_logo_blue.png" alt="" />
                            </div>
                            <div class="pp_content" style="padding-top: 3ch;">
                                <h3 style="text-align: left;"><?php echo __('Hosting Features', 'empowercast'); ?></h3>
                                <ul class="ul-disc">
                                    <li><?php echo __('Integrate with the EmpowerCast plugin', 'empowercast'); ?></li>
                                    <li><?php echo __('Upload audio directly in your episode', 'empowercast'); ?></li>
                                    <li><?php echo __('Publish your show directly on this website', 'empowercast'); ?></li>
                                    <li><?php echo __('Accessible world-class tech support', 'empowercast'); ?></li>
                                    <li><?php echo __('Standard Statistics included', 'empowercast'); ?></li>
                                    <li><?php echo __('Mobile-ready audio/video player', 'empowercast'); ?></li>
                                    <li><?php echo __('Free file migration', 'empowercast'); ?></li>
                                </ul>
                                <div style="text-align: left;">
                                    <a href="<?php echo esc_attr(add_query_arg( '_wpnonce', $pp_nonce, admin_url("admin.php?page={$page}&step=podopsSignup&onboarding_type=hosting$querystring_import"))); ?>">
                                        <button type="button" class="pp_button"><span><?php echo __('Start Free Trial', 'empowercast'); ?></span></button>
                                    </a>
                                    <a style="margin-left: 3ch;font-size: 14px; color: #747474;" href="<?php echo $next_page; ?>">
                                        <span><?php echo __('No Thanks', 'empowercast'); ?></span>
                                    </a>
                                </div>
                                <div style="text-align: left; margin-top: 1ch;">
                                    <p>
                                        <?php echo __('Already have Podops Hosting?', 'empowercast'); ?>
                                        <a href="<?php echo esc_attr(add_query_arg( '_wpnonce', $pp_nonce, admin_url("admin.php?page={$page}&step=podopsSignin&onboarding_type=hosting$querystring_import"))); ?>">
                                            <?php echo __('Sign In', 'empowercast'); ?>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pp_col">
                        <div class="pp_box" style="border: none; padding-top: 2em;">
                            <div class="pp_fit center">
                                <img src="<?php echo empowercast_get_root_url(); ?>images/onboarding/hero_circle_1.webp" alt="" class="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>