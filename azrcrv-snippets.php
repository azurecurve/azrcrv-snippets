<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Snippets
 * Description: Allows snippets of HTML, PHP, JavaScript and CSS to be created; an alternative to using a functions.php file.
 * Version: 2.1.1
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/azrcrv-snippets/
 * Text Domain: snippets
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname( __FILE__).'/pluginmenu/menu.php');
add_action('admin_init', 'azrcrv_create_plugin_menu_s');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// add actions
add_action('admin_menu', 'azrcrv_s_create_admin_menu');
add_action('admin_post_azrcrv_s_save_options', 'azrcrv_s_save_options');
add_action('plugins_loaded', 'azrcrv_s_load_languages');
add_action('wp_enqueue_scripts', 'azrcrv_s_load_css_javascript_php', 10);
add_action('init', 'azrcrv_s_create_custom_post_type');
add_action('add_meta_boxes', 'azrcrv_s_add_meta_box');
add_action('save_post', 'azrcrv_s_save_meta_box');
add_action( 'admin_init', 'azcrcrv_s_init' );
function azcrcrv_s_init() {
    add_action( 'before_delete_post', 'azrcrv_s_delete_postmeta', 10 );
}

// add filters
add_filter('plugin_action_links', 'azrcrv_s_add_plugin_action_link', 10, 2);
//add_filter('the_posts', 'azrcrv_s_check_for_shortcode', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_s_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_s_custom_image_url');

// add shortcodes
add_shortcode('snippet', 'azrcrv_s_output_snippet_shortcode');
add_shortcode('SNIPPET', 'azrcrv_s_output_snippet_shortcode');

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_load_languages() {
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('snippets', false, $plugin_rel_path);
}

/**
 * Check if shortcode on current page and then load css and jqeury.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_check_for_shortcode($posts){
    if (empty($posts)){
        return $posts;
	}
	
	// array of shortcodes to search for
	$shortcodes = array(
						'snippets',
						);
	
    // loop through posts
    $found = false;
    foreach ($posts as $post){
		// loop through shortcodes
		foreach ($shortcodes as $shortcode){
			// check the post content for the shortcode
			if (has_shortcode($post->post_content, $shortcode)){
				$found = true;
				// break loop as shortcode found in page content
				break 2;
			}
		}
	}
 
    if ($found){
		// as shortcode found call functions to load css and javascript
        azrcrv_s_load_css();
    }
    return $posts;
}

/**
 * Load plugin css.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_load_css(){
	wp_enqueue_style('azrcrv-s', plugins_url('assets/css/style.css', __FILE__));
}

/**
 * Get options including defaults.
 *
 * @since 2.0.0
 *
 */
function azrcrv_s_get_option($option_name){
	
	$upload_dir = wp_upload_dir();
 
	$defaults = array(
						'snippet-folder' => trailingslashit($upload_dir['basedir']).'snippets/',
						'snippet-url' => trailingslashit($upload_dir['baseurl']).'snippets/',
						'advanced-mode' => 0,
					);

	$options = get_option($option_name, $defaults);

	$options = wp_parse_args($options, $defaults);

	return $options;

 }

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.admin_url('admin.php?page=azrcrv-s').'"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'snippets').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_create_admin_menu(){
	
	// add settings to snippets submenu
	add_submenu_page(
						'edit.php?post_type=snippet'
						,esc_html__('Snippets Settings', 'snippets')
						,esc_html__('Settings', 'snippets')
						,'manage_options'
						,'azrcrv-s'
						,'azrcrv_s_display_options'
					);
	
	// add settings to azurecurve menu
	add_submenu_page(
						"azrcrv-plugin-menu"
						,esc_html__("Snippets Settings", "snippets")
						,esc_html__("Snippets", "snippets")
						,'manage_options'
						,'azrcrv-s'
						,'azrcrv_s_display_options'
					);
}

