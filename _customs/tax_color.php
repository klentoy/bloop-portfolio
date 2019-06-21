<?php
// Register Custom Taxonomy
function custom_taxonomy_color()
{

    $labels = array(
        'name'                       => _x('Colors', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Color', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Colors', 'text_domain'),
        'all_items'                  => __('All Items', 'text_domain'),
        'parent_item'                => __('Parent Item', 'text_domain'),
        'parent_item_colon'          => __('Parent Item:', 'text_domain'),
        'new_item_name'              => __('New Item Name', 'text_domain'),
        'add_new_item'               => __('Add New Item', 'text_domain'),
        'edit_item'                  => __('Edit Item', 'text_domain'),
        'update_item'                => __('Update Item', 'text_domain'),
        'view_item'                  => __('View Item', 'text_domain'),
        'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Items', 'text_domain'),
        'search_items'               => __('Search Items', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
        'no_terms'                   => __('No items', 'text_domain'),
        'items_list'                 => __('Items list', 'text_domain'),
        'items_list_navigation'      => __('Items list navigation', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy('portfolio_colors', array('portfolio'), $args);
}
add_action('init', 'custom_taxonomy_color', 0);


function portfolio_colors_taxonomy_custom_fields($tag)
{
    $t_id = $tag->term_id;
    $term_meta = get_option("taxonomy_term_$t_id");
    ?>

    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="color_hex"><?php _e('Color HEX'); ?></label>
        </th>
        <td>
            <input type="text" name="term_meta[color_hex]" id="term_meta[color_hex]" size="25" style="width:60%;" value="<?php echo $term_meta['color_hex'] ? $term_meta['color_hex'] : ''; ?>"><br />
            <span class="description"><?php _e('This will be the HEX value of the color desired.'); ?></span></br></br>
        </td>
    </tr>

<?php
}

add_action('portfolio_colors_add_form_fields', 'portfolio_colors_taxonomy_custom_fields', 10, 2);

// Edit term page
function portfolio_colors_taxonomy_edit_meta_field($term)
{

    // put the term ID into a variable
    $t_id = $term->term_id;

    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option("taxonomy_$t_id"); ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[color_hex]"><?php _e('Color HEX'); ?></label></th>
        <td>
            <input type="text" name="term_meta[color_hex]" id="term_meta[color_hex]" value="<?php echo esc_attr($term_meta['color_hex']) ? esc_attr($term_meta['color_hex']) : ''; ?>">
            <p class="description"><?php _e('This will be the HEX value of the color desired.'); ?></p>
        </td>
    </tr>
<?php
}
add_action('portfolio_colors_edit_form_fields', 'portfolio_colors_taxonomy_edit_meta_field', 10, 2);

// A callback function to save our extra taxonomy field(s)  
function save_taxonomy_custom_fields($term_id)
{
    if (isset($_POST['term_meta'])) {
        $t_id = $term_id;
        $term_meta = get_option("taxonomy_term_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array  
        update_option("taxonomy_term_$t_id", $term_meta);
    }
}

add_action('edited_portfolio_colors', 'save_taxonomy_custom_fields', 10, 2);
add_action('create_portfolio_colors', 'save_taxonomy_custom_fields', 10, 2);
