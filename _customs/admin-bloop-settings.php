<div class="wrap">
    <h1>Setting</h1>
    <p>Set collection visibility per user role.</p>
    <form method="post" action="options.php">
        <?php settings_fields('bloop-settings-group'); ?>
        <?php do_settings_sections('bloop-settings-group'); ?>
        <table class="form-table">
            <?php foreach($roles as $role): ?>
                <tr valign="top">
                    <th scope="row"><?php echo $role["name"]; ?></th>
                    <td>
                        <select name="<?php echo "bloop_option_". strtolower(str_replace(' ', '_', $role['name'])) ."[" . strtolower(str_replace(' ', '_', $role['name'])) . "]"; ?>[]" id="" multiple>
                            <?php foreach( $roles as $rolez ){ ?>
                                <option
                                    <?php
                                        selected_role_check($role["name"], strtolower($rolez['name']));
                                    ?>
                                    value="<?php
                                        $r = strtolower($rolez['name']);
                                        $r = str_replace(' ', '_', $r);
                                        echo esc_attr($r); 
                                    ?>"><?php echo esc_attr($rolez["name"]); ?></option>
                            <?php } ?>
                        </select>    
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>

        <?php submit_button(); ?>

    </form>
</div>