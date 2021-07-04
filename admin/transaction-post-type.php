<?php

	/**
	 * @package Easy Voting System
	 * @author Muhideen Mujeeb Adeoye
	 * @version 1.0.1
	 */

	@ob_start();
    add_action('init', 'evsystem_transaction_post_type');

    add_filter('manage_evsystem-transaction_posts_columns', 'evsystem_transaction_set_columns_name');

    add_action('manage_evsystem-transaction_posts_custom_column', 'evsystem_transaction_custom_columns', 10, 2);

    add_action('add_meta_boxes', 'evsystem_transaction_add_meta_box');

    add_action('save_post', 'evsystem_transaction_save_amount_data');

    add_action('save_post', 'evsystem_transaction_save_status_data');

    add_action('save_post', 'evsystem_transaction_save_email_data');

    add_action('save_post', 'evsystem_transaction_save_voted_for_data');

    function evsystem_transaction_post_type()
	{
		$labels = array(
			'name' => 'Easy Voting System Transaction',
			'singular_name' => 'Easy Voting System Transaction',
			'menu_name' => 'Easy Voting Systems Transaction',
			'name_admin_bar' => 'Easy Voting System Transaction',
		);

		$args = array(
			'labels' => $labels,
			'show_ui' => true,
			'show_ui_menu' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 200,
			'publicly_queryable' => false,
			'menu_icon' => 'dashicons-money-alt',
			'supports' => array('title'),
            'map_meta_cap' => false,
            'capabilities' => array(
                'create_posts' => 'do_not_allow',
            ),
			'rewrite' => array('slug' => 'transaction')
		);

		register_post_type('evsystem-transaction', $args);
	}


    function evsystem_transaction_set_columns_name($columns)
	{
		$clientColumns = array();
		$clientColumns['cb'] = "<input type=\"checkbox\" />";
		$clientColumns['title'] = 'Transaction Reference';
		$clientColumns['email'] = 'Email Address';
        $clientColumns['amount'] = 'Amount Paid';
		$clientColumns['voted_for'] = 'Voted For';
		$clientColumns['status'] = 'Status';
		return $clientColumns;

	}


    function evsystem_transaction_custom_columns($columns, $post_id)
	{

		switch ($columns) {
            case 'title':
				$value = get_the_title($post_id);
				echo '<strong>' . $value . '</strong>';
				break;

			case 'email':
				$value = get_post_meta($post_id, '_evsystem_transaction_email_value_key', true);
				echo '<strong>' . $value . '</strong>';
				break;

            case 'amount':
                $value = get_post_meta($post_id, '_evsystem_transaction_amount_value_key', true);
                echo '<strong>' . $value . '</strong>';
                break;

			case 'voted_for':
				$value = get_post_meta($post_id, '_evsystem_transaction_voted_for_value_key', true);
				echo '<strong>' . $value . '</strong>';
				break;

			case 'status':
				$value = get_post_meta($post_id, '_evsystem_transaction_status_value_key', true);
				echo '<strong>' . $value . '</strong>';
				break;
		}

	}


    function evsystem_transaction_add_meta_box()
	{
		add_meta_box('evsystem_transaction_email', 'Email', 'evsystem_transaction_email_callback', 'evsystem-transaction', 'normal');
		add_meta_box('evsystem_transaction_amount', 'Amount', 'evsystem_transaction_amount_callback', 'evsystem-transaction', 'normal');
		add_meta_box('evsystem_transaction_voted_for', 'Voted For', 'evsystem_transaction_voted_for_callback', 'evsystem-transaction', 'normal');
		add_meta_box('evsystem_transaction_status', 'Status', 'evsystem_transaction_status_callback', 'evsystem-transaction', 'normal');

	}

    function evsystem_transaction_email_callback($post)
	{
		wp_nonce_field('evsystem_transaction_save_email_data', 'evsystem_transaction_email_meta_box_nonce');
		$value = get_post_meta($post->ID, '_evsystem_transaction_email_value_key', true);

		echo '<label for="evsystem_transaction_email_field"> Email Address </label><br><br> ';
		echo '<input type="email" name="evsystem_transaction_email_field" id="evsystem_transaction_email_field" value="' . esc_attr($value) . '" size="25"/>';
	}


    function evsystem_transaction_amount_callback($post)
	{
		wp_nonce_field('evsystem_transaction_save_amount_data', 'evsystem_transaction_amount_meta_box_nonce');
		$value = get_post_meta($post->ID, '_evsystem_transaction_amount_value_key', true);

		echo '<label for="evsystem_transaction_amount_field"> Amount Paid </label><br><br> ';
		echo '<input type="text" name="evsystem_transaction_amount_field" id="evsystem_transaction_amount_field" value="' . esc_attr($value) . '" size="25"/>';
	}

    function evsystem_transaction_voted_for_callback($post)
	{
		wp_nonce_field('evsystem_transaction_save_voted_for_data', 'evsystem_transaction_voted_for_meta_box_nonce');
		$value = get_post_meta($post->ID, '_evsystem_transaction_voted_for_value_key', true);

		echo '<label for="evsystem_transaction_voted_for_field"> Voted For </label><br><br> ';
		echo '<input type="text" name="evsystem_transaction_voted_for_field" id="evsystem_transaction_voted_for_field" value="' . esc_attr($value) . '" size="25"/>';
	}

    function evsystem_transaction_status_callback($post)
	{
		wp_nonce_field('evsystem_transaction_save_status_data', 'evsystem_transaction_status_meta_box_nonce');
		$value = get_post_meta($post->ID, '_evsystem_transaction_status_value_key', true);

		echo '<label for="evsystem_transaction_status_field"> Voted For </label><br><br> ';
		echo '<input type="text" name="evsystem_transaction_status_field" id="evsystem_transaction_status_field" value="' . esc_attr($value) . '" size="25"/>';
	}



    function evsystem_transaction_save_status_data($post_id)
	{

		if (!isset($_POST['evsystem_transaction_status_meta_box_nonce'])) {
			return;
		}
		if (!wp_verify_nonce($_POST['evsystem_transaction_status_meta_box_nonce'], 'evsystem_transaction_save_status_data')) {
			return;
		}
		if (define('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
		if (!isset($_POST['evsystem_transaction_status_field'])) {
			return;
		}

		$my_data = sanitize_text_field($_POST['evsystem_transaction_status_field']);

		update_post_meta($post_id, '_evsystem_transaction_status_value_key', $my_data);

	}

    function evsystem_transaction_save_amount_data($post_id)
	{

		if (!isset($_POST['evsystem_transaction_amount_meta_box_nonce'])) {
			return;
		}
		if (!wp_verify_nonce($_POST['evsystem_transaction_amount_meta_box_nonce'], 'evsystem_transaction_save_amount_data')) {
			return;
		}
		if (define('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
		if (!isset($_POST['evsystem_transaction_amount_field'])) {
			return;
		}

		$my_data = sanitize_text_field($_POST['evsystem_transaction_amount_field']);

		update_post_meta($post_id, '_evsystem_transaction_amount_value_key', $my_data);

	}

    function evsystem_transaction_save_email_data($post_id)
	{

		if (!isset($_POST['evsystem_transaction_email_meta_box_nonce'])) {
			return;
		}
		if (!wp_verify_nonce($_POST['evsystem_transaction_email_meta_box_nonce'], 'evsystem_transaction_save_email_data')) {
			return;
		}
		if (define('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
		if (!isset($_POST['evsystem_transaction_email_field'])) {
			return;
		}

		$my_data = sanitize_text_field($_POST['evsystem_transaction_email_field']);

		update_post_meta($post_id, '_evsystem_transaction_email_value_key', $my_data);

	}


    function evsystem_transaction_save_voted_for_data($post_id)
	{

		if (!isset($_POST['evsystem_transaction_voted_for_meta_box_nonce'])) {
			return;
		}
		if (!wp_verify_nonce($_POST['evsystem_transaction_voted_for_meta_box_nonce'], 'evsystem_transaction_save_voted_for_data')) {
			return;
		}
		if (define('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
		if (!isset($_POST['evsystem_transaction_voted_for_field'])) {
			return;
		}

		$my_data = sanitize_text_field($_POST['evsystem_transaction_voted_for_field']);

		update_post_meta($post_id, '_evsystem_transaction_voted_for_value_key', $my_data);

	}