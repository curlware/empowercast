<?php

function empowercastadmin_edit_entry_options($General)
{
    //empowercast_admin_ep_settings_enqueue_scripts();
    if( !isset($General['default_url']) )
        $General['default_url'] = '';
    if( !isset($General['episode_box_mode']) )
        $General['episode_box_mode'] = 0; // Default not set, 1 = no duration/file size, 2 = yes duration/file size (default if not set)
    if( !isset($General['episode_box_embed']) )
        $General['episode_box_embed'] = 0;
    if( !isset($General['set_duration']) )
        $General['set_duration'] = 0;
    if( !isset($General['set_size']) )
        $General['set_size'] = 0;
    if( !isset($General['auto_enclose']) )
        $General['auto_enclose'] = 0;
    if( !isset($General['episode_box_player_size']) )
        $General['episode_box_player_size'] = 0;
    if( !isset($General['episode_box_closed_captioned']) )
        $General['episode_box_closed_captioned'] = 0;
    if( !isset($General['episode_box_order']) )
        $General['episode_box_order'] = 0;
    if( !isset($General['episode_box_feature_in_itunes']) )
        $General['episode_box_feature_in_itunes'] = 0;
    if( !isset($GeneralSettings['new_episode_box_pci']) )
        $GeneralSettings['new_episode_box_pci'] = 0;


    require_once(dirname(__FILE__) . "/../empowercastadmin-epbox-options.php");
    ?>
    <script language="javascript">
        jQuery(document).ready(function() {
            jQuery("body").css("background-color", "white");
            jQuery("body").css("font-family", "Roboto, sans-serif");
        });
    </script>
    <div class="wrap" id="empowercast_settings">
    <form enctype="multipart/form-data" method="POST" action="<?php echo admin_url( 'admin.php'); ?>?action=empowercast-ep-box-options-save"&amp;KeepThis=true&amp;TB_iframe=true&amp;width=600&amp;height=400&amp;modal=false">
        <?php wp_nonce_field('empowercast-edit');
        echo "<div id=\"tab-container-epbox-settings\">";
            echo "<div class=\"pp-tab\" style='border-top: none;'>";
                $titles = array("main" => esc_attr(__("Episode Entry Options", "empowercast")), "permalinks" => esc_attr(__("Permalinks", "empowercast")), "advanced" => esc_attr(__("Advanced Options", "empowercast")));
                echo "<button style='font-size: 80%;width: 25%;' class=\"tablinks active\" id=\"1\" title='{$titles['main']}' onclick=\"empowercast_openTab(event, 'epbox-main')\" >" . esc_html(__($titles['main'], 'empowercast')) . "</button>";
                echo "<button style='font-size: 80%;' class=\"tablinks\" id=\"2\" title='{$titles['permalinks']}' onclick=\"empowercast_openTab(event, 'epbox-permalinks')\">" . esc_html(__($titles['permalinks'], 'empowercast')) . "</button>";
                echo "<button style='font-size: 80%;' class=\"tablinks\" id=\"3\" title='{$titles['advanced']}' onclick=\"empowercast_openTab(event, 'epbox-advanced')\">" . esc_html(__($titles['advanced'], 'empowercast')) . "</button>";
                echo "</div>";
        ?>
            <div id="epbox-main" class="pp-tabcontent active">
                <?php empowercast_epbox_main_tab($General); ?>
            </div>
            <div id="epbox-permalinks" class="pp-tabcontent">
                <?php empowercast_epbox_permalinks_tab($General); ?>
            </div>
            <div id="epbox-advanced" class="pp-tabcontent">
                <?php empowercast_epbox_advanced_tab($General); ?>
            </div>
        </div>
        <p class="submit">
            <input style="margin-left: 30px;" type="submit" name="Submit" id="empowercast_save_button" class="button-primary button-podops" value="<?php echo __('Save Changes', 'empowercast') ?>" />
        </p>
    </form>
    <?php
}

