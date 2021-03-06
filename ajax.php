<?php

  /**
    * @package Easy_Voting_system
    * * @author Muhideen Mujeeb Adeoye
    * @version 1.0.1
  */

    add_action('wp_ajax_nopriv_evsystem_form_ajax', 'evsystem_ajax');
    add_action('wp_ajax_evsystem_form_ajax', 'evsystem_ajax');

    add_action('wp_ajax_nopriv_evsystem_free_ajax', 'evsystem_free_ajax');
    add_action('wp_ajax_evsystem_free_ajax', 'evsystem_free_ajax');

    function evsystem_ajax()
    {
      $quantity = intval($_POST['quantity']);
      $userID = intval($_POST['userID']);
      $amount = intval($_POST['amount']);
      $reference = sanitize_text_field($_POST['reference']);
      $email = sanitize_email($_POST['email']);

      if(post_exists( $reference,'','','')){

        $trn_id = post_exists( $reference,'','','');

      } else {

        $trn_id = wp_insert_post(array (
            'post_type' => 'evsystem-transaction',
            'post_title' => $reference,
            'post_status' => 'publish',
        ));

        if ($trn_id) {
            add_post_meta($trn_id, '_evsystem_transaction_email_value_key', $email);
            add_post_meta($trn_id, '_evsystem_transaction_amount_value_key', $amount);
            add_post_meta($trn_id, '_evsystem_transaction_quantity_value_key', $quantity);
            add_post_meta($trn_id, '_evsystem_transaction_voted_for_value_key', get_the_title( $userID ));
            add_post_meta($trn_id, '_evsystem_transaction_status_value_key', "Pending");
        }
      }

      //The parameter after verify/ is the transaction reference to be verified
      $url = 'https://api.paystack.co/transaction/verify/' . $reference;

      $headers = array(
          'Content-Type' => 'application/json',
          'Cache-Control' => 'no-cache',
          'Authorization' => 'Bearer ' . get_option('evsystem_paystack_secret_key'),
      );

      $args = array(
          'headers' => $headers,
          'timeout' => 60,
      );

      $request = wp_remote_get($url, $args);

      if (!is_wp_error($request) && 200 === wp_remote_retrieve_response_code($request)) {

          $paystack_response = json_decode(wp_remote_retrieve_body($request), true);

          if ('success' == $paystack_response['data']['status']) {

              $post_status = "publish"; //publish, draft, etc
              $post_type = "evsystem"; // or whatever post type desired

              /* Attempt to find post id by post name if it exists */
              $found_post = get_post($userID);
              $found_post_id = $found_post_title->ID;

            if (false === get_post_status($found_post)) {

                $result = array(
                    'success' => false,
                    'message' => "Candidate not found",
                );

                return wp_send_json($result);

            } else {

                $vote = get_post_meta($userID, "_evsystem_vote_value_key", true);

                $total = $vote + evsystem_fetch_vote($amount);
                update_post_meta($userID, '_evsystem_vote_value_key', $total);

                if ($trn_id) {
                    update_post_meta($trn_id, '_evsystem_transaction_status_value_key', "Successful");
                }

                $result = array(
                    'success' => true,
                    'message' => "Thanks for voting",
                );

                return wp_send_json($result);
            }

            return wp_send_json($paystack_response);

          } else {

              return wp_send_json(array('success' => false, 'status' => 500, 'message' => 'Payment failed'));

          }
      }

      die();
    }


    function evsystem_free_ajax()
    {
        $quantity = intval($_POST['quantity']);
        $userID = intval($_POST['userID']);
        $email = sanitize_email($_POST['email']);

        $reference = "FREE-".microtime();

        if(post_exists( $reference,'','','')){

            $trn_id = post_exists( $reference,'','','');

        } else {

            $trn_id = wp_insert_post(array (
                'post_type' => 'evsystem-transaction',
                'post_title' => $reference,
                'post_status' => 'publish',
            ));

            if ($trn_id) {
                add_post_meta($trn_id, '_evsystem_transaction_email_value_key', $email);
                add_post_meta($trn_id, '_evsystem_transaction_amount_value_key', 0);
                add_post_meta($trn_id, '_evsystem_transaction_quantity_value_key', $quantity);
                add_post_meta($trn_id, '_evsystem_transaction_voted_for_value_key', get_the_title( $userID ));
                add_post_meta($trn_id, '_evsystem_transaction_status_value_key', "Successful");
            }
        }

        $post_status = "publish"; //publish, draft, etc
        $post_type = "evsystem"; // or whatever post type desired

              /* Attempt to find post id by post name if it exists */
        $found_post = get_post($userID);
        $found_post_id = $found_post_title->ID;

        if (false === get_post_status($found_post)) {

            $result = array(
                'success' => false,
                'message' => "Candidate not found",
            );

            return wp_send_json($result);

        } else {

            $vote = get_post_meta($userID, "_evsystem_vote_value_key", true);

            $total = $vote + $quantity;
            update_post_meta($userID, '_evsystem_vote_value_key', $total);


            $result = array(
                'success' => true,
                'message' => "Thanks for voting",
            );

            return wp_send_json($result);
        }
        die();
    }