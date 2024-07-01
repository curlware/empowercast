<?php
// empowercastadmin-tags.php

function empowercast_admin_tags()
{
	$General = empowercast_get_settings('empowercast_general');
	$TagSettings = empowercast_default_settings($General, 'tags');

    // If we have empowercast credentials, check if the account has been verified
    $creds = get_option('empowercast_creds');
    empowercast_check_credentials($creds);
    wp_enqueue_script('empowercast-admin', empowercast_get_root_url() . 'js/admin.js', array(), EMPOWERCAST_VERSION );
?>
<script language="javascript"><!--
function ToggleID3Tags(Obj)
{
	document.getElementById('edit_id3_tags').style.display=(Obj.checked?'block':'none');
}
//-->
</script>
    <div class="pp-settings-section">
<h2><?php echo __('MP3 Tags', 'empowercast'); ?></h2>

<p class="pp-main" style="font-weight: normal;"><?php echo __('Podops Hosting users can configure how to have the service write their MP3 ID3 Tags before publishing episodes.', 'empowercast');

		echo __('ID3 tags contain useful information (title, artist, album, year, etc...) about your podcast as well as an image for display during playback in most media players.', 'empowercast');
		echo ' ';
		echo sprintf( __('Please visit the %s section of the %s to learn more about MP3 ID3 tags.', 'empowercast'),
				'<a href="https://podopshost.com/manual/creating-podcast-media/audio/id3-tags-media-meta-data/" target="_blank">'. __('ID3 Tags and media meta data', 'empowercast') .'</a>',
				'<a href="https://podopshost.com/manual/" target="_blank">' .  __('Podcasting Manual', 'empowercast') .'</a>'	);

?>
</p>
<?php
	if( empty($General['podops_hosting']) || $General['podops_hosting'] === 'false' )
	{
?>
        <input name="NotAvailable" class="pp-settings-checkbox" type="checkbox" value="1" onchange="alert('<?php echo __('You must configure your Podops Services Account in the Podops EmpowerCast > Basic Settings page in order to utilize this feature.', 'empowercast'); ?>'); this.checked=false; return false;" />
        <div class="pp-settings-subsection">
            <p class="pp-main"><?php echo __('Write Tags', 'empowercast'); ?></p>
            <p class="pp-sub"><?php echo __('Use Podops Hosting services to write MP3 ID3 tags to your media.', 'empowercast'); ?></p>
        </div>

<?php
	}
	else
	{
?>
        <input name="General[write_tags]" class="pp-settings-checkbox" type="checkbox" value="1" <?php if( !empty($General['write_tags']) ) echo 'checked '; ?> onchange="ToggleID3Tags(this);" />
        <div class="pp-settings-subsection">
            <p class="pp-main"><?php echo __('Write Tags', 'empowercast'); ?></p>
            <p class="pp-sub"><?php echo __('Use Podops Hosting services to write MP3 ID3 tags to your media.', 'empowercast'); ?></p>
        </div>
<?php } ?>

<div id="edit_id3_tags" style="display:<?php echo ( !empty($General['podops_hosting'])&& $General['podops_hosting']!=='false' ?( !empty($General['write_tags'])?'block':'none'):'block'); ?>;">

<?php
	if( empty($General['tag_title']) )
		$General['tag_title'] = '';
	if( empty($General['tag_artist']) )
		$General['tag_artist'] = '';
	if( empty($General['tag_album']) )
		$General['tag_album'] = '';
	if( empty($General['tag_genre']) )
		$General['tag_genre'] = '';
	if( empty($General['tag_year']) )
		$General['tag_year'] = '';
	if( empty($General['tag_track']) )
		$General['tag_track'] = '';
	if( empty($General['tag_composer']) )
		$General['tag_composer'] = '';
	if( empty($General['tag_copyright']) )
		$General['tag_copyright'] = '';
	if( empty($General['tag_url']) )
		$General['tag_url'] = '';
	if( empty($General['tag_coverart']) )
		$General['tag_coverart'] = '';
	
	empowercastadmin_tag_option('tag_title', $General['tag_title'], __('Title Tag', 'empowercast'), __('Use blog post title', 'empowercast') );
	empowercastadmin_tag_option('tag_artist', $General['tag_artist'], __('Artist Tag', 'empowercast'), __('Use Feed Talent Name', 'empowercast') );
	empowercastadmin_tag_option('tag_album', $General['tag_album'], __('Album Tag', 'empowercast'), __('Use blog title', 'empowercast') .': '.  get_bloginfo('name') .'' );
	empowercastadmin_tag_option('tag_genre', $General['tag_genre'], __('Genre Tag', 'empowercast'), __('Use genre \'Podcast\'', 'empowercast') );
	empowercastadmin_tag_option('tag_year', $General['tag_year'], __('Year Tag', 'empowercast'), __('Use current year', 'empowercast') );
	empowercastadmin_tag_option('tag_track', $General['tag_track'], __('Track Tag', 'empowercast'), __('Do not specify track number', 'empowercast') );
	empowercastadmin_tag_option('tag_composer', $General['tag_composer'], __('Composer Tag', 'empowercast'), __('Use Feed Talent Name', 'empowercast') );
	empowercastadmin_tag_option('tag_copyright', $General['tag_copyright'], __('Copyright Tag', 'empowercast'), __('Use &copy; Talent Name', 'empowercast') );
	empowercastadmin_tag_option('tag_url', $General['tag_url'], __('URL Tag', 'empowercast'), __('Use main blog URL', 'empowercast') .': '.  get_bloginfo('url') .'' );
	empowercastadmin_tag_option('tag_coverart', $General['tag_coverart'], __('Coverart Tag', 'empowercast'), '' );
	
?>

</div>
    </div>
<?php
} // End empowercast_admin_appearance()