/**
 * Custom plugin image path.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_custom_image_path($path){
    if (strpos($path, 'azrcrv-snippets') !== false){
        $path = plugin_dir_path(__FILE__).'assets/pluginimages';
    }
    return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_custom_image_url($url){
    if (strpos($url, 'azrcrv-snippets') !== false){
        $url = plugin_dir_url(__FILE__).'assets/pluginimages';
    }
    return $url;
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_display_options(){
	if (!current_user_can('manage_options')){
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'snippets'));
    }
	
	// Retrieve plugin configuration options from database
	$options = azrcrv_s_get_option('azrcrv-s');
	?>
	<div id="azrcrv-n-general" class="wrap">
		<fieldset>
			<h1>
				<?php
					echo '<a href="https://development.azurecurve.co.uk/classicpress-plugins/"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
					esc_html_e(get_admin_page_title());
				?>
			</h1>
			<?php if(isset($_GET['settings-updated'])){ ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('Settings have been saved.', 'snippets'); ?></strong></p>
				</div>
			<?php } ?>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="azrcrv_s_save_options" />
				<input name="page_options" type="hidden" value="snippet-folder,snippet-url,advanced-mode" />
				
				<!-- Adding security through hidden referrer field -->
				<?php wp_nonce_field('azrcrv-s', 'azrcrv-s-nonce'); ?>
				<table class="form-table">
				
					<tr><td colspan="2">
						<?php esc_html_e('<p>Allows snippets of HTML, PHP, JavaScript and CSS to be created; an alternative to using a functions.php file.</p>
						
<p>Snippets can be used to create re-usable HTML or JavaScript snippets or to create PHP to add_actions or add_filters without needing to add them to the functions.php file or create a plugin.</p>

<p>The following types of snippet can be created:</p>
<p>- HTML - can be loaded using the shortcode.<br />
- Internal CSS - automatically added as internal stylesheet.<br />
- CSS Stylesheet - automatically loaded.<br />
- Internal JavaScript - automatically added as internal JavaScript.<br />
- JavaScript File - automatically loaded.<br />
- PHP - can be loaded using the shortcode.<br />
- PHP File - automatically loaded.</p>

<p>Shortcode usage is either <strong>[snippet id=1013]</strong> (where the supplied id value is a snippet post_id) or <strong>[snippet slug="hello-world"]</strong>.</p>

<p>All snippets are loaded only on the site frontend; this protects the admin dashboard from white screen errors caused by badly formed PHP.</p>'
						, 'snippets'); ?>
					</td></tr>
					
					<tr><th scope="row"><label for="snippet-folder"><?php esc_html_e('Snippet Folder', 'snippets'); ?></label></th><td>
						<input name="snippet-folder" type="text" id="snippet-folder" value="<?php if (strlen($options['snippet-folder']) > 0){ echo stripslashes($options['snippet-folder']); } ?>" class="large-text" />
						<p class="description" id="snippet-folder-description"><?php esc_html_e('Specify the snippet folder where files should be created; if the folder does not exist, it will be created with 0777 permissions.', 'snippets'); ?></p></td>
					</td></tr>
							
							<tr><th scope="row"><label for="snippet-url"><?php esc_html_e('Snippet URL', 'snippets'); ?></label></th><td>
								<input name="url" type="text" id="snippet-url" value="<?php if (strlen($options['snippet-url']) > 0){ echo stripslashes($options['snippet-url']); } ?>" class="large-text" />
								<p class="description" id="snippet-url-description"><?php esc_html_e('Specify the URL for the snippets folder.', 'snippets'); ?></p></td>
							</td></tr>
					
					<tr>
						<th scope="row">
							<label for="advanced-mode">
								<?php esc_html_e('Enable advanced mode', 'snippets'); ?>
							</label>
						</th>
						<td>
							<label for="advanced-mode"><input name="advanced-mode" type="checkbox" id="advanced-mode" value="1" <?php checked('1', $options['advanced-mode']); ?> /><?php esc_html_e('Enable advanced mode?', 'snippets'); ?></label>
							<p class="description"><?php esc_html_e('Advanced mode will allow the creation of PHP snippets and files which are executed on the front-end only; great care should be taken as malformed PHP will white-screen the site.', 'snippets'); ?></p>
						</td>
					</tr>
				
				</table>
				<input type="submit" value="Save Changes" class="button-primary"/>
			</form>
		</fieldset>
	</div>
	<?php
}

/**
 * Check if function active (included due to standard function failing due to order of load).
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_is_plugin_active($plugin){
    return in_array($plugin, (array) get_option('active_plugins', array()));
}

/**
 * Save settings.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_save_options(){
	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'snippets'));
	}
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-s', 'azrcrv-s-nonce')){
	
		// Retrieve original plugin options array
		$options = get_option('azrcrv-s');
		
		$option_name = 'snippet-folder';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		if (!file_exists(sanitize_text_field($_POST[$option_name]))){
			mkdir(sanitize_text_field($_POST[$option_name]), 0777, true);
		}
			
		$option_name = 'snippet-url';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_url($_POST[$option_name]);
		}
		
		$option_name = 'advanced-mode';
		if (isset($_POST[$option_name])){
			$options[$option_name] = 1;
		}else{
			$options[$option_name] = 0;
		}
				
		// Store updated options array to database
		update_option('azrcrv-s', $options);
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-s&settings-updated', admin_url('admin.php')));
		exit;
	}
}

/**
 * Add inline CSS.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_load_css_javascript_php(){
	
	$options = azrcrv_s_get_option('azrcrv-s');
	$snippet_folder = trailingslashit($options['snippet-folder']);
	$snippet_url = trailingslashit($options['snippet-url']);
		
	global $wpdb;
	
	$querystr = "
		SELECT
			posts.post_content
			,posts.ID
		FROM
			$wpdb->posts as posts
		INNER JOIN
			$wpdb->postmeta as postmeta
				ON
					posts.ID = postmeta.post_id
				AND
					postmeta.meta_key = 'azrcrv_s_metafields'
		WHERE
			posts.post_status = 'publish'
		AND
			posts.post_type = 'snippet'
		ORDER BY
			posts.post_date DESC
	";
	
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	$custom_css = '';
	$custom_javascript = '';
	foreach ($pageposts as $post){
		$post_meta = get_post_meta($post->ID, 'azrcrv_s_metafields', true);
		if ($post_meta['snippet-type'] == 'CSS'){
			$custom_css .= stripslashes($post->post_content);
		}elseif ($post_meta['snippet-type'] == 'JavaScript'){
			$custom_javascript .= stripslashes($post->post_content);
		}elseif ($post_meta['snippet-type'] == 'JavaScript File'){
			//wp_enqueue_script('azrcrv-s-'.$post->ID, $snippet_folder.'snippet-'.$post->ID.'.js', array('jquery'), '3.9.1');
			wp_enqueue_script('azrcrv-s-'.$post->ID, $snippet_url.'snippet-'.$post->ID.'.js', array('jquery'), '3.9.1');
		}elseif ($post_meta['snippet-type'] == 'CSS Stylesheet'){
			wp_enqueue_style('azrcrv-s-'.$post->ID, $snippet_url.'snippet-'.$post->ID.'.css', '', '1.0.0');
		}elseif ($post_meta['snippet-type'] == 'PHP Function'){
			include_once ($snippet_folder.'snippet-'.$post->ID.'.php');
		}
	}
	if ($custom_css != ''){
		wp_enqueue_style('azrcrv-s', plugins_url('assets/css/style.css', __FILE__), '', '1.0.0');
		wp_add_inline_style('azrcrv-s', $custom_css);
	}
	if ($custom_javascript != ''){
		wp_enqueue_script('azrcrv-s', plugins_url('assets/scripts/scripts.js', __FILE__), array('jquery'), '3.9.1');
		wp_add_inline_script('azrcrv-s', $custom_javascript);
	}
}

/**
 * Create custom snippet post type.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_create_custom_post_type(){
	register_post_type('snippet',
		array(
				'labels' => array(
									'name' => esc_html__('Snippets', 'azrcrv-s'),
									'singular_name' => esc_html__('Snippet', 'azrcrv-s'),
									'add_new' => esc_html__('Add New', 'azrcrv-s'),
									'add_new_item' => esc_html__('Add New Snippet', 'azrcrv-s'),
									'edit' => esc_html__('Edit', 'azrcrv-s'),
									'edit_item' => esc_html__('Edit Snippet', 'azrcrv-s'),
									'new_item' => esc_html__('New Snippet', 'azrcrv-s'),
									'view' => esc_html__('View', 'azrcrv-s'),
									'view_item' => esc_html__('View Snippet', 'azrcrv-s'),
									'search_items' => esc_html__('Search Snippet', 'azrcrv-s'),
									'not_found' => esc_html__('No Snippet found', 'azrcrv-s'),
									'not_found_in_trash' => esc_html__('No Snippet found in Trash', 'azrcrv-s'),
									'parent' => esc_html__('Parent Snippet', 'azrcrv-s')
								),
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'menu_position' => 50,
			'supports' => array('title', 'revisions', 'editor'),
			'taxonomies' => array(''),
			'menu_icon' => plugins_url('assets/pluginimages/snippets.svg', __FILE__),
			'has_archive' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => false,
			'show_in_rest' => false,
		)
	);
}

/**
 * Add meta box.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_add_meta_box(){
	add_meta_box(
		'azrcrv_s_meta_box', // $id
		'Snippets', // $title
		'azrcrv_s_show_meta_box', // $callback
		'snippet', // $screen
		'normal', // $context
		'high' // $priority
	);
}


/**
 * Show meta box.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_show_meta_box(){
	global $post;  
	
	$meta_fields = get_post_meta($post->ID, 'azrcrv_s_metafields', true);
	
	$options = azrcrv_s_get_option('azrcrv-s');
	
	?>

	<input type="hidden" name="azrcrv_s_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>">

	<p>
		<label for="azrcrv_s_metafields[snippet-type]"><?php esc_html_e('Snippet Type', 'snippets'); ?></label>
		&nbsp;&nbsp;&nbsp;
		<select name="azrcrv_s_metafields[snippet-type]" style="width: 300px;">
		<?php
			echo "<option value='HTML' ".selected($meta_fields['snippet-type'], 'HTML').">".esc_html__('HTML', 'snippets')."</option>";
			echo "<option value='CSS' ".selected($meta_fields['snippet-type'], 'CSS').">".esc_html__('Internal CSS', 'snippets')."</option>";
			echo "<option value='CSS Stylesheet' ".selected($meta_fields['snippet-type'], 'CSS Stylesheet').">".esc_html__('CSS Stylesheet', 'snippets')."</option>";
			if ($options['advanced-mode'] == 1){
				echo "<option value='JavaScript' ".selected($meta_fields['snippet-type'], 'JavaScript').">".esc_html__('Internal JavaScript', 'snippets')."</option>";
				echo "<option value='JavaScript File' ".selected($meta_fields['snippet-type'], 'JavaScript File').">".esc_html__('JavaScript File', 'snippets')."</option>";
				echo "<option value='PHP' ".selected($meta_fields['snippet-type'], 'PHP').">".esc_html__('PHP', 'snippets')."</option>";
				echo "<option value='PHP Function' ".selected($meta_fields['snippet-type'], 'PHP Function').">".esc_html__('PHP File', 'snippets')."</option>";
			}
		?>
		</select>
	</p>

<?php

}

/**
 * Save meta box.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_save_meta_box($post_id){
	
	$options = azrcrv_s_get_option('azrcrv-s');
	$snippet_folder = trailingslashit($options['snippet-folder']);
	
	// verify nonce
	if (!isset($_POST['azrcrv_s_meta_box_nonce']) OR !wp_verify_nonce($_POST['azrcrv_s_meta_box_nonce'], basename(__FILE__))){
		return $post_id; 
	}
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return $post_id;
	}
	// check permissions
	if (!current_user_can('edit_page', $post_id)){
		return $post_id;
	} elseif (!current_user_can('edit_post', $post_id)){
		return $post_id;
	}
	
	$post = get_post($post_id);
	
	if ('publish' == $post->post_status){
		
		$old = get_post_meta($post_id, 'azrcrv_s_metafields', true);
		$new = $_POST['azrcrv_s_metafields'];
			
		// CSS Stylesheet
		if ($_POST['azrcrv_s_metafields']['snippet-type'] == 'CSS Stylesheet'){
			if ($old){
			$file_id = $snippet_folder.'snippet-'.$post_id.'.css';
			}else{
			$file_id = $snippet_folder.'snippet-'.($post_id -1).'.css';
			}
			
			$file = fopen($file_id,"w");
			
			$post = get_post($post_id);
			
			$post_content = $post->post_content;
			
			fwrite($file,$post_content);
			fclose($file);
		}elseif ($_POST['azrcrv_s_metafields']['snippet-type'] != 'CSS Stylesheet'){
			$file_id = $snippet_folder.'snippet-'.$post_id.'.css';
			unlink ($file_id);
		}
		
		// JavaScript File
		if ($_POST['azrcrv_s_metafields']['snippet-type'] == 'JavaScript File'){
			if ($old){
			$file_id = $snippet_folder.'snippet-'.$post_id.'.js';
			}else{
			$file_id = $snippet_folder.'snippet-'.($post_id -1).'.js';
			}
			
			$file = fopen($file_id,"w");
			
			$post = get_post($post_id);
			
			$post_content = $post->post_content;
			
			fwrite($file,$post_content);
			fclose($file);
		}elseif ($_POST['azrcrv_s_metafields']['snippet-type'] != 'JavaScript File'){
			$file_id = $snippet_folder.'snippet-'.$post_id.'.js';
			unlink ($file_id);
		}
		
		// PHP Function
		if ($_POST['azrcrv_s_metafields']['snippet-type'] == 'PHP Function'){
			if ($old){
			$file_id = $snippet_folder.'snippet-'.$post_id.'.php';
			}else{
			$file_id = $snippet_folder.'snippet-'.($post_id -1).'.php';
			}
			
			$file = fopen($file_id,"w");
			
			$post = get_post($post_id);
			
			$post_content = $post->post_content;
				if (substr($post_content, 0, 5) != '<?php'){
					$post_content = '<?php
	'.$post_content;
				}
				if (substr($post->post_content, -2, 2) != '?>'){
					$post_content = $post_content.'
	?>';
				}
			
			fwrite($file,$post_content);
			fclose($file);
		}elseif ($_POST['azrcrv_s_metafields']['snippet-type'] != 'PHP Function'){
			$file_id = $snippet_folder.'snippet-'.$post_id.'.php';
			unlink ($file_id);
		}

		if ($new && $new !== $old){
			update_post_meta($post_id, 'azrcrv_s_metafields', $new);
		}
	}
}

/**
 * Delete snippet postmeta when post deleted.
 *
 * @since 1.0.0
 *
 */	
