<?php
function bloop_acf_fields()
{
    if (function_exists('acf_add_local_field_group')) :

        acf_add_local_field_group(array(
            'key' => 'group_5d142e27a30c5',
            'title' => 'Collection Field',
            'fields' => array(
                array(
                    'key' => 'field_5d142e312c5e0',
                    'label' => 'Portfolios',
                    'name' => 'bloop_portfolios',
                    'type' => 'repeater',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'collapsed' => '',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'table',
                    'button_label' => '',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_5d142f1911f0b',
                            'label' => 'Portfolio',
                            'name' => 'bloop_collection_portfolio',
                            'type' => 'post_object',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'post_type' => array(
                                0 => 'portfolio',
                            ),
                            'taxonomy' => '',
                            'allow_null' => 0,
                            'multiple' => 0,
                            'return_format' => 'id',
                            'ui' => 1,
                        ),
                        array(
                            'key' => 'field_5d142e5f2c5e2',
                            'label' => 'Date Created',
                            'name' => 'bloop_date_created',
                            'type' => 'date_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'display_format' => 'd/m/Y',
                            'return_format' => 'd/m/Y',
                            'first_day' => 1,
                        ),
                        array(
                            'key' => 'field_5d142e7f2c5e3',
                            'label' => 'Shared to',
                            'name' => 'bloop_shared_to',
                            'type' => 'number',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'collection',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

    endif;
}

add_action('acf/init', 'bloop_acf_fields');
