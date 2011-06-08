<?php
/*
Plugin Name: Custom Comment Message
Plugin URI: http://plugintutorials.blogspot.com/2011/04/custom-comment-message-wordpress-plugin.html
Description: Allows you to specify the title of the comments section.
Version: 1.0
Author: ppg
Author URI: http://plugintutorials.blogspot.com/
License: GPL2

*/

/*  Copyright 2011  Pubudu Gunawardena

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/




if (!class_exists("CustomCommentMessage")) {
	class CustomCommentMessage {
		public function __construct()
	    {
			add_action( 'add_meta_boxes',
                        array( &$this, 'add_ccm_meta_box' ) );

			add_action('save_post', 
						array(&$this,'ccm_save_data') ) ;

			add_filter('comment_form_defaults',
                       array(&$this, 'edit_cm') ) ;

			add_action('admin_init', 
					   array(&$this, 'ccm_admin_init') );

			add_action('admin_menu', 
					   array(&$this, 'add_ccm_admin_page') );

			add_action( 'init', array(&$this,
						'load_ccm_textdomain') );

		}

		function load_ccm_textdomain(){
			load_plugin_textdomain('ccm_textdomain',false,
						'custom-comment-message/languages/');
		}	

		function ccm_admin_init(){
			register_setting( 'ccm_options',
							  'ccm_title_reply' );

			add_settings_section('ccm_main',
								 __('Custom Comment Message','ccm_textdomain'),
								 array(&$this,'ccm_section_text'),
								 plugin_basename(__FILE__));

			add_settings_field('plugin_text_string',
							   __('Default Comment Message','ccm_textdomain'),
							   array(&$this,'ccm_setting_string'),
							   plugin_basename(__FILE__),
							   'ccm_main');

		}

		function ccm_section_text() {
			echo 
			 '<p>'._e('Set the default comment message that you would like to use.','ccm_textdomain').'</p>';

		}

		function ccm_setting_string() {
			$option = get_option('ccm_title_reply');
?>
			<input id='plugin_text_string'             
				   name='ccm_title_reply' 
				   size='40'
				   type='text' 
				   value="<?php echo $option ?>" />
<?php
		}



		function add_ccm_admin_page(){
			add_options_page(__('Custom Comment Message Otions','ccm_textdomain'),
   		                     __('Custom Comment Message','ccm_textdomain'),
		                     'manage_options',
		                     plugin_basename(__FILE__),
		                     array(&$this,'print_ccm_admin_page'));
		}

		function print_ccm_admin_page(){
?>
    <div>
          <form method="post" 
				action="options.php">
         
            <?php settings_fields('ccm_options'); ?>
			<?php do_settings_sections(plugin_basename(__FILE__)); ?>

            <input type="submit" class="button-primary"
				   value="<?php _e('Save Changes','ccm_textdomain')?>"
				   name="Submit"/>
          </form>
    </div>
<?php

    	}

		public function edit_cm($defaults){
			global $wp_query;
			$post_id = $wp_query->post->ID;
			$custom_message = get_post_meta($post_id,
                                             'ccm_title_reply',
                                             true);
			if(empty($custom_message)){
				$custom_message = get_option('ccm_title_reply');
			}

			if(!empty($custom_message)){
				$defaults["title_reply"] = wp_kses_data($custom_message);
			}
			
			return $defaults;
		}

	    public function add_ccm_meta_box()
		{
		    add_meta_box(
		         'ccm_meta_box'
		        ,__('Custom Comment Message','ccm_textdomain')
		        ,array( &$this, 'render_ccm_meta_box_content' )
		        ,'post'		        
		    );
		}

		public function render_ccm_meta_box_content( $post )
		{		    
			wp_nonce_field( plugin_basename(__FILE__),
							'ccm_noncename');
?>
		    <label for="ccm__title_reply">
		         <?php _e('Custom Comment Message Title :' ,'ccm_textdomain')?>
			</label>
		    <input type="text"
		           id="ccm_title_reply"
		           name="ccm_title_reply"
				   value="<?php echo wp_kses_data(get_post_meta($post->ID,
                                                   'ccm_title_reply',
                                                   true)); ?>"
		           size="25" />
<?php
		}

		public function ccm_save_data( $post_id )
		{
			if ( !wp_verify_nonce( $_POST['ccm_noncename'],
                                   plugin_basename(__FILE__) ) )
	 	       return $post_id;
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
				return;
			}
			else if ( 'post' == $_POST['post_type'] && 
					current_user_can( 'edit_page', $post_id ) )
			{
				$ccm = $_POST['ccm_title_reply'];
				if(!empty($ccm)){
					add_post_meta($post_id, 'ccm_title_reply', $ccm, true)
							or
					update_post_meta($post_id, 'ccm_title_reply', $ccm);
				}else{
					delete_post_meta($post_id, 'ccm_title_reply');
				}
			}
		}
	}
}

if (class_exists("CustomCommentMessage")) {
	$ccmessage = new CustomCommentMessage();
}

?>
