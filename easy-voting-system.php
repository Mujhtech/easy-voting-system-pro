<?php
    /**
     * @package Easy_Voting_system
     * * @author Muhideen Mujeeb Adeoye
     * @version 1.0.1
     */
    /*
    Plugin Name: Easy Voting System
    Plugin URI: https://github.com/Mujhtech/easy-wp-voting-with-payment
    Description: Easy Voting system allows you to create a simple voting system with payment method
    Author: Mujhtech Mujeeb Muhideen
    Version: 1.0.1
    License: GPL-2.0+
    License URI: http://www.gnu.org/licenses/gpl-2.0.txt
    Author URI: https://github.com/Mujhtech/
    */
    defined('ABSPATH') || die('Direct access is not allow');

    register_activation_hook(__FILE__, 'evsystem_admin_notice_example_activation_hook');

    function evsystem_admin_notice_example_activation_hook()
    {

        set_transient('evsystem-admin-notice-example', true, 5);

    }

    function evsystem_admin_success_notice()
    {

        if (get_transient('evsystem-admin-notice-example')) {
            ?>

        <div class="updated notice is-dismissible">
            <p>Thank you for using this plugin! <strong>You are awesome</strong>.</p>
        </div>

        <?php
            delete_transient('evsystem-admin-notice-example');
        }
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'evsystem_add_action_links');
    function evsystem_add_action_links($links)
    {
        $mylinks = array(
            '<a href="' . admin_url('edit.php?post_type=evsystem&page=evsystem_plugin') . '">Settings</a>',
        );
        return array_merge($links, $mylinks);
    }

    require plugin_dir_path(__FILE__) . 'functions.php';
    require plugin_dir_path(__FILE__) . 'admin/custom-post-type.php';

    function evsystem_shortcode($atts, $content = null)
    {

        extract(shortcode_atts(
            array('contest' => 'all', 'pagination' => 0),
            $atts,
            'evsystem_plugin'
        ));

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/easy-wp-voting.php';
        return ob_get_clean();

    }
    add_shortcode('evsystem_plugin', 'evsystem_shortcode');

    function evsystem_scripts()
    {

        wp_enqueue_style('evsystem-owl-carousel-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.0.0', 'all');

        wp_enqueue_script('evsystem-paystack-js', 'https://js.paystack.co/v1/inline.js', array(), '1.0');

        wp_enqueue_script('evsystem-jquery', plugin_dir_url(__FILE__) . 'assets/js/jquery.min.js', false, '1.11.3', true);

        wp_enqueue_script('evsystem-js', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('evsystem-jquery'), '1.0.0', true);

    }

    add_action('wp_enqueue_scripts', 'evsystem_scripts');

    add_filter( 'single_template', 'evsystem_get_custom_post_type_template' );

	function evsystem_get_custom_post_type_template($single_template) {
		global $post;
	   
		if ($post->post_type == 'evsystem') {
			 $single_template = dirname(  __FILE__  ) . '/templates/single-vote.php';
		}
		return $single_template;
	}

    add_action( 'admin_notices', 'evsystem_paystack_keys_notice' );

    function evsystem_paystack_keys_notice() {
        
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $pub_key = get_option( 'evsystem_paystack_public_key' );
        $secret_key = get_option( 'evsystem_paystack_secret_key' );
        
        if ( $pub_key == null || $pub_key == "" || $secret_key == null || $secret_key == " " ) {
            
            echo '<div class="error"><p>' . sprintf( __( 'Paystack public and secret key required, Click <strong><a href="%s">here</a></strong> to enter it when you want to start voting on your site.', 'easy-voting-system' ), esc_url( admin_url( 'edit.php?post_type=evsystem&page=evsystem_plugin' ) ) ) . '</p></div>';

        }
    }

    require plugin_dir_path(__FILE__) . 'ajax.php';