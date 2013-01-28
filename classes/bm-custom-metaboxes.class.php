<?php

if(class_exists('BM_Custom_Metaboxes')) return;
	
class BM_Custom_Metaboxes {
	
	var $debug = false;
	
	var $admin_page = 'bmoney-custom-metaboxes';
	
	var $always_serialize = array();
	var $metaboxes = array();
	
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
	}
	
	/**
	 * BM_Custom_Metaboxes::_init()
	 * 
	 * @return void
	 */
	function _init(){
		if(is_admin()){
			
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
			
			$this->metaboxes = apply_filters('bmcm_metaboxes', $this->metaboxes);
			
			$this->always_serialize = array(
				'checkboxes'
			);
			
			$this->js_vars = array(
				'title'     => __( 'Upload or Choose Your File', 'bmcm' ), 
				'button'    => __( 'Use File', 'bmcm' )
            );
			
			require_once(BMCM_PATH.'field-filters.php');
			
			wp_register_style('bmcm-styles', BMCM_DIR.'/css/bmcm-styles.css', array(), BMCM_VERSION);
			wp_register_script('bmcm-scripts', BMCM_DIR.'/js/bmcm-scripts.js', array('jquery'), BMCM_VERSION);
	        wp_register_script('bmcm-media', BMCM_DIR.'/js/media.js', array('jquery'), BMCM_VERSION);
			
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
		wp_enqueue_style('bmcm-styles');
		wp_enqueue_script('bmcm-scripts');
		wp_enqueue_script('bmcm-media-vars');
		wp_enqueue_script('bmcm-media');
		wp_localize_script( 'bmcm-media-vars', 'bmcm_media_vars', $this->js_vars);
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
		
		include(BMPTC_PATH.'/admin/settings.php');
	}
	
	/**
	 * BM_Custom_Metaboxes::setup_metaboxes()
	 * 
	 * @return void
	 */
	function setup_metaboxes(){
		if(count($this->metaboxes)){
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
		}
		
		foreach($this->metaboxes as $index => $metabox){
			
			if(!isset($metabox['context'])) $metabox['context'] = 'normal';
			if(!isset($metabox['priority'])) $metabox['priority'] = 'high';
			
			add_meta_box(
				$metabox['id'],
				$metabox['title'],
				array(&$this, 'metabox_callback'),
				$metabox['post_type'],
				$metabox['context'],
				$metabox['priority'],
				$metabox['fields']
			);
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
		$fields = $metabox['args'];
		
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
			'checkboxes' => array(),
			'attributes' => array(),
			'settings' => array()
		);
		
		$settings = array(
			'textarea_rows' => 6
		);
		
		$index = 0;
		$total_fields = count($fields);
		foreach($fields as $key => $field){
			if(!$field['type']) continue;
			
			$field = array_merge($defaults, $field);
			
			$field['id'] = $this->set_id($key, $field);
			
			if(in_array($field['type'], $this->always_serialize)){
				$field['serialize'] = true;
			}
			if($field['type'] == 'money'){
				$field['before'] = '$';
			} elseif($field['type'] == 'wysiwyg'){
				$field['settings'] = array_merge($settings, $field['settings']);
			}
			
			$field['name'] = BMCM_OPT_PREFIX.$field['id'];
			
			if($field['serialize']){
				$field['name'] = $field['name'].'[]';
			}
			
			$field['value'] = $this->set_value($field, $post);
			
			$field['_index'] = $index;
			$field['_total'] = $total_fields;
			
			$output = apply_filters('bmcm_output_field_'.$field['type'], $key, $field, $post, $this);
			$element = apply_filters('bmcm_wrap_field', $output, $field);
			
			echo $element;
			$index++;
		}
	}
	
	/**
	 * BM_Custom_Metaboxes::set_id()
	 * 
	 * @param mixed $key
	 * @param mixed $field
	 * @return
	 */
	function set_id($key, $field){
		$id = !$field['id'] ? $key : $field['id'];
		
		if(is_numeric($id)){
			if($field['name']){
				$id = $field['name'];
			}
		}
		return $id;
	}
	
	/**
	 * BM_Custom_Metaboxes::set_value()
	 * 
	 * @param mixed $field
	 * @param mixed $post
	 * @return
	 */
	function set_value($field, $post){
		$value = ($field['default']) ? $field['default'] : '';
		
		if($post->ID){
			$stored = get_post_meta($post->ID, $field['id'], false);
			if(count($stored)){
				$value = $stored[0];
			}
		}
		
		if(isset($_POST[BMCM_OPT_PREFIX.$field['default']])){
			$value = $_POST[BMCM_OPT_PREFIX.$field['id']];
		}
		
		if($field['type'] == 'money'){
			$value = number_format($value, 2);
		}
		
		return $value;
	}
	
	/**
	 * BM_Custom_Metaboxes::save_values()
	 * 
	 * @param mixed $post_id
	 * @return void
	 */
	function save_values($post_id){
		if(isset($_POST) && count($_POST) > 0){
			$reset_fields = $this->get_reset_fields();
			foreach($_POST as $k => $v){
				if(substr($k, 0, strlen(BMCM_OPT_PREFIX)) == BMCM_OPT_PREFIX){
					$name = substr($k, strlen(BMCM_OPT_PREFIX));
					if(($del = array_search($name, $reset_fields)) !== false) {
						unset($reset_fields[$del]);
					}
					update_post_meta( $post_id, $name, $v );
				}
			}
			foreach($reset_fields as $field){
				update_post_meta( $post_id, $field, array() );
			}
		}
	}
	
	/**
	 * BM_Custom_Metaboxes::get_reset_fields()
	 * 
	 * @return
	 */
	function get_reset_fields(){
		$reset_fields = array();
		foreach($this->metaboxes as $metabox){
			foreach($metabox['fields'] as $key => $field){
				if($field['type'] == 'checkboxes' || $field['type'] == 'checkbox'){
					$id = $this->set_id($key, $field);
					$reset_fields[] = $id;
				}
			}
		}
		return $reset_fields;
	}
}
