jQuery(function($) {
	
	/* general fun stuff */
	bmcm_field_functionality();
	function bmcm_field_functionality(){
		// ALL TEXT FIELDS:
		$('.bmcm-field input[type="text"]').bind('paste', function(){
			var $input = $(this);
			setTimeout(function() {
				$input.val($input.val().trim());
			}, 0);
		});
		
		// URL fields
		var $url_fields = $('.type-url');
		var urlregex = new RegExp("^(http:\/\/|https:\/\/|ftp:\/\/){1}([0-9A-Za-z]+\.)");
		$.each($url_fields, function(i, el){
			var $field = $(el);
			var $input = $field.find('.field input[type="text"]:first');
			var $link = $field.find('.bmcm-url-link');
			if($link.length <= 1){
				$link = $('<a />').attr('href',$input.val()).addClass('bmcm-url-link').attr('target','_blank');
				$field.find('.field').append($link);
			}
			if(!$input.val()){
				$link.hide();
			}
			$input.bind('keyup paste', function(e){
				var $input = $(this);
				setTimeout(function() {
					var value = $input.val();
					if(value && urlregex.test(value)){
						$link.attr('href',value);
						$link.show();
					} else {
						$link.hide();
					}
				}, 0);
			});
		});
	}
	
	/* jquery ui */
    $('.datepicker').datepicker({
        dateFormat : 'mm/dd/yy'
    });
    
    var $sliders = $('.ui-slider');
    $.each($sliders, function(i, el){
    	var $slider = $(el);
    	var $target = $slider.next('.slider-percent');
    	var val = $target.val() ? $target.val() : 0;
    	var tooltip_val = val+'%';
    	
    	$slider.slider({
	    	orientation: 'horizontal',
	    	range: 'min',
			step: 1,
			min: 0,
			max: 100,
			value: val,
			slide: function(e, ui){
				$target.val(ui.value);
				tooltip_val = ui.value+'%';
				$slider.attr('title', tooltip_val);
				
				if(ui.value == 100){
					$slider.addClass('value-full');
				} else {
					$slider.removeClass('value-full');
				}
				
				$slider.tooltip({
					content: tooltip_val
					//TODO position tooltip over handle.
				});
			}
	    }).attr('title', tooltip_val);
	    
	    if(val == '100'){
	    	$slider.addClass('value-full');
	    }
    }).tooltip({
    	show: null,
		position: {
			my: 'center bottom',
			at: 'center top'
		},
		open: function( event, ui ) {
			ui.tooltip.animate({ top: ui.tooltip.position().top - 10 }, 'fast' );
		}
    });
    
    
    
    /* multiple fields */
    
    var $sortable = $('.multi-collection.sortable');
	$.each($sortable, function(i, el){
		var $item = $(el);
		$item.sortable({
			axis: 'y',
	    	start: function(e, ui){
	    		ui.item.addClass('dragging');
	    	},
	    	stop: function(e, ui){
	    		ui.item.removeClass('dragging');
	    		bmcm_reset_styles();
	    	}
	    });
	});
	
	
    var $multis = $('.type-multi');
    $.each($multis, function(i, el){
    	var $multi = $(el);
    	var $add = $multi.find('.multi-controls a.multi-add');
    	var $remove = $multi.find('.multi-controls a.multi-remove');
    	var $collection = $multi.parents('.multi-collection:first');
    	
    	if($collection.find('.multi-wrap').length <= 1){
			$collection.find('.multi-remove').hide();
		}
		
		var $additionals = $collection.find('.type-multi.additional');
		$.each($additionals, function(a, addl){
			if($(addl).find('.multi-wrap:first .bmcm-field').length == 1){
				$('.label', $(addl)).css('visibility','hidden');
			}
		});
    });
    
    
	bmcm_bind_buttons();
    function bmcm_bind_buttons(){
    	var $multis = $('.type-multi');
    	$.each($multis, function(i, el){
    		var $multi = $(el);
	    	var $add = $multi.find('.multi-controls a.multi-add');
	    	var $remove = $multi.find('.multi-controls a.multi-remove');
	    	
	    	$add.unbind('click').click(function(e){
	    		var $repeat = $(this).parents('.type-multi:first').clone();
	    		var $collection = $(this).parents('.multi-collection:first');
	    		
	    		// reset all fields
	    		$repeat.find('input[type="text"],input[type="radio"],input[type="hidden"],select,textarea').val('');
	    		$repeat.find('.media-display').html('');
	    		$repeat.find('.media-buttons a[href="#remove-media"]').hide();
	    		$repeat.find('.bmcm-url-link').attr('href','#').hide();
	    		
		    	$(this).parents('.type-multi:first').after($repeat);
		    	
		    	if($collection.find('.type-multi').length > 1){
	    			$collection.find('.multi-remove').show();
	    		}
	    		
	    		bmcm_bind_buttons();
	    		bmcm_reset_styles();
	    		return false;
	    	});
	    	
	    	$remove.unbind('click').click(function(e){
	    		var $collection = $(this).parents('.multi-collection:first');
	    		
	    		$(this).parents('.type-multi:first').remove();
	    		
		    	if($collection.find('.type-multi').length <= 1){
	    			$collection.find('.multi-remove').hide();
	    		}
	    		
	    		bmcm_bind_buttons();
	    		bmcm_reset_styles();
	    		return false;
	    	});
    	});
    }
    
    function bmcm_reset_styles(){
    	$.each($('.multi-collection'), function(i, el){
    		var $collection = $(el);
    		
    		$collection.find('.type-multi').removeClass('first odd even last');
    		$collection.find('.type-multi:first').addClass('first');
    		$collection.find('.type-multi:odd').addClass('odd');
    		$collection.find('.type-multi:even').addClass('even');
    		$collection.find('.type-multi:last').addClass('last');
    		
    		if($collection.find('.multi-wrap:first .bmcm-field').length == 1){
    			$collection.find('.bmcm-field .label').css('visibility','hidden');
    			$collection.find('.multi-wrap:first .bmcm-field:first .label').css('visibility','visible');
    		}
    	});
    	
    	bmcm_field_functionality();
    }
    
    
    /* tabs */
	var $tabs = $('.bmcm-tabs');
	$.each($tabs, function(i, el){
		var $tab_menu = $(el);
		var $contents = $tab_menu.next('.bmcm-tab-contents:first');
		
		$tab_menu.find('a').click(function(e){
			var $link = $(this);
			var href = $link.attr('href').substr($link.attr('href').indexOf('#')+1, $link.attr('href').length);
			$contents.find('.bmcm-tab').hide();
			$contents.find('.tab-'+href).show();
			$tab_menu.find('li.active').removeClass('active');
			$link.parents('li:first').addClass('active');
			return false;
		});
	});
});


if(!String.prototype.trim){
	String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};
	String.prototype.ltrim=function(){return this.replace(/^\s+/,'');};
	String.prototype.rtrim=function(){return this.replace(/\s+$/,'');};
	String.prototype.fulltrim=function(){return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ');};
}