
<div id="ewvwp-grid">
<?php 
    while ( $loop->have_posts() ) : $loop->the_post();
    $nickname = get_post_meta(get_the_ID(),"_evsystem_nickname_value_key",true);
    $age = get_post_meta(get_the_ID(),"_evsystem_age_value_key",true);
    $state = get_post_meta(get_the_ID(),"_evsystem_state_value_key",true);
    $vote = get_post_meta(get_the_ID(),"_evsystem_vote_value_key",true);
?>


<div class="ewvwp-product">
        <div class="ewvwp-make3D">
            <div class="ewvwp-product-front">
                <div class="ewvwp-shadow"></div>
                <?php the_post_thumbnail(); ?>
                <div class="ewvwp_image_overlay"></div>
                <div class="ewvwp_view_gallery">Vote Now</div>                
                <div class="ewvwp-stats">        	
                    <div class="ewvwp-stats-container">
                        <span class="ewvwp_product_price"><?php echo $age; ?></span>
                        <span class="ewvwp_product_name"><?php the_title(); ?></span>    
                        <p><?php echo $nickname; ?></p>                                            
                        
                        <?php if(get_option('evsystem_display_vote') == 1 || get_option('evsystem_display_state') == 1): ?>
                        <div class="product-options">

                            
                            <p>
                                <?php if(get_option('evsystem_display_state') == 1): ?>
                                    <strong>State:</strong> <?php echo $state; ?>
                                <?php endif; ?>
                                <?php if(get_option('evsystem_display_vote') == 1): ?>
                                    <br><strong>Votes:</strong> <?php echo $vote; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php endif; ?>                  
                    </div>                         
                </div>
            </div>
            
            <div class="ewvwp-product-back">
                <div class="ewvwp-shadow"></div>
                <form class="easy-wp-voting-form" onsubmit="return easyWpVotingForm(event, <?php print get_the_ID(); ?>)" action="#" method="post" id="easy-wp-voting-form-<?php print get_the_ID(); ?>" data-form="<?php print get_the_ID(); ?>" data-url="<?php echo admin_url('admin-ajax.php'); ?>">
                    <input type="email" name="email" id="email-<?php print get_the_ID(); ?>" placeholder="Enter your email" class="easy-wp-voting-form-input">
                    <input type="number" name="quantity" onkeyup="return updateAmount(event, <?php print get_the_ID(); ?>)" id="quantity-<?php print get_the_ID(); ?>" placeholder="Number of Votes" class="easy-wp-voting-form-input"/>
                    <input type="text" name="amount" id="amount-<?php print get_the_ID(); ?>" placeholder="Amount" class="easy-wp-voting-form-input" readonly/>
                    <button type="submit" id="easy-wp-voting-button">Vote</button>
                    <p><?php print(get_option('ewvwp_min_amount')); ?>NGN per 1 vote</p>
                </form>
                <small class="text-success form-control-msg easy-wp-voting-form-success-<?php print get_the_ID(); ?>" style="display:none; margin:0 auto 100px">Vote Successfully submitted, thank you!</small>
                <small class="text-danger form-control-msg easy-wp-voting-form-error-<?php print get_the_ID(); ?>" style="display:none; margin:0 auto 100px">There was a problem with the Inquiry Form, please try again!</small>
                <div class="ewvwp-flip-back">
                    <div class="ewvwp-cy"></div>
                    <div class="ewvwp-cx"></div>
                </div>
            </div>	  
        </div>
    </div>  


<?php endwhile; ?>

</div>
<?php
wp_reset_postdata(); 

?>
<script>

    function updateAmount(event, formid){

        var amount = $('#amount-'+formid).val();
        var quantity = event.target.value;

        var total = quantity * <?php echo get_option('evsystem_min_amount'); ?>;
        $("#amount-"+formid).val(total);

    }

    function easyWpVotingForm(event, formid){
        event.preventDefault();
        var amount = $('#amount-'+formid).val();
        var quantity = parseInt($('#quantity-'+formid).val());
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var email = $("#email-"+formid).val();

        if (email == "" || quantity == "" ) {

            alert("Fill the necessary details");

            return true;
        }


        var handler = PaystackPop.setup({
            key: '<?php echo get_option( 'evsystem_paystack_public_key' ); ?>', // Replace with your public key
            email: email,
            amount: amount * 100, // the amount value is multiplied by 100 to convert to the lowest currency unit
            currency: 'NGN', // Use GHS for Ghana Cedis or USD for US Dollars
            reference: 'Easy Wp Voting With Payment', // Replace with a reference you generated
            callback: function(response) {
            //this happens after the payment is completed successfully
            var reference = response.reference;
            console.log(reference);
            jQuery.ajax({
                url : ajaxurl,
                type : 'post',
                dataType: 'json',
                data : {

                    quantity : quantity,
                    userID : formid,
                    reference: reference,
                    email: email,
                    action: 'evsystem_form_ajax'

                },
                success : function( response ){
                        
                    if(response.success == true){
                        //$('#easy-wp-voting-form-'+formid).css('display', 'none');
                        //$('.easy-wp-voting-form-success-'+formid).css({'display':'block'})
                        alert(response.message);
                        setTimeout(window.location.reload(), 500);
                    } else {
                        //console.log(response.message);
                        alert(response.message);
                    }
                }

            });
            },
            onClose: function() {
                alert('Transaction was not completed, window closed.');
            },
        });
        handler.openIframe();
        
    }

</script>
