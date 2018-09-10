<div id="graphjs-admin-page" class="wrap">
    <h1>Pages</h1>
    <p>This tool allows you to create GraphJS pages easily, in just one click.</p>

    <?php settings_errors(); ?>

    <form method="post">
        <?php
            settings_fields('graphjs_options');
            do_settings_sections('graphjs_options');
        ?>

        <table class="form-table">
            <tbody>
                <?php foreach ($pages as $key => $page): ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="<?= esc_attr( $page['meta_key'] ) ?>"
                                       <?php checked(isset($page['page_id'])) ?>>
                                <?= $page['title'] ?>
                            </label>
                            <?php if ($page['page_id']): ?>
                            <a href="<?= esc_attr(get_edit_post_link($page['page_id'])) ?>"
                               class="button button-small button-edit-page">Edit page</a>
                            <?php endif; ?>
                            <?php if ($page['description']): ?>
                            <p class="description">
                                <?= $page['description'] ?>
                            </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php submit_button(); ?>

    </form>
</div>
