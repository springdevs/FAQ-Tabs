<?php
/*
Plugin Name: Product FAQ Tabs
Plugin URI: https://wordpress.org/plugins/sdevs-wc-faq-tabs
Description: Show frequently asked questions in a nice and organized fashion.
Version: 1.0.0
Author: SpringDevs
Author URI: https://springdevs.com/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: sdevs_faqtabs
Domain Path: /languages
*/

/**
 * Copyright (c) 2021 SpringDevs (email: contact@springdevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Sdevs_Custompft_Main class
 *
 * @class Sdevs_Custompft_Main The class that holds the entire Custompft_Main plugin
 */
final class Sdevs_Custompft_Main
{
    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the Custompft_Main class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct()
    {
        $this->define_constants();

        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initializes the Sdevs_Custompft_Main() class
     *
     * Checks for an existing Sdevs_Custompft_Main() instance
     * and if it doesn't find one, creates it.
     *
     * @return Sdevs_Custompft_Main|bool
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new Sdevs_Custompft_Main();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('SDEVS_CUSTOMPFT_VERSION', self::version);
        define('SDEVS_CUSTOMPFT_FILE', __FILE__);
        define('SDEVS_CUSTOMPFT_PATH', dirname(SDEVS_CUSTOMPFT_FILE));
        define('SDEVS_CUSTOMPFT_INCLUDES', SDEVS_CUSTOMPFT_PATH . '/includes');
        define('SDEVS_CUSTOMPFT_URL', plugins_url('', SDEVS_CUSTOMPFT_FILE));
        define('SDEVS_CUSTOMPFT_ASSETS', SDEVS_CUSTOMPFT_URL . '/assets');
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {
        if ($this->is_request('admin')) {
            $this->container['admin'] = new springdevs\custompft\Admin();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new springdevs\custompft\Frontend();
        }

        if ($this->is_request('ajax')) {
            $this->container['ajax'] = new springdevs\custompft\Ajax();
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {
        add_action('init', [$this, 'init_classes']);

        // Localize our plugin
        add_action('init', [$this, 'localization_setup']);
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {
        if ($this->is_request('ajax')) {
            // $this->container['ajax'] =  new Nirab\custompft\Ajax();
        }
        $this->container['assets'] = new springdevs\custompft\Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('sdevs_faqtabs', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined('DOING_AJAX');

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }
} // Custompft_Main

/**
 * Initialize the main plugin
 *
 * @return \Sdevs_Custompft_Main|bool
 */
function sdevs_custompft_main()
{
    return Sdevs_Custompft_Main::init();
}

/**
 *  kick-off the plugin
 */
sdevs_custompft_main();
