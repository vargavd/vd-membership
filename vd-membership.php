<?php
/*
Plugin Name: VD Membership
Plugin URI: https://github.com/vargavd/membership
Description: Manages members for termeszetvedok.hu
Version: 0.1
Requires PHP: 8.0
Author: vargavd
Author URI: https://github.com/vargavd
License: GPLv2 or later
Text Domain: vd-membership
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2026 vargavd
*/

require_once(plugin_dir_path(__FILE__) . 'inc/helper.php');
require_once(plugin_dir_path(__FILE__) . 'inc/disable-stuff.php');
require_once(plugin_dir_path(__FILE__) . 'inc/enqueue.php');

// redirect any page to login page
add_action('template_redirect', function () {
  if (!is_page('login')) {
    wp_redirect(wp_login_url(get_current_url()));
    exit;
  }
});