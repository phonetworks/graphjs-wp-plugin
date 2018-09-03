<?php

namespace Graphjs;

class WordpressPlugin
{
    const GRAPHJS_UUID = 'graphjs_uuid';
    const GRAPHJS_THEME = 'graphjs_theme';
    const GRAPHJS_COLOR = 'graphjs_color';
    const GRAPHJS_OVERRIDE_COMMENT = 'graphjs_override_comment';

    const GRAPHJS_DEFAULT_THEME = "light";
    const GRAPHJS_DEFAULT_OVERRIDE_COMMENT = false;

    const GRAPHJS_USERNAME = 'graphjs_username';
    const GRAPHJS_PASSWORD = 'graphjs_password';

    private $pluginFile;
    private $pluginDirectory;
    private $graphjs;

    /**
     * @param string $pluginFile
     * @param string $pluginDirectory
     * @param Graphjs $graphjs
     */
    public function __construct($pluginFile, $pluginDirectory, Graphjs $graphjs)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginDirectory = $pluginDirectory;
        $this->graphjs = $graphjs;
    }

    public function getPluginFile()
    {
        return $this->pluginFile;
    }

    public function getPluginDirectory()
    {
        return $this->pluginDirectory;
    }

    public function bootstrap()
    {
        $this->registerActivationHook();
        $this->registerDeactivationHook();
        $this->registerUninstallHook();

        $this->registerShortcodes();
        $this->registerActions();
        $this->registerFilters();
    }

    public function initMetabox()
    {
        add_action('add_meta_boxes', [ $this, 'addMetabox' ]);
        add_action('save_post', [ $this, 'saveMetabox' ], 10, 2);
    }

    public function addMetabox($postType)
    {
        $supportedPostTypes = [ 'post', 'page' ];

        if (in_array($postType, $supportedPostTypes)) {
            add_meta_box(
                'graphjs-admin-content-restriction',
                'GraphJS Content Restriction',
                [$this, 'renderMetabox'],
                $postType,
                'advanced',
                'default'
            );
        }
    }

    public function renderMetabox(\WP_Post $post)
    {
        $graphjsUsername = get_option(self::GRAPHJS_USERNAME);
        $graphjsPassword = get_option(self::GRAPHJS_PASSWORD);
        $graphjsContentRestriction = get_post_meta($post->ID, 'graphjs_restrict_content', true);

        $viewModel = [];
        $viewModel['contentRestriction'] = boolval($graphjsContentRestriction);
        $viewModel['graphjsId'] = get_post_meta($post->ID, 'graphjs_private_content_id', true);
        $viewModel['isRestrictable'] = $graphjsUsername && $graphjsPassword;
        $viewModel['graphjsSettingUrl'] = admin_url('admin.php?page=graphjs-settings');

        wp_enqueue_script('graphjs-metabox', plugin_dir_url($this->pluginFile) . 'js/metabox.js', 'jquery');

        extract($viewModel);
        include $this->pluginDirectory . '/view/content_restriction_metabox.php';
    }

    public function saveMetabox($postId, \WP_Post $post)
    {
        /**
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $postId;
        }

        $graphjsUsername = get_option(self::GRAPHJS_USERNAME);
        $graphjsPassword = get_option(self::GRAPHJS_PASSWORD);

        $privateContentId = get_post_meta($postId, 'graphjs_private_content_id', true);

        // return if not connected to GraphJS
        if (! ($graphjsUsername && $graphjsPassword)) {
            return;
        }

        $contentRestriction = $_POST['graphjs_content_restriction_status'] ?? null;
        $isRestricted = ($contentRestriction === 'on');

        if ($isRestricted) {
            $graphjsApi = new GraphjsApi([
                'publicId' => get_option(self::GRAPHJS_UUID),
            ]);

            $response = $graphjsApi->login([
                'username' => $graphjsUsername,
                'password' => $graphjsPassword,
            ]);
            if ($response instanceof \WP_Error) {
                return;
            }
            $json = json_decode($response['body'], true);
            if ($json['success'] === false) {
                return;
            }

            $cookies = $response['cookies'];
            $sessionCookie = current(array_filter($cookies, function (\WP_Http_Cookie $cookie) {
                return $cookie->name === 'id';
            }));
            if ($sessionCookie !== false) {
                $graphjsApi->setSessionCookie($sessionCookie);
            }
            if (! $privateContentId) {
                $response = $graphjsApi->addPrivateContent([
                    'data' => $post->post_content,
                ]);
                if ($response instanceof \WP_Error) {
                    return;
                }
                $json = json_decode($response['body'], true);
                if ($json['success'] === false) {
                    return;
                }
                $privateContentId = $json['id'];
            }
            else {
                $response = $graphjsApi->editPrivateContent($privateContentId, [
                    'data' => $post->post_content,
                ]);
                if ($response instanceof \WP_Error) {
                    return;
                }
                $json = json_decode($response['body'], true);
                if ($json['success'] === false) {
                    return;
                }
            }
            update_post_meta($postId, 'graphjs_private_content_id', $privateContentId);
        }

        update_post_meta($postId, 'graphjs_restrict_content', $isRestricted);
    }

    public function registerActivationHook()
    {
        register_activation_hook($this->pluginFile, [ $this, 'activate' ]);
    }

    public function registerDeactivationHook()
    {
        register_deactivation_hook($this->pluginFile, [ $this, 'deactivate' ]);
    }

    public function registerUninstallHook()
    {
        register_uninstall_hook($this->pluginFile, [ __CLASS__, 'uninstall' ]);
    }

    public function activate()
    {
        $uuid = get_option(self::GRAPHJS_UUID);
        if ($uuid === false) {
            add_option(self::GRAPHJS_UUID, "");
        }

        $theme = get_option(self::GRAPHJS_THEME);
        if ($theme === false) {
            add_option(self::GRAPHJS_THEME, self::GRAPHJS_DEFAULT_THEME);
        }

        $color = get_option(self::GRAPHJS_COLOR);
        if ($color === false) {
            add_option(self::GRAPHJS_COLOR, "");
        }

        $overrideComment = get_option(self::GRAPHJS_OVERRIDE_COMMENT);
        if ($overrideComment === false) {
            add_option(self::GRAPHJS_OVERRIDE_COMMENT, self::GRAPHJS_DEFAULT_OVERRIDE_COMMENT);
        }
    }

    public function deactivate()
    {

    }

    public static function uninstall()
    {
        delete_option(self::GRAPHJS_UUID);
        delete_option(self::GRAPHJS_THEME);
        delete_option(self::GRAPHJS_COLOR);
        delete_option(self::GRAPHJS_OVERRIDE_COMMENT);
    }

    public function registerShortcodes()
    {
        $elements = $this->graphjs->getElements();
        array_walk($elements, function ($element) {
            $shortcodeRenderer = new ShortcodeRenderer($element);
            add_shortcode($element, [ $shortcodeRenderer, 'render' ]);
        });
    }

    public function registerActions()
    {
        add_action('admin_init', function () {

            $this->addActionLinks();
            $this->registerSettings();
        });

        add_action('admin_menu', function () {
            $this->addAdminMenuPage();
        });

        add_action('wp_footer', [ $this, 'my_custom_admin_head' ]);

        if (is_admin()) {
            add_action('load-post.php', [ $this, 'initMetabox' ]);
            add_action('load-post-new.php', [ $this, 'initMetabox' ]);

            $callback = function () {
                add_action('admin_footer', [ $this, 'my_custom_admin_head' ]);
            };

            add_action('load-post.php', $callback);
            add_action('load-post-new.php', $callback);
        }

        add_action('admin_footer', [ $this, 'my_custom_admin_head' ]);

        add_action('admin_enqueue_scripts', [ $this, 'enqueueAdminScript' ]);

        add_action('widgets_init', function () {
            register_widget(new Widget($this));
        });

        add_action('wp_ajax_load_graphjs_private_content', [ $this, 'loadPrivateContentAjax' ]);
        add_action('wp_ajax_nopriv_load_graphjs_private_content', [ $this, 'loadPrivateContentAjax' ]);
    }

    public function loadPrivateContentAjax()
    {
        $content = $_POST['contents'];
        $content = apply_filters('the_content', $content);
        echo $content;
        wp_die();
    }

    public function enqueueAdminScript($hook)
    {
        global $post;

        $wp_scripts = wp_scripts();

        if ($hook === 'graphjs_page_graphjs-settings') {
            wp_enqueue_script('graphjs-jquery-ui-js', plugin_dir_url($this->pluginFile) . 'js/setting.js', [ 'jquery-ui-tabs' ]);
            wp_enqueue_style(
                'graphjs-jquery-ui-css',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css'
            );
        }
    }

    public function my_custom_admin_head()
    {
        $adminAjaxUrl = wp_json_encode(admin_url('admin-ajax.php'));
        $graphjsLoginUrl = wp_json_encode($this->getGraphjsLoginUrl());

        $path = $this->pluginDirectory . '/view/init.php';
        include $path;
    }

    public function addAdminMenuPage()
    {
        $graphjs_main_menu_page = function () {
            $elements = $this->graphjs->getElements();
            $path = $this->pluginDirectory . '/view/graphjs.php';
            include $path;
        };

        add_menu_page('GraphJS', 'GraphJS',
            'administrator', 'graphjs',
            $graphjs_main_menu_page, 'dashicons-admin-generic');

        $graphjs_settings_menu_page = function () {
            $inputNameGraphjsUsername = self::GRAPHJS_USERNAME;
            $inputNameGraphjsPassword = self::GRAPHJS_PASSWORD;
            $graphjsUsername = get_option(self::GRAPHJS_USERNAME);
            $path = $this->pluginDirectory . '/view/setting_view.php';
            include $path;
        };

        add_submenu_page('graphjs', 'Tutorial', 'Tutorial',
            'administrator', 'graphjs',
            $graphjs_main_menu_page);

        add_submenu_page('graphjs', 'GraphJS Settings', 'Settings',
            'administrator', 'graphjs-settings',
            $graphjs_settings_menu_page);
    }

    public function addActionLinks()
    {
        // Add setting link
        $plugin_file = plugin_basename($this->pluginFile);
        $fn = function ($actions) {
            $actions['settings'] = '<a href="' . menu_page_url('graphjs-settings', false) . '"/>' . __('Settings') . '</a>';
            return $actions;
        };
        add_filter("plugin_action_links_$plugin_file", $fn);
    }

    public function registerFilters()
    {
        add_filter('comments_template', function () {
            return $this->getCommentTemplate();
        });

        add_filter('whitelist_options', [ $this, 'filterWhitelistOptionsOnSave' ], 11);

        add_filter('template_include', [ $this, 'getTemplateInclude' ], 99);

        add_filter('document_title_parts', [ $this, 'getDocumentTitleParts' ]);

        add_filter('the_content', function ($content) {

            global $post;

            $restrictContent = get_post_meta($post->ID, 'graphjs_restrict_content', true);
            if (! $restrictContent) {
                return $content;
            }

            $privateContentId = get_post_meta($post->ID, 'graphjs_private_content_id', true);
            $content = sprintf('<div data-graphjs-private-content data-graphjs-id="%s"></div>', $privateContentId);

            wp_enqueue_script('graphjs-javascript', plugin_dir_url($this->pluginFile) . 'js/public.js', [ 'jquery' ]);

            return $content;
        }, 1);
    }

    public function getGraphjsLoginUrl()
    {
        /**
         * @var \WP_Rewrite $wp_rewrite
         */
        global $wp_rewrite;
        $permalink = $wp_rewrite->get_page_permastruct();
        $permalink = str_replace('%pagename%', 'graphjs-login', $permalink);
        $loginUrl = home_url( user_trailingslashit($permalink, 'page'));

        return $loginUrl;
    }

    public function getTemplateInclude($template)
    {
        /**
         * @var \WP $wp
         * @var \WP_Query $wp
         */
        global $wp, $wp_query;

        if ($wp->request === 'graphjs-login') {
            $wp_query->is_404 = false;
            status_header(200);
            wp_enqueue_script('graphjs-javascript', plugin_dir_url($this->pluginFile) . 'js/public.js', [ 'jquery' ]);
            $template = $this->pluginDirectory . '/view/login.php';
        }

        return $template;
    }

    public function getDocumentTitleParts(array $titleParts)
    {
        /**
         * @var \WP $wp
         */
        global $wp;

        if ($wp->request === 'graphjs-login') {
            $titleParts['title'] = 'GraphJS Login';
        }

        return $titleParts;
    }

    public function filterWhitelistOptionsOnSave($options)
    {
        if ($_POST['option_page'] !== 'graphjs_options') {
            return $options;
        }

        $graphjsOptions = $options['graphjs_options'] ?? null;
        if ($graphjsOptions === null) {
            return $options;
        }

        $loginStatus = $_POST['graphjs_login_status'] ?? null;
        if ($loginStatus === null) {
            return $options;
        }

        if ($loginStatus !== 'success') {
            $graphjsOptions = array_diff($graphjsOptions, [ self::GRAPHJS_USERNAME, self::GRAPHJS_PASSWORD ]);
            $options['graphjs_options'] = $graphjsOptions;
        }

        return $options;
    }

    public function getCommentTemplate()
    {
        if ($this->overrideCommentTemplate()) {
            return plugin_dir_path( dirname( __FILE__ ) ) . 'view/comment.php';
        }
    }

    public function overrideCommentTemplate()
    {
        return boolval(get_option(self::GRAPHJS_OVERRIDE_COMMENT));
    }

    public function registerSettings()
    {
        // Register allowed form fields of setting
        register_setting('graphjs_options', self::GRAPHJS_UUID, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_THEME, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_COLOR, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_OVERRIDE_COMMENT, 'boolval');
        register_setting('graphjs_options', self::GRAPHJS_USERNAME, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_PASSWORD, 'strval');
    }
}
