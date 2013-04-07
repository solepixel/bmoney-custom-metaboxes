<?php

if(class_exists('BM_Custom_Metaboxes')) return;
	
class BM_Custom_Metaboxes {
	
	var $debug = false;
	
	var $admin_page = 'bmoney-custom-metaboxes';
	
	var $always_serialize = array();
	var $metaboxes = array();
	
	var $post = NULL;
	
	var $tabs = array();
	var $last_tab = NULL;
	var $fields = array();
	var $index = 0;
	var $tab_index = 0;
	var $total_fields = 0;
	
	var $js_vars = array();
	var $settings = array();
	
	/**
	 * BM_Custom_Metaboxes::__construct()
	 * 
	 * @return void
	 */
	function __construct(){
		
	}
	
	/**
	 * BM_Custom_Metaboxes::initialize()
	 * 
	 * @return void
	 */
	function initialize(){
		add_action( 'init', array($this, '_init'));
		add_action( 'admin_init', array($this, 'build_metaboxes'), 9999 );
	}
	
	/**
	 * BM_Custom_Metaboxes::_init()
	 * 
	 * @return void
	 */
	function _init(){
		if(is_admin()){
			
			$this->_check_for_updates();
			
			$this->always_serialize = array(
				'checkboxes'
			);
			
			$this->js_vars = array(
				'title'     => __( 'Upload or Choose Your File', 'bmcm' ), 
				'button'    => __( 'Use File', 'bmcm' ),
				'selection_callback'	=> apply_filters('bmcm_js_select_callback', ''),
				'remove_callback'		=> apply_filters('bmcm_js_remove_callback', '')
            );
			
			require_once(BMCM_PATH.'field-filters.php');
			
			wp_register_style('bmcm-styles', BMCM_DIR.'css/bmcm-styles.css', array(), BMCM_VERSION);
			wp_register_script('bmcm-scripts', BMCM_DIR.'js/bmcm-scripts.js', array('jquery'), BMCM_VERSION);
	        wp_register_script('bmcm-media', BMCM_DIR.'js/media.js', array('jquery','media-upload','media-views'), BMCM_VERSION);
	        wp_localize_script('bmcm-media', 'bmcm_media_vars', $this->js_vars);
	        wp_register_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/flick/jquery-ui.css', array(), '1.9.1');
				
			#add_action('admin_menu', array($this, '_admin_menu'));
			add_action('admin_init', array($this, 'setup_metaboxes'));
			add_action('save_post', array($this, 'save_values') );
		}
		
	}
	
	/**
	 * BM_Custom_Metaboxes::enqueue_admin_scripts()
	 * 
	 * @return void
	 */
	function enqueue_admin_scripts(){
		wp_enqueue_media();
		wp_enqueue_style('bmcm-styles');
		wp_enqueue_script('bmcm-scripts');
		wp_enqueue_script('bmcm-media');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_style('jquery-style');
	}
	
	/**
	 * BM_Custom_Metaboxes::_admin_menu()
	 * 
	 * @return void
	 */
	function _admin_menu(){
		add_submenu_page('upload.php', BMCM_PI_NAME, BMCM_PI_NAME, 8, $this->admin_page, array($this, '_settings_page'));
	}
	
	
	/**
	 * BM_Custom_Metaboxes::_settings_updated()
	 * 
	 * @return void
	 */
	function _settings_updated(){
		 echo '<div class="updated"><p>Your settings have been updated.</p></div>';
	}
	
	/**
	 * BM_Custom_Metaboxes::_settings_page()
	 * 
	 * @return void
	 */
	function _settings_page(){
		if(isset($_POST) && count($_POST) > 0){
			foreach($_POST as $k => $v){
				if(substr($k, 0, strlen(BMCM_OPT_PREFIX)) == BMCM_OPT_PREFIX){
					$this->settings[$k] = sanitize_text_field($v);
				}
			}
			update_option(BMCM_OPT_PREFIX.'settings', $this->settings);
			add_action('admin_notices', array($this, '_settings_updated'));
		}
		
		include(BMCM_PATH.'/admin/settings.php');
	}
	
	/**
	 * BM_Custom_Metaboxes::setup_metaboxes()
	 * 
	 * @return void
	 */
	function setup_metaboxes(){
		$this->metaboxes = apply_filters('bmcm_metaboxes', $this->metaboxes);
	}
	

