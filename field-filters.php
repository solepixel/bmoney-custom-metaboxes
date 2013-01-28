<?php

add_filter('bmcm_wrap_field', 'bmcm_wrap_field', 10, 2);

add_filter('bmcm_output_field_text', 'bmcm_text', 10, 4);
add_filter('bmcm_output_field_text_small', 'bmcm_text', 10, 4);
add_filter('bmcm_output_field_text_medium', 'bmcm_text', 10, 4);
add_filter('bmcm_output_field_text_large', 'bmcm_text', 10, 4);
add_filter('bmcm_output_field_password', 'bmcm_text', 10, 4);
add_filter('bmcm_output_field_money', 'bmcm_text', 10, 4);

add_filter('bmcm_output_field_textarea', 'bmcm_textarea', 10, 4);

add_filter('bmcm_output_field_select', 'bmcm_select', 10, 4);

add_filter('bmcm_output_field_checkbox', 'bmcm_checkbox', 10, 4);
add_filter('bmcm_output_field_checkboxes', 'bmcm_checkboxes', 10, 4);
add_filter('bmcm_output_field_radios', 'bmcm_checkboxes', 10, 4);

add_filter('bmcm_output_field_upload', 'bmcm_upload', 10, 4);

add_filter('bmcm_output_field_wysiwyg', 'bmcm_wysiwyg', 10, 4);

//TODO: Add more stock fields
//		- date
//		- date/time
//		- time
//		- multi
//		- taxonomy