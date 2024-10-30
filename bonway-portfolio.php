<?php

include 'helpers/BspRenderHelper.php';
include 'helpers/BspMetaHelper.php';
include 'helpers/BspTextHelper.php';
/*
Plugin Name: Bonway Portfolio
Description: Create simple Portfolio entries, which can then be added to pages through shortcodes.
Version: 1.0.1
Author: Bonway Services
Author URI: https://www.bonway-services.nl
License: GPLv2 or later
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

Copyright 2019-2019 Bonway Services, Inc.
*/

/*
#==============================================================================#
[Basic hooks]
#==============================================================================#
 */
/**
 * Activation Hook for the module
 * @method bonway_portfolio_activation
 */
function bonway_portfolio_activation() {

}
register_activation_hook(__FILE__, 'bonway_portfolio_activation');

/**
 * Deactivation Hook for the module
 * @method bonway_portfolio_deactivation
 */
function bonway_portfolio_deactivation() {

}
register_deactivation_hook(__FILE__, 'bonway_portfolio_deactivation');

/**
 * Uninstall Hook for the module
 * @method bonway_portfolio_uninstall
 */
function bonway_portfolio_uninstall() {
    bonway_portfolio_deactivation();
}
register_uninstall_hook(__FILE__, 'bonway_portfolio_uninstall');

function bonway_portfolio_enqueue() {
    wp_enqueue_media();
    wp_enqueue_script("admin_js", plugins_url("js/admin.js", __FILE__));
}
add_action('admin_enqueue_scripts', 'bonway_portfolio_enqueue');

/*
#==============================================================================#
[Styling/Scripts of the plugin]
#==============================================================================#
*/

add_action('admin_enqueue_scripts', 'register_bonwaybsp_admin');
function register_bonwaybsp_admin($hook)
{
    $current_screen = get_current_screen();
    $screenId = $current_screen->id;

    if ($screenId === 'bonway-portfolio' || $screenId === 'edit-bonway-portfolio') {
        wp_enqueue_style('bonwaybsp_admin_style', plugins_url('style/admin.css',__FILE__ ));
        wp_enqueue_script("bonwaybsp_admin_js", plugins_url("js/admin.js", __FILE__), array('jquery'));
    }
}

add_action('wp_enqueue_scripts', 'register_bonwaybsp_global');
function register_bonwaybsp_global() {
    wp_enqueue_style('bonwaybsp_style', plugin_dir_url(__FILE__ ) . 'style/style.css');
}

/*
#==============================================================================#
[Post Types, Shortcodes, and Custom Columns]
#==============================================================================#
 */

/**
 * Registers a new posttype for the Bonway SBE
 * @method bonway_portfolio_post_type
 */
function bonway_portfolio_post_type()
{
    $labels = array(
        'name'          => __('Bonway Portfolios'),
        'singular_name' => __('Bonway Portfolio'),
 );

    $rewrite = array(
        'slug'  => 'bonway-portfolio'
 );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'menu_icon'             => plugin_dir_url(__FILE__ ) . 'images/bonway_logo_mini.png',
        'rewrite'               => $rewrite,
 );

    register_post_type('bonway-portfolio', $args);
}
add_action('init', 'bonway_portfolio_post_type');

/**
 * Registers the shortcode for the portfolio items
 * @method bonway_portfolio
 * @param  array               $atts    An array of attributes used for the shortcode
 * @param  string              $content Default NULL
 * @return Object                       Content of the selected block
 */
function bonway_portfolio($atts, $content=NULL){
    $atts = shortcode_atts(array(
        'id' => '',
        'identifier' => ''
  ), $atts, 'bonway_portfolio');
    $id = $atts['id'];
    $identifier = $atts['identifier'];

    return ("" !== $id) ? bonwaybsp_get_portfolio_by_id($id) : bonwaybsp_get_portfolio_by_identifier($identifier);
}
add_shortcode('bsp','bonway_portfolio');

/**
 * Initialize the custom columns for the module
 * @method bonway_portfolio_custom_columns
 * @param  array                   $columns Default param
 */
function bonway_portfolio_custom_columns($columns) {
    $columns['bonway_portfolio_identifier'] = "Identifier";
    $columns['bonway_portfolio_identifier_shortcode'] = "Identifier Shortcode";

    return $columns;
}
add_filter('manage_bonway-portfolio_posts_columns', 'bonway_portfolio_custom_columns');

/**
 * Insert data into custom columns
 * @method bonway_portfolio_custom_column
 * @param  array                  $column  Array of custom columns
 * @param  integer                $post_id ID of the post
 */
function bonway_portfolio_custom_column($column, $post_id) {
    switch ($column) {
        case 'bonway_portfolio_identifier' :
            $identifier = get_post_meta(get_the_ID(), 'bsp-identifier', true);
            echo (!empty($identifier)) ? $identifier : 'No identifier set';
            break;
        case 'bonway_portfolio_identifier_shortcode' :
            $identifier = get_post_meta(get_the_ID(), 'bsp-identifier', true);
            $printId = (!empty($identifier)) ? "[bsp identifier=&quot;" . $identifier . "&quot;]" : "No identifier set";
            $showCopy = (!empty($identifier)) ? "can-copy" : "";
            $copyMsg = "The shortcode <em>'" . $identifier . "'</em> has been copied succesfuly!";
            echo '<div class="' . $showCopy . ' bonwaybsp-inputcontainer"><div class="copy-btn js-bonwaybsp-copy-btn"></div><input class="js-bonwaybsp-shortcode readonly" value="' . $printId . '" readonly></input><div class="js-bonwaybsp-copy-msg copy-msg">' . $copyMsg . '</div></div>';
            break;
    }
}
add_action('manage_bonway-portfolio_posts_custom_column' , 'bonway_portfolio_custom_column', 10, 2);

/**
 * Returns an error based on session-data after a save
 * @method bonway_portfolio_custom_errors
 */
function bonway_portfolio_custom_errors() {
    if(isset($_SESSION) && array_key_exists('bonway_portfolio-error_identifier', $_SESSION)) {?>
        <div class="error notice notice-error is-dismissible">
            <p><?= $_SESSION['bonway_portfolio-error_identifier']; ?></p>
        </div><?php

        unset($_SESSION['bonway_portfolio-error_identifier']);
    }

    if(isset($_SESSION) && array_key_exists('bonway_portfolio-error_file', $_SESSION)) {?>
        <div class="error notice notice-error is-dismissible">
            <p><?= $_SESSION['bonway_portfolio-error_file']; ?></p>
        </div><?php

        unset($_SESSION['bonway_portfolio-error_file']);
    }
}
add_action('admin_notices', 'bonway_portfolio_custom_errors');