	/**
	 * BM_Custom_Metaboxes::build_metaboxes()
	 * 
	 * @return void
	 */
	function build_metaboxes(){
		if(count($this->metaboxes)){
			
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			foreach($this->metaboxes as $index => $metabox){
				
				if(!isset($metabox['id'])) $metabox['id'] = uniqid('bmcm_');
				if(!isset($metabox['title'])) $metabox['title'] = 'Untitled Metabox';
				if(!isset($metabox['post_type'])) $metabox['post_type'] = array('post');
				if(!isset($metabox['context'])) $metabox['context'] = 'normal';
				if(!isset($metabox['priority'])) $metabox['priority'] = 'high';
				if(!isset($metabox['fields'])) $metabox['fields'] = array();
				if(!isset($metabox['tabs'])) $metabox['tabs'] = array();
				
				// use our default callback.
				if(!isset($metabox['callback'])) $metabox['callback'] = array(&$this, 'metabox_callback');

				if(!is_array($metabox['post_type'])){
					$metabox['post_type'] = array($metabox['post_type']);
				}
				
				global ${$metabox['id'].'_postbox_class'};
				${$metabox['id'].'_postbox_class'} = array('bmcm');
				
				if(isset($metabox['class'])){
					if(!is_array($metabox['class'])) $metabox['class'] = array($metabox['class']);
					${$metabox['id'].'_postbox_class'} = array_merge(${$metabox['id'].'_postbox_class'}, $metabox['class']);
				}
				
				foreach($metabox['post_type'] as $post_type){
					add_filter('postbox_classes_'.$post_type.'_'.$metabox['id'],
						create_function('', 'global $'.$metabox['id'].'_postbox_class; return $'.$metabox['id'].'_postbox_class;') );
						
					add_meta_box(
						$metabox['id'],
						$metabox['title'],
						$metabox['callback'],
						$post_type,
						$metabox['context'],
						$metabox['priority'],
						array('fields' => $metabox['fields'], 'tabs' => $metabox['tabs'])
					);
				}
			}
		}
	}
	
