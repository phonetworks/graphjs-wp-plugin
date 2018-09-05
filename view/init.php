<script src="https://graphjs.com/v1.2/graph.js"></script>
<script>

var ADMIN_AJAX_URL = <?= $adminAjaxUrl ?>;
var GRAPHJS_LOGIN_URL = <?= $graphjsLoginUrl ?>

GraphJS.init(<?= wp_json_encode($uuid) ?>, <?= wp_json_encode($options) ?>);

</script>
