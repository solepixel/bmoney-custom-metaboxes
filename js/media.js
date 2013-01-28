/**
 * New Media Manager opener (WP >= 3.5)
 *
 * @since 1.0
 *
 * @package BMoney Custom Metaboxes
 * @author  Brian DiChiara
 */
jQuery(function($){
    
    var bmcm_media_frame;
    var $input;
    
    $('.bmcm-media').click(function(e){
		
        if ( bmcm_media_frame ) {
            bmcm_media_frame.open();
            return;
        }
        
        $input = $(this).prev('input[type="text"].media-reference:first');

        bmcm_media_frame = wp.media.frames.bmcm_media_frame = wp.media({
            className: 'media-frame bmcm-media-frame',
            frame: 'select',
            multiple: false,
            //title: bmcm_media_vars.title,
            title: 'Upload or Choose Your File',
            library: {
                //type: 'image'
            },
            button: {
                //text:  bmcm_media_vars.button
            }
        });

        bmcm_media_frame.on('select', function(){
            var media_attachment = bmcm_media_frame.state().get('selection').first().toJSON();
            $input.val(media_attachment.url);
        });

        bmcm_media_frame.open();
        
        return false;
    });
});