<?php
/**
 * Plugin Name:     WooCommerce Timed Vouchers
 * Description:     This plugin adds a new post type with time limited vouchers
 * Author:          Doede Jaarsma communicatie
 * Author URI:      https://doedejaarsma.nl
 * Text Domain:     woocommerce-timed-vouchers
 * Domain Path:     /languages
 * Version:         0.1.0
 * Requires at least: 5.4
 * Requires PHP: 7.2
 *
 * @package         WoocommerceTimedVouchers
 */

use WooCommerceTimedVouchers\Installer;
use WooCommerceTimedVouchers\TimedVouchers;

defined('ABSPATH') || exit;

defined('WOOTV_FILE') || define('WOOTV_FILE', __FILE__);
defined('WOOTV_DIR') || define('WOOTV_DIR', __DIR__);
require_once WOOTV_DIR . '/vendor/autoload.php';

register_activation_hook(__FILE__, [Installer::class, 'install']);

add_action('woocommerce_loaded', [TimedVouchers::class, 'bootstrap']);
