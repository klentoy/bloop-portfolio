<?php

# ACF CUSTOM FIELDS
# this will be used as reference or 
# custom field is not available
include '_customs/acf_exported_fields.php';

include '_customs/post_type_portfolio.php';
include '_customs/post_type_collection.php';
include '_customs/tax_product_type.php';
include '_customs/tax_categories.php';
include '_customs/tax_tags.php';
include '_customs/tax_color.php';
include '_customs/user_roles.php';

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

    $cats = get_the_terms( $post->ID, 'portfolio_categories' );
    $tags = get_the_terms( $post->ID, 'portfolio_tags' );
    $product_type = get_the_terms( $post->ID, 'product_type' );
    $_data['prod_type'] = $product_type;

    $colors = get_the_terms( $post->ID, 'portfolio_colors' );
    $_data['colors'] = $colors;

    $web_url = get_field( 'website_url', $post->ID );
    $_data['web_url'] = $web_url;

    $desktop_thumbnail = get_field( 'desktop_thumbnail', $post->ID );
    $_data['desktop_thumbnail'] = $desktop_thumbnail['sizes']['large'];

    $mobile_thumbnail = get_field( 'mobile_thumbnail', $post->ID );
    $_data['mobile_thumbnail'] = $mobile_thumbnail['sizes']['large'];

    //get screenshots from acf field
    $web_screenshots = get_field( 'website_screenshots', $post->ID );
    $screenshots = [];
    if (is_array($web_screenshots) || is_object($web_screenshots)) {
        foreach ($web_screenshots as $screenshot) {
            array_push($screenshots, $screenshot['sizes']['large']);
        }
    }
    $_data['screenshots'] = $screenshots;

    $proj_meta = [$product_type, $cats, $tags];
    $proj_tags = [];

    foreach ($proj_meta as $meta) {
        if (is_array($meta) || is_object($meta)) {
            foreach ($meta as $val) {
                array_push($proj_tags, $val->name);
            }
        }
    }
    $_data['proj_tags'] = $proj_tags ;


    //get featured image
    $project_thumbnail = get_the_post_thumbnail_url( $post->ID,'full');
    $_data['project_thumbnail'] = $project_thumbnail;

    $data->data = $_data;

    return $data;
}
add_filter('rest_prepare_portfolio', 'prepare_rest_proj', 10, 3);


// function prepare_rest_collection($data, $post, $request)
// {
//     $_data = $data->data;

//     //get featured image
//     $portfolio_collection = get_field( 'bloop_portfolios', $post->ID);
//     $portfolio_ids = [];

//     foreach ($portfolio_collection as $val) {
//         array_push( $portfolio_ids, $val['bloop_collection_portfolio']);
//     }
//     $_data['portfolios'] = $portfolio_ids;

//     $data->data = $_data;

//     return $data;
// }
// add_filter('rest_prepare_collection', 'prepare_rest_collection', 10, 3);

//add featured image to collection post type
add_theme_support( 'post-thumbnails' );

function get_tokens($token)
{
    global $wpdb;
    if ( $token ){ 
        $results = $wpdb->get_results( "SELECT * FROM wp_blooptoken WHERE token_generated = '{$token}'" , OBJECT );
        return $results;
    }
    return false;
}

add_filter('acf/rest_api/key', function ($key, $request, $type) {
    return 'collection';
}, 10, 3);

