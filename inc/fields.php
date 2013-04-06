<?php

if(function_exists('bmcm_wrap_field')) return;

/**
 * bmcm_wrap_field()
 * 
 * @param mixed $output
 * @param mixed $field
 * @return
 */
function bmcm_wrap_field($output, $field){
	$is_multi = in_array($field['type'], array('multi','multiple','repeate','repeatable')); //TODO: fix this...
	$type = $field['type'] == 'multi_additional' ? 'multi' : $field['type'];
	
	$index = isset($field['tab']) ? $field['_tab_index'] : $field['_index'];
	$total = isset($field['tab']) ? $field['_tab_total'] : $field['_total'];
	
	$class = array('field-'.$field['id'], 'type-'.$type);
		
	if($index == 0) $class[] = 'first';
	$class[] = ($index & 1) ? 'odd' : 'even';
	if($index+1 == $total) $class[] = 'last';
	
	if(!$field['title']) $class[] = 'no-label';
	
	$ret = '';
	if($is_multi){
		$_mclass = array('multi-collection');
		if($field['settings']['sortable']) $_mclass[] = 'sortable';
		$ret .= '<div class="'.implode(' ', $_mclass).'">';
	}
	$ret .= '<div class="bmcm-field '.implode(' ', $class).'">';
		$ret .= $output;
		if(!$is_multi && $type != 'multi') $ret .= '<br class="clear" />';
	$ret .= '</div>';
	
	if($is_multi){
		if(isset($field['additional'])){
			$ret .= $field['additional'];
		}
		$ret .= '</div>';
	}
	
	return $ret;
}


