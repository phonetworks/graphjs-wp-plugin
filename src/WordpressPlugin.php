<?php

namespace Graphjs;

use Graphjs\Markup\ElementInterface;

class WordpressPlugin
{
    const GRAPHJS_UUID = 'graphjs_uuid';
    const GRAPHJS_THEME = 'graphjs_theme';
    const GRAPHJS_COLOR = 'graphjs_color';

    const GRAPHJS_DEFAULT_THEME = "light";

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
        $elements = $this->graphjs->getElements();
        array_walk($elements, function (ElementInterface $element) {
            $shortcodeRenderer = new ShortcodeRenderer($element);
            add_shortcode($element->getName(), [ $shortcodeRenderer, 'render' ]);
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
    }

    public function my_custom_admin_head()
    {
        $path = $this->pluginDirectory . '/view/init.php';
        include $path;
    }

    public function addAdminMenuPage()
    {
        $graphjs_settings_page = function () {
            $path = $this->pluginDirectory . '/view/setting_view.php';
            include $path;
        };

        add_menu_page('GraphJS', 'GraphJS',
            'administrator', 'graphjs-settings',
            $graphjs_settings_page, 'dashicons-admin-generic');
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

    public function registerSettings()
    {
        // Register allowed form fields of setting
        register_setting('graphjs_options', self::GRAPHJS_UUID, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_THEME, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_COLOR, 'strval');
    }
}
