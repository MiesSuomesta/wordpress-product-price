<?php 
/**
 * Hello World
 *
 * @package     Product Price
 * @author      Lauri Jakku
 * @copyright   2022 Lauri Jakku
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Product Prices
 * Plugin URI:  https://lja.fi/wp-plugins/product-price
 * Description: Fetches product price info
 * Version:     1.0.0
 * Author:      Lauri Jakku
 * Author URI:  https://lja.fi/
 * Text Domain: Product price shortcode
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

$PRODUCT_PRICE_PLUGIN_DIR = WP_PLUGIN_DIR . '/product-price/';	
$PRODUCT_PRICE_PLUGIN_SLUG = 'product-price';	

function get_woo_product_sale_price_shortcode( $atts ) { 
	 
	$atts = shortcode_atts( array( 
		'id' => null,
		'currency' => null,
		'currency_space' => null,
		'html_elem' => null,
		'price_only' => null,
		'price_type' => null,
	), $atts, 'get_product_sale_price' ); 

  
	if ( empty( $atts[ 'id' ] ) ) { 
		return ''; 
	} 

	$currency = "€";
	if ( ! empty( $atts['currency'] ) )
	{ 
		$currency = $atts[ 'currency' ];
	} 
	
	$currency_space = " ";
	if ( ! empty( $atts['currency_space'] ) )
	{ 
		$currency_space = $atts[ 'currency_space' ];
	} 
	
	$html_elem = "h2";
	if ( ! empty( $atts['html_elem'] ) )
	{ 
		$html_elem = $atts[ 'html_elem' ];
	} 
	
	$price_only = false;
	if ( ! empty( $atts['price_only'] ) )
	{ 
		$price_only = $atts['price_only'] ;
	}
  
	$product = wc_get_product( $atts['id'] ); 

  
	if ( ! $product ) { 
		return ''; 
	} 

	$price_type = false;
	if ( ! empty( $atts['price_type'] ) )
	{ 
		$price_only = $atts['price_type'] ;
	}
	
	if ($product -> is_on_sale()) {
		$price 	= $product->get_sale_price();
	
		if ( ! $price_only ) {
			$currency = $currency_space . $currency;
		} else {
			$currency = "";
		}
		return $price . $currency;
	}
	
	return "";
}

function get_woo_product_regular_price_shortcode( $atts ) { 

	 
	$atts = shortcode_atts( array( 
		'id' => null,
		'currency' => null,
		'currency_space' => null,
		'html_elem' => null,
		'price_only' => null,
	), $atts, 'get_product_regular_price' ); 

  
	if ( empty( $atts[ 'id' ] ) ) { 
		return ''; 
	} 

	$currency = "€";
	if ( ! empty( $atts['currency'] ) )
	{ 
		$currency = $atts[ 'currency' ];
	} 
	
	$currency_space = " ";
	if ( ! empty( $atts['currency_space'] ) )
	{ 
		$currency_space = $atts[ 'currency_space' ];
	} 
	
	$html_elem = "h2";
	if ( ! empty( $atts['html_elem'] ) )
	{ 
		$html_elem = $atts[ 'html_elem' ];
	} 
	
	$price_only = false;
	if ( ! empty( $atts['price_only'] ) )
	{ 
		$price_only = $atts['price_only'] ;
	}
  
	$product = wc_get_product( $atts['id'] ); 

  
	if ( ! $product ) { 
		return ''; 
	} 

	$price 		= $product->get_regular_price();
	
	if ( ! $price_only ) {
		$currency = $currency_space . $currency;
	} else {
		$currency = "";
	}
	
	return $price . $currency;
}


function get_woo_product_sale_percent_shortcode( $atts ) { 

	$atts = shortcode_atts( array( 
		'id' => null,
		'currency' => null,
		'currency_space' => null,
		'html_elem' => null,
		'price_only' => null,
	), $atts, 'get_product_sale_percent' ); 

  
	if ( empty( $atts[ 'id' ] ) ) { 
		return ''; 
	} 

	$currency = "€";
	if ( ! empty( $atts['currency'] ) )
	{ 
		$currency = $atts[ 'currency' ];
	} 
	
	$currency_space = " ";
	if ( ! empty( $atts['currency_space'] ) )
	{ 
		$currency_space = $atts[ 'currency_space' ];
	} 
	
	$html_elem = "h2";
	if ( ! empty( $atts['html_elem'] ) )
	{ 
		$html_elem = $atts[ 'html_elem' ];
	} 
	
	$price_only = false;
	if ( ! empty( $atts['price_only'] ) )
	{ 
		$price_only = $atts['price_only'] ;
	}
  
	$product = wc_get_product( $atts['id'] ); 

  
	if ( ! $product ) { 
		return ''; 
	} 

	$price 		= $product->get_regular_price();
	$sale_price 	= $price;
	if ($product -> is_on_sale()) {
		$sale_price 	= $product->get_sale_price();
	}
	
	$percent = ( 100 * ( 1 - ($sale_price / $price) ));
	
	return $percent;
}

function get_woo_product_sale_banner_shortcode( $atts ) { 

	$atts = shortcode_atts( array( 
		'id' => null,
	), $atts, 'get_product_sale_banner' ); 

  
	if ( empty( $atts[ 'id' ] ) ) { 
		return ''; 
	} 

	$currency = "€";
	if ( ! empty( $atts['currency'] ) )
	{ 
		$currency = $atts[ 'currency' ];
	} 
	
	$product = wc_get_product( $atts['id'] ); 

  
	if ( ! $product ) { 
		return ''; 
	} 

	if ($product -> is_on_sale()) {
		if (strpos($HTTP_HOST, ".com") > 0) {
			$is_on_sale = "SALE";
		} else {
			$is_on_sale = "ALE";
		}
	}
	
	return $is_on_sale;
}

/* Add shortcodes -----------------------------------------------------------------------------*/
add_shortcode( 'get_product_regular_price', 	 'get_woo_product_regular_price_shortcode' ); 
add_shortcode( 'get_product_sale_price', 	 'get_woo_product_sale_price_shortcode'    ); 
add_shortcode( 'get_product_sale_percent',	 'get_woo_product_sale_percent_shortcode'  ); 
add_shortcode( 'get_product_sale_banner',	 'get_woo_product_sale_banner_shortcode'  ); 


//add_action( 'wp', 'update_hourly_post_type_update_info');
//function update_hourly_post_type_update_info()
//{       // Make sure this event hasn't been scheduled
//	if( !wp_next_scheduled( 'wp_product_variation_hourly' ) ) 
//	{       // Schedule the event
//		wp_schedule_event( time(), 'hourly', 'wp_product_variation_hourly' );
//	}
//}
//
////Here you need to add the function of XML which replace the price
//function wp_product_variation_hourly()
//{
//	//Your Price updating Code here.
//	$xml=simplexml_load_file($PRODUCT_PRICE_PLUGIN_PRICE_XML) or die("Error: Cannot create object from " . $PRODUCT_PRICE_PLUGIN_PRICE_XML);
//	print_r($xml);
//}
?>
