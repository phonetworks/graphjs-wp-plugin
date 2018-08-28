<script src="https://graphjs.com/v1.2/graph.js"></script>
<script>

GraphJS.init("<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_UUID)) ?>", {
    theme: "<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_THEME)) ?>",
    color: "<?= esc_attr(get_option(\Graphjs\WordpressPlugin::GRAPHJS_COLOR)) ?>",
});

</script>
