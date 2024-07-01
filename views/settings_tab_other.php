<?php
	// settings_tab_other.php

	if( !isset($FeedSettings['redirect']) )
		$FeedSettings['redirect'] = '';
	if( !isset($FeedSettings['premium_label']) )
		$FeedSettings['premium_label'] = '';
	if( !isset($FeedSettings['redirect']) )	
		$FeedSettings['redirect'] = '';
	if( !isset($FeedSettings['redirect2']) )
		$FeedSettings['redirect2'] = '';
 	
	if( !empty($FeedAttribs['type']) && ( ($FeedAttribs['type'] == 'ttid' && version_compare($GLOBALS['wp_version'], 4.5, '>=' ) ) || $FeedAttribs['type'] == 'category' || ($FeedAttribs['type'] == 'channel') || ($FeedAttribs['type'] == 'post_type') )  )
	{
		
?>
    <div class="pp-settings-section">
	    <h2><?php echo __('Media Statistics', 'empowercast'); ?></h2>



	    <h4><?php echo __('STATS PREFIX', 'empowercast'); ?></h4>
        <p class="pp-settings-text-no-margin">
            <?php echo __('Enter your Redirect URL issued by your media statistics service provider below.', 'empowercast'); ?>

<?php if( $FeedAttribs['type'] == 'category' ) { ?>
	<?php echo __('When specified, this will be the only media statistics redirect applied to this category. Please enable Strict Category Podcasting to apply statistics redirect on non category pages.', 'empowercast'); ?>
<?php } else if( $FeedAttribs['type'] == 'ttid' ) { ?>
	<?php echo __('Note: Stats prefix is applied to this podcast feed only. The redirect will NOT apply to pages.', 'empowercast'); ?>
<?php } else if( $FeedAttribs['type'] == 'channel' ) { ?>
	<?php echo __('When specified, this will be the only media statistics redirect applied to this podcast channel.', 'empowercast'); ?>
<?php } else if( $FeedAttribs['type'] == 'post_type' ) { ?>
	<?php echo __('When specified, this will be the only media statistics redirect applied to this podcast post type.', 'empowercast'); ?>
<?php } ?>
        </p>
        <input style="margin-top: 1ch;" type="text" class="pp-settings-text-input" name="Feed[redirect]" value="<?php echo esc_attr($FeedSettings['redirect']); ?>" maxlength="255" />
    </div>
<?php
	}
	
	if( $feed_slug ) // end if category, else channel...
	{
?>
<div class="pp-settings-section" style="height: 14em;">
    <h2><?php echo __('Episode Entry Box', 'empowercast'); ?></h2>

    <h4><?php echo __('BACKGROUND COLOR', 'empowercast'); ?></h4>
    <p class="pp-settings-text-no-margin"><?php echo __('Use a distinctive background color for this podcast channel\'s episode box.', 'empowercast'); ?></p>
    <div style="margin-top: 1ch;">
        <input type="text" class="pp-settings-text-input-less-wide" id="episode_background_color" name="EpisodeBoxBGColor[<?php echo $feed_slug; ?>]" style="width: 50%;margin-right: 40%;display: inherit; border: 1px solid #333333; <?php if( !empty($General['episode_box_background_color'][ $feed_slug ]) ) echo 'background-color: '.$General['episode_box_background_color'][ $feed_slug ]; ?>;" value="<?php if( !empty($General['episode_box_background_color'][ $feed_slug ]) )  echo esc_attr($General['episode_box_background_color'][ $feed_slug ]); ?>" maxlength="10" onblur="jQuery('#episode_background_color').css({'background-color' : this.value });" />
    </div>
    <div>
        <div style="background-color: #FFDFEF;" class="empowercast_color_box" onclick="document.getElementById('episode_background_color').value='#FFDFEF'; jQuery('#episode_background_color').css({'background-color' :'#FFDFEF' });"></div>
        <div style="background-color: #FBECD8;" class="empowercast_color_box" onclick="document.getElementById('episode_background_color').value='#FBECD8'; jQuery('#episode_background_color').css({'background-color' :'#FBECD8' });"></div>
        <div style="background-color: #FFFFCC;" class="empowercast_color_box" onclick="document.getElementById('episode_background_color').value='#FFFFCC'; jQuery('#episode_background_color').css({'background-color' :'#FFFFCC' });"></div>
        <div style="background-color: #DFFFDF;" class="empowercast_color_box" onclick="document.getElementById('episode_background_color').value='#DFFFDF'; jQuery('#episode_background_color').css({'background-color' :'#DFFFDF' });"></div>

        <div style="background-color: #EBFFFF;" class="empowercast_color_box" onclick="document.getElementById('episode_background_color').value='#EBFFFF'; jQuery('#episode_background_color').css({'background-color' :'#EBFFFF' });"></div>
        <div style="background-color: #D9E0EF;" class="empowercast_color_box" onclick="document.getElementById('episode_background_color').value='#D9E0EF'; jQuery('#episode_background_color').css({'background-color' :'#D9E0EF' });"></div>
        <div style="background-color: #EBE0EB;" class="empowercast_color_box" onclick="document.getElementById('episode_background_color').value='#EBE0EB'; jQuery('#episode_background_color').css({'background-color' :'#EBE0EB' });"></div>
    </div>


</div>

<!-- password protected feed option -->

<?php
		if( @$General['premium_caps'] && $feed_slug && $feed_slug != 'podcast' )
		{
?>
<div class="pp-settings-section">
<h2><?php echo __('Password Protect Podcast Channel', 'empowercast'); ?></h2>
<p>
	<?php echo __('Require visitors to have membership to your blog in order to gain access to this channel\'s Premium Content.', 'empowercast'); ?>
</p>
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('Protect Content', 'empowercast'); ?></th>
<td>
	<p style="margin-top: 5px;"><input type="checkbox" name="ProtectContent" value="1" <?php echo ( !empty($FeedSettings['premium']) ?'checked ':''); ?> onchange="empowercast_toggle_premium_content(this.checked);" /> <?php echo __('Require user to be signed-in to access feed.', 'empowercast'); ?></p>
<?php ?>
	<div style="margin-left: 20px; display: <?php echo ( !empty($FeedSettings['premium'])?'block':'none'); ?>;" id="premium_role"><?php echo __('User must have the following capability', 'empowercast'); ?>:
<select name="Feed[premium]" class="pp-settings-select">
<?php
			$caps = empowercast_admin_capabilities();
			$actual_premium_value = 'premium_content';
			if( !empty($FeedSettings['premium']) )
				$actual_premium_value = $FeedSettings['premium'];
			
			echo '<option value="">'.  __('None', 'empowercast') .'</option>';
			foreach( $caps as $value => $desc ) 
				echo "\t<option value=\"$value\"". ($actual_premium_value==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";
?>
</select></div>
</td>
</tr>
</table>
<div id="protected_content_message" style="display: <?php echo ( !empty($FeedSettings['premium'])?'block':'none'); ?>;">
<script language="Javascript" type="text/javascript"><!--
function empowercast_toggle_premium_content(enabled)
{
	jQuery('#premium_role').css('display', (enabled?'block':'none') );
	jQuery('#protected_content_message').css('display', (enabled?'block':'none') );
}	
function empowercast_premium_label_append_signin_link()
{
	jQuery('#premium_label').val( jQuery('#premium_label').val() + '<a href="<?php echo get_option('siteurl'); ?>/wp-login.php" title="<?php echo __('Sign In', 'empowercast'); ?>"><?php echo __('Sign In', 'empowercast'); ?><\/a>'); 
}
function empowercast_default_premium_label(event)
{
	if( confirm('<?php echo __('Use default label, are you sure?', 'empowercast'); ?>') )
	{
		jQuery('#premium_label_custom').css('display', (this.checked==false?'block':'none') );
		jQuery('#premium_label').val('');
	}
	else
	{
		return false;
	}
	return true;
}
//-->
</script>
	<table class="form-table">
	<tr valign="top">
	<th scope="row">
	<?php echo __('Unauthorized Label', 'empowercast'); ?>
	</th>
	<td>
	<p style="margin-top: 5px;"><input type="radio" name="PremiumLabel" value="0" <?php echo ($FeedSettings['premium_label']==''?'checked ':''); ?> onclick="return empowercast_default_premium_label(this)" />
		<?php echo __('Use default label', 'empowercast'); ?>:
	</p>
	<p style="margin-left: 20px;">
	<?php echo $FeedSettings['title']; ?>: <a href="<?php echo get_option('siteurl'); ?>/wp-login.php" target="_blank" title="Protected Content">(<?php echo __('Protected Content', 'empowercast'); ?>)</a>
	</p>
	<p style="margin-top: 5px;"><input type="radio" name="PremiumLabel" id="premium_label_1" value="1" <?php echo ($FeedSettings['premium_label']!=''?'checked ':''); ?> onchange="jQuery('#premium_label_custom').css('display', (this.checked?'block':'none') );" />
		<?php echo __('Use a custom label', 'empowercast'); ?>:
	</p>
	
	<div id="premium_label_custom" style="margin-left: 20px; display: <?php echo ($FeedSettings['premium_label']!=''?'block':'none'); ?>;">
	<textarea name="Feed[premium_label]" id="premium_label" style="width: 80%; height: 65px; margin-bottom: 0; padding-bottom: 0;"><?php echo esc_textarea($FeedSettings['premium_label']); ?></textarea>
		<div style="width: 80%; font-size: 85%; text-align: right;">
			<a href="#" onclick="empowercast_premium_label_append_signin_link();return false;"><?php echo __('Add sign in link to message', 'empowercast'); ?></a>
		</div>
		<p style="width: 80%;">
			<?php echo __('Label above appears in place of the in-page player and links when the current signed-in user does not have access to the protected content.', 'empowercast'); ?>
		</p>
	</div>
	</td>
	</tr>
	</table>
</div>
</div>
<?php
		}
		else if( !empty($General['premium_caps']) && $feed_slug )
		{
?>
<div class="pp-settings-section">
<h2><?php echo __('Password Protect Podcast Channel', 'empowercast'); ?></h2>
<p class="pp-settings-text">
	<?php echo __('This feature is not available for the default podcast channel.', 'empowercast'); ?>
</p>
</div>
<?php
		}
		
		// Podcast Channels and Custom Post Types...
		
		if( $FeedAttribs['type'] == 'channel' )
		{
		?>
<div class="pp-settings-section">
    <h2><?php echo __('Custom Posts', 'empowercast'); ?></h2>
    <p class="pp-settings-text-no-margin">
        <?php echo __('Set whether all post types or a specific custom post type may use this podcast channel. Custom post type must be of type \'Posts\'. Other post types such as \'Pages\' or \'Categories\' do not apply.', 'empowercast'); ?>
        <b>
            <?php echo __('Use the default setting if you do not understand custom post types.', 'empowercast'); ?>
        </b>
    </p>

    <h4 style="display: inline-block; margin-right: 1em;"><?php echo __('Custom Post Type', 'empowercast'); ?></h4>
    <select style="margin-top: 1ch;display: inline-block;" name="Feed[custom_post_type]" class="pp-settings-select">
<?php

			$post_types = empowercast_admin_get_post_types('post');
			$custom_post_type = '';
			if( !empty($FeedSettings['custom_post_type']) )
				$custom_post_type = $FeedSettings['custom_post_type'];
			
			echo '<option value="">'. __('All Post Types (default)', 'empowercast') .'</option>';
			foreach( $post_types as $index => $value )
			{
				$desc = $value;
				// TODO: See if we can get a post type label somehow
				$postTypeObj = get_post_type_object($value);
				if( !empty($postTypeObj->labels->name ) )
					$desc = $postTypeObj->labels->name . ' ('. $value .')';
				echo "\t<option value=\"$value\"". ($custom_post_type==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";
			}
			
			if( defined('EMPOWERCAST_CUSTOM_CAPABILITY_TYPE') )
			{
				$post_types = empowercast_admin_get_post_types( EMPOWERCAST_CUSTOM_CAPABILITY_TYPE );
				if( !empty($post_types) )
				{
					foreach( $post_types as $index => $value )
					{
						$desc = $value;
						// TODO: See if we can get a post type label somehow
						$postTypeObj = get_post_type_object($value);
						if( !empty($postTypeObj->labels->name ) )
							$desc = $postTypeObj->labels->name . ' ('. $value .')';
						echo "\t<option value=\"$value\"". ($custom_post_type==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";
					}
				}
			}
?>
    </select>
</div>
		<?php
		}
	} // else if channel