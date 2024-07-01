<style>

</style>

<?php
// empowercastadmin-tools.php

function empowercast_admin_tools(){
    $General = get_option('empowercast_general');

    // If we have empowercast credentials, check if the account has been verified
    $credentials = get_option('empowercast_creds');
    empowercast_check_credentials($credentials);
    wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION);
    ?>

    <h2 class="pp-page-header"><?php echo __('EmpowerCast Tools', 'empowercast'); ?></h2>
    <h3 class="pp-page-h3"><?php echo __('Useful utilities and tools.', 'empowercast'); ?></h3>

    <div class="pp-card-body">
        <!-- Update Plugins Cache -->
        <div class="pp-row pp-tools-row">
            <h3 class="pp-page-h3-bold pp-tools-item">Update Plugins Cache</h3>
            <a href="<?php echo admin_url() . wp_nonce_url("admin.php?page=empowercast/empowercastadmin_tools.php&amp;action=empowercast-clear-update_plugins", 'empowercast-clear-update_plugins'); ?>" title="Clear Plugins Cache"
               class="empowercast_save_button_other pp-tools-button">CLEAR PLUGINS CACHE</a>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">The list of plugins on the plugins page will cache the plugin version numbers for up to 24 hours.
                Click the link above to clear the cache to get the latest versions of plugins listed on your <a href="<?php echo admin_url() . 'plugins.php'?>">plugins</a> page.
            </p>
        </div>

        <hr>

        <!-- Translations -->
        <div class="pp-row pp-tools-row">
            <h3 class="pp-page-h3-bold pp-tools-item">Translations</h3>
            <a href="https://podopshost.com/support/empowercast-documentation/empowercast-language/translate-empowercast/" target="_blank" title="Translate EmpowerCast"
               class="empowercast_save_button_other pp-tools-button">TRANSLATE EMPOWERCAST</a>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">EmpowerCast translations are managed on the official
                <a href="https://translate.wordpress.org/projects/wp-plugins/empowercast/" target="_blank">WordPress translate site</a>.
            </p>
        </div>

        <hr>

        <!-- Media URL Replacement -->
        <div class="pp-row pp-tools-row">
            <h3 class="pp-page-h3-bold pp-tools-item">Media URL Replacement</h3>
            <a href="<?php echo admin_url("admin.php?page=empowercast/empowercastadmin_tools.php&amp;action=empowercast-find-replace"); ?>" title="Find and Replace Media"
               class="empowercast_save_button_other pp-tools-button">REPLACE MEDIA URLS</a>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">Find and replace complete or partial segments of media URLs.
                Useful if you move your media to a new website or service.
            </p>
        </div>

        <hr>

        <!-- Diagnostics -->
        <div class="pp-row pp-tools-row">
            <h3 class="pp-page-h3-bold pp-tools-item">Diagnostics</h3>
            <a href="<?php echo admin_url("admin.php?page=empowercast/empowercastadmin_tools.php&amp;action=empowercast-diagnostics"); ?>" title="Diagnose Your EmpowerCast Installation"
               class="empowercast_save_button_other pp-tools-button">RUN DIAGNOSTICS</a>
        </div>
        <div class="pp-row pp-tools-row">
            <p class="pp-tools-text">The Diagnostics page checks to see if your server is configured to support all the available features in Podops EmpowerCast.</p>
        </div>
    </div>
<?php } ?>