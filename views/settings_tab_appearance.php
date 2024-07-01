<?php
function empowercastadmin_website_settings_custom_feed($General, $FeedSettings, $FeedAttribs) {

    $feed_slug = '';
    if( !empty($FeedAttribs['feed_slug']) )
        $feed_slug = $FeedAttribs['feed_slug'];
    ?>

        <h1 class="pp-heading"><?php echo __('Website Settings', 'empowercast'); ?></h1>
        <div class="pp-settings-section">
            <ul>
                <li>
                    <input type="hidden" name="DisablePlayerFor" value="" />
                    <input class="pp-settings-checkbox" name="DisablePlayerFor" type="checkbox" <?php if( isset($General['disable_player'][$feed_slug]) ) echo 'checked '; ?> value="1" />
                    <div class="pp-settings-subsection">
                        <p class="pp-main"><?php echo __('Do not display web player or links for this podcast.', 'empowercast'); ?></p>
                    </div>
                    <input type="hidden" name="UpdateDisablePlayer" value="<?php echo $feed_slug; ?>" />
                </li>
            </ul>
        </div>

    <h1 class="pp-heading" style="margin-bottom: 1ch;"><?php echo __('Subscribe Page', 'empowercast'); ?></h1>
    <div>
        <p class="pp-settings-text"><?php echo __('Add a link to a page to explain to your audience how to subscribe to your podcast.', 'empowercast'); ?></p>
        <p class="pp-main" style="margin-bottom: 2em;"><?php echo __('The following link will be added to the Subscribe on Apple and Subscribe via RSS links below the player.', 'empowercast'); ?></p>

        <?php wp_dropdown_pages( array( 'id'=>'subscribe_page_link_id', 'name' => 'Feed[subscribe_page_link_id]', 'class' => 'pp-settings-select-inline', 'echo' => 1, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '', 'selected' => $FeedSettings['subscribe_page_link_id'] ) ); ?>
        <div id="subscribe_page_link_or" style="<?php echo ( !empty($FeedSettings['subscribe_page_link_id']) ?'display: none;':'display: inline-block;'); ?>">
            <p class="pp-main" style="display: inline-block; vertical-align: text-bottom; margin-left: 2ch; margin-right: 2ch;"><?php echo __(' - or - ', 'empowercast'); ?></p>
            <div style="display: inline-block; vertical-align: middle;">
                <input type="text" id="subscribe_page_link_href" class="pp-settings-text-input" value="<?php echo !empty($FeedSettings['subscribe_page_link_href']) ? esc_attr($FeedSettings['subscribe_page_link_href']) : ''; ?>" name="Feed[subscribe_page_link_href]" placeholder="Subscribe URL"<?php echo (!empty($FeedSettings['subscribe_page_link_id'])?'':' disabled'); ?> />
                <label for="subscribe_page_link_href" class="pp-settings-label-under"><?php echo __('(If subscribe page is not hosted on this site)', 'empowercast'); ?></label>
            </div>
        </div><!-- end subscribe_page_link_or -->

        <?php
        if( empty($FeedAttribs) && empty($FeedSettings['subscribe_page_link_href']) && empty($FeedSettings['subscribe_page_link_id']) )
        {
        ?>
        <h3 class="pp-main" style="text-decoration: underline;margin-top: 1em;margin-bottom: 2em;"><a href="#" id="empowercast_create_subscribe_page"><?php echo __('Create a subscribe page from Template', 'empowercast'); ?></a></h3>
        <p class="pp-settings-text"><?php echo __('Creates a page from a template with the [empowercast_subscribe] shortcode. We encourage you to edit this page in your own words. Depending on your SEO strategy, you may want to configure the meta robots content to noindex.', 'empowercast'); ?>
        </p>
        <?php
        }
        ?>
        <p class="pp-settings-text"><a href="https://podopshost.com/support/empowercast-documentation/subscribe-page/" target="_blank"><?php echo __('Learn more about the EmpowerCast Subscribe Page', 'empowercast'); ?></a></p>
        <?php
        // TODO: use the $FeedAttribs to create a recommended shortcode for this particular channel, may be simple [empowercast_subscribe] or it may specify the category, taxonomy, and/or feed_slug/post tpe podcasting
        ?>
        <label for="subscribe_page_link_text" class="pp-settings-label"><?php echo __('Subscribe Page Link Label:', 'empowercast'); ?></label>
        <input type="text" id="subscribe_page_link_text" class="pp-settings-text-input" value="<?php echo !empty($FeedSettings['subscribe_page_link_text']) ? esc_attr($FeedSettings['subscribe_page_link_text']) : ''; ?>" name="Feed[subscribe_page_link_text]" placeholder="" />
        <label for="subscribe_page_link_text" class="pp-settings-label-under"><?php echo __('(leave blank for default)', 'empowercast'); ?></label>

    </div>

    <?php
	}


