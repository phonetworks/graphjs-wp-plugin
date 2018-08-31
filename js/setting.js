(function ($) {

    function checkTab() {
        var active = $('#graphjs_auth_tabs .ui-tabs-active');
        var isLogin = active.find('[href=#tabs_login]').length !== 0;
        var isRegister = active.find('[href=#tabs_register]').length !== 0;
        if (isLogin) {
            $('#graphjs_login_username').prop('disabled', false);
            $('#graphjs_login_password').prop('disabled', false);
            $('#graphjs_register_username').prop('disabled', true);
            $('#graphjs_register_password').prop('disabled', true);
        }
        else if (isRegister) {
            $('#graphjs_login_username').prop('disabled', true);
            $('#graphjs_login_password').prop('disabled', true);
            $('#graphjs_register_username').prop('disabled', false);
            $('#graphjs_register_password').prop('disabled', false);
        }
    }

    $(function () {
        $('#graphjs_auth_tabs').tabs({
            create: function (e, ui) {
                checkTab();
            },
            activate: function (e, ui) {
                checkTab();
            }
        });
    });

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
            var msgGraphjsResponse = $('#graphjs_login_response');
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
                $('#graphjs_username').text(username);
                $('#graphjs_user').removeClass('hidden');
                $('#graphjs_auth_tabs').addClass('hidden');
            });
        });

        disableFormSubmitOnEnter($('#graphjs_register_username'));
        disableFormSubmitOnEnter($('#graphjs_register_email'));
        disableFormSubmitOnEnter($('#graphjs_register_password'));

        $('#graphjs_register_button').on('click', function () {

            var txtEmail = $('#graphjs_register_email');
            var txtUsername = $('#graphjs_register_username');
            var txtPassword = $('#graphjs_register_password');
            var msgGraphjsResponse = $('#graphjs_register_response');
            var inputLoginStatus = $('#graphjs_register_status');

            var email = txtEmail.val();
            var username = txtUsername.val();
            var password = txtPassword.val();

            GraphJS.register(username, email, password, function (responseJson) {
                if (responseJson.success === false) {
                    msgGraphjsResponse.text("Error: " + responseJson.reason);
                    inputLoginStatus.val('');
                    return;
                }
                msgGraphjsResponse.text("Successful");
                inputLoginStatus.val('success');
                $('#graphjs_username').text(username);
                $('#graphjs_user').removeClass('hidden');
                $('#graphjs_auth_tabs').addClass('hidden');
            });
        });
    });
})(jQuery);
