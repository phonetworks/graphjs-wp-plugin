(function ($) {

    function urlParam (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)')
            .exec(window.location.href);
        if (results == null) {
            return 0;
        }
        return results[1] || 0;
    }

    $(function () {
        GraphJS.on('afterLogin', function () {

            var redirectTo = urlParam('redirect_to');
            redirectTo = redirectTo ? decodeURIComponent(urlParam('redirect_to')) : redirectTo;
            if (redirectTo) {
                window.location.href = redirectTo;
            }
            else {
                window.location.href = $('#wp_home_url').val();
            }
        });
    });

    $(function () {
        GraphJS.getSession(function (json) {
            var isLoggedIn = (json.success === true);
            $('[data-graphjs-private-content]').each(function (index, element) {
                if (! isLoggedIn) {
                    var loginUrl = GRAPHJS_LOGIN_URL;
                    var queryParams = $.param({
                        'redirect_to': window.location.href,
                    });
                    loginUrl = loginUrl + '?' + queryParams;
                    $(element).html('This is private content. <a href="' + loginUrl
                        + '">Log-in to GraphJS</a> to view the content.');
                    return;
                }

                var id = $(element).data('graphjs-id');
                GraphJS.getPrivateContent(id, function (json) {
                    if (json.success === false) {
                        $(element).html('Failed to load private content');
                        return;
                    }
                    var contents = json.contents;
                    var payload = {
                        'action': 'load_graphjs_private_content',
                        'contents': contents,
                    };
                    $.post(ADMIN_AJAX_URL, payload, function (response) {
                        $(element).html(response);
                    });
                });
            });
        });
    });
})(jQuery);