function empowercastadmin_website_settings($General, $Feed) {
    ?>

        <h1 class="pp-heading"><?php echo __('Website Settings', 'empowercast'); ?></h1>
        <div class="pp-settings-section">
            <ul>
                <li>
                    <input class="pp-settings-radio" type="radio" name="General[disable_appearance]" value="0" <?php if( $General['disable_appearance'] == 0 ) echo 'checked'; ?> onclick="javascript: jQuery('#presentation_settings').css('display', (this.checked?'block':'none') );" />
                    <div class="pp-settings-subsection">
                        <p class="pp-main"><?php echo __('Enable EmpowerCast Media Players and Links', 'empowercast'); ?>(<?php echo __('default', 'empowercast'); ?>)</p>
                        <p class="pp-sub"><?php echo __('EmpowerCast will add media players and links to your site.', 'empowercast'); ?></p>
                    </div>
                </li>
                <li>
                    <input class="pp-settings-radio" type="radio" name="General[disable_appearance]" value="1" <?php if( $General['disable_appearance'] == 1 ) echo 'checked'; ?> onclick="javascript: jQuery('#presentation_settings').css('display', (this.checked?'none':'block') );" />
                    <div class="pp-settings-subsection">
                        <p class="pp-main"><?php echo __('Disable EmpowerCast Media Players and Links', 'empowercast'); ?></p>
                        <p class="pp-sub"><?php echo __('EmpowerCast will <u>not</u> add any media players or media links to your site. EmpowerCast will only be used to add podcasting support to your feeds.', 'empowercast'); ?></p>
                    </div>
                </li>
            </ul>
        </div>
            <?php


            global $wp_rewrite;
            if( $wp_rewrite->permalink_structure ) // Only display if permalinks is enabled in WordPress
            {
                ?>
                <div class="pp-settings-section">
                    <h2><?php echo __('Permalinks', 'empowercast'); ?></h2>
                    <select name="General[permalink_feeds_only]" class="pp-settings-select">
                        <?php
                        $options = array(0=>__('Default WordPress Behavior', 'empowercast'), 1=>__('Match Feed Name to Page/Category', 'empowercast') );
                        $current_value = (!empty($General['permalink_feeds_only'])?$General['permalink_feeds_only']:0);

                        foreach( $options as $value => $desc )
                            echo "\t<option value=\"$value\"". ($current_value==$value?' selected':''). ">$desc</option>\n";

                        ?>
                    </select>
                    <label class="pp-settings-label-under" for="General[permalink_feeds_only]"><?php echo sprintf(__('When configured, %s/podcast/ is matched to page/category named \'podcast\'.', 'empowercast'), get_bloginfo('url') ); ?></label>
                </div>
                <?php
            }
            ?>
        <div class="pp-settings-section">
            <h2><?php echo __('Episode Box Appearance', 'empowercast'); ?></h2>
            <ul>
                <li>
                    <input class="pp-settings-checkbox" type="checkbox" name="General[skip_to_episode_settings]" value="2" <?php if( isset($General['skip_to_episode_settings']) && $General['skip_to_episode_settings'] ) echo 'checked '; ?>/>
                    <div class="pp-settings-subsection">
                        <p class="pp-main"><?php echo __('Check this box to display all episode settings before the media is verified.', 'empowercast'); ?></p>
                        <p class="pp-sub"><?php echo __('If this setting is selected, you risk losing all your entered data if you try to publish an episode that has no media attached. This setting should only be enabled by very experienced EmpowerCast users.', 'empowercast'); ?></p>
                    </div>
                </li>
            </ul>
        </div>
    <?php
}

