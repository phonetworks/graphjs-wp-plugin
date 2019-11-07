<?= $beforeWidget ?>
<?= $title ?>
<div id="graphjs_widget_login">
    <a href="<?= esc_attr($loginUrl) ?>">Log in</a>
</div>
<div id="graphjs_widget_user" style="display: none;">
    <graphjs-auth></graphjs-auth>
</div>
<?= $afterWidget ?>

<script>

(function ($) {

    $(function () {
        GraphJS.getSession(function (responseJson) {
            if (responseJson.success === false) {
                return;
            }
            $('#graphjs_widget_user').show();
            $('#graphjs_widget_login').hide();
        });

        GraphJS.on('afterLogout', function () {
            $('#graphjs_widget_user').hide();
            $('#graphjs_widget_login').show();
        });
    });
})(jQuery);

</script>
