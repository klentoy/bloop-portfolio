<?php
add_action( 'admin_enqueue_scripts', function(){
    wp_enqueue_script( 'wp-vuejs', get_template_directory_uri() . '/_customs/js/vue.js');
    wp_enqueue_script( 'wp-vuejs-axios', get_template_directory_uri() . '/_customs/js/axios.min.js');
    wp_enqueue_script( 'wp-vuetify', get_template_directory_uri() . '/_customs/js/vuetify.min.js');
});

add_action( 'admin_menu', 'bloop_admin_menu' );
function bloop_admin_menu() {
    add_menu_page( 'Bloop Token', 'Bloop Token', 'manage_options', 'admin-bloop-page.php', 'bloop_token_page', 'dashicons-post-status', 6  );
    add_submenu_page( 'admin-bloop-page.php', 'Setting', 'Setting', 'manage_options', 'admin-bloop-setting.php', 'bloop_token_setting');
}
add_action( 'admin_init', function() {
	global $wp_roles;
    $roles = $wp_roles->roles;
    foreach ($roles as $role) {
        $r = strtolower($role['name']);
        $r = str_replace(' ', '_', $r);
        register_setting( 'bloop-settings-group', "bloop_option_" . $r );
    }
});

function selected_role_check ($main_role, $therole){
    $roles_roles = get_option('bloop_option_' . strtolower($main_role));
    if ( $roles_roles && $therole ){
        foreach($roles_roles[strtolower($main_role)] as $role){
            if ( $therole == $role ){
                echo "selected";
            }
        }
    }
}

function bloop_token_setting(){
	global $wp_roles;
    $roles = $wp_roles->roles;    
    include 'admin-bloop-settings.php';
}

function bloop_token_page (){
    $tokens = get_bloop_tokens();
    include 'admin-bloop-page.php';
}

function get_bloop_tokens($type = ""){
    global $wpdb;
    $prefix = $wpdb->prefix;
    if ( $type ){
        $sql = "SELECT 
                    bt.*, 
                    u.ID,
                    MAX(CASE WHEN t2.meta_key = 'first_name' THEN meta_value END) AS first_name,
                    MAX(CASE WHEN t2.meta_key = 'last_name' THEN meta_value END) AS last_name
                FROM {$prefix}blooptoken bt
                INNER JOIN {$prefix}users u ON u.ID = bt.author
                INNER JOIN {$prefix}usermeta t2 ON u.id = t2.user_id
                WHERE bt.share_type='$type' GROUP BY u.ID";
    }else{
        $sql = "SELECT 
                    bt.*, 
                    u.ID,
                    p.post_title,
                    MAX(CASE WHEN t2.meta_key = 'first_name' THEN meta_value END) AS first_name,
                    MAX(CASE WHEN t2.meta_key = 'last_name' THEN meta_value END) AS last_name
                FROM {$prefix}blooptoken bt
                INNER JOIN {$prefix}posts p ON p.ID = bt.collection_id
                INNER JOIN {$prefix}users u ON u.ID = bt.author
                INNER JOIN {$prefix}usermeta t2 ON u.id = t2.user_id GROUP BY u.ID";
    }

        $bloop_token = $wpdb->get_results($sql);
    if ( $bloop_token ){
        return $bloop_token;
    }
    return false;
}
