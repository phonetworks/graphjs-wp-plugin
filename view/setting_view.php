<div class="wrap">
    <h1>Settings</h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
            settings_fields('graphjs_options');
            do_settings_sections('graphjs_options');
        ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">GraphJS UUID</th>
                <td>
                    <input type="text" name="<?= esc_attr( \Graphjs\WordpressPlugin::GRAPHJS_UUID ) ?>"
                           value="<?= esc_attr( get_option(\Graphjs\WordpressPlugin::GRAPHJS_UUID) ) ?>"
                           class="regular-text"
                           placeholder="Example: b43467ac-883e-4222-9238-8df35e21af55">
                </td>
            </tr>

            <tr>
                <th scope="row">Theme</th>
                <td>
                    <select name="<?= esc_attr( \Graphjs\WordpressPlugin::GRAPHJS_THEME ) ?>">
                        <option value="light" <?php selected(get_option(\Graphjs\WordpressPlugin::GRAPHJS_THEME), 'light') ?>>Light</option>
                        <option value="dark" <?php selected(get_option(\Graphjs\WordpressPlugin::GRAPHJS_THEME), 'dark') ?>>Dark</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Color (Hex Format)</th>
                <td>
                    <input type="text" name="<?= esc_attr( \Graphjs\WordpressPlugin::GRAPHJS_COLOR ) ?>"
                           value="<?= esc_attr( get_option(\Graphjs\WordpressPlugin::GRAPHJS_COLOR) ) ?>"
                           class="regular-text"
                           placeholder="Example: #fe8945">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Host</th>
                <td>
                    <input type="text" name="<?= esc_attr( \Graphjs\WordpressPlugin::GRAPHJS_HOST ) ?>"
                           value="<?= esc_attr( get_option(\Graphjs\WordpressPlugin::GRAPHJS_HOST) ) ?>"
                           class="regular-text"
                           placeholder="Example: https://phonetworks.com">
                </td>
            </tr>

            <tr>
                <th scope="row">Comment Override</th>
                <td>
                    <input type="checkbox" name="<?= esc_attr( \Graphjs\WordpressPlugin::GRAPHJS_OVERRIDE_COMMENT ) ?>"
                            <?php checked(get_option(\Graphjs\WordpressPlugin::GRAPHJS_OVERRIDE_COMMENT)) ?>>
                    Override default comment with GraphJS Comment using <code>&lt;graphjs-comments&gt;</code>
                </td>
            </tr>

            <tr>
                <th scope="row">Use GraphJS Login</th>
                <td>
                    <input type="checkbox" name="<?= esc_attr( \Graphjs\WordpressPlugin::GRAPHJS_USE_GRAPHJS_LOGIN ) ?>"
                            <?php checked(get_option(\Graphjs\WordpressPlugin::GRAPHJS_USE_GRAPHJS_LOGIN)) ?>>
                    Use GraphJS Login for logging into WordPress
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
</div>
