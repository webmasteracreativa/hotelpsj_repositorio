<?php
namespace BookmePro\Lib;


/**
 * Class Plugin
 * @package BookmePro\Lib
 */
abstract class Plugin extends Base\Plugin
{
    protected static $prefix = 'bookme_pro_';
    protected static $title;
    protected static $version;
    protected static $slug;
    protected static $directory;
    protected static $main_file;
    protected static $basename;
    protected static $text_domain;
    protected static $root_namespace;
    protected static $embedded;

    public static function registerHooks()
    {
        parent::registerHooks();
    }

    public static function run()
    {
        $dir = Plugin::getDirectory() . '/lib/addons/';
        if (is_dir($dir)) {
            foreach (glob($dir . 'bookme-pro-addon-*', GLOB_ONLYDIR) as $path) {
                include_once $path . '/init.php';
            }
        }

        parent::run();
    }

}