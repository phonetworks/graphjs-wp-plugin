<p class="meta-options">
    <label for="graphjs_content_restriction_status">
        Restrict access using <code>&lt;graphjs-private-content&gt;&lt;/graphjs-private-content&gt;</code>
        <input type="checkbox" id="graphjs_content_restriction_status"
               name="graphjs_content_restriction_status"
            <?php checked($contentRestriction) ?>>
    </label>
</p>
<p class="meta-options">
    <?php if ($graphjsId): ?>
    <label for="graphjs_content_restriction_id">
        GraphJS ID
        <input type="text" disabled id="graphjs_content_restriction_id"
                value="<?= esc_attr( $graphjsId ) ?>">
    </label>
    <?php else: ?>
    <input type="hidden" id="graphjs_content_restriction_id"
           name="graphjs_content_restriction_id" value="<?= esc_attr( $graphjsId ) ?>">
    <?php endif; ?>
</p>
<h3>Login if not logged in</h3>
<p>
    <graphjs-auth-login></graphjs-auth-login>
</p>
<h3>This is the private content of this post</h3>
<p>
    <graphjs-private-content id="<?= esc_attr($graphjsId) ?>"></graphjs-private-content>
</p>
