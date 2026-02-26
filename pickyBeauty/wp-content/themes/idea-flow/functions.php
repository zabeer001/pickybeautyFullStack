<?php

/**
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme Idea Flow for publication on WordPress.org
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

require_once get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

add_action('tgmpa_register', 'idea_flow_register_required_plugins', 0);
function idea_flow_register_required_plugins()
{
	$plugins = array(
		array(
			'name'      => 'Superb Addons',
			'slug'      => 'superb-blocks',
			'required'  => false,
		),
		array(
			'name'      => 'Booking for Appointments and Events Calendar',
			'slug'      => 'ameliabooking',
			'required'  => false,
		),
	);

	$config = array(
		'id'           => 'idea-flow',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => true,
		'message'      => '',
	);

	tgmpa($plugins, $config);
}


function idea_flow_pattern_styles()
{
	wp_enqueue_style('idea-flow-patterns', get_template_directory_uri() . '/assets/css/patterns.css', array(), filemtime(get_template_directory() . '/assets/css/patterns.css'));
	if (is_admin()) {
		global $pagenow;
		if ('site-editor.php' === $pagenow) {
			// Do not enqueue editor style in site editor
			return;
		}
		wp_enqueue_style('idea-flow-editor', get_template_directory_uri() . '/assets/css/editor.css', array(), filemtime(get_template_directory() . '/assets/css/editor.css'));
	}
}
add_action('enqueue_block_assets', 'idea_flow_pattern_styles');


add_theme_support('wp-block-styles');

// Removes the default wordpress patterns
add_action('init', function () {
	remove_theme_support('core-block-patterns');
});

// Register customer Idea Flow pattern categories
function idea_flow_register_block_pattern_categories()
{
	register_block_pattern_category(
		'header',
		array(
			'label'       => __('Header', 'idea-flow'),
			'description' => __('Header patterns', 'idea-flow'),
		)
	);
	register_block_pattern_category(
		'call_to_action',
		array(
			'label'       => __('Call To Action', 'idea-flow'),
			'description' => __('Call to action patterns', 'idea-flow'),
		)
	);
	register_block_pattern_category(
		'content',
		array(
			'label'       => __('Content', 'idea-flow'),
			'description' => __('Idea Flow content patterns', 'idea-flow'),
		)
	);
	register_block_pattern_category(
		'teams',
		array(
			'label'       => __('Teams', 'idea-flow'),
			'description' => __('Team patterns', 'idea-flow'),
		)
	);
	register_block_pattern_category(
		'banners',
		array(
			'label'       => __('Banners', 'idea-flow'),
			'description' => __('Banner patterns', 'idea-flow'),
		)
	);
	register_block_pattern_category(
		'layouts',
		array(
			'label'       => __('Layouts', 'idea-flow'),
			'description' => __('layout patterns', 'idea-flow'),
		)
	);
	register_block_pattern_category(
		'testimonials',
		array(
			'label'       => __('Testimonial', 'idea-flow'),
			'description' => __('Testimonial and review patterns', 'idea-flow'),
		)
	);

}

add_action('init', 'idea_flow_register_block_pattern_categories');





// Initialize information content
require_once trailingslashit(get_template_directory()) . 'inc/vendor/autoload.php';

use SuperbThemesThemeInformationContent\ThemeEntryPoint;

ThemeEntryPoint::init([
    'type' => 'block', // block / classic
    'theme_url' => 'https://superbthemes.com/idea-flow/',
    'demo_url' => 'https://superbthemes.com/demo/idea-flow/',
    'features' => array(
    	array(
    		'title' => __("Theme Designer", "idea-flow"),
    		'icon' => "lego-duotone.webp",
    		'description' => __("Choose from over 300 designs for footers, headers, landing pages & all other theme parts.", "idea-flow")
    	),
    	   	array(
    		'title' => __("Editor Enhancements", "idea-flow"),
    		'icon' => "1-1.png",
    		'description' => __("Enhanced editor experience, grid systems, improved block control and much more.", "idea-flow")
    	),
    	array(
    		'title' => __("Custom CSS", "idea-flow"),
    		'icon' => "2-1.png",
    		'description' => __("Add custom CSS with syntax highlight, custom display settings, and minified output.", "idea-flow")
    	),
    	array(
    		'title' => __("Animations", "idea-flow"),
    		'icon' => "wave-triangle-duotone.webp",
    		'description' => __("Animate any element on your website with one click. Choose from over 50+ animations.", "idea-flow")
    	),
    	array(
    		'title' => __("WooCommerce Integration", "idea-flow"),
    		'icon' => "shopping-cart-duotone.webp",
    		'description' => __("Choose from over 100 unique WooCommerce designs for your e-commerce store.", "idea-flow")
    	),
    	array(
    		'title' => __("Responsive Controls", "idea-flow"),
    		'icon' => "arrows-out-line-horizontal-duotone.webp",
    		'description' => __("Make any theme mobile-friendly with SuperbThemes responsive controls.", "idea-flow")
    	)
    )
]);
