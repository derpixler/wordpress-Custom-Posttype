<?php
/**
* Plugin Name: Custom Postype for WordPress
* Plugin URI: 
* Description:  Custom post type for some custom. Creates three new meta boxes to collecting data about custom.
* Version: 1.0
* Author: Sven Balzer
* Author URI: http://pixel-in-motion.de
* Author Email: sven@pixel-in-motion.de
* License: WTFPL
*
*    This program is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License, version 2, as 
*    published by the Free Software Foundation.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*  
*    You should have received a copy of the GNU General Public License
*    along with this program; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
* 
**/   

namespace CustomPostType\create_the_posttype;

! defined( 'ABSPATH' ) AND exit;

    class The_Custom_Posttype {

        const nspace                = 'The_Custom_Posttype';
		public static $plugin_obj	= false;
		private static $db          = false;
        
        private static $metafile_fields = false;

        public function __construct(){

            self::$plugin_obj->class_name 	= __CLASS__;
            self::$plugin_obj->prefix       = "wp_cpt"; // @TODO: change prefix
			self::$plugin_obj->base         = plugin_basename(__FILE__);
			self::$plugin_obj->include_path = plugin_dir_path(__FILE__);
            self::$plugin_obj->name         = 'wp-custom-postype';
            
            
			load_plugin_textdomain( self::$plugin_obj->class_name, false, self::$plugin_obj->name  . '/lang/' );
            
            $this->set_metafile_fields();

            add_action( 'init', array( $this, 'register_custom_post_type' ) );
            add_action( 'add_meta_boxes', array( $this, 'create_custom_metaboxes' ) );
            add_action( 'save_post', array( $this, 'save_meta' ), 1, 2 );
        }


        // declare the Custom attributes
        private function set_metafile_fields(){

			// add more fields for your requierements
            $custom_fields = array( 
				'field_name1'	=> __( 'field name 1', self::$plugin_obj->class_name ),
				'field_name2'	=> __( 'field name 2', self::$plugin_obj->class_name ),
				'field_name3'	=> __( 'field name 3', self::$plugin_obj->class_name ),
			 );


           foreach( $custom_fields as $custom_attrname => $custom_value){
               self::$metafile_fields->$custom_attrname = $custom_value;
           } 

        }

        public function register_custom_post_type() {
			// rename labels for your choice
            $labels = array (
                'name'               => __('Custom', self::$plugin_obj->class_name ),
                'singular_name'      => __('item', self::$plugin_obj->class_name ),
                'add_new'            => __('new item', self::$plugin_obj->class_name ),
                'add_new_item'       => __('new item', self::$plugin_obj->class_name ),
                'new_item'           => __('new item', self::$plugin_obj->class_name ),
                'edit'               => __('edit item', self::$plugin_obj->class_name ),
                'edit_item'          => __('edit item', self::$plugin_obj->class_name ),
                'view'               => __('view item', self::$plugin_obj->class_name ),
                'view_item'          => __('view item', self::$plugin_obj->class_name ),
                'search_items'       => __('search item', self::$plugin_obj->class_name ),
                'not_found'          => __('no item found', self::$plugin_obj->class_name ),
                'not_found_in_trash' => __('no item in trash', self::$plugin_obj->class_name ),
                'parent'             => __('parent item', self::$plugin_obj->class_name )
            );

            $args = array(
                'labels'              => $labels,
                'public'              => TRUE,
                'publicly_queryable'  => TRUE,
                'show_ui'             => TRUE,
                'show_in_menu'        => TRUE,
                'query_var'           => TRUE,
                'capability_type'     => 'page',
                'has_archive'         => TRUE,
                'hierarchical'        => TRUE,
                'exclude_from_search' => FALSE,
                'menu_position'       => 100 ,
                'supports'            => array('title','editor','excerpt','custom-fields','revisions','thumbnail','page-attributes'),
                'taxonomies'          => array('category','post_tag')
            );

            register_post_type('custom', $args);
        }


        public function create_custom_metaboxes() {
            add_meta_box( 'profile_box', __('Characteristics', self::$plugin_obj->class_name ), array( $this, 'the_metaboxes' ), 'custom', 'normal', 'default');
        }


        public function the_metaboxes(){
            global $post;

            $meta_values = get_post_meta( $post->ID );

            foreach( self::$metafile_fields as $name => $profile_field ) : ?>  
                <div style="padding: 3px 0;" class="cf">
                    <div style="width: 75px; margin-right: 10px; float: left; text-align: right; padding-top: 5px;">
                        <?php echo $profile_field; ?>
                    </div>
                    <input type="text" style=" float: left; width: 120px;" name="<?php echo $name ?>" value="<?php if(isset($meta_values['_' . $name])) { echo esc_attr( $meta_values['_' . $name][0] ); } ?>" />
                </div>
            <?php endforeach;
        }


        public function save_meta( $post_id, $post ){

            foreach( self::$metafile_fields as $name => $profile_field ) {

                if ( isset( $_POST[ $name ] ) ) { 
                    update_post_meta( $post_id, '_' . $name, strip_tags( $_POST[ $name ] ) ); 
                }
            }

            if ( isset( $_POST[ 'pageid' ] ) ) { 
                update_post_meta( $post_id, '_pageid', strip_tags( $_POST[ 'pageid' ] ) ); 
            }

        }
        

    }




// Init Plugin when is it Loaded
add_action( 'plugins_loaded', 'init_wp_custom_postype');

function init_wp_custom_postype(){
    $wp->the_custom_posttype = new The_Custom_Posttype();
}
