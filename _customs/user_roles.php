<?php

function add_roles_on_plugin_activation() {
    add_role( 'developer', 'Developer', array( 'read' => true, 'edit_posts' => true, 'delete_posts' => false, ) );
}
add_roles_on_plugin_activation();