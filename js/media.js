/**
 * New Media Manager opener (WP >= 3.5)
 *
 * @since 1.0
 *
 * @package BMoney Custom Metaboxes
 * @author  Brian DiChiara
 */
jQuery(function($){
	
    window.bmcm_callbacks = {
    	select: bmcm_media_vars.selection_callback,
    	remove: bmcm_media_vars.remove_callback
    }
    
   $('.bmcm-media').live('click',function(e){
		var $button = $(this);
        
        if ( $button.media_frame ) {
            $button.media_frame.open();
            return;
        }
        
        $button.media_frame = wp.media.frames.bmcm_media_frame = wp.media({
            className: 'media-frame bmcm-media-frame',
            frame: 'select',
            multiple: false,
            title: bmcm_media_vars.title,
            title: 'Upload or Choose Your File',
            library: {
                //type: 'image'
            },
            button: {
                text:  bmcm_media_vars.button
            }
        });

        $button.media_frame.on('select', function(){
            var media_attachment = $button.media_frame.state().get('selection').first().toJSON();
            
	        var $input = $button.prev('.media-reference:first');
	        var $label = $button.next('.media-display:first');
	        var $remove = $button.parents('.field:first').find('a[href="#remove-media"]');
	        
            $input.val(media_attachment.id);
            $label.html(media_attachment.filename);
			$remove.show();
			
			var callback = window[bmcm_callbacks.select];
            if(typeof callback == 'function'){
            	callback(media_attachment, $input);
            }
        });
		
        $button.media_frame.open();
        
        return false;
    });
    
    $('a[href="#remove-media"]').live('click', function(){
    	var $parent = $(this).parents('.field:first');
    	$parent.find('.media-display').html('');
    	$parent.find('.media-reference').val('');
    	$(this).hide();
    	
        var callback = window[bmcm_callbacks.remove];
        if(typeof callback == 'function'){
        	callback($(this));
        }
        
    	return false;
    });
    
    $.each($('.media-reference'), function(i, el){
    	var $remove = $(el).parents('.field:first').find('a[href="#remove-media"]:first');
    	if($(el).val()){
    		$remove.show();
    	} else {
    		$remove.hide();
    	}
    });
});