function empowercastadmin_blog_settings($General, $Feed) { ?>



        <h1 class="pp-heading"><?php echo __('Blog Posts and Pages', 'empowercast'); ?></h1>


                <div class="pp-settings-section">
                    <h2><?php echo htmlspecialchars(__('Display Media & Links', 'empowercast')); ?></h2>
                    <ul>
                        <li>
                            <input class="pp-settings-radio" type="radio" name="General[display_player]" value="1" <?php if( $General['display_player'] == 1 ) echo 'checked'; ?> />
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Below page content', 'empowercast'); ?> (<?php echo __('default', 'empowercast'); ?>)</p>
                                <p class="pp-sub"><?php echo __('Player and media links will appear below your post and page content.', 'empowercast'); ?></p>
                            </div>
                        </li>
                        <li>
                            <input class="pp-settings-radio" type="radio" name="General[display_player]" value="2" <?php if( $General['display_player'] == 2 ) echo 'checked'; ?> />
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Above page content', 'empowercast'); ?></p>
                                <p class="pp-sub"><?php echo __('Player and media links will appear above your post and page content.', 'empowercast'); ?></p>
                            </div>
                        </li>
                        <li>
                            <input class="pp-settings-radio" type="radio" name="General[display_player]" value="0" <?php if( $General['display_player'] == 0 ) echo 'checked'; ?> />
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Disable', 'empowercast'); ?></p>
                                <p class="pp-sub"><?php echo __('Player and media links will NOT appear in your post and page content. Media player and links can be added manually by using the <i>shortcode</i> below.', 'empowercast'); ?></p>
                            </div>
                        </li>
                        <li>
                            <input class="pp-settings-checkbox" name="General[display_player_excerpt]" type="checkbox" value="1" <?php if( !empty($General['display_player_excerpt']) ) echo 'checked '; ?>/>
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Display player / links:', 'empowercast'); ?> <a href="http://codex.wordpress.org/Template_Tags/the_excerpt" title="<?php echo __('WordPress Excerpts', 'empowercast'); ?>" target="_blank"><?php echo __('WordPress Excerpts', 'empowercast'); ?></a></p>
                                <p class="pp-sub">(<?php echo __('e.g. search results', 'empowercast'); ?>)</p>
                            </div>
                        </li>
                        <li>
                            <input type="hidden" name="General[hide_player_more]" value="0" />
                            <input class="pp-settings-checkbox" name="General[hide_player_more]" type="checkbox" value="1" <?php if( !empty($General['hide_player_more']) ) echo 'checked '; ?>/>
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Hide player / links:', 'empowercast'); ?> <a href="https://en.support.wordpress.com/more-tag/" title="<?php echo __('Read More tagged posts', 'empowercast'); ?>" target="_blank"><?php echo __('Read More tagged posts', 'empowercast'); ?></a></p>
                            </div>
                        </li>
                    </ul>
                </div>


                <div class="pp-settings-section">
                    <h2><?php echo __('Player Settings', 'empowercast'); ?></h2>
                    <ul>
                        <li>
                            <input class="pp-settings-checkbox" type="checkbox" name="PlayerSettings[display_media_player]" value="2" <?php if( $General['player_function'] == 1 || $General['player_function'] == 2 ) echo 'checked '; ?>/>
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Display Player', 'empowercast'); ?></p>
                                <?php /* ?>
            <p style="margin-left: 35px;"><input type="checkbox" name="General[display_player_disable_mobile]" value="1" <?php if( !empty($General['display_player_disable_mobile']) ) echo 'checked '; ?>/> <?php echo __('Disable Media Player for known mobile devices.', 'empowercast'); ?></p>
            <?php */ ?>
                                <p class="pp-sub"><?php echo __('Whether or not the player is displayed.', 'empowercast'); ?></p>
                            </div>
                        </li>
                    </ul>
                </div>


                <div class="pp-settings-section">
                    <h2><?php echo __('Media Links', 'empowercast'); ?></h2>
                    <ul>
                        <li>
                            <input class="pp-settings-checkbox" type="checkbox" name="PlayerSettings[display_pinw]" value="3" <?php if( $General['player_function'] == 3 || $General['player_function'] == 1 ) echo 'checked '; ?>/>
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Display Play in new Window Link', 'empowercast'); ?></p>
                            </div>
                        </li>
                        <li>
                            <input class="pp-settings-checkbox" type="checkbox" name="PlayerSettings[display_download]" value="1" <?php if( $General['podcast_link'] != 0 ) echo 'checked '; ?>/>
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Display Download Link', 'empowercast'); ?></p>
                                <p class="pp-settings-text" style="margin: 1em 0 1em 0">
                                    <input class="round-checkbox" type="checkbox" id="display_download_size" name="PlayerSettings[display_download_size]" value="1" <?php if( $General['podcast_link'] == 2 || $General['podcast_link'] == 3 ) echo 'checked'; ?> onclick="if( this.checked ) { jQuery('#display_download_duration').removeAttr('checked'); }" />
                                    <?php echo __('Include file size', 'empowercast'); ?>
                                    <input class="round-checkbox" type="checkbox" style="margin-left: 30px;" id="display_download_duration" name="PlayerSettings[display_download_duration]" value="1" <?php if( $General['podcast_link'] == 3 ) echo 'checked'; ?> onclick="if( this.checked ) { jQuery('#display_download_size').attr('checked','checked'); }" />
                                    <?php echo __('Include file size and duration', 'empowercast'); ?>
                                </p>
                            </div>
                        </li>
                        <li>
                            <input class="pp-settings-checkbox" type="checkbox" name="General[podcast_embed]" value="1" <?php if( !empty($General['podcast_embed']) ) echo 'checked '; ?>/>
                            <div class="pp-settings-subsection">
                                <p class="pp-main"><?php echo __('Display Player Embed Link', 'empowercast'); ?></p>
                                <p class="pp-settings-text" style="margin: 1em 0 1em 0">
                                    <input class="round-checkbox" type="checkbox" name="General[podcast_embed_in_feed]" value="1" <?php if( !empty($General['podcast_embed_in_feed']) ) echo 'checked'; ?>  /> <?php echo __('Include embed in feeds', 'empowercast'); ?>
                                </p>
                                <p class="pp-label-bottom"><?php echo __('Embed option works with the MediaElement.js Media Player for audio and video, Flow Player Classic for audio and HTML5 Video player for video.', 'empowercast'); ?></p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="pp-settings-section">
                    <h2><?php echo __('Subscribe Links Under Player', 'empowercast'); ?> </h2>
                    <input style="margin: 0.25ch 4ch 4ch 3ch;" class="pp-settings-checkbox" type="checkbox" name="General[subscribe_links]" value="1" <?php if( $General['subscribe_links'] == 1 ) echo 'checked '; ?>/>
                    <div style="display: inline-block;">
                    <p class="pp-main"><?php echo __('Display subscribe links below player and media links.', 'empowercast'); ?></p>
                    </div>

                    <input class="pp-settings-text-input" type="text" id="subscribe_label" value="<?php echo esc_attr($General['subscribe_label']); ?>" name="General[subscribe_label]" placeholder="Subscribe Label" />
                    <label for="subscribe_label" class="pp-settings-label-under"><?php echo __('Leave blank to use default', 'empowercast'); ?></label>

                    <br /><br />
                </div>

    <?php
}

