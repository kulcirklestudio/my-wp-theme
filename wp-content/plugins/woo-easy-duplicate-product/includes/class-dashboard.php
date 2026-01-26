<?php
namespace WooEasyDuplicateProduct;

/**
 * 
 */
class Dashboard
{
	
	function __construct()
	{
		$this->init();
	}

	function wpgem_add_useful_info_box(){

		wp_add_dashboard_widget('wpgem-useful-info-box', 'Is your site too slow?', [$this, 'wpgem_useful_info_box'], 'normal', 'high');

	}

	function wpgem_useful_info_box(){
		include WEDP_PLUGIN_DIR . '/info-box.php';
	}
	function wedp_duplicate_product_admin_bar_button($admin_bar){

		global $pagenow;	
		global $post;

		if(!$post){
			return;
		}

		$post_type = $post->post_type;
		$action = (isset($_GET['action'])) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';
		if( 'product' != $post_type ){

			return;
		}

		if($pagenow == 'post.php' && 'product' != $post_type && $action != 'edit'){
			
			return ;
		}

		if($pagenow == 'edit.php'){
			
			return ;
		}

		if( 'product' == $post_type && !is_single() || !is_page()){
			//return; //@todo it's not clear why we have this here.
		}
		$_is_single = is_single();
		$_is_page = is_page();
		//print_r(get_defined_vars()); die();


		$duplicate_url = wp_nonce_url( admin_url( 'edit.php?post_type=product&action=duplicate_product&amp;post=' . $post->ID ), 'woocommerce-duplicate-product_' . $post->ID );

		$admin_bar_button_array = [

			'id' => 'wedp_admin_bar_button',
			'title' => 'Duplicate this product',
			'href' => $duplicate_url,
			'meta' => [
				
				'target' => '_blank',

			]
		];


		$speed_up_site_button_array =  [

			'id' => 'wpgem_speed_up_site_button',
			'title' => 'Want help?',
			'href' => 'https://drift.me/jeanpaul',
			'meta' => [

				'target' => '_blank'

			]

		];
		$admin_bar->add_menu($admin_bar_button_array);
		//$admin_bar->add_menu($speed_up_site_button_array);


	}
	
	function wedp_custom_bulk_admin_notices() {

	  if(isset($_GET['wedp_duplicated'])){
	
	  	$total_updated = sanitize_text_field(wp_unslash($_GET['wedp_duplicated']));
	  	$total_updated = preg_replace('/[^0-9]/', '', $total_updated); //Let's sanitize this

			if(!is_numeric($total_updated)){
				return;
			}

	  	$message = "{$total_updated} products duplicated.";

	  	echo  '<div class="updated"><p>'. esc_html($message) .'</p></div>';
	  }
	}

	public function add_plugin_links($plugin_actions, $plugin_file, $plugin_data = null)
	{
	/*	echo "\n\n\n";
			print_r($plugin_actions);
			
		echo "\n\n\n";

		print_r($plugin_file);
			
		echo "\n\n\n";*/

		if($plugin_file == 'woo-easy-duplicate-product/index.php'){

			$new_actions = [
				'support_desk' => '<a href="https://drift.me/jeanpaul">Support desk</a>'/*,
				'how_to_use' => '<a href="#wedp-how-to-use">How to use</a>'
				*/
			];

			foreach ($new_actions as $new_action) {
				array_push( $plugin_actions, $new_action);
			}

			if($plugin_data){
				array_push ($plugin_actions, '<a href="https://paypal.me/phpdevelopers/18usd">🍕Send me some pizza!</a>');
			}

		}
		return $plugin_actions;
	}

	public function init()
	{
		//add_action('wp_dashboard_setup', [$this, 'wpgem_add_useful_info_box']);
		add_action('admin_bar_menu', [$this, 'wedp_duplicate_product_admin_bar_button'], 110);
		add_action('admin_notices', [$this, 'wedp_custom_bulk_admin_notices']);
		add_filter( 'plugin_action_links', [$this, 'add_plugin_links'], 10, 2 );
		add_filter( 'plugin_row_meta',  [$this, 'add_plugin_links'], 10, 3 );

	}


}