	/**
	 * BM_Custom_Metaboxes::metabox_callback()
	 * 
	 * @param mixed $post
	 * @param mixed $metabox
	 * @return void
	 */
	function metabox_callback($post, $metabox){
		$this->fields = $metabox['args']['fields'];
		$this->tabs = $metabox['args']['tabs'];
		$this->index = 0;
		$this->total_fields = count($this->fields);
		$this->post = $post;
		
		foreach($this->fields as $key => $field){
			$this->fields[$key] = $this->cleanup_field($field, $key);
			$this->index++;
		}
		
		$total_tabs = count($this->tabs);
		
		if($total_tabs > 1){
			
			$this->add_tab_totals();
			
			echo '<div class="bmcm-tab-wrap">';
			echo '<ul class="bmcm-tabs">';
			$i = 0;
			$total_tabs = count($this->tabs);
			foreach($this->tabs as $tab_id => $tab_label){
				$classes = array();
				if($i == 0) $classes[] = 'first';
				if($i == 0) $classes[] = 'active';
				if($i == $total_tabs-1) $classes[] = 'last';
				$class = (count($classes)) ? ' class="'.implode(' ', $classes).'"' : '';
				echo '<li'.$class.'><a href="#'.$tab_id.'" class="'.$tab_id.'-tab">'.$tab_label.'</a></li>';
				$i++;
			}
			echo '</ul>';
			
			$last_tab = NULL;
			$tab_contents = '';
		}
		
		foreach($this->fields as $key => $field){
			
			if(isset($field['tab']) && $total_tabs > 1){
				if($field['tab'] != $last_tab){
					if($last_tab !== NULL){
						$tab_contents .= '</div>';
					}
					if($field['tab']){
						$tab_contents .= '<div class="bmcm-tab tab-'.$field['tab'].'"';
						if($last_tab !== NULL) $tab_contents .= ' style="display:none;"';
						$tab_contents .= '>';
					}
				}
			}
			
			$output = apply_filters('bmcm_output_field_'.$field['type'], $key, $field, $this->post, $this);
			$element = apply_filters('bmcm_wrap_field', $output, $field);
			
			if(isset($field['tab']) && $total_tabs > 1){
				$tab_contents .= $element;
				$last_tab = $field['tab'];
			} else {
				echo $element;
			}
		}
		
		if($total_tabs > 1){
			if($last_tab !== NULL) $tab_contents .= '</div>';
			if($tab_contents){
				echo '<div class="bmcm-tab-contents">';
					echo $tab_contents;
				echo '</div>
				<div class="clear"><!-- .clear --></div>
				</div>';
			}
		}
		
		echo '<div class="clear"><!-- .clear --></div>';
	}
	
	
	/**
	 * cleanup_field()
	 * 
	 * @param mixed $field
	 * @param mixed $key
	 * @return
	 */
	function cleanup_field($field, $key=NULL){
		$defaults = array( // avoid PHP notices
			'id' => '',
			'name' => '',
			'type' => '',
			'before' => '',
			'after' => '',
			'title' => '',
			'description' => '',
			'default' => '',
			'serialize' => false,
			'options' => array(),
			'settings' => array(),
			'attributes' => array(),
			'taxonomy' => ''
		);
		
		$wysiwyg_settings = array(
			'textarea_rows' => 6
		);
		
		$multi_settings = array(
			'sortable' => false
		);
		
		$field['type'] = $this->standardize_type($field['type']);
			
		$field = array_merge($defaults, $field);
		
		$is_multi = in_array($field['type'], array('multi','multiple','repeat','repeatable')); //TODO: fix this...
		
		if($is_multi){
			$field['serialize'] = true;
		}
		
		$field['id'] = $this->set_id($field, $key);
		$field['name'] = $this->set_name($field, $key);
		
		if(in_array($field['type'], $this->always_serialize)){
			$field['serialize'] = true;
		}
		
		if($field['type'] == 'money'){
			$field['before'] = '$'; //TODO: internationalize
		} elseif($field['type'] == 'wysiwyg'){
			$field['settings'] = array_merge($wysiwyg_settings, $field['settings']);
		} elseif($field['type'] == 'multi'){
			$field['settings'] = array_merge($multi_settings, $field['settings']);
		}
		
		if(isset($field['checkboxes'])){
			$field['options'] = $field['checkboxes'];
			unset($field['checkboxes']);
		}
		if(isset($field['radios'])){
			$field['options'] = $field['radios'];
			unset($field['radios']);
		}
		
		if($field['type'] == 'taxonomy' && $field['taxonomy']){
			$field['settings'] = array_merge(array('hide_empty' => false), $field['settings']);
			$terms = get_terms($field['taxonomy'], $field['settings']);
			$field['options'] = array(
				array('label' => 'Select One', 'value' => '')
			);
			foreach($terms as $term){
				$field['options'][] = array('label' => $term->name, 'value' => $term->term_id);
			}
		}
		
		if(is_string($field['options']) && function_exists($field['options'])){
			$field['options'] = call_user_func($field['options'], $field);
		} elseif(!is_array($field['options'])) {
			$field['options'] = array(
				array('value' => '', 'label' => __('INVALID OPTIONS PARAMETER', 'bmcm'))
			);
		}
		
		if($field['type'] == 'date'){
			$field['attributes'] = array_merge(array('class' => 'datepicker'), $field['attributes']);
		}
		
		$field['value'] = $this->set_value($field);
		
		if(isset($field['tab'])){
			if($this->last_tab != $field['tab']){
				$this->tab_index = 0;
			}
			$this->last_tab = $field['tab'];
			$field['_tab_index'] = $this->tab_index;
			$this->tab_index++;
		}
		
		$field['_index'] = $this->index;
		$field['_total'] = $this->total_fields;
		
		// probably should do this last...
		if($is_multi){
			$field = $this->prepare_multiple($field);
		}
		
		return $field;
	}
	
	
	/**
	 * prepare_multiple()
	 * 
	 * @param mixed $field
	 * @return
	 */
	function prepare_multiple($field){
		// start a new index for child fields
		$og_index = $this->index;
		$og_total = $this->total_fields;
		
		$this->index = 0;
		$this->total_fields = count($field['fields']);
		
		// setup the child fields.
		foreach($field['fields'] as $item_key => $item_field){
			$item_id = $item_field['id'];
			$item_field['id'] = $field['id'].'_'.$item_field['id'];
			$item_field['is_multi'] = true;
			$field['fields'][$item_key] = $this->cleanup_field($item_field, $item_key);
			$field['fields'][$item_key]['name'] = str_replace('[]', '', $field['name']).'['.$item_id.'][]';
			if($this->total_fields == 1){
				$field['fields'][$item_key]['title'] = $field['title'];
			}
			$this->index++;
		}
		
		$this->index = $og_index;
		$this->total_fields = $og_total;
		
		$additional = '';
		
		if(is_array($field['value'])){
			
			$number_of_fields = count($field['fields']);
			
			foreach($field['value'] as $i => $val){
				if($i == 0){
					// our first fields are already there, re-use them
					foreach($val as $field_key => $field_val){
						foreach($field['fields'] as $field_index => $child_field){
							$check_id = str_replace($field['id'].'_','', $child_field['id']);
							if($check_id == $field_key){
								$field['fields'][$field_index]['value'] = $field_val;
							}
						}
					}
				} else {
					$new_field = $field;
					$new_field['type'] = 'multi_additional';
					
					// reset the field values from the original
					foreach($new_field['fields'] as $new_subfield_key => $new_subfield_field){
						$new_subfield_field['value'] = '';
						$new_field['fields'][$new_subfield_key] = $new_subfield_field;
					}
					
					$this->index++;
					$this->total_fields++;
					$new_field['_index'] = $this->index;
					$new_field['_total'] = $this->total_fields;
					if(isset($new_field['tab'])) $new_field['_tab_index']++;
					//TODO: fix tab total for last multi element.
					
					// set the individual field values here
					foreach($val as $field_key => $field_val){
						foreach($new_field['fields'] as $field_index => $child_field){
							$check_id = str_replace($new_field['id'].'_','', $child_field['id']);
							if($check_id == $field_key){
								$new_field['fields'][$field_index]['value'] = $field_val;
							}
						}
					}
					
					// build the fields, and add to multi_additional output
					$new_field_output = apply_filters('bmcm_output_field_'.$new_field['type'], $i, $new_field, $this->post, $this);
					$additional .= apply_filters('bmcm_wrap_field', $new_field_output, $new_field);
				}
			}
		}
		
		if($additional){
			$field['additional'] = $additional;
			// reset total for all fields
			$field['_total'] = $this->total_fields;
			foreach($this->fields as $index => $item){
				$this->fields[$index]['_total'] = $this->total_fields;
			}
		}
		
		return $field;
	}
	
