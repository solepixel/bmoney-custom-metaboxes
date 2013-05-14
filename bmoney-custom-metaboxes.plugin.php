<?php
/**
 * Plugin Name: BMoney Custom Metaboxes
 * Plugin URI: https://github.com/solepixel/bmoney-custom-metaboxes/
 * Description: Easily add custom metaboxes to your post types
<<<<<<< HEAD
 * Version: 1.533
=======
 * Version: 1.532
>>>>>>> 0ab471a11a99535494d6a481b78829f86bfadc20
 * Author: Brian DiChiara
 * Author URI: http://www.briandichiara.com
 */

<<<<<<< HEAD
define('BMCM_VERSION', '1.533');
=======
define('BMCM_VERSION', '1.532');
>>>>>>> 0ab471a11a99535494d6a481b78829f86bfadc20
define('BMCM_PI_NAME', 'Custom Metaboxes');
define('BMCM_PI_DESCRIPTION', 'Easily add custom metaboxes to your post types');
define('BMCM_OPT_PREFIX', 'bmcm_');
define('BMCM_PATH', plugin_dir_path( __FILE__ ));
define('BMCM_DIR', plugin_dir_url( __FILE__ ));

include_once(BMCM_PATH.'inc/functions.php');
include_once(BMCM_PATH.'inc/fields.php');
include_once(BMCM_PATH.'updater/updater.php');
require_once(BMCM_PATH.'classes/bm-custom-metaboxes.class.php');

global $bmcm_plugin;
$bmcm_plugin = new BM_Custom_Metaboxes();
$bmcm_plugin->initialize();
