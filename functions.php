<?php
// Remove all default WP template redirects/lookups
remove_action('template_redirect', 'redirect_canonical');

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown
function remove_redirects()
{
    add_rewrite_rule('^/(.+)/?', 'index.php', 'top');
}
add_action('init', 'remove_redirects');

// Load scripts
function load_vue_scripts()
{
    wp_enqueue_script(
        'vuejs-wordpress-theme-starter-js',
        get_stylesheet_directory_uri() . '/dist/scripts/index.min.bundle.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/dist/scripts/index.min.bundle.js'),
        true
    );

    wp_enqueue_style(
        'vuejs-wordpress-theme-starter-css',
        get_stylesheet_directory_uri() . '/dist/styles.css',
        null,
        filemtime(get_stylesheet_directory() . '/dist/styles.css')
    );
}
add_action('wp_enqueue_scripts', 'load_vue_scripts', 100);

include '_customs/post_type_portfolio.php';
include '_customs/post_type_collection.php';
include '_customs/tax_product_type.php';
include '_customs/tax_categories.php';
include '_customs/tax_tags.php';
include '_customs/tax_color.php';

# ACF CUSTOM FIELDS
# this will be used as reference or 
# custom field is not available
# include '_customs/acf_exported_fields.php';


add_filter('register_taxonomy_args', 'custom_taxonomies', 10, 2);
function custom_taxonomies($args, $taxonomy_name)
{
    if ('categories' === $taxonomy_name) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'categories';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    if ('product_type' === $taxonomy_name) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'product_type';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    if ('portfolio_categories' === $taxonomy_name) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'portfolio_categories';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    if ('portfolio_colors' === $taxonomy_name) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'portfolio_colors';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    if ('portfolio_tags' === $taxonomy_name) {
        $args['show_in_rest'] = true;
        $args['rest_base']             = 'portfolio_tags';
        $args['rest_controller_class'] = 'WP_REST_Terms_Controller';
    }
    return $args;
}


function prepare_rest($data, $post, $request)
{
    $_data = $data->data;

    $cats = get_the_category($post->ID);
    $_data['cats'] = $cats;

    $data->data = $_data;

    return $data;
}
add_filter('rest_prepare_post', 'prepare_rest', 10, 3);

function prepare_rest_proj($data, $post, $request)
{
    $_data = $data->data;

    $cats = get_the_category($post->ID);
    $_data['cats'] = $cats;

    $data->data = $_data;

    return $data;
}
add_filter('rest_prepare_portfolio', 'prepare_rest_proj', 10, 3);

$product_type = get_the_terms( $post->ID, 'product_type' );
$_data['prod_type'] = $product_type;

/*
#
# CREATE TOKEN TABLE 
    CREATE TABLE wp_blooptoken (
    bt_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    collection_id bigint(20) UNSIGNED NOT NULL,
    token_generated longtext,
    remarks longtext,
    author bigint(20) UNSIGNED NOT NULL,
    created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (bt_id)
)
*/

function get_tokens($token)
{
    global $wpdb;
    if ( $token ){ 
        $results = $wpdb->get_results( "SELECT * FROM wp_blooptoken WHERE token_generated = '{$token}'" , OBJECT );
        return $results;
    }
    return false;
}


add_action('rest_api_init', function(){
    /**
     * 1 token is equal to a 1 collection
     * API: collections/v2
     * Method: GET
     **/
    register_rest_route('collections', '/v2', array(
        'methods' => 'GET',
        'callback' => function(WP_REST_Request $request){
            if (! $request['token'] )
                return array('error_message'=>'Token is required!');
        
            $tokens = get_tokens($request['token']);
            $token_ids = array();
            foreach( $tokens as $token ){
               array_push($token_ids, $token->collection_id);
            }
            if ( $token_ids ){
                $args = array(
                    'post_type'   => 'collection',
                    'post_status' => 'publish',
                    'post__in' => $token_ids,
                );
                $collections = get_posts($args);;
                return $collections;
            }
            return array('error_message'=>'Wrong token!');
        }
    ));

    /**
     * API: add_to_location/v2/{$id} 
     * Method: POST
     * $id = portfolio_id
     **/
    register_rest_route('add_to_collecion', '/v2/(?P<id>[\d]+)', array(
        'methods' => 'POST',
        'callback' => function(WP_REST_Request $request){
            $portolio_id = $request['id'] ? $request['id'] : null;
            $category_name = $request['collection_name'] ? $request['collection_name'] : null;
            $collection_id = $request['collection_id'] ? $request['collection_id'] : null;
            if ( 'publish' == get_post_status ( $portolio_id ) ) {
                update_field('bloop_portfolios', $portolio_id, $collection_id);
                return wp_get_single_post($portolio_id);
            }
            return $request->get_params();
        }
    ));


    /** 
    * TODO: KULANG PA NI
    */
    register_rest_route('add_token', '/v2', array(
        'methods' => 'POST',
        'callback' => function( WP_REST_Request $request_data ){
            $token = openssl_random_pseudo_bytes(16);
            $token = bin2hex($token);
            
            return $token;
            return $request_data->get_params();
        
        }
    ));
});