function azrcrv_s_delete_postmeta($post_id){
	
	$options = azrcrv_s_get_option('azrcrv-s');
	$snippet_folder = trailingslashit($options['snippet-folder']);
	
	if (isset($_POST['azrcrv_s_metafields']['snippet-type']) AND $_POST['azrcrv_s_metafields']['snippet-type'] == 'CSS Stylesheet'){
		$file_id = $snippet_folder.'snippet-'.$post_id.'.css';
		unlink ($file_id);
	}
	if (isset($_POST['azrcrv_s_metafields']['snippet-type']) AND $_POST['azrcrv_s_metafields']['snippet-type'] == 'JavaScript File'){
		$file_id = $snippet_folder.'snippet-'.$post_id.'.js';
		unlink ($file_id);
	}
	if (isset($_POST['azrcrv_s_metafields']['snippet-type']) AND $_POST['azrcrv_s_metafields']['snippet-type'] == 'PHP Function'){
		$file_id = $snippet_folder.'snippet-'.$post_id.'.php';
		unlink ($file_id);
	}
}

/**
 * Output Snippet shortcode.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_output_snippet_shortcode($atts, $content = null) {
	
	$options = get_option('azrcrv-s');
	
	// extract attributes from shortcode
	$args = shortcode_atts(array(
		'id' => '',
		'slug' => '',
	), $atts);
	$id = (int) $args['id'];
	$slug = $args['slug'];
	
	if ($id == 0){
		$post = get_page_by_path($slug, OBJECT, 'snippet');
		$id = $post->ID;
	}else {
		$post = get_post($id);
	}
	
	$snippet = '';
	if ($id != 0){
		$post_meta = get_post_meta($id, 'azrcrv_s_metafields', true);
		
		if (in_array($post_meta['snippet-type'], array('HTML', 'PHP'))){
			$snippet = $post->post_content;
			$snippet = do_shortcode($snippet);
			if ($post_meta['snippet-type'] == 'PHP'){
				if (substr($snippet, 0, 5) == '<?php'){
					$snippet = substr($snippet, 5);
				}
				if (substr($snippet, -2, 2) == '?>'){
					$snippet = substr($snippet, 0 , -2);
				}
				$snippet = azrcrv_s_execute_php($snippet);
			}
		}
	}
	return $snippet;
}

/**
 * Execute PHP.
 *
 * @since 1.0.0
 *
 */
