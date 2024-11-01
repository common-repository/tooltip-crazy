<?php
/**
 * Plugin Name: Tooltip Crazy
 * Plugin URI: http://tympanus.net/codrops/2014/10/07/tooltip-styles-inspiration/
 * Description: Tooltip based on the ideas from Manoela Ilic (http://tympanus.net/codrops/2014/10/07/tooltip-styles-inspiration/)
 * Version: 1.1.2
 * Author: Felix Welberg
 * Author URI: http://www.felixwelberg.de
 * Text Domain:
 * Domain Path:
 * Network:
 * License: http://tympanus.net/codrops/licensing/
 * 
 * Copyright 2015 Felix Welberg (email: felix@welberg.de)
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
 */
defined('ABSPATH') or die("No script kiddies please!");

// Add styles and scripts
function tooltipcrazy_scripts() {
    wp_enqueue_style('normalize', plugins_url('tooltips/css/normalize.css', __FILE__));
    wp_enqueue_style('tooltip-classic', plugins_url('tooltips/css/tooltip-classic.css', __FILE__));
    wp_enqueue_style('tooltip-bloated', plugins_url('tooltips/css/tooltip-bloated.css', __FILE__));
    wp_enqueue_style('tooltip-box', plugins_url('tooltips/css/tooltip-box.css', __FILE__));
    wp_enqueue_style('tooltip-sharp', plugins_url('tooltips/css/tooltip-sharp.css', __FILE__));
    wp_enqueue_style('tooltip-line', plugins_url('tooltips/css/tooltip-line.css', __FILE__));
//    wp_enqueue_script('script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'tooltipcrazy_scripts');

// Add backend CSS
function backend_scripts() {
    wp_enqueue_style('tooltipcrazy-backend-css', plugins_url('style.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'backend_scripts');

// Add button for TinyMCE
function tooltipcrazy_register_buttons($buttons) {
    array_push($buttons, 'tooltipcrazy');
    return $buttons;
}

add_filter('mce_buttons', 'tooltipcrazy_register_buttons');

function tooltipcrazy_register_tinymce_javascript($plugin_array) {
    $plugin_array['tooltipcrazy'] = plugins_url('/js/tinymce.js', __file__);
    return $plugin_array;
}

add_filter('mce_external_plugins', 'tooltipcrazy_register_tinymce_javascript');

// Add shortcodes
function tooltipcrazy_func($atts, $content = null) {
    $attr = shortcode_atts(array(
        'text' => '',
        'image' => '',
        'layout' => '',
        'effect' => '',
        'target' => '',
        'link' => '',
            ), $atts);

    if (!empty($attr['text'])) {
        $tc_text = $attr['text'];
    }

    if (!empty($attr['link'])) {
        if (!empty($attr['target'])) {
            $tc_link_target = ' target="' . $attr['target'] . '"';
        } else {
            $tc_link_target = '';
        }
        $tc_link = '<a href="' . $attr['link'] . '"' . $tc_link_target . '>';
        $tc_link_end = '</a>';
    }

    if (!empty($attr['image'])) {
        $tc_image = '<img src="' . $attr['image'] . '" />';
    }

    if (!empty($attr['layout'])) {
        $tc_layout = $attr['layout'];
    } else if (get_option('tooltipcrazy_default_layout')) {
        $tc_layout = get_option('tooltipcrazy_default_layout');
    } else {
        $tc_layout = 'classic';
    }

    if (!empty($attr['effect'])) {
        $tc_effect = 'effect-' . $attr['effect'];
    } else if (get_option('tooltipcrazy_default_effect')) {
        $tc_effect = 'effect-' . get_option('tooltipcrazy_default_effect');
    } else {
        $tc_effect = 'effect-1';
    }

    switch ($tc_layout) {
        case "classic":
            return '<span class="tooltip-classic tooltip-classic-' . $tc_effect . '"><span class="tooltip-classic-item">' . $content . '</span>' . $tc_link . '<span class="tooltip-classic-content">' . $tc_image . '<span class="tooltip-classic-text">' . $tc_text . '</span></span>' . $tc_link_end . '</span>';
        case "bloated":
            return '<span class="tooltip-bloated">' . $content . $tc_link . '<span class="tooltip-bloated-content">' . $tc_image . $tc_text . '</span>' . $tc_link_end . '</span>';
        case "box":
            return '<span class="tooltip-box"><span class="tooltip-box-item">' . $content . '</span>' . $tc_link . '<span class="tooltip-box-content clearfix">' . $tc_image . '<span class="tooltip-box-text">' . $tc_text . '</span></span>' . $tc_link_end . '</span>';
        case "sharp":
            return '<span class="tooltip-sharp tooltip-sharp-turnleft">' . $tc_link . '<span class="tooltip-sharp-item">' . $content . '</span><span class="tooltip-sharp-content">' . $tc_text . '</span>' . $tc_link_end . '</span>';
        case "line":
            return '<span class="tooltip-line">' . $tc_link . $content . '<span class="tooltip-line-content"><span class="tooltip-line-text"><span class="tooltip-line-inner">' . $tc_text . '</span></span></span>' . $tc_link_end . '</span>';
    }
}

add_shortcode('tooltip', 'tooltipcrazy_func');

register_activation_hook(__FILE__, 'tooltipcrazy_install');

register_deactivation_hook(__FILE__, 'tooltipcrazy_remove');

function tooltipcrazy_install() {
    add_option("tooltipcrazy_default_layout", 'classic', '', 'yes');
    add_option("tooltipcrazy_default_effect", '1', '', 'yes');
}

function tooltipcrazy_remove() {
    delete_option('tooltipcrazy_default_layout');
    delete_option('tooltipcrazy_default_effect');
}

if (is_admin()) {
    add_action('admin_menu', 'tooltipcrazy_option_page');

    function tooltipcrazy_option_page() {
        add_options_page('Tooltip Crazy', 'Tooltip Crazy', 'administrator', 'tooltipcrazy', 'tooltipcrazy_option_page_html');
    }

}

function tooltipcrazy_option_page_html() {
    ?>
    <div class="wrap">
        <h2>Tooltip Crazy</h2>
        <form method="post" action="options.php">
            <?php wp_nonce_field('update-options'); ?>
            <table class="form-table">
                <tr>
                    <th>Default layout:</th>
                    <td>
                        <select name="tooltipcrazy_default_layout" id="tooltipcrazy_default_layout">
                            <option value="classic" <?php
                            if (get_option('tooltipcrazy_default_layout') == 'classic') {
                                echo 'selected="selected"';
                            }
                            ?>>Classic</option>
                            <option value="bloated" <?php
                            if (get_option('tooltipcrazy_default_layout') == 'bloated') {
                                echo 'selected="selected"';
                            }
                            ?>>Bloated</option>
                            <option value="box" <?php
                            if (get_option('tooltipcrazy_default_layout') == 'box') {
                                echo 'selected="selected"';
                            }
                            ?>>Box</option>
                            <option value="sharp" <?php
                            if (get_option('tooltipcrazy_default_layout') == 'sharp') {
                                echo 'selected="selected"';
                            }
                            ?>>Sharp</option>
                            <option value="line" <?php
                            if (get_option('tooltipcrazy_default_layout') == 'line') {
                                echo 'selected="selected"';
                            }
                            ?>>Line</option>
                        </select>
                </tr>
                <tr>
                    <th>Default effect:</th>
                    <td>
                        <select name="tooltipcrazy_default_effect" id="tooltipcrazy_default_effect">
                            <option value="1" <?php
                            if (get_option('tooltipcrazy_default_effect') == '1') {
                                echo 'selected="selected"';
                            }
                            ?>>Fade</option>
                            <option value="2" <?php
                            if (get_option('tooltipcrazy_default_effect') == '2') {
                                echo 'selected="selected"';
                            }
                            ?>>Appear</option>
                            <option value="3" <?php
                            if (get_option('tooltipcrazy_default_effect') == '3') {
                                echo 'selected="selected"';
                            }
                            ?>>Flip + Fade</option>
                            <option value="4" <?php
                            if (get_option('tooltipcrazy_default_effect') == '4') {
                                echo 'selected="selected"';
                            }
                            ?>>Pop</option>
                            <option value="5" <?php
                            if (get_option('tooltipcrazy_default_effect') == '5') {
                                echo 'selected="selected"';
                            }
                            ?>>Flip 90°</option>
                        </select>
                </tr>
            </table>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="tooltipcrazy_default_layout,tooltipcrazy_default_effect" />
            <p>
                <input type="submit" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>
    <?php
}
?>