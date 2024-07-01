<?php
	// empowercastadmin-ping-sites.php
	function empowercastadmin_ping_sites_process()
	{
		$PingSites = false;
		if( isset($_POST['PingSite']) )
			$PingSites = $_POST['PingSite'];
		
		if( $PingSites && count($PingSites) > 0 )
		{
			$ping_site_data = get_option('ping_sites');
			
			foreach( $PingSites as $null => $url )
				$ping_site_data = trim($ping_site_data)."\r\n$url";
				
			update_option('ping_sites', $ping_site_data);
			empowercast_page_message_add_notice(  __('Update services added successfully.', 'empowercast') );
		}
		else
		{
			empowercast_page_message_add_notice(  __('No update services selected to add.', 'empowercast') );
		}
	}
	
	function empowercast_admin_ping_sites()
	{
		$ping_sites = get_option('ping_sites');
		$BlogSites = array('http://rpc.pingomatic.com/'=> __('Ping-o-Matic!', 'empowercast'),
			'http://blogsearch.google.com/ping/RPC2'=> __('Google Blog Search', 'empowercast'),
			'http://rssrpc.weblogs.com/RPC2'=> __('WebLogs', 'empowercast')  );
			
		$PodcastSites = array('http://audiorpc.weblogs.com/RPC2'=> __('WebLogs Audio', 'empowercast') );
?>


<input type="hidden" name="action" value="empowercast-ping-sites" />
<h2><?php echo __('Add Update services / Ping Sites', 'empowercast'); ?></h2>

<p style="margin-bottom: 0;"><?php echo __('Notify the following Update Services / Ping Sites when you create a new blog post / podcast episode.', 'empowercast'); ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Update Blog Services', 'empowercast'); ?></th>
<td>
	<p><?php echo __('Select the blog service you would like to notify.', 'empowercast'); ?></p>
<?php
	foreach( $BlogSites as $url => $name )
	{
		if( stripos($ping_sites, $url) !== false )
		{
?>
	<p><input name="Ignore[]" type="checkbox" checked disabled value="1" /> <?php echo $name; ?></p>
<?php
		}
		else
		{
?>
	<p><input name="PingSite[]" type="checkbox" value="<?php echo esc_attr($url); ?>" /> <?php echo $name; ?></p>
<?php
		}
	}
?>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php echo __('Update Podcast Services', 'empowercast'); ?></th>
<td>
	<p><?php echo __('Select the podcasting service you would like to notify.', 'empowercast'); ?></p>
<?php
	foreach( $PodcastSites as $url => $name )
	{
		if( stripos($ping_sites, $url) !== false )
		{
?>
	<p><input name="Ignore[]" type="checkbox" checked disabled value="1" /> <?php echo $name; ?></p>
<?php
		}
		else
		{
?>
	<p><input name="PingSite[]" type="checkbox" value="<?php echo esc_attr($url); ?>" /> <?php echo $name; ?></p>
<?php
		}
	}
?>
</td>
</tr>

</table>
<p>
	<?php echo __('You can manually add ping services by going to the to the "Update Services" section found in the <b>WordPress Settings</b> &gt; <b>Writing</b> page.', 'empowercast'); ?>
</p>
<p class="submit">
	<input type="submit" name="Submit" id="empowercast_save_button" class="button-primary button-podops" value="<?php echo __('Add Selected Update Services', 'empowercast'); ?>" />
</p>

	<!-- start footer -->
<?php
	}

?>