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
include '_customs/acf_exported_fields.php';


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

function add_to_collect(WP_REST_Request $request){
    $collection_id = $request['collection_id'] ? $request['collection_id'] : null;
    $category_name = $request['collection_name'] ? $request['collection_name'] : null;
    $portfolio_id = $request['portfolio_id'] ? $request['portfolio_id'] : null;
    $shared_id = $request['shared_id'] ? $request['shared_id'] : null;
    
    if ( 'publish' == get_post_status ( $portfolio_id ) || 'private' == get_post_status ( $portfolio_id ) ) {
        
        $field_key = "bloop_portfolios";

        $value = get_field($field_key, $collection_id);
        $value[] = array(
                        "bloop_collection_portfolio" => $portfolio_id,
                        "bloop_date_created" => current_time('mysql'),
                        "shared_id" => $shared_id
                    );
        $tempArr = array_unique(array_column($value, 'bloop_collection_portfolio'));
        $new_value = array_intersect_key($value, $tempArr);
        return update_field( $field_key, $new_value, $collection_id );

    }
    return false;
}

function fetch_collection(WP_REST_Request $request){
    $authorized = get_current_user_id();
    
    if ( $request['token'] || $authorized ){
        $token_ids = array();
        $portfolios = array();

        if ( $request['id'] ){
            $tokens = (array) $request['id'];
            foreach( $tokens as $token ){
                array_push($portfolios, get_field('bloop_portfolios', $token));
                array_push($token_ids, $token);
            }
        }else if ( get_tokens($request['token']) ){
            $tokens = get_tokens($request['token']);
            foreach( $tokens as $token ){
                array_push($portfolios, get_field('bloop_portfolios', $token->collection_id));
                array_push($token_ids, $token->collection_id);
            }
        }
        
        if ( $token_ids ){
            $args = array(
                'post_type'   => 'collection',
                'post_status' => 'private',
                'post__in' => $token_ids,
            );
            $collections = get_posts($args);
            array_push($collections, array("porfolios"=>$portfolios));
            return array('collection'=>$collections);
        }
    }else{
        if ( ! $authorized ){
            return array('status'=>'error','message'=>'User not authorized!');
        }
        
        if (! $request['token']){
            return array('status'=>'error','message'=>'Token is required!');
        }
    }
    return array('status'=>'error','message'=>'Wrong token!');
}

add_action('rest_api_init', function(){
    /**
     * 1 token is equal to a 1 collection
     * API: collections/v2
     * Method: GET
     **/
    register_rest_route('wp/v2', '/client_collection', array(
        'methods' => 'GET',
        'callback' => 'fetch_collection'
    ));
    
    register_rest_route('wp/v2', '/collection/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'fetch_collection'
    ));

    /**
     * API: wp/v2/add_to_collection{$id} 
     * Method: POST
     * $id = collection id
     **/
    register_rest_route('wp/v2', '/add_to_collection', array(
        'methods' => 'POST',
        'callback' => 'add_to_collect'
    ));


    /** 
    * TODO: KULANG PA NI
    */
    register_rest_route('wp/v2', '/add_token', array(
        'methods' => 'POST',
        'callback' => function( WP_REST_Request $request_data ){
            global $wpdb;
            $body = json_decode($request_data->get_body());
            $collection_id = $body->collection_id; // Importante
            $author = $body->author; // Importante
            $remarks = $body->collection_name ? $body->collection_name : "N/A";
            $token_generated = bloop_wof_tokenizer();
            
            if (!$collection_id || !$author)
                return array('status'=>'error', 'message' => 'No collection ID or author!');
            
            $tokenized = $wpdb->insert( $wpdb->prefix . "blooptoken", array(
                "collection_id" => $collection_id,
                "author" => $author,
                "remarks" => $remarks,
                "token_generated" => $token_generated,
            ));

            if ( $tokenized )
                return array('status'=>'success', 'token_id'=>$tokenized, 'collection_id'=>$collection_id);
        }
    ));
});

function bloop_wof_tokenizer(){
    $token = openssl_random_pseudo_bytes(5);
    $token = bin2hex($token);
    $token = crypt($token, '$6$'. crypt($token) .'$'. time() .'$');
    return $token;
}

function wpse_11826_search_by_title( $search, $wp_query ) {
    if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
        global $wpdb;

        $q = $wp_query->query_vars;
        $n = ! empty( $q['exact'] ) ? '' : '%';

        $search = array();

        foreach ( ( array ) $q['search_terms'] as $term )
            $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );

        if ( ! is_user_logged_in() )
            $search[] = "$wpdb->posts.post_password = ''";

        $search = ' AND ' . implode( ' AND ', $search );
    }

    return $search;
}

add_filter( 'posts_search', 'wpse_11826_search_by_title', 10, 2 );

function jwt_auth_function($data, $user) {
    $user_data = $user->data;
    $data['user_id'] = $user_data->ID;
    $data['user_role'] = $user->roles[0];
    return $data;
}
add_filter( 'jwt_auth_token_before_dispatch', 'jwt_auth_function', 10, 2 );

function remove_element_by_value($arr, $val) {
    $return = array(); 
    foreach($arr as $k => $v) {
       if(is_array($v)) {
          $return[$k] = remove_element_by_value($v, $val);
          continue;
       }
       if($v == $val) continue;
       $return[$k] = $v;
    }
    return $return;
 }