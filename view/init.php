<script src="https://graphjs.com/graph.js"></script>
<script>

GraphJS.init("<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_UUID)) ?>", {
    host: "<?= "https://gj" . strtolower(substr(str_replace("-","", esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_UUID))), 4)) . ".herokuapp.com" ?>",
    theme: "<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_THEME)) ?>",
    color: "<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_COLOR)) ?>",
});

</script>
