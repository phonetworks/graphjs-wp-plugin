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
                    <?php if ($graphjsUsername): ?>
                        <div>Connected as <strong id="graphjs_username"><?= $graphjsUsername ?></strong></div>
                    <?php else: ?>
                        <div>
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
                            <div><input type="hidden" id="graphjs_login_status" name="graphjs_login_status"></div>
                            <div id="graphjs_response"></div>
                            <div>
                                <button type="button" id="graphjs_login_button">Connect</button>
                            </div>
                        </div>
                    <?php endif ?>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
</div>

<script>

(function ($) {

    function disableFormSubmitOnEnter(jqElement) {
        jqElement.keydown(function(event) {
            if(event.keyCode == 13) {
                event.preventDefault();
            }
        });
    }

    $(function() {
        disableFormSubmitOnEnter($('#graphjs_login_username'));
        disableFormSubmitOnEnter($('#graphjs_login_password'));

        $('#graphjs_login_button').on('click', function () {

            var txtUsername = $('#graphjs_login_username');
            var txtPassword = $('#graphjs_login_password');
            var msgGraphjsResponse = $('#graphjs_response');
            var inputLoginStatus = $('#graphjs_login_status');

            var username = txtUsername.val();
            var password = txtPassword.val();

            GraphJS.login(username, password, function (responseJson) {
                if (responseJson.success === false) {
                    msgGraphjsResponse.text("Error: " + responseJson.reason);
                    inputLoginStatus.val('');
                    return;
                }
                msgGraphjsResponse.text("Successful");
                inputLoginStatus.val('success');
            });
        });
    });
})(jQuery);

</script>