/**
 * bmcm_section_break()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_section_break($key, $field, $post, $bmcm){
	extract($field);
	$output = '<hr id="'.$id.'" class="section-break" />';
	return $output;
}


/**
 * bmcm_multi()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_multi($key, $field, $post, $bmcm){
	extract($field);
	
	$output = '<div class="multi-controls"><a href="#add" class="multi-add">+</a><a href="#remove" class="multi-remove">-</a></div>';
	$output .= '<div class="multi-wrap">';
		if($description) echo '<div class="description">'.$description.'</div>';
		if($before) $output .= '<span class="before">'.$before.'</span>';
		foreach($fields as $item){
			$element = apply_filters('bmcm_output_field_'.$item['type'], $key, $item, $post, $this);
			$output .= apply_filters('bmcm_wrap_field', $element, $item);
		}
		if($after) $output .= '<span class="after">'.$after.'</span>';
	$output .= '</div>';
	return $output;
}


/**
 * bmcm_text()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_text($key, $field, $post, $bmcm){
	extract($field);
	$type = $type == 'password' ? $type : 'text';
	$output = '<label>';
		if($title) $output .= '<span class="label">'.$title.'</span>';
		$output .= '<span class="field">';
		if($before) $output .= '<span class="before">'.$before.'</span>';
		$output .= '<input type="'.$type.'" name="'.$name.'"';
		if($value || $value === '0'){
			$output .= 'value="'.$value.'"';
		}
		$output .= bmcm_attributes($attributes);
		$output .= '/>';
		if($after) $output .= '<span class="after">'.$after.'</span>';
		$output .= '</span>';
		if($description){
			$output .= '<span class="description">'.$description.'</span>';
		}
	$output .= '</label>';
	return $output;
}

/**
 * bmcm_textarea()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_textarea($key, $field, $post, $bmcm){
	extract($field);
	$output = '<label>';
		if($title) $output .= '<span class="label">'.$title.'</span>';
		$output .= '<span class="field"><textarea name="'.$name.'"';
		$output .= bmcm_attributes($attributes);
		$output .= '>'.htmlspecialchars($value).'</textarea>';
		if($description){
			$output .= '<span class="description">'.$description.'</span>';
		}
		$output .= '</span>';
	$output .= '</label>';
	return $output;
}

/**
 * bmcm_select()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_select($key, $field, $post, $bmcm){
	extract($field);
	$group = NULL;
	
	$output = '<label>';
		if($title) $output .= '<span class="label">'.$title.'</span>';
		$output .= '<span class="field"><select name="'.$name.'"';
		$output .= bmcm_attributes($attributes);
		$output .= '>';
			foreach($options as $opt){
				extract(bmcm_option_array($opt));
				if($grp != $group){
					if($group != NULL) $output .= '</optgroup>';
					$group = $grp;
					if($grp && $lbl){
						$output .= '<optgroup label="'.$lbl.'">';
					} else {
						$group = NULL;
					}
				} else {
					$output .= '<option value="'.$val.'"';
						if($val == $value) $output .= ' selected="selected"';
						if($disable) $output .= ' disabled="disabled"';
					$output .= '>'.$lbl.'</option>';
				}
			}
			if($group != NULL) $output .= '</optgroup>';
		$output .= '</select></span>';
		if($description){
			$output .= '<span class="description">'.$description.'</span>';
		}
	$output .= '</label>';
	return $output;
}


/**
 * bmcm_checkbox()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_checkbox($key, $field, $post, $bmcm){
	extract($field);
	
	$output = '<label class="checkbox">';
		$output .= '<span class="field">';
			$output .= '<input type="checkbox" name="'.$name.'" value="'.$val.'"';
			if($value == $val) $output .= ' checked="checked"';
			$output .= bmcm_attributes($attributes);
			$output .= '/>';
		$output .= '</span>';
		$output .= '<span class="label">'.$title.'</span>';
		if($description){
			$output .= '<span class="description">'.$description.'</span>';
		}
	$output .= '</label>';
	return $output;
}


/**
 * bmcm_checkboxes()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_checkboxes($key, $field, $post, $bmcm){
	extract($field);
	
	$value = !is_array($value) ? array($value) : $value;
	$type = ($type == 'radios') ? 'radio' : 'checkbox';
	
	$output = '<div class="cbx_wrap">';
		if($title) $output .= '<span class="label">'.$title.'</span>';
		$output .= '<ul class="cbxs '.$type.'">';
			foreach($options as $opt){
				extract(bmcm_option_array($opt));
				$output .= '<li><label>';
					$output .= '<input type="'.$type.'" name="'.$name.'" value="'.$val.'"';
					if(in_array($val, $value)) $output .= ' checked="checked"';
				$output .= ' /> '.$lbl.'</label></li>';
			}
		$output .= '</ul>';
		if($description){
			$output .= '<span class="description">'.$description.'</span>';
		}
	$output .= '</div>';
	return $output;
}


/**
 * bmcm_upload()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_upload($key, $field, $post, $bmcm){
	extract($field);
	$filename = '';
	$output = '<div class="upload_wrap">';
		if($title) $output .= '<span class="label">'.$title.'</span>';
		$output .= '<span class="field">';
			$output .= '<input type="hidden" name="'.$name.'" class="media-reference"';
			if($value || $value === '0'){
				if(is_numeric($value)){
					$output .= 'value="'.$value.'"';
					$filename = basename(wp_get_attachment_url($value));
				}
			}
			$output .= ' />';
			$output .= '<input type="button" value="Select Media" class="button bmcm-media" />';
			$output .= '<span class="media-display">'.$filename.'</span>';
			$output .= '<span class="media-buttons"><a href="#remove-media">Remove</a></span>';
			if($description)
				$output .= '<span class="description">'.$description.'</span>';
		$output .= '</span>';
	$output .= '</div>';
    return $output;
}

/**
 * bmcm_wysiwyg()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_wysiwyg($key, $field, $post, $bmcm){
	extract($field);
	$output = '<div class="wysiwyg_wrap">';
		if($title) $output .= '<label for="'.$id.'" class="label">'.$title.'</label>';
		$output .= '<div class="field">';
			ob_start();
			wp_editor( $value, $name, $settings );
			$output .= ob_get_contents();
			ob_end_clean();
		$output .= '</div>';
	$output .= '</div>';
	
	return $output;
}


/**
 * bmcm_slider()
 * 
 * @param mixed $key
 * @param mixed $field
 * @param mixed $post
 * @param mixed $bmcm
 * @return
 */
function bmcm_slider($key, $field, $post, $bmcm){
	extract($field);
	$output = '<div class="slider_wrap">';
		if($title) $output .= '<span class="label">'.$title.'</span>';
		$output .= '<span class="field">';
			$output .= '<span class="ui-slider"></span>';
			$output .= '<input type="hidden" name="'.$name.'" class="slider-percent"';
			if($value || $value === '0'){
				$output .= 'value="'.$value.'"';
			}
			$output .= ' />';
			if($description)
				$output .= '<span class="description">'.$description.'</span>';
		$output .= '</span>';
	$output .= '</div>';
    return $output;
}
