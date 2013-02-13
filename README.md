BMoney Custom Metaboxes
=================

~Current Version:1.1~

Metaboxes are easy to add. Simply add a filter in your hook/theme:
<?php add_filter('bmcm_metaboxes', 'my_metabox_callback_function'); ?>

Then setup your callback function to add a metabox. This is the recommended setup:
<?php

function my_custom_post_type_fields(){
	return array(
		'_test_field_one' => array(
			'type' => 'text',
			'title' => 'Test Field One'
		),
		'_test_field_two' => array(
			'type' => 'text_small',
			'title' => 'Test Field Small'
		),
		'_test_field_three' => array(
			'type' => 'text_medium',
			'title' => 'Test Field Medium'
		),
		'_test_field_four' => array(
			'type' => 'text_large',
			'title' => 'Test Field Large'
		),
		'_test_field_money' => array(
			'type' => 'money',
			'title' => 'Test Field Money'
		),
		'_test_field_2' => array(
			'type' => 'textarea',
			'title' => 'Test Field 2',
			'default' => 'default text',
			'description' => 'Use this field to enter a lot of text'
		),
		'_test_field_3' => array(
			'type' => 'select',
			'title' => 'Test Field 3',
			'options' => array( // this also supports a callback function, just pass in the function name as string
				array('Select One', ''), // label, value
				array('One', '1'),
				array('Two', '2'),
				array('Three', '3')
			),
			'default' => '2'
		),
		
		/* alternate ID syntax */
		array(
			'id' => '_test_field_4',
			'title' => 'Select a Checkbox or Two',
			'type' => 'checkboxes',
			'checkboxes' => array( // this can also be called "options"
				array('Zero', '0'), // label, value
				array('Seven', '7'),
				array('Eight', '8'),
				array('Nine', '9')
			)
		),
		array(
			'id' => '_test_field_5',
			'title' => 'Select a Radio Button',
			'type' => 'radios',
			'radios' => array( // alternate option syntax
				array('label' => 'Aye', 'value' => 'A'), // assoc array of lable/value
				array('label' => 'Bee', 'value' => 'B'),
				array('label' => 'Sea', 'value' => 'C'),
				array('label' => 'Dee', 'value' => 'D')
			)
		),
		'_test_field_10' => array(
			'type' => 'wysiwyg',
			'title' => 'Test WYSIWYG'
		),
		'_test_field_50' => array(
			'type' => 'upload', // file also works here, same result
			'title' => 'Test Upload'
		),
		'_test_field_70' => array(
			'type' => 'checkbox',
			'title' => 'I agree to the terms and conditions in the giant text box above that there is no way I actually read.'
		)
	);
}

function my_metabox_callback_function($metaboxes=array()){
	
	$fields = apply_filters('my_metabox_field_filter', my_custom_post_type_fields());
	
	$metaboxes[] = array(
		'id'		=> 'my_awesome_metabox', // if left blank, one is auto-generated
		'title'		=> 'My Awesome Metabox',
		'post_type' => 'my_post_type', // supports single, or multiple post types, defaults to "post"
		'fields' 	=> $fields
	);
	
	return $metaboxes;
}

?>


Actions
===========

None yet.

Filters
===========

### bmcm_output_field_{$field_type}
* Filter the output of the field type, substiting $field_type with the type of field. Use this filter to add your own custom field types, or modify existing field types
* Vars: (int)$key, (array)$field, (obj)$post, (obj)$bmcm_class

### bmcm_wrap_field
* Field wrapping element, makes it look pretty.
* Vars: (string)$field_output, (array)$field

### bmcm_value_{$field_id}
* Allows you to manipulate the Posted value of the field per field_id
* Vars: (mixed)$value, (array)$field, (obj)$bmcm_class

### bmcm_update_meta
* Last manipulation of the data before inserting/updating in the database
* Vars: (mixed)$value, (int)$post_id, (string)$name, (obj)$bmcm_class


Changelog
===========

### 1.1
* Added documentation
* More supported field types
* Added some filters for validating user input
* Action priority adjustment
* Minor bug fixes

### 1.0
* Initial build
