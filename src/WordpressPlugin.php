<?php

namespace Graphjs;

class WordpressPlugin
{
    const GRAPHJS_UUID = 'graphjs_uuid';
    const GRAPHJS_THEME = 'graphjs_theme';
    const GRAPHJS_COLOR = 'graphjs_color';

    const GRAPHJS_DEFAULT_THEME = "light";

    private $pluginFile;
    private $pluginDirectory;
    private $supportedElements = [];

    public function __construct($pluginFile, $pluginDirectory)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginDirectory = $pluginDirectory;
    }

    public function bootstrap()
    {
        $this->registerActivationHook();
        $this->registerDeactivationHook();
        $this->registerUninstallHook();

        $this->registerShortcodes();
        $this->registerActions();
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
    }

    public function deactivate()
    {

    }

    public static function uninstall()
    {
        delete_option(GRAPHJS_UUID);
        delete_option(GRAPHJS_THEME);
        delete_option(GRAPHJS_COLOR);
    }

    public function registerShortcodes()
    {
        add_shortcode('graphjs-auth', [ $this, 'graphjsShortcodeAuth' ]);
    }

    public function registerActions()
    {
        add_action('admin_menu', function () {
            $this->addAdminMenu();
        });

        add_action('wp_footer', [ $this, 'my_custom_admin_head' ]);
    }

    public function my_custom_admin_head()
    {
        $path = $this->pluginDirectory . '/view/init.php';
        include $path;
    }

    public function addAdminMenu()
    {
        $graphjs_settings_page = function () {
            $path = $this->pluginDirectory . '/view/setting_view.php';
            include $path;
        };

        add_menu_page('GraphJS Settings', 'GraphJS Settings',
            'administrator', 'graphjs-settings',
            $graphjs_settings_page, 'dashicons-admin-generic');

        add_action('admin_init', function () {

            // Add setting link
            $plugin_file = plugin_basename($this->pluginFile);
            $fn = function ($actions) {
                $actions['settings'] = '<a href="' . menu_page_url('graphjs-settings', false) . '"/>' . __('Settings') . '</a>';
                return $actions;
            };
            add_filter("plugin_action_links_$plugin_file", $fn);

            // Register allowed form fields of setting
            register_setting('graphjs_options', self::GRAPHJS_UUID, 'strval');
            register_setting('graphjs_options', self::GRAPHJS_THEME, 'strval');
            register_setting('graphjs_options', self::GRAPHJS_COLOR, 'strval');
        });
    }

    function graphjsShortcodeAuth($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        // override default attributes with usesr attributes
        $graphjs_atts = shortcode_atts([
            'type' => null,
            'theme' => null,
            'position' => null,
        ], $atts, $tag);

        $dom = new \DOMDocument('1.0');
        $auth_element = $dom->createElement('graphjs-auth');

        if (isset($graphjs_atts['type'])) {
            $attr_type = $dom->createAttribute('type');
            $attr_type->value = $graphjs_atts['type'];
            $auth_element->appendChild($attr_type);
        }

        if (isset($graphjs_atts['theme'])) {
            $attr_type = $dom->createAttribute('theme');
            $attr_type->value = $graphjs_atts['theme'];
            $auth_element->appendChild($attr_type);
        }

        if (isset($graphjs_atts['position'])) {
            $attr_type = $dom->createAttribute('position');
            $attr_type->value = $graphjs_atts['position'];
            $auth_element->appendChild($attr_type);
        }

        $dom->appendChild($auth_element);
        $output = $dom->saveHTML();
        return $output;
    }
}
