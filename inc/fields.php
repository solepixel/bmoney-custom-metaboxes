<?php

if(function_exists('bmcm_wrap_field')) return;

function bmcm_wrap_field($output, $field){
	$class = array('field-'.$field['id'], 'type-'.$field['type']);
	if($field['_index'] == 0) $class[] = 'first';
	if($field['_index']+1 == $field['_total']) $class[] = 'last';
	
	$ret = '<div class="bmcm-field '.implode(' ', $class).'">'.$output;
		$ret .= '<br class="clear" />';
	$ret .= '</div>';
	return $ret;
}


function bmcm_section_break($key, $field, $post, $bmcm){
	extract($field);
	$output = '<hr id="'.$id.'" class="section-break" />';
	return $output;
}

function bmcm_text($key, $field, $post, $bmcm){
	extract($field);
	$type = $type == 'password' ? $type : 'text';
	$output = '<label>';
		$output .= '<span class="label">'.$title.'</span>';
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

function bmcm_textarea($key, $field, $post, $bmcm){
	extract($field);
	$output = '<label>';
		$output .= '<span class="label">'.$title.'</span>';
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

function bmcm_select($key, $field, $post, $bmcm){
	extract($field);
	$group = NULL;
	
	$output = '<label>';
		$output .= '<span class="label">'.$title.'</span>';
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


function bmcm_upload($key, $field, $post, $bmcm){
	extract($field);
	$output = '<div class="upload_wrap">';
		$output .= '<span class="label">'.$title.'</span>';
		$output .= '<span class="field">';
			$output .= '<input type="text" name="'.$name.'" value="'.$value.'" class="media-reference" />';
			$output .= '<input type="button" value="Upload Media" class="button bmcm-media" />';
		$output .= '</span>';
		if($description){
			$output .= '<span class="description">'.$description.'</span>';
		}
	$output .= '</div>';
    return $output;
}

function bmcm_wysiwyg($key, $field, $post, $bmcm){
	extract($field);
	$output = '<div class="wysiwyg_wrap">';
		$output .= '<label for="'.$key.'" class="label">'.$title.'</label>';
		$output .= '<div class="field">';
			ob_start();
			wp_editor( $value, $name, $settings );
			$output .= ob_get_contents();
			ob_end_clean();
		$output .= '</div>';
	$output .= '</div>';
	
	return $output;
}