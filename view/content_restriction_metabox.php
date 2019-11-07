<p class="meta-options">
    <?php if ($isRestrictable): ?>
        <label for="graphjs_content_restriction_status">
            Restrict access using <code>&lt;graphjs-private-content&gt;&lt;/graphjs-private-content&gt;</code>
            <input type="checkbox" id="graphjs_content_restriction_status"
                   name="graphjs_content_restriction_status"
                <?php checked($contentRestriction) ?>>
        </label>
    <?php else: ?>
        <div id="graphjs_restriction_notice">
            <p>You must <a href="<?= $graphjsSettingUrl ?>">connect to GraphJS</a> to use content restriction</p>
        </div>
    <?php endif ?>
</p>
