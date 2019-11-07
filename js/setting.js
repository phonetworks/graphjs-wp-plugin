(function ($) {

    $(function() {

        GraphJS.on('afterLogin', function (formData) {

            var txtUsername = $('#txt_graphjs_username');
            var txtPassword = $('#txt_graphjs_password');
            var inputLoginStatus = $('#graphjs_login_status');
            var labelUsername = $('#label_graphjs_username');
            var wrapperLogin = $('#wrapper_graphjs_login');
            var wrapperCurrentUser = $('#wrapper_graphjs_current_user');

            var username = formData[0];
            var password = formData[1];

            txtUsername.val(username);
            txtPassword.val(password);

            inputLoginStatus.val('success');
            labelUsername.text(username);
            setTimeout(function () {
                wrapperLogin.fadeOut();
                wrapperCurrentUser.fadeIn('fast');
            }, 1000);
        });

        $('#btn_graphjs_change_login').click(function () {

            var wrapperLogin = $('#wrapper_graphjs_login');
            var wrapperCurrentUser = $('#wrapper_graphjs_current_user');

            setTimeout(function () {
                wrapperCurrentUser.hide();//fadeOut('fast');
                wrapperLogin.show();//fadeIn('fast');
            }, 1000);
        });
    });
})(jQuery);
