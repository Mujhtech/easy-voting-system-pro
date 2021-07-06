<?php

	/**
     * @package Easy_Voting_system
     * * @author Muhideen Mujeeb Adeoye
     * @version 1.0.1
     */

	$taxonomy_id = $contest;
	$page = $pagination;
	$set_page = get_option( 'evsystem_no_of_candidate_per_page' );

	if($contest == "all" && $pagination == "all"){
		$args = array(
			'post_type' => 'evsystem',
			'post_status' => 'publish',
			'posts_per_page' => -1, 
			'meta_key'   => '_evsystem_vote_value_key',
			'orderby'    => '_evsystem_vote_value_key', 
			'order' => 'DESC', 
		);
	} else {

		if($pagination != "all" && $pagination != 0){
			$args = array(  
				'post_type' => 'evsystem',
				'post_status' => 'publish',
				'posts_per_page' => $pagination, 
				'meta_key'   => '_evsystem_vote_value_key',
				'orderby'    => '_evsystem_vote_value_key',
				'order' => 'DESC',
			);
		} elseif($pagination != "all" && $pagination != 0 && $contest != "all"){
			$args = array(  
				'post_type' => 'evsystem',
				'post_status' => 'publish',
				'posts_per_page' => get_option( 'evsystem_no_of_candidate_per_page' ) ? get_option( 'evsystem_no_of_candidate_per_page' ) : 10, 
				'meta_key'   => '_evsystem_vote_value_key',
				'orderby'    => '_evsystem_vote_value_key',
				'order' => 'ASC', 
				'tax_query' => array(
					array(
					'taxonomy' => 'evsystem-category',
					'field' => 'term_id',
					'terms' => $contest,
					),
				),
			);
		} else {
			$args = array(  
				'post_type' => 'evsystem',
				'post_status' => 'publish',
				'posts_per_page' => get_option( 'evsystem_no_of_candidate_per_page' ) ? get_option( 'evsystem_no_of_candidate_per_page' ) : 10, 
				'meta_key'   => '_evsystem_vote_value_key',
				'orderby'    => '_evsystem_vote_value_key',
				'order' => 'ASC', 
				'tax_query' => array(
					array(
					'taxonomy' => 'evsystem-category',
					'field' => 'term_id',
					'terms' => $contest,
					),
				),
			);
		}	
	}

	$loop = new WP_Query( $args );

	if(!empty(get_option('evsystem_template'))){
		$template = get_option('evsystem_template');
	} else {
		$template = 1;
	}

	//echo $taxonomy_id;
	$term_name = get_term( $contest )->name;

	if ($contest != "all") {
		echo '<h1>'.$term_name.'</h1>';
	}
	//include 'pages/theme_'.$template.'.php';
	include 'pages/theme_2.php';

?>