add_action('rest_api_init', function(){
    
    register_rest_route('wp/v2', '/collection', array(
        'methods' => 'GET',
        'callback' => 'all_collections'
    ));

    register_rest_route('wp/v2', '/client_collection', array(
        'methods' => 'GET',
        'callback' => 'fetch_collection'
    ));
    
    register_rest_route('wp/v2', '/collection/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'fetch_collection'
    ));

    register_rest_route('wp/v2', '/collection', array(
        'methods' => 'POST',
        'callback' => 'post_collection'
    ));

    register_rest_route('wp/v2', '/add_to_collection', array(
        'methods' => 'POST',
        'callback' => 'add_to_collect'
    ));

    register_rest_route('wp/v2', '/portfolio_collections/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'portfolio_collections'
    ));

    register_rest_route('wp/v2', '/add_token', array(
        'methods' => 'POST',
        'callback' => 'add_token'
    ));

    register_rest_route('wp/v2', 'get_all_tokens', array(
        'methods' => 'GET',
        'callback' => 'get_all_token'
    ));

    register_rest_route('wp/v2', '/portfolio/(?P<id>\d+)', array(
        'menthods' => 'GET',
        'callback' => 'fetch_portfolio'
    ));

    register_rest_route('wp/v2', '/portfolio', array(
        'menthods' => 'GET',
        'callback' => 'fetch_portfolio'
    ));

    register_rest_route('wp/v2', '/users', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'get_user_list',
        'show_in_rest' => true
    ));

    register_rest_route('wp/v2', '/share_collection', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'share_collection'
    ));
    
});

function share_collection (WP_REST_Request $request) {
    if ( $authorized = get_current_user_id() ) {
        $body = json_decode($request->get_body());
        if ( get_userdata($body->user_id) === false )
            return false;
        $user_email = get_userdata($body->user_id)->user_email;
        $user_fullname = get_user_meta($body->user_id,'first_name', true) . ' ' . get_user_meta($body->user_id, 'last_name', true);
        $collection_name = get_the_title($body->collection_id);

        $sharer_name = get_user_meta($authorized,'first_name', true) . ' ' . get_user_meta($authorized, 'last_name', true);
        
        $e_t = "Hi $user_fullname, $sharer_name shared this collection: ";
        $e_t .= "<a href='" . get_bloginfo('url'). "/collection/". $body->collection_id ."' target='_blank'>$collection_name</a> <br/>";
        $e_t .= "Full Link <a href='" . get_bloginfo('url'). "/collection/". $body->collection_id ."' target='_blank'>" . get_bloginfo('url'). "/collection/". $body->collection_id ."</a>";
        
        $headers = 'From: '. get_bloginfo('admin_email') . "\r\n" .
            'Reply-To: ' . get_bloginfo('admin_email') . "\r\n".
            'Content-Type: text/html; charset=UTF-8' . "\r\n";
        
        wp_mail( $user_email, "Collection Shared by $sharer_name", $e_t, $headers );
        
        return array('status'=>'email sent!');
    }
    return false;
}

function get_user_list($request) {
    $role = $request['role'] ? array('role'=>'developer') : '';
    $results = get_users($role);
    $users = array();
    $controller = new WP_REST_Users_Controller();
    foreach ( $results as $key => $user ) {
         $data    = $controller->prepare_item_for_response( $user, $request );
         $users[] = $controller->prepare_response_for_collection( $data );
         $users[$key]['first_name'] = get_user_meta($user->ID, 'first_name', true);
         $users[$key]['last_name'] = get_user_meta($user->ID, 'last_name', true);
         $users[$key]['email'] = get_userdata($user->ID)->user_email;
     }
 
    return rest_ensure_response( $users );
 }

function add_to_collect(WP_REST_Request $request_data){
    $request_body = json_decode($request_data->get_body());
    $action = $request_body->action ? $request_body->action : null;
    $collection_id = $request_body->collection_id ? $request_body->collection_id : null;
    $category_name = $request_body->collection_name ? $request_body->collection_name : null;
    $portfolio_id = $request_body->portfolio_id ? $request_body->portfolio_id : null;
    $shared_id = $request_body->shared_id ? $request_body->shared_id : null;

    if ( 'publish' == get_post_status ( $portfolio_id ) || 'private' == get_post_status ( $portfolio_id ) ) {
        $field_key = "bloop_portfolios";
        $value = get_field($field_key, $collection_id);
        if ( ! $value ){
            $value = array();
        }
        if ( $action == "remove" ){
            if ( !(array_search($portfolio_id, array_column($value, "bloop_collection_portfolio")) + 1)){
                return false;
            }

            $portfolio_row_index = array_search($portfolio_id, array_column($value, "bloop_collection_portfolio")) + 1;
            if( delete_row("bloop_portfolios", $portfolio_row_index, $collection_id) ){
                return array('status'=>'success', 'message'=>'Successfully removed ' . $portfolio_id );
            }else{
                return array('status'=>'error', 'message'=>'error removing ' . $portfolio_id );
            }
        }
        if ( $action == "add" ){
            $value[] = array(
                "bloop_collection_portfolio" => $portfolio_id,
                "bloop_date_created" => current_time('mysql'),
                "bloop_shared_to" => $shared_id
            );
            $tempArr = array_unique(array_column($value, 'bloop_collection_portfolio'));
            $new_value = array_intersect_key($value, $tempArr);
            return update_field( $field_key, $new_value, $collection_id );
        }            


    }
    return $portfolio_id;
}