function empowercastadmin_tag_option($tag, $value, $label, $default_desc )
{
	$file = false;
	$other = false;
	$track = false;
	switch( $tag )
	{
		case 'tag_title': {
			$other = false;
		}; break;
		case 'tag_track': {
			$track = true;
		}; break;
		case 'tag_coverart': {
			$other = false;
			$file = true;
		}; break;
		default: {
			$other = true;
		}
	}
?>
    <div class="pp-settings-subsection" style="margin-left: 3em;">
<p class="pp-main">
<?php echo $label; ?>
</p>
<p class="pp-sub">
<?php
	if( !$file )
	{
?>
<input type="radio" name="General[<?php echo $tag; ?>]" value="0" <?php if( $value == '' ) echo 'checked'; ?> />
<?php
		echo $default_desc;
	}
	
	if( $file )
	{
		$FeedSettings = get_option('empowercast_feed');
		$SupportUploads = false;
		$UploadArray = wp_upload_dir();
		if( false === $UploadArray['error'] )
		{
			$upload_path =  $UploadArray['basedir'].'/empowercast/';
			
			if( !file_exists($upload_path) )
				$SupportUploads = @wp_mkdir_p( rtrim($upload_path, '/') );
			else
				$SupportUploads = true;
		}
?>
<input type="radio" name="General[<?php echo $tag; ?>]" value="0" <?php if( $value == '' ) echo 'checked'; ?> />
<?php echo __('Do not add a coverart image.', 'empowercast'); ?><br />
<input type="radio" id="<?php echo $tag; ?>_specify" name="General[<?php echo $tag; ?>]" value="1" <?php if( $value != '' ) echo 'checked'; ?> />

<input type="text" id="coverart_image" name="TagValues[<?php echo $tag; ?>]" style="width: 50%;" value="<?php echo esc_attr($value); ?>" maxlength="250" />
<a href="#" onclick="javascript: window.open( document.getElementById('coverart_image').value ); return false;"><?php echo __('preview', 'empowercast'); ?></a>

<p class="pp-label-bottom" style="margin-bottom: 0;"><?php echo __('Place the URL to the Coverart image above. e.g. http://mysite.com/images/coverart.jpg', 'empowercast'); ?></P>
<p class="pp-label-bottom" style="margin-bottom: 0;"><?php echo __('Coverart images may be saved as either .gif, .jpg or .png images of any size, though 300 x 300 or 600 x 600 in either png or jpg format is recommended.', 'empowercast'); ?></p>
<p class="pp-label-bottom" style="margin-bottom: 0;">
<?php if( $FeedSettings['itunes_image'] ) { ?>
<input type="hidden" name="NULL[current_coverart_image]" id="current_coverart_image" value="<?php echo htmlspecialchars($FeedSettings['itunes_image']); ?>" />
<a href="#" title="" onclick="document.getElementById('coverart_image').value=document.getElementById('current_coverart_image').value;document.getElementById('tag_coverart_specify').checked=true;return false;"><?php echo __('Click here to use your current iTunes image.', 'empowercast'); ?></a>

<?php } ?>
</p>
<?php if( $SupportUploads ) { ?>
<p class="pp-label-bottom" style="margin-bottom: 0;"><input name="coverart_image_checkbox" type="checkbox" onchange="empowercast_show_field('coverart_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'empowercast'); ?> </p>
<div style="display:none" id="coverart_image_upload">
	<label for="coverart_image_file"><?php echo __('Choose file', 'empowercast'); ?>:</label> <input type="file" name="coverart_image_file" />
</div>
<?php } ?>

<?php
	}
	
	if( $track )
	{
		$EmpowerCastTrackNumber = get_option('empowercast_track_number');
?><br />
<input type="radio" name="General[<?php echo $tag; ?>]" value="1" <?php if( !empty($value) ) echo 'checked'; ?> /> <?php echo __('Specify', 'empowercast'); ?>: 
<input type="text" name="EmpowerCastTrackNumber" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo esc_attr( !empty($EmpowerCastTrackNumber) ?$EmpowerCastTrackNumber:'1'); ?>" maxlength="5" />
<?php
		echo __('(value entered increments every episode)', 'empowercast');
	}
	
	if( $other )
	{
?><br />
<input type="radio" name="General[<?php echo $tag; ?>]" value="1" <?php if( $value != '' ) echo 'checked'; ?> /> <?php echo __('Specify', 'empowercast'); ?>: 
<input type="text" name="TagValues[<?php echo $tag; ?>]" style="width: 300px" value="<?php echo esc_attr($value); ?>" maxlength="250" />
<?php
	}
	
?>
</p>
    </div>
<?php
}

?>