	function add_tab_totals(){
		$last_tab = 0;
		foreach($this->fields as $key => $field){
			if(isset($field['tab']) && $field['_tab_index'] == 0 && $field['_index'] > 0){
				$parts = array_slice($this->fields, $last_tab, $field['_index'], true);
				$total_parts = count($parts);
				foreach($parts as $index => $part){
					$this->fields[$index]['_tab_total'] = $total_parts;
				}
				$last_tab = $field['_index'];
			}
		}
		
		$total_tabs = count($this->fields);
		$last_section = $total_tabs - $last_tab+1;
		if($last_section){
			$parts = array_slice($this->fields, $last_tab+1, $last_section, true);
			$total_parts = count($parts);
			foreach($parts as $index => $part){
				$this->fields[$index]['_tab_total'] = $total_parts;
			}
		}
	}
	
	/**
	 * BM_Custom_Metaboxes::set_id()
	 * 
	 * @param mixed $key
	 * @param mixed $field
	 * @return
	 */
	function set_id($field, $key=NULL){
		$id = !$field['id'] ? $key : $field['id'];
		
		if(is_numeric($id)){
			if($field['name']){
				$id = $field['name'];
			}
		}
		return $id;
	}
	
	function set_name($field, $key=NULL){
		$base_name = BMCM_OPT_PREFIX.$field['id'];
		$name = $base_name;
		
		if($field['serialize'] && substr($name, -2) != '[]'){
			$name .= '[]';
		}
		
		if(isset($field['is_multi']) && substr($name, -2) != '[]'){
			$name .= '[]';
		}
		
		return $name;
	}
	
	function standardize_type($type){
		if(substr($type, -3) == '_sm'){
			$type = str_lreplace('_sm', '_small', $type);
		} elseif(substr($type, -4) == '_med'){
			$type = str_lreplace('_med', '_medium', $type);
		} elseif(substr($type, -3) == '_lg'){
			$type = str_lreplace('_lg', '_large', $type);
		}
		if(!$type) $type = 'text';
		return $type;
	}
	
	/**
	 * BM_Custom_Metaboxes::set_value()
	 * 
	 * @param mixed $field
	 * @param mixed $post
	 * @return
	 */
	function set_value($field, $parent=NULL){
		$value = ($field['default']) ? $field['default'] : '';
		
		if($this->post->ID){
			if($field['type'] == 'taxonomy' && $field['taxonomy']){
				$terms = wp_get_post_terms( $this->post->ID, $field['taxonomy']);
				if(count($terms))
					$value = $terms[0]->term_id;
			} else {
				$stored = get_post_meta($this->post->ID, $field['id'], false);
				if(count($stored)){
					$value = $stored[0];
				}
			}
		}
		
		if(isset($_POST[BMCM_OPT_PREFIX.$field['default']])){
			$value = $_POST[BMCM_OPT_PREFIX.$field['id']];
		}
		
		if($field['type'] == 'money' && $value){
			$value = number_format((double)$value, 2);
		}
		
		return apply_filters('bmcm_value_'.$field['id'], $value, $field, $this);
	}
	
