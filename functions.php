<?php
	/**
	 * @package Easy_Voting_system
	 * @author Muhideen Mujeeb Adeoye
	 * @version 1.0.1
	 */

	function evsystem_add_admin_page()
	{

		//Admin page

		add_submenu_page('edit.php?post_type=evsystem', 'Easy Voting System Settings', 'Settings', 'manage_options', 'evsystem_plugin', 'evsystem_setting_page');

		//Activate Custom Setting

		add_action('admin_init', 'evsystem_custom_setting');

	}
	add_action('admin_menu', 'evsystem_add_admin_page');

	function evsystem_custom_setting()
	{

		register_setting('evsystem-group', 'evsystem_display_vote');
		register_setting('evsystem-group', 'evsystem_display_state');
		register_setting('evsystem-group', 'evsystem_paystack_public_key');
		register_setting('evsystem-group', 'evsystem_paystack_secret_key');
		register_setting('evsystem-group', 'evsystem_min_amount');
		register_setting('evsystem-group', 'evsystem_template');
		register_setting('evsystem-group', 'evsystem_no_of_candidate_per_page');
		register_setting('evsystem-group', 'evsystem_vote_button_text');
		register_setting('evsystem-group', 'evsystem_enable_free_vote');

		add_settings_section('evsystem-form-plugin', 'Settings', 'evsystem_plugin_settings', 'evsystem_plugin');

		add_settings_field('evsystem-display-vote', 'Display Vote Counts', 'evsystem_display_vote_input', 'evsystem_plugin', 'evsystem-form-plugin');

		add_settings_field('evsystem-display-state', 'Display Candidate State', 'evsystem_display_state_input', 'evsystem_plugin', 'evsystem-form-plugin');

		add_settings_field('evsystem-template', 'Select Template', 'evsystem_template_input', 'evsystem_plugin', 'evsystem-form-plugin');

		add_settings_field('evsystem-min-amount', 'Amount for one vote', 'evsystem_min_amount_input', 'evsystem_plugin', 'evsystem-form-plugin');

		add_settings_field('evsystem-no-of-cand-per-page', 'Number of Candidate Per Page', 'evsystem_no_of_cand_per_page_input', 'evsystem_plugin', 'evsystem-form-plugin');

		add_settings_field('evsystem-public-key', 'Paystack Public Key', 'evsystem_paystack_public_key_input', 'evsystem_plugin', 'evsystem-form-plugin');

		add_settings_field('evsystem-secret-key', 'Paystack Secret Key', 'evsystem_paystack_secret_key_input', 'evsystem_plugin', 'evsystem-form-plugin');

		add_settings_field('evsystem-vote-button-text', 'Vote button text', 'evsystem_vote_button_text_input', 'evsystem_plugin', 'evsystem-form-plugin');


		add_settings_field('evsystem-enable-free-vote', 'Enable Free Vote', 'evsystem_enable_free_vote_input', 'evsystem_plugin', 'evsystem-form-plugin');
	}

	function evsystem_setting_page()
	{
		include plugin_dir_path(__FILE__) . 'templates/admin.php';
	}

	function evsystem_plugin_settings()
	{
		//echo "Paystack Public Key";
	}

	function evsystem_vote_button_text_input()
	{
		$option = get_option('evsystem_vote_button_text') ? get_option('evsystem_vote_button_text') : 'Vote Now';
		echo '<input type="text" name="evsystem_vote_button_text" value="' . $option . '" id="evsystem_vote_button_text"/>';
	}

	function evsystem_paystack_public_key_input()
	{
		$option = get_option('evsystem_paystack_public_key');
		echo '<input type="text" name="evsystem_paystack_public_key" value="' . $option . '" id="evsystem_paystack_public_key"/>';
	}

	function evsystem_enable_free_vote_input(){
		$option = get_option('evsystem_enable_free_vote');
		$checked = (@$option == 1 ? 'checked' : '');
		echo '<label><input type="checkbox" name="evsystem_enable_free_vote" value="1" id="evsystem_enable_free_vote" ' . $checked . ' /></label>';
	}

	function evsystem_display_vote_input()
	{
		$option = get_option('evsystem_display_vote');
		$checked = (@$option == 1 ? 'checked' : '');
		echo '<label><input type="checkbox" name="evsystem_display_vote" value="1" id="evsystem_display_vote" ' . $checked . ' /></label>';
	}

	function evsystem_display_state_input()
	{
		$option = get_option('evsystem_display_state');
		$checked = (@$option == 1 ? 'checked' : '');
		echo '<label><input type="checkbox" name="evsystem_display_state" value="1" id="evsystem_display_state" ' . $checked . ' /></label>';
	}

	function evsystem_template_input()
	{
		$option = get_option('evsystem_template');
		echo '<select name="evsystem_template" id="evsystem_template">
				<option value="1"';?> <?php if ($option == 1) {echo "selected";}?> <?php echo '>Default</option>
				<option value="2"'; ?> <?php if ($option == 2) {echo "selected";}?> <?php echo '>Theme 1</option>
			</select>';
	}

	function evsystem_paystack_secret_key_input()
	{
		$option = get_option('evsystem_paystack_secret_key');
		echo '<input type="text" name="evsystem_paystack_secret_key" value="' . $option . '" id="evsystem_paystack_secret_key"/>';
	}

	function evsystem_min_amount_input()
	{
		$option = get_option('evsystem_min_amount');
		echo '<input type="number" name="evsystem_min_amount" value="' . $option . '" id="evsystem_min_amount"/><p class="description">Note: Amount is in NGN</p>';
	}

	function evsystem_no_of_cand_per_page_input()
	{
		$option = get_option('evsystem_no_of_candidate_per_page') ? get_option('evsystem_no_of_candidate_per_page') : 10;
		echo '<input type="number" name="evsystem_no_of_candidate_per_page" value="' . $option . '" id="evsystem_no_of_candidate_per_page"/><p class="description">Note: This is going to be the number of Candidate per page</p>';
	}