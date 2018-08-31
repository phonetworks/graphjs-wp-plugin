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

            <tr>
                <th scope="row">Comment Override</th>
                <td>
                    <input type="checkbox" name="<?= esc_attr( \Graphjs\WordpressPlugin::GRAPHJS_OVERRIDE_COMMENT ) ?>"
                            <?php checked(get_option(\Graphjs\WordpressPlugin::GRAPHJS_OVERRIDE_COMMENT)) ?>>
                    Override default comment with GraphJS Comment using <code>&lt;graphjs-comments&gt;</code>
                </td>
            </tr>

            <tr>
                <th scope="row">Connect with GraphJS</th>
                <td>
                    <div id="graphjs_user" class="<?= $graphjsUsername ? '' : 'hidden' ?>">Connected as <strong id="graphjs_username"><?= $graphjsUsername ?></strong></div>
                    <div id="graphjs_auth_tabs" class="<?= ! $graphjsUsername ? '' : 'hidden' ?>" style="max-width: 450px;">
                        <ul>
                            <li><a href="#tabs_login">Login</a></li>
                            <li><a href="#tabs_register">Register</a></li>
                        </ul>
                        <div id="tabs_login">
                            <div>
                                Username:
                                <br>
                                <input type="text" id="graphjs_login_username" name="<?= esc_attr( $inputNameGraphjsUsername ) ?>" class="regular-text">
                            </div>
                            <div>
                                Password:
                                <br>
                                <input type="password" id="graphjs_login_password" name="<?= esc_attr( $inputNameGraphjsPassword ) ?>" class="regular-text">
                            </div>
                            <div id="graphjs_login_response"></div>
                            <div>
                                <button type="button" id="graphjs_login_button" class="button button-default">Login</button>
                            </div>
                        </div>
                        <div id="tabs_register">
                            <div>
                                Username:
                                <br>
                                <input type="text" id="graphjs_register_username" name="<?= esc_attr( $inputNameGraphjsUsername ) ?>" class="regular-text">
                            </div>
                            <div>
                                Email:
                                <br>
                                <input type="text" id="graphjs_register_email" class="regular-text">
                            </div>
                            <div>
                                Password:
                                <br>
                                <input type="password" id="graphjs_register_password" name="<?= esc_attr( $inputNameGraphjsPassword ) ?>" class="regular-text">
                            </div>
                            <div id="graphjs_register_response"></div>
                            <div>
                                <button type="button" id="graphjs_register_button" class="button button-default">Register</button>
                            </div>
                        </div>
                    </div>
                    <div><input type="hidden" id="graphjs_login_status" name="graphjs_login_status"></div>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
</div>
