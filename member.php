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
}

new sedMember();