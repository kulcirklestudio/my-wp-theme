<?php
namespace WooEasyDuplicateProduct;

require_once 'class-scripts.php';
require_once 'class-dashboard.php';
require_once 'class-metabox.php';
require_once 'class-duplicate.php';

/**
 * 
 */
class Init
{
	
	function __construct()
	{
		//This plugin should only activate in admin section

		if (!is_admin()){
			return;
		}

		$plugin_dir = plugin_dir_path(__FILE__);

		$plugin_dir = str_replace('includes/', '', $plugin_dir);

		define ('WEDP_PLUGIN_DIR', $plugin_dir);

		$this->scripts();

		new Dashboard;

		new Duplicate;

		new MetaBox;
	}

	public function scripts()
	{ 
		$scripts = new Scripts;
		add_action('wp_enqueue_scripts', [$scripts, 'wedp_scripts']);

	}


}