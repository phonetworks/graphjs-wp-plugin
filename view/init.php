<script src="https://graphjs.com/v1.2/graph.js"></script>
<script>

var ADMIN_AJAX_URL = <?= $adminAjaxUrl ?>;
var GRAPHJS_LOGIN_URL = <?= $graphjsLoginUrl ?>

GraphJS.init("<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_UUID)) ?>", {
    theme: "<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_THEME)) ?>",
    color: "<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_COLOR)) ?>",
});

</script>
