/*jQuery(document).ready(function(){
  alert('funcionou ');
});*/

jQuery('#aw-whats-new-submit').click( function(){
	var activityTitle = jQuery('#activity-event-post-title').val();
	
	//alert('ajaxurl = ' + ajaxurl + "\n" + 'activity title = ' + activityTitle);
	

	jQuery.post(ajaxurl,
			{
				action : post_update,
				title  : activityTitle
			}
		
	);
	

});

