<?php 
/**
 * Hello World
 *
 * @package     Wordpress Product Prices
 * @author      Lauri Jakku
 * @copyright   2022 Lauri Jakku
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Wordpress Product Prices
 * Plugin URI:  https://lja.fi/wp-plugins/wordpress-product-price
 * Description: Fetches product price info
 * Version:     1.0.1
 * Author:      Lauri Jakku
 * Author URI:  https://lja.fi/
 * Text Domain: Product price shortcodes
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

$PRODUCT_PRICE_PLUGIN_SLUG = 'wordpress-product-price';	
$PRODUCT_PRICE_PLUGIN_DIR  = WP_PLUGIN_DIR . '/wordpress-product-price/';	
$PRODUCT_PRICE_PLUGIN_CSS  = $PRODUCT_PRICE_PLUGIN_SLUG . '.css';

function my_css_loader() {
	global $PRODUCT_PRICE_PLUGIN_CSS;
 	$css_url = plugins_url( $PRODUCT_PRICE_PLUGIN_CSS ,  __FILE__ );
	wp_enqueue_style( 'style',  $css_url);
}

add_action( 'wp_enqueue_scripts', 'my_css_loader' );

function get_woo_product_shortcode_atts( $shortcode_name, $atts ) { 
	 
	$atts = shortcode_atts( array( 
		'id' => null,
		'currency' => null,
		'currency_space' => null,
		'html_elem' => null,
		'price_only' => null,
		'price_type' => null,
	), $atts, $shortcode_name ); 

	if ( empty( $atts[ 'id' ] ) ) { 
		return ''; 
	} 

	return $atts;
}

function get_currency_from_atts($atts)
{
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
	
	return $currency . $currency_space;
}

function do_decoration($on_sale, $css_class_for_sale, $css_class_for_normal, $item, $element)
{
	if ($on_sale)
		$use_css = $css_class_for_sale;
	else
		$use_css = $css_class_for_normal;
	
	$start_tag = "<" . $element . " class='" . $use_css . "'>";
	$middle_tag = $item;
	$end_tag = "</" . $element . ">";
	
	return $start_tag . $middle_tag . $end_tag;
}

function on_sale_decorate($is_on_sale, $given_price, $given_saleprice, $given_percentage, $given_banner, 
		  $given_currency, $given_elementName)
{

	$price = do_decoration($is_on_sale, 'norm_price_strike', 'norm_price', $given_price, $given_elementName);
	$sale_percentage = do_decoration($is_on_sale, 'sale_percent', 'sale_percent', $given_percentage . '%', $given_elementName);
	$sale_price = do_decoration($is_on_sale, 'sale_price', 'sale_price', $given_saleprice, $given_elementName);
	$sale_banner = do_decoration($is_on_sale, 'sale_txt', 'sale_txt', $given_banner, $given_elementName);

	$rv = '<table class="saletable" align="center">
		<tr class="saletabletr" >
			<td class="saletabletd" rowspan="2">'. $sale_price .'</td>
			<td class="saletabletd">' . $sale_percentage .'&nbsp;'. $sale_banner . '</td>
		</tr>
		<tr class="saletabletr" >
			<td  class="saletabletd">'. $price .'</td>
		</tr>
		</table>';
	
	return $rv;
}

function not_on_sale_decorate($is_on_sale, $given_price, $given_saleprice, $given_percentage, $given_banner, 
		  $given_currency, $given_elementName)
{

	$price = do_decoration($is_on_sale, 'sale_txt', 'norm_price', $given_price, $given_elementName);
	
	$rv = '<table align="center"><tr><td>' . $price . '</td></tr></table>';
	
	return $rv;
}


function decorate($is_on_sale, $given_price, $given_saleprice, $given_percentage, $given_banner, 
		  $given_currency, $given_elementName) {

	if ($is_on_sale) {
		return on_sale_decorate($is_on_sale, $given_price, $given_saleprice, $given_percentage, $given_banner, 
		  $given_currency, $given_elementName);
	} else {
		return not_on_sale_decorate($is_on_sale, $given_price, $given_saleprice, $given_percentage, $given_banner, 
		  $given_currency, $given_elementName);
	}
}

function get_woo_product_sale_banner_shortcode( $atts ) { 
	 
	$atts = get_woo_product_shortcode_atts( 'get_product_sale_banner', $atts );
	$product = wc_get_product( $atts['id'] ); 
  
	if ( ! $product ) { 
		return ''; 
	} 
	
	if ( strpos($_SERVER['HTTP_HOST'], ".com") > 0 ) {
		$SALE_BANNER="SALE";
	} else {
		$SALE_BANNER="ALE";
	}
	
	if ($product -> is_on_sale()) {
		return $SALE_BANNER;
	}
	
	return "";
}

function get_woo_product_sale_price_shortcode( $atts ) { 
	 
	$atts = get_woo_product_shortcode_atts( 'get_product_sale_price', $atts );
	$currency = get_currency_from_atts($atts);
	
	$product = wc_get_product( $atts['id'] ); 
  
	if ( ! $product ) { 
		return ''; 
	} 
	
	if ($product -> is_on_sale()) {
		$price 	= $product->get_sale_price();
		return $price . $currency;
	}
	
	return "";
}


function get_woo_product_regular_price_shortcode( $atts ) { 
	 
	$atts = get_woo_product_shortcode_atts( 'get_product_regular_price', $atts );
	$currency = get_currency_from_atts($atts);
	
	$product = wc_get_product( $atts['id'] ); 
  
	if ( ! $product ) { 
		return ''; 
	} 
	
	$price 	= $product->get_regular_price();
	return $price . $currency;
}


function get_woo_product_sale_percent_shortcode( $atts ) { 

	$atts = get_woo_product_shortcode_atts( 'get_product_sale_percent', $atts );
	$currency = get_currency_from_atts($atts);
	
	$product = wc_get_product( $atts['id'] ); 
  
	if ( ! $product ) { 
		return ''; 
	} 

	$price 		= $product->get_regular_price();
	$sale_price 	= $price;
	if ($product -> is_on_sale()) {
		$sale_price 	= $product->get_sale_price();
	}
	
	$percent = (int)(( 100 * ( 1 - ($sale_price / $price) )));
	
	return $percent;
}


function get_woo_product_price_info_shortcode( $atts ) { 

	$atts = get_woo_product_shortcode_atts( 'get_product_price_info', $atts );
	/* Get all info .....*/
	$regular_price 		= get_woo_product_regular_price_shortcode( $atts );
	$sale_price 		= get_woo_product_sale_price_shortcode( $atts );
	$sale_percentage 	= get_woo_product_sale_percent_shortcode( $atts );
	$sale_banner 		= get_woo_product_sale_banner_shortcode( $atts );
	$currency 		= get_currency_from_atts($atts);


	$product = wc_get_product( $atts['id'] ); 

	$on_sale = $product -> is_on_sale();
	
		  
	$html = decorate($on_sale, $regular_price, $sale_price, $sale_percentage, $sale_banner,
					$currency, 'span');

	return $html;
}


/* Add shortcodes -----------------------------------------------------------------------------*/
add_shortcode( 'get_product_regular_price', 	 'get_woo_product_regular_price_shortcode' ); 
add_shortcode( 'get_product_sale_price', 	 'get_woo_product_sale_price_shortcode'    ); 
add_shortcode( 'get_product_sale_percent',	 'get_woo_product_sale_percent_shortcode'  ); 
add_shortcode( 'get_product_sale_banner',	 'get_woo_product_sale_banner_shortcode'  ); 
add_shortcode( 'get_product_price_info',	 'get_woo_product_price_info_shortcode'  ); 


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
