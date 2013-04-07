jQuery(function($) {
	
	/* jquery ui */
    $('.datepicker').datepicker({
        dateFormat : 'mm/dd/yy'
    });
    
    var $sliders = $('.ui-slider');
    $.each($sliders, function(i, el){
    	var $slider = $(el);
    	var $target = $slider.next('.slider-percent');
    	var val = $target.val() ? $target.val() : 0;
    	
    	$slider.slider({
	    	orientation: 'horizontal',
	    	range: 'min',
			step: 1,
			min: 0,
			max: 100,
			value: val,
			slide: function(e, ui){
				$target.val(ui.value);
				$slider.attr('title', ui.value+'%');
				if(ui.value == 100){
					$slider.addClass('value-full');
				} else {
					$slider.removeClass('value-full');
				}
			}
	    }).attr('title', val+'%');
	    
	    if(val == '100'){
	    	$slider.addClass('value-full');
	    }
    });
    
    
    
    /* multiple fields */
    
    var $sortable = $('.multi-collection.sortable');
	$.each($sortable, function(i, el){
		var $item = $(el);
		var padding = '';
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
		})
		
		bmcm_bind_buttons();
    });
    
    
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