function empowercastadmin_new_window_settings($General, $Feed) { ?>

        <h1 class="pp-heading"><?php echo __('Play in New Window Settings', 'empowercast'); ?></h1>

        <div style="margin-top: 2em;">
            <label for="General[new_window_width]" class="pp-settings-label"><?php echo __('Width of new window', 'empowercast'); ?></label>
            <input class="pp-settings-text-input" type="text" name="General[new_window_width]" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo esc_attr($General['new_window_width']); ?>" maxlength="4" />
            <label for="General[new_window_width]" class="pp-settings-label-under"><?php echo __('Width of new window (leave blank for 420 default)', 'empowercast'); ?></label>
        </div>

        <div style="margin: 2em 0;">
            <label for="General[new_window_height]" class="pp-settings-label"><?php echo __('Height of new window', 'empowercast'); ?></label>
            <input class="pp-settings-text-input" type="text" name="General[new_window_height]" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo esc_attr($General['new_window_height']); ?>" maxlength="4" />
            <label for="General[new_window_height]" class="pp-settings-label-under"><?php echo __('Height of new window (leave blank for 240 default)', 'empowercast'); ?></label>
        </div>

        <div style="margin: 2em 0;">
            <input style="margin: 0.5ch 2ch 0 0;" class="pp-settings-checkbox" type="checkbox" name="General[new_window_nofactor]" value="1" <?php if( !empty($General['new_window_nofactor']) ) echo 'checked'; ?>  />
            <div style="display: inline-block; width: 90%;">
                <p class="pp-main"><?php echo __('Do not factor in scroll bars', 'empowercast'); ?></p>
                <p class="pp-label-bottom"><?php echo __('By default, EmpowerCast adds to the width and height above to compensate for possible vertical and horizontal scroll bars. Check this option if you do not want EmpowerCast to compensate for browser scroll bars.', 'empowercast'); ?></p>
            </div>
        </div>

    <?php
}

