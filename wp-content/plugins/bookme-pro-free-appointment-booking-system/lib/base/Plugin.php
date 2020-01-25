<?php
namespace BookmePro\Lib\Base;

use BookmePro\Lib;

/**
 * Class Plugin
 * @package BookmePro\Lib\Base
 */
abstract class Plugin
{
    /******************************************************************************************************************
     * Protected properties                                                                                           *
     ******************************************************************************************************************/

    /**
     * Prefix for options and metas.
     *
     * @staticvar string
     */
    protected static $prefix;

    /**
     * Plugin title.
     *
     * @staticvar string
     */
    protected static $title;

    /**
     * Plugin version.
     *
     * @staticvar string
     */
    protected static $version;

    /**
     * Plugin slug.
     *
     * @staticvar string
     */
    protected static $slug;

    /**
     * Path to plugin directory.
     *
     * @staticvar string
     */
    protected static $directory;

    /**
     * Path to plugin main file.
     *
     * @staticvar string
     */
    protected static $main_file;

    /**
     * Plugin basename.
     *
     * @staticvar string
     */
    protected static $basename;

    /**
     * Plugin text domain.
     *
     * @staticvar string
     */
    protected static $text_domain;

    /**
     * Root namespace of plugin classes.
     *
     * @staticvar string
     */
    protected static $root_namespace;

    /**
     *
     * @staticvar bool
     */
    protected static $embedded;

    /******************************************************************************************************************
     * Private properties                                                                                             *
     ******************************************************************************************************************/

    /**
     * Array of plugin classes for objects.
     *
     * @var static[]
     */
    private static $plugin_classes = array();

    /******************************************************************************************************************
     * Public methods                                                                                                 *
     ******************************************************************************************************************/

    /**
     * Start Bookme Pro plugin.
     */
    public static function run()
    {
        static::registerHooks();
        // Run updates.
        $updater_class = static::getRootNamespace() . '\Lib\Updater';
        $updater = new $updater_class();
        $updater->run();
    }

    /**
     * Activate plugin.
     *
     * @param bool $network_wide
     */
    public static function activate($network_wide)
    {
        if ($network_wide && has_action('bookme_pro_plugin_activate')) {
            do_action('bookme_pro_plugin_activate', static::getSlug());
        } else {
            $installer_class = static::getRootNamespace() . '\Lib\Installer';
            $installer = new $installer_class();
            $installer->install();
        }
    }

    /**
     * Deactivate plugin.
     *
     * @param bool $network_wide
     */
    public static function deactivate($network_wide)
    {
        if ($network_wide && has_action('bookme_pro_plugin_deactivate')) {
            do_action('bookme_pro_plugin_deactivate', static::getSlug());
        } else {
            unload_textdomain('bookme_pro');
        }
    }

    /**
     * Uninstall plugin.
     *
     * @param string|bool $network_wide
     */
    public static function uninstall($network_wide)
    {
        if ($network_wide !== false && has_action('bookme_pro_plugin_uninstall')) {
            do_action('bookme_pro_plugin_uninstall', static::getSlug());
        } else {
            $installer_class = static::getRootNamespace() . '\Lib\Installer';
            $installer = new $installer_class();
            $installer->uninstall();
        }
    }

    /**
     * Check if plugin is enabled (applicable to add-ons).
     *
     * @return bool
     */
    public static function enabled()
    {
        return get_option(static::getPrefix() . 'enabled') == 1;
    }

    /**
     * Enable plugin (applicable to add-ons).
     */
    public static function enable()
    {
        update_option(static::getPrefix() . 'enabled', 1);
    }

    /**
     * Disable plugin (applicable to add-ons).
     */
    public static function disable()
    {
        update_option(static::getPrefix() . 'enabled', 0);
    }

    /**
     * Get prefix.
     *
     * @return mixed
     */
    public static function getPrefix()
    {
        if (static::$prefix === null) {
            static::$prefix = str_replace(array('-addon', '-'), array('', '_'), static::getSlug()) . '_';
        }

        return static::$prefix;
    }

