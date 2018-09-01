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
        GraphJS.on('afterLogin', function (formData) {

            var username = formData[0];
            var password = formData[1];

            debugger;
            var redirectTo = urlParam('redirect_to');
            redirectTo = decodeURIComponent(urlParam('redirect_to'));
            if (redirectTo) {
                window.location.href = redirectTo;
            }
            else {
                window.location.href = $('#wp_home_url').val();
            }
        });
    });
})(jQuery);