function azrcrv_s_execute_php($snippet){
	$snippet =(htmlspecialchars($snippet,ENT_QUOTES));
	$snippet = str_replace("&amp;#8217;","'",$snippet);
	$snippet = str_replace("&amp;#8216;","'",$snippet);
	$snippet = str_replace("&amp;#8242;","'",$snippet);
	$snippet = str_replace("&amp;#8220;","\"",$snippet);
	$snippet = str_replace("&amp;#8221;","\"",$snippet);
	$snippet = str_replace("&amp;#8243;","\"",$snippet);
	$snippet = str_replace("&amp;#039;","'",$snippet);
	$snippet = str_replace("&#039;","'",$snippet);
	$snippet = str_replace("&amp;#038;","&",$snippet);
	$snippet = str_replace("&amp;lt;br /&amp;gt;"," ", $snippet);
	$snippet = htmlspecialchars_decode($snippet);
	$snippet = str_replace("\\[","&#91;",$snippet);
	$snippet = str_replace("\\]","&#93;",$snippet);
	$snippet = str_replace("[","<",$snippet);
	$snippet = str_replace("]",">",$snippet);
	$snippet = str_replace("&#91;",'[',$snippet);
	$snippet = str_replace("&#93;",']',$snippet);
	$snippet = str_replace("&gt;",'>',$snippet);
	$snippet = str_replace("&lt;",'<',$snippet);
	ob_start();
	eval($snippet);
	$snippet = ob_get_contents();
	ob_end_clean();
	return ($snippet);
}