function empowercast_subscribe_settings($General, $FeedSettings) {

    if( $FeedSettings === false )
        $FeedSettings = empowercast_get_settings('empowercast_feed');
    if( empty($FeedSettings) )
        $FeedSettings = array();

    if( !isset($FeedSettings['subscribe_page_link_href']) )
        $FeedSettings['subscribe_page_link_href'] = '';
    if( !isset($FeedSettings['subscribe_page_link_id']) )
        $FeedSettings['subscribe_page_link_id'] = '';
    if( !isset($FeedSettings['subscribe_page_link_text']) )
        $FeedSettings['subscribe_page_link_text'] = '';

    ?>

<h1 class="pp-heading" style="margin-bottom: 1ch;"><?php echo __('Subscribe Page', 'empowercast'); ?></h1>
<div>
	<p class="pp-settings-text"><?php echo __('Add a link to a page to explain to your audience how to subscribe to your podcast.', 'empowercast'); ?></p>
	<p class="pp-main" style="margin-bottom: 2em;"><?php echo __('The following link will be added to the Subscribe on Apple and Subscribe via RSS links below the player.', 'empowercast'); ?></p>

	<?php wp_dropdown_pages( array( 'id'=>'subscribe_page_link_id', 'name' => 'Feed[subscribe_page_link_id]', 'class' => 'pp-settings-select-inline', 'echo' => 1, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '', 'selected' => $FeedSettings['subscribe_page_link_id'] ) ); ?>
	<div id="subscribe_page_link_or" style="<?php echo ( !empty($FeedSettings['subscribe_page_link_id']) ?'display: none;':'display: inline-block;'); ?>">
        <p class="pp-main" style="display: inline-block; vertical-align: text-bottom; margin-left: 2ch; margin-right: 2ch;"><?php echo __(' - or - ', 'empowercast'); ?></p>
        <div style="display: inline-block; vertical-align: middle;">
            <input type="text" id="subscribe_page_link_href" class="pp-settings-text-input" value="<?php echo esc_attr($FeedSettings['subscribe_page_link_href']); ?>" name="Feed[subscribe_page_link_href]" placeholder="Subscribe URL"<?php echo (!empty($FeedSettings['subscribe_page_link_id'])?'':' disabled'); ?> />
            <label for="subscribe_page_link_href" class="pp-settings-label-under"><?php echo __('(If subscribe page is not hosted on this site)', 'empowercast'); ?></label>
        </div>
	</div><!-- end subscribe_page_link_or -->
	
<?php
		//if( empty($FeedAttribs) && empty($FeedSettings['subscribe_page_link_href']) && empty($FeedSettings['subscribe_page_link_id']) )
		//{
?>
	<h3 class="pp-main" style="text-decoration: underline;margin-top: 1em;margin-bottom: 2em;"><a href="#" id="empowercast_create_subscribe_page"><?php echo __('Create a subscribe page from Template', 'empowercast'); ?></a></h3>
	<p class="pp-settings-text"><?php echo __('Creates a page from a template with the [empowercast_subscribe] shortcode. We encourage you to edit this page in your own words. Depending on your SEO strategy, you may want to configure the meta robots content to noindex.', 'empowercast'); ?>
	</p>
<?php
		//}
?>
	<p class="pp-settings-text"><a href="https://podopshost.com/support/empowercast-documentation/subscribe-page/" target="_blank"><?php echo __('Learn more about the EmpowerCast Subscribe Page', 'empowercast'); ?></a></p>
	<?php
	// TODO: use the $FeedAttribs to create a recommended shortcode for this particular channel, may be simple [empowercast_subscribe] or it may specify the category, taxonomy, and/or feed_slug/post tpe podcasting
	?>
    <label for="subscribe_page_link_text" class="pp-settings-label"><?php echo __('Subscribe Page Link Label:', 'empowercast'); ?></label>
    <input type="text" id="subscribe_page_link_text" class="pp-settings-text-input" value="<?php echo esc_attr($FeedSettings['subscribe_page_link_text']); ?>" name="Feed[subscribe_page_link_text]" placeholder="" />
    <label for="subscribe_page_link_text" class="pp-settings-label-under"><?php echo __('(leave blank for default)', 'empowercast'); ?></label>

</div>
<h1 class="pp-heading" style="margin-top: 1em;"><?php echo __('Subscribe Widget', 'empowercast'); ?></h1>
<div>
        <p class="pp-settings-text" style="margin-top: 1em;"><?php echo __('Select the appearance of the subscribe buttons used on your subscribe page', 'empowercast'); ?></p>
			<?php
				if( empty($General['subscribe_widget_shape']) ) {
					$General['subscribe_widget_shape'] = 'squared'; // Default value
				}
			?>
                <div class="pp-settings-section">
                    <h2><?php esc_html_e( 'Shape' , 'empowercast'); ?></h2>
                    <input type="radio" id="subscribe_widget_shape_squared" class="pp-settings-radio-small"
                           name="General[subscribe_widget_shape]" value="squared" <?php echo $General['subscribe_widget_shape'] === 'squared' ? 'checked' : '' ?> />
                    <p class="pp-settings-text" style="display: inline-block;"> <?php esc_html_e('Squared', 'empowercast'); ?></p>
                    <br />
                    <input type="radio" id="subscribe_widget_shape_rounded" class="pp-settings-radio-small"
                           name="General[subscribe_widget_shape]" value="rounded" <?php echo $General['subscribe_widget_shape'] === 'rounded' ? 'checked' : '' ?> />
                    <p class="pp-settings-text" style="display: inline-block;"> <?php esc_html_e('Rounded', 'empowercast'); ?></p>
                </div>
                <?php
                if( empty($General['subscribe_no_important_styling']) )
                    $General['subscribe_no_important_styling'] = 'include';

                ?>
                <div class="pp-settings-section">
                    <h2><?php esc_html_e( 'Custom Styling' , 'empowercast'); ?></h2>
                    <select id="subscribe_no_important_styling" class="pp-settings-select" name="General[subscribe_no_important_styling]">
                        <option value="include" <?php echo selected($General['subscribe_no_important_styling'], 'include') ?> ><?php esc_html_e( 'Include recommended styling (recommended)' , 'empowercast'); ?></option>
                        <option value="exclude" <?php echo selected($General['subscribe_no_important_styling'], 'exclude')?> ><?php esc_html_e( 'Exclude recommended styling' , 'empowercast'); ?></option>
                    </select>
                    <p class="description pp-settings-text">
                        <?php esc_html_e( 'Select Exclude to remove !important and text align left CSS styling.' , 'empowercast'); ?>
                        <br />
                        <?php esc_html_e( 'These rules may interfere with custom styling or styling from other plugins.' , 'empowercast'); ?>
                    </p>
                </div>
                <div class="pp-settings-section">
                    <h2><?php esc_html_e( 'Susbcribe Button Preview' , 'empowercast'); ?></h2>
                    <iframe id="btnPreview" title="<?php echo esc_attr(__('Subscribe Button Preview', 'empowercast')); ?>" style="height: 127px;width: 400px;" src="<?php echo admin_url('admin.php?action=empowercast-jquery-subscribe-preview&style=modern&shape='.$General['subscribe_widget_shape']) ?>"></iframe>
                </div>

</div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', ()=> {
            let baserUrl = "<?php echo admin_url('admin.php?action=empowercast-jquery-subscribe-preview'); ?>";
            const previewIframe = document.querySelector('#btnPreview');
            const styleSelector = document.querySelector('#subscribe_widget_style');
            const squaredRadio = document.querySelector('#subscribe_widget_shape_squared');
            const roundedRadio = document.querySelector('#subscribe_widget_shape_rounded');
            let selectedShape = '';
            if (squaredRadio.checked) {
                selectedShape = 'squared';
            }
            function refreshIframe() {
                previewIframe.src = `${baserUrl}&style=${styleSelector.value}&shape=${selectedShape}`
            }
            if (styleSelector) {
                styleSelector.addEventListener('change', refreshIframe);
                squaredRadio.addEventListener('change', () => {
                    selectedShape = 'squared';
                    refreshIframe();
                });
                roundedRadio.addEventListener('change', () => {
                    selectedShape = '';
                    refreshIframe();
                });
            }
        });
    </script>
