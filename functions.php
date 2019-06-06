<?php
// Remove all default WP template redirects/lookups
remove_action( 'template_redirect', 'redirect_canonical' );

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown
function remove_redirects() {
	add_rewrite_rule( '^/(.+)/?', 'index.php', 'top' );
}
add_action( 'init', 'remove_redirects' );

// Load scripts
function load_vue_scripts() {
	wp_enqueue_script(
		'vuejs-wordpress-theme-starter-js',
		get_stylesheet_directory_uri() . '/dist/scripts/index.min.bundle.js',
		array( 'jquery' ),
		filemtime( get_stylesheet_directory() . '/dist/scripts/index.min.bundle.js' ),
		true
	);

	wp_enqueue_style(
		'vuejs-wordpress-theme-starter-css',
		get_stylesheet_directory_uri() . '/dist/styles.css',
		null,
		filemtime( get_stylesheet_directory() . '/dist/styles.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'load_vue_scripts', 100 );

include '_customs/post_type_portfolio.php';
include '_customs/tax_product_type.php';
include '_customs/tax_categories.php';
include '_customs/tax_tags.php';
include '_customs/tax_color.php';


add_filter( 'register_taxonomy_args', 'custom_taxonomies', 10, 2 );
function custom_taxonomies( $args, $taxonomy_name ) {
    if ( 'categories' === $taxonomy_name ) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'categories';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    if ( 'product_type' === $taxonomy_name ) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'product_type';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    if ( 'portfolio_colors' === $taxonomy_name ) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'portfolio_colors';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    if ( 'portfolio_tags' === $taxonomy_name ) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'portfolio_tags';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    return $args;
}