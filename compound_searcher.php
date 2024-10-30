<?php
/*
Plugin Name: Compound Searcher
Plugin URI: http://tec.jpn.ph/comp/CompoundSearcher/
Description: Gets the searchboxes on posts to support operators like OR, minus(-) and parens.
Version: 0.1.4
Author: gnaka08
Author URI: http://tec.jpn.ph/
License: LGPL2 or later
*/
/**
 * @copyright 2013 gnaka08

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, see <http://www.gnu.org/licenses/>.

 */
declare(encoding='UTF-8');
namespace CompoundSearcher;
mb_internal_encoding("UTF-8");
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'SearchWordsSQL.php');


/* options */

add_action('admin_init', function() {
	add_settings_section(
		'compound_searcher_setting_section',
		__('Compound Searcher Options', 'compound_searcher_domain'),
		function () {
			echo '<p>' . __('Compound Searcher Option. The item will not be shown if you leave blank. Press down arrow to complete.', 'compound_searcher_domain') . '</p>';
		},
		'general'
	);

	add_settings_field(
		'compound_searcher_placeholder_text',
		__('Placeholder Text', 'compound_searcher_domain'),
		function () {
?>
	<input type="text" name="compound_searcher_placeholder_text" autocomplete="on" list="compound_searcher_placeholder_list" value="<?php echo get_option('compound_searcher_placeholder_text'); ?>">
	<datalist id="compound_searcher_placeholder_list">
		<option value="Compound Search ...">
		<option value="<?php _e('Compound Search ...', 'compound_searcher_domain'); ?>">
	</datalist>
<?php
		},
		'general',
		'compound_searcher_setting_section'
	);
	register_setting('general', 'compound_searcher_placeholder_text');

	add_settings_field(
		'compound_searcher_help_url',
		__('Help URL', 'compound_searcher_domain'),
		function () {
?>
	<input type="text" name="compound_searcher_help_url" autocomplete="on" list="compound_searcher_url_list" value="<?php echo get_option('compound_searcher_help_url'); ?>">
	<datalist id="compound_searcher_url_list">
		<option value="http://tec.jpn.ph/comp/CompoundSearcher/help/help.en.html">
		<option value="http://tec.jpn.ph/comp/CompoundSearcher/help/help.ja.html">
	</datalist>
<?php
		},
		'general',
		'compound_searcher_setting_section'
	);
	register_setting('general', 'compound_searcher_help_url');

});

add_filter('posts_search', function ($cond) {
	global $sb, $wp_query, $wpdb;

	$sb = new \SearchWordsSQL\SQLBuilder(
		"replace($wpdb->posts.post_title, ' ', '') LIKE replace(%s, ' ', '')  OR "
			. "replace($wpdb->posts.post_content, ' ', '') LIKE replace(%s, ' ', '')",
		function ($v) { $l = \SearchWordsSQL\SQLLikeValueCallback($v); return array($l, $l);}
	);

	try {
		if (!array_key_exists('s', $wp_query->query_vars)) return $cond;
		$res = $sb->Build($wp_query->query_vars['s']);
		$a = \SearchWordsSQL\array_flatten($res['value']);
		$cond = " AND " . $wpdb->prepare($res['SQL'], $a);
	} catch (\InvalidArgumentException $e) {
		error_log("Invalid query '" . $wp_query->query_vars['s'] . "'. Falling back to the standard search.");
	}

	return $cond;
});

/* set placeholder text and a help icon */
add_filter('get_search_form', function ($html) {
	$dom = new \DOMDocument();
	$dom->preserveWhiteSpace = false;

	$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
	$dom->loadHTML($html);
	$inputs = $dom->getElementsByTagName('input');
	for ($i = 0; $i < $inputs->length; $i++) {
		$input = $inputs->item($i);
		if ($input->getAttribute("name") == "s") {
			if ($ptext = get_option("compound_searcher_placeholder_text")) {
				$input->setAttribute("placeholder", $ptext);
			}
			if ($helpurl = get_option('compound_searcher_help_url')) {
				$script = $dom->createElement("script");
				$script->appendChild(
					$dom->createTextNode(<<<EOL
setHelpIconURL('$helpurl');
jQuery("input[name='s']")
	.bind('mouseover', function(e){HelpIconOnInputField(e);})
	.bind('mouseout', function(e){HelpIconOnInputField(e);});
EOL
					)
				);
				$input->parentNode->appendChild($script);
			}
		}
	}
	$html = "";
	$cbody =& $dom->getElementsByTagName('body')->item(0)->childNodes;
	for ($i = 0; $i < $cbody->length; $i++) {
		$html .= $dom->saveHTML($cbody->item($i));
	}
	return $html;
});
add_action('wp_enqueue_scripts', function () {
	$b = mb_substr(__DIR__, mb_strrpos(__DIR__, DIRECTORY_SEPARATOR) + 1);
	wp_enqueue_style(
		'compound_searcher_genericons_style',
//		plugins_url("genericons/genericons.css", __FILE__),	// this doesn't work if the plugin directory is linked to an external directory by a symbolic link.
		plugins_url("$b/genericons/genericons.css"),
		array(),
		"3.8"
	);
	wp_enqueue_style(
		'compound_searcher_style',
		plugins_url("$b/style.css")
	);
	wp_enqueue_script(
		'HelpIconOnInputField',
		plugins_url("$b/HelpIconOnInputField.js"),
		array( 'jquery' )
	);
});


?>