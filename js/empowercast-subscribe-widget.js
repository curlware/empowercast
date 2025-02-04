// empowercast-subscribe-widget.js

function empowercast_subscribe_widget_change(event) {
	switch( jQuery(event).val() )
	{
		case 'channel': {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').show();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-ttid').hide();
		}; break;
		case 'post_type': {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').show();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').show();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-ttid').hide();
		}; break;
		case 'category': {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').show();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-ttid').hide();
		}; break;
		case 'ttid': {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-ttid').show();
		}; break;
		default: {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-ttid').hide();
		}; break;
	}
}