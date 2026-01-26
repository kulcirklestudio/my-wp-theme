<?php
namespace WooEasyDuplicateProduct;

/**
 * 
 */
class MetaBox
{
	
	function __construct()
	{
		$this->metaboxes();
	}

	function wedp_add_the_metabox($_post){
	
		global $post;	

		$post_type = $post->post_type;

		if('product' != $post_type){
			return ;//$_post;
		}

		add_meta_box( 'woocommerce-easy-product-duplicate', __( 'Duplicate this product', 'woo-easy-duplicate-product' ), [$this, 'wedp_show_the_duplicate_link'], 'product', 'side', 'high' );

		return $_post;
	}

	function wedp_show_the_duplicate_link ($post){
		
		$url = '<a target="_blank" href="' . wp_nonce_url( admin_url( 'edit.php?post_type=product&action=duplicate_product&amp;post=' . $post->ID ), 'woocommerce-duplicate-product_' . $post->ID ) . '" aria-label="' . esc_attr__( 'Make a duplicate from this product', 'woo-easy-duplicate-product' )
				. '" rel="permalink">' . __( 'Duplicate once', 'woo-easy-duplicate-product' ) . '</a>';


		$multi_box = file_get_contents(WEDP_PLUGIN_DIR . '/multi-box.php');
	 
		$meta_box = $url . $multi_box;

		echo '<script>
			var wedp_product_id = '. esc_html($post->ID) .';

			var wedp_wp_nonce = "'.esc_html(wp_create_nonce( 'wedp-duplicate-product-nonce' )).'";

		</script>';
		echo $meta_box;
	}
	
	public function metaboxes()
	{
		add_action( 'add_meta_boxes', [$this, 'wedp_add_the_metabox'], 30 );

	}
}