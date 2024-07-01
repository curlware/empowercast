<?php


function empowercast_admin_import_feed(){
    // If we have empowercast credentials, check if the account has been verified
    $creds = get_option('empowercast_creds');
    empowercast_check_credentials($creds);
    wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );
	?>

    <div class="pp-card-body">
        <div class="pp-row pp-tools-row">
            <h2 class="pp-page-sub-header">Import Podcast</h2>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text" style="margin-bottom: 10px;">Import your podcast including episodes, media files and settings.</p>
        </div>

        <div class="pp-row pp-tools-row">
            <h3>Hosting platforms</h3>
        </div>
        <div class="pp-row pp-tools-row" style="margin-bottom: 40px;">
            <a class="hosting-platform-button" href="<?php echo admin_url("admin.php?import=empowercast-soundcloud-rss-podcast"); ?>">
                <img src="<?php echo empowercast_get_root_url(); ?>images/soundcloud.png" class="hosting-platform-img" alt="<?php echo __('SoundCloud', 'empowercast'); ?>"/>
                <p class="hosting-platform-text">SoundCloud</p>
            </a>
            <a class="hosting-platform-button" href="<?php echo admin_url("admin.php?import=empowercast-libsyn-rss-podcast"); ?>">
                <img style="width: 32%;" src="<?php echo empowercast_get_root_url(); ?>images/libsyn.png" class="hosting-platform-img" alt="<?php echo __('Libsyn', 'empowercast'); ?>"/>
                <p class="hosting-platform-text">LibSyn</p>
            </a>
            <a class="hosting-platform-button" href="<?php echo admin_url("admin.php?import=empowercast-podbean-rss-podcast"); ?>">
                <img style="width: 29%;" src="<?php echo empowercast_get_root_url(); ?>images/podbean.png" class="hosting-platform-img" alt="<?php echo __('PodBean', 'empowercast'); ?>"/>
                <p class="hosting-platform-text">PodBean</p>
            </a>
            <a class="hosting-platform-button" href="<?php echo admin_url("admin.php?import=empowercast-squarespace-rss-podcast"); ?>">
                <img src="<?php echo empowercast_get_root_url(); ?>images/squarespace.png" class="hosting-platform-img" alt="<?php echo __('Squarespace', 'empowercast'); ?>"/>
                <p class="hosting-platform-text">Squarespace</p>
            </a>
            <a class="hosting-platform-button" href="<?php echo admin_url("admin.php?import=empowercast-anchor-rss-podcast"); ?>">
                <img style="width: 31%;" src="<?php echo empowercast_get_root_url(); ?>images/anchor.png" class="hosting-platform-img" alt="<?php echo __('Anchor', 'empowercast'); ?>"/>
                <p class="hosting-platform-text">Anchor</p>
            </a>
            <a class="hosting-platform-button" href="<?php echo admin_url("admin.php?import=empowercast-buzzsprout-rss-podcast"); ?>">
                <img style="width: 31%;" src="<?php echo empowercast_get_root_url(); ?>images/buzzsprout.png" class="hosting-platform-img" alt="<?php echo __('Buzzsprout', 'empowercast'); ?>"/>
                <p class="hosting-platform-text">Buzzsprout</p>
            </a>
            <a class="hosting-platform-button" href="<?php echo admin_url("admin.php?import=empowercast-rss-podcast"); ?>">
                <img style="width: 23%;" src="<?php echo empowercast_get_root_url(); ?>images/rss.png" class="hosting-platform-img" alt="<?php echo __('Anywhere else', 'empowercast'); ?>"/>
                <p class="hosting-platform-text">Anywhere else</p>
            </a>
        </div>

        <div class="pp-row pp-tools-row">
            <h3 style="margin-bottom: 10px;">Migrate Podcast Media</h3>
        </div>

        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">Migrate Media to your Podops Podcast Media Hosting Account.</p>
        </div>

        <div class="pp-row pp-tools-row">
            <a style="margin: 0 0 30px 0;" class="empowercast_save_button_other pp-tools-button" href="<?php echo admin_url("admin.php?page=empowercast/empowercastadmin_migrate.php"); ?>"><?php echo __('MIGRATE MEDIA', 'empowercast'); ?></a>
        </div>
    </div>
<?php }

// eof