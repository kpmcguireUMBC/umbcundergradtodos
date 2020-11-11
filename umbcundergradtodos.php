<?php
/**
 * Plugin Name:     UMBC Undergraduate Admissions Todos
 * Description:     Display todo list items
 * Version:         0.1.0
 * Author:          Kevin McGuire
 * License:         GPL-2.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     umbcundergradtodos
 */

function umbcundergrad_todos_block_init()
{
	$dir = dirname(__FILE__);

	$script_asset_path = "$dir/build/index.asset.php";
	if (!file_exists($script_asset_path)) {
		throw new Error('You need to run `npm start` or `npm run build` for the "umbcundergrad/todos" block first.');
	}
	$index_js = 'build/index.js';

	$script_asset = require $script_asset_path;
	wp_register_script('umbcundergrad_todos_block_editor_js', plugins_url($index_js, __FILE__), $script_asset['dependencies'], $script_asset['version']);
	wp_set_script_translations('umbcundergrad_todos_block_editor_js', 'umbcundergradtodos');

	$editor_css = 'build/style-index.css';
	wp_register_style('umbcundergrad_todos_block_editor_css', plugins_url($editor_css, __FILE__), [], filemtime("$dir/$editor_css"));

	if (!function_exists('register_block_type')) {
		// Gutenberg is not active.
		return;
	}

	register_block_type('umbcundergrad/todos', [
		'editor_script' => 'umbcundergrad_todos_block_editor_js',
		'editor_style' => 'umbcundergrad_todos_block_editor_css',
		'style' => 'umbcundergrad_todos_block_editor_css',
	]);
}
add_action('init', 'umbcundergrad_todos_block_init');

add_action('init', 'create_todos_category');

function create_todos_category()
{
	$labels = [
		'name' => _x('Todos Category', 'taxonomy general name'),
		'singular_name' => _x('Todos Category', 'taxonomy singular name'),
		'search_items' => __('Search Todos Categories'),
		'popular_items' => __('Popular Todos Categories'),
		'all_items' => __('All Todos Categories'),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __('Edit Todos Categories'),
		'update_item' => __('Update Todos Category'),
		'add_new_item' => __('Add New Todos Categories'),
		'new_item_name' => __('New Todos Categories'),
		'separate_items_with_commas' => __('Separate Todos Categories with commas'),
		'add_or_remove_items' => __('Add or remove Todos Categories'),
		'choose_from_most_used' => __('Choose from the most used Todos Categories'),
		'menu_name' => __('Todos Categories'),
	];

	register_taxonomy('todos_categories', 'page', [
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_rest' => true,
		'show_admin_column' => false,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => ['slug' => 'todos_categories'],
	]);

	$default_todos_categories = ["First Year", "Transfer", "Shady Grove", "Living on Campus"];

	foreach ($default_todos_categories as $cat) {
		$slug_style = strtolower($cat);

		$parent_term = term_exists($slug_style, 'todos_categories');

		if (!$parent_term) {
			$parent_term_id = $parent_term['term_id'];

			wp_insert_term(
				$cat, // the term
				'todos_categories', // the taxonomy
				[
					'description' => $cat,
					'slug' => $slug,
					'parent' => $parent_term_id,
				]
			);
		}
	}
}

// add_filter('jetpack_gutenberg', '__return_false');

add_filter('template_include', 'todo_list_template', 99);

function todo_list_template($template)
{
	$file_name = 'todo-list-template.php';

	if (is_page('Todos')) {
		if (locate_template($file_name)) {
			$template = locate_template($file_name);
		} else {
			$template = dirname(__FILE__) . '/templates/' . $file_name;
		}
	}

	load_template($template, false);

	return $template;
}
