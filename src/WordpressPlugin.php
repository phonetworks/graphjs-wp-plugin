<?php

namespace Graphjs;

class WordpressPlugin
{
    const GRAPHJS_UUID = 'graphjs_uuid';
    const GRAPHJS_THEME = 'graphjs_theme';
    const GRAPHJS_COLOR = 'graphjs_color';
    const GRAPHJS_OVERRIDE_COMMENT = 'graphjs_override_comment';
    const GRAPHJS_USE_GRAPHJS_LOGIN = 'graphjs_use_graphjs_login';

    const GRAPHJS_DEFAULT_THEME = "light";
    const GRAPHJS_DEFAULT_OVERRIDE_COMMENT = false;
    const GRAPHJS_DEFAULT_USE_GRAPHJS_LOGIN = false;

    private $pluginFile;
    private $pluginDirectory;
    private $graphjs;
    private $graphjsApi;

    /**
     * @param string $pluginFile
     * @param string $pluginDirectory
     * @param Graphjs $graphjs
     */
    public function __construct($pluginFile, $pluginDirectory, Graphjs $graphjs, GraphjsApi $graphjsApi)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginDirectory = $pluginDirectory;
        $this->graphjs = $graphjs;
        $this->graphjsApi = $graphjsApi;
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

        $useGraphjsLogin = get_option(self::GRAPHJS_USE_GRAPHJS_LOGIN);
        if ($useGraphjsLogin === false) {
            add_option(self::GRAPHJS_USE_GRAPHJS_LOGIN, self::GRAPHJS_DEFAULT_USE_GRAPHJS_LOGIN);
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

        add_action('login_head', function () {

            if (! $this->useGraphjsLogin()) {
                return;
            }

            global $error;

            if (! empty($_POST['log']) || ! empty($_POST['pwd'])) {
                return;
            }

            if (! empty($_POST['graphjs_username']) && empty($_POST['graphjs_password'])) {
                $error .= 'GraphJS Password is required';
            }
            if (! empty($_POST['graphjs_password']) && empty($_POST['graphjs_username'])) {
                $error .= 'GraphJS Username is required';
            }
        });

        add_filter('authenticate', [ $this, 'authenticate' ], 30, 3);

        add_action('login_form', function () {

            if (! $this->useGraphjsLogin()) {
                return;
            }

            $username = isset($_POST['graphjs_username']) ? $_POST['graphjs_username'] : '';
            $password = isset($_POST['graphjs_password']) ? $_POST['graphjs_password'] : '';
            echo <<<HTML
<p style="text-align: center;">
OR <br> <h3 style="text-align: center;">Login using GraphJS</h3>
</p>
<p>
    <label for="graphjs_username">
        GraphJS Username
        <br>
        <input type="text" id="graphjs_username" class="input" name="graphjs_username" value="$username" size="20">
    </label>
</p>
<p>
    <label for="graphjs_password">
        GraphJS Password
        <br>
        <input type="password" id="graphjs_password" class="input" name="graphjs_password" value="$password" size="20">
    </label>
</p>
HTML;
        });
    }

    public function authenticate($user, $username, $password)
    {
        // Default login is successful
        if ($user instanceof \WP_User) {
            return $user;
        }

        return $this->authenticateGraphjs($user);
    }

    public function authenticateGraphjs(\WP_Error $wpError)
    {
        if (! $this->useGraphjsLogin()) {
            return $wpError;
        }

        if (! empty($_POST['graphjs_username']) && empty($_POST['graphjs_password'])) {
            $wpError->add('graphjs_password', '<strong>ERROR</strong>: The GraphJS Password field is empty.');
        }
        if (! empty($_POST['graphjs_password']) && empty($_POST['graphjs_username'])) {
            $wpError->add('graphjs_username', '<strong>ERROR</strong>: The GraphJS Username field is empty.');
        }

        // check form data
        $isValid = ! empty($_POST['graphjs_username']) && ! empty($_POST['graphjs_password']);
        if (! $isValid) {
            return $wpError;
        }

        $username = $_POST['graphjs_username'];
        $password = $_POST['graphjs_password'];

        // get graphjs user id from API
        $graphjsUserId = $this->getGraphjsUserId($username, $password, $wpError);
        if ($graphjsUserId instanceof \WP_Error) {
            return $graphjsUserId;
        }

        // get user having graphjs user id
        $user = $this->getUserByGraphjsUserId($graphjsUserId);
        if ($user instanceof \WP_User) {
            return $user;
        }

        // get profile from API
        $profile = $this->getGraphjsUserProfile($graphjsUserId, $wpError);
        if ($profile instanceof \WP_Error) {
            return $profile;
        }

        $email = $profile['email'];

        // get user having same email as graphjs profile
        $user = get_user_by('email', $email);
        if ($user instanceof \WP_User) {
            if (($ret = $this->setGraphjsUserIdOfUser($user->ID, $graphjsUserId, $wpError)) instanceof \WP_Error) {
                return $ret;
            }
            return $user;
        }

        // check if user registration is enabled
        if (! $this->canUserRegister()) {
            return $wpError;
        }

        $newUsername = $this->getNewUsername($profile['username']);
        $newPassword = password_hash($this->randomPassword(10), PASSWORD_DEFAULT);
        $user = $this->registerUser($newUsername, $newPassword, $email, $wpError);
        if ($user instanceof \WP_Error) {
            return $user;
        }
        if (($ret = $this->setGraphjsUserIdOfUser($user->ID, $graphjsUserId, $wpError)) instanceof \WP_Error) {
            return $ret;
        }
        return $user;
    }

