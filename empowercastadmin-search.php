<?php
// empowercastadmin-search.php

function empowercast_admin_search()
{
	$General = empowercast_get_settings('empowercast_general');
	if( empty($General['seo_feed_title']) )
		$General['seo_feed_title'] = '';
	
?>
<script language="javascript"><!--

jQuery(document).ready(function() {
	
<?php  
	
		if( !empty($General['seo_feed_title'])  && $General['seo_feed_title'] == 1 )
		echo "	jQuery('#empowercast_example_post_title').hide();\n";
?>
	jQuery('#seo_feed_title').change( function() {
		if( this.checked )
			jQuery('#empowercast_seo_feed_title_1').prop('checked', true);
		else
			jQuery('.empowercast_seo_feed_title').prop('checked', false);
	});
	jQuery('.empowercast_seo_feed_title').change( function() {
		
		jQuery('#seo_feed_title').prop('checked', true);
		switch( this.value )
		{
			case '1':
			case 1: {
				jQuery('#empowercast_example_post_title').hide();
			}; break;
			case '2':
			case 2: {
				jQuery('#empowercast_example_post_title').show();
				
				var p_title_html = jQuery('#empowercast_example_post_title')[0].outerHTML;
				var e_title_html = jQuery('#empowercast_example_episode_title')[0].outerHTML;
				jQuery('#empowercast_example_post_episode_title').html( e_title_html + p_title_html);
			}; break;
			case '3':
			case 3: {
				jQuery('#empowercast_example_post_title').show();
				
				var p_title_html = jQuery('#empowercast_example_post_title')[0].outerHTML;
				var e_title_html = jQuery('#empowercast_example_episode_title')[0].outerHTML;
				jQuery('#empowercast_example_post_episode_title').html( p_title_html + e_title_html);
			}; break;
			default: {
				
			}
		}
	});
});
//-->
</script>
<h1 class="pp-heading"><?php echo __('Podcast SEO', 'empowercast'); ?></h1>
<p class="pp-settings-text">
    <?php echo __('Enable features to help with podcasting search engine optimization (SEO). The following options can assist your web and podcasting SEO strategies.', 'empowercast'); ?>
	<a href="https://podopshost.com/support/empowercast-documentation/podcasting-seo-settings/"  target="_blank"><?php echo __('Learn More', 'empowercast'); ?></a>
</p>


<div class="pp-settings-section">
    <h2><?php echo __('Episode Titles', 'empowercast'); ?></h2>
	<input name="EmpowerCastSearchToggle[seo_feed_title]" type="hidden" value="0" />
	<input class="pp-settings-checkbox" id="seo_feed_title" name="EmpowerCastSearchToggle[seo_feed_title]" type="checkbox" value="1" <?php if( !empty($General['seo_feed_title']) ) echo 'checked '; ?> />
    <div class="pp-settings-subsection-no-border" style="padding-left: 1ch;">
        <p class="pp-main">
        <?php echo __('Specify custom episode titles for podcast feeds.', 'empowercast'); ?>
	    </p>
        <div>
            <input class="pp-settings-radio-small" type="radio" style="margin: 2ch 8px 0 1em;vertical-align: top;" class="empowercast_seo_feed_title" id="empowercast_seo_feed_title_1" name="General[seo_feed_title]" value="1" <?php if( $General['seo_feed_title'] == 1 ) echo 'checked'; ?> />
            <div class="pp-settings-subsection-no-border" style="padding-bottom: 0;">
                <p class="pp-settings-text" style="margin: 0;"><?php echo __('Feed episode title replaces post title', 'empowercast'); ?></p>
                <p class="pp-sub" style="font-size: 14px"><?php echo __('Default', 'empowercast'); ?></p>
            </div>
        </div>
        <div>
            <input class="pp-settings-radio-small" type="radio" style="margin-left: 1em;" class="empowercast_seo_feed_title" id="empowercast_seo_feed_title_2" name="General[seo_feed_title]" value="2" <?php if( $General['seo_feed_title'] == 2 ) echo 'checked'; ?> />
            <div class="pp-settings-subsection-no-border">
                <p class="pp-settings-text" style="margin: 0;"><?php echo __('Feed episode title prefixes post title', 'empowercast'); ?></p>
            </div>
        </div>
        <div>
            <input class="pp-settings-radio-small" type="radio" style="margin-left: 1em;" class="empowercast_seo_feed_title" id="empowercast_seo_feed_title_3" name="General[seo_feed_title]" value="3" <?php if( $General['seo_feed_title'] == 3 ) echo 'checked'; ?> />
            <div class="pp-settings-subsection-no-border">
                <p class="pp-settings-text" style="margin: 0;"><?php echo __('Feed episode title appended to post title', 'empowercast'); ?></p>
            </div>
        </div>
	</div>
</div>

<div class="pp-settings-section">
    <h2><?php echo __('AudioObjects', 'empowercast'); ?></h2>
	<input name="General[seo_audio_objects]" type="hidden" value="0" />
	<input class="pp-settings-checkbox" name="General[seo_audio_objects]" type="checkbox" value="1" <?php if( !empty($General['seo_audio_objects']) ) echo 'checked '; ?> />
    <div class="pp-settings-subsection-no-border" style="padding-left: 1ch;">
        <p class="pp-main"><?php echo __('Schema.org audio objects in microdata format.', 'empowercast'); ?></p>
        <p class="pp-sub"><?php echo __('What this means and why it\'s important', 'empowercast'); ?></p>
    </div>
</div>

<div class="pp-settings-section">
    <h2><?php echo __('VideoObjects', 'empowercast'); ?></h2>
	<input name="General[seo_video_objects]" type="hidden" value="0" />
	<input class="pp-settings-checkbox" name="General[seo_video_objects]" type="checkbox" value="1" <?php if( !empty($General['seo_video_objects']) ) echo 'checked '; ?> />
    <div class="pp-settings-subsection-no-border" style="padding-left: 1ch;">
        <p class="pp-main"><?php echo __('Schema.org video objects in microdata format.', 'empowercast'); ?></p>
        <p class="pp-sub"><?php echo __('What this means and why it\'s important', 'empowercast'); ?></p>
    </div>
</div>

<?php

?>

<?php
} // End empowercast_admin_search()

