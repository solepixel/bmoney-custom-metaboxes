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
		
		if($button.attr('id') && bmcm_media_vars.fields[$button.attr('id')]){
			var properties = bmcm_media_vars.fields[$button.attr('id')];
		} else {
			var properties = bmcm_media_vars.defaults;
		}
		
        var params = {
            className: 'media-frame bmcm-media-frame',
            frame: 'select',
            multiple: properties.allow_multiple,
            title: properties.title,
            library: {
                type: properties.library_type
            },
            button: {
                text:  properties.button
            }
        };
        
        /*
        // save this for something else...
        if($button.attr('id')){
	    	var $collection = $button.parents('.bmcm-field').find('input[name^="bmcm_'+$button.attr('id')+'"]');
	    	if($collection.length){
	    		$button.selection = bmcm_get_selection($collection);
	    	}
    	}
        
        // preselect
        if($button.selection){
            params.selection = $button.selection;
            params.editing = true;
        }*/
        
        // open up the frame.
        $button.media_frame = wp.media.frames.bmcm_media_frame = wp.media(params);

        $button.media_frame.on('select', function(){
        	var $selection = $button.media_frame.state().get('selection');
        	if(!properties.allow_multiple){
	            var media_attachment = $selection.first().toJSON();
	            
	            /* SAMPLE media_attachment OBJECT:
		            alt: ""
					author: "3"
					caption: ""
					compat: Object { item="", meta=""}
					date: Date {Fri Apr 05 2013 21:10:28 GMT-0500 (Central Daylight Time)}
					dateFormatted: "April 5, 2013"
					description: ""
					editLink: "http://site.url.com...hp?post=243&action=edit"
					filename: "Koala1.jpg"
					height: 768
					icon: "http://site.url.com...ges/crystal/default.png"
					id: 243
					link: "http://site.url.com...-functionality/koala-2/"
					menuOrder: 0
					mime: "image/jpeg"
					modified: Date {Fri Apr 05 2013 21:10:28 GMT-0500 (Central Daylight Time)}
					name: "koala-2"
					nonces: Object { update="1f6a9ce823", delete="adcec5ffb0" }
					orientation: "landscape"
					sizes: Object { thumbnail={...}, medium={...}, full={...}}
						height: 768
						orientation: "landscape"
						url: "http://work.dev.infomed...oads/2013/03/Koala1.jpg"
						width: 1024
					status: "inherit"
					subtype: "jpeg"
					title: "Koala"
					type: "image"
					uploadedTo: 176
					url: "http://site.url.com...oads/2013/03/Koala1.jpg"
					width: 1024
	            */
	            
	            if(properties.target_input){
		        	$button.nextAll(properties.target_input+':first').val(media_attachment.id);
		        }
		        if(properties.target_label){
		        	$button.nextAll(properties.target_label+':first').html(media_attachment.filename);
	        	}
	            if(properties.library_type == 'image' && properties.target_image){
	            	$button.nextAll(properties.target_image+':first').attr('src', media_attachment.url);
	            }
	            
	            var $remove = $button.parents('.field:first').find('a[href="#remove-media"]');
				$remove.show();
			} else {
				var images = $selection.toJSON();
				var $container = $button.nextAll(properties.target_container+':first');
				
				$.each(images, function(index, image) {
					var $single = $(properties.single_markup);
					$container.append($single);
					
					if(properties.target_input){
			        	$single.find(properties.target_input+':first').val(image.id);
			        }
			        if(properties.target_label){
			        	$single.find(properties.target_label+':first').html(image.filename);
		        	}
		            if(properties.target_image){
		            	$single.find(properties.target_image+':first').attr('src', image.sizes.thumbnail.url);
		            }
				});
			}
			
			//$button.selection = bmcm_get_selection($selection);
			
			var callback = window[bmcm_callbacks.select];
            if(typeof callback == 'function'){
            	callback(media_attachment, $input);
            }
        });
		
        $button.media_frame.open();
        
        return false;
    });
    
    
    $('a[href="#remove-media"]').live('click', function(){
    	if($(this).parents('.bmcm-gallery-image:first').length){
    		$(this).parents('.bmcm-gallery-image:first').remove();
    	} else {
	    	var $parent = $(this).parents('.field:first');
	    	$parent.find('.media-display').html('');
	    	$parent.find('.media-reference').val('');
	    	$(this).hide();
    	}
    	
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
    
    
    
    var $galleries = $('.bmcm-gallery');
	$.each($galleries, function(i, el){
		var $item = $(el);
		$item.sortable({
	    	start: function(e, ui){
	    		ui.item.addClass('dragging');
	    	},
	    	stop: function(e, ui){
	    		ui.item.removeClass('dragging');
	    	}
	    });
	});
	
	
	function bmcm_get_selection($collection){
		var image_ids = new Array();
		if(typeof $collection.toJSON == 'function'){
			$.each($collection.toJSON(), function(index, image) {
				image_ids[image_ids.length] = image.id;
			});
		} else {
			$.each($collection, function(index, image) {
				image_ids[image_ids.length] = $(image).val();
			});
		}
		
		var content = '[gallery ids="'+image_ids.join(',')+'"]';
		
		var shortcode = wp.shortcode.next( 'gallery', content ),
			defaultPostId = $('#post_ID').val() ? $('#post_ID').val() : wp.media.gallery.defaults.id,
			attachments, selection;
		
		// Bail if we didn't match the shortcode or all of the content.
		if ( ! shortcode || shortcode.content !== content )
			return;

		// Ignore the rest of the match object.
		shortcode = shortcode.shortcode;
		

		if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
			shortcode.set( 'id', defaultPostId );
			
		attachments = wp.media.gallery.attachments( shortcode );

		selection = new wp.media.model.Selection( attachments.models, {
			props:    attachments.props.toJSON(),
			multiple: true
		});

		selection.gallery = attachments.gallery;

		// Fetch the query's attachments, and then break ties from the
		// query to allow for sorting.
		selection.more().done( function() {
			// Break ties with the query.
			selection.props.set({ query: false });
			selection.unmirror();
			selection.props.unset('orderby');
		});
		
		return selection;
	}
});
