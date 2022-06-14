<?php
/*
Plugin Name: SED Member (User Extension)
Plugin URI: https://github.com/rpi-virtuell/sed-member
Description: Wordpress Plugin to extend Wordpress User by Member Class
Version: 1.0
Author: Daniel Reintanz
Author URI: https://github.com/FreelancerAMP
*/

class sedMember
{
    public function __construct()
    {
        add_action('admin_notices', array($this, 'backend_notifier'));
        add_action('init', array($this, 'register_gravity_form'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('wp_login', array($this, 'sync_user_member_relation'), 10, 2);
        add_shortcode('rpi-userprofile', array($this, 'get_user_profile_tags'));
    }

    public function register_gravity_form()
    {
//        $form = GFAPI::get_form(3);
//        file_put_contents(__DIR__ . '/form.dat', serialize($form));

        global $wpdb;
        $form_title = 'Nutzereinstellungen';
        $formssql = "SELECT ID FROM {$wpdb->prefix}gf_form WHERE title = %s and is_trash = 0;";
        if (empty($formId = $wpdb->get_var($wpdb->prepare($formssql, $form_title)))) {
            $form = unserialize(file_get_contents(__DIR__ . '/form.dat'));
            $formId = GFAPI::add_form($form);
        }
    }



    public function backend_notifier()
    {

        if (!function_exists('add_form')) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e('WARNING: Required Plugins required by SED Member are missing. Make sure they are activated in order to use SED Member functionality!'); ?> </p>
            </div>
            <?php
        }
    }

    public function register_post_types()
    {
        /**
         * Post Type: Mitglied.
         */

        $labels = [
            "name" => __("Mitglieder", "twentytwentytwo"),
            "singular_name" => __("Mitglied", "twentytwentytwo"),
        ];

        $args = [
            "label" => __("Mitglieder", "twentytwentytwo"),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true, // TODO: MIGHT CHANGE LATER WIP
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => true,
            "exclude_from_search" => false,
            'capability_type' => array('post'),
            "map_meta_cap" => true,
            "hierarchical" => false,
            "can_export" => true,
            "rewrite" => ["slug" => "member", "with_front" => true],
            "query_var" => true,
            "menu_icon" => "dashicons-list-view",
            "supports" => [
                'title',
                "editor",
            ],
            'taxonomies' => [],
            "show_in_graphql" => false,
        ];

        register_post_type("member", $args);

        /**
         * Post Type: Gruppe.
         */

        $labels = [
            "name" => __("Gruppen", "twentytwentytwo"),
            "singular_name" => __("Gruppe", "twentytwentytwo"),
        ];

        $args = [
            "label" => __("Gruppe", "twentytwentytwo"),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true, // TODO: MIGHT CHANGE LATER WIP
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => true,
            "exclude_from_search" => false,
            'capability_type' => array('post'),
            "map_meta_cap" => true,
            "hierarchical" => false,
            "can_export" => true,
            "rewrite" => ["slug" => "group", "with_front" => true],
            "query_var" => true,
            "menu_icon" => "dashicons-list-view",
            "supports" => [
                'title',
                "editor",
            ],
            'taxonomies' => [],
            "show_in_graphql" => false,
        ];

        register_post_type("group", $args);
    }


    function register_taxonomies()
    {

        /**
         * Taxonomy: Badges.
         */

        $labels = [
            "name" => __("Badges", "blocksy"),
            "singular_name" => __("Badge", "blocksy"),
        ];


        $args = [
            "label" => __("Badges", "blocksy"),
            "labels" => $labels,
            "public" => true,
            "publicly_queryable" => true,
            "hierarchical" => true,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => ['slug' => 'badge', 'with_front' => true,],
            "show_admin_column" => true,
            "show_in_rest" => true,
            "show_tagcloud" => false,
            "rest_base" => "badge",
            "rest_controller_class" => "WP_REST_Terms_Controller",
            "rest_namespace" => "wp/v2",
            "show_in_quick_edit" => true,
            "sort" => true,
            "show_in_graphql" => false,
        ];
        register_taxonomy("badge", ["member"], $args);

        /**
         * Taxonomy: Channel.
         */

        $labels = [
            "name" => __("Channels", "blocksy"),
            "singular_name" => __("Channel", "blocksy"),
        ];


        $args = [
            "label" => __("Channel", "blocksy"),
            "labels" => $labels,
            "public" => true,
            "publicly_queryable" => true,
            "hierarchical" => true,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => ['slug' => 'channel', 'with_front' => true, 'hierarchical' => true,],
            "show_admin_column" => true,
            "show_in_rest" => true,
            "show_tagcloud" => false,
            "rest_base" => "channel",
            "rest_controller_class" => "WP_REST_Terms_Controller",
            "rest_namespace" => "wp/v2",
            "show_in_quick_edit" => true,
            "sort" => false,
            "show_in_graphql" => false,
        ];
        register_taxonomy("channel", ["member"], $args);

        /**
         * Taxonomy: Tags.
         */

        $labels = [
            "name" => __("Tags", "blocksy"),
            "singular_name" => __("Tag", "blocksy"),
        ];


        $args = [
            "label" => __("Tag", "blocksy"),
            "labels" => $labels,
            "public" => true,
            "publicly_queryable" => true,
            "hierarchical" => true,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => ['slug' => 'rpi_tag', 'with_front' => true, 'hierarchical' => true,],
            "show_admin_column" => true,
            "show_in_rest" => true,
            "show_tagcloud" => false,
            "rest_base" => "rpi_tag",
            "rest_controller_class" => "WP_REST_Terms_Controller",
            "rest_namespace" => "wp/v2",
            "show_in_quick_edit" => true,
            "sort" => false,
            "show_in_graphql" => false,
        ];
        register_taxonomy("rpi_tag", ["member"], $args);
    }

    public function sync_user_member_relation($user_login, $user)
    {
        if (is_a($user, 'WP_User')) {
            $member = get_posts(array(
                'post_status' => 'any',
                'post_type' => 'member',
                'author' => $user->ID
            ));
            if (is_array($member) && !empty(reset($member))) {
                return;
            } else {
               $member = wp_insert_post(array(
                    'ID' => $user->ID,
                    'post_title' => $user->display_name,
                    'post_status' => 'publish',
                    'post_author' => $user->ID,
                    'post_type' => 'member'
                ));
            }
        }
    }

    public function get_user_profile_tags($atts){

        if (isset($atts['content']))
        {
          echo '<div></div>';
        }
    }

}

new sedMember();