    /**
     * @param string username
     * @return string
     */
    public function getNewUsername($username = '')
    {
        // TODO: generate unique username
        return $username;
    }

    /**
     * @param int $length
     * @return string
     */
    public function randomPassword($length) {
        $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.
            '0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|';

        $str = '';
        $max = strlen($chars) - 1;

        for ($i=0; $i < $length; $i++)
            $str .= $chars[mt_rand(0, $max)];

        return $str;
    }

    /**
     * @param string $graphjsUserId
     * @return null|string
     */
    public function getUserByGraphjsUserId($graphjsUserId)
    {
        $users = get_users([
            'meta_key' => 'graphjs_user_id',
            'meta_value' => $graphjsUserId,
        ]);

        return empty($users) ? null : current($users);
    }

    /**
     * @param string $username
     * @param string $password
     * @param \WP_Error $wpError
     * @return string|\WP_Error
     */
    public function getGraphjsUserId($username, $password, \WP_Error $wpError)
    {
        $response = $this->graphjsApi->login([
            'username' => $username,
            'password' => $password,
        ]);

        if ($response instanceof \WP_Error) {
            $wpError->errors += $response->errors;
            return $wpError;
        }

        $apiResponse = json_decode(wp_remote_retrieve_body($response), true);

        if ($apiResponse['success'] === false) {
            $wpError->add('invalid_graphjs_credentials', $apiResponse['reason']);
            return $wpError;
        }

        return $apiResponse['id'];
    }

    /**
     * @param string $graphjsUserId
     * @param \WP_Error $wpError
     * @return array|\WP_Error
     */
    public function getGraphjsUserProfile($graphjsUserId, \WP_Error $wpError)
    {
        $response = $this->graphjsApi->getProfile([
            'id' => $graphjsUserId,
        ]);

        if ($response instanceof \WP_Error) {
            $wpError->errors += $response->errors;
            return $wpError;
        }

        $apiResponse = json_decode(wp_remote_retrieve_body($response), true);

        if ($apiResponse['success'] === false) {
            $wpError->add('invalid_graphjs_credentials', $apiResponse['reason']);
            return $wpError;
        }

        return $apiResponse['profile'];
    }

    /**
     * @param int $userId
     * @param string $graphjsUserId
     * @param \WP_Error $wpError
     * @return true|\WP_Error
     */
    public function setGraphjsUserIdOfUser($userId, $graphjsUserId, \WP_Error $wpError)
    {
        $metaId = add_user_meta($userId, 'graphjs_user_id', $graphjsUserId, true);
        if ($metaId === false) {
            $wpError->add('user_meta_assign_failed', 'Failed to assign user meta <strong>graphjs_user_id</strong>');
            return $wpError;
        }

        return true;
    }

    /**
     * @return \WP_User|\WP_Error
     */
    public function registerUser($username, $password, $email, \WP_Error $wpError)
    {
        // create new user
        $userId = wp_create_user($username, $password, $email);
        if ($userId instanceof \WP_Error) {
            $wpError->errors += $userId->errors;
            return $wpError;
        }

        return get_user_by('id', $userId);
    }

    public function my_custom_admin_head()
    {
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

    public function useGraphjsLogin()
    {
        return boolval(get_option(self::GRAPHJS_USE_GRAPHJS_LOGIN));
    }

    /**
     * @return bool
     */
    public function canUserRegister()
    {
        return boolval(get_option('users_can_register'));
    }

    public function registerSettings()
    {
        // Register allowed form fields of setting
        register_setting('graphjs_options', self::GRAPHJS_UUID, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_THEME, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_COLOR, 'strval');
        register_setting('graphjs_options', self::GRAPHJS_OVERRIDE_COMMENT, 'boolval');
        register_setting('graphjs_options', self::GRAPHJS_USE_GRAPHJS_LOGIN, 'boolval');
    }
}
