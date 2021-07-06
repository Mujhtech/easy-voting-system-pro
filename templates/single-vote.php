<?php

	/**
	 * @package Easy Voting System
	 * @author Muhideen Mujeeb Adeoye
	 * @version 1.0.1
	 */

     get_header();

    ?>
	<style>
		.evsystem-container {
			display: flex;
			flex-flow: row wrap;
			justify-content: center;
			margin: 5px 0;
		}

		.evsystem-column {
			flex: 1;
			/* border: 1px solid gray; */
			margin: 2px;
			padding: 10px;
			&:first-child { margin-left: 0; }
			&:last-child { margin-right: 0; }
			
		}
		@media screen and (max-width: 980px) {
			.evsystem-container .evsystem-column {
				margin-bottom: 5px;
				flex-basis: 40%;
				&:nth-last-child(2) {
					margin-right: 0;
				}
				&:last-child {
					flex-basis: 100%;
					margin: 0;
				}
			}
		}

		@media screen and (max-width: 680px) {
			.evsystem-container .evsystem-column {
				flex-basis: 100%;
				margin: 0 0 5px 0;
			}
		}
	</style>

	<div class="evsystem-container">
			<?php
			
				$v_categories = array(
					array('amount' => 500, 'vote' => 10),
					array('amount' => 5000, 'vote' => 100),
					array('amount' => 10000, 'vote' => 1000),
					array('amount' => 25000, 'vote' => 10000),
					array('amount' => 50000, 'vote' => 25000),
					array('amount' => 100000, 'vote' => 100000),
					array('amount' => 150000, 'vote' => 200000),
					array('amount' => 250000, 'vote' => 500000),
					array('amount' => 500000, 'vote' => 1000000),
					array('amount' => 1000000, 'vote' => 2000000),
				);
							
				if( have_posts() ):
								
					while( have_posts() ): the_post();

					$nickname = get_post_meta(get_the_ID(),"_evsystem_nickname_value_key",true);
					$age = get_post_meta(get_the_ID(),"_evsystem_age_value_key",true);
					$state = get_post_meta(get_the_ID(),"_evsystem_state_value_key",true);
					$vote = get_post_meta(get_the_ID(),"_evsystem_vote_value_key",true);
					$occupation = get_post_meta(get_the_ID(),"_evsystem_occupation_value_key",true);

					?>

						<div class="evsystem-column">
							<?php the_post_thumbnail(); ?>
						</div>
						<div class="evsystem-column">
							<h4><?php echo $nickname; ?></h4>
							<h1><?php the_title(); ?></h1>
							<h4><?php echo $occupation; ?></h4>
							<?php if(get_option('evsystem_display_state') == 1): ?>
                                <strong>State:</strong> <?php echo $state; ?>
                            <?php endif; ?>
                            <?php if(get_option('evsystem_display_vote') == 1): ?>
                                <br><strong>Votes:</strong> <?php echo $vote; ?>
                            <?php endif; ?>
							<form onsubmit="return <?php if(get_option('evsystem_enable_free_vote') == 1): ?>evsystemFree(event)<?php else: ?>evsystemForm(event)<?php endif; ?>">
								<input type="email" id="evsystem-email" placeholder="Enter your email">
								<input type="number" id="evsystem-number" placeholder="Enter numbers of vote" <?php if(get_option('evsystem_enable_free_vote') == 0): ?>onkeyup="return updateAmount(event)"<?php endif; ?>>
								<input type="submit" id="evsystem-button" value="<?php echo get_option('evsystem_vote_button_text'); ?>">
							</form>
						</div>
						<?php
					endwhile;
				endif;
			?>
	</div>
	<script>

		function updateAmount(event){

			const quantity = event.target.value;
			const total = quantity * <?php echo get_option('evsystem_min_amount'); ?>;
			const current_text = "<?php echo get_option('evsystem_vote_button_text'); ?>";
			const btn_text = "Pay ₦" + total + " & " + current_text;
			const button = document.getElementById('evsystem-button');

			if(total == null || total == 0){
				button.value = current_text;
			} else {
				button.value = btn_text;
			}

		}

		function evsystemForm(event){
			event.preventDefault();
			const btn_field = document.getElementById('evsystem-button');
			const quantity_field = document.getElementById('evsystem-number');
			const email_field = document.getElementById('evsystem-email');
			const amount = parseInt(quantity_field.value) * <?php echo get_option('evsystem_min_amount'); ?>;
			const ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
			const email = email_field.value;
			const formId = <?php echo get_the_ID(); ?>;
			const username = "<?php the_title(); ?>";

			if (email == "") {

				alert("Please enter your email address");

				return true;
			}

			if (quantity == "") {

				alert("Please select vote category");

				return true;
			}

			quantity_field.setAttribute("disabled", true);
			email_field.setAttribute("disabled", true);
			btn_field.setAttribute("disabled", true);
			var handler = PaystackPop.setup({
				key: '<?php echo get_option( 'evsystem_paystack_public_key' ); ?>',
				email: email,
				amount: amount * 100,
				currency: 'NGN',
				callback: function(response) {
				var reference = response.reference;
				console.log(reference);
				jQuery.ajax({
					url : ajaxurl,
					type : 'post',
					dataType: 'json',
					data : {

						quantity : quantity_field.value,
						userID : formId,
						reference: reference,
						username: username,
						email: email,
						amount: amount,
						action: 'evsystem_form_ajax'

					},
					success : function( response ){
							
						if(response.success == true){
							alert(response.message);
							quantity_field.setAttribute("disabled", true);
							email_field.setAttribute("disabled", true);
							btn_field.setAttribute("disabled", true);
							setTimeout(window.location.reload(), 500);
						} else {
							alert(response.message);
							quantity_field.setAttribute("disabled", true);
							email_field.setAttribute("disabled", true);
							btn_field.setAttribute("disabled", true);
						}
					}

				});
				},
				onClose: function() {
					alert('Transaction was not completed, window closed.');
					quantity_field.setAttribute("disabled", true);
					email_field.setAttribute("disabled", true);
					btn_field.setAttribute("disabled", true);
				},
			});
			handler.openIframe();
			
		}


		function evsystemFree(event){
			event.preventDefault();
			const btn_field = document.getElementById('evsystem-button');
			const quantity_field = document.getElementById('evsystem-number');
			const email_field = document.getElementById('evsystem-email');
			const ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
			const email = email_field.value;
			const quantity = quantity_field.value;
			const formId = <?php echo get_the_ID(); ?>;
			const username = "<?php the_title(); ?>";

			if (email == "") {

				alert("Please enter your email address");

				return true;
			}

			if (quantity == "") {

				alert("Please select vote category");

				return true;
			}

			quantity_field.setAttribute("disabled", true);
			email_field.setAttribute("disabled", true);
			btn_field.setAttribute("disabled", true);

			jQuery.ajax({
				url : ajaxurl,
				type : 'post',
				dataType: 'json',
				data : {

					quantity : quantity,
					userID : formId,
					username: username,
					email: email,
					action: 'evsystem_free_ajax'

				},
				success : function( response ){
							
					if(response.success == true){
						alert(response.message);
						quantity_field.setAttribute("disabled", true);
						email_field.setAttribute("disabled", true);
						btn_field.setAttribute("disabled", true);
						setTimeout(window.location.reload(), 500);
					} else {
						alert(response.message);
						quantity_field.setAttribute("disabled", true);
						email_field.setAttribute("disabled", true);
						btn_field.setAttribute("disabled", true);
					}
				}

			});
			
		}

	</script>

    <?php

    get_footer();

?>