	/**
	 * BM_Custom_Metaboxes::save_values()
	 * 
	 * @param mixed $post_id
	 * @return void
	 */
	function save_values($post_id){
		if(isset($_POST) && count($_POST) > 0){
			$multi_fields = $this->get_fields(array('multi','multiple','repeat','repeatable')); //TODO: fix this...
			$reset_fields = $this->get_fields(array('checkboxes','checkbox'));
			$taxonomy_fields = $this->get_fields(array('taxonomy'), 'taxonomy');
			
			foreach($_POST as $k => $v){
				if(substr($k, 0, strlen(BMCM_OPT_PREFIX)) == BMCM_OPT_PREFIX){
					$v = is_array($v) ? $this->recursive_sanitize($v) : sanitize_text_field($v);
					$name = substr($k, strlen(BMCM_OPT_PREFIX));
					
					if(($del = array_search($name, $reset_fields)) !== false) {
						unset($reset_fields[$del]);
					}
					
					if(($multi = array_search($name, $multi_fields)) !== false){
						$new_val = array();
						
						//rewrite the post array
						foreach($v as $var => $vals){
							foreach($vals as $index => $val){
								$new_val[$index][$var] = $val;
							}
						}
						
						$v = $new_val;
					}
					
					$v = apply_filters('bmcm_update_meta', $v, $post_id, $name, $this);
					
					if(in_array($name, $taxonomy_fields) && $taxonomy = array_search($name, $taxonomy_fields)){
						wp_set_post_terms( $post_id, $v, $taxonomy );
					} else {
						update_post_meta( $post_id, $name, $v );
					}
				}
			}
			
			foreach($reset_fields as $field){
				update_post_meta( $post_id, $field, array() );
			}
		}
	}
	
	/**
	 * BM_Custom_Metaboxes::recursive_sanitize()
	 * 
	 * @param mixed $array
	 * @return
	 */
	function recursive_sanitize($array=array()){
		if(is_string($array)){
			return sanitize_text_field($array);
		} elseif(is_array($array)){
			$new_array = array();
			foreach($array as $k => $v){
				$new_array[$k] = $this->recursive_sanitize($v);
			}
			return $new_array;
		}
		return $array;
	}
	
	/**
	 * BM_Custom_Metaboxes::get_fields()
	 * 
	 * @param mixed $types
	 * @return
	 */
	function get_fields($types=array(), $key_val=NULL){
		if(!is_array($types)) $types = array($types);
		
		$fields = array();
		foreach($this->metaboxes as $metabox){
			if(!is_array($metabox['post_type'])){
				$metabox['post_type'] = array($metabox['post_type']);
			}
			if(in_array(get_post_type(), $metabox['post_type'])){ //TODO: support nested multi fields here.
				foreach($metabox['fields'] as $key => $field){
					if(in_array($field['type'], $types)){
						$id = $this->set_id($field, $key);
						if($key_val){
							$fields[$field[$key_val]] = $id;
						} else {
							$fields[] = $id;
						}
					}
				}
			}
		}
		return $fields;
	}
	
	
	
	function _check_for_updates(){
		if(class_exists('WP_GitHub_Updater')){
			$config = array(
				'slug' => 'bmoney-custom-metaboxes/bmoney-custom-metaboxes.plugin.php', // this is the slug of your plugin
				'proper_folder_name' => 'bmoney-custom-metaboxes', // this is the name of the folder your plugin lives in
				'api_url' => 'https://api.github.com/solepixel/bmoney-custom-metaboxes', // the github API url of your github repo
				'raw_url' => 'https://raw.github.com/solepixel/bmoney-custom-metaboxes/master/', // the github raw url of your github repo
				'github_url' => 'https://github.com/solepixel/bmoney-custom-metaboxes', // the github url of your github repo
				'zip_url' => 'https://github.com/solepixel/bmoney-custom-metaboxes/archive/master.zip', // the zip url of the github repo
				'sslverify' => false, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
				'requires' => '3.0', // which version of WordPress does your plugin require?
				'tested' => '3.5', // which version of WordPress is your plugin tested up to?
				'readme' => 'README.md', // which file to use as the readme for the version number
				'access_token' => '', // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
			);
			new WP_GitHub_Updater($config);
		}
	}
}
