<?php
/*
Plugin Name: Colorful Post
Plugin URI: http://psd.to-html.com
Description: A simple 'post title color' plugin that lets you select the color of your post title manually.
Version: 1.0.3
Author: Jason Zhao
Author URI: http://psd.to-html.com
*/



// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
  exit;
}



if ( !class_exists('nnColorfulPosts') ) {
  class nnColorfulPosts {
		public $pluginName;
		public $pluginUrl;
		public $pluginPath;

    public $settingsUrl;

    private $option_key    = 'nnColorfulPostsTitle';
    private $default_color = '#000000';
		

		public function __construct() {
			$this -> pluginName  = 'Colorful Post';
			$this -> pluginUrl   = plugins_url('/', __FILE__);
			$this -> pluginPath  = plugin_dir_path( __FILE__ );

      // unified separator for current server
      $this -> pluginPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this -> pluginPath);

      // settings page at backend for this plugin
      $this -> settingsUrl = admin_url( 'options-general.php?page=' . basename(__FILE__) );

      add_action('save_post', array($this, 'set_post_color'));
		}



		/**
		 * homepage at the backend
		 * 
		 */
		public function homepage() {
      if ( $_POST['action'] == 'nnColorfulPosts-reset-all-post-title-color' ) {
        // reset title color
        $this -> reset_all_post_title_color();

        // set message
        add_settings_error(
          'nnColorfulPostsSettingsUpdateSuccess',
          'nnColorfulPostsSettingsUpdateSuccess',
          'Reset Successfully.',
          'updated'
        );
      }


      // display page
 			require $this -> pluginPath . 'templates' . DIRECTORY_SEPARATOR . 'index.php';
		}



    /**
     * load css/js for admin page
     *
     */
    public function load_script_admin() {
      // load color picker css
      wp_enqueue_style( 'wp-color-picker' );


      // load our css
      $css_url = $this -> pluginUrl . 'static/admin.css';
      wp_enqueue_style('custom-style-handle', $css_url);


      // load our js and with color picker depends on
      $js_url  = $this -> pluginUrl . 'static/admin.js';
      wp_enqueue_script('custom-script-handle', $js_url, array( 'wp-color-picker' ));
    }



    /**
     * display the button "Set Post/Page Title Color"
     *
     */
    public function generate_button_pick_color($post) {
      // get current post's color
      $color = get_post_meta($post -> ID, $this -> option_key, true);
      $color = $color ? $color : $this -> default_color;

      // display the button
      $button_pick_color_html = <<<EOT
      <div id="container-nnColorfulPosts-set-post-title-color">
        <input type="input" value="{$color}" data-default-color="{$color}"
               class="button button-small" id="button-nnColorfulPosts-pick-color"
               name="nnColorfulPostTitleColor" />
      </div>
EOT;

      echo $button_pick_color_html;
    }



    /**
     * set one post title color
     *
     */
    public function set_post_color($post_id) {
      // If this is just a revision, don't send the email.
      if ( wp_is_post_revision( $post_id ) ) {
        return $post_id;
      }


      // to prevent metadata or custom fields from disappearing...
      if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return $post_id;
      }


      $color = $this -> is_hex_color($_POST['nnColorfulPostTitleColor']);
      $color = $color ? $color : $this -> default_color;
      if ( $color ) {
        // save
        update_post_meta($post_id, $this -> option_key, $color);
      }
    }



    /**
     * check the string is hex color value
     *
     * @param    string    $color
     * @return   bool
     */
    private function is_hex_color($color) {
      $color = str_replace('#', '', $color);
      $color = trim($color);

      // check the length
      $length = strlen($color);
      if ( $length != 6 ) {
        return false;
      }

      $allow_chars = array_merge(range('a', 'f'));

      // check one by one
      for ( $i = 0; $i < $length; $i++ ) {
        $n = strtolower( $color[$i] );

        // number, is ok
        if ( is_numeric($n) ) {
          continue;
        }

        // char, need check
        if ( !in_array($n, $allow_chars) ) {
          return false;
        }
      }


      return '#' . $color;
    }



    /**
     * colorful the post title for wordpress front page only
     *
     * @param     string    $post_title    The post title
     * @param     int       $id            The post ID
     * @return    string
     */
    public function colorful_post_title($post_title, $id = null) {
      // only colorful for front page
      if ( is_blog_admin() ) {
        return $post_title;
      }


      // get this post color
      $post_title_color = get_post_meta($id, $this -> option_key, true);

      if ( !$post_title_color ) {
        return $post_title;
      }

      // make sure the color is real hex color value
      if ( !$this -> is_hex_color($post_title_color) ) {
        return $post_title;
      }

      // if is default color, does not set
      if ( $this -> default_color == $post_title_color ) {
        return $post_title;
      }

      // add color
      $post_title = sprintf(
        '<span class="nnColorfulPostColor" style="color:%s">%s</span>',
        $post_title_color,
        $post_title
      );

      return $post_title;
    }



    /**
     * reset all post title color
     *
     */
    public function reset_all_post_title_color() {
      global $wpdb;

      $clear_sql = "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` = '{$this -> option_key}'";
      $wpdb -> query($clear_sql);
    }


		
    /**
     * callback action when user activate this plugin
     *
     */
    public function activate() {

    }



    /**
     * callback action when user deactivate this plugin
     *
     */
		public function deactivate() {

		}
	}	// End Class nnColorfulPosts
}


$obj_nnColorfulPosts = new nnColorfulPosts();


/**
 * add plugin menu on sidebar channel "Settings"
 * 
 */
function nnColorfulPostsGenerateAdminMenu() {
  global $obj_nnColorfulPosts;

  add_options_page(
    $obj_nnColorfulPosts -> pluginName,
    $obj_nnColorfulPosts -> pluginName,
    9,
    basename(__FILE__),
    array($obj_nnColorfulPosts, 'homepage')
  );
}
add_action('admin_menu', 'nnColorfulPostsGenerateAdminMenu');





/**
 * add the link "Settings" on plugin menu
 *
 */
function nnColorfulPostsGenerateSettingsLinks($links) {
  global $obj_nnColorfulPosts;
  $links[] = '<a href="' . $obj_nnColorfulPosts -> settingsUrl . '">Settings</a>';

  return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'nnColorfulPostsGenerateSettingsLinks' );





/**
 * register activate/deactivate action
 *
 */
register_activation_hook( __FILE__, array($obj_nnColorfulPosts, 'activate') );
register_deactivation_hook( __FILE__, array($obj_nnColorfulPosts, 'deactivate') );


/**
 * add hook to display the button "pick color" under the page/posts title
 *
 */
add_action('edit_form_after_title', array($obj_nnColorfulPosts, 'generate_button_pick_color'));


// load css and js
add_action('admin_head', array($obj_nnColorfulPosts, 'load_script_admin'));


// add hook to colorful the post title
add_filter('the_title', array($obj_nnColorfulPosts, 'colorful_post_title'), 10, 2);