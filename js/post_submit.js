(function ($) {
    function confirmContinueOnFail() {
        return confirm("Failed to save to GraphJS. Continue to save post?");
    }
    $(function () {
        $('#publish').on('click', function (ev) {
            var isRestriced = $('#graphjs_content_restriction_status').is(":checked");
            if (! isRestriced) {
                return;
            }

            ev.preventDefault();

            var form = $('#post');
            var content = $('#content').val();
            var graphjsId = $('#graphjs_content_restriction_id').text();
            if (! graphjsId) {
                GraphJS.addPrivateContent(content, function (response) {
                    if (response.success !== true) {
                        if(confirmContinueOnFail()) {
                            form.submit();
                        }
                        return;
                    }
                    $('#graphjs_content_restriction_id').val(response.id);
                    form.submit();
                });
            }
            else {
                GraphJS.editPrivateContent(graphjsId, content, function (response) {
                    if (response.success !== true) {
                        if(confirmContinueOnFail()) {
                            form.submit();
                        }
                        return;
                    }
                    form.submit();
                });
            }
        });
    });
})(jQuery);
