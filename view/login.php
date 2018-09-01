<?php get_header() ?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <h1>GraphJS Login</h1>
        <graphjs-auth-login></graphjs-auth-login>
    </main>
</div>
<?php get_sidebar() ?>
<input type="hidden" id="wp_home_url" value="<?= esc_attr(get_home_url()) ?>">
<?php get_footer() ?>