function fetch_collection(WP_REST_Request $request){
    $authorized = get_current_user_id();
    if ( $request['token'] || $authorized ){
        $token_ids = array();
        $collection_items = array();
        $collection = array();

        if ( $request['id'] ){
            $tokens = (array) $request['id'];
            foreach( $tokens as $token ){
                $fields = get_field('bloop_portfolios', $token);
                array_push($collection_items, $fields);
                foreach( $collection_items[0] as $portfolio ){
                    array_push($collection, $portfolio['bloop_collection_portfolio']);
                }
                array_push($token_ids, $token);
            }
        }else if ( get_tokens($request['token']) ){
            $tokens = get_tokens($request['token']);
            foreach( $tokens as $token ){
                array_push($collection_items, get_field('bloop_portfolios', $token->collection_id));
                foreach( $collection_items[0] as $portfolio ){
                    array_push($collection, $portfolio['bloop_collection_portfolio']);
                }                
                array_push($token_ids, $token->collection_id);
            }
        }

        $args = array(
            'post_type'   => 'collection',
            'post__in' => $token_ids
        );
        $collection_info = get_posts($args);
        
        if ( $fields ){
            $args = array(
                'post_type'   => 'portfolio',
                'post__in' => $collection,
            );
            $portfolios = get_posts($args);
            //adds acf data to portfolios object
            foreach( $portfolios as $portfolio ){
                $slug = get_post_field( 'post_name', $portfolio->ID );
                $web_url = get_field( 'website_url', $portfolio->ID );
                $desktop_thumbnail = get_field( 'desktop_thumbnail', $portfolio->ID );
                $mobile_thumbnail = get_field( 'mobile_thumbnail', $portfolio->ID );
                $project_thumbnail = get_the_post_thumbnail_url( $portfolio->ID,'full');

                $web_screenshots = get_field( 'website_screenshots', $portfolio->ID );
                $screenshots = [];
                if (is_array($web_screenshots) || is_object($web_screenshots)) {
                    foreach ($web_screenshots as $screenshot) {
                        array_push($screenshots, $screenshot['sizes']['large']);
                    }
                }

                $product_type = get_the_terms( $portfolio->ID, 'product_type' );
                $colors = get_the_terms( $portfolio->ID, 'portfolio_colors' );
                $cats = get_the_terms( $portfolio->ID, 'portfolio_categories' );
                $tags = get_the_terms( $portfolio->ID, 'portfolio_tags' );
 
                $proj_meta = [$product_type, $cats, $tags];
                $proj_tags = [];
                foreach ($proj_meta as $meta) {
                    if (is_array($meta) || is_object($meta)) {
                        foreach ($meta as $val) {
                            array_push($proj_tags, $val->name);
                        }
                    }
                }

                $portfolio->slug = $slug;  
                $portfolio->web_url = $web_url;
                $portfolio->desktop_thumbnail = $desktop_thumbnail['sizes']['large'];
                $portfolio->mobile_thumbnail = $mobile_thumbnail['sizes']['large'];
                $portfolio->screenshots = $screenshots;       
                $portfolio->prod_type = $product_type;
                $portfolio->colors = $colors;
                $portfolio->proj_tags = $proj_tags;  
                $portfolio->project_thumbnail = $project_thumbnail;
            }            
            //array_push($portfolios, array("portfolios"=>$portfolios));
            return array('collection'=>$collection_info[0], 'portfolios'=>$portfolios);
        } else {
            return array('collection'=>$collection_info[0], 'portfolios'=> []);
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

function post_collection(WP_REST_Request $request_data){
    $request_body = json_decode($request_data->get_body());
    $portfolios = $request_body->bloop_portfolios[0];
    
    $post_type = "collection";
    $post_array = array(
        "post_title" => $request_body->title,
        "post_type" => $post_type,
        "post_content" => $request_body->content,
        "post_status" => $request_body->status ? $request_body->status : "draft",
    );

    $post_id = wp_insert_post($post_array);
    $field_key = "field_5d142e312c5e0";
    $value = array(
        array(
            "bloop_collection_portfolio" => $portfolios->bloop_collection_portfolio,
            "bloop_date_created" => $portfolios->bloop_collection_portfolio,
            "bloop_shared_to" => $portfolios->shared_id
        )
    );
    update_field( $field_key, $value, $post_id );
    return $post_id;
}

function portfolio_collections(WP_REST_Request $request){
    global $wpdb;
    $author_id = $request['author'] ? $request['author'] : get_current_user_id();

    if ( $request['id'] && $author_id  ){
        $collections = $wpdb->get_results( 
            "SELECT post_id 
            FROM wp_postmeta AS pm 
            JOIN wp_posts AS p  
            ON pm.post_id = p.ID
            WHERE meta_key LIKE 'bloop_portfolios_%_bloop_collection_portfolio' 
            AND meta_value = ". $request['id'] ." 
            AND post_type='collection'
            AND (post_status = 'publish' OR post_status = 'private')
            AND post_author = $author_id");
            
        return array('status'=>'success', 'collection_ids'=>$collections);
    }

    return array('status'=>'error', 'message'=> 'Portfolio and Author IDs required!');
}

function add_token(WP_REST_Request $request_data){
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

function get_all_token($token){
    global $wpdb;
    if ( $token ){ 
        return $wpdb->get_results( "SELECT * FROM wp_blooptoken" , OBJECT );
    }
    return false;
}


function fetch_portfolio(WP_REST_Request $request){
    if ( $id = $request['id'] ){
        $args = array(
            'post_type'=>'portfolio',
            'post__in' => array($id),
            'post_status' => 'private' );
        if ( $token = get_tokens ( $request["token"] ) ){
            $portf = (array) get_post($args);
            return array_merge($portf, is_array(get_fields($id)) ? get_fields($id) : array());
        }else if ( get_current_user_id() ){
            $portf = (array) get_post($args);
            return array_merge($portf, is_array(get_fields($id)) ? get_fields($id) : array());
        }else{
            return array('status'=>'error', 'message' => 'No Token or unauthorized!');
        }
    }else{
        return get_posts(array(
            'post_type'   => 'portfolio',
            'post_status' => 'private'
        ));
    }
}

function all_collections( WP_REST_Request $request ){
    global $wpdb;
    $author_id = $request['author'] ? $request['author'] : get_current_user_id();
    $collections_str = "
        SELECT $wpdb->posts.* 
        FROM $wpdb->posts
        WHERE ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')
        AND $wpdb->posts.post_type = 'collection'
        AND $wpdb->posts.post_date < NOW()
        AND $wpdb->posts.post_author = $author_id
        ORDER BY $wpdb->posts.post_date DESC";
    if( $collections = $wpdb->get_results($collections_str, OBJECT) ){
        return array('status'=>'success', 'collections'=>$collections);
    }else{
        return array('status'=>'error', 'message'=>"No Collections!");
    }
}


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

function remove_menu_items() {
    if( !current_user_can( 'administrator' ) ):
        remove_menu_page( 'edit.php?post_type=collection' );
    endif;
}
add_action( 'admin_menu', 'remove_menu_items' );