<?php
/**
 * Plugin Name: WP Robots Txt Version2
 * Plugin URI: https://github.com/ADARTA/WP-Robots-Txt-Version2
 * Description: Edit your robots.txt file from Settings->Reading WordPress Admin page
 * Version: 2.0.0
 * Text Domain: wp-robots-txt-v2
 * Author: ADARTA Software
 * Author URI: https://github.com/ADARTA
 * License: GPL-2.0+
 * Copyright 2014 by the contributors
 * Original work Copyright 2013 Christopher Davis <http://christopherdavis.me>
 *   published at https://github.com/chrisguitarguy/WP-Robots-Txt
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    WordPress
 * @package     WPRobotsTxtV2
 * @copyright   WP-Robots-Txt-Version2 - Copyright 2014 by the contributors
 * @license     http://opensource.org/licenses/GPL-2.0 GPL-2.0+
 */

!defined('ABSPATH') && exit;

define('WP_ROBOTS_TXT_DIR', plugin_dir_path(__FILE__));

require_once WP_ROBOTS_TXT_DIR . 'inc/core.php';
if (is_admin()) {
    require_once WP_ROBOTS_TXT_DIR . 'inc/options-page.php';
    CD_RDTE_Admin_Page::init();
}

add_filter('robots_txt', 'cd_rdte_filter_robots', 10, 2);
register_activation_hook(__FILE__, 'cd_rdte_activation');
register_deactivation_hook(__FILE__, 'cd_rdte_deactivation');
