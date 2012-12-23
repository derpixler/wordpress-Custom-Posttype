<?php
/*
Plugin Name: Custom Postype for WordPress
Plugin URI: 
Description:  Custom post type for some herbs. Creates three new meta boxes to collecting data about herbs.
Version: 1.0
Author: rene reimann
Author URI: http://www.awsome-wordpress-plugin.com
Author Email: info@awsome-wordpress-plugin.com
Author google profile ID: 110560099924916827007
Author twitter: https://twitter.com/awsome-wordpress-plugin
Company Name:
Company URI:
License:

  Copyright 2012 TODO (email@domain.com)

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

namespace CustomPostType\create_the_posttype;

! defined( 'ABSPATH' ) AND exit;

    class The_Custom_Posttype {

        const nspace                = 'The_Custom_Posttype';
		public static $plugin_obj	= false;
		private static $db          = false;
        
        private static $metafile_fields = false;

        public function __construct(){

            self::$plugin_obj->class_name 	= __CLASS__;
            self::$plugin_obj->prefix       = "thpt";
			self::$plugin_obj->base         = plugin_basename(__FILE__);
			self::$plugin_obj->include_path = plugin_dir_path(__FILE__);
            self::$plugin_obj->name         = 'wp-herbs-postype';
            
            
			load_plugin_textdomain( self::$plugin_obj->class_name, false, self::$plugin_obj->name  . '/lang/' );
            
            $this->set_metafile_fields();

            add_action( 'init', array( $this, 'register_herbs_post_type' ) );
            add_action( 'add_meta_boxes', array( $this, 'create_herbs_metaboxes' ) );
            add_action( 'save_post', array( $this, 'save_meta' ), 1, 2 );
        }


        // declare the Custom attributes
        private function set_metafile_fields(){

            $herbs_fields = array( 
                                'lifespan'          => __( 'Lifespan', self::$plugin_obj->class_name ),
                                'location'          => __( 'Location', self::$plugin_obj->class_name ),
                                'soil'              => __( 'Soil', self::$plugin_obj->class_name ),
                                'plant_distance'    => __( 'Plant distance', self::$plugin_obj->class_name ),
                                'height'            => __( 'Height', self::$plugin_obj->class_name ),
                                'sowing'            => __( 'Sowing', self::$plugin_obj->class_name ),
                                'planting_period'   => __( 'Planting period', self::$plugin_obj->class_name ),
                                'anthesis'          => __( 'Anthesis', self::$plugin_obj->class_name ),
                                'harvest'           => __( 'Harvest', self::$plugin_obj->class_name ),
                                'blossom'           => __( 'Blossom' , self::$plugin_obj->class_name ),
                                'flower_colour'     => __( 'Flower colour', self::$plugin_obj->class_name ),
                                'maintenance'       => __( 'Maintenance', self::$plugin_obj->class_name ),
                                'water_consumption' => __( 'Water consumption', self::$plugin_obj->class_name ),
                                'toxicity'          => __( 'Toxicity', self::$plugin_obj->class_name ),
                             );


           foreach( $herbs_fields as $herbs_attrname => $herbs_value){
               self::$metafile_fields->$herbs_attrname = $herbs_value;
           } 

        }

        public function register_herbs_post_type() {

            $labels = array (
                'name'               => __('Custom', self::$plugin_obj->class_name ),
                'singular_name'      => __('Herb', self::$plugin_obj->class_name ),
                'add_new'            => __('new Herb', self::$plugin_obj->class_name ),
                'add_new_item'       => __('new Herb', self::$plugin_obj->class_name ),
                'new_item'           => __('new Herb', self::$plugin_obj->class_name ),
                'edit'               => __('edit Herb', self::$plugin_obj->class_name ),
                'edit_item'          => __('edit Herb', self::$plugin_obj->class_name ),
                'view'               => __('view Herb', self::$plugin_obj->class_name ),
                'view_item'          => __('view Herb', self::$plugin_obj->class_name ),
                'search_items'       => __('search Herb', self::$plugin_obj->class_name ),
                'not_found'          => __('no Herb found', self::$plugin_obj->class_name ),
                'not_found_in_trash' => __('no Herb in trash', self::$plugin_obj->class_name ),
                'parent'             => __('parent Herb', self::$plugin_obj->class_name )
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

            register_post_type('herbs', $args);
        }


        public function create_herbs_metaboxes() {
            add_meta_box( 'profile_box', __('Characteristics', self::$plugin_obj->class_name ), array( $this, 'the_metaboxes' ), 'herbs', 'normal', 'default');
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
add_action( 'plugins_loaded', 'init_wp_herbs_postype');

function init_wp_herbs_postype(){
    $wp->the_herbs_posttype = new The_Custom_Posttype();
}