<?php
}

function empowercast_shortcode_settings($GeneralSettings, $FeedAttribs) {
	// Display the shortcodes!
    $feed_slug = '';
    if( !empty($FeedAttribs['feed_slug']) )
        $feed_slug = $FeedAttribs['feed_slug'];

	$shortcode = array();
	$shortcode['empowercast'] = '[empowercast]';
	$shortcode['empowercast_playlist'] = '[empowercast_playlist]';
	$shortcode['empowercast_subscribe'] = '[empowercast_subscribe]';
	if( !empty($feed_slug) && $feed_slug != 'podcast' )
	{
		$shortcode['empowercast'] = '[empowercast channel="'.$feed_slug.'"]';
		$shortcode['empowercast_playlist'] = '[empowercast_playlist channel="'.$feed_slug.'"]';
		$shortcode['empowercast_subscribe'] = '[empowercast_subscribe channel="'.$feed_slug.'"]';
	}
	if( !empty($FeedAttribs['post_type']) )
	{
		$shortcode['empowercast'] = '[empowercast channel="'.$feed_slug.'" post_type="'.$FeedAttribs['post_type'].'"]';
		$shortcode['empowercast_playlist'] = '[empowercast_playlist channel="'.$feed_slug.'" post_type="'.$FeedAttribs['post_type'].'"]';
		$shortcode['empowercast_subscribe'] = '[empowercast_subscribe channel="'.$feed_slug.'" post_type="'.$FeedAttribs['post_type'].'"]';
	}
	if( !empty($FeedAttribs['category_id']) )
	{
		$shortcode['empowercast_playlist'] = '[empowercast_playlist category="'.$FeedAttribs['category_id'].'"]';
		$shortcode['empowercast_subscribe'] = '[empowercast_subscribe category="'.$FeedAttribs['category_id'].'"]';
	}
	if( !empty($FeedAttribs['term_taxonomy_id']) )
	{
		$shortcode['empowercast_playlist'] = '[empowercast_playlist term_taxonomy_id="'.$FeedAttribs['term_taxonomy_id'].'" post_type="post"]';
		$shortcode['empowercast_subscribe'] = '[empowercast_subscribe term_taxonomy_id="'.$FeedAttribs['term_taxonomy_id'].'"]';
	}
	
?>
<div>
<h1 class="pp-heading"><?php echo __('EmpowerCast Player Shortcode', 'empowercast'); ?></h1>
<div class="pp-shortcode-container">
    <b><?php echo '<code class="pp-shortcode">'.$shortcode['empowercast'].'</code>'; ?></b>
</div>
<p class="pp-settings-text">
<?php echo __('The Player shortcode is used to position your media presentation (player and download links) exactly where you want within your post or page content.', 'empowercast'); ?> 
</p>
<p class="pp-settings-text">
<?php echo sprintf(__('Please visit the %s page for additional options.', 'empowercast'), '<a href="https://podopshost.com/support/empowercast-documentation/shortcode/" target="_blank">'. __('EmpowerCast Player Shortcode', 'empowercast') .'</a>' ); ?>
</p>
<p class="pp-label-bottom" style="font-size: 12px;">
<?php echo __('Note: When specifying a URL to media in the empowercast shortcode, only the player is included. The Media Links will NOT be included since there is not enough meta information to display them.', 'empowercast'); ?>
</p>
<h1 class="pp-heading"><?php echo __('EmpowerCast Playlist Shortcode', 'empowercast'); ?></h1>
<?php if( empty($GeneralSettings['playlist_player']) ) { // Either not set or set on  
?>
<p class="pp-main" style="margin: 3ch 0 2ch 0;">
	<input style="margin: 3px 2ch 0 0;" class="pp-settings-checkbox" type="checkbox" name="General[playlist_player]" value="1" />
	<?php echo __('Enable EmpowerCast Playlist Player', 'empowercast'); ?>
</p>
<?php } ?>
<div class="pp-shortcode-container">
    <b><?php echo '<code class="pp-shortcode">'.$shortcode['empowercast_playlist'].'</code>'; ?></b>
</div>
<p class="pp-settings-text">
<?php echo __('The Playlist shortcode is used to display a player with a playlist of your podcast episodes. It utilizes the default playlist built into WordPress.', 'empowercast'); ?> 
</p>
<p class="pp-settings-text">
<?php echo sprintf(__('Please visit the %s page for additional options.', 'empowercast'), '<a href="https://podopshost.com/support/empowercast-documentation/empowercast-playlist-shortcode/" target="_blank">'. __('EmpowerCast Playlist Shortcode', 'empowercast') .'</a>' ); ?>
</p>

<h1 class="pp-heading"><?php echo __('EmpowerCast Subscribe Shortcode', 'empowercast'); ?> </h1>
<div class="pp-shortcode-container">
    <b><?php echo '<code class="pp-shortcode">'.$shortcode['empowercast_subscribe'].'</code>'; ?></b>
</div>
<p class="pp-settings-text">
<?php echo __('The Subscribe shortcode is used to display a subscribe to podcast widget for your podcast. It is intended for use on a custom subscribe page. See the Subscribe Page section below for more details.', 'empowercast'); ?> 
</p>
<p class="pp-settings-text">
<?php echo sprintf(__('Please visit the %s page for additional options.', 'empowercast'), '<a href="https://podopshost.com/support/empowercast-documentation/empowercast-subscribe-shortcode/" target="_blank">'. __('EmpowerCast Subscribe Shortcode', 'empowercast') .'</a>' ); ?>
</p>



<h1 class="pp-heading"><?php echo __('EmpowerCast Skip-to-Position in Player Shortcode', 'empowercast'); ?> </h1>
<div class="pp-shortcode-container">
    <b><?php echo '<code class="pp-shortcode">[skipto time="TIME"]</code>'; ?></b>
</div>
<p class="pp-settings-text">
<?php echo __('The Skip-to-Position in Player shortcode creates a link in your page to skip to the specified position in the player. Time may be entered in seconds, minutes:seconds, or hours:minutes:seconds. A label may be specified by providing text followed by a closing shortcode tag..', 'empowercast'); ?>
</p>
<em><?php echo __('See examples below.', 'empowercast'); ?></em>
<ul style="margin-top: 2em;">
<li>[skipto time="45"] &#8212;  <a href="#" onclick="return false;">0:45</a></li>
<li>[skipto time="45"]Skip intro[/skipto] &#8212; <a href="#" onclick="return false;">Skip intro</a></li>
<li>[skipto time="3:05"] <?php echo __('or', 'empowercast'); ?> [skipto time="185"] &#8212; <a href="#" onclick="return false;">3:05</a></li>
<li>[skipto time="1:23:45"] <?php echo __('or', 'empowercast'); ?> [skipto time="83:45"] <?php echo __('or', 'empowercast'); ?> [skipto time="5025"] &#8212; <a href="#" onclick="return false;">1:23:45</a></li>
</ul>
<p class="pp-settings-text" style="margin-top: 2em;">
<?php echo sprintf(__('Please visit the %s page for additional options.', 'empowercast'), '<a href="https://podopshost.com/support/empowercast-documentation/skip-to-position-in-player/" target="_blank">'. __('EmpowerCast Skip-to-Position in Player Shortcode', 'empowercast') .'</a>' ); ?>
</p>
    <h1 class="pp-heading"><?php echo __('Having Issues?', 'empowercast'); ?></h1>
    <div style="margin-top: 3ch;">
        <select name="General[player_aggressive]" class="pp-settings-select">
            <?php
            $linkoptions = array(0=>__('No, everything is working', 'empowercast'),
                1=>__('Yes, please try to fix', 'empowercast'),
                2=>__('Yes, alternative fix', 'empowercast'),
                3=>__('Yes, excluding excerpts', 'empowercast'),
                4=>__('Yes, wp_head check', 'empowercast') );

            foreach( $linkoptions as $value => $desc )
                echo "\t<option value=\"$value\"". ($GeneralSettings['player_aggressive']==$value?' selected':''). ">$desc</option>\n";

            ?>
        </select>
        <div>
            <label style="margin-right: 40vw;" for="General[player_aggressive]" class="pp-settings-label-under">
                <?php echo __('Use this option if you are having problems with the players not appearing on some or all of your pages.', 'empowercast'); ?>
            </label>
        </div>
        <p class="pp-settings-text" style="margin-top: 3ch;"><a href="https://podopshost.com/support/empowercast-documentation/media-appearance/resolving-plugin-theme-conflict-issues/" target="_blank"><?php echo __('Learn More', 'empowercast'); ?></a></p>
        <p class="pp-settings-text">
            <?php echo __('If the above option fixes the player issues, then you most likely have a conflicting theme or plugin activated. You can verify your theme is not causing the problem by testing your site using the latest default WordPress theme (twentyfourteen). For plugins, disable them one by one until the player re-appears, which indicates the last plugin deactivated caused the conflict.', 'empowercast'); ?>
        </p>
    </div>

</div>
<?php } ?>