    /**
     * Get plugin title.
     *
     * @return string
     */
    public static function getTitle()
    {
        if (static::$title === null) {
            if (!function_exists('get_plugin_data')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $plugin_data = get_plugin_data(static::getMainFile());
            static::$version = $plugin_data['Version'];
            static::$title = $plugin_data['Name'];
            static::$text_domain = $plugin_data['TextDomain'];
        }

        return static::$title;
    }

    /**
     * Get plugin version.
     *
     * @return string
     */
    public static function getVersion()
    {
        if (static::$version === null) {
            if (!function_exists('get_plugin_data')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $plugin_data = get_plugin_data(static::getMainFile());
            static::$version = $plugin_data['Version'];
            static::$title = $plugin_data['Name'];
            static::$text_domain = $plugin_data['TextDomain'];
        }

        return static::$version;
    }

    /**
     * Get plugin slug.
     *
     * @return string
     */
    public static function getSlug()
    {
        if (static::$slug === null) {
            static::$slug = basename(static::getDirectory());
        }

        return static::$slug;
    }

    /**
     * Get path to plugin directory.
     *
     * @return string
     */
    public static function getDirectory()
    {
        if (static::$directory === null) {
            $reflector = new \ReflectionClass(get_called_class());
            static::$directory = dirname(dirname($reflector->getFileName()));
        }

        return static::$directory;
    }

    /**
     * Get path to plugin main file.
     *
     * @return string
     */
    public static function getMainFile()
    {
        if (static::$main_file === null) {
            static::$main_file = static::getDirectory() . '/init.php';
        }

        return static::$main_file;
    }

    /**
     * Get plugin basename.
     *
     * @return string
     */
    public static function getBasename()
    {
        if (static::$basename === null) {
            static::$basename = plugin_basename(static::getMainFile());
        }

        return static::$basename;
    }

    /**
     * Get plugin text domain.
     *
     * @return string
     */
    public static function getTextDomain()
    {
        if (static::$text_domain === null) {
            if (!function_exists('get_plugin_data')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $plugin_data = get_plugin_data(static::getMainFile());
            static::$version = $plugin_data['Version'];
            static::$title = $plugin_data['Name'];
            static::$text_domain = $plugin_data['TextDomain'];
        }

        return static::$text_domain;
    }

    /**
     * Get root namespace of called class.
     *
     * @return string
     */
    public static function getRootNamespace()
    {
        if (static::$root_namespace === null) {
            $called_class = get_called_class();
            static::$root_namespace = substr($called_class, 0, strpos($called_class, '\\'));
        }

        return static::$root_namespace;
    }

    /**
     * Get entity classes.
     *
     * @return Lib\Base\Entity[]
     */
    public static function getEntityClasses()
    {
        $result = array();

        $dir = static::getDirectory() . '/lib/entities';
        if (is_dir($dir)) {
            foreach (scandir($dir) as $filename) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }
                $result[] = static::getRootNamespace() . '\Lib\Entities\\' . basename($filename, '.php');
            }
        }

        return $result;
    }

    /**
     * Get plugin installation time.
     *
     * @return int
     */
    public static function getInstallationTime()
    {
        return get_option(static::getPrefix() . 'installation_time');
    }

    /**
     * Check whether the plugin is network active.
     *
     * @return bool
     */
    public static function isNetworkActive()
    {
        return is_plugin_active_for_network(static::getBasename());
    }

    /**
     * Get plugin class for given object.
     *
     * @param $object
     * @return static
     */
    public static function getPluginFor($object)
    {
        $class = get_class($object);

        if (!isset (self::$plugin_classes[$class])) {
            self::$plugin_classes[$class] = substr($class, 0, strpos($class, '\\')) . '\Lib\Plugin';
        }

        return self::$plugin_classes[$class];
    }

    /**
     * Check if add-on is embedded.
     *
     * @return bool
     */
    public static function embedded()
    {
        if (static::$embedded === null) {
            static::$embedded = strpos(static::getDirectory(), '/lib/addons/') > 0;
        }

        return static::$embedded;
    }

    /******************************************************************************************************************
     * Protected methods                                                                                              *
     ******************************************************************************************************************/

    /**
     * Register hooks.
     * @todo Change to protected.
     */
    public static function registerHooks()
    {
        /** @var Plugin $plugin_class */
        $plugin_class = get_called_class();

        if ($plugin_class::embedded()) {
            add_action('activate_' . Lib\Plugin::getBasename(), array($plugin_class, 'activate'), 99, 1);
            add_action('deactivate_' . Lib\Plugin::getBasename(), array($plugin_class, 'deactivate'), 99, 1);
        } else {
            register_activation_hook(static::getMainFile(), array($plugin_class, 'activate'));
            register_deactivation_hook(static::getMainFile(), array($plugin_class, 'deactivate'));
            register_uninstall_hook(static::getMainFile(), array($plugin_class, 'uninstall'));
        }

        add_action('plugins_loaded', function () use ($plugin_class) {
            // l10n.
            load_plugin_textdomain($plugin_class::getTextDomain(), false, $plugin_class::getSlug() . '/languages');
        });

        // Add handlers to Bookme Pro filters.
        add_filter('bookme_pro_plugins', function (array $plugins) use ($plugin_class) {
            $plugins[$plugin_class::getSlug()] = $plugin_class;
            return $plugins;
        });

        // WordPress daily cron
        if (!wp_next_scheduled('bookme_pro_daily_task')) {
            wp_schedule_event(time(), 'daily', 'bookme_pro_daily_task');
        }
    }

}