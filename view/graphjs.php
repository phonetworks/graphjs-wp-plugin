<div class="wrap">
    <h1>GraphJS</h1>

    <h2>Usage</h2>
    <p>
        This plugin provides a set of shortcodes that generates equivalent graphjs HTML elements with attributes.
        <br>
        For example, the shortcode <code>[graphjs-auth position="topleft"]</code> will generate <code>&lt;graphjs-auth position="topleft"&gt;&lt;/graphjs-auth&gt;</code>.
    </p>
    <p>For more information on Graphjs elements, see <a href="https://graphjs.com/docs" target="_blank">https://graphjs.com/docs</a>.</p>

    <h2>Supported Shortcodes</h2>

    <ol>
        <?php foreach ($elements as $element): ?>
            <li><code><?= $element ?></code></li>
        <?php endforeach; ?>
    </ol>
</div>
