<?php

function bmcm_attributes($attributes){
	$out = '';
	if(is_array($attributes) && count($attributes) > 0){
		foreach($attributes as $property => $val){
			$out .= ' '.$property.'="'.$val.'"';
		}
	}
	return $out;
}

