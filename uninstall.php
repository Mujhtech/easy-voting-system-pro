<?php

	/*

	========================
	UNINSTALL FUNCTIONS
	========================
	*/

	if (!defined('WP_UNINSTALL_PLUGIN')) {
		exit;
	}

	//delete all custom post type with metadata
	$myplugin_cpt_args = array('post_type' => 'evsystem', 'posts_per_page' => -1);
	$myplugin_cpt_posts = get_posts($myplugin_cpt_args);
	foreach ($myplugin_cpt_posts as $post) {
		wp_delete_post($post->ID, false);
		delete_post_meta($post->ID, '_evsystem_vote_value_key');
		delete_post_meta($post->ID, '_evsystem_age_value_key');
		delete_post_meta($post->ID, '_evsystem_occupation_value_key');
		delete_post_meta($post->ID, '_evsystem_state_value_key');
		delete_post_meta($post->ID, '_evsystem_nickname_value_key');
	}

	//remove shortcode
	remove_shortcode('evsystem_plugin');

	//delete register options
	delete_option('evsystem_paystack_public_key');
	delete_option('evsystem_paystack_secret_key');
	delete_option('evsystem_display_state');
	delete_option('evsystem_display_vote');
	delete_option('evsystem_min_amount');
	delete_option('evsystem_template');
	delete_option('evsystem_no_of_candidate_per_page');
	delete_option('evsystem_vote_button_text');
