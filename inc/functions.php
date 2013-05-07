<?php

/**
 * bmcm_attributes()
 * 
 * @param mixed $attributes
 * @return
 */
function bmcm_attributes($attributes){
	$out = '';
	if(is_array($attributes) && count($attributes) > 0){
		foreach($attributes as $property => $val){
			$out .= ' '.$property.'="'.$val.'"';
		}
	}
	return $out;
}

/**
 * bmcm_option_array()
 * 
 * @param mixed $option
 * @return
 */
function bmcm_option_array($option){
	$defaults = array('label' => '', 'value' => '', 'disabled' => false, 'group' => NULL);
	if(!bmcm_is_assoc_array($option)){
		list($lbl, $val, $disable, $grp) = array_merge($option, $defaults);
		$option = array(
			'label' => $lbl,
			'value' => $val,
			'disabled' => $disable,
			'group' => $grp
		);
	}
	
	$arr = array();
	foreach($option as $k => $v){
		if($k == 'lbl'){
			$key = 'label';
		} elseif($k == 'val'){
			$key = 'value';
		} elseif($k == 'disable'){
			$key = 'disabled';
		} elseif($k == 'grp'){
			$key = 'group';
		} else {
			$key = $k;
		}
		
		$arr[$key] = $v;
	}
	
	$array = array(
		'lbl' => $arr['label'],
		'val' => $arr['value'],
		'disable' => $arr['disabled'],
		'grp' => $arr['group']
	);
	
	return $array;
}

/**
 * bmcm_is_assoc_array()
 * 
 * @param mixed $arr
 * @return
 */
function bmcm_is_assoc_array($arr){
	return array_keys($arr) !== range(0,count($arr) - 1);
}

/**
 * str_lreplace()
 * 
 * @param mixed $search
 * @param mixed $replace
 * @param mixed $subject
 * @return
 */
function str_lreplace($search, $replace, $subject){
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

/**
 * us_states()
 * 
 * @param mixed $field
 * @return
 */
function us_states($field=NULL){
	$states = array(
		array( 'val' => 'AL', 'lbl' => 'Alabama'),
		array( 'val' => 'AK', 'lbl' => 'Alaska'),
		array( 'val' => 'AZ', 'lbl' => 'Arizona'),
		array( 'val' => 'AR', 'lbl' => 'Arkansas'),
		array( 'val' => 'CA', 'lbl' => 'California'),
		array( 'val' => 'CO', 'lbl' => 'Colorado'),
		array( 'val' => 'CT', 'lbl' => 'Connecticut'),
		array( 'val' => 'DE', 'lbl' => 'Delaware'),
		array( 'val' => 'DC', 'lbl' => 'District Of Columbia'),
		array( 'val' => 'FL', 'lbl' => 'Florida'),
		array( 'val' => 'GA', 'lbl' => 'Georgia'),
		array( 'val' => 'HI', 'lbl' => 'Hawaii'),
		array( 'val' => 'ID', 'lbl' => 'Idaho'),
		array( 'val' => 'IL', 'lbl' => 'Illinois'),
		array( 'val' => 'IN', 'lbl' => 'Indiana'),
		array( 'val' => 'IA', 'lbl' => 'Iowa'),
		array( 'val' => 'KS', 'lbl' => 'Kansas'),
		array( 'val' => 'KY', 'lbl' => 'Kentucky'),
		array( 'val' => 'LA', 'lbl' => 'Louisiana'),
		array( 'val' => 'ME', 'lbl' => 'Maine'),
		array( 'val' => 'MD', 'lbl' => 'Maryland'),
		array( 'val' => 'MA', 'lbl' => 'Massachusetts'),
		array( 'val' => 'MI', 'lbl' => 'Michigan'),
		array( 'val' => 'MN', 'lbl' => 'Minnesota'),
		array( 'val' => 'MS', 'lbl' => 'Mississippi'),
		array( 'val' => 'MO', 'lbl' => 'Missouri'),
		array( 'val' => 'MT', 'lbl' => 'Montana'),
		array( 'val' => 'NE', 'lbl' => 'Nebraska'),
		array( 'val' => 'NV', 'lbl' => 'Nevada'),
		array( 'val' => 'NH', 'lbl' => 'New Hampshire'),
		array( 'val' => 'NJ', 'lbl' => 'New Jersey'),
		array( 'val' => 'NM', 'lbl' => 'New Mexico'),
		array( 'val' => 'NY', 'lbl' => 'New York'),
		array( 'val' => 'NC', 'lbl' => 'North Carolina'),
		array( 'val' => 'ND', 'lbl' => 'North Dakota'),
		array( 'val' => 'OH', 'lbl' => 'Ohio'),
		array( 'val' => 'OK', 'lbl' => 'Oklahoma'),
		array( 'val' => 'OR', 'lbl' => 'Oregon'),
		array( 'val' => 'PA', 'lbl' => 'Pennsylvania'),
		array( 'val' => 'RI', 'lbl' => 'Rhode Island'),
		array( 'val' => 'SC', 'lbl' => 'South Carolina'),
		array( 'val' => 'SD', 'lbl' => 'South Dakota'),
		array( 'val' => 'TN', 'lbl' => 'Tennessee'),
		array( 'val' => 'TX', 'lbl' => 'Texas'),
		array( 'val' => 'UT', 'lbl' => 'Utah'),
		array( 'val' => 'VT', 'lbl' => 'Vermont'),
		array( 'val' => 'VA', 'lbl' => 'Virginia'),
		array( 'val' => 'WA', 'lbl' => 'Washington'),
		array( 'val' => 'WV', 'lbl' => 'West Virginia'),
		array( 'val' => 'WI', 'lbl' => 'Wisconsin'),
		array( 'val' => 'WY', 'lbl' => 'Wyoming')
	);
	
	$states = apply_filters('us_states', $states);
	
	return $states;
}