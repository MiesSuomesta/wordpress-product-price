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
	wp_enqueue_style( 'style1',  $css_url);
	wp_enqueue_style( 'style2',  'https://fonts.googleapis.com/css2?family=MuseoModerno:wght@600&display=swap');
}

add_action( 'wp_enqueue_scripts', 'my_css_loader' );

function get_woo_product_shortcode_atts( $shortcode_name, $atts ) { 
	 
	$atts = shortcode_atts( array( 
		'id' => null,
		'currency' => null,
		'currency_space' => null,
		'html_elem' => null,
		'hinfo' => null,
		'hadd' => null,
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

function do_decoration_single($css_class, $item, $element)
{
	
	if ( $item == false ) 
	{
		return "<" . $element . " class='" . $css_class . "'>";
	}
	
	$start_tag = "<" . $element . " class='" . $css_class . "'>";
	$middle_tag = $item;
	$end_tag = "</" . $element . ">";
	return $start_tag . $middle_tag . $end_tag;
}


function do_decoration($on_sale, $css_class_for_sale, $css_class_for_normal, $item, $element)
{
	if ($on_sale)
		$use_css = $css_class_for_sale;
	else
		$use_css = $css_class_for_normal;
	
	return do_decoration_single($use_css, $item, $element);
}

function mk_header_html($given_info, $element)
{
	if ( empty( $given_info[ 'hadd' ] ) ) { 
		/* was empty */
		$product_title_add = "";
	} else {
		/* was not empty */
		$product_title_add = ": " .$given_info['hadd'];
	}

	$product_title_add_html 	= do_decoration_single('product_title_add', $product_title_add, $element);

	if ( empty( $given_info[ 'hinfo' ] ) ) { 
		/* was empty */
		$product_title 	= $given_info['product_name'];
	} else {
		/* was not empty */
		$product_title 	= $given_info['hinfo'];
	}

	$product_title_html 	= do_decoration_single('product_title', $product_title, $element);

	return $product_title_html . $product_title_add_html;
}

function mk_footer_html($given_info, $element)
{
	$sale 		= $given_info['sale'];
	$product	= $given_info['product'];
	$product_name 	= $given_info['product_name'];

	$rv = "";
	if ($sale) {
		$currency 	= $given_info['currency'];
		$sale_price 	= $given_info['sale_price'];
		$sale_percent 	= $given_info['sale_percent'];
		$sale_banner 	= $given_info['sale_banner'];
		$sale_till 	= $given_info['sale_till'];
		$sale_to_date	= $given_info['sale_to_date'];
		$sale_valid 	= $given_info['sale_valid'];
		
		$sale_percent_html 	= do_decoration_single('sale_percent',		$sale_percent . '%', 		$given_elementName);
		$sale_price_html 	= do_decoration_single('sale_price', 		$sale_price . $currency, 	$given_elementName);
		$sale_banner_html	= do_decoration_single('sale_banner', 		$sale_banner, 			$given_elementName);
		$sale_till_html		= do_decoration_single('sale_till', 		$sale_till, 			$given_elementName);
		$sale_to_date_html	= do_decoration_single('sale_to_date', 		$sale_to_date, 			$given_elementName);
		$sale_valid_html	= do_decoration_single('sale_valid', 		$sale_valid, 			$given_elementName);

		if ($given_info['international'])
		{
			$rv = $sale_valid_html .$bsp. $sale_till_html .$bsp. $sale_to_date_html;
		} else {
			$rv = $sale_valid_html .$bsp. $sale_to_date_html .$bsp. $sale_till_html;
		}
	}

	return $rv;
}

function on_sale_decorate($given_info, $given_elementName)
{
	$product	= $given_info['product'];
	$sale 		= $given_info['sale'];
	$regular_price 	= $given_info['regular_price'];
	$currency 	= $given_info['currency'];
	$sale_price 	= $given_info['sale_price'];
	$sale_percent 	= $given_info['sale_percent'];
	$sale_banner 	= $given_info['sale_banner'];
	$sale_till 	= $given_info['sale_till'];

	$price_html 		= do_decoration_single('norm_price_strike',	$regular_price . $currency, 	$given_elementName);
	$sale_percent_html 	= do_decoration_single('sale_percent',		$sale_percent . '%', 		$given_elementName);
	$sale_price_html 	= do_decoration_single('sale_price', 		$sale_price . $currency, 	$given_elementName);
	$sale_banner_html	= do_decoration_single('sale_banner', 		$sale_banner, 			$given_elementName);
	$sale_till_html		= do_decoration_single('sale_till', 		$sale_till, 			$given_elementName);

	$header_html = mk_header_html($given_info, $given_elementName);
	$footer_html = mk_footer_html($given_info, $given_elementName);

	$rv = '<table class="saletable">
		<tr class="saletabletrheader" ><td class="header_td" colspan="3">'. $header_html .'</td></tr>
		<tr class="saletabletr" >
			<td class="sale_price_td" rowspan="2">'. $sale_price_html .'</td>
			<td class="sale_percent_td">' . $sale_percent_html .'&nbsp;'. $sale_banner_html . '</td>
		</tr>
		<tr class="saletabletr" ><td class="price_td">'. $price_html .'</td></tr>
		<tr class="saletabletrfooter" ><td class="footer_td" colspan="3">'. $footer_html .'</td></tr>
		</table>';
	
	return $rv;
}

function not_on_sale_decorate($given_info, $given_elementName)
{
	$product	= $given_info['product'];
	$sale 		= $given_info['sale'];
	$regular_price 	= $given_info['regular_price'];
	$currency 	= $given_info['currency'];
	$sale_price 	= $given_info['sale_price'];
	$sale_percent 	= $given_info['sale_percent'];
	$sale_banner 	= $given_info['sale_banner'];
	$sale_till 	= $given_info['sale_till'];

	$price_html 		= do_decoration_single('norm_price_strike',	$regular_price . $currency, 	$given_elementName);
	$sale_percent_html 	= do_decoration_single('sale_percent',		$sale_percent . '%', 		$given_elementName);
	$sale_price_html 	= do_decoration_single('sale_price', 		$sale_price . $currency, 	$given_elementName);
	$sale_banner_html	= do_decoration_single('sale_banner', 		$sale_banner, 			$given_elementName);
	$sale_till_html		= do_decoration_single('sale_till', 		$sale_till, 			$given_elementName);

	$header_html = mk_header_html($given_info, $given_elementName);
	$footer_html = mk_footer_html($given_info, $given_elementName);

	$rv = '<table class="saletable">
		<tr class="saletabletrheader" ><td class="header_td" colspan="3">'. $header_html .'</td></tr>
		<tr class="saletabletr" >
			<td class="sale_price_td" rowspan="2">'. $sale_price_html .'</td>
			<td class="sale_percent_td">' . $sale_percent_html .'&nbsp;'. $sale_banner_html . '</td>
		</tr>
		<tr class="saletabletr" ><td class="price_td">Norm. '. $price_html .'</td></tr>
		<tr class="saletabletrfooter" ><td class="footer_td" colspan="3">'. $footer_html .'</td></tr>
		</table>';
	
	return $rv;
}


function decorate($given_info, $given_elementName)
{
	if ($given_info['sale']) {
		return on_sale_decorate($given_info, $given_elementName);
	} else {
		return not_on_sale_decorate($given_info, $given_elementName);
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
	
	if ($product->is_on_sale()) {
		return $SALE_BANNER;
	}
	
	return "";
}

function get_woo_product_sale_date_info( $atts ) { 
	 
	$atts = get_woo_product_shortcode_atts( 'get_product_sale_date_end', $atts );
	$product = wc_get_product( $atts['id'] ); 

	if ( ! $product ) { 
		return ''; 
	} 
	
	if ( strpos($_SERVER['HTTP_HOST'], ".com") > 0 ) {
		$SALE_BANNER="until";
	} else {
		$SALE_BANNER="asti";
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
	$currency = get_currency_from_atts($atts);

	$product = wc_get_product( $atts['id'] ); 

	/* Get sale price & percentage */
	$regular_price	= $product->get_regular_price();
	$on_sale 	= $product->is_on_sale();
	$sale_price 	= $regular_price;
	
	$info = [];
	$info['hadd']		= $atts['hadd'];
	$info['hinfo']		= $atts['hinfo'];
	$info['sale'] 		= $on_sale;
	$info['regular_price'] 	= $regular_price;
	$info['currency'] 	= $currency;
	$info['sale_price'] 	= "";
	$info['sale_percent'] 	= "";
	$info['sale_banner'] 	= "";
	$info['sale_till'] 	= "";
	$info['product_name'] 	= $product->get_name();
	$info['product'] 	= $product;
	$info['international'] 	= false;

	if ($on_sale) {
		$sale_banner	 = "";
		$sale_valid	 = "";
		$sale_till	 = "";
		$sale_price 	 = $product->get_sale_price();
		$sale_percent 	 = (int)(( 100 * ( 1 - ($sale_price / $regular_price) )));
		$sale_to_obj 	 = $product->get_date_on_sale_to();		
		
		

		$info['sale_to_date'] = false;
		if ( strpos($_SERVER['HTTP_HOST'], ".com") > 0 ) {
			$sale_banner="SALE";
		} else {
			$sale_banner="ALE";
		}
		
		if ( $sale_to_obj != null ) {

			if ( strpos($_SERVER['HTTP_HOST'], ".com") > 0 ) {
				$sale_valid="offer valid ";
				$sale_till="until";
				$info['sale_to_date_format'] = "m.d.";
				$info['international'] = true;
			} else {
				$sale_valid="tarjous voimassa";
				$sale_till="asti";
				$info['sale_to_date_format'] = "d.m.";
				$info['international'] = false;
			}
			
			$info['sale_to_date'] = $sale_to_obj->date($info['sale_to_date_format']);
		}
		
		$info['sale_valid'] 	= $sale_valid;
		$info['sale_price'] 	= $sale_price;
		$info['sale_percent'] 	= $sale_percent;
		$info['sale_banner'] 	= $sale_banner;
		$info['sale_till'] 	= $sale_till;

	}

/*	print("<pre>");
	print_r($info);
	print("</pre>");
*/		  
	$html = decorate($info, 'span');

	return $html;
}


/* Add shortcodes -----------------------------------------------------------------------------*/
add_shortcode( 'get_product_regular_price', 	 'get_woo_product_regular_price_shortcode' ); 
add_shortcode( 'get_product_sale_price', 	 'get_woo_product_sale_price_shortcode'    ); 
add_shortcode( 'get_product_sale_percent',	 'get_woo_product_sale_percent_shortcode'  ); 
add_shortcode( 'get_product_sale_banner',	 'get_woo_product_sale_banner_shortcode'  ); 
add_shortcode( 'get_product_price_info',	 'get_woo_product_price_info_shortcode'  ); 

/*
vuoden
[get_product_price_info id="7323"]
[get_product_price_info id="7324"]
[get_product_price_info id="7327"]
[get_product_price_info id="7328"]

[get_product_price_info id="7329"]
[get_product_price_info id="7330"]
[get_product_price_info id="7331"]

5vuoden
[get_product_price_info id="7333"]
[get_product_price_info id="7334"]
[get_product_price_info id="7335"]
[get_product_price_info id="7336"]
[get_product_price_info id="7337"]
[get_product_price_info id="7338"]
[get_product_price_info id="7339"]
*/



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
